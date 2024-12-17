<?php
session_start();
require_once '../conection/conexion.php';

// Comentar en un proyecto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario']) && isset($_POST['id_proyecto'])) {
    $comentario = $_POST['comentario'];
    $id_proyecto = $_POST['id_proyecto'];
    $id_usuario = $_SESSION['usuario_id']; // Usuario autenticado

    try {
        $stmt = $conn->prepare("INSERT INTO Comentarios (id_usuario, id_proyecto, comentario) 
                               VALUES (:id_usuario, :id_proyecto, :comentario)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();

        // Redirigir después de comentar
        header(header: "Location: index.html");
        exit;
    } catch (PDOException $e) {
        echo "Error al comentar: " . $e->getMessage();
    }
}
?>
