<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Iniciar la sesión si no está activa
}
require_once '../conection/conexion.php';

try {
    // Obtener proyectos con contratistas y contar los "Me gusta"
    $stmt = $conn->prepare("
        SELECT p.*, u.nombre, 
               (SELECT COUNT(*) FROM MeGusta WHERE id_proyecto = p.id_proyecto) AS total_me_gusta
        FROM proyectos p
        JOIN Usuarios u ON p.id_contratista = u.id_usuario
        ORDER BY p.fecha_publicacion DESC
    ");
    $stmt->execute();
    $proyectos = $stmt->fetchAll();

    foreach ($proyectos as $proyecto) {
        ?>
        <div class="proyecto">
            <h3><?= htmlspecialchars($proyecto['titulo']) ?></h3>
            <p><strong><?= htmlspecialchars($proyecto['nombre']) ?></strong>: <?= htmlspecialchars($proyecto['descripcion']) ?></p>
            <img src="<?= htmlspecialchars($proyecto['imagen']) ?>" alt="Imagen del proyecto">
            <small><?= htmlspecialchars($proyecto['fecha_publicacion']) ?></small>

            <div class="acciones">
                <!-- Botón "Me gusta" -->
                <form class="form-me-gusta" action="megusta.php" method="POST">
                    <input type="hidden" name="id_proyecto" value="<?= $proyecto['id_proyecto'] ?>">
                    <button type="submit" class="btn-me-gusta">
                        <?= htmlspecialchars($proyecto['total_me_gusta']) ?> | Me gusta
                    </button>
                </form>

                <!-- Botón "Ver comentarios" -->
                <button class="btn-ver-comentarios" data-proyecto-id="<?= $proyecto['id_proyecto'] ?>">Ver Comentarios</button>
            </div>

            <!-- Comentarios (cargados dinámicamente con JS o inicialmente ocultos) -->
            <div class="comentarios" style="display: none;" data-proyecto-id="<?= $proyecto['id_proyecto'] ?>">
                <?php
                $stmt_comentarios = $conn->prepare("
                    SELECT c.*, u.nombre 
                    FROM Comentarios c
                    JOIN Usuarios u ON c.id_usuario = u.id_usuario
                    WHERE c.id_proyecto = :id_proyecto
                ");
                $stmt_comentarios->bindParam(':id_proyecto', $proyecto['id_proyecto']);
                $stmt_comentarios->execute();
                $comentarios = $stmt_comentarios->fetchAll();

                foreach ($comentarios as $comentario) {
                    ?>
                    <div class="comentario">
                        <strong><?= htmlspecialchars($comentario['nombre']) ?></strong>: <?= htmlspecialchars($comentario['comentario']) ?>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Formulario para agregar comentarios -->
            <form class="form-comentario" action="comentar.php" method="POST">
                <textarea name="comentario" placeholder="Escribe tu comentario" required></textarea>
                <input type="hidden" name="id_proyecto" value="<?= $proyecto['id_proyecto'] ?>">
                <button type="submit">Comentar</button>
            </form>
        </div>
        <?php
    }
} catch (PDOException $e) {
    echo "<p>Error al cargar los proyectos: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
