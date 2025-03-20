<?php
// app/Controllers/Auth.php
namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    protected $session;

    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->session = \Config\Services::session();
    //     helper(['form', 'url']);
    // }

    public function index()
    {
        $session = \Config\Services::session(); // ✅ Correcto

        if ($session->has('user_id')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        $session = \Config\Services::session();

        // Depurar entrada
        $correo = $this->request->getPost('correo');
        $contrasena = $this->request->getPost('contrasena');
        log_message('error', "Intento de login: $correo / $contrasena");

        $userModel = new UsuarioModel();
        $user = $userModel->where('correo', $correo)->first();

        // Verificar si se encontró el usuario
        if (!$user) {
            log_message('error', "Usuario no encontrado: $correo");
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas');
        }

        // Depurar hash almacenado
        log_message('error', "Hash en BD: " . $user['contrasena']);

        // Verificar contraseña
        if (!password_verify($contrasena, $user['contrasena'])) {
            log_message('error', "Contraseña no coincide para: $correo");
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas');
        }
        $rules = [
            'correo' => 'required|valid_email',
            'contrasena' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UsuarioModel();
        $user = $userModel->where('correo', $this->request->getPost('correo'))->first();

        if (!$user || !password_verify($this->request->getPost('contrasena'), $user['contrasena'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas');
        }

        if ($user['estado'] == 'desactivo') {
            return redirect()->back()->withInput()->with('error', 'Cuenta desactivada');
        }

        // Configurar sesión CORRECTAMENTE
        $this->session->set([
            'id_usuario' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido'],
            'perfil' => $user['perfil'], // ← Campo CRUCIAL
            'imagen_perfil' => $user['imagen_perfil'],
            'isLoggedIn' => true
        ]);

        // Redirección específica para admin
        if ($user['perfil'] === 'admin') {
            return redirect()->to('newsfeed');
        }

        return redirect()->to('newsfeed');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function processForgotPassword()
    {
        $rules = [
            'correo' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UsuarioModel();
        $user = $userModel->where('correo', $this->request->getPost('correo'))->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'No existe una cuenta con este correo');
        }

        // Generate token and save to database
        $token = bin2hex(random_bytes(32));
        $recoveryModel = new \App\Models\RecuperacionModel();

        $recoveryData = [
            'id_usuario' => $user['id_usuario'],
            'token' => $token,
            'usado' => 0
        ];

        $recoveryModel->insert($recoveryData);

        // Send email with reset link
        $email = \Config\Services::email();
        $email->setTo($user['correo']);
        $email->setSubject('Recuperación de contraseña');
        $email->setMessage('Para recuperar tu contraseña, haz clic en el siguiente enlace: '
            . base_url('auth/reset_password/' . $token));
        $email->send();

        return redirect()->back()->with('message', 'Se ha enviado un correo con instrucciones para recuperar tu contraseña');
    }

    public function resetPassword($token)
    {
        
        $recoveryModel = new \App\Models\RecuperacionModel();
        $recovery = $recoveryModel->where('token', $token)
            ->where('usado', 0)
            ->first();

        if (!$recovery) {
            return redirect()->to('auth')->with('error', 'Token inválido o ya utilizado');
        }

        // Check if token is less than 24 hours old
        $createdDate = new \DateTime($recovery['fecha_solicitud']);
        $now = new \DateTime();
        $diff = $now->diff($createdDate);

        if ($diff->days > 0) {
            return redirect()->to('auth')->with('error', 'El token ha expirado');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function processResetPassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $recoveryModel = new \App\Models\RecuperacionModel();
        $recovery = $recoveryModel->where('token', $token)
            ->where('usado', 0)
            ->first();

        if (!$recovery) {
            return redirect()->to('auth')->with('error', 'Token inválido o ya utilizado');
        }

        // Update password
        $userModel = new UsuarioModel();
        $userModel->update($recovery['id_usuario'], [
            'contrasena' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        // Mark token as used
        $recoveryModel->update($recovery['id_recuperacion'], ['usado' => 1]);

        return redirect()->to('auth')->with('message', 'Contraseña actualizada correctamente');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('auth');
    }
}
