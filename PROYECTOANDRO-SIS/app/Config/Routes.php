<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Ruta principal redirige al login
$routes->get('/', 'AuthController::login');

// Rutas de autenticación
$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');
});

// Rutas protegidas (ejemplo)
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
});

$routes->group('contratista', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'ContratistaController::dashboard');
});