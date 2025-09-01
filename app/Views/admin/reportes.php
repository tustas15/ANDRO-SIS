<?= $this->extend('layouts/usuarios_layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="feed">
        <center>
            <div class="publish">
                <h1>Reportes - <?= ucfirst($tipoReporte) ?></h1>
            </div>
            <br>
            <div class="report-nav">
                <h3>
                    <a href="<?= site_url("admin/reportes/contratistas") ?>" class="selected-orange">
                        Contratistas
                    </a>|
                    <a href="<?= site_url("admin/reportes/proyectos") ?>" class="selected-orange">
                       Proyectos
                    </a>|
                    <a href="<?= site_url("admin/reportes/categorias") ?>" class="selected-orange">
                        Categorías
                    </a>
                </h3>
            </div>
        </center>

        <?php if ($tipoReporte == 'contratistas'): ?>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>Contratista</th>
                        <th>Proyectos</th>
                        <th>Publicaciones</th>
                        <th>Likes</th>
                        <th>Comentarios</th>
                        <th>Presupuesto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contratistas as $c): ?>
                        <tr>
                            <td><?= $c['nombre'] . ' ' . $c['apellido'] ?></td>
                            <td><?= $c['total_proyectos'] ?></td>
                            <td><?= $c['total_publicaciones'] ?></td>
                            <td><?= $c['total_megustas'] ?></td>
                            <td><?= $c['total_comentarios'] ?></td>
                            <td>$<?= number_format($c['presupuesto_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($tipoReporte == 'proyectos'): ?>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Contratista</th>
                        <th>Categoría</th>
                        <th>Presupuesto</th>
                        <th>Avance</th>
                        <th>Publicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proyectos as $p): ?>
                        <tr>
                            <td><?= $p['titulo'] ?></td>
                            <td><?= $p['contratista'] ?></td>
                            <td><?= $p['categoria'] ?></td>
                            <td>$<?= number_format($p['presupuesto'], 2) ?></td>
                            <td><?= $p['avance'] ?? 0 ?>%</td>
                            <td><?= $p['total_publicaciones'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($tipoReporte == 'categorias'): ?>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Proyectos</th>
                        <th>Presupuesto Total</th>
                        <th>Avance Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $cat): ?>
                        <tr>
                            <td><?= $cat['nombre'] ?></td>
                            <td><?= $cat['total_proyectos'] ?></td>
                            <td>$<?= number_format($cat['presupuesto_total'], 2) ?></td>
                            <td><?= number_format($cat['promedio_avance'], 2) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="export-buttons">
            <a href="<?= site_url("admin/reportes/exportarPDF/{$tipoReporte}") ?>" class="export-button">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?= site_url("admin/reportes/exportarExcel/{$tipoReporte}") ?>" class="export-button">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>

        <center>
            <h3>Estadísticas Globales</h3>
        </center>
        <div class="global-stats">
            <div class="stat-item">
                <h4>Total Contratistas</h4>
                <p><?= $estadisticasGlobales['total_contratistas'] ?></p>
            </div>
            <div class="stat-item">
                <h4>Total Proyectos</h4>
                <p><?= $estadisticasGlobales['total_proyectos'] ?></p>
            </div>
            <div class="stat-item">
                <h4>Presupuesto Total</h4>
                <p>$<?= number_format($estadisticasGlobales['presupuesto_total'], 2) ?></p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos Responsive */
    .responsive-table {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    table {
        min-width: 600px;
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    th, td {
        padding: 15px;
        text-align: center;
        font-size: 0.95em;
        border-bottom: 1px solid #f0f0f0;
    }

    th {
        background-color: #3F4257;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    tbody tr:hover {
        background-color: #f8f9fa;
    }

    .export-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin: 30px 0;
    }

    .export-button {
        padding: 12px 25px;
        border-radius: 5px;
        background: #57b846;
        color: white !important;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }

    .export-button:hover {
        background: #4aa339;
        transform: translateY(-2px);
    }

    .global-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .stat-item {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    @media (max-width: 768px) {
        th, td {
            padding: 12px;
            font-size: 0.85em;
        }
        
        .export-buttons {
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }
        
        .export-button {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        th, td {
            padding: 10px;
            font-size: 0.8em;
        }
        
        .global-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>