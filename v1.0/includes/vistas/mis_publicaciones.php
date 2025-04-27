<?php
require_once '../conection/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

try {

    // Obtener proyectos y calcular progreso
    $stmt = $conn->prepare("
        SELECT p.*, 
               SUM(pub.peso) AS progreso 
        FROM proyecto p
        LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
        WHERE p.id_contratista = :id_contratista
        GROUP BY p.id_proyectos
    ");
    $stmt->bindParam(':id_contratista', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .etapa-badge {
            font-size: 0.9em;
            padding: 0.5em 1em;
        }
        .card:hover {
            transform: translateY(-5px);
            transition: 0.3s;
        }
        .progress {
            height: 25px;
            border-radius: 20px;
        }

        
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-primary"><i class="fas fa-project-diagram"></i> Mis Proyectos</h1>
        
        <?php if (count($proyectos) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($proyectos as $proyecto): 
                    $colorEtapa = match($proyecto['etapa']) {
                        'planificacion' => 'bg-warning',
                        'ejecucion'    => 'bg-info',
                        'finalizado'   => 'bg-success',
                        default        => 'bg-secondary'
                    };
                    $progreso = min($proyecto['progreso'] ?? 0, 100);
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="etapa-badge <?= $colorEtapa ?> text-white rounded-pill">
                                <?= ucfirst($proyecto['etapa']) ?>
                            </span>
                            <small class="text-muted">#<?= $proyecto['id_proyectos'] ?></small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($proyecto['titulo']) ?></h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Progreso:</label>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped" 
                                         role="progressbar" 
                                         style="width: <?= $progreso ?>%"
                                         aria-valuenow="<?= $progreso ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= $progreso ?>%
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Presupuesto:</span>
                                <strong class="text-success">
                                    $<?= number_format($proyecto['presupuesto'], 2) ?>
                                </strong>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
    <a href="index.php?view=todas_publicaciones&id_proyecto=<?= htmlspecialchars($proyecto['id_proyectos']) ?>" 
       class="btn btn-primary w-100">
       Ver publicaciones
    </a>
</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No tienes proyectos asignados.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>