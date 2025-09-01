<?= $this->extend('layouts/usuarios_layout') ?>

<?= $this->section('content') ?>
<div class="row border-radius">
    <div class="friend">
    <center class="publish">
        <h2>Contactos</h2>
    </center>
        <div class="friend">
            <?php foreach ($contactos as $contacto): ?>
                <?php if ($contacto['id_usuario'] != session('id_usuario')): ?>
                <div class="friend_title">
                    
                        <img src="<?= site_url('images/usuarios/'.$contacto['imagen_perfil']) ?>" 
                             alt="<?= esc($contacto['nombre']) ?>">
                        <span>
                            <b>
                            <?= esc($contacto['nombre']) ?> 
                            <?= esc($contacto['apellido']) ?>
                            </b>
                            <p class="perfil-badge perfil-<?= $contacto['perfil'] ?>">
                                <?= ucfirst($contacto['perfil']) ?>
                            </p>
                        </span>
                        <a href="<?= site_url("conversacion/{$contacto['id_usuario']}") ?>"><button class="add-friend">Chat</button></a>
                    </a>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>