<?php
session_start();
require_once '../conection/conexion.php';

// Mostrar los proyectos
$stmt = $conn->prepare("SELECT proyectos.*, usuarios.nombre FROM proyectos 
                        JOIN Usuarios ON proyectos.id_contratista = Usuarios.id_usuario ORDER BY fecha_publicacion DESC");
$stmt->execute();
$proyectos = $stmt->fetchAll();

foreach ($proyectos as $proyecto) {
    echo "<div class='proyecto'>";
    echo "<h3>" . htmlspecialchars($proyecto['titulo']) . "</h3>";
    echo "<p><strong>" . htmlspecialchars($proyecto['nombre']) . "</strong>: " . htmlspecialchars($proyecto['descripcion']) . "</p>";
    echo "<img src='" . htmlspecialchars($proyecto['imagen']) . "' alt='Imagen del proyecto'>";
    echo "<small>" . $proyecto['fecha_publicacion'] . "</small>";

    // Mostrar los comentarios
    echo "<h3>Comentarios:</h3>";
    $stmt_comentarios = $conn->prepare("SELECT comentarios.*, Usuarios.nombre FROM Comentarios comentarios 
                                       JOIN Usuarios ON comentarios.id_usuario = Usuarios.id_usuario WHERE comentarios.id_proyecto = :id_proyecto");
    $stmt_comentarios->bindParam(':id_proyecto', $proyecto['id_proyecto']);
    $stmt_comentarios->execute();
    $comentarios = $stmt_comentarios->fetchAll();

    foreach ($comentarios as $comentario) {
        echo "<div class='comentario'>";
        echo "<strong>" . htmlspecialchars($comentario['nombre']) . "</strong>: " . htmlspecialchars($comentario['comentario']);
        echo "</div>";
    }

    // Formulario para comentar
    echo "<form action='comentar.php' method='POST'>
            <textarea name='comentario' placeholder='Escribe tu comentario'></textarea>
            <input type='hidden' name='id_proyecto' value='" . $proyecto['id_proyecto'] . "'>
            <button type='submit'>Comentar</button>
          </form>";

    // Mostrar "me gusta"
    $stmt_me_gusta = $conn->prepare("SELECT COUNT(*) AS total_me_gusta FROM MeGusta WHERE id_proyecto = :id_proyecto");
    $stmt_me_gusta->bindParam(':id_proyecto', $proyecto['id_proyecto']);
    $stmt_me_gusta->execute();
    $total_me_gusta = $stmt_me_gusta->fetch();

    // Mostrar el botón de "Me gusta" como un formulario
    echo '<form action="megusta.php" method="POST">
            <input type="hidden" name="id_proyecto" value="' . $proyecto['id_proyecto'] . '">
            <button type="submit" class="btn-me-gusta">
                <span>' . $total_me_gusta['total_me_gusta'] . '</span> Me gusta
            </button>
          </form>';
    echo "</div>"; // Cerrar div proyecto
}
?>
