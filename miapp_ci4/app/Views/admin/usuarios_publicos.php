<?= $this->extend('layouts/usuarios_layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <center>
    <div class="publish">
        <h2>Ciudadanos Registrados</h2>
    </div>
    </center>
    <div class="feed_content_image">
    <a href="<?= site_url('admin/usuarios/crear') ?>" class="selected-orange">
            <i class="fa fa-plus"></i> Nuevo Ciudadano
        </a>
    </div>
    <div class="row border-radius">
        <div class="friend">
            <?php foreach ($usuarios as $usuario): ?>
                <div class="friend_title">
                    <img src="<?= site_url('images/usuarios/' . ($usuario['imagen_perfil'] ?? 'user_default.jpg')) ?>" class="ciudadano-avatar">
                    <span>
                        <b><?= $usuario['nombre'] . ' ' . $usuario['apellido'] ?></b><br>
                        <p><?= $usuario['correo'] ?></p>
                    </span>
                    <button class="<?= $usuario['estado'] === 'activo' ? 'delete-friend' : 'add-friend' ?> toggle-status"
                        data-id="<?= $usuario['id_usuario'] ?>">
                        <?= $usuario['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<center>
    <a href="">
        <div class="loadmorefeed">
            <i class="fa fa-ellipsis-h"></i>
        </div>
    </a>
</center>

<?= $this->endSection() ?>

<style>
    .friend_title {
        display: flex;
        align-items: center;
        padding: 15px;
        margin: 10px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .friend_title img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }

    .add-friend {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: auto;
    }

    .delete-friend {
        background: #f44336;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: auto;
    }

    .loadmorefeed {
        background: #fff;
        padding: 10px 20px;
        border-radius: 50px;
        margin: 20px 0;
        display: inline-block;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>