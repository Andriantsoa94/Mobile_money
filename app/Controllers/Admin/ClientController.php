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

        $builder = (new UserModel())
            ->select('user.*, solde.value AS soldeValue, GROUP_CONCAT(numero.numero) AS numeros')
            ->join('solde', 'solde.idUser = user.id', 'left')
            ->join('numero', 'numero.iduser = user.id', 'left')
            ->where('user.idrole', $idRole)
            ->groupBy('user.id');

        if ($recherche !== '') {
            $builder = $builder->groupStart()
                ->like('user.nom', $recherche)
                ->orLike('user.CIN', $recherche)
                ->orLike('numero.numero', $recherche)
                ->groupEnd();
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

        $solde        = (new SoldeModel())->getValeur($id);
        $numeros      = (new NumeroModel())->where('iduser', $id)->findAll();
        $transactions = (new TransactionModel())->pourUtilisateur($id);

        return view('admin/clientDetail', [
            'client'       => $client,
            'solde'        => $solde,
            'numeros'      => $numeros,
            'transactions' => $transactions,
        ]);
    }
}
