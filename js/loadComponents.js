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
          })
          .catch((error) => {
            console.error(error);
          });
      }
    }
  
    loadComponent("header", "header.html");
    loadComponent("footer", "footer.html");
  });
  