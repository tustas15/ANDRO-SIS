function addCommentListeners() {
    const commentForms = document.querySelectorAll('.form-comentario');
    const commentSections = document.querySelectorAll('.comentarios');
    const btnVerComentarios = document.querySelectorAll('.btn-ver-comentarios');

    commentForms.forEach((form, index) => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const proyectoId = this.getAttribute('data-proyecto-id');
            const commentSection = commentSections[index];
            const commentForm = this;
            const textarea = commentForm.querySelector('textarea');
            const submitButton = commentForm.querySelector('button[type="submit"]');

            submitButton.disabled = true;

            fetch('comentar.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentForm.style.display = 'block';
                    commentSection.style.display = 'block';
                    fetchComments(proyectoId, commentSection);
                    textarea.value = '';
                } else {
                    console.error('Error al comentar:', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                submitButton.disabled = false;
            });
        });
    });

    btnVerComentarios.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            commentSections[index].style.display = 'block';
            btn.style.display = 'none';
            commentForms[index].style.display = 'block';
        });
    });
}

function fetchComments(proyectoId, commentSection) {
    fetch(`comentar.php?id_proyecto=${proyectoId}`)
        .then(response => response.text())
        .then(html => {
            commentSection.innerHTML = html + commentSection.innerHTML;
        })
        .catch(error => {
            console.error('Error al cargar los comentarios:', error);
        });
}
