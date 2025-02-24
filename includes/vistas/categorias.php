<?php
require_once '../conection/conexion.php';

try {
    $stmt = $conn->query("
        SELECT c.id_categoria, c.nombre, COUNT(p.id_proyectos) AS total_proyectos
        FROM categorias c
        LEFT JOIN proyecto p ON c.id_categoria = p.id_categoria
        GROUP BY c.id_categoria, c.nombre
    ");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .category-card {
            background-color: #007bff; /* Azul Bootstrap */
            color: white;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 150px;
            position: relative;
        }
        .category-card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        .category-card .count {
            font-size: 2rem;
            font-weight: bold;
        }
        .category-card a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
        }
        .category-card a:hover {
            text-decoration: underline;
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

<div class="container mt-4">
    <div class="row g-3">
        <?php foreach ($categorias as $categoria): ?>
            <div class="col-md-4"> <!-- 3 tarjetas por fila -->
                <div class="category-card">
                    <h3><?= htmlspecialchars($categoria['nombre']) ?></h3>
                    <span class="count"><?= $categoria['total_proyectos'] ?></span>
                    <a href="index.php?view=categoria_proyectos&id_categoria=<?= $categoria['id_categoria'] ?>">
                        Listado <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
