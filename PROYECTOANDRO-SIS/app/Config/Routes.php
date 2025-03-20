<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('auth', 'AuthController::index');
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('auth/forgot_password', 'AuthController::forgotPassword');
$routes->post('auth/process_forgot_password', 'AuthController::processForgotPassword');
$routes->get('auth/reset_password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('auth/process_reset_password', 'AuthController::processResetPassword');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Ruta específica para gestión de proyectos admin
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    // Agregar esta línea
    $routes->get('proyectos', 'Admin\ProyectosController::index');
    $routes->post('proyectos/crear', 'Admin\ProyectosController::crearCategoria');
    $routes->post('proyectos/eliminar', 'Admin\ProyectosController::eliminarCategoria');
    // Rutas existentes
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->post('proyectos/crear', 'Admin\ProyectosController::crearCategoria');
    $routes->post('proyectos/eliminar', 'Admin\ProyectosController::eliminarCategoria');
});

// Contratista routes
$routes->group('contratista', ['filter' => 'contratista'], function ($routes) {
    $routes->get('proyectos', 'Contratista\ProyectosController::index');
    $routes->get('publicaciones', 'Contratista\PublicacionesController::index');
    $routes->get('conversaciones', 'Contratista\Conversaciones::index');
});

// Publico routes
$routes->group('publico', ['filter' => 'publico'], function ($routes) {
    $routes->get('proyectos', 'Publico\ProyectosController::index');
    $routes->get('publicaciones', 'Publico\PublicacionesController::index');
});

$routes->group('newsfeed', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PublicacionesController::index', ['as' => 'newsfeed.index']);
    $routes->post('crear', 'PublicacionesController::crear', ['as' => 'newsfeed.crear']);
    $routes->post('comentar', 'PublicacionesController::crearComentario');
    $routes->post('like', 'PublicacionesController::toggleLike');
});

$routes->get('proyectos/detalle/(:num)', 'ProyectosController::detalle/$1');
$routes->get('categorias/detalle/(:num)', 'CategoriasController::detalle/$1');
