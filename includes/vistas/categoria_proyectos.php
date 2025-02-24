<?php
require_once '../conection/conexion.php';

$id_categoria = $_GET['id_categoria'] ?? 0;

try {
    // Obtener el nombre de la categoría
    $stmt_categoria = $conn->prepare("SELECT nombre FROM categorias WHERE id_categoria = ?");
    $stmt_categoria->execute([$id_categoria]);
    $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra la categoría, mostrar un mensaje de error
    if (!$categoria) {
        die("Categoría no encontrada");
    }

    // Obtener los proyectos de la categoría
    $stmt_proyectos = $conn->prepare("
        SELECT id_proyecto, titulo 
        FROM proyectos 
        WHERE id_categoria = ?
    ");
    $stmt_proyectos->execute([$id_categoria]);
    $proyectos = $stmt_proyectos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            padding: 20px;
            background-color:#007bff;
            color: white;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
        }

        li {
            background-color: white;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        li:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        a {
            text-decoration: none;
            color:#007bff;
            font-size: 1.1em;
        }

        a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>

<h2><?= htmlspecialchars($categoria['nombre']) ?></h2>
<ul>
    <?php foreach ($proyectos as $proyecto): ?>
        <li>
            <a href="index.php?view=proyecto_publicaciones&id_proyecto=<?= $proyecto['id_proyecto'] ?>">
                <?= htmlspecialchars($proyecto['titulo']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>
