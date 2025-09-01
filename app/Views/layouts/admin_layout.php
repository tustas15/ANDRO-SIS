<!-- app/Views/layouts/admin_layout.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | GAD La Esperanza</title>
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>">

    <!-- CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('dashboard') ?>">
                <img src="<?= base_url('images/logo_esperanza.png') ?>" height="40" alt="Logo">
            </a>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= site_url('newsfeed') ?>">
                            <i class="fa fa-home"></i> Dashboard
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                <img src="assets/images/admin_imagen.jpg"
                        class="rounded-circle me-2" width="35" height="35">
                    <span class="text-light me-3"><?= session('nombre') ?></span>
                    <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-light">
                        <i class="fa fa-sign-out"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="container-fluid mt-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>