<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

$mensaje = '';
$alert_type = ''; // Tipo de alerta para Bootstrap

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [código existente sin cambios] ...
    
    try {
        // ... [código existente sin cambios] ...
        $mensaje = "Usuario creado exitosamente.";
        $alert_type = 'success';
    } catch (PDOException $e) {
        $mensaje = "Error al crear el usuario: " . $e->getMessage();
        $alert_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Nuevo Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        a{
            text-decoration: none;
        }
        a:hover{
            color:rgb(43, 68, 95);
            text-decoration: none;
        }
        .custom-card {
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .required-asterisk {
            color: red;
            margin-left: 3px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card custom-card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Crear Nuevo Usuario</h3>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre<span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido<span class="required-asterisk">*</span></label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico<span class="required-asterisk">*</span></label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>

                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña<span class="required-asterisk">*</span></label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="perfil" class="form-label">Perfil<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="perfil" name="perfil" required>
                                        <option value="admin">Administrador</option>
                                        <option value="contratista">Contratista</option>
                                        <option value="publico">Público</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="estado" class="form-label">Estado<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="activo">Activo</option>
                                        <option value="desactivo">Desactivado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Crear Usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>