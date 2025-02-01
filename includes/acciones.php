<?php
// acciones.php
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
    // En la sección de procesar comentarios, modificar la respuesta para incluir si el comentario pertenece al usuario logueado
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
        
        // Agregar información sobre si el comentario pertenece al usuario logueado
        foreach ($comentarios as &$comentario) {
            $comentario['pertenece_al_usuario'] = ($comentario['id_usuario'] == $_SESSION['id_usuario']);
        }
        
        $response = [
            'success' => true,
            'comentarios' => $comentarios
        ];
    } else {
        $response['message'] = 'Error al guardar el comentario';
    }
}

    // Procesar eliminación de comentarios
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar_comentario') {
        $id_comentario = $_POST['id_comentario'];
        $id_usuario = $_SESSION['id_usuario'];
        
        // Verificar si el comentario pertenece al usuario
        $stmt = $conn->prepare("SELECT * FROM Comentarios WHERE id_comentario = :id_comentario AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_comentario', $id_comentario);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comentario) {
            $stmt = $conn->prepare("DELETE FROM Comentarios WHERE id_comentario = :id_comentario");
            $stmt->bindParam(':id_comentario', $id_comentario);
            
            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error al eliminar el comentario';
            }
        } else {
            $response['message'] = 'No tienes permiso para eliminar este comentario';
        }
    }

    // Procesar edición de comentarios
    if (isset($_POST['action']) && $_POST['action'] === 'editar_comentario') {
        $id_comentario = $_POST['id_comentario'];
        $id_usuario = $_SESSION['id_usuario'];
        $nuevo_comentario = trim($_POST['comentario']);
        
        // Verificar si el comentario pertenece al usuario
        $stmt = $conn->prepare("SELECT * FROM Comentarios WHERE id_comentario = :id_comentario AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_comentario', $id_comentario);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comentario) {
            $stmt = $conn->prepare("UPDATE Comentarios SET comentario = :comentario WHERE id_comentario = :id_comentario");
            $stmt->bindParam(':comentario', $nuevo_comentario);
            $stmt->bindParam(':id_comentario', $id_comentario);
            
            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error al editar el comentario';
            }
        } else {
            $response['message'] = 'No tienes permiso para editar este comentario';
        }
    }
    
    echo json_encode($response);
    exit;
}