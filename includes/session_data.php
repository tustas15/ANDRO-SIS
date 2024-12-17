<?php
session_start();

if (isset($_SESSION['nombre'])) {
    echo $_SESSION['nombre'];  // Devolver el nombre del usuario si está en sesión
} else {
    echo 'Invitado';  // Si no está autenticado, devolver 'Invitado'
}

if(isset(($_SESSION['perfil']))){
    echo $_SESSION['perfil'];
} else{
    echo 'perfil no definido';
}