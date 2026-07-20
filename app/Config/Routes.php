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
    $routes->post('depot', 'Client\DepotController::store');
    $routes->get('retrait', 'Client\RetraitController::index');
    $routes->post('retrait', 'Client\RetraitController::store');
    $routes->get('transfert', 'Client\TransfertController::index');
    $routes->post('transfert', 'Client\TransfertController::store');
    $routes->get('historique', 'Client\HistoriqueController::index');
});

$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');

    $routes->get('prefixes',                     'Admin\PrefixeController::index');
    $routes->get('prefixes/nouveau',             'Admin\PrefixeController::nouveau');
    $routes->post('prefixes',                    'Admin\PrefixeController::creer');
    $routes->get('prefixes/(:num)/modifier',     'Admin\PrefixeController::modifier/$1');
    $routes->post('prefixes/(:num)/modifier',    'Admin\PrefixeController::mettreAJour/$1');
    $routes->post('prefixes/(:num)/supprimer',   'Admin\PrefixeController::supprimer/$1');

    $routes->get('config',                       'Admin\ConfigController::index');
    $routes->get('config/nouveau',               'Admin\ConfigController::nouveau');
    $routes->post('config',                      'Admin\ConfigController::creer');
    $routes->get('config/(:num)/modifier',       'Admin\ConfigController::modifier/$1');
    $routes->post('config/(:num)/modifier',      'Admin\ConfigController::mettreAJour/$1');
    $routes->post('config/(:num)/supprimer',     'Admin\ConfigController::supprimer/$1');

    $routes->get('gains', 'Admin\GainController::index');

    $routes->get('clients',              'Admin\ClientController::index');
    $routes->get('clients/(:num)',       'Admin\ClientController::detail/$1');
});
