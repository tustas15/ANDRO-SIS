<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <!-- Mensajes Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Información de la Categoría -->
     <center>
    <div class="row">
        <div class="publish">
            <h1><?= esc($categoria['nombre']) ?></h1>
        </div>
    </div>
    </center>

    <!-- Proyectos en esta Categoría -->
    
            <?php if (empty($proyecto)): ?>
                <p>No hay proyectos en esta categoría.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($proyecto as $proyect): ?> 
                        <div class="row border-radius">
                            <div class="feed">
                                <div class="feed_title">
                                    <a href="<?= base_url('proyecto/'.$proyect['id_proyectos']) ?>" class="selected-orange"><h3 ><?= esc($proyect['titulo']) ?></h3></a>
                                    </div>
                                    <div class="feed_content_image">
                                        <strong>Etapa:</strong> <?= esc($proyect['etapa']) ?><br>
                                        <strong>Presupuesto:</strong> $<?= number_format($proyect['presupuesto'], 2) ?>
                                    
                                    
                                    
                                    <?php 
                                    $contratista = array_filter($contratistas, function($c) use ($proyect) {
                                        return $c['id_usuario'] == $proyect['id_contratista'];
                                    });
                                    $contratista = reset($contratista);
                                    ?>
                                    
                                    <?php if ($contratista): ?>
                                        <div>
                                            <strong>Contratista:</strong> 
                                            <?= esc($contratista['nombre'] . ' ' . $contratista['apellido']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="progress mt-2" style="height: 20px;">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: <?= $proyect['porcentaje_total'] ?>%;"
                         aria-valuenow="<?= $proyect['porcentaje_total'] ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= number_format($proyect['porcentaje_total'], 2) ?>%
                    </div>
                </div>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
</div>
<style>
/* En tu CSS personalizado */
.progress {
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.5s ease;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #57b846;
    color:black;
}
</style>
<?= $this->endSection() ?>