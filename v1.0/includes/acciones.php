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
        $id_publicacion = $_POST['id_publicacion'];
        
        $stmt = $conn->prepare("SELECT * FROM MeGusta WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_publicacion', $id_publicacion);
        $stmt->execute();
        $megusta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($megusta) {
            $stmt = $conn->prepare("DELETE FROM MeGusta WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion");
        } else {
            $stmt = $conn->prepare("INSERT INTO MeGusta (id_usuario, id_publicacion) VALUES (:id_usuario, :id_publicacion)");
        }
        
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_publicacion', $id_publicacion);
        
        if ($stmt->execute()) {
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id_publicacion");
            $stmt->bindParam(':id_publicacion', $id_publicacion);
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $response = [
                'success' => true,
                'total' => $total, // Cambiado de 'total_megusta' a 'total'
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
    $id_publicacion = $_POST['id_publicacion'];
    $comentario = trim($_POST['comentario']);
    
    if (empty($comentario)) {
        $response['message'] = 'El comentario no puede estar vacío';
        echo json_encode($response);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO Comentarios (id_usuario, id_publicacion, comentario) VALUES (:id_usuario, :id_publicacion, :comentario)");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':id_publicacion', $id_publicacion);
    $stmt->bindParam(':comentario', $comentario);
    
    if ($stmt->execute()) {
        // Obtener todos los comentarios actualizados
        $stmt = $conn->prepare("SELECT c.*, u.nombre, u.apellido 
        FROM Comentarios c 
        INNER JOIN Usuarios u ON c.id_usuario = u.id_usuario 
        WHERE c.id_publicacion = :id_publicacion 
        ORDER BY c.fecha DESC");
        $stmt->bindParam(':id_publicacion', $id_publicacion);
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