<?php
/**
 * Vista: Listado de Áreas del Laboratorio
 * Muestra tabla con todas las áreas registradas usando DataTables
 */
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $data['titulo'] ?? 'Áreas' ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php if (isset($data['breadcrumb'])): ?>
                        <?php foreach ($data['breadcrumb'] as $item): ?>
                            <li class="breadcrumb-item">
                                <?php if (!empty($item['url'])): ?>
                                    <a href="<?= $item['url'] ?>"><?= $item['nombre'] ?></a>
                                <?php else: ?>
                                    <?= $item['nombre'] ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Card con tabla -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-sitemap mr-2"></i>
                    Listado de Áreas
                </h3>
                <div class="card-tools">
                    <a href="/catalogos/areas/crear" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Nueva Área
                    </a>
                </div>
            </div>
            <div class="card-body">
                
                <div class="table-responsive">
                    <table id="tablaAreas" class="table table-bordered table-striped table-hover">
                        <thead>
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
</section>

<!-- Scripts específicos -->
<script>
$(document).ready(function() {
    
    // Inicializar DataTable
    const tabla = $('#tablaAreas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/catalogos/areas/listar',
            type: 'GET'
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
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
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
                // Enviar petición de eliminación
                window.location.href = `/catalogos/areas/eliminar/${areaId}`;
            }
        });
    });
    
    // Mostrar mensajes flash si existen
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $tipo => $mensaje): ?>
            Swal.fire({
                icon: '<?= $tipo ?>',
                title: '<?= $tipo === 'success' ? 'Éxito' : 'Error' ?>',
                text: '<?= $mensaje ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
});
</script>

<style>
/* Estilos adicionales para la tabla */
#tablaAreas tbody tr {
    cursor: default;
}

#tablaAreas tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
