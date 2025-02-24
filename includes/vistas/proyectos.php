<?php
require '../conection/conexion.php';

// Obtener el número de página actual
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$proyectosPorPagina = 5;
$offset = ($pagina - 1) * $proyectosPorPagina;

// Obtener los proyectos de la base de datos
// Obtener los proyectos de la base de datos con el nombre del contratista
$stmt = $conn->prepare("
    SELECT p.*, u.nombre as nombre_contratista, u.apellido as apellido_contratista 
    FROM Proyectos p 
    INNER JOIN Usuarios u ON p.id_contratista = u.id_usuario 
    LIMIT :limit OFFSET :offset
");
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
    <!-- Incluir FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .botones-final {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.form-comentario textarea {
    width: 100%;
    margin: 10px 0;
    min-height: 80px;
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
        .acciones-comentario {
            margin-left: 10px;
        }
        .acciones-comentario i {
            cursor: pointer;
            margin-right: 5px;
        }
        .editar-comentario {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php foreach ($proyectos as $proyecto): ?>
        <div class="proyecto">
            <h2><?php echo htmlspecialchars($proyecto['titulo']); ?></h2>
            <h3>Contratista: <a href="index.php?view=perfil_contratista&id=<?php echo $proyecto['id_contratista']; ?>"><?php echo htmlspecialchars($proyecto['nombre_contratista']) . ' ' . htmlspecialchars($proyecto['apellido_contratista']); ?></a></h3>
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
        
        <!-- Botones ABAJO -->
        <div class="botones-final">
            <button type="submit">Comentar</button>
            <button type="button" onclick="mostrarComentarios(<?php echo $proyecto['id_proyecto']; ?>)">Comentarios</button>
        </div>
        <div class="error-message"></div>
    </form>

            <!-- Sección de comentarios -->
            <div id="comentarios-<?php echo $proyecto['id_proyecto']; ?>" class="comentarios">
                <?php $comentarios = obtenerComentarios($proyecto['id_proyecto'], $conn); ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <p>
                        <strong><?php echo htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']); ?>:</strong> 
                        <span id="comentario-texto-<?php echo $comentario['id_comentario']; ?>"><?php echo htmlspecialchars($comentario['comentario']); ?></span>
                        <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $comentario['id_usuario']): ?>
                            <span class="acciones-comentario">
                                <i class="fas fa-pencil-alt" onclick="mostrarEditarComentario(<?php echo $comentario['id_comentario']; ?>)"></i>
                                <i class="fas fa-trash" onclick="eliminarComentario(<?php echo $comentario['id_comentario']; ?>)"></i>
                            </span>
                        <?php endif; ?>
                    </p>
                    <!-- Formulario para editar comentario -->
                    <div id="editar-comentario-<?php echo $comentario['id_comentario']; ?>" class="editar-comentario">
                        <textarea id="editar-texto-<?php echo $comentario['id_comentario']; ?>"><?php echo htmlspecialchars($comentario['comentario']); ?></textarea>
                        <button onclick="guardarEdicionComentario(<?php echo $comentario['id_comentario']; ?>)">Guardar</button>
                        <button onclick="cancelarEdicionComentario(<?php echo $comentario['id_comentario']; ?>)">Cancelar</button>
                    </div>
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

        function mostrarEditarComentario(id_comentario) {
            document.getElementById('editar-comentario-' + id_comentario).style.display = 'block';
        }

        function cancelarEdicionComentario(id_comentario) {
            document.getElementById('editar-comentario-' + id_comentario).style.display = 'none';
        }

        function guardarEdicionComentario(id_comentario) {
            const nuevoComentario = document.getElementById('editar-texto-' + id_comentario).value;
            fetch('acciones.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=editar_comentario&id_comentario=${id_comentario}&comentario=${encodeURIComponent(nuevoComentario)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recargar la página para ver los cambios
                } else {
                    alert(data.message || 'Error al editar el comentario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
        }

        function eliminarComentario(id_comentario) {
    fetch('acciones.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=eliminar_comentario&id_comentario=${id_comentario}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recargar la página para ver los cambios
        } else {
            alert(data.message || 'Error al eliminar el comentario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
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
            // En la sección de manejar comentarios, actualizar la lógica para mostrar los íconos de editar y eliminar
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
                comentariosDiv.innerHTML = data.comentarios.map(comentario => {
                    let acciones = '';
                    if (comentario.pertenece_al_usuario) {
                        acciones = `
                            <span class="acciones-comentario">
                                <i class="fas fa-pencil-alt" onclick="mostrarEditarComentario(${comentario.id_comentario})"></i>
                                <i class="fas fa-trash" onclick="eliminarComentario(${comentario.id_comentario})"></i>
                            </span>
                        `;
                    }
                    return `
                        <p>
                            <strong>${comentario.nombre} ${comentario.apellido}:</strong> 
                            <span id="comentario-texto-${comentario.id_comentario}">${comentario.comentario}</span>
                            ${acciones}
                        </p>
                        <div id="editar-comentario-${comentario.id_comentario}" class="editar-comentario">
                            <textarea id="editar-texto-${comentario.id_comentario}">${comentario.comentario}</textarea>
                            <button onclick="guardarEdicionComentario(${comentario.id_comentario})">Guardar</button>
                            <button onclick="cancelarEdicionComentario(${comentario.id_comentario})">Cancelar</button>
                        </div>
                    `;
                }).join('');
                
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