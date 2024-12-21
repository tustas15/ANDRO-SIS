// main.js

// Cargar componentes y añadir listeners principales
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
                    if (file === "header.php") {
                        updateUserName();
                    }
                    loadCSS('../assets/css/style.css');
<<<<<<< HEAD
                    addMeGustaListeners();
                    addCommentListeners();
=======
>>>>>>> parent of 04c2192 (Reapply "Comentarios")
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    }

<<<<<<< HEAD
=======
    function loadCSS(href) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        link.href = href + "?v=" + new Date().getTime(); // Añade un parámetro de versión para evitar caché
        document.head.appendChild(link);
    }

>>>>>>> parent of 04c2192 (Reapply "Comentarios")
    function updateUserName() {
        fetch('../includes/session_data.php')
            .then(response => response.text())
            .then(data => {
                const userNameElement = document.querySelector('.nombreperfil');
                if (userNameElement) {
                    userNameElement.textContent = data;
                }
            })
            .catch(error => {
                console.error('Error al obtener el nombre del usuario:', error);
            });
    }

<<<<<<< HEAD
    function loadCSS(href) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        link.href = href + "?v=" + new Date().getTime();
        document.head.appendChild(link);
    }

    // Cargar componentes
    loadComponent("header", "../includes/header.php");
    loadComponent("main", "../includes/proyectos.php");
    loadComponent("footer", "../includes/footer.html");

    // Añadir listeners adicionales
    addMeGustaListeners();
    addCommentListeners();
=======
    loadComponent("header", "../includes/header.php");
    loadComponent("main", "../includes/proyectos.php");
    loadComponent("footer", "../includes/footer.html");
>>>>>>> parent of 04c2192 (Reapply "Comentarios")
});
