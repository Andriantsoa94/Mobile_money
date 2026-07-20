<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\NumeroModel;
use App\Models\PrefixeModel;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
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
        $transactionModel = new TransactionModel();
        $prefixeModel     = new PrefixeModel();
        $numeroModel      = new NumeroModel();
        $soldeModel       = new SoldeModel();

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

        $ligneDest = $numeroModel->findByNumero($numeroDest);

        if (! $ligneDest) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Numéro destinataire non enregistré.');
        }

        $idUserDest = (int) $ligneDest['iduser'];

        if ($idUserDest === (int) $idUser) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Impossible de vous transférer à vous-même.');
        }

        $numeroSource       = $numeroModel->where('iduser', $idUser)->first();
        $idOperateurSource = $numeroSource ? $prefixeModel->trouverOperateurParNumero($numeroSource['numero']) : null;
        $idOperateurDest   = $prefixeModel->trouverOperateurParNumero($numeroDest);

        if ($idOperateurSource !== null && $idOperateurSource === $idOperateurDest) {
            $fraisCalcules = $transactionModel->frais($montantSaisi, (int) $idOperateurSource);
        } else {
            $fraisCalcules = 0.0;
        }

        if ($inclureFrais === 1) {
            $montantEnvoye = $montantSaisi;
            $montantDebite = $montantSaisi + $fraisCalcules;
        } else {
            $montantEnvoye = max(0, $montantSaisi - $fraisCalcules);
            $montantDebite = $montantSaisi;
        }

        if (! $soldeModel->soldeSuffisant($idUser, $montantDebite)) {
            return redirect()->to('/client/transfert')->withInput()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
        }

        try {
            $soldeModel->transferer($idUser, $idUserDest, $montantDebite, $montantEnvoye);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/transfert')->withInput()->with('error', $e->getMessage());
        }

        $transactionModel->insert([
            'idUser'      => $idUser,
            'idOperateur' => $idOperateurSource,
            'gain'        => $fraisCalcules,
            'valeur'      => $montantSaisi,
            'frais'       => $fraisCalcules,
        ]);

        return redirect()->to('/client')->with('success', 'Transfert effectué avec succès.');
    }
}