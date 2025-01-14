<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Iniciar la sesión si no está iniciada
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
    <div class="title">ANDRO - SIS</div>
    <div class="icons">
        <i class="fas fa-bell" aria-label="Notificaciones"></i>
        <div class="profile">
            <i class="fas fa-user-circle" aria-label="Perfil de usuario"></i>
            <div class="nombreperfil">
                <?php
                if (isset($_SESSION['nombre'])) {
                    echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8');  // Evitar inyección XSS
                } else {
                    echo "Invitado";  // Si no está autenticado, mostrar "Invitado"
                }
                ?>
            </div>
        </div>
    </div>
</header>
