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
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire invalide.');
        }

        $numeroSource      = $numeroModel->where('iduser', $idUser)->first();
        $idOperateurSource = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;

        $estChezNous     = $prefixeModel->appartientANous($numeroDest);
        $idOperateurDest = $prefixeModel->trouverOperateurParNumero($numeroDest);

        // Pas de frais de retrait pour les autres opérateurs, seulement une commission.
        // Le "gain" plateforme, lui, ne s'applique que sur nos propres transferts.
        if ($estChezNous) {
            $tranche    = $configModel->trancheDe($montantSaisi);
            $frais      = (float) ($tranche['frais'] ?? 0);
            $gain       = (float) ($tranche['gain'] ?? 0);
            $commission = 0.0;
        } else {
            $frais      = 0.0;
            $gain       = 0.0;
            $commission = $commissionModel->pourOperateur($idOperateurDest);
        }

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
     * Formulaire de transfert vers plusieurs destinataires : un seul montant
     * total, divisé équitablement entre tous les numéros saisis.
     */
    public function multiple()
    {
        $idUser = session()->get('user_id');
        $solde  = (new SoldeModel())->getValeur($idUser);

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
     * Traite un envoi vers plusieurs destinataires. Le montant total saisi
     * est divisé équitablement entre tous les numéros renseignés. Tous les
     * numéros doivent appartenir au même opérateur.
     */
    public function storeMultiple()
    {
        $configModel      = new ConfigModel();
        $prefixeModel     = new PrefixeModel();
        $numeroModel      = new NumeroModel();
        $soldeModel       = new SoldeModel();
        $transactionModel = new TransactionModel();
        $commissionModel  = new CommissionModel();

        $idUser        = session()->get('user_id');
        $numerosPostes = $this->request->getPost('numero') ?? [];
        $montantTotal  = (float) $this->request->getPost('montant');
        $inclureFrais  = (int) $this->request->getPost('frais');

        // 1) Ne garder que les numéros réellement remplis.
        $numeros = [];
        foreach ($numerosPostes as $numero) {
            $numero = trim((string) $numero);
            if ($numero !== '') {
                $numeros[] = $numero;
            }
        }

        if (empty($numeros)) {
            return redirect()->to('/client/transfert/multiple')->withInput()->with('error', 'Veuillez renseigner au moins un destinataire.');
        }

        if ($montantTotal <= 0) {
            return redirect()->to('/client/transfert/multiple')->withInput()->with('error', 'Montant total invalide.');
        }

        $nombreDestinataires = count($numeros);
        $montantParNumero    = round($montantTotal / $nombreDestinataires, 2);

        $numeroSource      = $numeroModel->where('iduser', $idUser)->first();
        $idOperateurSource = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;

        // 2) Valider chaque numéro (format, même opérateur pour tous).
        $destinataires  = [];
        $idOperateurRef = null;

        foreach ($numeros as $n => $numero) {
            $rang = $n + 1;

            if (! preg_match('/^[0-9]{10}$/', $numero)) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : numéro invalide (10 chiffres attendus).");
            }

            if (! $prefixeModel->estValide(substr($numero, 0, 3))) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : numéro invalide.");
            }

            $idOperateurDest = $prefixeModel->trouverOperateurParNumero($numero);

            // "Même opérateur uniquement" : on compare le préfixe brut (3 chiffres)
            // pour couvrir aussi les numéros non répertoriés en base.
            $prefixeRef = $idOperateurRef['prefixe'] ?? null;
            $prefixeActuel = substr($numero, 0, 3);

            if ($idOperateurRef === null) {
                $idOperateurRef = ['operateur' => $idOperateurDest, 'prefixe' => $prefixeActuel];
            } elseif ($idOperateurDest !== null && $idOperateurRef['operateur'] !== null && $idOperateurDest !== $idOperateurRef['operateur']) {
                return redirect()->to('/client/transfert/multiple')->withInput()->with('error', "Ligne {$rang} : tous les numéros doivent être du même opérateur.");
            } elseif ($idOperateurDest === null && $idOperateurRef['operateur'] === null && $prefixeActuel !== $idOperateurRef['prefixe']) {
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

            if ($estChezNous) {
                $tranche    = $configModel->trancheDe($montantParNumero);
                $frais      = (float) ($tranche['frais'] ?? 0);
                $gain       = (float) ($tranche['gain'] ?? 0);
                $commission = 0.0;
            } else {
                $frais      = 0.0;
                $gain       = 0.0;
                $commission = $commissionModel->pourOperateur($idOperateurDest);
            }

            if ($inclureFrais === 1) {
                $montantEnvoye = $montantParNumero;
                $montantDebite = $montantParNumero + $frais + $commission;
            } else {
                $montantEnvoye = max(0, $montantParNumero - $frais - $commission);
                $montantDebite = $montantParNumero;
            }

            $destinataires[] = [
                'estChezNous'     => $estChezNous,
                'idUserDest'      => $idUserDest,
                'idOperateurDest' => $idOperateurDest,
                'montantSaisi'    => $montantParNumero,
                'montantDebite'   => $montantDebite,
                'montantEnvoye'   => $montantEnvoye,
                'frais'           => $frais,
                'gain'            => $gain,
                'commission'      => $commission,
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

        return redirect()->to('/client')->with('success', $nombreDestinataires . ' transfert(s) effectué(s) avec succès (' . number_format($montantParNumero, 0, ',', ' ') . ' Ar chacun).');
    }
}
