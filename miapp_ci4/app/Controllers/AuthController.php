<?php
// app/Controllers/Auth.php
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RecuperacionModel;
use App\Libraries\ResendService;

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


        $userModel = new UsuarioModel();
        $user = $userModel->where('correo', $correo)->first();

        // Verificar si se encontró el usuario
        if (!$user) {

            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas');
        }

        // Depurar hash almacenado
        log_message('error', "Hash en BD: " . $user['contrasena']);

        // Verificar contraseña
        if (!password_verify($contrasena, $user['contrasena'])) {

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

        // Generar token
        $codigo = bin2hex(random_bytes(32));
        $recoveryModel = new \App\Models\RecuperacionModel();

        // Generar token único
        $token = bin2hex(random_bytes(32));

        // Guardar en recuperacioncontrasenas
        $recoveryModel->insert([
            'id_usuario' => $user['id_usuario'],
            'token' => $token,
            'usado' => 0
        ]);

        // Crear link de recuperación
        $resetLink = base_url("auth/reset_password/{$token}");

        // Enviar email con Resend
        $resend = new \App\Libraries\ResendService();
        $resend->sendEmail(
            $user['correo'],
            'Restablece tu contraseña',
            view('emails/recuperacion', ['resetLink' => $resetLink])
        );

        return redirect()->back()->with('message', 'Se ha enviado un correo con el link de recuperación');
    }
    public function resetPassword($token)
    {
        $recoveryModel = new RecuperacionModel();
        $recovery = $recoveryModel->tokenValido($token);

        if (!$recovery) {
            return redirect()->to('auth')->with('error', 'Enlace inválido o expirado');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function processResetPassword()
    {
        $token = $this->request->getPost('token');
        $recoveryModel = new RecuperacionModel();
        $recovery = $recoveryModel->tokenValido($token);

        if (!$recovery) {
            return redirect()->to('auth')->with('error', 'Enlace inválido o expirado');
        }

        // Validar contraseñas
        $rules = [
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Actualizar contraseña
        $userModel = new UsuarioModel();
        $userModel->update($recovery['id_usuario'], [
            'contrasena' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        // Marcar token como usado
        $recoveryModel->marcarComoUsado($recovery['id_recuperacion']);

        return redirect()->to('auth')->with('success', 'Contraseña restablecida exitosamente');
    }
    public function register()
    {
        // Si ya está logueado redirigir
        if (session()->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        return view('auth/create_ciudadano');
    }

    public function processRegister()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|max_length[100]',
            'apellido' => 'required|max_length[100]',
            'correo' => 'required|valid_email|is_unique[usuarios.correo]',
            'contrasena' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UsuarioModel();

        // Generar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $userData = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'contrasena' => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'perfil' => 'publico',
            'codigo_verificacion' => $codigo,
            'verificado' => 0,
            'estado' => 'desactivo'
        ];
        // Guardar usuario y manejar errores
        if (!$userModel->save($userData)) {
            log_message('error', 'Error al guardar usuario: ' . print_r($userModel->errors(), true));
            return redirect()->back()->withInput()->with('error', 'Error al registrar el usuario. Por favor, intenta nuevamente.');
        }
        try {
            $resend = new \App\Libraries\ResendService();
            $resend->sendEmail(
                $userData['correo'],
                'Verifica tu cuenta',
                view('emails/verificacion', ['codigo' => $codigo])
            );
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar correo de verificación: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al enviar el correo de verificación. Contacta al soporte.');
        }

        return redirect()->to('auth/verificar')->with('success', 'Código enviado a tu correo');
    }
    public function verificar()
    {
        // Si ya está logueado redirigir
        if (session()->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        $data = [
            'title' => 'Verificar Código',
            'error' => session()->getFlashdata('error'),
            'success' => session()->getFlashdata('success')
        ];

        return view('auth/verificar', $data);
    }

    public function verificarCodigo()
    {
        $codigo = $this->request->getPost('codigo');
        $userModel = new UsuarioModel();

        $user = $userModel->where('codigo_verificacion', $codigo)
            ->where('estado', 'desactivo')
            ->first();

        if ($user) {
            $userModel->update($user['id_usuario'], [
                'verificado' => 1,
                'estado' => 'activo',
                'codigo_verificacion' => null
            ]);
            return redirect()->to('auth')->with('success', '¡Cuenta verificada!');
        }

        return redirect()->back()->with('error', 'Código inválido o expirado');
    }

    public function verificarRecuperacion()
    {
        $codigo = $this->request->getPost('codigo');
        $recoveryModel = new RecuperacionModel();

        $recovery = $recoveryModel->where('token', $codigo)
            ->where('usado', 0)
            ->first();

        if ($recovery) {
            // Marcar como usado
            $recoveryModel->update($recovery['id_recuperacion'], ['usado' => 1]);
            // Redirigir a reset password
            return redirect()->to("auth/reset_password/{$codigo}");
        }

        return redirect()->back()->with('error', 'Código inválido o expirado');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('auth');
    }
}
