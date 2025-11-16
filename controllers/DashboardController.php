<?php
/**
 * Controlador del Dashboard
 * Pantalla principal del sistema con estadísticas y accesos rápidos
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
        
        // Obtener estadísticas del sistema
        $stats = $this->obtenerEstadisticas();
        
        $data = [
            'title' => 'Dashboard - Sistema de Laboratorio',
            'usuario' => $usuario,
            'stats' => $stats
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    /**
     * Obtener estadísticas del dashboard
     * 
     * @return array Estadísticas del sistema
     */
    private function obtenerEstadisticas() {
        $db = Database::getInstance()->getConnection();
        
        try {
            // Total de pacientes activos
            $stmt = $db->query("SELECT COUNT(*) as total FROM pacientes WHERE activo = 1");
            $totalPacientes = $stmt->fetch()['total'] ?? 0;
            
            // Total de órdenes del día actual
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordenes WHERE DATE(fecha_registro) = CURDATE()");
            $ordenesHoy = $stmt->fetch()['total'] ?? 0;
            
            // Órdenes pendientes de procesamiento (registradas o en proceso)
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordenes WHERE estatus IN ('registrada', 'en_proceso')");
            $ordenesPendientes = $stmt->fetch()['total'] ?? 0;
            
            // Resultados pendientes de validación
            $stmt = $db->query("SELECT COUNT(*) as total FROM resultados WHERE estatus = 'capturado'");
            $resultadosPendientes = $stmt->fetch()['total'] ?? 0;
            
            return [
                'totalPacientes' => (int)$totalPacientes,
                'ordenesHoy' => (int)$ordenesHoy,
                'ordenesPendientes' => (int)$ordenesPendientes,
                'resultadosPendientes' => (int)$resultadosPendientes
            ];
            
        } catch (Exception $e) {
            // En caso de error, retornar valores por defecto
            logMessage('Error al obtener estadísticas del dashboard: ' . $e->getMessage(), 'ERROR');
            
            return [
                'totalPacientes' => 0,
                'ordenesHoy' => 0,
                'ordenesPendientes' => 0,
                'resultadosPendientes' => 0
            ];
        }
    }
    
    /**
     * Obtener actividad reciente del sistema (para futuras mejoras)
     * 
     * @param int $limite Número de registros a obtener
     * @return array Lista de actividades recientes
     */
    private function obtenerActividadReciente($limite = 10) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    tipo_entidad,
                    entidad_id,
                    accion,
                    usuario_id,
                    fecha_hora,
                    descripcion
                FROM auditoria
                ORDER BY fecha_hora DESC
                LIMIT :limite
            ");
            
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            logMessage('Error al obtener actividad reciente: ' . $e->getMessage(), 'ERROR');
            return [];
        }
    }
}
