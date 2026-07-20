<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;

class GainController extends BaseController
{
    public function index()
    {
        $filtres = [
            'dateDebut'       => $this->request->getGet('dateDebut'),
            'dateFin'         => $this->request->getGet('dateFin'),
            'idTypeOperation' => $this->request->getGet('idTypeOperation'),
        ];

        $transactionModel = new TransactionModel();

        $totalGains   = $transactionModel->totalGains($filtres);
        $gainsParType = $transactionModel->gainsParType($filtres);
        $typeOperationModel = new TypeOperationModel();

        return view('admin/gains', [
            'filtres'      => $filtres,
            'totalGains'   => $totalGains,
            'gainsParType' => $gainsParType,
            'types'        => $typeOperationModel->filtreByNom(),
        ]);
    }
}
