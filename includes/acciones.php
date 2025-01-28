<?php
// procesar_acciones.php
require '../conection/conexion.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['id_usuario'])) {
        $response['message'] = 'Debes iniciar sesión para realizar esta acción';
        echo json_encode($response);
        exit;
    }
    
    // Procesar me gusta
    if (isset($_POST['action']) && $_POST['action'] === 'megusta') {
        $id_usuario = $_SESSION['id_usuario'];
        $id_proyecto = $_POST['id_proyecto'];
        
        $stmt = $conn->prepare("SELECT * FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->execute();
        $megusta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($megusta) {
            $stmt = $conn->prepare("DELETE FROM MeGusta WHERE id_usuario = :id_usuario AND id_proyecto = :id_proyecto");
        } else {
            $stmt = $conn->prepare("INSERT INTO MeGusta (id_usuario, id_proyecto) VALUES (:id_usuario, :id_proyecto)");
        }
        
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        
        if ($stmt->execute()) {
            // Obtener el nuevo total de me gusta
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM MeGusta WHERE id_proyecto = :id_proyecto");
            $stmt->bindParam(':id_proyecto', $id_proyecto);
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $response = [
                'success' => true,
                'total_megusta' => $total,
                'dio_megusta' => !$megusta
            ];
        } else {
            $response['message'] = 'Error al procesar el me gusta';
        }
    }
    
    // Procesar comentarios
    if (isset($_POST['action']) && $_POST['action'] === 'comentar') {
        $id_usuario = $_SESSION['id_usuario'];
        $id_proyecto = $_POST['id_proyecto'];
        $comentario = trim($_POST['comentario']);
        
        if (empty($comentario)) {
            $response['message'] = 'El comentario no puede estar vacío';
            echo json_encode($response);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO Comentarios (id_usuario, id_proyecto, comentario) VALUES (:id_usuario, :id_proyecto, :comentario)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->bindParam(':comentario', $comentario);
        
        if ($stmt->execute()) {
            // Obtener todos los comentarios actualizados
            $stmt = $conn->prepare("SELECT c.*, u.nombre, u.apellido 
            FROM Comentarios c 
            INNER JOIN Usuarios u ON c.id_usuario = u.id_usuario 
            WHERE c.id_proyecto = :id_proyecto 
            ORDER BY c.fecha DESC");
            $stmt->bindParam(':id_proyecto', $id_proyecto);
            $stmt->execute();
            $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [
                'success' => true,
                'comentarios' => $comentarios
            ];
        } else {
            $response['message'] = 'Error al guardar el comentario';
        }
    }
    
    echo json_encode($response);
    exit;
}