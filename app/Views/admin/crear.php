<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>

<div class="row border-radius">
    <div class="feed">
        <div class="publish" style="border-top: 5px solid #57b846;">
            <center>
                <h2> Crear Usuario</h2>
            </center>

            <?php if (session('success')): ?>
                <div class="alert alert-success" style="margin: 15px;"><?= session('success') ?></div>
            <?php endif; ?>

            <?php if (session('errors')): ?>
                <div class="alert alert-danger" style="margin: 15px;">
                    <ul>
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <form method="post" action="<?= base_url('admin/usuarios/guardar') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="publish_textarea" style="flex-direction: column; padding: 20px;">
                    <!-- Sección de Datos Básicos -->
                    <div style="width: 100%; margin-bottom: 15px;">
                        <div class="input-group">
                            <label style="display: block; margin-bottom: 5px; color: #515365;">Nombre</label>
                            <input type="text" name="nombre" required
                                class="form-input" style="width: 90%;">
                        </div>

                        <div class="input-group" style="margin-top: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #515365;">Apellido</label>
                            <input type="text" name="apellido" required
                                class="form-input" style="width: 90%;">
                        </div>

                        <div class="input-group" style="margin-top: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #515365;">Cédula o Ruc</label>
                            <input type="text" name="cedula" required
                                class="form-input" style="width: 90%;"
                                value="<?= old('cedula') ?>">
                        </div>
                        <div class="input-group" style="margin-top: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #515365;">Correo Electrónico</label>
                            <input type="email" name="correo" required
                                class="form-input" style="width: 90%;">
                        </div>

                        <div class="input-group" style="margin-top: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #515365;">Contraseña</label>
                            <input type="password" name="contrasena" required
                                class="form-input" style="width: 90%;">
                        </div>
                    </div>

                    <!-- Sección de Opciones -->
                    <div style="width: 100%;margin-bottom: 15px; border-top: 1px solid #e6ecf5; padding-top: 20px;">
                        <div class="input-row" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
                            <!-- Sección Perfil -->
                            <div class="pi-input pi-input-lg">
                                <label>Perfil</label>
                                <select name="perfil" required class="form-select" style="width: 100%;">
                                    <?php foreach ($perfiles as $perfil): ?>
                                        <option value="<?= $perfil ?>"><?= ucfirst($perfil) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Sección Estado -->
                            <div class="pi-input pi-input-lg">
                                <label>Estado</label>
                                <select name="estado" required class="form-select" style="width: 100%;">
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado ?>"><?= ucfirst($estado) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Sección Imagen -->
                        <div class="input-group">
                            <label style="display: block; margin-bottom: 10px; color: #515365;">Imagen de Perfil</label>
                            <div style="border: 2px dashed #e6ecf5; border-radius: 8px; padding: 20px; text-align: center; width: 86%;">
                                <input type="file" name="imagen_perfil"
                                    id="imageUpload"
                                    style="display: none;"
                                    accept="image/*">
                                <label for="imageUpload" style="cursor: pointer; display: block;">
                                    <img src="<?= base_url('images/default.jpg') ?>"
                                        id="imagePreview"
                                        style="width: 106px; height: 106px; border-radius: 50%; object-fit: cover; border: 3px solid #57b846; margin: 0 auto 10px;">
                                    <p style="color: #888da8; font-size: 0.9em; margin: 0 auto; max-width: 300px;">
                                        Haz clic para subir una imagen<br>
                                        (Tamaño requerido: 106x106px)
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="publish_icons" style="justify-content: center; padding: 20px; border-top: 1px solid #e6ecf5;">
                        <button type="submit"
                            style="background: #57b846; color: white; border: none; padding: 12px 30px; border-radius: 4px; cursor: pointer; transition: background 0.3s;">
                            <i class="fa fa-save"></i> Guardar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const [file] = e.target.files
        if (file) {
            const reader = new FileReader()
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result

                // Forzar redimensionado en vista previa
                const img = document.getElementById('imagePreview')
                img.style.width = '106px'
                img.style.height = '106px'
            }
            reader.readAsDataURL(file)
        }
    })
</script>

<style>
    .form-input {
        padding: 10px;
        border: 1px solid #e6ecf5;
        border-radius: 4px;
        transition: border-color 0.3s;
    }

    .form-input:focus {
        border-color: #57b846;
        outline: none;
    }

    .form-select {
        padding: 10px;
        border: 1px solid #e6ecf5;
        border-radius: 4px;
        background: white;
        transition: border-color 0.3s;
    }

    .form-select:focus {
        border-color: #57b846;
        outline: none;
    }

    @media screen and (max-width: 768px) {
        .input-row {
            flex-direction: column;
        }

        .form-input,
        .form-select {
            width: 100% !important;
        }

        #imagePreview {
            width: 80px !important;
            height: 80px !important;
        }
    }
</style>

<?= $this->endSection() ?>