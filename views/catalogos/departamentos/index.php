<?php
/**
 * Vista: Listado de Departamentos del Laboratorio
 */
?>

<!-- jQuery (REQUERIDO) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Contenido Principal -->
<div class="container-fluid py-4">
    
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Departamentos</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                    <li class="breadcrumb-item">Catálogos</li>
                    <li class="breadcrumb-item active">Departamentos</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('/catalogos/departamentos/crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nuevo Departamento
            </a>
        </div>
    </div>

    <!-- Mensaje Flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    endif; 
    ?>
    
    <!-- Tarjeta con Tabla -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-building me-2"></i>
                Listado de Departamentos
            </h5>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table id="tablaDepartamentos" class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">ID</th>
                            <th width="15%">Área</th>
                            <th width="12%">Código</th>
                            <th width="25%">Nombre</th>
                            <th width="10%" class="text-center">Orden</th>
                            <th width="10%" class="text-center">Estado</th>
                            <th width="10%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables cargará los datos aquí -->
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
</div>

<!-- Script de DataTables -->
<script>
$(document).ready(function() {
    
    // Inicializar DataTables
    const tabla = $('#tablaDepartamentos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= url('/catalogos/departamentos/listar') ?>',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('Error al cargar datos:', error);
                console.error('Código:', code);
                console.error('Respuesta:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los datos. Revisa la consola.'
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'area' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'orden', className: 'text-center' },
            { data: 'activo', className: 'text-center' },
            { data: 'acciones', className: 'text-center', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron registros",
            emptyTable: "No hay datos disponibles",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
    
    // Evento para eliminar departamento
    $('#tablaDepartamentos').on('click', '.btn-eliminar', function() {
        const departamentoId = $(this).data('id');
        const departamentoNombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Se eliminará el departamento:<br><strong>${departamentoNombre}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar petición de eliminación
                fetch('<?= url('/catalogos/departamentos/eliminar/') ?>' + departamentoId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tabla.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el departamento'
                    });
                });
            }
        });
    });
    
});
</script>