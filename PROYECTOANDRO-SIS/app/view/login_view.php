<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Login - Sistema</title>
    <link rel="shortcut icon" href="../../public/images/logo_esperanza.png" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="../../public/css/login/util.css">
    <link rel="stylesheet" href="../../public/css/login/main.css">

    <!-- Icons FontAwesome 4.7.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <a href="index."><img src="../../public/images/logo_esperanza.png" alt="Logo del sistema"></a>
                </div>

                <form class="login100-form validate-form" method="POST" action="/auth/login">
                    <span class="login100-form-title">
                        Inicio de Sesión
                    </span>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-4">
                        <?= $error ?>
                    </div>
                    <?php endif; ?>

                    <div class="wrap-input100 validate-input" data-validate="Correo requerido">
                        <input class="input100" type="email" name="correo" placeholder="Correo" required
                            value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Contraseña requerida">
                        <input class="input100" type="password" name="password" placeholder="Contraseña" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Ingresar
                        </button>
                    </div>

                    <div class="text-center p-t-12">
                        <a class="txt2" href="/auth/recuperar">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="../view/register_view.php">
                            Crear nueva cuenta
                            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../public/js/jquery/jquery-3.2.1.min.js"></script>
    <script src="../../public/js/popper.js"></script>
    <script src="../../public/js/tilt.jquery.min.js"></script>
    <script >
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
</body>
</html>