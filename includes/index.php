<?php
session_start(); // Si necesitas sesiones en este archivo

// Función para cargar vistas dinámicamente
function cargarVista($vista) {
    $rutaVista = __DIR__ . "/vistas/$vista.php";
    if (file_exists($rutaVista)) {
        require $rutaVista;
    } else {
        echo "<p>La vista <strong>$vista</strong> no se encontró.</p>";
    }
}

// Establecer la vista por defecto si no se recibe en el parámetro "view"
$vista = isset($_GET['view']) ? $_GET['view'] : 'proyectos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../js/loadComponents.js"></script>
</head>
<body>
    <?php require 'header.php'; ?>
    
    <main class="container">
        <?php cargarVista($vista); ?>
    </main>
    
    <?php require 'footer.php'; ?>
</body>
</html>