<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');

        $userModel        = new UserModel();
        $soldeModel        = new SoldeModel();
        $transactionModel  = new TransactionModel();

        $user  = $userModel->find($userId);
        $solde = $soldeModel->where('idUser', $userId)->first();

        $dernieresTransactions = $transactionModel
            ->where('idUser', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        return view('client/dashboard', [
            'user'                  => $user,
            'solde'                 => $solde['value'] ?? 0,
            'dernieresTransactions' => $dernieresTransactions,
        ]);
    }
}
