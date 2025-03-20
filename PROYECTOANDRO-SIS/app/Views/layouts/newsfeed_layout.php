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
        echo $this->include('partials/navbar_admin');
    } elseif ($perfil === 'contratista') {
        echo $this->include('partials/navbar_contratista');
    } else {
        echo $this->include('partials/navbar_publico');
    }
    ?>

    <div class="all">
        <div class="rowfixed"></div>
        <?= $this->include('partials/sidebar') ?>

        <div class="right_row">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('partials/suggestions') ?>
    </div>

    <?= $this->include('partials/modals') ?>
    <?= $this->include('partials/footer') ?>
</body>

</html>