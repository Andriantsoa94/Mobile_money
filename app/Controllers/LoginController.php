<?php

namespace App\Controllers;

use App\Models\NumeroModel;
use App\Models\PrefixeModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class LoginController extends BaseController
{
    /**
     * Affiche le formulaire de login
     */
    public function index()
    {
        $prefixeModel = new PrefixeModel();
        $prefixes     = array_column($prefixeModel->listeActifs(), 'numero');

        return view('auth/login', ['prefixes' => $prefixes]);
    }

    /**
     * Traite la soumission du numéro de téléphone
     */
    public function login()
    {
        $numero = trim((string) $this->request->getPost('numero'));

        // Validation basique du format
        if (!preg_match('/^[0-9]{10}$/', $numero)) {
            return redirect()->to('/login')->withInput()->with('error', 'Numéro invalide (10 chiffres attendus).');
        }

        $prefixeModel = new PrefixeModel();
        $prefixe      = substr($numero, 0, 3);

        // Le préfixe doit exister dans la table prefixe (033, 037, ...)
        if (!$prefixeModel->estValide($prefixe)) {
            return redirect()->to('/login')->withInput()->with('error', 'Préfixe non autorisé.');
        }

        $numeroModel = new NumeroModel();
        $userModel   = new UserModel();
        $roleModel   = new RoleModel();

        $ligneNumero = $numeroModel->findByNumero($numero);

        if ($ligneNumero) {
            // Numéro déjà connu -> on récupère le user existant
            $user = $userModel->find($ligneNumero['iduser']);
        } else {
            // Numéro inconnu mais préfixe valide -> création auto d'un compte client
            $roleClient = $roleModel->findByType('client');

            if (!$roleClient) {
                return redirect()->to('/login')->with('error', "Le rôle 'client' n'existe pas encore en base. Lancez le seeder.");
            }

            $userId = $userModel->insert([
                'nom'    => null,
                'CIN'    => null,
                'idrole' => $roleClient['id'],
            ]);

            $numeroModel->insert([
                'numero' => $numero,
                'iduser' => $userId,
            ]);

            $user = $userModel->find($userId);
        }

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Utilisateur introuvable.');
        }

        $role = $roleModel->find($user['idrole']);
        $type = $role['type'] ?? 'client';

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'numero'     => $numero,
            'role'       => $type,
        ]);

        return redirect()->to($type === 'admin' ? '/admin' : '/client');
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}
