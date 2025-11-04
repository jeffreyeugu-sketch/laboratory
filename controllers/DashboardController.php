<?php
/**
 * Controlador del Dashboard
 * Pantalla principal del sistema
 */

class DashboardController extends Controller {
    
    /**
     * Mostrar el dashboard principal
     */
    public function index() {
        // Verificar autenticación
        if (!Auth::check()) {
            redirect('login');
        }
        
        $usuario = currentUser();
        
        // Obtener estadísticas básicas
        $db = Database::getInstance()->getConnection();
        
        // Total de pacientes
        $stmt = $db->query("SELECT COUNT(*) as total FROM pacientes WHERE activo = 1");
        $totalPacientes = $stmt->fetch()['total'];
        
        // Total de órdenes hoy
        $stmt = $db->query("SELECT COUNT(*) as total FROM ordenes WHERE DATE(fecha_registro) = CURDATE()");
        $ordenesHoy = $stmt->fetch()['total'];
        
        // Órdenes pendientes
        $stmt = $db->query("SELECT COUNT(*) as total FROM ordenes WHERE estatus IN ('registrada', 'en_proceso')");
        $ordenesPendientes = $stmt->fetch()['total'];
        
        // Resultados pendientes de validación
        $stmt = $db->query("SELECT COUNT(*) as total FROM resultados WHERE estatus = 'capturado'");
        $resultadosPendientes = $stmt->fetch()['total'];
        
        $data = [
            'title' => 'Dashboard',
            'usuario' => $usuario,
            'stats' => [
                'totalPacientes' => $totalPacientes,
                'ordenesHoy' => $ordenesHoy,
                'ordenesPendientes' => $ordenesPendientes,
                'resultadosPendientes' => $resultadosPendientes
            ]
        ];
        
        $this->view('dashboard/index', $data);
    }
}
