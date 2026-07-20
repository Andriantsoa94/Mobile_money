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

class RetraitController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('user_id');
        $solde  = (new SoldeModel())->getValeur($idUser);

        return view('client/retrait', [
            'solde' => $solde,
        ]);
    }

    public function store()
    {
        $idUser  = session()->get('user_id');
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->to('/client/retrait')->with('error', 'Montant invalide.');
        }

        $configModel = new ConfigModel();
        $soldeModel  = new SoldeModel();

        $tranche = $configModel->trancheDe($montant);
        $frais   = (float) ($tranche['frais'] ?? 0);
        $gain    = (float) ($tranche['gain'] ?? 0);

        // Seul le "frais" est deduit du solde, jamais le "gain" (interne).
        $totalDebite = $montant + $frais;

        if (! $soldeModel->soldeSuffisant($idUser, $totalDebite)) {
            return redirect()->to('/client/retrait')->with('error', 'Solde insuffisant pour ce retrait (montant + frais).');
        }

        $numero      = (new NumeroModel())->where('iduser', $idUser)->first();
        $idOperateur = $numero ? (new PrefixeModel())->trouverOperateurParNumero($numero['numero']) : null;
        $typeRetrait = (new TypeOperationModel())->where('nom', 'Retrait')->first();

        try {
            $soldeModel->retrait($idUser, $totalDebite);
        } catch (RuntimeException $e) {
            return redirect()->to('/client/retrait')->with('error', $e->getMessage());
        }

        (new TransactionModel())->insert([
            'idUser'          => $idUser,
            'idOperateur'     => $idOperateur,
            'idTypeOperation' => $typeRetrait['id'] ?? null,
            'valeur'          => $montant,
            'frais'           => $frais,
            'gain'            => $gain,
        ]);

        return redirect()->to('/client')->with('success', 'Retrait effectué avec succès.');
    }
}
