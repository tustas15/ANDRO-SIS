<?php
require_once '../conection/conexion.php';


$contratistas = $conn->query("SELECT * FROM usuarios WHERE perfil = 'contratista'")->fetchAll(PDO::FETCH_ASSOC);
// Operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear nuevo proyecto
    if (isset($_POST['create'])) {
        $titulo = $_POST['titulo'];
        $id_categoria = $_POST['id_categoria'];
        $presupuesto = $_POST['presupuesto'];
        $etapa = $_POST['etapa'];
        $id_contratista = $_POST['id_contratista'];

        try {
            $stmt = $conn->prepare("INSERT INTO proyecto (id_contratista, titulo, id_categoria, presupuesto, etapa) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_contratista, $titulo, $id_categoria, $presupuesto, $etapa]);
            $msg = "Proyecto creado exitosamente!";
        } catch (PDOException $e) {
            $error = "Error al crear proyecto: " . $e->getMessage();
        }
    }

    // Editar proyecto
    if (isset($_POST['edit'])) {
        $id_proyectos = $_POST['id_proyectos'];
        $titulo = $_POST['titulo'];
        $id_categoria = $_POST['id_categoria'];
        $presupuesto = $_POST['presupuesto'];
        $etapa = $_POST['etapa'];
        $id_contratista = $_POST['id_contratista'];

        try {
            $stmt = $conn->prepare("UPDATE proyecto SET 
                                  titulo = ?, 
                                  id_categoria = ?, 
                                  presupuesto = ?, 
                                  etapa = ?,
                                  id_contratista = ? 
                                  WHERE id_proyectos = ?");
            $stmt->execute([$titulo, $id_categoria, $presupuesto, $etapa, $id_contratista, $id_proyectos]);
            $msg = "Proyecto actualizado exitosamente!";
        } catch (PDOException $e) {
            $error = "Error al actualizar proyecto: " . $e->getMessage();
        }
    }

    // Eliminar proyecto
    if (isset($_POST['delete'])) {
        $id_proyectos = $_POST['id_proyectos'];

        try {
            // Verificar si tiene comentarios
            $stmt = $conn->prepare("SELECT COUNT(*) AS total 
                                  FROM comentarios c
                                  JOIN publicacion p ON c.id_publicacion = p.id_publicacion
                                  WHERE p.id_proyectos = ?");
            $stmt->execute([$id_proyectos]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                $error = "No se puede eliminar el proyecto porque tiene comentarios";
            } else {
                $stmt = $conn->prepare("DELETE FROM proyecto WHERE id_proyectos = ?");
                $stmt->execute([$id_proyectos]);
                $msg = "Proyecto eliminado exitosamente!";
            }
        } catch (PDOException $e) {
            $error = "Error al eliminar proyecto: " . $e->getMessage();
        }
    }
}

// Obtener categorías
$categorias = $conn->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

// Obtener proyectos según categoría
$id_categoria_filter = $_GET['categoria'] ?? null;
$where = $id_categoria_filter ? "WHERE p.id_categoria = $id_categoria_filter" : "";

$proyectos = $conn->query("
    SELECT p.*, 
    c.nombre AS categoria,
    u.nombre AS nombre_contratista,
    u.apellido AS apellido_contratista,
    (SELECT COUNT(*) FROM publicacion WHERE id_proyectos = p.id_proyectos) AS publicaciones,
    (SELECT COUNT(*) FROM comentarios 
     JOIN publicacion ON comentarios.id_publicacion = publicacion.id_publicacion 
     WHERE publicacion.id_proyectos = p.id_proyectos) AS comentarios
    FROM proyecto p
    JOIN categorias c ON p.id_categoria = c.id_categoria
    JOIN usuarios u ON p.id_contratista = u.id_usuario
    ORDER BY p.fecha_publicacion DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Agrupar proyectos por categoría
$proyectosPorCategoria = [];
foreach ($proyectos as $pro) {
    $categoriaId = $pro['id_categoria'];
    if (!isset($proyectosPorCategoria[$categoriaId])) {
        $proyectosPorCategoria[$categoriaId] = [
            'nombre' => $pro['categoria'],
            'proyectos' => []
        ];
    }
    $proyectosPorCategoria[$categoriaId]['proyectos'][] = $pro;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Proyectos</title>
    <style>
        a:hover{
            color: #333;
            text-decoration: none;
        }
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background: #f8f9fa;
            --text-color: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--background);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .categories-container {
            display: grid;
            grid-gap: 20px;
        }

        .category-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .category-header {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-header h3 {
            margin: 0;
        }

        .toggle-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .category-content {
            padding: 20px;
            display: none;
        }

        .category-card.active .category-content {
            display: block;
        }

        .category-card.active .toggle-icon {
            transform: rotate(180deg);
        }

        .project-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .project-table th,
        .project-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .project-table th {
            background: var(--secondary-color);
            color: white;
        }

        .project-table tr:hover {
            background: #f5f5f5;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-section h2 {
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        input, select, button {
            padding: 8px 12px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background: var(--secondary-color);
            color: white;
            border: none;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        button:hover {
            opacity: 0.9;
        }

        .msg {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
        }

        .edit-form.active {
            display: block;
        } .edit-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 500px;
            width: 90%;
        }

        .edit-modal h3 {
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-actions {
            margin-top: 20px;
            text-align: right;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mensajes -->
        <?php if(isset($msg)): ?>
            <div class="msg success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="msg error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Formulario de creación -->
        <div class="form-section">
            <h2>Crear nuevo proyecto</h2>
            <form method="POST">
                <input type="text" name="titulo" placeholder="Título" required>
                
                <select name="id_categoria" required>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="id_contratista" required>
                    <?php foreach($contratistas as $c): ?>
                        <option value="<?= $c['id_usuario'] ?>">
                            <?= $c['nombre'] ?> <?= $c['apellido'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="presupuesto" step="0.01" placeholder="Presupuesto" required>
                
                <select name="etapa" required>
                    <option value="planificacion">Planificación</option>
                    <option value="ejecucion">Ejecución</option>
                    <option value="finalizado">Finalizado</option>
                </select>

                <button type="submit" name="create">Crear</button>
            </form>
        </div>

        <!-- Modal de Edición -->
        <div class="overlay" id="overlay"></div>
        <div class="edit-modal" id="editModal">
            <h3>Editar Proyecto</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="id_proyectos" id="editId">
                
                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" id="editTitulo" required>
                </div>

                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="id_categoria" id="editCategoria" required>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Contratista:</label>
                    <select name="id_contratista" id="editContratista" required>
                        <?php foreach($contratistas as $c): ?>
                            <option value="<?= $c['id_usuario'] ?>">
                                <?= $c['nombre'] ?> <?= $c['apellido'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Presupuesto:</label>
                    <input type="number" name="presupuesto" id="editPresupuesto" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Etapa:</label>
                    <select name="etapa" id="editEtapa" required>
                        <?php foreach(['planificacion', 'ejecucion', 'finalizado'] as $etapa): ?>
                            <option value="<?= $etapa ?>"><?= ucfirst($etapa) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" name="edit">Guardar</button>
                    <button type="button" onclick="closeEditForm()">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- Listado de proyectos por categoría -->
        <div class="categories-container">
            <?php foreach($proyectosPorCategoria as $catId => $categoria): ?>
                <div class="category-card">
                    <div class="category-header" onclick="toggleCategory(<?= $catId ?>)">
                        <h3><?= $categoria['nombre'] ?></h3>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="category-content" id="category-<?= $catId ?>">
                        <table class="project-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Contratista</th>
                                    <th>Presupuesto</th>
                                    <th>Etapa</th>
                                    <th>Publicaciones</th>
                                    <th>Comentarios</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categoria['proyectos'] as $pro): ?>
                                <tr data-id="<?= $pro['id_proyectos'] ?>"
                                    data-titulo="<?= htmlspecialchars($pro['titulo']) ?>"
                                    data-categoria="<?= $pro['id_categoria'] ?>"
                                    data-contratista="<?= $pro['id_contratista'] ?>"
                                    data-presupuesto="<?= $pro['presupuesto'] ?>"
                                    data-etapa="<?= $pro['etapa'] ?>">
                                    <td><?= $pro['titulo'] ?></td>
                                    <td><?= $pro['nombre_contratista'] ?> <?= $pro['apellido_contratista'] ?></td>
                                    <td>$<?= number_format($pro['presupuesto'], 2) ?></td>
                                    <td><?= ucfirst($pro['etapa']) ?></td>
                                    <td><?= $pro['publicaciones'] ?></td>
                                    <td><?= $pro['comentarios'] ?></td>
                                    <td>
                                        <button type="button" onclick="showEditForm(this)">Editar</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_proyectos" value="<?= $pro['id_proyectos'] ?>">
                                            <button type="submit" name="delete" <?= ($pro['comentarios'] > 0) ? 'disabled title="No se puede eliminar con comentarios"' : '' ?>>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Mostrar formulario de edición
        function showEditForm(button) {
            const row = button.closest('tr');
            const modal = document.getElementById('editModal');
            const overlay = document.getElementById('overlay');

            // Llenar datos del formulario
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('editTitulo').value = row.dataset.titulo;
            document.getElementById('editCategoria').value = row.dataset.categoria;
            document.getElementById('editContratista').value = row.dataset.contratista;
            document.getElementById('editPresupuesto').value = row.dataset.presupuesto;
            document.getElementById('editEtapa').value = row.dataset.etapa;

            // Mostrar elementos
            modal.style.display = 'block';
            overlay.style.display = 'block';
        }

        // Cerrar formulario
        function closeEditForm() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        // Toggle categorías
        function toggleCategory(catId) {
            const content = document.getElementById(`category-${catId}`);
            const card = content.parentElement;
            card.classList.toggle('active');
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        }

        // Cerrar al hacer click fuera del modal
        document.getElementById('overlay').addEventListener('click', closeEditForm);
    </script>
</html>