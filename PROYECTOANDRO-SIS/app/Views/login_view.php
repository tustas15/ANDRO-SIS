<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login - Sistema</title>
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

                <form class="login100-form validate-form" method="POST" action="<?= base_url('auth/login') ?>">
                    <?= csrf_field() ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mb-4">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                    <?php endif; ?>

                    <span class="login100-form-title">
                        Inicio de Sesión
                    </span>

                    <div class="wrap-input100 validate-input" data-validate="Correo requerido">
                        <input class="input100" type="email" name="correo" placeholder="Correo" 
                            value="<?= old('correo') ?>">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('correo')): ?>
                        <div class="text-danger p-b-10"><?= $validation->getError('correo') ?></div>
                    <?php endif; ?>

                    <div class="wrap-input100 validate-input" data-validate="Contraseña requerida">
                        <input class="input100" type="password" name="contrasena" placeholder="Contraseña">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('contrasena')): ?>
                        <div class="text-danger p-b-10"><?= $validation->getError('contrasena') ?></div>
                    <?php endif; ?>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Ingresar
                        </button>
                    </div>

                    <div class="text-center p-t-12">
                        <a class="txt2" href="<?= base_url('auth/recuperar') ?>">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="<?= base_url('auth/registro') ?>">
                            Crear nueva cuenta
                            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                        </a>
                    </div>
                </form>
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