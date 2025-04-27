<?php
require_once '../conection/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id_proyecto'])) {
    die("Error: Proyecto no especificado.");
}

$id_proyecto = intval($_GET['id_proyecto']);

try {
    // Obtener información del proyecto
    $stmtProyecto = $conn->prepare("SELECT titulo FROM proyecto WHERE id_proyectos = :id_proyecto");
    $stmtProyecto->bindParam(':id_proyecto', $id_proyecto, PDO::PARAM_INT);
    $stmtProyecto->execute();
    $proyecto = $stmtProyecto->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {
        die("Error: Proyecto no encontrado.");
    }

    // Obtener publicaciones con el título del proyecto
    $stmtPublicaciones = $conn->prepare("
        SELECT p.*, pr.titulo AS proyecto_titulo 
        FROM publicacion p 
        JOIN proyecto pr ON p.id_proyectos = pr.id_proyectos 
        WHERE p.id_proyectos = :id_proyecto
        ORDER BY p.fecha_publicacion DESC
    ");
    $stmtPublicaciones->bindParam(':id_proyecto', $id_proyecto, PDO::PARAM_INT);
    $stmtPublicaciones->execute();
    $publicaciones = $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

function obtenerComentariosPublicacion($id_publicacion, $conn) {
    $stmt = $conn->prepare("
        SELECT c.*, u.nombre, u.apellido 
        FROM comentarios c
        INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.id_publicacion = :id_publicacion
        ORDER BY c.fecha DESC
    ");
    $stmt->execute([':id_publicacion' => $id_publicacion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerTotalMegustasPublicacion($id_publicacion, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id_publicacion");
    $stmt->execute([':id_publicacion' => $id_publicacion]);
    return $stmt->fetchColumn();
}

function usuarioDioMegustaPublicacion($id_usuario, $id_publicacion, $conn) {
    if (!$id_usuario) return false;
    $stmt = $conn->prepare("
        SELECT 1 FROM megusta 
        WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion
    ");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_publicacion' => $id_publicacion
    ]);
    return (bool)$stmt->fetchColumn();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones - <?= htmlspecialchars($proyecto['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2A5C82;
            --secondary-color: #5BA4E6;
            --accent-color: #FF6B6B;
        }

        .publicacion-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 2rem;
        }

        .publicacion-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .publicacion-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .publicacion-imagen {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .interacciones {
            display: flex;
            gap: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid #eee;
        }

        .btn-interaccion {
            background: none;
            border: none;
            color: #666;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.2s ease;
        }

        .btn-interaccion:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .btn-interaccion.activo {
            color: var(--accent-color);
        }

        .comentario {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
            position: relative;
        }

        .acciones-comentario {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .comentario:hover .acciones-comentario {
            opacity: 1;
        }

        .form-comentario textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 8px;
            resize: none;
            margin: 1rem 0;
        }

        .fecha-publicacion {
            color: #6c757d;
            font-size: 0.9em;
        }

        .badge-proyecto {
            background: rgba(255,255,255,0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .publicacion-imagen {
                height: 250px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="h3 mb-0">
                <i class="fas fa-newspaper me-2"></i>
                Publicaciones de 
                <span class="text-primary"><?= htmlspecialchars($proyecto['titulo'])?></span>
            </h1>
            <a href="index.php?view=mis_publicaciones" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>

        <?php if (count($publicaciones) > 0): ?>
            <div class="row">
                <?php foreach ($publicaciones as $publicacion): ?>
                <div class="col-lg-8 mx-auto">
                    <article class="publicacion-card">
                        <div class="publicacion-header">
                            <h2 class="h4 mt-3 mb-0"><?= htmlspecialchars($publicacion['titulo']) ?></h2>
                        </div>

                        <div class="publicacion-body p-4">
                        <p class="lead mb-4"><?= nl2br(htmlspecialchars($publicacion['descripcion'])) ?></p>
    
                        <?php if (!empty($publicacion['imagen'])): ?>
                                <img src="uploads/<?= htmlspecialchars($publicacion['imagen']) ?>" 
                                     class="publicacion-imagen">
                            <?php endif; ?>

                            
                            <div class="interacciones">
                                <form class="form-megusta" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                                    <button type="submit" 
                                            class="btn-interaccion <?= (isset($_SESSION['id_usuario']) && usuarioDioMegustaPublicacion($_SESSION['id_usuario'], $publicacion['id_publicacion'], $conn)) ? 'activo' : '' ?>">
                                        <i class="fas fa-thumbs-up me-2"></i>
                                        <span class="total-megusta">
                                            <?= obtenerTotalMegustasPublicacion($publicacion['id_publicacion'], $conn) ?>
                                        </span>
                                    </button>
                                </form>

                                <button class="btn-interaccion" 
                                        onclick="toggleComentarios(<?= $publicacion['id_publicacion'] ?>)">
                                    <i class="fas fa-comment me-2"></i>
                                    Comentarios
                                </button>
                            </div>

                            <div id="comentarios-<?= $publicacion['id_publicacion'] ?>" class="comentarios-container" style="display: none;">
                                <?php $comentarios = obtenerComentariosPublicacion($publicacion['id_publicacion'], $conn); ?>
                                <?php if (count($comentarios) > 0): ?>
                                    <?php foreach ($comentarios as $comentario): ?>
                                        <div class="comentario">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 32px; height: 32px">
                                                    <?= substr($comentario['nombre'], 0, 1) . substr($comentario['apellido'], 0, 1) ?>
                                                </div>
                                                <div class="ms-2">
                                                    <strong><?= htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']) ?></strong>
                                                    <small class="text-muted ms-2"><?= date('d M H:i', strtotime($comentario['fecha'])) ?></small>
                                                </div>
                                            </div>
                                            <p class="mb-0"><?= htmlspecialchars($comentario['comentario']) ?></p>
                                            <?php if (isset($_SESSION['id_usuario']) && $comentario['id_usuario'] == $_SESSION['id_usuario']): ?>
                                                <div class="acciones-comentario">
                                                    <button class="btn btn-link p-0 text-secondary" 
                                                            onclick="editarComentario(<?= $comentario['id_comentario'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-link p-0 text-danger" 
                                                            onclick="eliminarComentario(<?= $comentario['id_comentario'] ?>)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-3 text-muted">
                                        Sé el primero en comentar
                                    </div>
                                <?php endif; ?>

                                <form class="form-comentario" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                                    <div class="input-group">
                                        <textarea name="comentario" 
                                                  class="form-control" 
                                                  placeholder="Escribe tu comentario..." 
                                                  rows="2"
                                                  required></textarea>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-4"></i>
                    <h3 class="h5 mb-3">No hay publicaciones en este proyecto</h3>
                    <p class="text-muted">Cuando se creen publicaciones, aparecerán aquí.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleComentarios(id) {
            const comentarios = document.getElementById(`comentarios-${id}`);
            comentarios.style.display = comentarios.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>