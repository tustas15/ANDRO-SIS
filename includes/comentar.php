<?php
session_start();
require_once '../conection/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario']) && isset($_POST['id_proyecto'])) {
    $comentario = trim($_POST['comentario']); // Eliminar espacios en blanco
    $id_proyecto = $_POST['id_proyecto'];
    $id_usuario = $_SESSION['usuario_id'];

    // Verificar que el comentario no esté vacío
    if (empty($comentario)) {
        echo json_encode(['success' => false, 'error' => 'El comentario no puede estar vacío']);
        exit;
    }

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Verificar si existe un comentario idéntico reciente (últimos 5 segundos)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Comentarios 
                               WHERE id_usuario = :id_usuario 
                               AND id_proyecto = :id_proyecto 
                               AND comentario = :comentario 
                               AND fecha >= DATE_SUB(NOW(), INTERVAL 5 SECOND)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Comentario duplicado']);
            exit;
        }

        // Insertar el nuevo comentario
        $stmt = $conn->prepare("INSERT INTO Comentarios (id_usuario, id_proyecto, comentario) 
                               VALUES (:id_usuario, :id_proyecto, :comentario)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();

        $conn->commit();
        echo json_encode(['success' => true]);
        exit;
        
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_proyecto'])) {
    try {
        $stmt = $conn->prepare("SELECT comentarios.*, Usuarios.nombre 
                               FROM Comentarios comentarios 
                               JOIN Usuarios ON comentarios.id_usuario = Usuarios.id_usuario 
                               WHERE comentarios.id_proyecto = :id_proyecto 
                               ORDER BY comentarios.fecha DESC");
        $stmt->bindParam(':id_proyecto', $_GET['id_proyecto']);
        $stmt->execute();
        $comentarios = $stmt->fetchAll();

        foreach ($comentarios as $comentario) {
            echo "<div class='comentario'>";
            echo "<strong>" . htmlspecialchars($comentario['nombre']) . "</strong>: " . 
                 htmlspecialchars($comentario['comentario']);
            echo "</div>";
        }
        exit;
    } catch (PDOException $e) {
        echo "Error al cargar comentarios: " . $e->getMessage();
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Solicitud inválida']);
?>