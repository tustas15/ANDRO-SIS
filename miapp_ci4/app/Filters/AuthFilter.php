<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('auth');
        }

        // Obtener ruta actual y método HTTP
        $currentRoute = uri_string();
        $method = $request->getMethod();

        // Rutas permitidas para todos los roles (incluyendo métodos POST)
        $allowedRoutes = [
            'auth',
            'auth/login',
            'auth/process_forgot_password',
            'auth/reset_password/*', // Permite cualquier token
            'auth/process_reset_password',
            'password/reset/*',
            'newsfeed',
            'newsfeed/crear',
            'newsfeed/comentar',
            'newsfeed/like',
            'password',
            'password/update',
            'publicacion',
            'categorias',
            'proyectos',
            'seleccion_proyectos',
            'seleccion_categoria'
        ];

        // Verificar si es una ruta de reset con token
        if (str_starts_with($currentRoute, 'auth/reset_password/')) {
            return; // Permitir acceso sin autenticación
        }

        

        // Redirigir si no está autenticado
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('auth');
        }

        // Permitir acceso a rutas específicas
        if (in_array($currentRoute, $allowedRoutes)) {
            return;
        }

        // Validar prefijo según rol
        $perfil = $session->get('perfil');
        $allowedPrefix = match ($perfil) {
            'admin' => 'admin',
            'contratista' => 'contratista',
            'publico' => 'publico',
            default => ''
        };

        // Si la ruta no coincide con el prefijo permitido
        if ($allowedPrefix && !str_starts_with($currentRoute, $allowedPrefix)) {
            return redirect()->to('newsfeed');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No es necesario implementar esto
    }
}
