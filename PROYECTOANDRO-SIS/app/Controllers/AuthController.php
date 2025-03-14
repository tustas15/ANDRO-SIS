<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller {
    public function login() {
        helper(['form', 'url']);
        
        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();
            
            $rules = [
                'email' => 'required|valid_email',
                'pass' => 'required|min_length[6]'
            ];
            
            if ($this->validate($rules)) {
                $model = new UserModel();
                $user = $model->where('correo', $this->request->getPost('email'))->first();
                
                if ($user && password_verify($this->request->getPost('pass'), $user['contrasena'])) {
                    $session = session();
                    $session->set([
                        'id_usuario' => $user['id_usuario'],
                        'nombre' => $user['nombre'],
                        'perfil' => $user['perfil'],
                        'logged_in' => true
                    ]);
                    
                    // Redirección según perfil
                    switch ($user['perfil']) {
                        case 'admin':
                            return redirect()->to('/admin/dashboard');
                        case 'contratista':
                            return redirect()->to('/contratista/dashboard');
                        default:
                            return redirect()->to('/');
                    }
                } else {
                    return redirect()->back()->withInput()->with('error', 'Credenciales inválidas');
                }
            } else {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
        }
        return view('login_view.php');
    }
    
    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}