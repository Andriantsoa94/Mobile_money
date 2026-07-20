<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('client', ['filter' => 'role:client'], function ($routes) {
    $routes->get('/', 'LoginController::index');
    $routes->get('/', 'Client\DashboardController::index');
    $routes->get('depot', 'Client\DepotController::index');
    $routes->get('retrait', 'Client\RetraitController::index');
    $routes->get('transfert', 'Client\TransfertController::index');
    $routes->get('historique', 'Client\HistoriqueController::index');
});

?>