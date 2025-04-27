<?php
session_start();

if (isset($_SESSION['nombre'])) {
    echo $_SESSION['nombre'];  // Devolver el nombre del usuario si está en sesión
} else {
    echo 'Invitado';  // Si no está autenticado, devolver 'Invitado'
}
