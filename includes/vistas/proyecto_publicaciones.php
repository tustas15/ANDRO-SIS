<?php
require_once '../conection/conexion.php';

$id_proyecto = $_GET['id_proyecto'] ?? 0;

try {
    $stmt = $conn->prepare("
        SELECT titulo, descripcion, fecha_publicacion, imagen, etapa 
        FROM proyectos 
        WHERE id_proyecto = ?
    ");
    $stmt->execute([$id_proyecto]);
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-color: #004C8C;
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

        small {
            display: block;
            margin-top: 10px;
            color: #A6D1E6;
        }

        /* Estilo para la fecha de publicación */
        small {
            font-size: 0.9em;
            text-align: right;
        }

        /* Imagen */
        .imagen-publicacion {
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            margin: 20px 0;
            display: block;
        }

        /* Estilo para la etapa */
        .etapa {
            background-color: #A6D1E6;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

    </style>
</head>
<body>

<ul>
    <?php foreach ($publicaciones as $publicacion): ?>
        <li>
            <h3><?= htmlspecialchars($publicacion['titulo']) ?></h3>
            <p><?= nl2br(htmlspecialchars($publicacion['descripcion'])) ?></p>
            
            <!-- Mostrar imagen -->
            <?php if (!empty($publicacion['imagen'])): ?>
                <img src="<?= htmlspecialchars($publicacion['imagen']) ?>" alt="Imagen del proyecto" class="imagen-publicacion">
            <?php endif; ?>
            
            <!-- Mostrar etapa -->
            <div class="etapa"><?= htmlspecialchars($publicacion['etapa']) ?></div>
            
            <small><?= $publicacion['fecha_publicacion'] ?></small>
            
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>
