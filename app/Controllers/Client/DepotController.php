<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ConfigModel;
use App\Models\NumeroModel;
use App\Models\PrefixeModel;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
use RuntimeException;

class DepotController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('user_id');
        $solde  = (new SoldeModel())->getValeur($idUser);

        return view('client/depot', [
            'solde' => $solde,
        ]);
    }

    public function store()
    {
        $idUser  = session()->get('user_id');
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->to('/client/depot')->with('error', 'Montant invalide.');
        }

        $numero      = (new NumeroModel())->where('iduser', $idUser)->first();
        $idOperateur = $numero ? (new PrefixeModel())->trouverOperateurParNumero($numero['numero']) : null;
        $frais       = (new ConfigModel())->calculerFrais($montant);

        try {
            (new SoldeModel())->depot($idUser, $montant);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/depot')->with('error', $e->getMessage());
        }

        (new TransactionModel())->insert([
            'idUser'      => $idUser,
            'idOperateur' => $idOperateur,
            'gain'        => $frais,
        ]);

        return redirect()->to('/client')->with('success', 'Dépôt effectué avec succès.');
    }
}
