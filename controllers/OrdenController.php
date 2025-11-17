<?php
/**
 * OrdenController
 * 
 * Controlador para gestión de órdenes de servicio
 */

require_once CORE_PATH . '/Controller.php';
require_once MODELS_PATH . '/Orden.php';
require_once MODELS_PATH . '/Paciente.php';
require_once MODELS_PATH . '/Estudio.php';

class OrdenController extends Controller {
    
    private $ordenModel;
    private $pacienteModel;
    private $estudioModel;
    
    public function __construct() {
        $this->ordenModel = new Orden();
        $this->pacienteModel = new Paciente();
        $this->estudioModel = new Estudio();
    }
    
    /**
     * Lista de órdenes
     */
    public function index() {
        $this->requireAuth();
        $this->requirePermission('ordenes.ver');
        
        $sucursalId = $_SESSION['sucursal_id'];
        
        // Obtener filtros
        $filtros = [
            'fecha_inicio' => $this->input('fecha_inicio', date('Y-m-d')),
            'fecha_fin' => $this->input('fecha_fin', date('Y-m-d')),
            'estatus' => $this->input('estatus'),
            'folio' => $this->input('folio'),
            'paciente' => $this->input('paciente')
        ];
        
        $ordenes = $this->ordenModel->obtenerPorSucursal($sucursalId, $filtros);
        
        $data = [
            'ordenes' => $ordenes,
            'filtros' => $filtros,
            'titulo' => 'Órdenes de Servicio'
        ];
        
        $this->view('ordenes/index', $data);
    }
    
    /**
     * Muestra formulario para crear orden
     */
    public function crear() {
        $this->requireAuth();
        $this->requirePermission('ordenes.crear');
        
        if ($this->isPost()) {
            return $this->guardar();
        }
        
        $pacienteId = $this->input('paciente_id');
        $paciente = null;
        
        if ($pacienteId) {
            $paciente = $this->pacienteModel->obtenerConDetalles($pacienteId);
        }
        
        $data = [
            'paciente' => $paciente,
            'titulo' => 'Nueva Orden'
        ];
        
        $this->view('ordenes/crear', $data);
    }
    
    /**
     * Guarda una nueva orden
     */
    public function guardar() {
        $this->requireAuth();
        $this->requirePermission('ordenes.crear');
        
        if (!$this->isPost()) {
            $this->jsonError('Método no permitido', 405);
        }
        
        // Obtener datos de la orden
        $ordenData = [
            'paciente_id' => $this->input('paciente_id'),
            'sucursal_id' => $_SESSION['sucursal_id'],
            'usuario_registro_id' => Auth::id(),
            'lista_precio_id' => $this->input('lista_precio_id', 1),
            'medico_solicitante' => $this->input('medico_solicitante'),
            'diagnostico' => $this->input('diagnostico'),
            'prioridad' => $this->input('prioridad', PRIORIDAD_NORMAL),
            'descuento_monto' => $this->input('descuento_monto', 0),
            'descuento_porcentaje' => $this->input('descuento_porcentaje', 0),
            'cargo_monto' => $this->input('cargo_monto', 0),
            'cargo_descripcion' => $this->input('cargo_descripcion'),
            'notas' => $this->input('notas')
        ];
        
        // Obtener estudios
        $estudiosInput = $this->input('estudios');
        
        if (empty($estudiosInput) || !is_array($estudiosInput)) {
            $this->jsonError('Debe agregar al menos un estudio');
        }
        
        $estudios = [];
        foreach ($estudiosInput as $estudio) {
            $estudios[] = [
                'estudio_id' => $estudio['estudio_id'],
                'codigo_estudio' => $estudio['codigo'],
                'nombre_estudio' => $estudio['nombre'],
                'precio_unitario' => $estudio['precio'],
                'cantidad' => $estudio['cantidad'] ?? 1,
                'descuento_porcentaje' => $estudio['descuento'] ?? 0
            ];
        }
        
        try {
            $this->ordenModel->db->beginTransaction();
            
            $ordenId = $this->ordenModel->crearOrden($ordenData, $estudios);
            
            // Agregar indicaciones
            $this->agregarIndicaciones($ordenId, $estudiosInput);
            
            $this->ordenModel->db->commit();
            
            // Obtener la orden completa
            $orden = $this->ordenModel->obtenerConDetalles($ordenId);
            
            $this->jsonSuccess([
                'orden_id' => $ordenId,
                'folio' => $orden['folio']
            ], 'Orden creada exitosamente');
            
        } catch (Exception $e) {
            $this->ordenModel->db->rollBack();
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al crear la orden: ' . $e->getMessage());
        }
    }
    
    /**
     * Agrega indicaciones de estudios a la orden
     */
    private function agregarIndicaciones($ordenId, $estudios) {
        foreach ($estudios as $estudio) {
            $estudioId = $estudio['estudio_id'];
            
            // Obtener indicaciones del catálogo
            $indicaciones = $this->estudioModel->obtenerIndicaciones($estudioId);
            
            foreach ($indicaciones as $ind) {
                $sql = "INSERT INTO orden_indicaciones 
                        (orden_id, estudio_id, indicacion, tipo, orden_impresion)
                        VALUES (?, ?, ?, ?, ?)";
                
                $this->ordenModel->query($sql, [
                    $ordenId,
                    $estudioId,
                    $ind['indicacion'],
                    $ind['tipo'],
                    $ind['orden']
                ]);
            }
        }
    }
    
    /**
     * Muestra detalles de una orden
     */
    public function ver() {
        $this->requireAuth();
        $this->requirePermission('ordenes.ver');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->redirectWith('/laboratorio-clinico/public/ordenes', 'Orden no encontrada', 'error');
        }
        
        $orden = $this->ordenModel->obtenerConDetalles($id);
        
        if (!$orden) {
            $this->redirectWith('/laboratorio-clinico/public/ordenes', 'Orden no encontrada', 'error');
        }
        
        $data = [
            'orden' => $orden,
            'titulo' => 'Detalle de Orden - ' . formatFolio($orden['folio'])
        ];
        
        $this->view('ordenes/ver', $data);
    }
    
    /**
     * Cancela una orden
     */
    public function cancelar() {
        $this->requireAuth();
        $this->requirePermission('ordenes.cancelar');
        
        $id = $this->input('id');
        $motivo = $this->input('motivo');
        
        if (!$id || !$motivo) {
            $this->jsonError('Datos incompletos');
        }
        
        try {
            $this->ordenModel->cancelarOrden($id, $motivo, Auth::id());
            
            Auth::logAudit(
                Auth::id(),
                'cancelar_orden',
                'ordenes',
                'orden',
                $id,
                null,
                ['motivo' => $motivo],
                'Orden cancelada'
            );
            
            $this->jsonSuccess(null, 'Orden cancelada exitosamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al cancelar la orden');
        }
    }
    
    /**
     * Busca estudios (AJAX)
     */
    public function buscarEstudios() {
        $this->requireAuth();
        
        $termino = $this->input('q', '');
        
        if (strlen($termino) < 2) {
            $this->jsonSuccess([]);
        }
        
        $estudios = $this->estudioModel->buscar($termino, 20);
        
        // Formatear para select2
        $resultados = array_map(function($e) {
            return [
                'id' => $e['id'],
                'text' => $e['codigo_interno'] . ' - ' . $e['nombre'],
                'codigo' => $e['codigo_interno'],
                'nombre' => $e['nombre'],
                'area' => $e['area_nombre'],
                'tipo_muestra' => $e['tipo_muestra_nombre']
            ];
        }, $estudios);
        
        $this->jsonSuccess($resultados);
    }
    
    /**
     * Obtiene el precio de un estudio (AJAX)
     */
    public function obtenerPrecioEstudio() {
        $this->requireAuth();
        
        $estudioId = $this->input('estudio_id');
        $listaPrecioId = $this->input('lista_precio_id', 1);
        
        if (!$estudioId) {
            $this->jsonError('Estudio no especificado');
        }
        
        $precio = $this->estudioModel->obtenerPrecio($estudioId, $listaPrecioId);
        
        if ($precio === null) {
            $this->jsonError('Precio no encontrado');
        }
        
        $this->jsonSuccess(['precio' => $precio]);
    }
    
    /**
     * Imprime etiquetas de la orden
     */
    public function imprimirEtiquetas() {
        $this->requireAuth();
        $this->requirePermission('ordenes.imprimir_etiquetas');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->jsonError('Orden no especificada');
        }
        
        $orden = $this->ordenModel->obtenerConDetalles($id);
        
        if (!$orden) {
            $this->jsonError('Orden no encontrada');
        }
        
        // TODO: Generar PDF de etiquetas
        $this->jsonSuccess(['url' => '/pdf/etiquetas/' . $id]);
    }
    
    /**
     * Imprime la orden de trabajo
     */
    public function imprimirOrden() {
        $this->requireAuth();
        $this->requirePermission('ordenes.imprimir_orden');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->jsonError('Orden no especificada');
        }
        
        $orden = $this->ordenModel->obtenerConDetalles($id);
        
        if (!$orden) {
            $this->jsonError('Orden no encontrada');
        }
        
        // TODO: Generar PDF de orden
        $this->jsonSuccess(['url' => '/pdf/orden/' . $id]);
    }
    
    /**
     * Imprime el recibo
     */
    public function imprimirRecibo() {
        $this->requireAuth();
        $this->requirePermission('ordenes.imprimir_recibo');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->jsonError('Orden no especificada');
        }
        
        $orden = $this->ordenModel->obtenerConDetalles($id);
        
        if (!$orden) {
            $this->jsonError('Orden no encontrada');
        }
        
        // TODO: Generar PDF de recibo
        $this->jsonSuccess(['url' => '/pdf/recibo/' . $id]);
    }
}
