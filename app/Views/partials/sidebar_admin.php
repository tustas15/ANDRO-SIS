<div class="left_row">
    <div class="left_row_profile">
        <img id="portada" src="<?= base_url('images/logo_esperanza-300px.png') ?>" alt="Portada" />
        <div class="left_row_profile">
            <img id="profile_pic"
                src="<?= site_url('images/usuarios/' . (session('imagen_perfil') ?? 'user.jpg')) ?>"
                alt="Perfil" />
            <span><?= esc(session('nombre') . ' ' . session('apellido')) ?? 'Usuario' ?><br>
            </span>
        </div>
    </div>
    <div class="rowmenu">
        <ul>
            <li><a href="<?= site_url('newsfeed') ?>" id="rowmenu-selected"><i class="fa fa-globe"></i>Newsfeed</a></li>
            <li><a href="<?= site_url('categorias') ?>"><i class="fa fa-folder"></i>Categorías</a></li>
            <li><a href="<?= site_url('proyectos') ?>"><i class="fa fa-briefcase"></i>Proyectos</a></li>
            <li><a href="<?= site_url('admin/usuarios/crear') ?>"><i class="fa fa-user-plus"></i>Crear Usuarios</a></li>
            <li><a href="<?= site_url('admin/backup') ?>"><i class="fa fa-hdd-o"></i>Base de datos</a></li>
            <li class="primarymenu">
                <i class="fa fa-users"></i>Usuarios
                <ul>
                    <li><a href="<?= site_url('admin/usuarios/administradores') ?>"><i class="fa fa-user"></i>Administradores</a></li>
                    <li><a href="<?= site_url('admin/usuarios/contratistas') ?>"><i class="fa fa-wrench"></i>Contratistas</a></li>
                    <li><a href="<?= site_url('admin/usuarios/ciudadanos') ?>"><i class="fa fa-user"></i>Ciudadanos</a></li>
                </ul>
            </li>
            <li class="primarymenu">
                <i class="fa fa-folder-open"></i> Reportes
                <ul>
                    <li><a href="<?= site_url('admin/reportes/contratistas') ?>"><i class="fa fa-wrench"></i>Contratistas</a></li>
                    <li><a href="<?= site_url('admin/reportes/proyectos') ?>"><i class="fa fa-file-text"></i>Proyectos</a></li>
                    <li><a href="<?= site_url('admin/reportes/categorias') ?>"><i class="fa fa-folder"></i>Categorías</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- NavMobile -->
<div class="mobilemenu">
    <div class="mobilemenu_profile">
        <div class="mobilemenu_profile">
            <img id="mobilemenu_portada" src="<?= base_url('images/logo_esperanza-300px.png') ?>" alt="Portada" />
            <img id="mobilemenu_profile_pic" src="<?= base_url('images/usuarios/' . (session('imagen_perfil') ?? 'user.jpg')) ?>" alt="Perfil" />
            <span><?= esc(session('nombre') . ' ' . session('apellido')) ?? 'Usuario' ?><br>
                <p><?= session('perfil') ?? 'Perfil' ?></p>
            </span>
        </div>
    </div>
    <div class="mobilemenu_menu">
        <ul>
            <!-- Menú principal -->
            <li><a href="<?= site_url('newsfeed') ?>" id="mobilemenu-selected"><i class="fa fa-globe"></i> Newsfeed</a></li>
            <li><a href="<?= site_url('categorias') ?>"><i class="fa fa-folder"></i> Categorías</a></li>
            <li><a href="<?= site_url('proyectos') ?>"><i class="fa fa-briefcase"></i> Proyectos</a></li>
            <li><a href="<?= site_url('admin/usuarios/crear') ?>"><i class="fa fa-user-plus"></i> Crear Usuarios</a></li>
            <li><a href="<?= site_url('admin/backup') ?>"><i class="fa fa-hdd-o"></i> Base de datos</a></li>
            <li><a href="<?= site_url('chat') ?>"><i class="fa fa-comments-o"></i> Mensajes</a></li>
            
            <!-- Menú con subelementos: Usuarios -->
            <li class="primarymenu">
                <a href="#"><i class="fa fa-users"></i> Usuarios</a>
                <ul class="mobilemenu_child">
                    <li><a href="<?= site_url('admin/usuarios/administradores') ?>"><i class="fa fa-user"></i> Administradores</a></li>
                    <li><a href="<?= site_url('admin/usuarios/contratistas') ?>"><i class="fa fa-wrench"></i> Contratistas</a></li>
                    <li><a href="<?= site_url('admin/usuarios/ciudadanos') ?>"><i class="fa fa-user"></i> Ciudadanos</a></li>
                </ul>
            </li>
            
            <!-- Menú con subelementos: Reportes -->
            <li class="primarymenu">
                <a href="#"><i class="fa fa-folder-open"></i> Reportes</a>
                <ul class="mobilemenu_child">
                    <li><a href="<?= site_url('admin/reportes/contratistas') ?>"><i class="fa fa-wrench"></i> Contratistas</a></li>
                    <li><a href="<?= site_url('admin/reportes/proyectos') ?>"><i class="fa fa-file-text"></i> Proyectos</a></li>
                    <li><a href="<?= site_url('admin/reportes/categorias') ?>"><i class="fa fa-folder"></i> Categorías</a></li>
                </ul>
            </li>
        </ul>
        <hr>
        <ul>
            <li><a href="<?= site_url('auth/logout') ?>"><i class="fa fa-power-off"></i> Cerrar sesión</a></li>
        </ul>
    </div>
</div>
