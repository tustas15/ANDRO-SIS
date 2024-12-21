<?php
session_start();
require_once '../conection/conexion.php';

header('Content-Type: application/json');

if (isset($_SESSION['usuario_id']) && isset($_POST['id_proyecto'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $id_proyecto = $_POST['id_proyecto'];

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Verificar si ya existe el me gusta
        $stmt_check = $conn->prepare("SELECT * FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
        $stmt_check->bindParam(':id_usuario', $id_usuario);
        $stmt_check->bindParam(':id_proyecto', $id_proyecto);
        $stmt_check->execute();
        $existing_like = $stmt_check->fetch();

        if ($existing_like) {
            // Eliminar el me gusta
            $stmt_delete = $conn->prepare("DELETE FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
            $stmt_delete->bindParam(':id_usuario', $id_usuario);
            $stmt_delete->bindParam(':id_proyecto', $id_proyecto);
            $stmt_delete->execute();
            $accion = 'quitar';
        } else {
            // Insertar el me gusta
            $stmt_insert = $conn->prepare("INSERT INTO MeGusta (id_usuario, id_proyecto) VALUES (:id_usuario, :id_proyecto)");
            $stmt_insert->bindParam(':id_usuario', $id_usuario);
            $stmt_insert->bindParam(':id_proyecto', $id_proyecto);
            $stmt_insert->execute();
            $accion = 'dar';
        }

        // Obtener el nuevo total
        $stmt_count = $conn->prepare("SELECT COUNT(*) AS total_me_gusta FROM MeGusta WHERE id_proyecto = :id_proyecto");
        $stmt_count->bindParam(':id_proyecto', $id_proyecto);
        $stmt_count->execute();
        $total = $stmt_count->fetch();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'accion' => $accion,
            'total_me_gusta' => $total['total_me_gusta']
        ]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No autenticado o parámetros inválidos'
    ]);
}
?>