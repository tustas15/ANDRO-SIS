<!-- app/Views/auth/verificar.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Verificar-Recuperacion - Sistema</title>
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
                    <a href="<?= base_url() ?>"><img src="<?= base_url('images/logo_esperanza.png') ?>" alt="Logo"></a>
                </div>

                <?= form_open('auth/verificar-codigo', ['class' => 'login100-form validate-form']) ?>
                    <?php if (session('error')): ?>
                        <div class="alert alert-danger mb-4"><?= session('error') ?></div>
                    <?php endif; ?>

                    <span class="login100-form-title">Verificación de Recuperación</span>

                    <div class="wrap-input100 validate-input" data-validate="Código requerido">
                        <input class="input100" type="text" name="codigo" placeholder="Código de 6 dígitos" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-shield" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">Verificar</button>
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
</body>
</html>