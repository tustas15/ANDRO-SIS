<div class="navbar">
    <div class="navbar_menuicon" id="navicon">
        <i class="fa fa-navicon"></i>
    </div>
    <div class="navbar_logo">
        <img src="<?= base_url('images/logo_esperanza-300px.png') ?>" alt="Logo" />
    </div>
    <div class="navbar_page">
        <span> ANDRO-SIS</span>
    </div>
    <div class="navbar_search">
        <!-- <form method="GET" action="/">
            <input type="text" placeholder="Search people.." />
            <button type="submit"><i class="fa fa-search"></i></button>
        </form> -->
    </div>
    <div class="navbar_icons">
        <ul>
            <!-- <li id="friendsmodal"><i class="fa fa-user-o"></i><span id="notification">5</span></li> -->
            <li id="messagesmodal">
                <a href="<?= site_url('chat') ?>" class="selected-orange">
                    <i class="fa fa-comments-o"></i>
                </a>
            </li>
            <!-- <a href="#" style="color:white">
                <li><i class="fa fa-globe"></i></li>
            </a> -->
        </ul>
    </div>
    <div class="navbar_user" id="profilemodal" style="cursor:pointer">
        <img src="<?= site_url('images/usuarios/' . (session('imagen_perfil') ?? 'user.jpg')) ?>"
            alt="Perfil"
            class="user-avatar">

        <span id="navbar_user_top">
            <?= esc(session('nombre') ?? 'Usuario') ?> <?= esc(session('apellido') ?? '') ?><br>
            <p><?= esc(session('perfil') ?? 'Perfil') ?></p>
        </span>

        <i class="fa fa-angle-down"></i>
    </div>
</div>