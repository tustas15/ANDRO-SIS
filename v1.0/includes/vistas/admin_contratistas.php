<?php
require_once '../conection/conexion.php';

// Verificar permisos
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: index.php');
    exit();
}


// Obtener contratistas para mostrar la tabla
$query = "SELECT id_usuario, nombre, apellido, correo, estado FROM Usuarios WHERE perfil = 'contratista'";
$stmt = $conn->query($query);
$contratistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CONTRATISTAS</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
        a {
            text-decoration: none;
            color:#007bff;
            font-size: 1.1em;
        }
        a:hover {
            color:rgb(43, 68, 95);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>CONTRATISTAS</h1>
    <div class="container">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contratistas as $contratista): ?>
                <tr>
                    <td><?= htmlspecialchars($contratista['nombre']) ?></td>
                    <td><?= htmlspecialchars($contratista['apellido']) ?></td>
                    <td><?= htmlspecialchars($contratista['correo']) ?></td>
                    <td><?= htmlspecialchars($contratista['estado']) ?></td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   class="toggle-estado" 
                                   data-id="<?= $contratista['id_usuario'] ?>"
                                   <?= ($contratista['estado'] == 'activo') ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
    $('.toggle-estado').change(function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const nuevoEstado = checkbox.prop('checked') ? 'activo' : 'desactivo';
        const estadoTexto = nuevoEstado === 'activo' ? 'activo' : 'desactivo'; // Texto a mostrar
        
        $.ajax({
            url: 'actualizar_estado.php',
            method: 'POST',
            data: {
                id_usuario: id,
                estado: nuevoEstado
            },
            success: function(response) {
                if (response.trim() === 'exito') {
                    // Actualizar el texto del estado en la misma fila
                    checkbox.closest('tr').find('td:eq(3)').text(estadoTexto);
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });
});
    </script>
</body>
</html>