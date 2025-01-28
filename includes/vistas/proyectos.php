<?php
require '../conection/conexion.php';

// Obtener el número de página actual
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$proyectosPorPagina = 5;
$offset = ($pagina - 1) * $proyectosPorPagina;

// Obtener los proyectos de la base de datos
$stmt = $conn->prepare("SELECT * FROM Proyectos LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $proyectosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el total de proyectos para la paginación
$totalProyectos = $conn->query("SELECT COUNT(*) FROM Proyectos")->fetchColumn();
$totalPaginas = ceil($totalProyectos / $proyectosPorPagina);

// Función para obtener comentarios
function obtenerComentarios($id_proyecto, $conn) {
    $stmt = $conn->prepare("
        SELECT c.*, u.nombre, u.apellido 
        FROM Comentarios c 
        INNER JOIN Usuarios u ON c.id_usuario = u.id_usuario 
        WHERE c.id_proyecto = :id_proyecto 
        ORDER BY c.fecha DESC
    ");
    $stmt->bindParam(':id_proyecto', $id_proyecto);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener el total de "me gusta" de un proyecto
function obtenerTotalMegustas($id_proyecto, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM MeGusta WHERE id_proyecto = :id_proyecto");
    $stmt->bindParam(':id_proyecto', $id_proyecto);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Función para verificar si el usuario actual dio "me gusta"
function usuarioDioMegusta($id_usuario, $id_proyecto, $conn) {
    $stmt = $conn->prepare("SELECT * FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':id_proyecto', $id_proyecto);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyectos</title>
    <style>
        .proyecto {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .comentarios {
            display: none;
            margin-top: 10px;
        }
        .btn-megusta.activo {
            background-color: #007bff;
            color: white;
        }
        .error-message {
            color: red;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php foreach ($proyectos as $proyecto): ?>
        <div class="proyecto">
            <h2><?php echo htmlspecialchars($proyecto['titulo']); ?></h2>
            <p><?php echo htmlspecialchars($proyecto['descripcion']); ?></p>
            <img src="<?php echo htmlspecialchars($proyecto['imagen']); ?>" alt="Imagen del proyecto" width="200">
            <p>Etapa: <?php echo htmlspecialchars($proyecto['etapa']); ?></p>
            <p>Fecha de publicación: <?php echo htmlspecialchars($proyecto['fecha_publicacion']); ?></p>

            <!-- Botón para dar o quitar "me gusta" -->
            <form class="form-megusta" data-proyecto-id="<?php echo $proyecto['id_proyecto']; ?>">
                <button type="submit" class="btn-megusta <?php echo (isset($_SESSION['id_usuario']) && usuarioDioMegusta($_SESSION['id_usuario'], $proyecto['id_proyecto'], $conn)) ? 'activo' : ''; ?>">
                    <span class="total-megusta"><?php echo obtenerTotalMegustas($proyecto['id_proyecto'], $conn); ?></span> | Me gusta
                </button>
                <div class="error-message"></div>
            </form>

            <!-- Formulario para comentar -->
            <form class="form-comentario" data-proyecto-id="<?php echo $proyecto['id_proyecto']; ?>">
                <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
                <button type="submit">Comentar</button>
                <div class="error-message"></div>
            </form>

            <button onclick="mostrarComentarios(<?php echo $proyecto['id_proyecto']; ?>)">Comentarios</button>

            <!-- Sección de comentarios -->
            <div id="comentarios-<?php echo $proyecto['id_proyecto']; ?>" class="comentarios">
    <?php $comentarios = obtenerComentarios($proyecto['id_proyecto'], $conn); ?>
    <?php foreach ($comentarios as $comentario): ?>
        <p>
            <strong><?php echo htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']); ?>:</strong> 
            <?php echo htmlspecialchars($comentario['comentario']); ?>
        </p>
    <?php endforeach; ?>
</div>
        </div>
    <?php endforeach; ?>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <div>
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?>">Anterior</a>
            <?php endif; ?>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>">Siguiente</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
        function mostrarComentarios(id_proyecto) {
            var comentarios = document.getElementById('comentarios-' + id_proyecto);
            if (comentarios.style.display === 'none' || comentarios.style.display === '') {
                comentarios.style.display = 'block';
            } else {
                comentarios.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Manejar me gusta
            document.querySelectorAll('.form-megusta').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const proyectoId = this.dataset.proyectoId;
                    const botonMegusta = this.querySelector('.btn-megusta');
                    const errorDiv = this.querySelector('.error-message');
                    
                    fetch('acciones.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=megusta&id_proyecto=${proyectoId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const totalSpan = botonMegusta.querySelector('.total-megusta');
                            totalSpan.textContent = data.total_megusta;
                            
                            if (data.dio_megusta) {
                                botonMegusta.classList.add('activo');
                            } else {
                                botonMegusta.classList.remove('activo');
                            }
                            errorDiv.style.display = 'none';
                        } else {
                            errorDiv.textContent = data.message || 'Error al procesar la acción';
                            errorDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorDiv.textContent = 'Error de conexión';
                        errorDiv.style.display = 'block';
                    });
                });
            });
            
            // Manejar comentarios
            document.querySelectorAll('.form-comentario').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const proyectoId = this.dataset.proyectoId;
                    const comentarioText = this.querySelector('textarea').value;
                    const errorDiv = this.querySelector('.error-message');
                    
                    fetch('acciones.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=comentar&id_proyecto=${proyectoId}&comentario=${encodeURIComponent(comentarioText)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar la lista de comentarios
                            const comentariosDiv = document.getElementById(`comentarios-${proyectoId}`);
        comentariosDiv.innerHTML = data.comentarios.map(comentario => 
            `<p><strong>${comentario.nombre} ${comentario.apellido}:</strong> ${comentario.comentario}</p>`
        ).join('');
                            
                            // Mostrar los comentarios si están ocultos
                            comentariosDiv.style.display = 'block';
                            
                            // Limpiar el textarea y el mensaje de error
                            this.querySelector('textarea').value = '';
                            errorDiv.style.display = 'none';
                        } else {
                            errorDiv.textContent = data.message || 'Error al procesar el comentario';
                            errorDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorDiv.textContent = 'Error de conexión';
                        errorDiv.style.display = 'block';
                    });
                });
            });
        });
    </script>
</body>
</html>