<!-- app/Views/dashboard/admin.php -->
<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Administrar Categorías</h1>

    <!-- Mensajes de estado -->
    <?php if (session('mensaje')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Crear Nueva Categoría
        </div>
        <div class="card-body">
            <form method="POST" action="<?= route_to('admin.proyectos.eliminar') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nombre_categoria" class="form-label">Nombre de la categoría</label>
                    <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria"
                        value="<?= old('nombre_categoria') ?>" required>
                    <?php if (session('errors.nombre_categoria')): ?>
                        <div class="text-danger"><?= session('errors.nombre_categoria') ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>
                    Crear Categoría
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Listado de Categorías
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Proyectos asociados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categorias) && is_array($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><?= esc($categoria['categoria_nombre'] ?? '') ?></td>
                                    <td>
                                        <?= !empty($categoria['proyectos'])
                                            ? esc($categoria['proyectos'])
                                            : '<span class="text-muted">Sin proyectos</span>' ?>
                                    </td>
                                    <td>
                                        <?php if (empty($categoria['proyectos'])): ?>
                                            <form method="POST" action="<?= route_to('admin.proyectos') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria'] ?>">
                                                <input type="hidden" name="eliminar_categoria" value="1">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Confirmar eliminación?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">No se puede eliminar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="alert alert-warning mb-0">
                                        No se encontraron categorías registradas
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>