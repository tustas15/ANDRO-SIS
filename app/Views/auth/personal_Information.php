<?= $this->extend('layouts/profile_settings_layout') ?>

<?= $this->section('content') ?>

<div class="row border-radius">
    <center>
        <div class="settings shadow">
            <div class="settings_title">
                <h3>Información Personal</h3>
                <?php if (session('success')): ?>
                    <div class="alert alert-success"><?= session('success') ?></div>
                <?php endif; ?>
            </div>
            <div class="settings_content">
                <form action="<?= site_url('auth/perfil/actualizar') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Sección de imagen de perfil -->
                    <div class="pi-input pi-input-lgg text-center">
                        <div class="avatar-upload">
                            <img src="<?= base_url('images/usuarios/' . ($usuario['imagen_perfil'] ?? 'default.jpg')) ?>"
                                class="avatar-preview"
                                id="avatarPreview"
                                style="width: 100px; height: 100px;"> <!-- Opcional: agregar estilo inline -->
                            <label for="imagenInput" class="avatar-upload-label">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file"
                                id="imagenInput"
                                name="imagen_perfil">
                        </div>
                    </div>

                    <!-- Campos del formulario -->
                    <div class="pi-input pi-input-lg">
                        <span>Nombre</span>
                        <input type="text"
                            name="nombre"
                            value="<?= old('nombre', $usuario['nombre'] ?? '') ?>"
                            class="<?= session('errors.nombre') ? 'is-invalid' : '' ?>">
                        <?php if (session('errors.nombre')): ?>
                            <div class="invalid-feedback"><?= session('errors.nombre') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="pi-input pi-input-lg">
                        <span>Apellido</span>
                        <input type="text"
                            name="apellido"
                            value="<?= old('apellido', $usuario['apellido'] ?? '') ?>"
                            class="<?= session('errors.apellido') ? 'is-invalid' : '' ?>">
                        <?php if (session('errors.apellido')): ?>
                            <div class="invalid-feedback"><?= session('errors.apellido') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="pi-input pi-input-lgg">
                        <span>Correo Electrónico</span>
                        <input type="email"
                            name="correo"
                            value="<?= old('correo', $usuario['correo'] ?? '') ?>"
                            class="<?= session('errors.correo') ? 'is-invalid' : '' ?>">
                        <?php if (session('errors.correo')): ?>
                            <div class="invalid-feedback"><?= session('errors.correo') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Agrega más campos según tu modelo de datos -->

                    <button type="submit" style="background-color: #57b846">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </center>
</div>


<?= $this->endSection() ?>
<script>
    document.getElementById('imagenInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Redimensionamiento responsive
    window.addEventListener('resize', () => {
        if (cropper) {
            cropper.replace();
        }
    });
</script>
<style>
    /* Estilos para la carga de imagen */
    .avatar-upload {
        position: relative;
        margin: 0 auto 20px;
        width: 100px;
        height: 100px;
        margin-bottom: 15px;
    }

    .avatar-upload::before {
        content: "";
        display: block;
        padding-top: 100%;
    }

    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ddd;
        cursor: pointer;
    }

    .avatar-upload-label {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #007bff;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .avatar-upload-label:hover {
        background: #0056b3;
    }

    /* Estilos para campos inválidos */
    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
    }

    
</style>