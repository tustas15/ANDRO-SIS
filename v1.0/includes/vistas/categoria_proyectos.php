<?php
require_once '../conection/conexion.php';

$id_categoria = $_GET['id_categoria'] ?? 0;

try {
    // Obtener nombre de categoría
    $stmt_categoria = $conn->prepare("SELECT nombre FROM categorias WHERE id_categoria = ?");
    $stmt_categoria->execute([$id_categoria]);
    $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) die("Categoría no encontrada");

    // Obtener proyectos con sumatoria de porcentajes de publicaciones
    $stmt_proyectos = $conn->prepare("
        SELECT 
            p.id_proyectos,
            p.titulo,
            COALESCE(SUM(pb.peso), 0) AS porcentaje_total
        FROM proyecto p
        LEFT JOIN publicacion pb ON p.id_proyectos = pb.id_proyectos
        WHERE p.id_categoria = ?
        GROUP BY p.id_proyectos
    ");
    $stmt_proyectos->execute([$id_categoria]);
    $proyectos = $stmt_proyectos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <style>
        /* Estilos originales preservados */
        body { font-family: Arial; background: #f4f4f9; margin: 0; }
        h2 { text-align: center; background: #007bff; color: white; padding: 20px; }
        ul { list-style: none; padding: 0; margin: 20px auto; width: 80%; max-width: 600px; }
        li { background: white; margin: 10px 0; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        a { color: #007bff; text-decoration: none; font-size: 1.1em; }
        a:hover{color:rgb(22, 60, 102);}

        /* Barra de porcentaje ajustada */
        .porcentaje-bar {
            width: 100%;
            height: 25px;
            background: #e0e0e0;
            border-radius: 12px;
            margin-top: 10px;
            overflow: hidden;
        }

        .porcentaje-fill {
            height: 100%;
            background: #007bff;
            transition: width 0.5s ease;
            position: relative;
        }

        .porcentaje-text {
            position: absolute;
            right: 10px;
            color: white;
            font-size: 0.9em;
            line-height: 25px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<h2><?= htmlspecialchars($categoria['nombre']) ?></h2>
<ul>
    <?php foreach ($proyectos as $proyecto): 
        $porcentaje = number_format($proyecto['porcentaje_total'], 2);
    ?>
        <li>
            <a href="index.php?view=proyecto_publicaciones&id_proyecto=<?= $proyecto['id_proyectos'] ?>">
                <?= htmlspecialchars($proyecto['titulo']) ?>
            </a>
            <div class="porcentaje-bar">
                <div class="porcentaje-fill" style="width: <?= $porcentaje ?>%;">
                    <span class="porcentaje-text"><?= $porcentaje ?>%</span>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>