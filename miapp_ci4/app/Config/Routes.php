<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('auth', 'AuthController::index');
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');
$routes->post('auth/logout', 'AuthController::index');
$routes->get('auth/forgot_password', 'AuthController::forgotPassword');
$routes->post('auth/process_forgot_password', 'AuthController::processForgotPassword');
$routes->get('auth/reset_password/(:segment)', 'AuthController::resetPassword/$1');
$routes->post('auth/process_reset_password', 'AuthController::processResetPassword');
$routes->get('auth/registro', 'AuthController::register');
$routes->post('auth/process_register', 'AuthController::processRegister');
$routes->get('auth/perfil', 'UsuariosController::perfil');
$routes->post('auth/perfil/actualizar', 'UsuariosController::actualizarPerfil');
$routes->get('auth/verificar', 'AuthController::verificar');
$routes->post('auth/verificar-codigo', 'AuthController::verificarCodigo');
$routes->get('auth/verificar-recuperacion', 'AuthController::verificarRecuperacionView');
$routes->post('auth/verificar-recuperacion', 'AuthController::verificarRecuperacion');


// Agregar estas rutas
$routes->group('password', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PasswordController::index');
    $routes->post('update', 'PasswordController::update');
});


$routes->group('categorias', ['filter' => 'admin'], function ($routes) { // Solo para admin
    $routes->get('/', 'CategoriasController::index');
    $routes->post('crear', 'CategoriasController::crear');
    $routes->post('eliminar', 'CategoriasController::eliminar');
});

$routes->group('proyectos', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'ProyectosController::index');
    $routes->post('crear', 'ProyectosController::crear');
    $routes->post('eliminar', 'ProyectosController::eliminar');
    $routes->post('update', 'ProyectosController::update');
});


// Ruta específica para gestión de proyectos admin
$routes->group('admin', ['filter' => 'admin', 'namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('usuarios/crear', 'UsuariosController::formCrearUsuario', ['as' => 'admin.usuarios.crear']);
    $routes->post('usuarios/guardar', 'UsuariosController::guardarUsuario', ['as' => 'admin.usuarios.guardar']);

    $routes->get('usuarios/administradores', 'UsuariosController::indexAdmins', ['as' => 'admin.usuarios.admins']);
    $routes->get('usuarios/contratistas', 'UsuariosController::indexContratistas', ['as' => 'admin.usuarios.contratistas']);
    $routes->get('usuarios/ciudadanos', 'UsuariosController::indexCiudadanos', ['as' => 'admin.usuarios.ciudadanos']);
    $routes->post('usuarios/updateStatus', 'UsuariosController::updateStatus');

    $routes->group('backup', ['filter' => 'admin'], function ($routes) {
        $routes->get('/', 'BackupController::index');
        $routes->post('create', 'BackupController::createBackup');
        $routes->post('restore', 'BackupController::restoreBackup');
        $routes->get('delete/(:segment)', 'BackupController::deleteBackup/$1');
        $routes->get('download/(:segment)', 'BackupController::downloadBackup/$1');
    });

    $routes->group('reportes', function ($routes) {
        $routes->get('(:segment)', 'ReportesController::index/$1');
        $routes->get('exportarPDF/(:segment)', 'ReportesController::exportarPDF/$1');
        $routes->get('exportarExcel/(:segment)', 'ReportesController::exportarExcel/$1');
    });
});

// Contratista routes
$routes->group('contratista', ['filter' => 'contratista'], function ($routes) {
    $routes->get('proyectos', 'ProyectosController::index');
    $routes->get('publicaciones', 'PublicacionesController::vistas');
    $routes->get('publicacion', 'PublicacionesController::vista');
    $routes->get('conversaciones', 'Conversaciones::index');
    $routes->post('crear', 'PublicacionesController::crear');
});

// Publico routes
$routes->group('publico', ['filter' => 'publico'], function ($routes) {
    $routes->get('proyectos', 'ProyectosController::index');
    $routes->get('publicaciones', 'PublicacionesController::index');
});

$routes->group('newsfeed', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PublicacionesController::index', ['as' => 'newsfeed.index']);
    $routes->post('crear', 'PublicacionesController::crear', ['as' => 'newsfeed.crear']);
    $routes->post('comentar', 'PublicacionesController::crearComentario');
    $routes->post('like', 'PublicacionesController::toggleLike');
});

$routes->get('chat', 'ChatController::index');
$routes->get('conversacion/(:num)', 'ChatController::conversacion/$1');
$routes->post('enviarMensaje', 'ChatController::enviarMensaje');
$routes->get('descargar/(:num)', 'ChatController::descargar/$1');

$routes->get('contratista/(:num)', 'UsuariosController::view/$1');

$routes->get('proyecto/(:num)', 'PublicacionesController::view/$1');

$routes->get('categoria/(:num)', 'CategoriasController::view/$1');

$routes->get('publicacion/(:num)', 'PublicacionesController::verPublicacion/$1', ['as' => 'publicacion.detalle']);
$routes->post('publicacion/toggleLike', 'PublicacionesController::toggleLike');


$routes->get('proyectos/detalle/(:num)', 'ProyectosController::detalle/$1');
$routes->get('categorias/detalle/(:num)', 'CategoriasController::detalle/$1');
