<?php
class AuthController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new LoginModel($conn);
    }

    public function login() {
        session_start();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo']);
            $password = trim($_POST['password']);

            try {
                if ($usuario = $this->model->autenticar($correo, $password)) {
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['perfil'] = $usuario['perfil'];
                    
                    $this->redireccionarSegunPerfil($usuario['perfil']);
                    exit();
                }
                $error = "Credenciales incorrectas";
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        require VIEWS_PATH . '/auth/login_view.php';
    }

    private function redireccionarSegunPerfil($perfil) {
        $rutas = [
            'admin' => '/admin/categorias',
            'contratista' => '/proyectos',
            'publico' => '/proyectos'
        ];
        
        header('Location: ' . ($rutas[$perfil] ?? '/'));
    }
}
?>