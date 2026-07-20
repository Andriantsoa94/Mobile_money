<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'LoginController::index');
$routes->get('login', 'LoginController::index');
$routes->post('login', 'LoginController::login');
$routes->get('logout', 'LoginController::logout');

$routes->group('client', ['filter' => 'role:client'], function ($routes) {
    $routes->get('/', 'Client\DashboardController::index');
    $routes->get('depot', 'Client\DepotController::index');
    $routes->get('retrait', 'Client\RetraitController::index');
    $routes->get('transfert', 'Client\TransfertController::index');
    $routes->get('historique', 'Client\HistoriqueController::index');
});

$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
});
