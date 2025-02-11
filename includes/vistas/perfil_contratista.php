<?php
require '../conection/conexion.php';

// Verificar si se recibió el ID del contratista
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_contratista = filter_var($_GET['id'], FILTER_VALIDATE_INT);

// Obtener datos del contratista
$stmt = $conn->prepare("
    SELECT * 
    FROM Usuarios 
    WHERE id_usuario = :id 
    AND perfil = 'contratista'
");
$stmt->bindParam(':id', $id_contratista, PDO::PARAM_INT);
$stmt->execute();
$contratista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contratista) {
    die("Contratista no encontrado");
}

// Obtener proyectos del contratista
$stmt = $conn->prepare("
    SELECT * 
    FROM Proyectos 
    WHERE id_contratista = :id
    ORDER BY fecha_publicacion DESC
");
$stmt->bindParam(':id', $id_contratista, PDO::PARAM_INT);
$stmt->execute();
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funciones auxiliares
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

function obtenerTotalMegustas($id_proyecto, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM MeGusta WHERE id_proyecto = :id_proyecto");
    $stmt->bindParam(':id_proyecto', $id_proyecto);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

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
    <title>Perfil de <?php echo htmlspecialchars($contratista['nombre']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .perfil-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .imagen-perfil {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .proyectos-lista {
            display: grid;
            gap: 20px;
        }
        .proyecto-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-megusta {
            padding: 5px 10px;
            cursor: pointer;
            border: 1px solid #007bff;
            background: white;
            color: #007bff;
        }
        .btn-megusta.activo {
            background: #007bff;
            color: white;
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
            padding: 8px;
            min-height: 80px;
        }
        .acciones-comentario i {
            cursor: pointer;
            margin-left: 10px;
        }
        .editar-comentario {
            display: none;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Encabezado del perfil -->
<div class="perfil-header">
    <img src="<?php echo htmlspecialchars($contratista['imagen_perfil']); ?>" 
         alt="Imagen de perfil" 
         class="imagen-perfil">
    <div>
        <h1><?php echo htmlspecialchars($contratista['nombre'] . ' ' . $contratista['apellido']); ?></h1>
        <p>Correo: <?php echo htmlspecialchars($contratista['correo']); ?></p>
    </div>
</div>

<!-- Listado de proyectos -->
<h2>Proyectos realizados</h2>
<br>
<div class="proyectos-lista">
    <?php if (count($proyectos) > 0): ?>
        <?php foreach ($proyectos as $proyecto): ?>
            <div class="proyecto">
                <h3><?php echo htmlspecialchars($proyecto['titulo']); ?></h3>
                <img src="<?php echo htmlspecialchars($proyecto['imagen']); ?>" 
                     alt="Imagen del proyecto" 
                     width="200">
                <p><?php echo htmlspecialchars($proyecto['descripcion']); ?></p>
                <p><strong>Etapa:</strong> <?php echo htmlspecialchars($proyecto['etapa']); ?></p>
                <p><strong>Publicado:</strong> <?php echo date('d/m/Y', strtotime($proyecto['fecha_publicacion'])); ?></p>

                <!-- Botón Me Gusta -->
                <form class="form-megusta" data-proyecto-id="<?php echo $proyecto['id_proyecto']; ?>">
                    <button type="submit" class="btn-megusta <?php echo (isset($_SESSION['id_usuario']) && usuarioDioMegusta($_SESSION['id_usuario'], $proyecto['id_proyecto'], $conn)) ? 'activo' : ''; ?>">
                        <span class="total-megusta"><?php echo obtenerTotalMegustas($proyecto['id_proyecto'], $conn); ?></span> | Me gusta
                    </button>
                    <div class="error-message"></div>
                </form>

                <!-- Formulario de comentarios -->
                <form class="form-comentario" data-proyecto-id="<?php echo $proyecto['id_proyecto']; ?>">
                    <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
                    <div class="botones-final">
                        <button type="submit">Comentar</button>
                        <button type="button" onclick="mostrarComentarios(<?php echo $proyecto['id_proyecto']; ?>)">Comentarios</button>
                    </div>
                    <div class="error-message"></div>
                </form>

                <!-- Lista de comentarios -->
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
                        <!-- Formulario de edición -->
                        <div id="editar-comentario-<?php echo $comentario['id_comentario']; ?>" class="editar-comentario">
                            <textarea id="editar-texto-<?php echo $comentario['id_comentario']; ?>"><?php echo htmlspecialchars($comentario['comentario']); ?></textarea>
                            <button onclick="guardarEdicionComentario(<?php echo $comentario['id_comentario']; ?>)">Guardar</button>
                            <button onclick="cancelarEdicionComentario(<?php echo $comentario['id_comentario']; ?>)">Cancelar</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este contratista aún no tiene proyectos publicados</p>
    <?php endif; ?>
</div>

<script>
    // Funciones básicas
    function mostrarComentarios(id_proyecto) {
        const comentarios = document.getElementById(`comentarios-${id_proyecto}`);
        comentarios.style.display = comentarios.style.display === 'none' ? 'block' : 'none';
    }

    function mostrarEditarComentario(id_comentario) {
        document.getElementById(`editar-comentario-${id_comentario}`).style.display = 'block';
    }

    function cancelarEdicionComentario(id_comentario) {
        document.getElementById(`editar-comentario-${id_comentario}`).style.display = 'none';
    }

    // Manejo de Me Gusta
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
                    botonMegusta.classList.toggle('activo', data.dio_megusta);
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

    // Manejo de Comentarios
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
                    location.reload();
                } else {
                    errorDiv.textContent = data.message || 'Error al comentar';
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

    // Funciones para editar/eliminar comentarios
    function guardarEdicionComentario(id_comentario) {
        const nuevoComentario = document.getElementById(`editar-texto-${id_comentario}`).value;
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
                location.reload();
            } else {
                alert(data.message || 'Error al editar');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function eliminarComentario(id_comentario) {
        if (confirm('¿Estás seguro de eliminar este comentario?')) {
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
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>

</body>
</html>