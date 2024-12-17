<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "AndRo";  // Asegúrate de que este es el nombre correcto de tu base de datos
$port = 3306;  // Puerto de MySQL en XAMPP

try {
    $conn = new PDO("mysql:host=$server;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
    die();
}
?>
