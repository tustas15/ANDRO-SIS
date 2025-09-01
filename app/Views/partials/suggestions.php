<div class="suggestions_row">
    <!-- Sección de Contratistas -->
    <div class="row shadow">  
        <div class="row_title">  
            <span>CONTRATISTAS </span>  
        </div>  
        <?php foreach ($contratistas as $contratista): ?>  
            <div class="row_contain">  
                <img src="<?= base_url('images/usuarios/'.$contratista['imagen_perfil'] ?? 'user.jpg') ?>" alt="" />  
                <span><a href="<?= base_url('contratista/'.$contratista['id_usuario']) ?>"><b><?= htmlspecialchars(($contratista['nombre'] ?? '') . ' ' . ($contratista['apellido'] ?? '')) ?></b></a><br>  
                    <div>Proyectos a cargo: <?= $contratista['total_proyectos'] ?? 0 ?></div>  
                </span>  
            </div>  
        <?php endforeach; ?>  
    </div>  

    <!-- Sección de Proyectos -->
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
                                <a href="<?= base_url('proyecto/'.$proyecto['id_proyectos']) ?>"
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

    <!-- Sección de Categorías -->
    <div class="row shadow">
        <div class="row_title">
            <span>CATEGORÍAS</span>
        </div>
        <div class="row_contain">
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <div class="proyecto-item">
                        <span><b>
                                <a href="<?= base_url('categoria/'.$categoria['id_categoria']) ?>"
                                    class="selected-orange">
                                    <?= htmlspecialchars($categoria['nombre'] ?? 'Sin nombre') ?></b>
                            <br>
                            </a>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="proyecto-item">
                    <span>No hay categorías registradas</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<button onclick="topFunction()" id="myBtn" title="Go to top">
    <i class="fa fa-arrow-up"></i>
</button>