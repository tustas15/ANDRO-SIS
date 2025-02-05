<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'contratista') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $etapa = $_POST['etapa'];
    $id_contratista = $_SESSION['id_usuario'];

    // Insertar la nueva publicación
    $query = "INSERT INTO Proyectos (id_contratista, titulo, descripcion, etapa) VALUES (:id_contratista, :titulo, :descripcion, :etapa)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':id_contratista' => $id_contratista,
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':etapa' => $etapa
    ]);

    header('Location: mis_publicaciones.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Publicación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Crear Nueva Publicación</h1>
    <form method="POST" action="">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required><br>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea><br>

        <label for="etapa">Etapa:</label>
        <select id="etapa" name="etapa" required>
            <option value="planificacion">Planificación</option>
            <option value="ejecucion">Ejecución</option>
            <option value="finalizado">Finalizado</option>
        </select><br>

        <button type="submit">Publicar</button>
    </form>
</body>
</html>