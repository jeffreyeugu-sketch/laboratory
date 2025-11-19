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
                            <th width="30%">Descripción</th>
                            <th width="10%">Estado</th>
                            <th width="12%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables cargará los datos vía AJAX -->
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    
    // Inicializar DataTable
    const tabla = $('#tablaAreas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= url('/catalogos/areas/listar') ?>',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('Error en DataTables:', error, thrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los datos. Verifica la consola.'
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'descripcion' },
            { data: 'activo', orderable: false },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
        },
        order: [[1, 'asc']], // Ordenar por código
        pageLength: 25,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });
    
    // Manejar eliminación
    $('#tablaAreas').on('click', '.btn-eliminar', function() {
        const areaId = $(this).data('id');
        const areaNombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Está seguro?',
            html: `¿Desea eliminar el área <strong>${areaNombre}</strong>?<br><br>
                   <small class="text-muted">Si tiene estudios relacionados, solo se desactivará.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= url('/catalogos/areas/eliminar/') ?>' + areaId;
            }
        });
    });
    
    // Mostrar mensajes flash
    <?php if (isset($_SESSION['flash_message'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['flash_type'] ?? 'info' ?>',
            title: '<?= $_SESSION['flash_type'] === 'success' ? 'Éxito' : 'Aviso' ?>',
            text: '<?= addslashes($_SESSION['flash_message']) ?>',
            timer: 3000,
            showConfirmButton: false
        });
        <?php 
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
});
</script>

<style>
/* Estilos adicionales */
.card {
    border: none;
    border-radius: 10px;
}

.table thead th {
    font-weight: 600;
    font-size: 14px;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    padding: 0.35rem 0.65rem;
    font-weight: 500;
}
</style>
