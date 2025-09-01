<!-- app/Views/layouts/change_password_layout.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <?= $this->include('partials/header') ?>
</head>

<body>
    <?php
    $perfil = session('perfil');
    if ($perfil === 'admin') {
        echo $this->include('partials/navbar');
    } elseif ($perfil === 'contratista') {
        echo $this->include('partials/navbar');
    } else {
        echo $this->include('partials/navbar_publico.php');
    }
    ?>

    <div class="all">
        <div class="rowfixed"></div>

        <?php
        $perfil = session('perfil');
        if ($perfil === 'admin') {
            echo $this->include('partials/sidebar_admin');
        } elseif ($perfil === 'contratista') {
            echo $this->include('partials/sidebar_contratista');
        } else {
            echo $this->include('partials/sidebar_publico');
        }
        ?>
        <div class="right_row">
            <?= $this->renderSection('content') ?>
        </div>

        <div class="suggestions_row">
            <div class="row shadow">
                <div class="row_title">
                    <span>Configuración</span>
                </div>
                <div class="menusetting_contain">
                    <ul>
                        <li>
                            <a href="<?= site_url('auth/perfil') ?>">Informacion Personal</a>
                        </li>
                        <li>
                            <a href="<?= site_url('password') ?>">Cambio de Contraseña</a>
                        </li>
                        <li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-arrow-up"></i></button>


    <?= $this->include('partials/modals') ?>
    <?= $this->include('partials/footer') ?>
</body>

</html>