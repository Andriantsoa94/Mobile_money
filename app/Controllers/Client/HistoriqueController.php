<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

class HistoriqueController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('user_id');

        $transactions = (new TransactionModel())->pourUtilisateur($idUser);

        return view('client/historique', [
            'transactions' => $transactions,
        ]);
    }
}
