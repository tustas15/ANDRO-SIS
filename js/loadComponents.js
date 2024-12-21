document.addEventListener("DOMContentLoaded", function () {
    function loadComponent(selector, file) {
        const element = document.querySelector(selector);
        if (element) {
            fetch(file)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`Error al cargar ${file}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then((html) => {
                    element.innerHTML = html;
                    // Si cargamos el header, actualizamos el nombre del usuario
                    if (file === "header.php") {
                        updateUserName();
                    }
                    // Cargar el archivo CSS cuando cargamos los componentes
                    loadCSS('../assets/css/style.css');
                    
                    // Añadir event listeners para los botones de "Me gusta"
                    addMeGustaListeners();
                    
                    // Añadir event listeners para los formularios de comentarios
                    addCommentListeners();
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    }

    function addMeGustaListeners() {
        const meGustaForms = document.querySelectorAll('.form-me-gusta');
        meGustaForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const proyectoId = this.getAttribute('data-proyecto-id');
                const btnMeGusta = this.querySelector('.btn-me-gusta');

                fetch('megusta.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.total_me_gusta !== undefined) {
                        btnMeGusta.innerHTML = `${data.total_me_gusta} | Me gusta`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    }

    function addCommentListeners() {
        const commentForms = document.querySelectorAll('.form-comentario');
        const btnVerComentarios = document.querySelectorAll('.btn-ver-comentarios');
    
        commentForms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Deshabilitar el botón de submit temporalmente
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                
                const proyectoId = this.getAttribute('data-proyecto-id');
                const commentSection = this.closest('.proyecto').querySelector('.comentarios');
                const formData = new FormData(this);
    
                try {
                    const response = await fetch('comentar.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Limpiar el campo de comentario
                        this.querySelector('textarea').value = '';
                        
                        // Solo actualizar si los comentarios están visibles
                        if (commentSection.style.display === 'block') {
                            await fetchComments(proyectoId, commentSection);
                        }
                    } else {
                        console.error('Error al comentar:', data.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    // Reactivar el botón de submit
                    submitButton.disabled = false;
                }
            });
        });
    
        btnVerComentarios.forEach(btn => {
            btn.addEventListener('click', async function() {
                const commentSection = this.closest('.proyecto').querySelector('.comentarios');
                const proyectoId = this.closest('.proyecto').querySelector('.form-comentario').getAttribute('data-proyecto-id');
                
                // Mostrar la sección de comentarios y ocultar el botón
                commentSection.style.display = 'block';
                this.style.display = 'none';
                
                // Cargar los comentarios
                await fetchComments(proyectoId, commentSection);
            });
        });
    }
    
    async function fetchComments(proyectoId, commentSection) {
        try {
            const response = await fetch(`comentar.php?id_proyecto=${proyectoId}`);
            const html = await response.text();
            commentSection.innerHTML = html;
        } catch (error) {
            console.error('Error al cargar los comentarios:', error);
        }
    }
    

    function updateUserName() {
        fetch('../includes/session_data.php')
            .then(response => response.text())
            .then(data => {
                const userNameElement = document.querySelector('.nombreperfil');
                if (userNameElement) {
                    userNameElement.textContent = data;  // Actualizamos el nombre
                }
            })
            .catch(error => {
                console.error('Error al obtener el nombre del usuario:', error);
            });
    }

    function loadCSS(href) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        link.href = href + "?v=" + new Date().getTime(); // Añade un parámetro de versión para evitar caché
        document.head.appendChild(link);
    }

    loadComponent("header", "../includes/header.php");
    loadComponent("main", "../includes/proyectos.php");
    loadComponent("footer", "../includes/footer.html");
    addMeGustaListeners();
    addCommentListeners();
});