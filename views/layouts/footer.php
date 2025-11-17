<!-- Main Footer -->
<footer class="main-footer">
    <div class="float-end d-none d-sm-inline">
        <b>Versión</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?= date('Y') ?> 
        <a href="#" class="text-decoration-none">Laboratorio Clínico</a>.
    </strong>
    Todos los derechos reservados.
    
    <span class="ms-3">
        <small class="text-muted">
            Usuario: <strong><?= htmlspecialchars($_SESSION['user']['nombre_completo'] ?? 'N/A') ?></strong> |
            Sucursal: <strong><?= htmlspecialchars($_SESSION['user']['sucursal_nombre'] ?? 'N/A') ?></strong> |
            Rol: <strong><?= htmlspecialchars($_SESSION['user']['rol_nombre'] ?? 'N/A') ?></strong>
        </small>
    </span>
</footer>
