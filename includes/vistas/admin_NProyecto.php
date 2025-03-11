<?php
require_once '../conection/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_proyecto'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO proyecto (id_contratista, titulo, etapa, id_categoria, presupuesto) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_contratista'],
            $_POST['titulo'],
            $_POST['etapa'],
            $_POST['id_categoria'],
            $_POST['presupuesto']
        ]);
        $_SESSION['mensaje'] = "Proyecto creado exitosamente!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al crear proyecto: " . $e->getMessage();
    }
}

if (isset($_GET['eliminar'])) {
    $id_proyecto = filter_var($_GET['eliminar'], FILTER_VALIDATE_INT);

    if (!$id_proyecto) {
        $_SESSION['error'] = "ID inválido";
        header("Location: index.php?view=admin_NProyecto");
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Eliminar comentarios y me gusta
        $stmt = $conn->prepare("DELETE c, m 
                               FROM comentarios c
                               JOIN megusta m USING(id_publicacion)
                               WHERE c.id_publicacion IN (
                                   SELECT id_publicacion 
                                   FROM publicacion 
                                   WHERE id_proyectos = ?
                               )");
        $stmt->execute([$id_proyecto]);

        // 2. Eliminar publicaciones
        $stmt = $conn->prepare("DELETE FROM publicacion WHERE id_proyectos = ?");
        $stmt->execute([$id_proyecto]);

        // 3. Eliminar proyecto
        $stmt = $conn->prepare("DELETE FROM proyecto WHERE id_proyectos = ?");
        $stmt->execute([$id_proyecto]);

        $conn->commit();
        $_SESSION['mensaje'] = "✅ Proyecto eliminado";

    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "❌ Error: " . $e->getMessage();
    }

    header("Location: index.php?view=admin_NProyecto");
    exit;
}

// Actualizar etapa del proyecto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_etapa'])) {
    try {
        $stmt = $conn->prepare("UPDATE proyecto SET etapa = ? WHERE id_proyectos = ?");
        $stmt->execute([$_POST['etapa'], $_POST['id_proyecto']]);
        $_SESSION['mensaje'] = "Etapa actualizada correctamente!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar etapa: " . $e->getMessage();
    }
}

// Obtener categorías con proyectos
try {
    // Categorías con proyectos
    $categorias = $conn->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categorias as &$categoria) {
        $stmt = $conn->prepare("SELECT p.*, u.nombre as contratista 
                               FROM proyecto p
                               JOIN usuarios u ON p.id_contratista = u.id_usuario
                               WHERE id_categoria = ?");
        $stmt->execute([$categoria['id_categoria']]);
        $categoria['proyectos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contratistas para formulario
    $contratistas = $conn->query("SELECT * FROM usuarios WHERE perfil = 'contratista'")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Proyectos</title>
    <style>
        a:hover{
            text-decoration: none;
            color:#2c3e50;
        }
        :root {
            --primary-color:rgb(255, 255, 255);
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #ecf0f1;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #ecf0f1;
            color: var(--text-dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 2rem;
            background: var(--primary-color);
            color: white;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 15px;
            margin: 1rem 0;
            border-radius: 5px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background: var(--light-bg);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .card-body {
            padding: 1.5rem;
        }

        .project-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .project-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            transition: transform 0.2s;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 700;
        }

        .badge-planificacion { background: #f1c40f; color: black; }
        .badge-ejecucion { background: #3498db; color: white; }
        .badge-finalizado { background: #27ae60; color: white; }

        .form-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .form-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .actions-container {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .stage-form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .price-tag {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--success-color);
        }
    </style>
</head>
<body>
    <div class="container">

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Formulario de creación -->
        <section class="form-section">
            <h2>➕ Nuevo Proyecto</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Título del Proyecto</label>
                        <input type="text" name="titulo" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Contratista Responsable</label>
                        <select name="id_contratista" required>
                            <?php foreach ($contratistas as $c): ?>
                                <option value="<?= $c['id_usuario'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Etapa Inicial</label>
                        <select name="etapa" required>
                            <option value="planificacion">Planificación</option>
                            <option value="ejecucion">Ejecución</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="id_categoria" required>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Presupuesto (USD)</label>
                        <input type="number" step="0.01" name="presupuesto" required>
                    </div>
                </div>
                <button type="submit" name="crear_proyecto" class="btn btn-primary">Crear Proyecto</button>
            </form>
        </section>

        <!-- Listado de proyectos -->
        <section>
            <h2>📂 Proyectos por Categoría</h2>
            <?php foreach ($categorias as $categoria): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?= htmlspecialchars($categoria['nombre']) ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="project-grid">
                            <?php foreach ($categoria['proyectos'] as $proyecto): ?>
                                <div class="project-card">
                                    <div class="badge badge-<?= $proyecto['etapa'] ?>">
                                        <?= ucfirst($proyecto['etapa']) ?>
                                    </div>
                                    <h4><?= htmlspecialchars($proyecto['titulo']) ?></h4>
                                    <p class="price-tag">$<?= number_format($proyecto['presupuesto'], 2) ?></p>
                                    <p>👷 Contratista: <?= htmlspecialchars($proyecto['contratista']) ?></p>
                                    <p>📅 <?= date('d/m/Y', strtotime($proyecto['fecha_publicacion'])) ?></p>
                                    
                                    <div class="actions-container">
                                        <form method="POST" class="stage-form">
                                            <input type="hidden" name="id_proyecto" value="<?= $proyecto['id_proyectos'] ?>">
                                            <select name="etapa" class="btn">
                                                <option value="planificacion" <?= $proyecto['etapa'] === 'planificacion' ? 'selected' : '' ?>>Planificación</option>
                                                <option value="ejecucion" <?= $proyecto['etapa'] === 'ejecucion' ? 'selected' : '' ?>>Ejecución</option>
                                                <option value="finalizado" <?= $proyecto['etapa'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                            </select>
                                            <button type="submit" name="cambiar_etapa" class="btn btn-primary">Actualizar</button>
                                        </form>
                                        
                                        <a href="index.php?view=admin_NProyecto&eliminar=<?= $proyecto['id_proyectos'] ?>" 
   onclick="return confirm('¿Estás seguro?')" 
   class="btn btn-danger">
   Eliminar
</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</body>
</html>