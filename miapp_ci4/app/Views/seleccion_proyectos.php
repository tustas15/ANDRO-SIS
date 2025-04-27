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
            <h1><?= esc($proyecto['titulo'] ?? 'Proyecto sin título') ?></h1>
            <!-- Línea agregada: Contratista -->
            <p class="lead text-muted">
                Contratista: <?= esc($proyecto['contratista_nombre'] ?? '') ?> <?= esc($proyecto['contratista_apellido'] ?? '') ?>
            </p>
        </div>
    </div>
</center>

    <!-- Listado de publicaciones -->
    
        <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="row border-radius">
                <div class="feed">
                    <div class="feed_title">
                        <h3><?= esc($publicacion['titulo']) ?></h3>
                    </div>
                        <div class="feed_content_image">
                        <p><?= esc($publicacion['descripcion']) ?></p>
                        <?php if ($publicacion['imagen']): ?>
                            <img src="<?= base_url('uploads/' . $publicacion['imagen']) ?>" class="img-fluid">
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
                        </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No hay publicaciones para este proyecto</div>
        <?php endif; ?>
    
    </div>
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