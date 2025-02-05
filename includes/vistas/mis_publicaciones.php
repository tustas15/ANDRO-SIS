<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'contratista') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

$id_contratista = $_SESSION['id_usuario'];

// Consulta para obtener las publicaciones del contratista
$query = "SELECT id_proyecto, titulo, descripcion, etapa, fecha_publicacion FROM Proyectos WHERE id_contratista = :id_contratista";
$stmt = $conn->prepare($query);
$stmt->execute([':id_contratista' => $id_contratista]);
$publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Publicaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Mis Publicaciones</h1>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Etapa</th>
                <th>Fecha de Publicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($publicaciones as $publicacion): ?>
                <tr>
                    <td><?php echo $publicacion['titulo']; ?></td>
                    <td><?php echo $publicacion['descripcion']; ?></td>
                    <td><?php echo $publicacion['etapa']; ?></td>
                    <td><?php echo $publicacion['fecha_publicacion']; ?></td>
                    <td>
                        <a href="editar_publicacion.php?id=<?php echo $publicacion['id_proyecto']; ?>">Editar</a>
                        <a href="eliminar_publicacion.php?id=<?php echo $publicacion['id_proyecto']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>