<?php
require_once '../conection/conexion.php';


$id_proyecto = $_GET['id_proyecto'] ?? 0;

// Funciones auxiliares
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

try {
    // Obtener datos del proyecto
    $stmt = $conn->prepare("SELECT titulo, fecha_publicacion, etapa FROM proyecto WHERE id_proyectos = ?");
    $stmt->execute([$id_proyecto]);
    $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener publicaciones asociadas
    $stmtpublicacion = $conn->prepare("
        SELECT id_publicacion, titulo, descripcion, fecha_publicacion, imagen 
        FROM publicacion 
        WHERE id_proyectos = ?
    ");
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
    <title>Detalles del Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2A5C82;
            --secondary-color: #5BA4E6;
            --accent-color: #FF6B6B;
        }

        .project-header {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    
    .project-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, transparent 100%);
    }

    .badge {
        letter-spacing: 0.05em;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .badge:hover {
        transform: translateY(-1px);
    }

        .project-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }

        .project-card:hover {
            transform: translateY(-5px);
        }

        .etapa-badge {
            font-size: 0.9em;
            padding: 0.5em 1.2em;
            background: var(--accent-color);
            position: relative;
            top: -15px;
        }

        .project-image {
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            width: 100%;
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
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            margin: 1rem 0;
            position: relative;
        }

        .acciones-comentario {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .comentario:hover .acciones-comentario {
            opacity: 1;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            border-left: 2px solid var(--primary-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            background: var(--primary-color);
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .project-image {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php if ($publicacion): ?>
        <div class="project-header text-center py-4">
    
        <h1 class="h2 mb-2 fw-light"><?= htmlspecialchars($publicacion['titulo']) ?></h1>
        
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="badge rounded-pill bg-white text-primary border border-primary border-2 px-3 py-1 fs-6">
                <i class="fas fa-layer-group me-2"></i>
                <?= htmlspecialchars($publicacion['etapa']) ?>
            </span>
            
        </div>
    
</div>
        
        <main class="container">
            <?php if (!empty($proyectos)): ?>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="timeline">
                            <?php foreach ($proyectos as $proyecto): ?>
                                <article class="timeline-item project-card p-4">
                                    <div class="card-body">
                                        <h3 class="h5 text-primary mb-3"><?= htmlspecialchars($proyecto['titulo']) ?></h3>
                                        
                                        <?php if (!empty($proyecto['imagen'])): ?>
                                            <img src="<?= htmlspecialchars($proyecto['imagen']) ?>" 
                                                 class="project-image mb-3"
                                                 alt="<?= htmlspecialchars($proyecto['titulo']) ?>">
                                        <?php endif; ?>
                                        
                                        <p class="text-muted"><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                                        
                                        <div class="interacciones">
                                            <form class="form-megusta" data-publicacion-id="<?= $proyecto['id_publicacion'] ?>">
                                                <button type="submit" 
                                                        class="btn-interaccion <?= (isset($_SESSION['id_usuario']) && usuarioDioMegustaPublicacion($_SESSION['id_usuario'], $proyecto['id_publicacion'], $conn)) ? 'activo' : '' ?>">
                                                    <i class="fas fa-thumbs-up"></i>
                                                    <span class="total-megusta">
                                                        <?= obtenerTotalMegustasPublicacion($proyecto['id_publicacion'], $conn) ?>
                                                    </span>
                                                </button>
                                            </form>

                                            <button class="btn-interaccion" 
                                                    onclick="toggleComentarios(<?= $proyecto['id_publicacion'] ?>)">
                                                <i class="fas fa-comment"></i>
                                                Comentarios
                                            </button>
                                        </div>

                                        <div id="comentarios-<?= $proyecto['id_publicacion'] ?>" class="comentarios-container" style="display: none;">
                                            <?php $comentarios = obtenerComentariosPublicacion($proyecto['id_publicacion'], $conn) ?>
                                            <?php if (!empty($comentarios)): ?>
                                                <?php foreach ($comentarios as $comentario): ?>
                                                    <div class="comentario">
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                 style="width: 32px; height: 32px">
                                                                <?= substr($comentario['nombre'], 0, 1) . substr($comentario['apellido'], 0, 1) ?>
                                                            </div>
                                                            <div>
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
                                            
                                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                                <form class="form-comentario" data-publicacion-id="<?= $proyecto['id_publicacion'] ?>">
                                                    <div class="input-group">
                                                        <textarea class="form-control" 
                                                                  placeholder="Escribe tu comentario..." 
                                                                  rows="2"
                                                                  required></textarea>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <?= date('d M Y H:i', strtotime($proyecto['fecha_publicacion'])) ?>
                                        </small>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-4"></i>
                    <h2 class="h4 mb-3">No hay publicaciones disponibles</h2>
                    <p class="text-muted">Las actualizaciones del proyecto aparecerán aquí</p>
                </div>
            <?php endif; ?>
        </main>
    <?php else: ?>
        <div class="container text-center py-5">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No se encontró información del proyecto
            </div>
            <a href="javascript:history.back()" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidad de comentarios
        function toggleComentarios(id) {
            const comentarios = document.getElementById(`comentarios-${id}`);
            comentarios.style.display = comentarios.style.display === 'none' ? 'block' : 'none';
        }

        // Manejo de Me Gusta
        document.querySelectorAll('.form-megusta').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const publicacionId = form.dataset.publicacionId;
                
                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=megusta&id_publicacion=${publicacionId}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const boton = form.querySelector('button');
                        const contador = form.querySelector('.total-megusta');
                        contador.textContent = data.total;
                        boton.classList.toggle('activo', data.dio_megusta);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // Manejo de Comentarios
        document.querySelectorAll('.form-comentario').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const publicacionId = form.dataset.publicacionId;
                const texto = form.querySelector('textarea').value;
                
                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=comentar&id_publicacion=${publicacionId}&comentario=${encodeURIComponent(texto)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const comentariosDiv = document.getElementById(`comentarios-${publicacionId}`);
                        const nuevoComentario = document.createElement('div');
                        nuevoComentario.className = 'comentario';
                        nuevoComentario.innerHTML = `
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px">
                                    ${data.iniciales}
                                </div>
                                <div>
                                    <strong>${data.nombre}</strong>
                                    <small class="text-muted ms-2">Ahora</small>
                                </div>
                            </div>
                            <p class="mb-0">${texto}</p>
                            <div class="acciones-comentario">
                                <button class="btn btn-link p-0 text-secondary" 
                                        onclick="editarComentario(${data.id_comentario})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-link p-0 text-danger" 
                                        onclick="eliminarComentario(${data.id_comentario})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                        comentariosDiv.insertBefore(nuevoComentario, form);
                        form.querySelector('textarea').value = '';
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // Funciones para editar/eliminar comentarios
        async function eliminarComentario(idComentario) {
            try {
                const response = await fetch('acciones.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=eliminar_comentario&id_comentario=${idComentario}`
                });
                const data = await response.json();
                if (data.success) {
                    document.querySelector(`[onclick*="${idComentario}"]`).closest('.comentario').remove();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function editarComentario(idComentario) {
            const comentarioElement = document.querySelector(`[onclick*="${idComentario}"]`).closest('.comentario');
            const textoOriginal = comentarioElement.querySelector('p').textContent;
            
            const nuevoTexto = prompt('Edita tu comentario:', textoOriginal);
            if (nuevoTexto !== null && nuevoTexto.trim() !== textoOriginal.trim()) {
                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=editar_comentario&id_comentario=${idComentario}&comentario=${encodeURIComponent(nuevoTexto)}`
                    });
                    const data = await response.json();
                    if (data.success) {
                        comentarioElement.querySelector('p').textContent = nuevoTexto;
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }
    </script>
</body>
</html>