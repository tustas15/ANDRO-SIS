<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

// Consulta para obtener los usuarios públicos
$query = "SELECT id_usuario, nombre, apellido, correo, estado FROM Usuarios WHERE perfil = 'publico'";
$stmt = $conn->query($query);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios Públicos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Gestionar Usuarios Públicos</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id_usuario']; ?></td>
                    <td><?php echo $usuario['nombre']; ?></td>
                    <td><?php echo $usuario['apellido']; ?></td>
                    <td><?php echo $usuario['correo']; ?></td>
                    <td><?php echo $usuario['estado']; ?></td>
                    <td>
                        <a href="editar_publico.php?id=<?php echo $usuario['id_usuario']; ?>">Editar</a>
                        <a href="cambiar_estado_publico.php?id=<?php echo $usuario['id_usuario']; ?>">Cambiar Estado</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>