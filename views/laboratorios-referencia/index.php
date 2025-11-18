<?php
/**
 * Vista: catalogos/laboratorios-referencia/index.php
 * Listado de laboratorios de referencia
 */
?>

<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-1">
            <i class="fas fa-hospital text-primary me-2"></i>
            Laboratorios de Referencia
        </h2>
        <p class="text-muted">Gestión de laboratorios externos para subrogación de estudios</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= url('catalogos/laboratorios-referencia/crear') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nuevo Laboratorio
        </a>
    </div>
</div>

<!-- Tarjeta con tabla -->
<div class="stat-card">
    <div class="table-responsive">
        <table id="tablaLaboratorios" class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Ciudad/Estado</th>
                    <th>Contacto</th>
                    <th>Días Entrega</th>
                    <th>Estado</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($laboratorios as $lab): ?>
                <tr>
                    <td><span class="badge bg-secondary"><?= e($lab['codigo']) ?></span></td>
                    <td>
                        <strong><?= e($lab['nombre']) ?></strong>
                        <?php if($lab['razon_social']): ?>
                            <br><small class="text-muted"><?= e($lab['razon_social']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($lab['ciudad'] || $lab['estado']): ?>
                            <?= e($lab['ciudad']) ?><?= $lab['ciudad'] && $lab['estado'] ? ', ' : '' ?><?= e($lab['estado']) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($lab['contacto_nombre']): ?>
                            <small class="d-block"><?= e($lab['contacto_nombre']) ?></small>
                            <?php if($lab['contacto_telefono']): ?>
                                <small class="text-muted"><i class="fas fa-phone"></i> <?= e($lab['contacto_telefono']) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-info"><?= $lab['dias_entrega_promedio'] ?> días</span>
                    </td>
                    <td>
                        <?php if($lab['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= url('catalogos/laboratorios-referencia/ver/' . $lab['id']) ?>" 
                               class="btn btn-info" 
                               title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('catalogos/laboratorios-referencia/editar/' . $lab['id']) ?>" 
                               class="btn btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="eliminarLaboratorio(<?= $lab['id'] ?>, '<?= e($lab['nombre']) ?>')"
                                    title="Eliminar">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#tablaLaboratorios').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
        },
        order: [[5, 'desc'], [1, 'asc']], // Ordenar por estado (activo primero) y luego nombre
        pageLength: 25
    });
});

// Función para eliminar laboratorio
function eliminarLaboratorio(id, nombre) {
    Swal.fire({
        title: '¿Eliminar Laboratorio?',
        html: `¿Deseas eliminar el laboratorio <strong>${nombre}</strong>?<br><small class="text-muted">Si tiene estudios asignados, solo se desactivará.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario para enviar POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= url('catalogos/laboratorios-referencia/eliminar/') ?>' + id;
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
        color: #495057;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 14px;
    }
    
    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>
