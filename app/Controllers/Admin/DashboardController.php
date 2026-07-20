<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        $roleModel        = new RoleModel();

        $totalGains             = $transactionModel->totalGains();
        $totalTransactions      = $transactionModel->countAllResults();
        $transactionsAujourdhui = $transactionModel->nombreAujourdhui();
        $totalClients = $roleModel->totalClients();
        $dernieresTransactions = $transactionModel->dernieresAvecClient(8);

        return view('admin/dashboard', [
            'totalGains'             => $totalGains,
            'totalTransactions'      => $totalTransactions,
            'transactionsAujourdhui' => $transactionsAujourdhui,
            'totalClients'           => $totalClients,
            'dernieresTransactions'  => $dernieresTransactions,
        ]);
    }
}
