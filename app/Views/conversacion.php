<?= $this->extend('layouts/usuarios_layout') ?>

<?= $this->section('content') ?>

<style>
    .chat-header {
        max-width: 800px;
        margin: 20px auto 10px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .chat-header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .chat-header-info {
        flex-grow: 1;
    }
    
    .chat-header-name {
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 3px;
    }
    
    .chat-header-perfil {
        font-size: 0.9em;
        color: #7f8c8d;
    }

    .message-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
        border-radius: 10px;
        height: 60vh;
        overflow-y: auto;
    }

    .message {
        display: flex;
        align-items: start;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .message.own-message {
        flex-direction: row-reverse;
        background: #e3f2fd;
    }

    .message-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message-content {
        flex-grow: 1;
    }

    .message-sender {
        font-weight: bold;
        margin-bottom: 5px;
        color: #2c3e50;
    }

    .message p {
        margin: 0;
        color: #34495e;
        line-height: 1.5;
    }

    .archivo a {
        color: #57b846;
        text-decoration: none;
        display: inline-block;
        margin-top: 8px;
        padding: 5px 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .time {
        display: block;
        font-size: 0.8em;
        color: #7f8c8d;
        margin-top: 5px;
    }

    form {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    textarea {
        flex-grow: 1;
        padding: 10px;
        border: 1px solid #bdc3c7;
        border-radius: 5px;
        min-height: 50px;
        resize: vertical;
    }

    input[type="file"] {
        padding: 8px;
        border: 1px dashed #bdc3c7;
        border-radius: 5px;
    }

    button {
        padding: 10px 20px;
        background: #57b846;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background: #49993b;
    }

    .alert-error {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }

    .file-preview {
        margin-top: 10px;
        padding: 5px;
        background: #f8f9fa;
        border-radius: 5px;
    }
</style>


<div class="row border-radius">
    <div class="chat-header">
        <img src="<?= site_url('images/usuarios/'.$contacto['imagen_perfil']) ?>" 
             alt="<?= esc($contacto['nombre']) ?>">
        <div class="chat-header-info">
            <div class="chat-header-name">
                <?= esc($contacto['nombre']) ?> <?= esc($contacto['apellido']) ?>
            </div>
            <div class="chat-header-perfil">
                <?= ucfirst($contacto['perfil']) ?>
            </div>
        </div>
    </div>
    <div class="message-container">
        <?php foreach ($mensajes as $mensaje): ?>
            <div class="message <?= ($mensaje['id_remitente'] == session('id_usuario')) ? 'own-message' : '' ?>">
                <p><?= $mensaje['mensaje'] ?></p>
                <?php foreach ($mensaje['adjuntos'] as $adjunto): ?>
                    <div class="archivo">
                        <a href="<?= site_url("descargar/{$adjunto['id_archivo']}") ?>">
                            <i class="fa <?= $adjunto['tipo'] === 'documento' ? 'fa-file-pdf' : 'fa-file-image' ?>"></i>
                            <?= $adjunto['ruta_archivo'] ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <span class="time"><?= date('H:i', strtotime($mensaje['fecha'])) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="<?= site_url('enviarMensaje') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_conversacion" value="<?= $idConversacion ?>">
        <textarea name="mensaje" placeholder="Escribe tu mensaje..."></textarea>
        <input type="file" name="archivo">
        <button type="submit">Enviar</button>
    </form>
</div>
<script>
    // Mostrar nombre de archivo seleccionado
    document.getElementById('archivo').addEventListener('change', function(e) {
        const preview = document.getElementById('file-preview');
        if (this.files.length > 0) {
            preview.innerHTML = `<i class="fa fa-file"></i> ${this.files[0].name}`;
        } else {
            preview.innerHTML = '';
        }
    });
</script>


<?= $this->endSection() ?>