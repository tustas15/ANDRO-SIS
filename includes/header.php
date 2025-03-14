<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    /* Estilos para el menú de la izquierda */
.left-section {
    position: relative;
    display: flex;
    align-items: center;
}

.menu-container {
    position: relative;
}

#profile-menu {
    display: none;
    position: absolute;
    top: 100%; /* Coloca el menú debajo del ícono */
    left: 0; /* Alinea el menú a la izquierda */
    background-color: #fff;
    border: 1px solid #ccc;
    z-index: 1000; /* Asegura que esté por encima de otros elementos */
}

/* Estilos para el menú de perfil */
.profile {
    position: relative;
}

#dropdown-menu {
    display: none;
    position: absolute;
    top: 100%; /* Coloca el menú debajo del ícono */
    right: 0; /* Alinea el menú a la derecha */
    background-color: #fff;
    border: 1px solid #ccc;
    z-index: 1000; /* Asegura que esté por encima de otros elementos */
}

/* Mostrar el menú cuando se hace clic */
.menu-icon:hover + #profile-menu,
#profile-menu:hover {
    display: block;
}

#user-icon:hover + #dropdown-menu,
#dropdown-menu:hover {
    display: block;
}
</style>
<header class="header">
<div class="left-section">
    <?php if (isset($_SESSION['perfil']) && ($_SESSION['perfil'] === 'admin' || $_SESSION['perfil'] === 'contratista')): ?>
        <div class="menu-container">
            <i class="fas fa-bars menu-icon" aria-label="Abrir menú"></i>
            <div class="dropdown-menu" id="profile-menu">
                <?php if ($_SESSION['perfil'] === 'admin'): ?>
                    <a href="index.php?view=crear_usuario">Nuevo usuario</a>
                    <a href="index.php?view=admin_contratistas">Contratistas</a>
                    <a href="index.php?view=admin_publicos">Usuarios Públicos</a>
                    <a href="index.php?view=admin_NProyecto">Proyectos</a>
                    <a href="index.php?view=admin_NCategoria">Categorias</a>
                <?php elseif ($_SESSION['perfil'] === 'contratista'): ?>
                    <a href="index.php?view=nueva_publicacion">Crear Nueva Publicación</a>
                    <a href="index.php?view=mis_publicaciones">Ver Mis Publicaciones</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
   <!-- <div class="search-bar">
        <i class="fas fa-search" aria-label="Buscar"></i>
        <input type="text" placeholder="Buscar..." aria-label="Caja de búsqueda">
    </div>-->
</div>
<a href="<?php
    if (isset($_SESSION['perfil'])) {
        if ($_SESSION['perfil'] === 'admin') {
            echo 'index.php?view=categorias'; // O la URL que desees para el admin
        } elseif ($_SESSION['perfil'] === 'contratista') {
            echo 'index.php?view=proyectos'; // O la URL que desees para el contratista
        } else {
            echo 'index.php?view=proyectos'; // URL por defecto si el perfil es otro
        }
    } else {
        echo 'index.php?view=proyectos'; // Si no hay sesión, redirige al proyecto
    }
?>" class="title">ANDRO - SIS</a>
    <div class="icons">
    <div class="profile">
        <i class="fas fa-user-circle" id="user-icon" aria-label="Perfil de usuario"></i>
        <div class="nombreperfil">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8');
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.querySelector('.menu-icon');
    const profileMenu = document.querySelector('#profile-menu');
    const userIcon = document.querySelector('#user-icon');
    const dropdownMenu = document.querySelector('#dropdown-menu');

    menuIcon.addEventListener('click', function() {
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
        dropdownMenu.style.display = 'none'; // Oculta el otro menú
    });

    userIcon.addEventListener('click', function() {
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        profileMenu.style.display = 'none'; // Oculta el otro menú
    });

    // Cerrar los menús si se hace clic fuera de ellos
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.menu-icon') && !event.target.matches('.dropdown-menu')) {
            profileMenu.style.display = 'none';
        }
        if (!event.target.matches('#user-icon') && !event.target.matches('.dropdown-menu')) {
            dropdownMenu.style.display = 'none';
        }
    });
});
</script>