<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
// Manejar envío del formulario con AJAX
$('#form-publicacion').on('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    $.ajax({
        url: '<?= route_to('newsfeed.crear') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success) {
                location.reload(); // Recargar la página para ver la nueva publicación
            } else {
                alert('Error: ' + (response.error || 'Error desconocido'));
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Error en el servidor');
        }
    });
});
</script>
<script>
    
    // Manejar Me Gusta
    document.querySelectorAll('.form-megusta').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const publicacionId = form.dataset.publicacionId;

            try {
                const response = await fetch('acciones.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
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
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
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
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
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
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
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
    $(document).ready(function() {


        $("#messagesmodal").hover(function() {
            $(".modal-comments").toggle();
        });
        $(".modal-comments").hover(function() {
            $(".modal-comments").toggle();
        });



        $("#friendsmodal").hover(function() {
            $(".modal-friends").toggle();
        });
        $(".modal-friends").hover(function() {
            $(".modal-friends").toggle();
        });


        $("#profilemodal").hover(function() {
            $(".modal-profile").toggle();
        });
        $(".modal-profile").hover(function() {
            $(".modal-profile").toggle();
        });


        $("#navicon").click(function() {
            $(".mobilemenu").fadeIn();
        });
        $(".all").click(function() {
            $(".mobilemenu").fadeOut();
        });
    });
</script>
<script>
    window.onscroll = function() {
        scrollFunction()
    };

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