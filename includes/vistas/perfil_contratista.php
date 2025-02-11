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

// Si no se encuentra el contratista
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
        <h1><?php echo htmlspecialchars($contratista['nombre'] . ' ' . htmlspecialchars($contratista['apellido'])); ?></h1>
        <p>Correo: <?php echo htmlspecialchars($contratista['correo']); ?></p>
    </div>
</div>

<!-- Listado de proyectos -->
<h2>Proyectos realizados</h2>
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
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este contratista aún no tiene proyectos publicados</p>
    <?php endif; ?>
</div>

</body>
</html>