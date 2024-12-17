<?php
session_start();
require_once '../conection/conexion.php';

header('Content-Type: application/json');

// Si es una solicitud POST, agregar comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && isset($_POST['id_proyecto'])) {
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

        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al comentar: ' . $e->getMessage()]);
        exit;
    }
}

// Si es una solicitud GET, devolver los comentarios
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_proyecto'])) {
    $id_proyecto = $_GET['id_proyecto'];

    try {
        $stmt = $conn->prepare("SELECT comentarios.*, Usuarios.nombre FROM Comentarios comentarios 
                                JOIN Usuarios ON comentarios.id_usuario = Usuarios.id_usuario 
                                WHERE comentarios.id_proyecto = :id_proyecto");
        $stmt->bindParam(':id_proyecto', $id_proyecto);
        $stmt->execute();
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los comentarios en formato HTML
        foreach ($comentarios as $comentario) {
            echo "<div class='comentario'>";
            echo "<strong>" . htmlspecialchars($comentario['nombre']) . "</strong>: " . htmlspecialchars($comentario['comentario']);
            echo "</div>";
        }
        exit;
    } catch (PDOException $e) {
        echo "Error al obtener los comentarios: " . $e->getMessage();
        exit;
    }
}
?>
