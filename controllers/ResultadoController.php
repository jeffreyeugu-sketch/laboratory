<?php
/**
 * ResultadoController
 * 
 * Controlador para captura y gestión de resultados
 */

require_once CORE_PATH . '/Controller.php';
require_once MODELS_PATH . '/Resultado.php';
require_once MODELS_PATH . '/Orden.php';
require_once MODELS_PATH . '/Estudio.php';

class ResultadoController extends Controller {
    
    private $resultadoModel;
    private $ordenModel;
    private $estudioModel;
    
    public function __construct() {
        $this->resultadoModel = new Resultado();
        $this->ordenModel = new Orden();
        $this->estudioModel = new Estudio();
    }
    
    /**
     * Lista de trabajo - órdenes pendientes
     */
    public function index() {
        $this->requireAuth();
        $this->requirePermission('resultados.ver');
        
        $areaId = $_SESSION['area_id'] ?? null;
        $sucursalId = $_SESSION['sucursal_id'];
        
        $pendientes = [];
        
        if ($areaId) {
            $pendientes = $this->resultadoModel->obtenerPendientesPorArea($areaId, $sucursalId);
        }
        
        $data = [
            'pendientes' => $pendientes,
            'titulo' => 'Lista de Trabajo'
        ];
        
        $this->view('resultados/index', $data);
    }
    
    /**
     * Captura de resultados estándar
     */
    public function capturar() {
        $this->requireAuth();
        $this->requirePermission('resultados.capturar');
        
        $ordenEstudioId = $this->input('orden_estudio_id');
        
        if (!$ordenEstudioId) {
            $this->redirectWith('/laboratorio-clinico/public/resultados', 'Estudio no especificado', 'error');
        }
        
        if ($this->isPost()) {
            return $this->guardar();
        }
        
        // Obtener información del estudio
        $sql = "SELECT oe.*, e.nombre as estudio_nombre,
                       o.folio, o.paciente_id,
                       CONCAT(p.nombres, ' ', p.apellido_paterno) as paciente_nombre,
                       p.expediente, p.fecha_nacimiento, p.sexo,
                       TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
                       TIMESTAMPDIFF(MONTH, p.fecha_nacimiento, CURDATE()) as edad_meses
                FROM orden_estudios oe
                JOIN ordenes o ON oe.orden_id = o.id
                JOIN pacientes p ON o.paciente_id = p.id
                JOIN estudios e ON oe.estudio_id = e.id
                WHERE oe.id = ?";
        
        $ordenEstudio = $this->ordenModel->queryOne($sql, [$ordenEstudioId]);
        
        if (!$ordenEstudio) {
            $this->redirectWith('/laboratorio-clinico/public/resultados', 'Estudio no encontrado', 'error');
        }
        
        // Obtener parámetros del estudio
        $parametros = $this->estudioModel->obtenerParametros($ordenEstudio['estudio_id']);
        
        // Obtener resultados existentes
        $resultadosExistentes = $this->resultadoModel->obtenerPorOrdenEstudio($ordenEstudioId);
        
        // Mapear resultados por parametro_id
        $resultadosMap = [];
        foreach ($resultadosExistentes as $r) {
            $resultadosMap[$r['parametro_id']] = $r;
        }
        
        // Agregar valores de referencia según edad y sexo
        foreach ($parametros as &$parametro) {
            $valorRef = $this->obtenerValorReferenciaAplicable(
                $parametro['valores_referencia'],
                $ordenEstudio['sexo'],
                $ordenEstudio['edad_meses']
            );
            
            $parametro['valor_referencia_aplicable'] = $valorRef;
            $parametro['resultado_existente'] = $resultadosMap[$parametro['id']] ?? null;
        }
        
        $data = [
            'ordenEstudio' => $ordenEstudio,
            'parametros' => $parametros,
            'titulo' => 'Capturar Resultados'
        ];
        
        $this->view('resultados/capturar', $data);
    }
    
    /**
     * Obtiene el valor de referencia aplicable según edad y sexo
     */
    private function obtenerValorReferenciaAplicable($valoresRef, $sexo, $edadMeses) {
        if (empty($valoresRef)) {
            return null;
        }
        
        foreach ($valoresRef as $vr) {
            // Verificar sexo
            if ($vr['sexo'] && $vr['sexo'] !== $sexo) {
                continue;
            }
            
            // Verificar edad
            if ($vr['edad_min'] !== null && $edadMeses < $vr['edad_min']) {
                continue;
            }
            
            if ($vr['edad_max'] !== null && $edadMeses > $vr['edad_max']) {
                continue;
            }
            
            return $vr;
        }
        
        return $valoresRef[0] ?? null;
    }
    
    /**
     * Guarda los resultados
     */
    public function guardar() {
        $this->requireAuth();
        $this->requirePermission('resultados.capturar');
        
        if (!$this->isPost()) {
            $this->jsonError('Método no permitido', 405);
        }
        
        $ordenEstudioId = $this->input('orden_estudio_id');
        $resultados = $this->input('resultados');
        
        if (!$ordenEstudioId || !is_array($resultados)) {
            $this->jsonError('Datos incompletos');
        }
        
        try {
            $this->resultadoModel->db->beginTransaction();
            
            foreach ($resultados as $parametroId => $valor) {
                $parametro = $this->obtenerParametro($parametroId);
                
                if (!$parametro) {
                    continue;
                }
                
                $resultadoData = [
                    'orden_estudio_id' => $ordenEstudioId,
                    'parametro_id' => $parametroId,
                    'unidad_medida' => $parametro['unidad_medida'],
                    'metodo_captura' => CAPTURA_MANUAL,
                    'usuario_captura_id' => Auth::id()
                ];
                
                // Asignar valor según tipo
                if ($parametro['tipo_clave'] === 'numerico' || $parametro['tipo_clave'] === 'decimal') {
                    $resultadoData['valor_numerico'] = $valor;
                } elseif ($parametro['tipo_clave'] === 'opcion_multiple') {
                    $resultadoData['valor_opcion_id'] = $valor;
                } else {
                    $resultadoData['valor_texto'] = $valor;
                }
                
                // Agregar valores de referencia
                $valorRef = $this->input("valor_ref_{$parametroId}");
                if ($valorRef) {
                    $ref = json_decode($valorRef, true);
                    $resultadoData['valor_referencia_min'] = $ref['min'] ?? null;
                    $resultadoData['valor_referencia_max'] = $ref['max'] ?? null;
                    $resultadoData['valor_referencia_texto'] = $ref['texto'] ?? null;
                }
                
                $this->resultadoModel->guardarResultado($resultadoData);
            }
            
            // Actualizar estatus del estudio a "capturado"
            $sql = "UPDATE orden_estudios 
                    SET estatus = ?, fecha_captura = NOW(), usuario_captura_id = ?
                    WHERE id = ?";
            $this->ordenModel->query($sql, [ESTUDIO_CAPTURADO, Auth::id(), $ordenEstudioId]);
            
            $this->resultadoModel->db->commit();
            
            $this->jsonSuccess(null, 'Resultados guardados exitosamente');
            
        } catch (Exception $e) {
            $this->resultadoModel->db->rollBack();
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al guardar los resultados');
        }
    }
    
    /**
     * Obtiene información de un parámetro
     */
    private function obtenerParametro($parametroId) {
        $sql = "SELECT ep.*, pt.clave as tipo_clave
                FROM estudio_parametros ep
                JOIN parametro_tipos pt ON ep.tipo_parametro_id = pt.id
                WHERE ep.id = ?";
        
        return $this->estudioModel->queryOne($sql, [$parametroId]);
    }
    
    /**
     * Validación técnica
     */
    public function validar() {
        $this->requireAuth();
        $this->requirePermission('resultados.validar_tecnico');
        
        $ordenEstudioId = $this->input('orden_estudio_id');
        $observaciones = $this->input('observaciones');
        
        if (!$ordenEstudioId) {
            $this->jsonError('Estudio no especificado');
        }
        
        try {
            $this->resultadoModel->validarTecnico($ordenEstudioId, Auth::id(), $observaciones);
            
            $this->jsonSuccess(null, 'Estudio validado técnicamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al validar el estudio');
        }
    }
    
    /**
     * Liberación médica
     */
    public function liberar() {
        $this->requireAuth();
        $this->requirePermission('resultados.liberar_medico');
        
        $ordenEstudioId = $this->input('orden_estudio_id');
        $observaciones = $this->input('observaciones');
        
        if (!$ordenEstudioId) {
            $this->jsonError('Estudio no especificado');
        }
        
        try {
            $this->resultadoModel->liberarMedico($ordenEstudioId, Auth::id(), $observaciones);
            
            $this->jsonSuccess(null, 'Estudio liberado médicamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al liberar el estudio');
        }
    }
    
    /**
     * Captura de microbiología (cultivos)
     */
    public function microbiologia() {
        $this->requireAuth();
        $this->requirePermission('resultados.capturar');
        
        $ordenEstudioId = $this->input('orden_estudio_id');
        
        if (!$ordenEstudioId) {
            $this->redirectWith('/laboratorio-clinico/public/resultados', 'Estudio no especificado', 'error');
        }
        
        // Obtener información del estudio
        $sql = "SELECT oe.*, e.nombre as estudio_nombre,
                       o.folio, o.paciente_id,
                       CONCAT(p.nombres, ' ', p.apellido_paterno) as paciente_nombre,
                       p.expediente
                FROM orden_estudios oe
                JOIN ordenes o ON oe.orden_id = o.id
                JOIN pacientes p ON o.paciente_id = p.id
                JOIN estudios e ON oe.estudio_id = e.id
                WHERE oe.id = ?";
        
        $ordenEstudio = $this->ordenModel->queryOne($sql, [$ordenEstudioId]);
        
        if (!$ordenEstudio) {
            $this->redirectWith('/laboratorio-clinico/public/resultados', 'Estudio no encontrado', 'error');
        }
        
        // Obtener cultivo existente si hay
        $sql = "SELECT * FROM resultado_cultivo WHERE orden_estudio_id = ?";
        $cultivo = $this->resultadoModel->queryOne($sql, [$ordenEstudioId]);
        
        // Obtener catálogo de microorganismos
        $sql = "SELECT * FROM catalogo_microorganismos WHERE activo = 1 ORDER BY frecuente DESC, nombre_cientifico";
        $microorganismos = $this->resultadoModel->queryAll($sql);
        
        // Obtener catálogo de antibióticos
        $sql = "SELECT * FROM catalogo_antibioticos WHERE activo = 1 ORDER BY familia, orden";
        $antibioticos = $this->resultadoModel->queryAll($sql);
        
        $data = [
            'ordenEstudio' => $ordenEstudio,
            'cultivo' => $cultivo,
            'microorganismos' => $microorganismos,
            'antibioticos' => $antibioticos,
            'titulo' => 'Microbiología - Cultivos y Antibiograma'
        ];
        
        $this->view('resultados/microbiologia', $data);
    }
    
    /**
     * Imprime reporte de resultados
     */
    public function imprimir() {
        $this->requireAuth();
        $this->requirePermission('resultados.imprimir');
        
        $ordenId = $this->input('orden_id');
        
        if (!$ordenId) {
            $this->jsonError('Orden no especificada');
        }
        
        // TODO: Generar PDF de resultados
        $this->jsonSuccess(['url' => '/pdf/resultados/' . $ordenId]);
    }
}
