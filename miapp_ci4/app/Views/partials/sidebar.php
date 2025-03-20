<div class="left_row">
    <div class="left_row_profile">
        <img id="portada" src="<?= base_url('images/portada.jpg') ?>" alt="Portada" />
        <div class="left_row_profile">
            <img id="profile_pic" src="<?= base_url(session('/imagen/imagen_perfil') ?? 'images/user.jpg') ?>" alt="Perfil" />
            <span><?= esc(session('nombre')) ?? 'Usuario' ?><br>
                <p>150k followers / 50 follow</p>
            </span>
        </div>
    </div>
    <div class="rowmenu">
        <ul>
            <li><a href="index.html" id="rowmenu-selected"><i class="fa fa-globe"></i>Newsfeed</a></li>
            <li><a href="profile.html"><i class="fa fa-user"></i>Profile</a></li>
            <li><a href="friends.html"><i class="fa fa-users"></i>Friends</a></li>
            <li class="primarymenu"><i class="fa fa-compass"></i>Explore</li>
            <ul>
                <li style="border:none"><a href="#">Activity</a></li>
                <li style="border:none"><a href="#">Friends</a></li>
                <li style="border:none"><a href="#">Groups</a></li>
                <li style="border:none"><a href="#">Pages</a></li>
                <li style="border:none"><a href="#">Saves</a></li>
            </ul>
            <li class="primarymenu"><i class="fa fa-user"></i>Rapid Access</li>
            <ul>
                <li style="border:none"><a href="#">Your-Page.html</a></li>
                <li style="border:none"><a href="#">Your-Group.html</a></li>
            </ul>
        </ul>
    </div>
</div>

