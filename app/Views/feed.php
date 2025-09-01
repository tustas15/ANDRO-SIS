<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>
<div class="row border-radius">
    <div class="feed">
        <div class="feed_title">
        <img src="<?= base_url('images/' . $publicacion['imagen_perfil'] ?? 'user.jpg') ?>" alt="<?= esc($publicacion['nombre']) ?>">
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
                <img src="<?= base_url('uploads/' . $publicacion['imagen']) ?>">
            </div>
            <?php endif; ?>
        </div>

        <div class="feed_footer">
            <ul class="feed_footer_left">
                <li class="hover-orange <?= $user_like ? 'selected-orange' : '' ?>">
                    <a href="javascript:void(0)" class="like-btn" data-publicacion="<?= $publicacion['id_publicacion'] ?>">
                        <i class="fe fe-corazon"></i>
                        <span class="count"><?= $total_likes ?></span>
                    </a>
                </li>
            </ul>
            <ul class="feed_footer_right">
                    <li>
                        <a href="<?= base_url('publicacion/'.$publicacion['id_publicacion']) ?>" class="hover-orange">
                            <i class="fa fa-comments-o"></i>
                            <?= count($comentarios) ?> comentarios
                        </a>
                    </li>
                </ul>
        </div>
    </div>
    <div class="publish"></div>
    <div class="feedcomments">
        
        <?php if (session('isLoggedIn')): ?>
        <form class="form-comentario" data-publicacion-id="<?= $publicacion['id_publicacion'] ?>">
                <div class="publish_textarea">
                    <?= csrf_field() ?>
                    <textarea name="comentario" placeholder="Escribe tu comentario..." style="resize: none;"></textarea>
                </div>
                <div class="publish_icons">
                    <ul></ul>
                    <button type="submit">Comentar</button>
                </div>
        </form>
        <?php endif; ?>

        <?php foreach ($comentarios as $comentario): ?>
            <li>
        <div class="feedcomments-user">
            <img src="<?= base_url('images/usuarios/'.$comentario['imagen_perfil'] ?? 'user.jpg') ?>">
            <span><b><?= esc($comentario['nombre']) ?> <?= esc($comentario['apellido']) ?></b><br><p><?= date('d M Y H:i', strtotime($comentario['fecha'])) ?></p></span>
        </div>
        <div class="feedcomments-comment">
                <p><?= esc($comentario['comentario']) ?></p>
        </div>
        </li>
        <?php endforeach; ?>

        
    </div>
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