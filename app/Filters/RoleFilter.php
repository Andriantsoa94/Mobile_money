<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $roleUtilisateur = $session->get('role');
        $roleRequis       = $arguments[0] ?? null;

        if ($roleRequis && $roleUtilisateur !== $roleRequis) {
            $redirection = $roleUtilisateur === 'operateur' ? '/admin' : '/client';
            return redirect()->to($redirection)->with('error', 'Accès non autorisé.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}