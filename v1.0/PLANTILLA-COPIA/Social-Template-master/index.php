<?php
session_start();
require_once '../../conection/conexion.php';
$id_usuario = $_SESSION['id_usuario'];
$query = "SELECT nombre, apellido, correo, perfil, imagen_perfil FROM Usuarios WHERE id_usuario = :id_usuario";
$stmt_usuario = $conn->prepare($query);
$stmt_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_usuario->execute();
$usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

// Configuración de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$publicacionesPorPagina = 5;
$offset = ($pagina - 1) * $publicacionesPorPagina;

try {
    // Obtener publicaciones
    $stmt = $conn->prepare("
        SELECT 
            p.titulo as proyecto_titulo,
            pub.*, u.imagen_perfil,
            u.nombre as contratista_nombre,
            u.apellido as contratista_apellido
        FROM publicacion pub
        INNER JOIN proyecto p ON pub.id_proyectos = p.id_proyectos
        INNER JOIN usuarios u ON p.id_contratista = u.id_usuario
        ORDER BY pub.fecha_publicacion DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $publicacionesPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt_proyectos = $conn->prepare("
        SELECT id_proyectos, titulo, fecha_publicacion 
        FROM proyecto 
        ORDER BY fecha_publicacion DESC
    ");
    $stmt_proyectos->execute();
    $proyectos = $stmt_proyectos->fetchAll(PDO::FETCH_ASSOC);

    $stmt=$conn->prepare("SELECT * from categorias order by id_categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    


    $stmt_contratistas = $conn->prepare("
        SELECT u.*,
    COUNT(DISTINCT p.id_proyectos) as total_proyectos,
    COUNT(DISTINCT pub.id_publicacion) as total_publicaciones,
    COUNT(DISTINCT m.id_megusta) as total_megustas
    FROM usuarios u
    LEFT JOIN proyecto p ON u.id_usuario = p.id_contratista
    LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
    LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
    WHERE perfil = 'contratista'
        ORDER BY nombre ASC
    ");
    $stmt_contratistas->execute();
    $contratistas = $stmt_contratistas->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total de páginas
    $totalPublicaciones = $conn->query("SELECT COUNT(*) FROM publicacion")->fetchColumn();
    $totalPaginas = ceil($totalPublicaciones / $publicacionesPorPagina);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Funciones auxiliares
function obtenerComentariosPublicacion($id_publicacion, $conn) {
    $stmt = $conn->prepare("
        SELECT c.*, u.nombre, u.apellido 
        FROM comentarios c
        INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.id_publicacion = :id_publicacion
        ORDER BY c.fecha DESC
    ");
    $stmt->execute([':id_publicacion' => $id_publicacion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerTotalMegustasPublicacion($id_publicacion, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM megusta WHERE id_publicacion = :id_publicacion");
    $stmt->execute([':id_publicacion' => $id_publicacion]);
    return $stmt->fetchColumn();
}

function obtenerTotalComentarios($id_publicacion, $conn){
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM comentarios Where id_publicacion=:id_publicacion");
    $stmt->execute([':id_publicacion' => $id_publicacion]);
    return $stmt->fetchColumn();
}

function usuarioDioMegustaPublicacion($id_usuario, $id_publicacion, $conn) {
    if (!$id_usuario) return false;
    $stmt = $conn->prepare("
        SELECT 1 FROM megusta 
        WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion
    ");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_publicacion' => $id_publicacion
    ]);
    return (bool)$stmt->fetchColumn();
}

// Obtener datos del contratista con estadísticas



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>GAD "LA ESPERANZA"</title>
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <!-- Icons FontAwesome 4.7.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"  type="text/css" />



</head>
<body>
    <div class="navbar">
        <div class="navbar_menuicon" id="navicon">
            <i class="fa fa-navicon"></i>
        </div>
        <div class="navbar_logo">
            <img src="images/logo_esperanza.png" alt="" />
        </div>
        <div class="navbar_page">
            <span>LA ESPERANZA</span>
        </div>
        <div class="navbar_search">
            <form method="" action="/">
                <input type="text" placeholder="Search people.." />
                <button><i class="fa fa-search"></i></button>
            </form>
        </div>
        <div class="navbar_icons">
            <ul>
                <li id="friendsmodal"><i class="fa fa-user-o"></i><span id="notification">5</span></li>
                <li id="messagesmodal"><i class="fa fa-comments-o"></i><span id="notification">2</span></li>
                <a href="" style="color:white"><li><i class="fa fa-globe"></i></li></a>
            </ul>
        </div>
        <div class="navbar_user" id="profilemodal" style="cursor:pointer">
            <img src="../../assets/images/<?php echo htmlspecialchars($usuario['imagen_perfil'], ENT_QUOTES, 'UTF-8'); ?>" alt="" />
            <span id="navbar_user_top"><?php
                echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8');
            ?>
            <br><p>User</p></span><i class="fa fa-angle-down"></i>
        </div>
    </div>

    <div class="all">

        <div class="rowfixed"></div>
        <div class="left_row">
            <div class="left_row_profile">
                <div class="left_row_profile">
                    <img id="profile_pic" src="../../assets/images/<?php echo htmlspecialchars($usuario['imagen_perfil'], ENT_QUOTES, 'UTF-8'); ?>" />
                    <span><?php
            echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); 
            ?><br><p>150k followers / 50 follow</p></span>
                </div>
            </div>
            <div class="rowmenu">
                <ul>
                    <li><a href="index.html" id="rowmenu-selected"><i class="fa fa-globe"></i>Newsfeed</a></li>
                    <li><a href="profile.html"><i class="fa fa-user"></i>Profile</a></li>
                    <li><a href="friends.html"><i class="fa fa-users"></i>Friends</a></li>
                    <li class="primarymenu"><i class="fa fa-compass"></i>Explore</li>
                    <ul>
                        <li style="border:none"><a href="#A">Activity</a></li>
                        <li style="border:none"><a href="#">Friends</a></li>
                        <li style="border:none"><a href="#">Groups</a></li>
                        <li style="border:none"><a href="#">Pages</a></li>
                        <li style="border:none"><a href="#">Saves</a></li>
                    </ul>
                    <li class="primarymenu"><i class="fa fa-user"></i>Rapid Access</li>
                    <ul>
                        <li style="border:none"><a href="#">Your-Page.html</a></li>
                        <li style="border:none"><a href="#">Your-Group.html</a></li>
                    </ul>
                </ul>
            </div>
        </div>

        <div class="right_row">
            
        <div class="row border-radius">
        <?php if (count($publicaciones) > 0): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="feed">
                    <div class="feed_title">
                        <img src="../../assets/images/<?php echo htmlspecialchars($publicacion['imagen_perfil']); ?>" alt="" />
                        <span><b><?= htmlspecialchars($publicacion['contratista_nombre'] . ' ' . $publicacion['contratista_apellido']) ?> </b><br> <b>PROYECTO:</b> <a href="feed.php?id_publicacion=<?= $publicacion['id_publicacion'] ?>"><?= htmlspecialchars($publicacion['proyecto_titulo']) ?></a><br><b><?= htmlspecialchars($publicacion['titulo']) ?></b><p><?= date('d M Y', strtotime($publicacion['fecha_publicacion'])) ?></p></span>
                    </div>
                    <div class="feed_content">
                        <div class="feed_content_image">
                            <p><?= nl2br(htmlspecialchars($publicacion['descripcion'])) ?></p>
                            <a href="feed.php?id_publicacion=<?= $publicacion['id_publicacion'] ?>"><img src="images/<?= htmlspecialchars($publicacion['imagen']) ?>" alt="" /></a>
                        </div>
                    </div>

                    
                    <div class="feed_footer">
                        
                        <ul class="feed_footer_left">
                        <form class="form-megusta" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                            <button type="submit"  class="hover-orange selected-orange" <?= (isset($_SESSION['id_usuario']) && usuarioDioMegustaPublicacion($_SESSION['id_usuario'], $publicacion['id_publicacion'], $conn)) ? 'activo' : '' ?>><i class="fa fa-heart"></i> <span class="total-megusta"><?= obtenerTotalMegustasPublicacion($publicacion['id_publicacion'], $conn) ?></span></button>
                            </form>
                        </ul>
                        <ul class="feed_footer_right">
                            <li>
                                <a href="feed.php?id_publicacion=<?= $publicacion['id_publicacion'] ?>" style="color:#515365;"><li class="hover-orange"><i class="fa fa-comments-o"></i> <span class="total-comentario"><?= obtenerTotalComentarios($publicacion['id_publicacion'], $conn) ?></span></li></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            

            <center>
                <a href=""><div class="loadmorefeed">
                    <i class="fa fa-ellipsis-h"></i>
                </div></a>
            </center>
        </div>

        <div class="suggestions_row">
            <div class="row shadow">
                <div class="row_title">
                    <span>CONTRATISTAS </span>
                </div>
                <?php foreach ($contratistas as $contratista): ?>
                <div class="row_contain">
                    <img src="../../assets/images/<?= htmlspecialchars($contratista['imagen_perfil']) ?>" alt="" />
                    <span><b><?= htmlspecialchars($contratista['nombre'] . ' ' . $contratista['apellido']) ?></b><br> 
                    <div >Proyectos acargo: <?= $contratista['total_proyectos'] ?></div></span>
                </div>
                <?php endforeach; ?>
                
            </div>

            <div class="row shadow">
                <div class="row_title">
                    <span>PROYECTOS</span>
                </div>
                <div class="row_contain">
                    <?php if (!empty($proyectos)): ?>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <?php 
                                $fecha = date('d M Y', strtotime($proyecto['fecha_publicacion']));
                                $titulo = htmlspecialchars($proyecto['titulo'], ENT_QUOTES, 'UTF-8');
                    ?>
                <div class="proyecto-item">
                <span><b>
                    <a href="detalle_proyecto.php?id=<?= $proyecto['id_proyectos'] ?>" 
                       class="selected-orange">
                        <?= $titulo ?></b>
                        <br>
                    </a>
                    creado el <?= $fecha ?>
                </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="proyecto-item">
            <span>No hay proyectos registrados</span>
        </div>
    <?php endif; ?>
</div>
            </div>


            <div class="row shadow">
                <div class="row_title">
                    <span>CATEGORIAS</span>
                </div>
                <div class="row_contain">
                <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <?php 
                                $titulo = htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8');
                    ?>
            <div class="proyecto-item">
                <span><b>
                    <a href="detalle_proyecto.php?id=<?= $proyecto['id_proyectos'] ?>" 
                       class="selected-orange">
                        <?= $titulo ?></b>
                        <br>
                    </a>
                </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="proyecto-item">
            <span>No hay proyectos registrados</span>
        </div>
    <?php endif; ?>
    
            </div>
        </div>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-arrow-up"></i></button>



    <!-- Modal Messages -->
    <div class="modal modal-comments">
        <div class="modal-icon-select"><i class="fa fa-sort-asc" aria-hidden="true"></i></div>
        <div class="modal-title">
            <span>CHAT / MESSAGES</span>
             <a href="messages.html"><i class="fa fa-ellipsis-h"></i></a>
        </div>
        <div class="modal-content">
            <ul>
                <li>
                    <a href="#">
                        <img src="images/user-7.jpg" alt="" />
                        <span><b>Diana Jameson</b><br>Hi James! It’s Diana, I just wanted to let you know that we have to reschedule...<p>4 hours ago</p></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="images/user-6.jpg" alt="" />
                        <span><b>Elaine Dreyfuss</b><br>We’ll have to check that at the office and see if the client is on board with...<p>Yesterday at 9:56pm</p></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="images/user-3.jpg" alt="" />
                        <span><b>Jake Parker</b><br>Great, I’ll see you tomorrow!.<p>4 hours ago</p></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Modal Friends -->
    <div class="modal modal-friends">
        <div class="modal-icon-select"><i class="fa fa-sort-asc" aria-hidden="true"></i></div>
        <div class="modal-title">
            <span>FRIEND REQUESTS</span>
             <a href="friends.html"><i class="fa fa-ellipsis-h"></i></a>
        </div>
        <div class="modal-content">
            <ul>
                <li>
                    <a href="#">
                        <img src="images/user-2.jpg" alt="" />
                        <span><b>Tony Stevens</b><br>4 Friends in Common</span>
                        <button class="modal-content-accept">Accept</button><button class="modal-content-decline">Decline</button>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="images/user-6.jpg" alt="" />
                        <span><b>Tamara Romanoff</b><br>2 Friends in Common</span>
                        <button class="modal-content-accept">Accept</button><button class="modal-content-decline">Decline</button>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="images/user-4.jpg" alt="" />
                        <span><b>Nicholas Grissom</b><br>1 Friend in Common</span>
                        <button class="modal-content-accept">Accept</button><button class="modal-content-decline">Decline</button>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Modal Profile -->
    <div class="modal modal-profile">
        <div class="modal-icon-select"><i class="fa fa-sort-asc" aria-hidden="true"></i></div>
        <div class="modal-title">
            <span>YOUR ACCOUNT</span>
             <a href="settings.html"><i class="fa fa-cogs"></i></a>
        </div>
        <div class="modal-content">
            <ul>
                <li>
                    <a href="settings.html">
                        <i class="fa fa-tasks" aria-hidden="true"></i>
                        <span><b>Profile Settings</b><br>Yours profile settings</span>
                    </a>
                </li>
                <li>
                    <a href="login.html">
                        <i class="fa fa-power-off" aria-hidden="true"></i>
                        <span><b>Log Out</b><br>Close your session</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- NavMobile -->
    <div class="mobilemenu">
        
        <div class="mobilemenu_profile">
            <img id="mobilemenu_portada" src="images/portada.jpg" />
            <div class="mobilemenu_profile">
                <img id="mobilemenu_profile_pic" src="images/user.jpg" /><br>
                <span>Jonh Hamstrong<br><p>150k followers / 50 follow</p></span>
            </div>
            <div class="mobilemenu_menu">
                <ul>
                    <li><a href="index.html" id="mobilemenu-selected"><i class="fa fa-globe"></i>Newsfeed</a></li>
                    <li><a href="profile.html"><i class="fa fa-user"></i>Profile</a></li>
                    <li><a href="friends.html"><i class="fa fa-users"></i>Friends</a></li>
                    <li><a href="messages.html"><i class="fa fa-comments-o"></i>messages</a></li>
                    <li class="primarymenu"><i class="fa fa-compass"></i>Explore</li>
                    <ul class="mobilemenu_child">
                        <li style="border:none"><a href="#"><i class="fa fa-globe"></i>Activity</a></li>
                        <li style="border:none"><a href="#"><i class="fa fa-file"></i>Friends</a></li>
                        <li style="border:none"><a href="#"><i class="fa fa-file"></i>Groups</a></li>
                        <li style="border:none"><a href="#"><i class="fa fa-file"></i>Pages</a></li>
                        <li style="border:none"><a href="#"><i class="fa fa-file"></i>Saves</a></li>
                    </ul>
                    <li class="primarymenu"><i class="fa fa-user"></i>Rapid Access</li>
                    <ul class="mobilemenu_child">
                        <li style="border:none"><a href="#"><i class="fa fa-star-o"></i>Your-Page.html</a></li>
                        <li style="border:none"><a href="#"><i class="fa fa-star-o"></i>Your-Group.html</a></li>
                    </ul>
                </ul>
                    <hr>
                <ul>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">FAQ's</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="login.html">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>

        // Manejar Me Gusta
        document.querySelectorAll('.form-megusta').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const publicacionId = form.dataset.publicacionId;
                
                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=megusta&id_publicacion=${publicacionId}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const boton = form.querySelector('button');
                        const contador = form.querySelector('.total-megusta');
                        contador.textContent = data.total;
                        boton.classList.toggle('activo', data.dio_megusta);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });


        // Manejar Comentarios
        document.querySelectorAll('.form-comentario').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const publicacionId = form.dataset.publicacionId;
                const texto = form.querySelector('textarea').value;
                
                try {
                    const response = await fetch('acciones.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=comentar&id_publicacion=${publicacionId}&comentario=${encodeURIComponent(texto)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const boton = form.querySelector('button');
                        const contador = form.querySelector('.total-comentarios');
                        contador.textContent = data.total;
                        boton.classList.toggle('activo', data.comentario);
                    }

                    if (data.success) {
                        const comentariosDiv = document.getElementById(`comentarios-${publicacionId}`);
                        comentariosDiv.querySelectorAll('.comentario').forEach(c => c.remove());
                        



                        // Dentro de la función de manejo de comentarios (proyectos.php)
data.comentarios.forEach(comentario => {
    const div = document.createElement('div');
    div.className = 'comentario';
    div.innerHTML = `
        <strong>${comentario.nombre} ${comentario.apellido}</strong>
        <p>${comentario.comentario}</p>
        <small>${new Date(comentario.fecha).toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        })}</small>
        ${comentario.pertenece_al_usuario ? `
        <div class="acciones-comentario">
            <i class="fas fa-edit" 
               onclick="editarComentario(${comentario.id_comentario})" 
               title="Editar comentario"
               aria-label="Editar comentario"></i>
            <i class="fas fa-trash-alt" 
               onclick="eliminarComentario(${comentario.id_comentario})" 
               title="Eliminar comentario"
               aria-label="Eliminar comentario"></i>
        </div>
        ` : ''}
    `;
    comentariosDiv.insertBefore(div, form);
});
                        
                        form.querySelector('textarea').value = '';
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // Función eliminarComentario actualizada
        async function eliminarComentario(idComentario) {
    try {
        const response = await fetch('acciones.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=eliminar_comentario&id_comentario=${idComentario}`
        });
        const data = await response.json();
        if (data.success) {
            document.querySelector(`.fa-trash-alt[onclick*="${idComentario}"]`)
                    .closest('.comentario').remove();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function editarComentario(idComentario) {
    const comentarioElement = document.querySelector(`.fa-edit[onclick*="${idComentario}"]`)
                               .closest('.comentario');
    const textoOriginal = comentarioElement.querySelector('p').textContent;
    
    const nuevoTexto = prompt('Edita tu comentario:', textoOriginal);
    if (nuevoTexto !== null && nuevoTexto.trim() !== textoOriginal.trim()) {
        try {
            const response = await fetch('acciones.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=editar_comentario&id_comentario=${idComentario}&comentario=${encodeURIComponent(nuevoTexto)}`
            });
            const data = await response.json();
            if (data.success) {
                comentarioElement.querySelector('p').textContent = nuevoTexto;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}



    // Modals
    $(document).ready(function(){


        $("#messagesmodal").hover(function(){
            $(".modal-comments").toggle();
        });
        $(".modal-comments").hover(function(){
            $(".modal-comments").toggle();
        });



        $("#friendsmodal").hover(function(){
            $(".modal-friends").toggle();
        });
        $(".modal-friends").hover(function(){
            $(".modal-friends").toggle();
        });


        $("#profilemodal").hover(function(){
            $(".modal-profile").toggle();
        });
        $(".modal-profile").hover(function(){
            $(".modal-profile").toggle();
        });


        $("#navicon").click(function(){
            $(".mobilemenu").fadeIn();
        });
        $(".all").click(function(){
            $(".mobilemenu").fadeOut();
        });
    });
    </script>
    <script>
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>