<?php
// Verificar autenticación y perfil
if (!isset($_SESSION['id_usuario']) ){
    header('Location: index.php');
    exit();
}

if ($_SESSION['perfil'] !== 'contratista') {
    header('Location: acceso_denegado.php');
    exit();
}

// Configuración de la base de datos
require_once '../conection/conexion.php';

// Configuración de imágenes
$upload_dir = 'uploads/proyectos/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2097152; // 2MB

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Procesar operaciones CRUD
try {
    $id_contratista = $_SESSION['id_usuario'];
    
    // Crear nuevo proyecto
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_proyecto'])) {
        $titulo = htmlspecialchars($_POST['titulo']);
        $descripcion = htmlspecialchars($_POST['descripcion']);
        $etapa = $_POST['etapa'];
        $imagen = null;

        // Manejar imagen
        if (!empty($_FILES['imagen']['name'])) {
            $file = $_FILES['imagen'];
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Formato de imagen no válido');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('El tamaño máximo permitido es 2MB');
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagen = $upload_dir . uniqid() . '.' . $extension;
            
            if (!move_uploaded_file($file['tmp_name'], $imagen)) {
                throw new Exception('Error al subir la imagen');
            }
        }

        $stmt = $conn->prepare("INSERT INTO Proyectos 
                              (id_contratista, titulo, descripcion, etapa, imagen)
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_contratista, $titulo, $descripcion, $etapa, $imagen]);
        
        $_SESSION['mensaje'] = 'Proyecto creado exitosamente';
        header('Location: proyectos.php');
        exit;
    }

    // Actualizar proyecto
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_proyecto'])) {
        $id_proyecto = $_POST['id_proyecto'];
        $titulo = htmlspecialchars($_POST['titulo']);
        $descripcion = htmlspecialchars($_POST['descripcion']);
        $etapa = $_POST['etapa'];

        // Validar propiedad
        $stmt = $conn->prepare("SELECT id_contratista, imagen FROM Proyectos WHERE id_proyecto = ?");
        $stmt->execute([$id_proyecto]);
        $proyecto = $stmt->fetch();
        
        if ($proyecto['id_contratista'] != $id_contratista) {
            throw new Exception('Acceso no autorizado');
        }

        $imagen = $proyecto['imagen'];
        
        // Manejar nueva imagen
        if (!empty($_FILES['imagen']['name'])) {
            $file = $_FILES['imagen'];
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Formato de imagen no válido');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('El tamaño máximo permitido es 2MB');
            }

            // Eliminar imagen anterior
            if ($imagen && file_exists($imagen)) {
                unlink($imagen);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagen = $upload_dir . uniqid() . '.' . $extension;
            
            if (!move_uploaded_file($file['tmp_name'], $imagen)) {
                throw new Exception('Error al subir la imagen');
            }
        }

        $update = $conn->prepare("UPDATE Proyectos 
                                  SET titulo = ?, descripcion = ?, etapa = ?, imagen = ?
                                  WHERE id_proyecto = ?");
        $update->execute([$titulo, $descripcion, $etapa, $imagen, $id_proyecto]);
        
        $_SESSION['mensaje'] = 'Proyecto actualizado exitosamente';
        header('Location: proyectos.php');
        exit;
    }

    // Eliminar proyecto
    if (isset($_GET['eliminar'])) {
        $id_proyecto = $_GET['eliminar'];
        
        $stmt = $conn->prepare("SELECT id_contratista, imagen FROM Proyectos WHERE id_proyecto = ?");
        $stmt->execute([$id_proyecto]);
        $proyecto = $stmt->fetch();
        
        if ($proyecto['id_contratista'] != $id_contratista) {
            throw new Exception('Acceso no autorizado');
        }

        // Eliminar imagen
        if ($proyecto['imagen'] && file_exists($proyecto['imagen'])) {
            unlink($proyecto['imagen']);
        }

        $delete = $conn->prepare("DELETE FROM Proyectos WHERE id_proyecto = ?");
        $delete->execute([$id_proyecto]);
        
        $_SESSION['mensaje'] = 'Proyecto eliminado exitosamente';
        header('Location: proyectos.php');
        exit;
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: proyectos.php');
    exit;
}

// Obtener proyectos del contratista
$query = "SELECT id_proyecto, titulo, descripcion, etapa, imagen,
          DATE_FORMAT(fecha_publicacion, '%d/%m/%Y %H:%i') AS fecha_formateada
          FROM Proyectos 
          WHERE id_contratista = ?
          ORDER BY fecha_publicacion DESC";

$stmt = $conn->prepare($query);
$stmt->execute([$id_contratista]);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Proyectos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .img-thumbnail { max-width: 150px; max-height: 100px; object-fit: cover; }
        .badge-planificacion { background-color: #ffc107; }
        .badge-ejecucion { background-color: #0d6efd; }
        .badge-finalizado { background-color:#007bff; }
        .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Encabezado y mensajes -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="fas fa-project-diagram"></i> Mis Proyectos
            </h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nuevoModal">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </button>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Listado de proyectos -->
        <?php if (empty($proyectos)): ?>
            <div class="alert alert-info">No tienes proyectos registrados</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="col">
                        <div class="card h-100 hover-shadow">
                            <?php if ($proyecto['imagen']): ?>
                                <img src="<?= htmlspecialchars($proyecto['imagen']) ?>" 
                                     class="card-img-top" 
                                     alt="Imagen del proyecto"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($proyecto['titulo']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge rounded-pill <?= match($proyecto['etapa']) {
                                        'planificacion' => 'badge-planificacion',
                                        'ejecucion'    => 'badge-ejecucion',
                                        'finalizado'   => 'badge-finalizado'
                                    } ?>">
                                        <?= ucfirst($proyecto['etapa']) ?>
                                    </span>
                                    <small class="text-muted"><?= $proyecto['fecha_formateada'] ?></small>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editarModal"
                                            data-id="<?= $proyecto['id_proyecto'] ?>"
                                            data-titulo="<?= htmlspecialchars($proyecto['titulo']) ?>"
                                            data-descripcion="<?= htmlspecialchars($proyecto['descripcion']) ?>"
                                            data-etapa="<?= $proyecto['etapa'] ?>"
                                            data-imagen="<?= htmlspecialchars($proyecto['imagen']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="proyectos.php?eliminar=<?= $proyecto['id_proyecto'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Eliminar este proyecto permanentemente?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Nuevo Proyecto -->
    <div class="modal fade" id="nuevoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Proyecto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="crear_proyecto">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Etapa</label>
                            <select name="etapa" class="form-select" required>
                                <option value="planificacion">Planificación</option>
                                <option value="ejecucion">Ejecución</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen del Proyecto</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Proyecto -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Proyecto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="editar_proyecto">
                        <input type="hidden" name="id_proyecto" id="editar_id">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" id="editar_titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="editar_descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Etapa</label>
                            <select name="etapa" id="editar_etapa" class="form-select" required>
                                <option value="planificacion">Planificación</option>
                                <option value="ejecucion">Ejecución</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen del Proyecto</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <div class="mt-2" id="imagen-actual-container"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Manejar modal de edición
    $('#editarModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const proyecto = {
            id: button.data('id'),
            titulo: button.data('titulo'),
            descripcion: button.data('descripcion'),
            etapa: button.data('etapa'),
            imagen: button.data('imagen')
        };

        const modal = $(this);
        modal.find('#editar_id').val(proyecto.id);
        modal.find('#editar_titulo').val(proyecto.titulo);
        modal.find('#editar_descripcion').val(proyecto.descripcion);
        modal.find('#editar_etapa').val(proyecto.etapa);
        
        // Mostrar imagen actual
        const imagenContainer = modal.find('#imagen-actual-container');
        if (proyecto.imagen) {
            imagenContainer.html(`
                <div class="alert alert-info mt-2">
                    <p class="mb-1">Imagen actual:</p>
                    <img src="${proyecto.imagen}" class="img-thumbnail" style="max-height: 150px;">
                </div>
            `);
        } else {
            imagenContainer.html('<p class="text-muted">No hay imagen actual</p>');
        }
    });

    // Confirmación antes de eliminar
    $('a[href*="eliminar="]').on('click', function(e) {
        if (!confirm('¿Estás seguro de eliminar este proyecto?')) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>