<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Iniciar la sesión si no está activa
}
require_once '../conection/conexion.php';

// Obtener la información del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$query = "SELECT nombre, apellido, correo, perfil, imagen_perfil FROM Usuarios WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];

    // Procesar la subida de la imagen de perfil
    if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
        $imagen_nombre = basename($_FILES['imagen_perfil']['name']);
        $imagen_tmp = $_FILES['imagen_perfil']['tmp_name'];
        $ruta_imagen = __DIR__ . '/../../assets/img/perfiles/' . $imagen_nombre; // Ajusta la ruta según tu estructura

        // Mover la imagen a la carpeta de perfiles
        if (move_uploaded_file($imagen_tmp, $ruta_imagen)) {
            $imagen_perfil = 'assets/img/perfiles/' . $imagen_nombre; // Ruta relativa para la base de datos
        } else {
            $imagen_perfil = $usuario['imagen_perfil']; // Mantener la imagen actual si hay un error
        }
    } else {
        $imagen_perfil = $usuario['imagen_perfil']; // Mantener la imagen actual si no se subió una nueva
    }

    // Actualizar la información del usuario en la base de datos
    $query = "UPDATE Usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, imagen_perfil = :imagen_perfil WHERE id_usuario = :id_usuario";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
    $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindParam(':imagen_perfil', $imagen_perfil, PDO::PARAM_STR);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['nombre'] = $nombre; // Actualizar el nombre en la sesión
        $_SESSION['imagen_perfil'] = $imagen_perfil; // Actualizar la imagen en la sesión
        $mensaje = "Perfil actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el perfil.";
    }
}
?>


    <div class="container">
        <h1>Perfil</h1>

        <?php if (isset($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="index.php?view=perfil" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label for="imagen_perfil">Imagen de Perfil:</label>
                <input type="file" id="imagen_perfil" name="imagen_perfil">
                <?php if ($usuario['imagen_perfil']): ?>
                    <img src="<?php echo htmlspecialchars($usuario['imagen_perfil'], ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen de perfil" width="100">
                <?php endif; ?>
            </div>

            <button type="submit">Actualizar Perfil</button>
        </form>
    </div>
