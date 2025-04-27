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


        <center>
    <div class="row">
        <div class="publish">
            <h1>CONTRATISTA</h1>
        </div>
    </div>
    </center>

    <div class="row shadow">
    <div class="row_contain_profilephotospage">
        <ul>
        <li>
            <img 
                src="<?= base_url ('images/usuarios/'.$contratista['imagen_perfil']) ?>"
                alt="" />
                </li>
        </ul>
        <div class="feed_content_image">
            <span><?= $contratista['nombre'] ?> <?= $contratista['apellido'] ?><br><?= $contratista['correo'] ?>
            </span>
        </div>
        </div>
    </div>

    <!-- Listado de proyectos -->
  

    <?php foreach($proyectos as $proyecto): ?>
        <div class="feed">
        <div class="row">
            <div class="publish">
    <div class="feed_content_image bg-light cursor-pointer" 
         onclick="togglePublicaciones(<?= $proyecto['id_proyectos'] ?>)">
        <h3><?= $proyecto['titulo'] ?></h3>
        <small class="text-muted">
            <?= ucfirst($proyecto['etapa']) ?> | 
            Presupuesto: $<?= number_format($proyecto['presupuesto'], 2) ?>
        </small>
        
        <!-- Agregar esta barra de progreso -->
        <div class="progress mt-2" style="height: 20px;">
        <div class="progress-bar bg-success" 
     style="width: <?= $proyecto['porcentaje_total'] ?>%; background-color:#57b846; color:black !important;">
     <?= number_format($proyecto['porcentaje_total'], 2) ?>%
</div>
        </div>
    </div>
</div>


            <!-- Publicaciones (ocultas inicialmente) -->
            <div id="publicaciones-<?= $proyecto['id_proyectos'] ?>" class="card-body" style="display: none;">
                <?php if(!empty($proyecto['publicaciones'])): ?>
                    <?php foreach($proyecto['publicaciones'] as $publicacion): ?>
                        <div class="row feed">
                            <h3 class="feed_title"><?= $publicacion['titulo'] ?></h3>
                            <div class="feed_content_image">
                            <p><?= $publicacion['descripcion'] ?></p>
                            <?php if($publicacion['imagen']): ?>
                                <img src="<?= base_url('uploads/' . $publicacion['imagen']) ?>" 
                                     class="img-thumbnail" 
                                     style="max-width: 300px;">
                            <?php endif; ?>
                            </div>

                            <div class="feed_footer">
                <ul class="feed_footer_left">
                    <li class="hover-orange <?= $publicacion['total_likes'] ? 'selected-orange' : '' ?>">
                        <a href="#" class="like-btn" data-publicacion="<?= $publicacion['id_publicacion'] ?>">
                            <i class="fe fe-corazon"></i>
                            <span class="count"><?= $publicacion['total_likes'] ?></span>
                        </a>
                    </li>
                </ul>
                <ul class="feed_footer_right">
                    <li>
                        <a href="<?= base_url('publicacion/'.$publicacion['id_publicacion']) ?>" class="hover-orange">
                            <i class="fa fa-comments-o"></i>
                            <?= $publicacion['total_comentarios'] ?> comentarios
                        </a>
                    </li>
                </ul>
            </div>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">No hay publicaciones para este proyecto</div>
                <?php endif; ?>
            </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<style>
    /* Agregar al final de tu archivo CSS */
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
}
</style>


<script>
function togglePublicaciones(idProyecto) {
    const elemento = document.getElementById(`publicaciones-${idProyecto}`);
    elemento.style.display = elemento.style.display === 'none' ? 'block' : 'none';
}
</script>
<script>
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const publicacionId = this.dataset.publicacion;
        const formData = new FormData();
        formData.append('id_publicacion', publicacionId);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('publicacion/toggleLike') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Importante para que CI4 reconozca la solicitud AJAX
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            // Actualizar elementos del DOM
            const countElement = this.querySelector('.count');
            const heartIcon = this.querySelector('i');
            
            if (countElement) countElement.textContent = data.total_likes;
            if (heartIcon) {
                this.parentElement.classList.toggle('selected-orange', data.user_like);
                heartIcon.style.color = data.user_like ? '#ff6b00' : '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error. Por favor recarga la página.');
        });
    });
});
</script>

<?= $this->endSection() ?>