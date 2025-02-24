<?php
require_once '../conection/conexion.php';

$id_proyecto = $_GET['id_proyecto'] ?? 0;

try {
    // Obtener los datos del proyecto
    $stmt = $conn->prepare("SELECT titulo, fecha_publicacion, etapa FROM proyecto WHERE id_proyectos = ?");
    $stmt->execute([$id_proyecto]);
    $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener las publicaciones asociadas al proyecto
    $stmtpublicacion = $conn->prepare("SELECT titulo, descripcion,fecha_publicacion, imagen FROM publicacion WHERE id_proyectos = ?");
    $stmtpublicacion->execute([$id_proyecto]);
    $proyectos = $stmtpublicacion->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fb;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2 {
            background-color:  #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        ul {
            list-style: none;
            padding: 20px;
        }
        li {
            background-color: #ffffff;
            border: 1px solid #A6D1E6;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #004C8C;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        p {
            color: #5A9BD5;
            line-height: 1.6;
        }
        .imagen-publicacion {
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            margin: 20px 0;
            display: block;
        }
        .etapa {
            background-color:  #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        small {
            display: block;
            margin-top: 10px;
            color: #A6D1E6;
            text-align: right;
            font-size: 0.9em;
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
    <?php if ($publicacion): ?>
        <h2><?= htmlspecialchars($publicacion['titulo']) ?></h2>
        <div class="etapa">Etapa: <?= htmlspecialchars($publicacion['etapa']) ?></div>
        <small>Fecha de inicio: <?= htmlspecialchars($publicacion['fecha_publicacion']) ?></small>
        <ul>
            <?php foreach ($proyectos as $proyecto): ?>
                <li>
                    <h3><?= htmlspecialchars($proyecto['titulo']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                    <?php if (!empty($proyecto['imagen'])): ?>
                        <img src="<?= htmlspecialchars($proyecto['imagen']) ?>" alt="Imagen del proyecto" class="imagen-publicacion">
                    <?php endif; ?>
                    
                    <small>Fecha de publicación: <?= htmlspecialchars($proyecto['fecha_publicacion']) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h2>No se encontró información sobre el proyecto.</h2>
    <?php endif; ?>
</body>
</html>
