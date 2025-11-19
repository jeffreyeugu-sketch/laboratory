<?php
/**
 * Vista: Listado de Tipos de Muestra
 */
?>

<!-- jQuery -->
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
            <h2 class="mb-1">Tipos de Muestra</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                    <li class="breadcrumb-item">Catálogos</li>
                    <li class="breadcrumb-item active">Tipos de Muestra</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('/catalogos/tipos-muestra/crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nuevo Tipo
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
                <i class="fas fa-vial me-2"></i>
                Listado de Tipos de Muestra
            </h5>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table id="tablaTipos" class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">ID</th>
                            <th width="12%">Código</th>
                            <th width="25%">Nombre</th>
                            <th width="25%">Requisitos</th>
                            <th width="15%">Estado</th>
                            <th width="15%">Acciones</th>
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
    
    const tabla = $('#tablaTipos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= url('/catalogos/tipos-muestra/listar') ?>',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('Error completo:', xhr.responseText);
                console.error('Status:', xhr.status);
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'Revisa la consola para más detalles'
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'requisitos' },
            { data: 'activo' },
            { data: 'acciones', orderable: false, searchable: false }
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
            zeroRecords: "No se encontraron registros coincidentes",
            emptyTable: "No hay datos disponibles en la tabla",
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
    
    // Evento para eliminar
    $('#tablaTipos').on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Se eliminará el tipo de muestra:<br><strong>${nombre}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= url('/catalogos/tipos-muestra/eliminar/') ?>' + id, {
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
                        tabla.ajax.reload();
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
                        text: 'No se pudo eliminar el tipo de muestra'
                    });
                });
            }
        });
    });
    
});
</script>