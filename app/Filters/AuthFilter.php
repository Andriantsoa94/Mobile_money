<?php
namespace App\Filters;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;

class AuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('user')) {
            return redirect()->to('/login')->with('error', 'Connectez-vous pour acceder a cette page');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}
?>