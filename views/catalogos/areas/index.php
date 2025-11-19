<?php
/**
 * Vista: Listado de Áreas del Laboratorio
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
            <h2 class="mb-1">Áreas del Laboratorio</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                    <li class="breadcrumb-item">Catálogos</li>
                    <li class="breadcrumb-item active">Áreas</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('/catalogos/areas/crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nueva Área
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
                <i class="fas fa-sitemap me-2"></i>
                Listado de Áreas
            </h5>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table id="tablaAreas" class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">ID</th>
                            <th width="15%">Código</th>
                            <th width="25%">Nombre</th>
                            <th width="32%">Descripción</th>
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
    const tabla = $('#tablaAreas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= url('/catalogos/areas/listar') ?>',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('Error al cargar datos:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los datos. Revisa la consola.'
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'descripcion' },
            { data: 'activo' },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
    
    // Evento para eliminar área
    $('#tablaAreas').on('click', '.btn-eliminar', function() {
        const areaId = $(this).data('id');
        const areaNombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Se eliminará el área:<br><strong>${areaNombre}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar petición de eliminación
                fetch('<?= url('/catalogos/areas/eliminar/') ?>' + areaId, {
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
                        tabla.ajax.reload(); // Recargar tabla
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
                        text: 'No se pudo eliminar el área'
                    });
                });
            }
        });
    });
    
});
</script>