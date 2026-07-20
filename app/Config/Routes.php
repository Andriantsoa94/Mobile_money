<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Livres::index');
//
//$routes->group('livres', static function ($routes) {
//	$routes->get('ajouter', 'Livres::create');
//	$routes->post('ajouter', 'Livres::store');
//	$routes->get('modifier/(:num)', 'Livres::edit/$1');
//	$routes->post('modifier/(:num)', 'Livres::update/$1');
//
//	$routes->get('(:num)', 'Livres::show/$1');
//
//	$routes->post('supprimer/(:num)', 'Livres::delete/$1');
//	$routes->post('preter/(:num)', 'Emprunts::preter/$1');
//	$routes->post('retourner/(:num)', 'Emprunts::retourner/$1');
//});


$routes->get('/login', 'AuthController::form');
$routes->post('/login', 'AuthController::login');

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('livres', 'LivreController::index');

    $routes->get('profil', 'ProfilController::index');
    $routes->get('/logout', 'AuthController::logout');

    $routes->get('livres/(:num)', 'LivreController::show/$1');

    $routes->post('livres/supprimer/(:num)', 'LivreController::delete/$1');
    $routes->post('livres/modifier/(:num)', 'LivreController::update/$1');
    $routes->get('livres/modifier/(:num)', 'LivreController::edit/$1');
    $routes->post('livres/(:num)/avis', 'LivreController::ajouterAvis/$1');

    $routes->post('livres/preter/(:num)','EmpruntController::preter/$1');
    $routes->post('livres/retourner/(:num)','EmpruntController::retourner/$1');
    $routes->post('livres/reserver/(:num)','EmpruntController::reserver/$1');
    $routes->post('livres/reservation/annuler/(:num)','EmpruntController::annulerReservation/$1');
});

$routes->group('', ['filter' => 'role:admin,biblio,bibliothecaire'], function($routes) {
    $routes->get('livres/ajouter','LivreController::create');
    $routes->post('livres/ajouter','LivreController::store');
    $routes->get('livres/export/csv', 'LivreController::exportCsv');
    $routes->get('livres/export/pdf', 'LivreController::exportPdf');
});

$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard','Admin\DashboardController::index');
    $routes->post('livres/supprimer/(:num)','Admin\LivreController::supprimer/$1');
    $routes->get('utilisateurs','Admin\UserController::index');
});

$routes->group('gestion', ['filter' => 'role:admin,biblio,bibliothecaire'],
    function($routes) {
        $routes->get('emprunts','Gestion\EmpruntController::index');
        $routes->get('emprunts/retards','Gestion\EmpruntController::retards');
    });