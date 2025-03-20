<div class="navbar">
    <div class="navbar_menuicon" id="navicon">
        <i class="fa fa-navicon"></i>
    </div>
    <div class="navbar_logo">
        <img src="<?= base_url('images/logo.png') ?>" alt="Logo" />
    </div>
    <div class="navbar_page">
        <span> ANDRO-SIS</span>
    </div>
    <div class="navbar_search">
        <form method="GET" action="/">
            <input type="text" placeholder="Search people.." />
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="navbar_icons">
        <ul>
            <li id="friendsmodal"><i class="fa fa-user-o"></i><span id="notification">5</span></li>
            <li id="messagesmodal"><i class="fa fa-comments-o"></i><span id="notification">2</span></li>
            <a href="#" style="color:white">
                <li><i class="fa fa-globe"></i></li>
            </a>
        </ul>
    </div>
    <div class="navbar_user" id="profilemodal" style="cursor:pointer">
        <img src="<?= base_url(session('/imagen/imagen_perfil') ?? 'images/user.jpg') ?>" alt="Perfil" />
        <span id="navbar_user_top"><?= esc(session('nombre')) ?? 'Usuario' ?><br>
            <p><?= session('perfil') ?? 'Perfil' ?></p>
        </span>
        <i class="fa fa-angle-down"></i>
    </div>
</div>