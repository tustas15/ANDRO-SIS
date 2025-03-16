<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        
        // Redirigir si ya está autenticado
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }
    }

    public function login()
    {
        return view('login_view');
    }

    public function auth()
    {
        $validation = $this->validate([
            'correo' => 'required|valid_email',
            'contrasena' => 'required|min_length[6]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('correo', $this->request->getPost('correo'))->first();

        if (!$user || !password_verify($this->request->getPost('contrasena'), $user['contrasena'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas');
        }

        $this->setUserSession($user);
        return redirect()->to($this->getRedirectUrl());
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    private function setUserSession($user)
    {
        session()->set([
            'id_usuario' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'correo' => $user['correo'],
            'perfil' => $user['perfil'],
            'logged_in' => true
        ]);
    }

    private function getRedirectUrl()
    {
        return match(session()->get('perfil')) {
            'admin' => '/admin/dashboard',
            'contratista' => '/contratista/dashboard',
            default => '/'
        };
    }
}