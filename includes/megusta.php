<?php
session_start();
require_once '../conection/conexion.php';

// Comprobar si el usuario está autenticado
if (isset($_SESSION['usuario_id']) && isset($_POST['id_proyecto'])) {
    $id_usuario = $_SESSION['usuario_id']; // Obtener el id del usuario
    $id_proyecto = $_POST['id_proyecto']; // Obtener el id del proyecto

    // Verificar si el usuario ya ha dado "Me gusta"
    $stmt_check = $conn->prepare("SELECT * FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
    $stmt_check->bindParam(':id_usuario', $id_usuario);
    $stmt_check->bindParam(':id_proyecto', $id_proyecto);
    $stmt_check->execute();
    $existing_like = $stmt_check->fetch();

    if ($existing_like) {
        // Si el usuario ya ha dado "Me gusta", eliminarlo
        $stmt_delete = $conn->prepare("DELETE FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
        $stmt_delete->bindParam(':id_usuario', $id_usuario);
        $stmt_delete->bindParam(':id_proyecto', $id_proyecto);
        $stmt_delete->execute();
        $accion = 'quitar';
    } else {
        // Si el usuario no ha dado "Me gusta", insertarlo
        $stmt_insert = $conn->prepare("INSERT INTO MeGusta (id_usuario, id_proyecto) VALUES (:id_usuario, :id_proyecto)");
        $stmt_insert->bindParam(':id_usuario', $id_usuario);
        $stmt_insert->bindParam(':id_proyecto', $id_proyecto);
        $stmt_insert->execute();
        $accion = 'dar';
    }

    // Obtener el nuevo total de "Me gusta" para el proyecto
    $stmt_me_gusta = $conn->prepare("SELECT COUNT(*) AS total_me_gusta FROM MeGusta WHERE id_proyecto = :id_proyecto");
    $stmt_me_gusta->bindParam(':id_proyecto', $id_proyecto);
    $stmt_me_gusta->execute();
    $total_me_gusta = $stmt_me_gusta->fetch();

    // Redirigir de vuelta al proyecto con el nuevo total de "Me gusta"
    header('Location: index.html');
    exit;
} else {
    echo json_encode(['error' => 'No autenticado o parámetros inválidos']);
    exit;
}
?>
