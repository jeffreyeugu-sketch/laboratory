<?php
/**
 * Vista: catalogos/estudios/index.php
 * Listado de estudios
 */
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-1">
            <i class="fas fa-flask text-primary me-2"></i>
            Catálogo de Estudios
        </h2>
        <p class="text-muted">Gestión del catálogo completo de estudios del laboratorio</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= url('catalogos/estudios/crear') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nuevo Estudio
        </a>
    </div>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table id="tablaEstudios" class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Área</th>
                    <th>Tipo Muestra</th>
                    <th>Días</th>
                    <th>Subrogado</th>
                    <th>Estado</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($estudios as $estudio): ?>
                <tr>
                    <td><span class="badge bg-secondary"><?= e($estudio['codigo_interno']) ?></span></td>
                    <td>
                        <strong><?= e($estudio['nombre']) ?></strong>
                        <?php if($estudio['nombre_corto']): ?>
                            <br><small class="text-muted"><?= e($estudio['nombre_corto']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-info"><?= e($estudio['area_nombre']) ?></span></td>
                    <td><?= e($estudio['tipo_muestra_nombre']) ?></td>
                    <td><?= $estudio['dias_proceso'] ?> día<?= $estudio['dias_proceso'] > 1 ? 's' : '' ?></td>
                    <td>
                        <?php if($estudio['es_subrogado']): ?>
                            <span class="badge bg-warning">
                                <i class="fas fa-share-square"></i> Subrogado
                            </span>
                            <?php if($estudio['laboratorio_referencia_nombre']): ?>
                                <br><small class="text-muted"><?= e($estudio['laboratorio_referencia_nombre']) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($estudio['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= url('catalogos/estudios/ver/' . $estudio['id']) ?>" 
                               class="btn btn-info" 
                               title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('catalogos/estudios/editar/' . $estudio['id']) ?>" 
                               class="btn btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="eliminarEstudio(<?= $estudio['id'] ?>, '<?= e($estudio['nombre']) ?>')"
                                    title="Desactivar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaEstudios').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
        },
        order: [[6, 'desc'], [1, 'asc']],
        pageLength: 25
    });
});

function eliminarEstudio(id, nombre) {
    Swal.fire({
        title: '¿Desactivar Estudio?',
        html: `¿Deseas desactivar el estudio <strong>${nombre}</strong>?<br><small class="text-muted">Podrás reactivarlo después si es necesario.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= url('catalogos/estudios/eliminar/') ?>' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 14px;
    }
    .table td {
        vertical-align: middle;
        font-size: 14px;
    }
</style>
