<?php
require_once '../conection/conexion.php';

// Verificar si el usuario es contratista y está logueado
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'contratista') {
    header("Location: login.php");
    exit();
}

$contratista_id = $_SESSION['id_usuario'];
$error = '';
$success = '';

// Obtener proyectos del contratista
$stmt_proyectos = $conn->prepare("SELECT * FROM proyecto WHERE id_contratista = ?");
$stmt_proyectos->execute([$contratista_id]);
$proyectos = $stmt_proyectos->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_publicacion'])) {
        $id_proyectos = $_POST['id_proyectos'];
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $peso = $_POST['peso'];
        
        if (empty($titulo) || empty($descripcion) || empty($peso)) {
            $error = "Todos los campos son obligatorios";
        } else {
            $imagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/';
                $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                    $imagen = $fileName;
                }
            }
            
            try {
                $stmt = $conn->prepare("INSERT INTO publicacion 
                    (id_proyectos, titulo, descripcion, imagen, peso) 
                    VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id_proyectos, $titulo, $descripcion, $imagen, $peso]);
                $success = "Publicación creada exitosamente";
            } catch (PDOException $e) {
                $error = "Error al crear la publicación: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['editar_publicacion'])) {
        $id_publicacion = $_POST['id_publicacion'];
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $peso = $_POST['peso'];
        
        $stmt = $conn->prepare("SELECT p.id_proyectos 
                               FROM publicacion pub
                               JOIN proyecto p ON pub.id_proyectos = p.id_proyectos
                               WHERE pub.id_publicacion = ? AND p.id_contratista = ?");
        $stmt->execute([$id_publicacion, $contratista_id]);
        
        if ($stmt->rowCount() === 0) {
            $error = "No tienes permiso para editar esta publicación";
        } else {
            $imagen = $_POST['imagen_actual'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/';
                $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                    if ($imagen) {
                        @unlink($uploadDir . $imagen);
                    }
                    $imagen = $fileName;
                }
            }
            
            try {
                $stmt = $conn->prepare("UPDATE publicacion 
                                        SET titulo = ?, descripcion = ?, imagen = ?, peso = ?
                                        WHERE id_publicacion = ?");
                $stmt->execute([$titulo, $descripcion, $imagen, $peso, $id_publicacion]);
                $success = "Publicación actualizada exitosamente";
            } catch (PDOException $e) {
                $error = "Error al actualizar la publicación: " . $e->getMessage();
            }
        }
    }
    
}

// Obtener publicaciones agrupadas por proyecto
$stmt_publicaciones = $conn->prepare("SELECT 
        p.id_proyectos AS proyecto_id,
        p.titulo AS proyecto_titulo,
        pub.id_publicacion,
        pub.titulo AS publicacion_titulo,
        pub.descripcion,
        pub.imagen,
        pub.peso,
        pub.fecha_publicacion
    FROM proyecto p
    LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
    WHERE p.id_contratista = ?
    ORDER BY p.id_proyectos, pub.fecha_publicacion DESC");

$stmt_publicaciones->execute([$contratista_id]);
$resultados = $stmt_publicaciones->fetchAll(PDO::FETCH_ASSOC);

$proyectos_con_publicaciones = [];
foreach ($resultados as $row) {
    $proyecto_id = $row['proyecto_id'];
    
    if (!isset($proyectos_con_publicaciones[$proyecto_id])) {
        $proyectos_con_publicaciones[$proyecto_id] = [
            'titulo' => $row['proyecto_titulo'],
            'publicaciones' => []
        ];
    }
    
    if ($row['id_publicacion']) {
        $proyectos_con_publicaciones[$proyecto_id]['publicaciones'][] = [
            'id_publicacion' => $row['id_publicacion'],
            'titulo' => $row['publicacion_titulo'],
            'descripcion' => $row['descripcion'],
            'imagen' => $row['imagen'],
            'peso' => $row['peso'],
            'fecha' => $row['fecha_publicacion']
        ];
    }
}

// Obtener datos para edición
$editar_publicacion = null;
if (isset($_GET['editar'])) {
    $id_publicacion = $_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM publicacion WHERE id_publicacion = ?");
    $stmt->execute([$id_publicacion]);
    $editar_publicacion = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Contratista</title>
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; }
        .error { color: red; }
        .success { color: green; }
        .proyecto-container { margin: 30px 0; border: 2px solid #007bff; border-radius: 8px; padding: 15px; }
        .proyecto-titulo { color: #007bff; margin-bottom: 15px; }
        .publicacion-item { margin-left: 20px; margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; }
        input, textarea, select { width: 100%; margin: 5px 0 15px; padding: 8px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        img { max-width: 200px; height: auto; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Panel del Contratista</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <!-- Formulario de publicación -->
        <div class="form-section">
            <h2><?php echo $editar_publicacion ? 'Editar' : 'Nueva'; ?> Publicación</h2>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editar_publicacion): ?>
                    <input type="hidden" name="id_publicacion" value="<?php echo $editar_publicacion['id_publicacion']; ?>">
                    <input type="hidden" name="imagen_actual" value="<?php echo $editar_publicacion['imagen']; ?>">
                <?php endif; ?>
                
                <div>
                    <label>Proyecto:</label>
                    <select name="id_proyectos" required>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <option value="<?php echo $proyecto['id_proyectos']; ?>"
                                <?php if ($editar_publicacion && $editar_publicacion['id_proyectos'] == $proyecto['id_proyectos']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($proyecto['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label>Título:</label>
                    <input type="text" name="titulo" required 
                           value="<?php echo $editar_publicacion ? htmlspecialchars($editar_publicacion['titulo']) : ''; ?>">
                </div>
                
                <div>
                    <label>Descripción:</label>
                    <textarea name="descripcion" required><?php 
                        echo $editar_publicacion ? htmlspecialchars($editar_publicacion['descripcion']) : ''; 
                    ?></textarea>
                </div>
                
                <div>
                    <label>Imagen:</label>
                    <input type="file" name="imagen">
                    <?php if ($editar_publicacion && $editar_publicacion['imagen']): ?>
                        <p>Imagen actual: <?php echo $editar_publicacion['imagen']; ?></p>
                        <img src="../uploads/<?php echo htmlspecialchars($editar_publicacion['imagen']); ?>" width="200">
                    <?php endif; ?>
                </div>
                
                <div>
                    <label>Peso (%):</label>
                    <input type="number" step="0.01" name="peso" required 
                           value="<?php echo $editar_publicacion ? $editar_publicacion['peso'] : ''; ?>">
                </div>
                
                <button type="submit" name="<?php echo $editar_publicacion ? 'editar_publicacion' : 'crear_publicacion'; ?>">
                    <?php echo $editar_publicacion ? 'Actualizar' : 'Crear'; ?> Publicación
                </button>
                
                <?php if ($editar_publicacion): ?>
                    <a href="?">Cancelar edición</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Listado de proyectos con publicaciones -->
        <div class="publicaciones-list">
            <h2>Publicaciones por Proyecto</h2>
            
            <?php if (empty($proyectos_con_publicaciones)): ?>
                <p>No tienes proyectos con publicaciones</p>
            <?php else: ?>
                <?php foreach ($proyectos_con_publicaciones as $proyecto): ?>
                    <div class="proyecto-container">
                        <h3 class="proyecto-titulo">
                            <?php echo htmlspecialchars($proyecto['titulo']); ?>
                        </h3>
                        
                        <?php if (empty($proyecto['publicaciones'])): ?>
                            <p>Este proyecto no tiene publicaciones aún</p>
                        <?php else: ?>
                            <?php foreach ($proyecto['publicaciones'] as $pub): ?>
                                <div class="publicacion-item">
                                    <h4><?php echo htmlspecialchars($pub['titulo']); ?></h4>
                                    <p><?php echo htmlspecialchars($pub['descripcion']); ?></p>
                                    <p>Porcentaje: <?php echo $pub['peso']; ?> %</p>
                                    <small><?php echo date('d/m/Y H:i', strtotime($pub['fecha'])); ?></small>
                                    <?php if ($pub['imagen']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($pub['imagen']); ?>" width="200">
                                    <?php endif; ?>
                                    <a href="?editar=<?php echo $pub['id_publicacion']; ?>">Editar</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>