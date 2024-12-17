<?php
session_start();  // Iniciar la sesión para acceder a las variables de sesión
?>
<div class="left-section">
    <?php 
    if (isset($_SESSION['perfil']) && ($_SESSION['perfil'] === 'admin' || $_SESSION['perfil'] === 'contratista')) { 
    ?>
        <i class="fas fa-bars menu-icon"></i> 
    <?php 
    } 
    ?>
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Buscar...">
    </div>
</div>
<div class="title">ANDRO - SIS</div>
<div class="icons">
    <i class="fas fa-bell"></i> 
    <div class="profile">
        <i class="fas fa-user-circle"></i>
        <div class="nombreperfil">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo $_SESSION['nombre'];  // Mostrar el nombre del usuario
            } else {
                echo "Invitado";  // Si no está autenticado, mostrar "Invitado"
            }
            ?>
        </div>
    </div>
</div>
