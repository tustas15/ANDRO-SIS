<?php
require_once '../conection/conexion.php';



// Procesar ambas acciones (crear y eliminar)
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Crear nueva categoría
    if (isset($_POST['crear_categoria'])) {
        $nombre = trim($_POST['nombre_categoria']);
        
        if (!empty($nombre)) {
            try {
                $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->execute();
                $mensaje = "Categoría creada exitosamente";
            } catch (PDOException $e) {
                $error = "Error al crear categoría: " . $e->getMessage();
            }
        } else {
            $error = "El nombre de la categoría no puede estar vacío";
        }
    }
    
    // Eliminar categoría
    if (isset($_POST['eliminar_categoria'])) {
        $id_categoria = $_POST['id_categoria'];
        
        try {
            // Verificar si la categoría tiene proyectos
            $sql = "SELECT COUNT(*) FROM proyecto WHERE id_categoria = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id_categoria);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $sql = "DELETE FROM categorias WHERE id_categoria = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id_categoria);
                $stmt->execute();
                $mensaje = "Categoría eliminada exitosamente";
            } else {
                $error = "No se puede eliminar categoría con proyectos asociados";
            }
        } catch (PDOException $e) {
            $error = "Error al eliminar categoría: " . $e->getMessage();
        }
    }
}

// Obtener categorías
try {
    $sql = "SELECT c.id_categoria, c.nombre AS categoria_nombre, 
            GROUP_CONCAT(p.titulo SEPARATOR ', ') AS proyectos
            FROM categorias c
            LEFT JOIN proyecto p ON c.id_categoria = p.id_categoria
            GROUP BY c.id_categoria
            ORDER BY c.id_categoria";
    $stmt = $conn->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener categorías: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Categorías</title>
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-group { margin-bottom: 15px; }
        input[type="text"] { padding: 5px; width: 300px; }
        .form-eliminar { display: inline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Categorías</h1>
        
        <?php if (!empty($mensaje)): ?>
            <p class="success"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <h2>Crear Nueva Categoría</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nombre de la categoría:</label>
                <input type="text" name="nombre_categoria" required>
            </div>
            <button type="submit" name="crear_categoria">Crear Categoría</button>
        </form>

        <h2>Listado de Categorías</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Proyectos asociados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['categoria_nombre']); ?></td>
                    <td><?php echo $categoria['proyectos'] ? htmlspecialchars($categoria['proyectos']) : 'Sin proyectos'; ?></td>
                    <td>
                        <?php if (empty($categoria['proyectos'])): ?>
                            <form class="form-eliminar" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                                <button type="submit" name="eliminar_categoria">Eliminar</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #999;">No se puede eliminar</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>