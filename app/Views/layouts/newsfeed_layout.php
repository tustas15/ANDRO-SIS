<!-- app/Views/layouts/newsfeed_layout.php -->
<!DOCTYPE html>
<html lang="en">

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

        <?= $this->include('partials/suggestions') ?>
    </div>

    <?= $this->include('partials/modals') ?>
    <?= $this->include('partials/footer') ?>
</body>

</html>