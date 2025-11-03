<?php
/**
 * DashboardController
 * 
 * Controlador para el panel principal del sistema
 */

require_once CORE_PATH . '/Controller.php';

class DashboardController extends Controller {
    
    /**
     * Muestra el dashboard principal
     */
    public function index() {
        // Requerir autenticación
        $this->requireAuth();
        
        // Obtener estadísticas del día
        $db = Database::getInstance()->getConnection();
        $sucursalId = $_SESSION['sucursal_id'];
        $hoy = date('Y-m-d');
        
        // Órdenes del día
        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM ordenes
            WHERE sucursal_id = ? AND DATE(fecha_registro) = ?
        ");
        $stmt->execute([$sucursalId, $hoy]);
        $ordenesHoy = $stmt->fetch()['total'] ?? 0;
        
        // Ingresos del día
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(monto), 0) as total
            FROM pagos
            WHERE sucursal_id = ? AND DATE(fecha_pago) = ? AND cancelado = 0
        ");
        $stmt->execute([$sucursalId, $hoy]);
        $ingresosHoy = $stmt->fetch()['total'] ?? 0;
        
        // Resultados pendientes
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT oe.orden_id) as total
            FROM orden_estudios oe
            JOIN ordenes o ON oe.orden_id = o.id
            WHERE o.sucursal_id = ? AND oe.estatus IN ('pendiente', 'capturado')
        ");
        $stmt->execute([$sucursalId]);
        $pendientes = $stmt->fetch()['total'] ?? 0;
        
        // Órdenes por entregar
        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM ordenes
            WHERE sucursal_id = ? AND estatus = 'liberada'
        ");
        $stmt->execute([$sucursalId]);
        $porEntregar = $stmt->fetch()['total'] ?? 0;
        
        // Últimas órdenes
        $stmt = $db->prepare("
            SELECT o.*, 
                   CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as paciente_nombre,
                   p.expediente
            FROM ordenes o
            JOIN pacientes p ON o.paciente_id = p.id
            WHERE o.sucursal_id = ?
            ORDER BY o.fecha_registro DESC
            LIMIT 10
        ");
        $stmt->execute([$sucursalId]);
        $ultimasOrdenes = $stmt->fetchAll();
        
        // Datos para la vista
        $data = [
            'ordenesHoy' => $ordenesHoy,
            'ingresosHoy' => $ingresosHoy,
            'pendientes' => $pendientes,
            'porEntregar' => $porEntregar,
            'ultimasOrdenes' => $ultimasOrdenes,
            'usuario' => Auth::user()
        ];
        
        $this->view('dashboard/index', $data);
    }
}
