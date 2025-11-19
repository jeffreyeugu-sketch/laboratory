<?php
/**
 * Vista: Listado de Metodologías del Laboratorio
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
            <h2 class="mb-1">Metodologías</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                    <li class="breadcrumb-item">Catálogos</li>
                    <li class="breadcrumb-item active">Metodologías</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('/catalogos/metodologias/crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nueva Metodología
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
                <i class="fas fa-flask me-2"></i>
                Listado de Metodologías
            </h5>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table id="tablaMetodologias" class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">ID</th>
                            <th width="10%">Orden</th>
                            <th width="12%">Código</th>
                            <th width="12%">Abreviatura</th>
                            <th width="30%">Nombre</th>
                            <th width="10%">Estado</th>
                            <th width="10%">Acciones</th>
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
    const tabla = $('#tablaMetodologias').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= url('/catalogos/metodologias/listar') ?>',
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
            { data: 'orden', className: 'text-center' },
            { 
                data: 'codigo',
                render: function(data) {
                    if (data === 'N/A' || !data) {
                        return '<span class="text-muted">—</span>';
                    }
                    return '<code>' + data + '</code>';
                }
            },
            { 
                data: 'abreviatura',
                render: function(data) {
                    if (data === 'N/A' || !data) {
                        return '<span class="text-muted">—</span>';
                    }
                    return '<strong>' + data + '</strong>';
                }
            },
            { data: 'nombre' },
            { data: 'activo' },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron registros coincidentes",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna ascendente",
                sortDescending: ": activar para ordenar la columna descendente"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
    
    // Evento para eliminar metodología
    $('#tablaMetodologias').on('click', '.btn-eliminar', function() {
        const metodologiaId = $(this).data('id');
        const metodologiaNombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Se eliminará la metodología:<br><strong>${metodologiaNombre}</strong>`,
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
                fetch('<?= url('/catalogos/metodologias/eliminar/') ?>' + metodologiaId, {
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
                        text: 'No se pudo eliminar la metodología'
                    });
                });
            }
        });
    });
    
});
</script>