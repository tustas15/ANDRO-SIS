<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

// Consulta para obtener los contratistas
$query = "SELECT id_usuario, nombre, apellido, correo, estado FROM Usuarios WHERE perfil = 'contratista'";
$stmt = $conn->query($query);
$contratistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Contratistas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
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
            <?php foreach ($contratistas as $contratista): ?>
                <tr>
                    <td><?php echo $contratista['id_usuario']; ?></td>
                    <td><?php echo $contratista['nombre']; ?></td>
                    <td><?php echo $contratista['apellido']; ?></td>
                    <td><?php echo $contratista['correo']; ?></td>
                    <td><?php echo $contratista['estado']; ?></td>
                    <td>
                        <a href="editar_contratista.php?id=<?php echo $contratista['id_usuario']; ?>">Editar</a>
                        <a href="cambiar_estado_contratista.php?id=<?php echo $contratista['id_usuario']; ?>">Cambiar Estado</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>