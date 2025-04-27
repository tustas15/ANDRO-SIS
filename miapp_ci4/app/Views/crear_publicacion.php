<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Formulario de Creación -->
    <div class="row">
        <div class="feed">
            <div class="publish">
                <div class="settings_content">
                    <center>
                        <h2>Nueva Publicación</h2>
                    </center>
                    <form id="formCrearPublicacion" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Selección de Proyecto -->
                        <div class="pi-input pi-input-lg">
                            <select name="id_proyecto" class="pi-input pi-input-lgg" id="id_proyecto" required>
                                <option value="">Selecciona un proyecto</option>
                                <?php foreach ($proyectos as $proy):
                                    $disponible = 100 - $proy['total_peso'];
                                ?>
                                    <option value="<?= $proy['id_proyectos'] ?>"
                                        data-disponible="<?= $disponible ?>">
                                        <?= esc($proy['titulo']) ?>
                                        (Disponible: <?= number_format($disponible, 2) ?>%)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Campos del formulario -->
                        <div class="pi-input pi-input-lgg">
                            <input type="text" name="titulo" id="titulo" placeholder="Título" required>
                        </div>

                        <label for="contenido" class="feed_content_image">Contenido:</label>
                        <div class="publish_textarea">
                            <textarea name="contenido" placeholder="Contenido..." id="contenido" rows="3" required></textarea>
                        </div>

                        <label for="peso" class="feed_content_image">Porcentaje del Proyecto (%)</label>
                        <div class="pi-input pi-input-lgg">
                            <input type="number" name="peso" id="peso" step="0.01" min="0" max="100" required>
                        </div>

                        <!-- Sección de imagen -->
                        <div class="input-group feed_content_image">
                            <label for="imagen">Imagen:</label>
                            <div style="border: 2px dashed #e6ecf5; border-radius: 8px; padding: 20px; text-align: center; width: 86%;"
                                id="drop-zone">
                                <input type="file" name="imagen" id="imagen" style="display: none;" accept="image/*">
                                <label for="imagen" style="cursor: pointer; display: block; width: 100%; height: 100%;">
                                    <div id="preview-container">
                                        <p style="color: #888da8; font-size: 0.9em; margin: 0 auto; max-width: 300px;">
                                            Arrastra aquí o haz clic para subir una imagen
                                        </p>
                                    </div>
                                </label>
                                <div id="file-info" class="file-info"></div>
                            </div>
                        </div>
                        <center>
                            <button type="submit" style="background-color: #57b846">Publicar</button>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Proyectos -->

    <div class=" row ">
        <center>
            <div class="publish">
                <h2>Tus Proyectos</h2>
            </div>
        </center>
    </div>
    <?php foreach ($proyectos as $proyecto): ?>
        <div class="row">
            <div class="flex-grow-1">
                <h3 class="feed_title"><?= esc($proyecto['titulo']) ?> | <?= number_format($proyecto['total_peso'], 2) ?>% <br>

                    Presupuesto: $<?= number_format($proyecto['presupuesto'], 2) ?><br>
                    Estado: <?= ucfirst($proyecto['etapa']) ?>
                </h3>


                <!-- Listado de Publicaciones -->
                <div class="feed_content_image">
                    <h4>Publicaciones:</h4>
                    <?php foreach ($proyecto['publicaciones'] as $publicacion): ?>
                        <div class="feed_title">
                            <h4><?= esc($publicacion['titulo']) ?> | <?= number_format($publicacion['peso'], 2) ?>%</h4>
                        </div>
                    <?php endforeach; ?>
                    <!-- <small>
                                            <?= esc($proyecto['categoria_nombre']) ?>
                                        </small> -->

                </div>
            </div>
        </div>
    <?php endforeach; ?>




</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('drop-zone');
        const inputFile = document.getElementById('imagen');
        const previewContainer = document.getElementById('preview-container');
        const fileInfo = document.getElementById('file-info');

        // Manejo de Drag & Drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
                inputFile.files = files; // Asignar archivo al input
            }
        });

        // Manejo de selección manual
        inputFile.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        function handleFile(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Previsualización" 
                         class="img-preview">
                `;

                    fileInfo.innerHTML = `
                    <div>${file.name}</div>
                    <div>${(file.size / 1024).toFixed(2)} KB</div>
                `;
                };

                reader.readAsDataURL(file);
            } else {
                fileInfo.innerHTML = '<div class="text-danger">Solo se permiten archivos de imagen</div>';
                inputFile.value = '';
            }
        }

        // Resto del código de validación y envío del formulario
        const proyectoSelect = document.getElementById('id_proyecto');
        const pesoInput = document.getElementById('peso');

        function actualizarMaximo() {
            const selectedOption = proyectoSelect.options[proyectoSelect.selectedIndex];
            const disponible = parseFloat(selectedOption.dataset.disponible) || 0;
            pesoInput.max = disponible.toFixed(2);
            pesoInput.placeholder = `Máximo: ${disponible.toFixed(2)}%`;
        }

        proyectoSelect.addEventListener('change', actualizarMaximo);

        pesoInput.addEventListener('input', function() {
            const max = parseFloat(this.max) || 0;
            const value = parseFloat(this.value) || 0;

            if (value > max) {
                this.setCustomValidity(`Límite: ${max}%`);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('formCrearPublicacion').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('<?= site_url('contratista/crear') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Error al crear la publicación');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error en la conexión');
                });
        });
    });
</script>


<?= $this->endSection() ?>