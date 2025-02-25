<?php
require '../conection/conexion.php';

// Configuración de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$publicacionesPorPagina = 5;
$offset = ($pagina - 1) * $publicacionesPorPagina;

// Consulta para obtener publicaciones con datos del proyecto
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

// Calcular total de páginas
$totalPublicaciones = $conn->query("SELECT COUNT(*) FROM publicacion")->fetchColumn();
$totalPaginas = ceil($totalPublicaciones / $publicacionesPorPagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicaciones de Proyectos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .publicacion-container {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .proyecto-titulo {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .publicacion-titulo {
            color: #34495e;
            font-size: 1.2em;
            margin: 15px 0 10px;
        }
        .publicacion-imagen {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 10px 0;
        }
        .interacciones {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn-interaccion {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .paginacion {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php foreach ($publicaciones as $publicacion): ?>
        <div class="publicacion-container">
            <!-- Título del Proyecto -->
            <h2 class="proyecto-titulo">
                <?= htmlspecialchars($publicacion['proyecto_titulo']) ?>
            </h2>
            
            <!-- Título de la Publicación -->
            <h3 class="publicacion-titulo">
                <?= htmlspecialchars($publicacion['titulo']) ?>
            </h3>
            
            <!-- Imagen de la Publicación -->
            <?php if (!empty($publicacion['imagen'])): ?>
                <img src="uploads/<?= htmlspecialchars($publicacion['imagen']) ?>" 
                     class="publicacion-imagen"
                     alt="Imagen de la publicación">
            <?php endif; ?>
            
            <!-- Descripción de la Publicación -->
            <p class="publicacion-descripcion">
                <?= nl2br(htmlspecialchars($publicacion['descripcion'])) ?>
            </p>
            
            <!-- Fecha de Publicación -->
            <small><?= date('d/m/Y H:i', strtotime($publicacion['fecha_publicacion'])) ?></small>

            <!-- Interacciones -->
            <div class="interacciones">
                <!-- Botón Me Gusta -->
                <form class="form-megusta" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                    <button type="submit" class="btn-interaccion <?= (isset($_SESSION['id_usuario'])) && usuarioDioMegustaPublicacion($_SESSION['id_usuario'], $publicacion['id_publicacion'], $conn) ? 'activo' : '' ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="total-megusta">
                            <?= obtenerTotalMegustasPublicacion($publicacion['id_publicacion'], $conn) ?>
                        </span>
                    </button>
                </form>

                <!-- Botón Comentarios -->
                <button class="btn-interaccion" onclick="toggleComentarios(<?= $publicacion['id_publicacion'] ?>)">
                    <i class="fas fa-comment"></i> Comentarios
                </button>
            </div>

            <!-- Sección de Comentarios -->
            <div id="comentarios-<?= $publicacion['id_publicacion'] ?>" style="display: none;">
                <?php $comentarios = obtenerComentariosPublicacion($publicacion['id_publicacion'], $conn) ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comentario">
                        <strong><?= htmlspecialchars($comentario['nombre'] . ' ' . htmlspecialchars($comentario['apellido'])) ?></strong>
                        <p><?= htmlspecialchars($comentario['comentario']) ?></p>
                        <small><?= date('d/m/Y H:i', strtotime($comentario['fecha'])) ?></small>
                    </div>
                <?php endforeach; ?>
                
                <!-- Formulario para nuevo comentario -->
                <form class="form-comentario" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                    <textarea name="comentario" placeholder="Escribe tu comentario..."></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Paginación -->
    <div class="paginacion">
        <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>" <?= $i == $pagina ? 'class="activa"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($pagina < $totalPaginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>

    <script>
        // Función para mostrar/ocultar comentarios
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
                
                // proyectos.js (sección de comentarios)
try {
    const response = await fetch('acciones.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=comentar&id_publicacion=${publicacionId}&comentario=${encodeURIComponent(texto)}`
    });
    
    const data = await response.json();
    
    if (data.success) {
        const comentariosDiv = document.getElementById(`comentarios-${publicacionId}`);
        // Limpiar comentarios existentes
        comentariosDiv.querySelectorAll('.comentario').forEach(c => c.remove());
        // Agregar nuevos comentarios
        data.comentarios.forEach(comentario => {
            const div = document.createElement('div');
            div.className = 'comentario';
            div.innerHTML = `
                <strong>${comentario.nombre} ${comentario.apellido}</strong>
                <p>${comentario.comentario}</p>
                <small>${new Date(comentario.fecha).toLocaleString()}</small>
                ${comentario.pertenece_al_usuario ? `
                <button onclick="editarComentario(${comentario.id_comentario})">Editar</button>
                <button onclick="eliminarComentario(${comentario.id_comentario})">Eliminar</button>
                ` : ''}
            `;
            comentariosDiv.insertBefore(div, form);
        });
        // Limpiar textarea
        form.querySelector('textarea').value = '';
    }
} catch (error) {
    console.error('Error:', error);
}
            });
        });
    </script>
</body>
</html>