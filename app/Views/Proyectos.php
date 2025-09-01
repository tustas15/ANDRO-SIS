<?= $this->extend('layouts/newsfeed_layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <!-- Mensajes Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Formulario de creación -->
    <div class="row">
        <center>
            <div class="publish">
                <h2>Crear proyecto</h2>
            </div>
            <div class="settings_content">
                <?= form_open('proyectos/crear') ?>
                <div class="pi-input pi-input-lgg">
                    <input type="text" name="titulo" placeholder="Título" required>
                </div>

                <div class="pi-input pi-input-lg">
                    <select name="id_categoria" class="pi-input pi-input-lg" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>"><?= esc($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pi-input pi-input-lg">
                    <select name="id_contratista" class="pi-input pi-input-lg" required>
                        <?php foreach ($contratistas as $c): ?>
                            <option value="<?= $c['id_usuario'] ?>">
                                <?= esc($c['nombre']) ?> <?= esc($c['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pi-input pi-input-lg">
                    <input type="number" name="presupuesto" step="0.01" placeholder="Presupuesto" required>
                </div>

                <div class="pi-input pi-input-lg">
                    <select name="etapa" class="pi-input pi-input-lg" required>
                        <?php foreach (['planificacion', 'ejecucion', 'finalizado'] as $etapa): ?>
                            <option value="<?= $etapa ?>"><?= ucfirst(esc($etapa)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pi-input pi-input-lg">
                <label>Fecha de Inicio:</label>
                <input 
                    type="date" 
                    name="fecha_publicacion" 
                    class="pi-input pi-input-lg"
                    value="<?= date('Y-m-d') ?>"
                    required>
            </div>

            <div class="pi-input pi-input-lg">
                <label>Fecha Finalización</label>
                <input 
                    type="date" 
                    name="fecha_fin" 
                    class="pi-input pi-input-lg"
                    min="<?= date('Y-m-d') ?>"
                >
            </div>

                <button type="submit" style="background-color: #57b846">Crear</button>
                <?= form_close() ?>
            </div>
        </center>
    </div>

    <!-- Listado de proyectos -->
    <div class="categories-container">
        <?php foreach ($proyectosPorCategoria as $catId => $categoria): ?>
            <div class="card shadow mb-4">
                <div class="collapse" id="category-<?= $catId ?>">
                    <div class="card-body">
                        <div class="row border-radius">
                            <?php foreach ($categoria['proyectos'] as $pro): ?>
                                <div class="feed mb-4"
                                    data-id="<?= $pro['id_proyectos'] ?>"
                                    data-titulo="<?= esc($pro['titulo'], 'attr') ?>"
                                    data-categoria="<?= $pro['id_categoria'] ?>"
                                    data-contratista="<?= $pro['id_contratista'] ?>"
                                    data-presupuesto="<?= $pro['presupuesto'] ?>"
                                    data-etapa="<?= $pro['etapa'] ?>"
                                    data-fecha-publicacion="<?= date('Y-m-d', strtotime($pro['fecha_publicacion'])) ?>"
                                    data-fecha-fin="<?= $pro['fecha_fin'] ? date('Y-m-d', strtotime($pro['fecha_fin'])) : '' ?>">

                                    <div class="feed_title">
                                        <h3 class="mb-0"><?= esc($categoria['nombre']) ?></h3>
                                    </div>

                                    <div class="feed_content_image">
                                        <span>
                                            <b><?= esc($pro['titulo']) ?></b><br>
                                            <div>Contratista: <?= esc($pro['nombre_contratista']) ?> <?= esc($pro['apellido_contratista']) ?></div>
                                            <div>Presupuesto: $<?= number_format($pro['presupuesto'], 2) ?></div>
                                            <div>Etapa: <?= ucfirst(esc($pro['etapa'])) ?></div>
                                            <div>Fecha inicio: <?= date('d/m/Y', strtotime($pro['fecha_publicacion'])) ?></div>
                                            <div>Fecha fin: <?= $pro['fecha_fin'] ? date('d/m/Y', strtotime($pro['fecha_fin'])) : '<span class="text-muted">No definida</span>' ?></div>
                                            <div><?= $pro['publicaciones'] ?> Publicacion/es</div>
                                        </span>
                                    </div>

                                    <div class="feed_footer">
                                        <ul class="feed_footer_left">

                                        </ul>
                                        <ul class="feed_footer_right">
                                            <li>
                                                <?= form_open('proyectos/eliminar', ['class' => 'd-inline']) ?>
                                                <input type="hidden" name="id_proyectos" value="<?= $pro['id_proyectos'] ?>">
                                                <button type="submit" class="btn-delete" <?= ($pro['comentarios'] > 0) ? 'disabled title="No se puede eliminar con comentarios"' : '' ?>>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <?= form_close() ?>
                                            </li>
                                            <li>
                                                <button type="button" style="background: #57b846; 
                                                    color: white; 
                                                    border: none; 
                                                    padding: 6px 12px;
                                                    border-radius: 4px;
                                                    cursor: pointer;" class="btn-edit" onclick="showEditForm(this)">Editar</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Edición -->
<div class="edit-modal" id="editModal">
    <div class="modal-content">
        <h3>Editar Proyecto</h3>
        <?= form_open('proyectos/update', ['id' => 'editForm']) ?>
        <?= csrf_field() ?>
        <input type="hidden" name="id_proyectos" id="editId">

        <div class="pi-input pi-input-lgg">
            <labdivel>Título:</labdel>
                <input type="text" name="titulo" id="editTitulo" class="form-control" required>
        </div>

        <div class="pi-input pi-input-lg">
            <label>Categoría:</label>
            <select name="id_categoria" id="editCategoria" class="form-control" required>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>"><?= esc($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pi-input pi-input-lg">
            <label>Contratista:</label>
            <select name="id_contratista" id="editContratista" class="form-control" required>
                <?php foreach ($contratistas as $c): ?>
                    <option value="<?= $c['id_usuario'] ?>">
                        <?= esc($c['nombre']) ?> <?= esc($c['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pi-input pi-input-lg">
            <label>Presupuesto:</label>
            <input type="number" name="presupuesto" id="editPresupuesto" class="form-control" step="0.01" required>
        </div>

        <div class="pi-input pi-input-lg">
            <label>Etapa:</label>
            <select name="etapa" id="editEtapa" class="form-control" required>
                <?php foreach (['planificacion', 'ejecucion', 'finalizado'] as $etapa): ?>
                    <option value="<?= $etapa ?>"><?= ucfirst(esc($etapa)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

            <div class="pi-input pi-input-lg">
                <label>Fecha de Inicio:</label>
                <input 
                    type="date" 
                    name="fecha_publicacion" 
                    id="editFechaPublicacion" 
                    class="form-control" 
                    required
                >
            </div>

            <div class="pi-input pi-input-lg">
                <label>Fecha Fin:</label>
                <input 
                    type="date" 
                    name="fecha_fin" 
                    id="editFechaFin" 
                    class="form-control"
                >
            </div>

        
        <div class="feed_footer">
            <ul class="feed_footer_left"></ul>
            <ul class="feed_footer_right">
                <li>
                    <button type="submit" style="background: green; 
                                                    color: white; 
                                                    border: none; 
                                                    padding: 6px 12px;
                                                    border-radius: 4px;
                                                    cursor: pointer;">Guardar</button>
                    <button type="button" style="background: #57b846; 
                                                    color: white; 
                                                    border: none; 
                                                    padding: 6px 12px;
                                                    border-radius: 4px;
                                                    cursor: pointer;" onclick="closeEditForm()">Cancelar</button>
                </li>
            </ul>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function showEditForm(button) {
    const proyectoDiv = button.closest('.feed');
    const dataset = proyectoDiv.dataset;

    // Campos existentes
    document.getElementById('editId').value = dataset.id;
    document.getElementById('editTitulo').value = dataset.titulo;
    document.getElementById('editCategoria').value = dataset.categoria;
    document.getElementById('editContratista').value = dataset.contratista;
    document.getElementById('editPresupuesto').value = dataset.presupuesto;
    document.getElementById('editEtapa').value = dataset.etapa;

    // Campos de fecha (nuevos)
    document.getElementById('editFechaPublicacion').value = dataset.fechaPublicacion;
    document.getElementById('editFechaFin').value = dataset.fechaFin;

    document.getElementById('editModal').style.display = 'block';
}

    function closeEditForm() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

<style>
    .edit-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        /* Cambiado de hidden a none para mejor compatibilidad */
        z-index: 9999;
        overflow: auto;
        padding: 20px 0;
    }

    .modal-content {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        margin: auto;
        /* Centrado automático */
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .form-actions {
        margin-top: 1rem;
        text-align: right;
    }

    .btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        margin-left: 0.5rem;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<?= $this->endSection() ?>