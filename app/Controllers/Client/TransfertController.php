<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\CommissionModel;
use App\Models\ConfigModel;
use App\Models\NumeroModel;
use App\Models\PrefixeModel;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;
use RuntimeException;

class TransfertController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('user_id');
        $solde  = (new SoldeModel())->getValeur($idUser);

        return view('client/transfert', [
            'solde' => $solde,
        ]);
    }

    public function store()
    {
        $configModel      = new ConfigModel();
        $prefixeModel     = new PrefixeModel();
        $numeroModel      = new NumeroModel();
        $soldeModel       = new SoldeModel();
        $transactionModel = new TransactionModel();
        $commissionModel  = new CommissionModel();

        $idUser       = session()->get('user_id');
        $numeroDest   = trim((string) $this->request->getPost('numero'));
        $inclureFrais = (int) $this->request->getPost('frais');
        $montantSaisi = (float) $this->request->getPost('montant');

        if (! preg_match('/^[0-9]{10}$/', $numeroDest)) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire invalide (10 chiffres attendus).');
        }

        if ($montantSaisi <= 0) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Montant invalide.');
        }

        if (! $prefixeModel->estValide(substr($numeroDest, 0, 3))) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Préfixe opérateur non reconnu pour ce numéro.');
        }

        $numeroSource      = $numeroModel->where('iduser', $idUser)->first();
        $idOperateurSource = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;

        // Appartenance du numéro destinataire : chez nous ou autre opérateur ?
        $estChezNous     = $prefixeModel->appartientANous($numeroDest);
        $idOperateurDest = $prefixeModel->trouverOperateurParNumero($numeroDest);

        $tranche    = $configModel->trancheDe($montantSaisi);
        $frais      = (float) ($tranche['frais'] ?? 0);
        $gain       = (float) ($tranche['gain'] ?? 0);
        // Commission uniquement si le destinataire est chez un AUTRE opérateur.
        $commission = $estChezNous ? 0.0 : $commissionModel->pourOperateur($idOperateurDest);

        // Un transfert vers un autre opérateur ne peut pas créditer un solde interne :
        // on l'enregistre comme sortie (montant + frais + commission débités).
        if ($estChezNous) {
            $ligneDest = $numeroModel->findByNumero($numeroDest);
            if (! $ligneDest) {
                return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire non enregistré.');
            }

            $idUserDest = (int) $ligneDest['iduser'];
            if ($idUserDest === (int) $idUser) {
                return redirect()->to('/client/transfert')->withInput()->with('error', 'Impossible de vous transférer à vous-même.');
            }
        } else {
            $idUserDest = null;
        }

        // Calcul du débit :
        //  - chez nous  : montant + frais (+0 commission)
        //  - autre op.  : montant + frais + commission
        if ($inclureFrais === 1) {
            $montantEnvoye = $montantSaisi;
            $montantDebite = $montantSaisi + $frais + $commission;
        } else {
            $montantEnvoye = max(0, $montantSaisi - $frais - $commission);
            $montantDebite = $montantSaisi;
        }

        if (! $soldeModel->soldeSuffisant($idUser, $montantDebite)) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
        }

        $typeTransfert = (new TypeOperationModel())->where('nom', 'Transfert')->first();

        try {
            if ($estChezNous) {
                $soldeModel->transferer($idUser, $idUserDest, $montantDebite, $montantEnvoye);
            } else {
                // Destinataire externe : uniquement débit du solde de l'expéditeur.
                $soldeModel->retrait($idUser, $montantDebite);
            }
        } catch (RuntimeException $e) {
            return redirect()->to('/client/transfert')->withInput()->with('error', $e->getMessage());
        }

        $transactionModel->insert([
            'idUser'           => $idUser,
            'idOperateur'      => $idOperateurSource,
            'idTypeOperation'  => $typeTransfert['id'] ?? null,
            'valeur'           => $montantSaisi,
            'frais'            => $frais,
            'gain'             => $gain,
            'commission'       => $commission,
            'idAutreOperateur' => $estChezNous ? null : $idOperateurDest,
        ]);

        return redirect()->to('/client')->with('success', 'Transfert effectué avec succès.');
    }

    /**
     * Formulaire de transfert vers plusieurs destinataires (nombre libre, ajout via JS).
     */
    public function multiple()
    {
        $idUser = session()->get('user_id');
        $solde  = (new SoldeModel())->getValeur($idUser);

        // Table prefixe -> idOperateur pour la vérification JS côté client.
        $prefixes = (new PrefixeModel())->findAll();
        $prefixesOperateurs = [];
        foreach ($prefixes as $p) {
            $prefixesOperateurs[$p['numero']] = (int) $p['idoperateur'];
        }

        return view('client/transfertMultiple', [
            'solde'              => $solde,
            'prefixesOperateurs' => $prefixesOperateurs,
        ]);
    }

    /**
     * Traite un envoi vers plusieurs destinataires en une seule soumission.
     * Toutes les lignes sont validées avant que le moindre solde ne soit touché.
     */
    public function storeMultiple()
    {
        $configModel      = new ConfigModel();
        $prefixeModel     = new PrefixeModel();
        $numeroModel      = new NumeroModel();
        $soldeModel       = new SoldeModel();
        $transactionModel = new TransactionModel();
        $commissionModel  = new CommissionModel();

        $idUser       = session()->get('user_id');
        $numeros      = $this->request->getPost('numero') ?? [];
        $montants     = $this->request->getPost('montant') ?? [];
        $inclureFrais = (int) $this->request->getPost('frais');

        $numeroSource      = $numeroModel->where('iduser', $idUser)->first();
        $idOperateurSource = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;

        // 1) Ne garder que les lignes réellement remplies.
        $lignes = [];
        foreach ($numeros as $i => $numero) {
            $numero  = trim((string) $numero);
            $montant = (float) ($montants[$i] ?? 0);

            if ($numero === '' && $montant <= 0) {
                continue;
            }

            $lignes[] = ['numero' => $numero, 'montant' => $montant];
        }

        if (empty($lignes)) {
            return redirect()->to('/client/transfert/multiple')->withInput()->with('error', 'Veuillez renseigner au moins un destinataire.');
        }

        // 2) Valider chaque ligne (format, préfixe, même opérateur pour tous).
        $destinataires  = [];
        $idOperateurRef = null;

        foreach ($lignes as $n => $ligne) {
            $numero  = $ligne['numero'];
            $montant = $ligne['montant'];
            $rang    = $n + 1;

            if (! preg_match('/^[0-9]{10}$/', $numero)) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : numéro invalide (10 chiffres attendus).");
            }

            if ($montant <= 0) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : montant invalide.");
            }

            if (! $prefixeModel->estValide(substr($numero, 0, 3))) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : préfixe opérateur non reconnu.");
            }

            $idOperateurDest = $prefixeModel->trouverOperateurParNumero($numero);

            if ($idOperateurRef === null) {
                $idOperateurRef = $idOperateurDest;
            } elseif ($idOperateurDest !== $idOperateurRef) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : tous les numéros doivent être du même opérateur.");
            }

            $estChezNous = $prefixeModel->appartientANous($numero);

            if ($estChezNous) {
                $ligneDest = $numeroModel->findByNumero($numero);
                if (! $ligneDest) {
                    return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : numéro non enregistré.");
                }

                $idUserDest = (int) $ligneDest['iduser'];
                if ($idUserDest === (int) $idUser) {
                    return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : impossible de vous transférer à vous-même.");
                }
            } else {
                $idUserDest = null;
            }

            $tranche    = $configModel->trancheDe($montant);
            $frais      = (float) ($tranche['frais'] ?? 0);
            $gain       = (float) ($tranche['gain'] ?? 0);
            $commission = $estChezNous ? 0.0 : $commissionModel->pourOperateur($idOperateurDest);

            if ($inclureFrais === 1) {
                $montantEnvoye = $montant;
                $montantDebite = $montant + $frais + $commission;
            } else {
                $montantEnvoye = max(0, $montant - $frais - $commission);
                $montantDebite = $montant;
            }

            $destinataires[] = [
                'estChezNous'      => $estChezNous,
                'idUserDest'       => $idUserDest,
                'idOperateurDest'  => $idOperateurDest,
                'montantSaisi'     => $montant,
                'montantDebite'    => $montantDebite,
                'montantEnvoye'    => $montantEnvoye,
                'frais'            => $frais,
                'gain'             => $gain,
                'commission'       => $commission,
            ];
        }

        // 3) Vérifier que le solde couvre le total AVANT de toucher quoi que ce soit.
        $totalDebite = array_sum(array_column($destinataires, 'montantDebite'));

        if (! $soldeModel->soldeSuffisant($idUser, $totalDebite)) {
            return redirect()->to('/client/transfert/multiple')->withInput()->with('error', 'Solde insuffisant pour couvrir l\'ensemble des transferts (' . number_format($totalDebite, 0, ',', ' ') . ' Ar).');
        }

        $typeTransfert = (new TypeOperationModel())->where('nom', 'Transfert')->first();

        // 4) Exécuter tous les transferts.
        try {
            foreach ($destinataires as $destinataire) {
                if ($destinataire['estChezNous']) {
                    $soldeModel->transferer(
                        $idUser,
                        $destinataire['idUserDest'],
                        $destinataire['montantDebite'],
                        $destinataire['montantEnvoye']
                    );
                } else {
                    $soldeModel->retrait($idUser, $destinataire['montantDebite']);
                }

                $transactionModel->insert([
                    'idUser'           => $idUser,
                    'idOperateur'      => $idOperateurSource,
                    'idTypeOperation'  => $typeTransfert['id'] ?? null,
                    'valeur'           => $destinataire['montantSaisi'],
                    'frais'            => $destinataire['frais'],
                    'gain'             => $destinataire['gain'],
                    'commission'       => $destinataire['commission'],
                    'idAutreOperateur' => $destinataire['estChezNous'] ? null : $destinataire['idOperateurDest'],
                ]);
            }
        } catch (RuntimeException $e) {
            return redirect()->to('/client/transfert/multiple')->withInput()->with('error', $e->getMessage());
        }

        $nombre = count($destinataires);
        return redirect()->to('/client')->with('success', $nombre . ' transfert(s) effectué(s) avec succès.');
    }
}
