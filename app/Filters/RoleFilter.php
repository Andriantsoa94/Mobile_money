<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RequestInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {
        $session = session();
        $user = $session->get("user");

        if (!$user || !in_array($user['role'], $arguments ?? [])) {
            return redirect()->to('/livres')->with('error', 'Acces refuse : droits insuffisants.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {

    }
}