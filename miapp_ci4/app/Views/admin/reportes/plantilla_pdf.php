<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
        }

        .header {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.8em;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1><?= $titulo ?></h1>
        <p>Generado el: <?= $fecha ?></p>
    </div>

    <?php if (isset($contratistas)): ?>
        <h3>Contratistas</h3>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Proyectos</th>
                <th>Publicaciones</th>
                <th>Likes</th>
                <th>Comentarios</th>
                <th>Presupuesto</th>
            </tr>
            <?php foreach ($contratistas as $c): ?>
                <tr>
                    <td><?= $c['nombre'] ?> <?= $c['apellido'] ?></td>
                    <td><?= $c['total_proyectos'] ?></td>
                    <td><?= $c['total_publicaciones'] ?></td>
                    <td><?= $c['total_megustas'] ?></td>
                    <td><?= $c['total_comentarios'] ?></td>
                    <td>$<?= number_format($c['presupuesto_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (isset($proyectos)): ?>
        <h3>Proyectos</h3>
        <table>
            <tr>
                <th>Proyecto</th>
                <th>Contratista</th>
                <th>Categoría</th>
                <th>Presupuesto</th>
                <th>Avance</th>
                <th>Publicaciones</th>
            </tr>
            <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td><?= $p['titulo'] ?></td>
                    <td><?= $p['contratista'] ?></td>
                    <td><?= $p['categoria'] ?></td>
                    <td>$<?= number_format($p['presupuesto'], 2) ?></td>
                    <td><?= $p['avance'] ?>%</td>
                    <td><?= $p['total_publicaciones'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (isset($categorias)): ?>
        <h3>Categorías</h3>
        <table>
            <tr>
                <th>Categoría</th>
                <th>Proyectos</th>
                <th>Presupuesto</th>
                <th>Avance Promedio</th>
            </tr>
            <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td><?= $cat['nombre'] ?></td>
                    <td><?= $cat['total_proyectos'] ?></td>
                    <td>$<?= number_format($cat['presupuesto_total'], 2) ?></td>
                    <td><?= number_format($cat['promedio_avance'], 2) ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <?php if (isset($contratistas)): ?>
        <!-- Agregar después de la tabla -->
        <div style="page-break-before: always;">
            <h3>Distribución de Proyectos</h3>
            <svg width="600" height="400">
                <?php
                $colors = ['#4e73df', '#1cc88a', '#36b9cc'];
                $total = array_sum(array_column($contratistas, 'total_proyectos'));
                $current = 0;
                $radius = 150;
                $center = 200;
                $i = 0;

                foreach ($contratistas as $c) {
                    $percentage = ($c['total_proyectos'] / $total) * 360;
                    $x1 = $center + $radius * cos(deg2rad($current));
                    $y1 = $center + $radius * sin(deg2rad($current));
                    $x2 = $center + $radius * cos(deg2rad($current + $percentage));
                    $y2 = $center + $radius * sin(deg2rad($current + $percentage));

                    echo '<path d="M' . $center . ',' . $center . ' L' . $x1 . ',' . $y1 . ' A' . $radius . ',' . $radius . ' 0 ' . ($percentage > 180 ? 1 : 0) . ',1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $colors[$i++ % count($colors)] . '"/>';
                    $current += $percentage;
                }
                ?>
            </svg>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Sistema de Reportes - <?= date('Y') ?></p>
        <p>Presupuesto Total: $<?= number_format($estadisticasGlobales['presupuesto_total'], 2) ?></p>
    </div>
</body>

</html>