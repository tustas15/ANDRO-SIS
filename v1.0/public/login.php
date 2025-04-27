<?php
session_start(); 
require_once '../conection/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturamos los datos del formulario
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    try {
        // Preparar consulta para verificar el correo y la contraseña
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si existe el usuario y si las contraseñas coinciden
        if ($user && $password === $user['contrasena']) {
            // Credenciales correctas: guardar el nombre en la sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];  // Guardar el ID del usuario
            $_SESSION['nombre'] = $user['nombre'];  // Guardamos el nombre del usuario
            $_SESSION['perfil'] = $user['perfil'];
            switch ($user['perfil']) {
                case 'admin':
                    header("Location: ../includes/index.php?view=categorias");
                    break;
                case 'contratista':
                    header("Location: ../includes/index.php?view=proyectos");
                    break;
                case 'publico':
                default:
                    header("Location: ../includes/index.php?view=proyectos");
                    break;
            }
            exit;
        } else {
            // Credenciales incorrectas
            $error = "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error al conectar con la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="../assets/css/login.css" rel="stylesheet" />
</head>
<body>
    <form class="form" method="POST" action="">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="flex-column">
            <label>Correo</label>
        </div>
        <div class="inputForm">
            <input type="text" name="correo" class="input" placeholder="Ingresa tu correo" required />
        </div>

        <div class="flex-column">
            <label>Contraseña</label>
        </div>
        <div class="inputForm">
            <input type="password" name="password" class="input" placeholder="Ingresa tu contraseña" required />
        </div>

        <div class="flex-row">
            <div>
                <input type="checkbox" name="remember" />
                <label>Recuérdame</label>
            </div>
            <a href="#" class="span">¿Olvidó su contraseña?</a>
        </div>
        <button type="submit" class="button-submit">Iniciar sesión</button>
        <p class="p">¿No tienes una cuenta? <a href="#" class="span">Regístrate</a></p>
    </form>
</body>
</html>