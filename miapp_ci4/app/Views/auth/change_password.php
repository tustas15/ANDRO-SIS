<?= $this->extend('layouts/profile_settings_layout') ?>

<?= $this->section('content') ?>
<div class="row border-radius">
    <center>
        <div class="settings shadow">
            <div class="settings_title">
                <h3>Cambiar Contraseña</h3>
                
                <!-- Mostrar todos los mensajes en un solo bloque -->
                <?php if (session('error') || session('success') || !empty($errors)): ?>
                    <div class="alert-messages">
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger"><?= session('error') ?></div>
                        <?php endif; ?>
                        
                        <?php if (session('success')): ?>
                            <div class="alert alert-success"><?= session('success') ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= is_array($error) ? implode(', ', $error) : $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="settings_content">
                <form action="<?= base_url('password/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="pi-input pi-input-lgg">
                        <span>Contraseña actual</span>
                        <input type="password" name="current_password" 
                               value="<?= old('current_password') ?>"
                               required>
                    </div>
                    
                    <div class="pi-input pi-input-lg">
                        <span>Nueva contraseña</span>
                        <input type="password" name="new_password" 
                               value="<?= old('new_password') ?>"
                               required>
                    </div>
                    
                    <div class="pi-input pi-input-lg">
                        <span>Confirmar nueva contraseña</span>
                        <input type="password" name="confirm_password" 
                               value="<?= old('confirm_password') ?>"
                               required>
                    </div>
                    <button type="submit" style="background-color: #57b846">
                        Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </center>
</div>
<?= $this->endSection() ?>