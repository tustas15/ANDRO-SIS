<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>
<!-- Sección para publicar -->
<?php if (in_array(session('perfil'), ['admin', 'contratista'])): ?>
    <!--  <div class="row">
        <div class="publish">
            <div class="row_title">
                <span><i class="fa fa-newspaper-o" aria-hidden="true"></i> Estado</span>
            </div>
            <form id="form-publicacion" method="POST" action="<?= site_url('newsfeed/crear') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id_proyecto" value="1">
                <div class="publish_textarea">
                    <img class="border-radius-image" src="<?= base_url(session('/imagne/imagen_perfil') ?? 'images/user.jpg') ?>" alt="Perfil de <?= esc(session('nombre')) ?>">
                    <textarea name="contenido" placeholder="¿Qué novedades hay?" required></textarea>
                </div>
                <div class="publish_icons">
                    <ul>
                        <li>
                            <input type="file" name="imagen" id="imagen-publicacion" hidden>
                            <label for="imagen-publicacion"><i class="fa fa-camera"></i></label>
                        </li>
                        <li><i class="fa fa-video-camera"></i></li>
                            <li><i class="fa fa-map-marker" aria-hidden="true"></i></li> 
                    </ul>
                    <button type="submit">Publicar</button>
                </div>
            </form>
        </div>
    </div> -->
<?php endif; ?>

<?php foreach ($publicaciones as $publicacion): ?>
    <div class="row border-radius">
        <div class="feed">
            <div class="feed_title">
                <img src="<?= base_url('images/usuarios/' . $publicacion['imagen_perfil'] ?? 'user.jpg') ?>" alt="<?= esc($publicacion['nombre']) ?>">
                <span>
                    <b><?= esc($publicacion['nombre']) ?> <?= esc($publicacion['apellido']) ?></b><br>
                    <b>PROYECTO: <?= esc($publicacion['proyecto_titulo']) ?></b><br>
                    <b><?= esc($publicacion['titulo']) ?></b>
                    <p><?= date('d M Y', strtotime($publicacion['fecha_publicacion'])) ?></p>
                </span>
            </div>

            <div class="feed_content">

                <?php if (!empty($publicacion['imagen'])): ?>
                    <div class="feed_content_image">
                        <p><?= esc($publicacion['descripcion']) ?></p>
                        <img src="<?= base_url('uploads/' . $publicacion['imagen']) ?>" alt="Publicación de <?= esc($publicacion['nombre']) ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="feed_footer">
                <ul class="feed_footer_left">
                    <li class="hover-orange <?= $publicacion['user_like'] ? 'selected-orange' : '' ?>">
                        <a href="#" class="like-btn" data-publicacion="<?= $publicacion['id_publicacion'] ?>">
                            <i class="fa fa-heart"></i>
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

<script>
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const publicacionId = this.dataset.publicacion;
        const heartIcon = this.querySelector('i');
        const countElement = this.querySelector('.count');
        const listItem = this.closest('li');
        
        fetch('<?= base_url('publicacion/toggleLike') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                id_publicacion: publicacionId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contador
                countElement.textContent = data.total_likes;
                
                // Toggle clase de like
                listItem.classList.toggle('selected-orange', data.user_like);
                
                // Animación opcional
                if (data.user_like) {
                    heartIcon.classList.add('animate-like');
                    setTimeout(() => heartIcon.classList.remove('animate-like'), 500);
                }
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

<?= $this->endSection() ?>