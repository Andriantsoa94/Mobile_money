<?php

namespace App\Controllers;

use App\Models\NumeroModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $numero = trim((string) $this->request->getPost('numero'));

        if (!preg_match('/^[0-9]{10}$/', $numero)) {
            return redirect()->to('/login')->withInput()->with('error', 'Numéro invalide (10 chiffres attendus).');
        }

        $numeroModel = new NumeroModel();
        $ligneNumero = $numeroModel->findByNumero($numero);

        if (!$ligneNumero) {
            return redirect()->to('/login')->withInput()->with('error', 'Numéro non reconnu. Contactez votre opérateur.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($ligneNumero['iduser']);

        if (!$user) {
            return redirect()->to('/login')->withInput()->with('error', 'Numéro non reconnu. Contactez votre opérateur.');
        }

        $roleModel = new RoleModel();
        $role      = $roleModel->find($user['idrole']);
        $type      = $role['type'] ?? 'client';

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'numero'     => $numero,
            'role'       => $type,
        ]);

        return redirect()->to($type === 'admin' ? '/admin' : '/client');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}