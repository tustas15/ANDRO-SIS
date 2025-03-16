<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/forgot_password', 'Auth::forgotPassword');
$routes->post('auth/process_forgot_password', 'Auth::processForgotPassword');
$routes->get('auth/reset_password/(:any)', 'Auth::resetPassword/$1');
$routes->post('auth/process_reset_password', 'Auth::processResetPassword');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Ruta específica para gestión de proyectos admin
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('proyectos', 'Admin\Proyectos::index');
    $routes->post('proyectos/crear', 'Admin\Proyectos::crearCategoria');
    $routes->post('proyectos/eliminar', 'Admin\Proyectos::eliminarCategoria');
});

// Contratista routes
$routes->group('contratista', ['filter' => 'contratista'], function ($routes) {
    $routes->get('proyectos', 'Contratista\Proyectos::index');
    $routes->get('publicaciones', 'Contratista\Publicaciones::index');
    $routes->get('conversaciones', 'Contratista\Conversaciones::index');
});

// Publico routes
$routes->group('publico', ['filter' => 'publico'], function ($routes) {
    $routes->get('proyectos', 'Publico\Proyectos::index');
    $routes->get('publicaciones', 'Publico\Publicaciones::index');
});
