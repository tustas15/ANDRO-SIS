<!-- app/Views/layouts/newsfeed_layout.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->include('partials/header') ?>
</head>

<body>
    <?= $this->include('partials/navbar') ?>

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
        
        <div class="right_row" style="width:80%;">
            <?= $this->renderSection('content') ?>
        </div>
        <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-arrow-up"></i></button>

        
    </div>

    <?= $this->include('partials/modals') ?>
    <?= $this->include('partials/footer') ?>
</body>

</html>