<?php


use App\Controllers\BaseController;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $idUser = session()->get('idUser');

        $userModel = new UserModel();
//        $soldeModel = new

        return view('client/dashboard');
    }
}
