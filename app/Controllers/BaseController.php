<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    protected $helpers = ['form', 'url'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Render a view with shared controller helper style.
     */
    protected function render(string $view, array $data = []): string
    {
        return view($view, $data);
    }

    /**
     * Simple auth guard:
     * - If no auth system exists yet, do nothing.
     * - If auth is present and user is not logged in, redirect to home.
     */
    protected function requireLogin(): ?RedirectResponse
    {
        $session = session();

        $user = $session->get('user');

        if (! is_array($user) || ! isset($user['id'])) {
            return redirect()->to('/login')->with('error', 'Connexion requise.');
        }

        return null;
    }
}
