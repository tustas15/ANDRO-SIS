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
            <li><a href="<?= site_url('contratista/publicacion') ?>"><i class="fa fa-edit"></i>Publicaciónes</a></li>
        </ul>
    </div>
</div>
<!-- NavMobile -->
<div class="mobilemenu">
    <div class="mobilemenu_profile">
        <!-- <img id="mobilemenu_portada" src="<?= base_url('images/logo_esperanza-300px.png') ?>" alt="Portada" /> -->
        <div class="mobilemenu_profile">
            <img id="mobilemenu_profile_pic" src="<?= base_url('images/usuarios/' . session('imagen_perfil') ?? 'user.jpg') ?>" alt="Perfil" />
            <span><?= esc(session('nombre') . ' ' . session('apellido')) ?? 'Usuario' ?><br>
                <p><?= session('perfil') ?? 'Perfil' ?></p>
            </span>
        </div>
    </div>
    <div class="mobilemenu_menu">
        <ul>
            <li><a href="<?= site_url('newsfeed') ?>"><i class="fa fa-globe"></i> Newsfeed</a></li>
            <li><a href="<?= site_url('contratista/publicacion') ?>"><i class="fa fa-edit"></i> Crear Nueva Publicación</a></li>
        </ul>
        <hr>
        <ul>
            <li><a href="#">Términos & Condiciones</a></li>
            <li><a href="#">FAQ's</a></li>
            <li><a href="#">Contacto</a></li>
            <li><a href="<?= site_url('auth/logout') ?>"><i class="fa fa-power-off"></i> Cerrar sesión</a></li>
        </ul>
    </div>
</div>