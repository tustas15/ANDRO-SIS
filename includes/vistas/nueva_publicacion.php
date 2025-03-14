<?php
session_start();
require_once '../conection/conexion.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener información del usuario
try {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$_SESSION['id_usuario']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario || $usuario['perfil'] != 'contratista') {
        header("Location: acceso_denegado.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Variables de estado
$mensaje = '';
$publicacionEditar = null;
$accion = 'crear';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $camposRequeridos = ['id_proyectos', 'titulo', 'descripcion', 'peso'];
        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                throw new Exception("Todos los campos son requeridos");
            }
        }

        $datos = [
            'id_proyectos' => $_POST['id_proyectos'],
            'titulo' => htmlspecialchars($_POST['titulo']),
            'descripcion' => htmlspecialchars($_POST['descripcion']),
            'peso' => floatval($_POST['peso'])
        ];

        // Validar proyecto pertenece al contratista
        $stmt = $conn->prepare("SELECT id_proyectos FROM proyecto WHERE id_contratista = ? AND id_proyectos = ?");
        $stmt->execute([$usuario['id_usuario'], $datos['id_proyectos']]);
        if (!$stmt->fetch()) {
            throw new Exception("Proyecto no válido");
        }

        // Manejar imagen
        if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception("Formato de imagen no válido");
            }
            
            $nombreImagen = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $rutaImagen = 'uploads/' . $nombreImagen;
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
                throw new Exception("Error al subir la imagen");
            }
            $datos['imagen'] = $rutaImagen;
        }

        // Determinar si es edición
        if (isset($_POST['editar'])) {
            $accion = 'editar';
            $id_publicacion = $_POST['id_publicacion'];
            
            // Validar pertenencia
            $stmt = $conn->prepare("SELECT p.id_publicacion 
                                   FROM publicacion p
                                   JOIN proyecto pr ON p.id_proyectos = pr.id_proyectos
                                   WHERE p.id_publicacion = ? AND pr.id_contratista = ?");
            $stmt->execute([$id_publicacion, $usuario['id_usuario']]);
            if (!$stmt->fetch()) {
                throw new Exception("Publicación no válida");
            }

            // Actualizar
            $sql = "UPDATE publicacion SET 
                    titulo = ?, 
                    descripcion = ?, 
                    peso = ?" . 
                    (isset($datos['imagen']) ? ", imagen = ?" : "") . 
                    " WHERE id_publicacion = ?";
            
            $params = [$datos['titulo'], $datos['descripcion'], $datos['peso']];
            if (isset($datos['imagen'])) $params[] = $datos['imagen'];
            $params[] = $id_publicacion;
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $mensaje = "Publicación actualizada correctamente";
        } else {
            // Crear nueva
            $sql = "INSERT INTO publicacion (id_proyectos, titulo, descripcion, imagen, peso) 
                    VALUES (?, ?, ?, ?, ?)";
            $params = [
                $datos['id_proyectos'],
                $datos['titulo'],
                $datos['descripcion'],
                $datos['imagen'] ?? null,
                $datos['peso']
            ];
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $mensaje = "Publicación creada correctamente";
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}

// Manejar edición GET
if (isset($_GET['editar'])) {
    try {
        $stmt = $conn->prepare("SELECT p.* 
                              FROM publicacion p
                              JOIN proyecto pr ON p.id_proyectos = pr.id_proyectos
                              WHERE p.id_publicacion = ? AND pr.id_contratista = ?");
        $stmt->execute([$_GET['editar'], $usuario['id_usuario']]);
        $publicacionEditar = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($publicacionEditar) {
            $accion = 'editar';
        }
    } catch (PDOException $e) {
        $mensaje = "Error al obtener publicación: " . $e->getMessage();
    }
}

// Obtener proyectos y publicaciones
try {
    $stmt = $conn->prepare("SELECT * FROM proyecto WHERE id_contratista = ?");
    $stmt->execute([$usuario['id_usuario']]);
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($proyectos as &$proyecto) {
        $stmt = $conn->prepare("SELECT * FROM publicacion WHERE id_proyectos = ?");
        $stmt->execute([$proyecto['id_proyectos']]);
        $proyecto['publicaciones'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Publicaciones</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --light: #ecf0f1;
            --danger: #e74c3c;
            --success: #2ecc71;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f5f6fa;
            color: var(--primary);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .publicacion {
            border-left: 4px solid var(--secondary);
            margin: 1rem 0;
            padding: 1rem;
            background: var(--light);
        }

        .formulario {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .imagen-preview {
            max-width: 200px;
            margin: 1rem 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenido <?= htmlspecialchars($usuario['nombre']) ?></h1>
            <p>Gestiona tus proyectos y publicaciones</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert <?= strpos($mensaje, 'Error') === 0 ? 'alert-error' : 'alert-success' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Tus Proyectos</h2>
            <?php foreach ($proyectos as $proyecto): ?>
                <div class="card" style="margin-top: 1rem;">
                    <h3><?= htmlspecialchars($proyecto['titulo']) ?> 
                        <small>(<?= ucfirst($proyecto['etapa']) ?>)</small>
                    </h3>
                    
                    <?php if (!empty($proyecto['publicaciones'])): ?>
                        <div class="grid">
                            <?php foreach ($proyecto['publicaciones'] as $publicacion): ?>
                                <div class="publicacion">
                                    <h4><?= htmlspecialchars($publicacion['titulo']) ?></h4>
                                    <p><?= htmlspecialchars($publicacion['descripcion']) ?></p>
                                    <?php if ($publicacion['imagen']): ?>
                                        <img src="<?= htmlspecialchars($publicacion['imagen']) ?>" 
                                             class="imagen-preview"
                                             alt="Imagen publicación">
                                    <?php endif; ?>
                                    <p>Peso: <?= htmlspecialchars($publicacion['peso']) ?> kg</p>
                                    <a href="?editar=<?= $publicacion['id_publicacion'] ?>" 
                                       class="btn btn-primary">
                                        Editar
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No hay publicaciones en este proyecto</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="formulario">
            <h2><?= $accion === 'editar' ? 'Editar' : 'Nueva' ?> Publicación</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <?php if ($accion === 'editar'): ?>
                    <input type="hidden" name="editar" value="1">
                    <input type="hidden" name="id_publicacion" 
                           value="<?= $publicacionEditar['id_publicacion'] ?? '' ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Proyecto:</label>
                    <select name="id_proyectos" required 
                        <?= $accion === 'editar' ? 'disabled' : '' ?>>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <option value="<?= $proyecto['id_proyectos'] ?>"
                                <?= ($accion === 'editar' && $proyecto['id_proyectos'] == ($publicacionEditar['id_proyectos'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($proyecto['titulo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" required 
                           value="<?= $publicacionEditar['titulo'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="4" required><?= 
                        $publicacionEditar['descripcion'] ?? '' ?></textarea>
                </div>

                <div class="form-group">
                    <label>Imagen:</label>
                    <input type="file" name="imagen" accept="image/*">
                    <?php if ($accion === 'editar' && !empty($publicacionEditar['imagen'])): ?>
                        <p>Imagen actual: 
                            <a href="<?= htmlspecialchars($publicacionEditar['imagen']) ?>" 
                               target="_blank">
                                Ver imagen
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Peso (kg):</label>
                    <input type="number" step="0.01" name="peso" required 
                           value="<?= $publicacionEditar['peso'] ?? '' ?>">
                </div>

                <button type="submit" class="btn btn-success">
                    <?= $accion === 'editar' ? 'Guardar Cambios' : 'Crear Publicación' ?>
                </button>
                
                <?php if ($accion === 'editar'): ?>
                    <a href="?" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>