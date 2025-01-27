<?php
// logout.php

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio
header('Location: ../public/login.php');
exit(); // Asegurarse de que el script se detenga después de la redirección
?>