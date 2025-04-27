<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        $currentRoute = uri_string();
        if (strpos($currentRoute, 'admin/reportes/exportar') === 0) {
            return;
        }

        if (!$session->get('isLoggedIn') || $session->get('perfil') !== 'admin') {
            // Evitar bucle de redirección
            if (current_url() != site_url('auth')) {
                return redirect()->to('auth')->with('error', 'Acceso no autorizado');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
