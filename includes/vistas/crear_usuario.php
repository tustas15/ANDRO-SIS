<?php
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../conection/conexion.php';

$mensaje = ''; // Variable para mostrar mensajes de éxito o error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $perfil = $_POST['perfil'];
    $estado = $_POST['estado'];

    try {
        // Insertar el nuevo usuario en la base de datos
        $query = "INSERT INTO Usuarios (nombre, apellido, correo, contrasena, perfil, estado) 
                  VALUES (:nombre, :apellido, :correo, :contrasena, :perfil, :estado)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':contrasena' => $contrasena,
            ':perfil' => $perfil,
            ':estado' => $estado
        ]);

        $mensaje = "Usuario creado exitosamente.";
    } catch (PDOException $e) {
        $mensaje = "Error al crear el usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if ($mensaje): ?>
        <p><?php echo $mensaje; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <label for="perfil">Perfil:</label>
        <select id="perfil" name="perfil" required>
            <option value="admin">Admin</option>
            <option value="contratista">Contratista</option>
            <option value="publico">Público</option>
        </select><br>

        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="activo">Activo</option>
            <option value="desactivo">Desactivado</option>
        </select><br>

        <button type="submit">Crear Usuario</button>
    </form>
</body>
</html>