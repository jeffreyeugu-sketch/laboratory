<?php
/**
 * PagoController
 * 
 * Controlador para gestión de pagos
 */

require_once CORE_PATH . '/Controller.php';
require_once MODELS_PATH . '/Pago.php';
require_once MODELS_PATH . '/Orden.php';

class PagoController extends Controller {
    
    private $pagoModel;
    private $ordenModel;
    
    public function __construct() {
        $this->pagoModel = new Pago();
        $this->ordenModel = new Orden();
    }
    
    /**
     * Muestra formulario para registrar pago
     */
    public function registrar() {
        $this->requireAuth();
        $this->requirePermission('pagos.registrar');
        
        $ordenId = $this->input('orden_id');
        
        if (!$ordenId) {
            $this->redirectWith('/laboratorio-clinico/public/ordenes', 'Orden no especificada', 'error');
        }
        
        if ($this->isPost()) {
            return $this->guardar();
        }
        
        // Obtener orden con detalles
        $orden = $this->ordenModel->obtenerConDetalles($ordenId);
        
        if (!$orden) {
            $this->redirectWith('/laboratorio-clinico/public/ordenes', 'Orden no encontrada', 'error');
        }
        
        // Obtener historial de pagos
        $pagos = $this->pagoModel->obtenerPorOrden($ordenId);
        
        // Obtener formas de pago
        $sql = "SELECT * FROM formas_pago WHERE activo = 1 ORDER BY orden_display";
        $formasPago = $this->pagoModel->queryAll($sql);
        
        $data = [
            'orden' => $orden,
            'pagos' => $pagos,
            'formasPago' => $formasPago,
            'titulo' => 'Registrar Pago'
        ];
        
        $this->view('pagos/registrar', $data);
    }
    
    /**
     * Guarda un pago
     */
    public function guardar() {
        $this->requireAuth();
        $this->requirePermission('pagos.registrar');
        
        if (!$this->isPost()) {
            $this->jsonError('Método no permitido', 405);
        }
        
        $ordenId = $this->input('orden_id');
        $monto = $this->input('monto');
        $formaPagoId = $this->input('forma_pago_id');
        $pagoMultiple = $this->input('pago_multiple') === '1';
        
        // Validar datos básicos
        if (!$ordenId || !$monto) {
            $this->jsonError('Datos incompletos');
        }
        
        // Obtener la orden para validar saldo
        $orden = $this->ordenModel->findById($ordenId);
        
        if (!$orden) {
            $this->jsonError('Orden no encontrada');
        }
        
        if ($monto > $orden['saldo']) {
            $this->jsonError('El monto es mayor al saldo pendiente');
        }
        
        $pagoData = [
            'orden_id' => $ordenId,
            'monto' => $monto,
            'forma_pago_id' => $formaPagoId,
            'referencia' => $this->input('referencia'),
            'banco' => $this->input('banco'),
            'usuario_registro_id' => Auth::id(),
            'sucursal_id' => $_SESSION['sucursal_id'],
            'notas' => $this->input('notas')
        ];
        
        $formasPago = null;
        
        // Si es pago múltiple, procesar formas de pago
        if ($pagoMultiple) {
            $formasPagoInput = $this->input('formas_pago');
            
            if (!is_array($formasPagoInput) || empty($formasPagoInput)) {
                $this->jsonError('Debe especificar las formas de pago');
            }
            
            $formasPago = [];
            $totalFormas = 0;
            
            foreach ($formasPagoInput as $forma) {
                $formasPago[] = [
                    'forma_pago_id' => $forma['forma_pago_id'],
                    'monto' => $forma['monto'],
                    'referencia' => $forma['referencia'] ?? null,
                    'banco' => $forma['banco'] ?? null
                ];
                
                $totalFormas += $forma['monto'];
            }
            
            // Validar que la suma coincida
            if (abs($totalFormas - $monto) > 0.01) {
                $this->jsonError('La suma de las formas de pago no coincide con el monto total');
            }
        }
        
        try {
            $pagoId = $this->pagoModel->registrarPago($pagoData, $formasPago);
            
            // Registrar en auditoría
            Auth::logAudit(
                Auth::id(),
                'registrar_pago',
                'pagos',
                'pago',
                $pagoId,
                null,
                $pagoData,
                'Pago registrado por $' . $monto
            );
            
            // Obtener el pago completo
            $pago = $this->pagoModel->findById($pagoId);
            
            $this->jsonSuccess([
                'pago_id' => $pagoId,
                'folio_pago' => $pago['folio_pago']
            ], 'Pago registrado exitosamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al registrar el pago: ' . $e->getMessage());
        }
    }
    
    /**
     * Ver historial de pagos de una orden
     */
    public function historial() {
        $this->requireAuth();
        $this->requirePermission('pagos.ver');
        
        $ordenId = $this->input('orden_id');
        
        if (!$ordenId) {
            $this->jsonError('Orden no especificada');
        }
        
        $pagos = $this->pagoModel->obtenerPorOrden($ordenId);
        
        $this->jsonSuccess(['pagos' => $pagos]);
    }
    
    /**
     * Cancela un pago
     */
    public function cancelar() {
        $this->requireAuth();
        $this->requirePermission('pagos.cancelar');
        
        $pagoId = $this->input('pago_id');
        $motivo = $this->input('motivo');
        
        if (!$pagoId || !$motivo) {
            $this->jsonError('Datos incompletos');
        }
        
        try {
            $this->pagoModel->cancelarPago($pagoId, $motivo, Auth::id());
            
            Auth::logAudit(
                Auth::id(),
                'cancelar_pago',
                'pagos',
                'pago',
                $pagoId,
                null,
                ['motivo' => $motivo],
                'Pago cancelado'
            );
            
            $this->jsonSuccess(null, 'Pago cancelado exitosamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al cancelar el pago');
        }
    }
    
    /**
     * Imprime recibo de pago
     */
    public function imprimirRecibo() {
        $this->requireAuth();
        $this->requirePermission('pagos.imprimir_recibo');
        
        $pagoId = $this->input('pago_id');
        
        if (!$pagoId) {
            $this->jsonError('Pago no especificado');
        }
        
        // TODO: Generar PDF de recibo
        $this->jsonSuccess(['url' => '/pdf/recibo-pago/' . $pagoId]);
    }
}
