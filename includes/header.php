<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="header">
    
    <div class="left-section">
        <?php if (isset($_SESSION['perfil']) && ($_SESSION['perfil'] === 'admin' || $_SESSION['perfil'] === 'contratista')): ?>
            <i class="fas fa-bars menu-icon" aria-label="Abrir menú"></i>
        <?php endif; ?>
        <div class="search-bar">
            <i class="fas fa-search" aria-label="Buscar"></i>
            <input type="text" placeholder="Buscar..." aria-label="Caja de búsqueda">
        </div>
    </div>
    <a href="index.php?view=proyectos" class="title">ANDRO - SIS</a>
    <div class="icons">
        <i class="fas fa-bell" aria-label="Notificaciones"></i>
        <div class="profile">
            <i class="fas fa-user-circle" id="user-icon" aria-label="Perfil de usuario"></i>
            <div class="nombreperfil">
                <?php
                if (isset($_SESSION['nombre'])) {
                    echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8');
                } else {
                    echo "Invitado";
                }
                ?>
            </div>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="index.php?view=perfil">Perfil</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </div>
</header>