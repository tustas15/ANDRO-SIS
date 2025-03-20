$(document).ready(function() {
    // Likes
    $('.like-btn').click(function(e) {
        e.preventDefault();
        const btn = $(this);
        const idPublicacion = btn.data('publicacion');
        
        $.post('<?= route_to('publicaciones.like') ?>', {
            id_publicacion: idPublicacion,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(response) {
            btn.find('.count').text(response.total);
            btn.toggleClass('selected-orange');
        });
    });

    // Comentarios
    $('.form-comentario').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const idPublicacion = form.data('publicacion');
        const comentario = form.find('input').val();
        
        $.post('<?= route_to('publicaciones.comentar') ?>', {
            id_publicacion: idPublicacion,
            comentario: comentario,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(response) {
            if(response.success) {
                form.closest('.comentarios').prepend(`
                    <div class="comentario">
                        <img src="<?= base_url(session('imagen_perfil')) ?>" 
                             alt="${response.usuario.nombre}">
                        <div>
                            <b>${response.usuario.nombre}</b>
                            <p>${comentario}</p>
                            <small>Ahora</small>
                        </div>
                    </div>
                `);
                form.find('input').val('');
            }
        });
    });

    // Publicaciones
    $('#form-publicacion').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= route_to('publicaciones.crear') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    window.location.reload();
                }
            }
        });
    });
});