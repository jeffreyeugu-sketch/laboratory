<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-1">
            <i class="fas fa-vial text-primary me-2"></i>
            Catálogo de Estudios
        </h2>
        <p class="text-muted">Gestión del catálogo de estudios del laboratorio</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= url('catalogos/estudios/crear') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nuevo Estudio
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
<div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
endif; 
?>

<!-- Tarjeta con tabla -->
<div class="stat-card">
    <div class="table-responsive">
        <table id="tablaEstudios" class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Área</th>
                    <th>Tipo Muestra</th>
                    <th>Días Proceso</th>
                    <th>Subrogado</th>
                    <th>Estado</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Cargado dinámicamente con DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- CSS adicional para DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    const tabla = $('#tablaEstudios').DataTable({
        ajax: {
            url: '<?= url('catalogos/estudios/listar') ?>',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'codigo_interno',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            { 
                data: 'nombre',
                render: function(data, type, row) {
                    let html = '<strong>' + data + '</strong>';
                    if (row.nombre_corto) {
                        html += '<br><small class="text-muted">' + row.nombre_corto + '</small>';
                    }
                    return html;
                }
            },
            { 
                data: 'area_nombre',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            { 
                data: 'tipo_muestra_nombre',
                render: function(data) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'dias_proceso',
                render: function(data) {
                    return data + ' día(s)';
                }
            },
            { 
                data: 'es_subrogado',
                render: function(data, type, row) {
                    if (data == 1) {
                        let html = '<span class="badge bg-warning">Sí</span>';
                        if (row.laboratorio_referencia_nombre) {
                            html += '<br><small class="text-muted">' + row.laboratorio_referencia_nombre + '</small>';
                        }
                        return html;
                    } else {
                        return '<span class="badge bg-success">No</span>';
                    }
                }
            },
            { 
                data: 'activo',
                render: function(data) {
                    if (data == 1) {
                        return '<span class="badge bg-success">Activo</span>';
                    } else {
                        return '<span class="badge bg-secondary">Inactivo</span>';
                    }
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= url('catalogos/estudios/ver/') ?>${row.id}" 
                               class="btn btn-info" 
                               title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('catalogos/estudios/editar/') ?>${row.id}" 
                               class="btn btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger" 
                                    onclick="confirmarEliminar(${row.id}, '${row.nombre}')" 
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        order: [[1, 'asc']],
        pageLength: 25
    });
    
    // Función para confirmar eliminación
    window.confirmarEliminar = function(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas desactivar el estudio <strong>${nombre}</strong>?<br><small class="text-muted">Podrás reactivarlo después si es necesario.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= url('catalogos/estudios/eliminar/') ?>' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
});
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
    
    .dataTables_wrapper .dataTables_length select {
        padding: 5px 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        padding: 5px 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-left: 5px;
    }
    
    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>
