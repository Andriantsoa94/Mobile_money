<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
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
        $idUser     = session()->get('user_id');
        $numeroDest = trim((string) $this->request->getPost('numero'));
        $montant    = (float) $this->request->getPost('montant');

        if (! preg_match('/^[0-9]{10}$/', $numeroDest)) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire invalide (10 chiffres attendus).');
        }

        if ($montant <= 0) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Montant invalide.');
        }

        $prefixeModel = new PrefixeModel();

        if (! $prefixeModel->estValide(substr($numeroDest, 0, 3))) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Préfixe opérateur non reconnu pour ce numéro.');
        }

        $numeroModel = new NumeroModel();
        $ligneDest   = $numeroModel->findByNumero($numeroDest);

        if (! $ligneDest) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire non enregistré.');
        }

        $idUserDest = (int) $ligneDest['iduser'];

        if ($idUserDest === (int) $idUser) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Impossible de vous transférer à vous-même.');
        }

        $configModel = new ConfigModel();
        $soldeModel  = new SoldeModel();

        $frais         = $configModel->calculerFrais($montant);
        $montantDebite = $montant + $frais;

        if (! $soldeModel->soldeSuffisant($idUser, $montantDebite)) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Solde insuffisant pour ce transfert (montant + frais).');
        }

        $numeroSource  = $numeroModel->where('iduser', $idUser)->first();
        $idOperateur   = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;
        $typeTransfert = (new TypeOperationModel())->where('nom', 'Transfert')->first();

        try {
            $soldeModel->transferer($idUser, $idUserDest, $montantDebite, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/transfert')->withInput()->with('error', $e->getMessage());
        }

        (new TransactionModel())->insert([
            'idUser'          => $idUser,
            'idOperateur'     => $idOperateur,
            'idTypeOperation' => $typeTransfert['id'] ?? null,
            'gain'            => $frais,
            'valeur'          => $montant,
        ]);

        return redirect()->to('/client')->with('success', 'Transfert effectué avec succès.');
    }
}
