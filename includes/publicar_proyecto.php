<?php
session_start();
require_once '../conection/conexion.php';  // Conexión a la base de datos

// Publicar un proyecto nuevo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo']) && isset($_POST['descripcion'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen']; // Si deseas permitir la subida de imágenes
    $id_contratista = $_SESSION['usuario_id']; // ID del contratista (usuario autenticado)

    try {
        $stmt = $conn->prepare("INSERT INTO Proyectos (id_contratista, titulo, descripcion, imagen, etapa) 
                               VALUES (:id_contratista, :titulo, :descripcion, :imagen, 'planificacion')");
        $stmt->bindParam(':id_contratista', $id_contratista);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->execute();

        // Redirigir después de publicar el proyecto
        header("Location: proyectos.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al publicar el proyecto: " . $e->getMessage();
    }
}
?>
