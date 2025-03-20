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

        // Permitir acceso a newsfeed para todos los roles
        if (uri_string() == 'newsfeed' && $session->get('isLoggedIn')) {
            return;
        }

        // Redirección según rol
        $perfil = $session->get('perfil');
        $currentRoute = $request->getUri()->getPath();

        if ($perfil == 'admin' && !str_starts_with($currentRoute, 'admin')) {
            return redirect()->to('newsfeed');
        }

        if ($perfil == 'contratista' && !str_starts_with($currentRoute, 'contratista')) {
            return redirect()->to('contratista/proyectos');
        }

        if ($perfil == 'publico' && !str_starts_with($currentRoute, 'publico')) {
            return redirect()->to('publico/proyectos');
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No es necesario implementar esto
    }
}
