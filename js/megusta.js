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
