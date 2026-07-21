<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\Epargne;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class EpargneController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('user_id');

        $epargneModel = new Epargne();

        $epargne = $epargneModel->getValeur($idUser);
        $pourcentage = $epargneModel->getPourcentage($idUser);


        return view('client/epargne', [
            'epargne' => $epargne,
            'pourcentage' => $pourcentage
        ]);
       
    }
}
