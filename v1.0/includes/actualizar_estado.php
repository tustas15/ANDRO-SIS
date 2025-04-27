<?php
require_once '../conection/conexion.php';
session_start();

// Verificar permisos y sesión
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_usuario'];
    $estado = $_POST['estado'];
    
    try {
        $sql = "UPDATE Usuarios SET estado = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$estado, $id]);
        echo 'exito';
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'error';
    }
    exit();
}

http_response_code(405);
exit();