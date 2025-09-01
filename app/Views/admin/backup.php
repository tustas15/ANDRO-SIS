<?= $this->extend('layouts/usuarios_layout') ?>

<?= $this->section('content') ?>

<div class="row">
    
        <!-- Sección de Crear Respaldo -->
        <div class="publish">
            <center>
            <h2>Gestión de Respaldos</h2>
            </center>
            <?php if (session('success')): ?>
                <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>
            <?php if (session('error')): ?>
                <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>
        </div>
    <div class="settings shadow">
        <div class="settings_content">
            <div class="row">
                <center>
                <div class="pi-input pi-input-lgg">
                    <center>
                    <h4>Crear Nuevo Respaldo</h4>
                    <form action="<?= site_url('admin/backup/create') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" style="background-color: #57b846">
                            <i class="fa fa-database me-2"></i> Generar Respaldo
                        </button>
                    </form>
                    </center>
                </div>
            
            <!-- Sección de Restaurar -->
            <div class="pi-input pi-input-lgg">
                <center>
                        <h4>Restaurar Respaldo</h4>
                        <form action="<?= site_url('admin/backup/restore') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="custom-file-upload">
                                <label for="file-input" class="file-label">
                                    <i class="fa fa-cloud-upload-alt"></i>
                                    <span class="file-text">Seleccionar archivo ( .sql )</span>
                                    <div class="browse-button">Examinar</div>
                                </label>
                                <input type="file" id="file-input" class="form-control" name="backup_file" required accept=".sql,.gz">
                                <span class="file-name" id="file-name">Ningún archivo seleccionado</span>
                            </div>
                            <button type="submit" style="background: #57b846;">
                                <i class="fa fa-upload me-2"></i> Restaurar Base de Datos
                            </button>
                        </form>
                        </center>
                    </div>
                </center>
            </div>
        </div>
            <!-- Listado de Respaldos -->
            <div class="row">
                
    <div class="col-12">
                <div class="table-responsive">
                    <center>
                    <h4>Respaldos Existentes</h4>
                    </center>
                    <table >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td><?= $backup['name'] ?></td>
                                    <td><?= $backup['size'] ?></td>
                                    <td><?= $backup['date'] ?></td>
                                    <td>
                                        <a href="<?= site_url('admin/backup/download/' . $backup['name']) ?>"
                                            class="selected-orange">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <a href="<?= site_url('admin/backup/delete/' . $backup['name']) ?>"
                                            style="color:#bb2e2e"
                                            onclick="return confirm('¿Eliminar este respaldo?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    
</div>
<style>
    table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

thead tr {
    background-color: #3F4257;
    color: white;
    position: sticky;
    top: 0;
}

th, td {
    padding: 15px;
    text-align: center;
    font-size: 0.9em;
}

th {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

tbody tr {
    border-bottom: 1px solid #e6ecf5;
    transition: all 0.2s ease;
}

tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

tbody tr:hover {
    background-color: #fff3f0;
    transform: translateX(4px);
}

/* Contenedor responsive para tablas */
.responsive-table {
    overflow-x: auto;
    margin: 20px 0;
    border-radius: 8px;
}

/* Estilos para botones de exportación */
.export-buttons {
    display: flex;
    gap: 15px;
    margin: 20px 0;
}

.export-button {
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #57b846;
    color: white !important;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.export-button:hover {
    background-color:#419232;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 94, 58, 0.3);
}

/* Estilos para la sección de restaurar */
.restore-section {
    margin-top: 30px;
    padding: 25px;
    border-radius: 12px;
    background: #f8f9fa;
}

.custom-file-upload {
    position: relative;
    margin-left: 15px;
    margin-right: 15px;
}

.file-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    border: 2px dashed #57b846;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-label:hover {
    background: rgba(87, 184, 70, 0.05);
    border-color: #419232;
}

.file-label i {
    font-size: 40px;
    color: #57b846;
    margin-bottom: 15px;
}

.file-text {
    color: #6c757d;
    font-size: 16px;
    margin-bottom: 10px;
}

.browse-button {
    padding: 8px 25px;
    background: #57b846;
    color: white;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.file-label:hover .browse-button {
    background: #419232;
    transform: translateY(-2px);
}

#file-input {
    display: none;
}

.file-name {
    display: block;
    margin-top: 10px;
    color: #6c757d;
    font-size: 14px;
}

.restore-button {
    background: #57b846;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    border: none;
    font-size: 16px;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.restore-button:hover {
    background: #419232;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(87, 184, 70, 0.3);
}

/* Nuevos estilos responsive */
@media screen and (max-width: 768px) {
        table {
            border: 0;
            box-shadow: none;
        }
        
        table thead {
            display: none;
        }
        
        table tr {
            display: block;
            margin-bottom: 1.5rem;
            border: 1px solid #e6ecf5;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        table td {
            display: block;
            text-align: right;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e6ecf5;
            font-size: 0.9em;
        }
        
        table td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #3F4257;
            text-transform: uppercase;
            font-size: 0.8em;
        }
        
        table td:last-child {
            border-bottom: 0;
        }
        
        /* Ajustar iconos en móvil */
        table td[data-label="Acciones"] a {
            margin: 0 5px;
            display: inline-block;
        }
    }

    /* Mejorar scroll en móvil */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
<script>
// JavaScript para mostrar el nombre del archivo seleccionado
document.getElementById('file-input').addEventListener('change', function(e) {
    const fileName = document.getElementById('file-name');
    if (this.files.length > 0) {
        fileName.textContent = this.files[0].name;
    } else {
        fileName.textContent = 'Ningún archivo seleccionado';
    }
});
</script>

<?= $this->endSection() ?>

