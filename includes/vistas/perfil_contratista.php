<?php
require '../conection/conexion.php';

// Verificar ID de contratista
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id_contratista = filter_var($_GET['id'], FILTER_VALIDATE_INT);

// Obtener datos del contratista con estadísticas
$stmt = $conn->prepare("
    SELECT u.*,
    COUNT(DISTINCT p.id_proyectos) as total_proyectos,
    COUNT(DISTINCT pub.id_publicacion) as total_publicaciones,
    COUNT(DISTINCT m.id_megusta) as total_megustas
    FROM usuarios u
    LEFT JOIN proyecto p ON u.id_usuario = p.id_contratista
    LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
    LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
    WHERE u.id_usuario = :id AND u.perfil = 'contratista'
    GROUP BY u.id_usuario
");
$stmt->execute([':id' => $id_contratista]);
$contratista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contratista) die("Contratista no encontrado");

// Obtener proyectos del contratista
$stmt = $conn->prepare("
    SELECT p.*, c.nombre as categoria_nombre
    FROM proyecto p
    JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.id_contratista = :id
    ORDER BY p.fecha_publicacion DESC
");
$stmt->execute([':id' => $id_contratista]);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para obtener publicaciones de un proyecto
function obtenerPublicacionesProyecto($id_proyecto, $conn) {
    $stmt = $conn->prepare("
        SELECT pub.*, 
        COUNT(DISTINCT m.id_megusta) as total_megustas,
        COUNT(DISTINCT c.id_comentario) as total_comentarios
        FROM publicacion pub
        LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
        LEFT JOIN comentarios c ON pub.id_publicacion = c.id_publicacion
        WHERE pub.id_proyectos = :id
        GROUP BY pub.id_publicacion
        ORDER BY pub.fecha_publicacion DESC
    ");
    $stmt->execute([':id' => $id_proyecto]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($contratista['nombre']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .perfil-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .perfil-header {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 30px;
            align-items: center;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .perfil-imagen {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #3498db;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .estadistica-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .proyecto-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .proyecto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .publicaciones-container {
            display: none;
            margin-top: 15px;
            padding-left: 20px;
            border-left: 3px solid #3498db;
        }

        .publicacion-item {
            margin: 10px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            position: relative;
        }

        .proyecto-activo {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .badge-etapa {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .planificacion { background: #fff3cd; color: #856404; }
        .ejecucion { background: #d4edda; color: #155724; }
        .finalizado { background: #d1ecf1; color: #0c5460; }

        .interacciones {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-interaccion {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-megusta {
            background: #e8f4ff;
            color: #3498db;
        }

        .btn-megusta.activo {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="perfil-container">
        <!-- Encabezado del perfil -->
        <div class="perfil-header">
            <img src="../uploads/<?= htmlspecialchars($contratista['imagen_perfil']) ?>" 
                 class="perfil-imagen" 
                 alt="Imagen de perfil">
            
            <div>
                <h1><?= htmlspecialchars($contratista['nombre'] . ' ' . $contratista['apellido']) ?></h1>
                <p class="correo"><?= htmlspecialchars($contratista['correo']) ?></p>
                
                <div class="estadisticas">
                    <div class="estadistica-item">
                        <div class="valor"><?= $contratista['total_proyectos'] ?></div>
                        <div class="label">Proyectos</div>
                    </div>
                    <div class="estadistica-item">
                        <div class="valor"><?= $contratista['total_publicaciones'] ?></div>
                        <div class="label">Publicaciones</div>
                    </div>
                    <div class="estadistica-item">
                        <div class="valor"><?= $contratista['total_megustas'] ?></div>
                        <div class="label">Me gustas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de proyectos -->
        <h2>Proyectos realizados</h2>
        <div class="proyectos-lista">
            <?php foreach ($proyectos as $proyecto): ?>
                <div class="proyecto-card" onclick="window.location.href='index.php?view=proyecto_publicaciones&id_proyecto=<?= $proyecto['id_proyectos'] ?>'">
                    <div class="proyecto-header">
                        <div>
                            <h3><?= htmlspecialchars($proyecto['titulo']) ?></h3>
                            <p>Categoría: <?= htmlspecialchars($proyecto['categoria_nombre']) ?></p>
                        </div>
                        <span class="badge-etapa <?= $proyecto['etapa'] ?>">
                            <?= ucfirst($proyecto['etapa']) ?>
                        </span>
                    </div>
                    <p>Presupuesto: $<?= number_format($proyecto['presupuesto'], 2) ?></p>
                    <small>Publicado el <?= date('d/m/Y', strtotime($proyecto['fecha_publicacion'])) ?></small>

                    
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function togglePublicaciones(idProyecto) {
            const contenedor = document.getElementById(`pub-${idProyecto}`);
            const proyectoCard = contenedor.parentElement;
            
            // Cerrar otros proyectos
            document.querySelectorAll('.proyecto-card').forEach(card => {
                if (card !== proyectoCard) {
                    card.classList.remove('proyecto-activo');
                    card.querySelector('.publicaciones-container').style.display = 'none';
                }
            });

            // Toggle estado del proyecto clickeado
            proyectoCard.classList.toggle('proyecto-activo');
            contenedor.style.display = proyectoCard.classList.contains('proyecto-activo') ? 'block' : 'none';
        }

        // Manejo de Me Gusta
        document.querySelectorAll('.form-megusta').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const publicacionId = form.dataset.publicacionId;
                const boton = form.querySelector('button');
                const contador = form.querySelector('.contador');

                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=megusta&id_publicacion=${publicacionId}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        contador.textContent = data.total;
                        boton.classList.toggle('activo', data.dio_megusta);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>
</html>