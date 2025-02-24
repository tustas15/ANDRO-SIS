<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'contratista') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $etapa = $_POST['etapa'];
    $id_contratista = $_SESSION['id_usuario'];
    $imagen = null;

    // Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        
        // Verificar tipo MIME
        $fileType = mime_content_type($file['tmp_name']);
        if ($fileType !== 'image/jpeg') {
            $_SESSION['error'] = 'Solo se permiten imágenes JPG';
            header('Location: crear_publicacion.php');
            exit();
        }
        
        // Crear directorio si no existe
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generar nombre único
        $filename = uniqid() . '.jpg';
        $destination = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['error'] = 'Error al guardar la imagen';
            header('Location: crear_publicacion.php');
            exit();
        }
        
        $imagen = $filename;
    }

    // Insertar en la base de datos
    $query = "INSERT INTO Proyectos (id_contratista, titulo, descripcion, etapa, imagen) 
              VALUES (:id_contratista, :titulo, :descripcion, :etapa, :imagen)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':id_contratista' => $id_contratista,
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':etapa' => $etapa,
        ':imagen' => $imagen
    ]);

    header('Location: mis_publicaciones.php');
    exit();
}
?>

<!-- Añade esto en el HTML para mostrar errores -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Publicación</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #f5f6fa;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.2em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
            font-size: 0.95em;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #dcdde1;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            background: #3498db;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: #2980b9;
        }

        .success-message {
            background: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: none;
        }

        @media (min-width: 768px) {
            body {
                padding: 60px 20px;
            }
            
            button {
                width: auto;
                display: block;
                margin: 0 auto;
                padding: 14px 35px;
            }
        }
        .error-message {
        background: #e74c3c;
        color: white;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 25px;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Publicación</h1>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título del Proyecto</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ingrese el título del proyecto">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción Detallada</label>
                <textarea id="descripcion" name="descripcion" required placeholder="Describa los detalles del proyecto"></textarea>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen del Proyecto (solo JPG)</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg">
            </div>

            <div class="form-group">
                <label for="etapa">Etapa Actual del Proyecto</label>
                <select id="etapa" name="etapa" required>
                    <option value="">Seleccione una etapa</option>
                    <option value="planificacion">Planificación</option>
                    <option value="ejecucion">Ejecución</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>

            <button type="submit">Publicar Proyecto</button>
        </form>
    </div>
</body>
</html>