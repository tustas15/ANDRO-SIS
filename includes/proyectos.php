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
    echo "<div class='comentarios' style='display: none;'>";
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
    echo "</div>"; // Cerrar div comentarios

    // Botones "Me gusta" y "Agregar comentario"
    echo "<div class='acciones' style='display: flex; gap: 10px; align-items: center;'>";

    // Mostrar "me gusta"
    $stmt_me_gusta = $conn->prepare("SELECT COUNT(*) AS total_me_gusta FROM MeGusta WHERE id_proyecto = :id_proyecto");
    $stmt_me_gusta->bindParam(':id_proyecto', $proyecto['id_proyecto']);
    $stmt_me_gusta->execute();
    $total_me_gusta = $stmt_me_gusta->fetch();

    

    echo '<form class="form-me-gusta" data-proyecto-id="' . $proyecto['id_proyecto'] . '" action="megusta.php" method="POST" style="margin: 0;">
        <input type="hidden" name="id_proyecto" value="' . $proyecto['id_proyecto'] . '">
        <button type="submit" class="btn-me-gusta" data-proyecto-id="' . $proyecto['id_proyecto'] . '">
            ' . $total_me_gusta['total_me_gusta'] . ' | Me gusta
        </button>
      </form>';

    // Botón "Ver comentarios"
    echo "<button class='btn-ver-comentarios'>Ver Comentarios</button>";

    echo "</div>"; // Cerrar div acciones

    // Formulario "Agregar comentario"
    echo "<form class='form-comentario' data-proyecto-id='" . $proyecto['id_proyecto'] . "' action='comentar.php' method='POST'>";
    echo "    <textarea name='comentario' placeholder='Escribe tu comentario'></textarea>";
    echo "    <input type='hidden' name='id_proyecto' value='" . $proyecto['id_proyecto'] . "'>";
    echo "    <button type='submit'>Comentar</button>";
    echo "</form>";

    echo "</div>"; // Cerrar div proyecto
}
?>
