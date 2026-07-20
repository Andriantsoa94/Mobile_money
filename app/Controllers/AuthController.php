<?php
namespace App\Controllers;
use App\Models\UserModel;
class AuthController extends BaseController
{
    public function form()
    {
        return view('auth/login');
    }
    public function login()
    {
        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = $model->where('email', $email)->first();
        $storedPassword = $user['password'] ?? '';
        $passwordMatches = password_verify($password, $storedPassword) || hash_equals((string) $storedPassword, (string) $password);

        if (!$user || !$passwordMatches) {
            return view('auth/login', [
                'erreur' => 'Email ou mot de passe incorrect'
            ]);
        }

        if (!password_get_info($storedPassword)['algo']) {
            $model->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session()->set('user', [
            'id'
            => $user['id'],
            'nom'
            => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        return redirect()->to('/livres');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}