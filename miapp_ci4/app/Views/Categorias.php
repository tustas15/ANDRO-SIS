<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>
<div class="container">

    <!-- Mensajes Flash -->
    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row">
        <center>
        <div class="publish">
                <h2> Crear Categoría</h2>
            <div class="settings_content">
            <?= form_open('categorias/crear', ['class' => 'custom-form', 'id' => 'form-categoria']) ?>
            <?= csrf_field() ?>
            <div class="pi-input pi-input-lgg">
                <?= form_input([
                    'name' => 'nombre',
                    'id' => 'nombre',
                    'type' => 'text',
                    'placeholder' => 'Nombre de la categoría',
                    'class' => 'form-textarea',
                    'required' => true
                ]) ?>
                <?= csrf_field() ?>
            </div>
            <div >
                <ul>
                </ul>
                <?= form_button([
                    'type' => 'submit',
                    'content' => 'Crear Categoria',
                    'style' => 'background-color: #57b846'
                ]) ?>
            </div>
            <?= form_close() ?>
            </div>
        </div>
        </center>
    </div>



    <!-- Listado de categorías -->
    <div class="row border-radius">
        <?php foreach ($categorias as $categoria) : ?>
            <div class="feed">

                <div class="feed_content">
                    <div class="feed_content_image">
                        <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                            <thead>
                                <tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle;">
                                        <strong><?= esc($categoria['nombre']) ?>

                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: right; vertical-align: middle;">
                                        <?php if ($categoria['proyectos'] == 0) : ?>
                                            <?= form_open('categorias/eliminar', ['style' => 'display: inline-block;']) ?>
                                            <?= form_hidden('id_categoria', $categoria['id_categoria']) ?>
                                            <button type="submit"
                                                style="background:#bb2222; 
                                                    color: white; 
                                                    border: none; 
                                                    padding: 6px 12px;
                                                    border-radius: 4px;
                                                    cursor: pointer;"
                                                onclick="return confirm('¿Eliminar categoría permanentemente?')">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                            <?= form_close() ?>
                                        <?php else : ?>
                                            <span style="color: #6c757d; font-size: 0.9em;"></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="feed_title">
                    <span> - <?= $categoria['titulos_proyectos'] ?? '' ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>