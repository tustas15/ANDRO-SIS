
<!-- app/Views/auth/reset_password.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Restablecer Contraseña - Sistema</title>
    <link rel="shortcut icon" href="<?= base_url('images/logo_esperanza.png') ?>" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('css/login/util.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login/main.css') ?>">

    <!-- Icons FontAwesome 4.7.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <a href="<?= base_url() ?>"><img src="<?= base_url('images/logo_esperanza.png') ?>" alt="Logo del sistema"></a>
                </div>

                <?= form_open('auth/process_reset_password', ['class' => 'login100-form validate-form']) ?>
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= $token ?>">

                <?php if (session('error')): ?>
                    <div class="alert alert-danger mb-4">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>

                <span class="login100-form-title">
                    Nueva Contraseña
                </span>

                <div class="wrap-input100 validate-input" data-validate="Contraseña requerida">
                    <input class="input100" type="password" name="password" placeholder="Nueva Contraseña" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                </div>
                <?php if (session('errors.password')): ?>
                    <div class="text-danger p-b-10"><?= session('errors.password') ?></div>
                <?php endif; ?>

                <div class="wrap-input100 validate-input" data-validate="Confirmar contraseña">
                    <input class="input100" type="password" name="password_confirm" placeholder="Confirmar Contraseña" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                </div>
                <?php if (session('errors.password_confirm')): ?>
                    <div class="text-danger p-b-10"><?= session('errors.password_confirm') ?></div>
                <?php endif; ?>

                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn">
                        Restablecer Contraseña
                    </button>
                </div>

                <div class="text-center p-t-12">
                    <a class="txt2" href="<?= site_url('auth') ?>">
                        <i class="fa fa-arrow-left m-l-5" aria-hidden="true"></i>
                        Volver al Login
                    </a>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>

    <script src="<?= base_url('js/jquery/jquery-3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('js/popper.js') ?>"></script>
    <script src="<?= base_url('js/tilt.jquery.min.js') ?>"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
</body>

</html>