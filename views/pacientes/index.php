<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-1">
            <i class="fas fa-users text-primary me-2"></i>
            Pacientes
        </h2>
        <p class="text-muted">Gestión de pacientes del laboratorio</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= url('pacientes/crear') ?>" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>
            Nuevo Paciente
        </a>
    </div>
</div>

<!-- Tarjeta con tabla -->
<div class="stat-card">
    <div class="table-responsive">
        <table id="tablaPacientes" class="table table-hover">
            <thead>
                <tr>
                    <th>Expediente</th>
                    <th>Nombre Completo</th>
                    <th>Edad</th>
                    <th>Sexo</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Registro</th>
                    <th width="120">Acciones</th>
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
    const tabla = $('#tablaPacientes').DataTable({
        ajax: {
            url: '<?= url('pacientes/listar') ?>',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'expediente',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            { 
                data: 'nombre_completo',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'edad',
                render: function(data) {
                    return data + ' años';
                }
            },
            { 
                data: 'sexo',
                render: function(data) {
                    if (data === 'M') {
                        return '<span class="badge bg-info">Masculino</span>';
                    } else {
                        return '<span class="badge bg-pink" style="background-color: #ec4899;">Femenino</span>';
                    }
                }
            },
            { 
                data: 'telefono',
                render: function(data) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'email',
                render: function(data) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'fecha_registro',
                render: function(data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-MX');
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= url('pacientes/ver/') ?>${data}" 
                               class="btn btn-info" 
                               title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('pacientes/editar/') ?>${data}" 
                               class="btn btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="eliminarPaciente(${data}, '${row.nombre_completo}')" 
                                    class="btn btn-danger" 
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
        },
        responsive: true,
        order: [[6, 'desc']], // Ordenar por fecha de registro DESC
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        processing: true
    });
    
    // Función para eliminar paciente
    window.eliminarPaciente = function(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar al paciente <strong>${nombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
                form.action = '<?= url('pacientes/eliminar/') ?>' + id;
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
