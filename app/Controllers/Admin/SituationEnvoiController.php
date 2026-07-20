<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

class SituationEnvoiController extends BaseController
{
    public function index()
    {
        $filtres = [
            'dateDebut' => $this->request->getGet('dateDebut'),
            'dateFin'   => $this->request->getGet('dateFin'),
        ];

        $montants = (new TransactionModel())->montantsAEnvoyerParOperateur($filtres);

        $totalAEnvoyer = array_sum(array_column($montants, 'totalMontant'));

        return view('admin/situationEnvoi', [
            'filtres'       => $filtres,
            'montants'      => $montants,
            'totalAEnvoyer' => $totalAEnvoyer,
        ]);
    }
}
