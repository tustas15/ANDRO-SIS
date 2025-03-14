<?php
session_start();
include_once "";

// Verifica si el usuario está autenticado
if (isset($_SESSION['user_id']) && isset($_SESSION['tipo_usuario'])) {
    // Si el usuario ya está autenticado, redirige según su tipo de usuario
    switch ($_SESSION['tipo_usuario']) {
        case 1: // Administrador
            header("Location: ./admin/indexAd.php");
            break;
        case 2: // Entrenador
            header("Location: ./entrenador/indexEntrenador.php");
            break;
        default:
            // Si el tipo de usuario no está definido o es incorrecto, redirige a la página de inicio de sesión
            header("Location: ./public/index.php");
            break;
    }
    exit(); // Asegúrate de que el código no siga ejecutándose después de la redirección
} else {
    // Si el usuario no está autenticado, redirige a la página de inicio de sesión
    header("Location: ./app/view/login_view.php");
    exit();
}
?>
