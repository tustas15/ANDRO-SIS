<?php
require_once '../conection/conexion.php';

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

// Configuración de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$publicacionesPorPagina = 5;
$offset = ($pagina - 1) * $publicacionesPorPagina;

try {
    // Obtener publicaciones
    $stmt = $conn->prepare("
        SELECT 
            p.titulo as proyecto_titulo,
            pub.*,
            u.nombre as contratista_nombre,
            u.apellido as contratista_apellido
        FROM publicacion pub
        INNER JOIN proyecto p ON pub.id_proyectos = p.id_proyectos
        INNER JOIN usuarios u ON p.id_contratista = u.id_usuario
        ORDER BY pub.fecha_publicacion DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $publicacionesPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener contratistas
    $stmt_contratistas = $conn->prepare("
        SELECT id_usuario, nombre, apellido, imagen_perfil 
        FROM usuarios 
        WHERE perfil = 'contratista'
        ORDER BY nombre ASC
    ");
    $stmt_contratistas->execute();
    $contratistas = $stmt_contratistas->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total de páginas
    $totalPublicaciones = $conn->query("SELECT COUNT(*) FROM publicacion")->fetchColumn();
    $totalPaginas = ceil($totalPublicaciones / $publicacionesPorPagina);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones de Proyectos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2A5C82;
            --secondary-color: #5BA4E6;
            --accent-color: #FF6B6B;
        }

        .contenedor-principal {
            display: flex;
            gap: 2rem;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .columna-publicaciones {
            flex: 3;
        }

        .columna-contratistas {
            flex: 1;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            height: fit-content;
        }

        .publicacion-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            transition: transform 0.2s;
        }

        .publicacion-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .proyecto-titulo {
            color: #f8f9fa;
            margin: 0;
            font-size: 1.25rem;
        }

        .publicacion-imagen {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .publicacion-body {
            padding: 1.5rem;
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

        .contratista-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .contratista-item:hover {
            transform: translateX(5px);
        }

        .contratista-imagen {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .paginacion {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .paginacion a {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            transition: opacity 0.2s;
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

        @media (max-width: 768px) {
            .contenedor-principal {
                flex-direction: column;
            }
            
            .publicacion-imagen {
                height: 250px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="contenedor-principal">
        <!-- Columna de Contratistas -->
        <div class="columna-contratistas">
            <h3 class="mb-4">Contratistas Activos</h3>
            <div class="contratistas-lista">
                <?php foreach ($contratistas as $contratista): ?>
                    <div class="contratista-item" 
                         onclick="window.location.href='index.php?view=perfil_contratista&id=<?= $contratista['id_usuario'] ?>'">
                        <?php if (!empty($contratista['imagen_perfil'])): ?>
                            <img src="uploads/<?= htmlspecialchars($contratista['imagen_perfil']) ?>" 
                                 class="contratista-imagen" 
                                 alt="<?= htmlspecialchars($contratista['nombre']) ?>">
                        <?php else: ?>
                            <div class="contratista-imagen bg-secondary"></div>
                        <?php endif; ?>
                        <div>
                            <div class="contratista-nombre">
                                <?= htmlspecialchars($contratista['nombre'] . ' ' . $contratista['apellido']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Columna de Publicaciones -->
        <div class="columna-publicaciones">
            <?php if (count($publicaciones) > 0): ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <article class="publicacion-card">
                        <div class="publicacion-header">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="proyecto-titulo">
                                    <?= htmlspecialchars($publicacion['proyecto_titulo']) ?>
                                </h2>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($publicacion['fecha_publicacion'])) ?>
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small>Publicado por:</small>
                                <strong><?= htmlspecialchars($publicacion['contratista_nombre'] . ' ' . $publicacion['contratista_apellido']) ?></strong>
                            </div>
                        </div>
                        
                        <div class="publicacion-body">
                        <h3 class="h5 mb-3"><?= htmlspecialchars($publicacion['titulo']) ?></h3>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($publicacion['descripcion'])) ?></p>
                            <?php if (!empty($publicacion['imagen'])): ?>
                                <img src="uploads/<?= htmlspecialchars($publicacion['imagen']) ?>" 
                                     class="publicacion-imagen"
                                     alt="<?= htmlspecialchars($publicacion['titulo']) ?>">
                            <?php endif; ?>
                            
                            
                            
                            

                            <div class="interacciones">
                                <form class="form-megusta" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                                    <button type="submit" 
                                            class="btn-interaccion <?= (isset($_SESSION['id_usuario']) && usuarioDioMegustaPublicacion($_SESSION['id_usuario'], $publicacion['id_publicacion'], $conn)) ? 'activo' : '' ?>">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span class="total-megusta">
                                            <?= obtenerTotalMegustasPublicacion($publicacion['id_publicacion'], $conn) ?>
                                        </span>
                                    </button>
                                </form>

                                <button class="btn-interaccion" 
                                        onclick="toggleComentarios(<?= $publicacion['id_publicacion'] ?>)">
                                    <i class="fas fa-comment"></i>
                                    Comentarios
                                </button>
                            </div>

                            <div id="comentarios-<?= $publicacion['id_publicacion'] ?>" class="comentarios-container" style="display: none;">
                                <?php $comentarios = obtenerComentariosPublicacion($publicacion['id_publicacion'], $conn) ?>
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
                                
                                <form class="form-comentario" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
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
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-4"></i>
                    <h3 class="h5 mb-3">No hay publicaciones disponibles</h3>
                    <p class="text-muted">Cuando se creen nuevas publicaciones, aparecerán aquí.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacion">
        <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>" <?= $i == $pagina ? 'style="background:var(--secondary-color)"' : '' ?>>
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($pagina < $totalPaginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <script>
        function toggleComentarios(id) {
            const div = document.getElementById(`comentarios-${id}`);
            div.style.display = div.style.display === 'none' ? 'block' : 'none';
        }

        // Manejar Me Gusta
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

        // Manejar Comentarios
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
                        comentariosDiv.querySelectorAll('.comentario').forEach(c => c.remove());
                        
                        // Dentro de la función de manejo de comentarios (proyectos.php)
data.comentarios.forEach(comentario => {
    const div = document.createElement('div');
    div.className = 'comentario';
    div.innerHTML = `
        <strong>${comentario.nombre} ${comentario.apellido}</strong>
        <p>${comentario.comentario}</p>
        <small>${new Date(comentario.fecha).toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        })}</small>
        ${comentario.pertenece_al_usuario ? `
        <div class="acciones-comentario">
            <i class="fas fa-edit" 
               onclick="editarComentario(${comentario.id_comentario})" 
               title="Editar comentario"
               aria-label="Editar comentario"></i>
            <i class="fas fa-trash-alt" 
               onclick="eliminarComentario(${comentario.id_comentario})" 
               title="Eliminar comentario"
               aria-label="Eliminar comentario"></i>
        </div>
        ` : ''}
    `;
    comentariosDiv.insertBefore(div, form);
});
                        
                        form.querySelector('textarea').value = '';
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // Función eliminarComentario actualizada
        async function eliminarComentario(idComentario) {
    try {
        const response = await fetch('acciones.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=eliminar_comentario&id_comentario=${idComentario}`
        });
        const data = await response.json();
        if (data.success) {
            document.querySelector(`.fa-trash-alt[onclick*="${idComentario}"]`)
                    .closest('.comentario').remove();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function editarComentario(idComentario) {
    const comentarioElement = document.querySelector(`.fa-edit[onclick*="${idComentario}"]`)
                               .closest('.comentario');
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