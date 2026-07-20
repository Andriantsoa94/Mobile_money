<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NumeroModel;
use App\Models\RoleModel;
use App\Models\SoldeModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class ClientController extends BaseController
{
    public function index()
    {
        $recherche = trim((string) $this->request->getGet('q'));

        $roleClient = (new RoleModel())->findByType('client');
        $idRole     = $roleClient['id'] ?? 0;
        $userModel = new UserModel();

        $builder = $userModel->findByRole($idRole);

        if ($recherche !== '') {
            $builder = $userModel->filtrer($builder, $recherche);
        }

        $clients = $builder->orderBy('user.nom', 'ASC')->find();

        return view('admin/clientsListe', [
            'clients'   => $clients,
            'recherche' => $recherche,
        ]);
    }

    public function detail(int $id)
    {
        $client = (new UserModel())->find($id);
        if (! $client) {
            return redirect()->to('/admin/clients')->with('error', 'Client introuvable.');
        }

        $numeroModel = new NumeroModel();
        $soldeModel = new SoldeModel();
        $transactionModel = new TransactionModel();

        $solde        = $soldeModel->getValeur($id);
        $numeros      = $numeroModel->findByUserId($id);
        $transactions = $transactionModel->pourUtilisateur($id);

        return view('admin/clientDetail', [
            'client'       => $client,
            'solde'        => $solde,
            'numeros'      => $numeros,
            'transactions' => $transactions,
        ]);
    }
}
