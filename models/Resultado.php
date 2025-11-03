<?php
/**
 * Modelo Resultado
 * 
 * Maneja todas las operaciones relacionadas con captura de resultados
 */

require_once CORE_PATH . '/Model.php';

class Resultado extends Model {
    
    protected $table = 'orden_resultados';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'orden_estudio_id',
        'parametro_id',
        'valor_numerico',
        'valor_texto',
        'valor_opcion_id',
        'unidad_medida',
        'valor_referencia_min',
        'valor_referencia_max',
        'valor_referencia_texto',
        'fuera_rango',
        'valor_critico',
        'metodo_captura',
        'equipo_id',
        'usuario_captura_id',
        'observaciones'
    ];
    
    /**
     * Obtiene los resultados de un estudio de una orden
     * 
     * @param int $ordenEstudioId
     * @return array
     */
    public function obtenerPorOrdenEstudio($ordenEstudioId) {
        $sql = "SELECT r.*,
                       ep.nombre as parametro_nombre,
                       ep.codigo as parametro_codigo,
                       ep.tipo_parametro_id,
                       pt.clave as tipo_parametro,
                       po.valor as opcion_valor
                FROM {$this->table} r
                JOIN estudio_parametros ep ON r.parametro_id = ep.id
                JOIN parametro_tipos pt ON ep.tipo_parametro_id = pt.id
                LEFT JOIN parametro_opciones po ON r.valor_opcion_id = po.id
                WHERE r.orden_estudio_id = ?
                ORDER BY ep.orden";
        
        return $this->queryAll($sql, [$ordenEstudioId]);
    }
    
    /**
     * Guarda o actualiza un resultado
     * 
     * @param array $resultadoData
     * @return int
     */
    public function guardarResultado($resultadoData) {
        // Verificar si ya existe un resultado para este parámetro
        $sql = "SELECT id FROM {$this->table}
                WHERE orden_estudio_id = ? AND parametro_id = ?";
        
        $existe = $this->queryOne($sql, [
            $resultadoData['orden_estudio_id'],
            $resultadoData['parametro_id']
        ]);
        
        // Verificar si está fuera de rango o es crítico
        $resultadoData['fuera_rango'] = $this->verificarFueraDeRango($resultadoData);
        $resultadoData['valor_critico'] = $this->verificarValorCritico($resultadoData);
        
        if ($existe) {
            // Actualizar
            $this->update($existe['id'], $resultadoData);
            return $existe['id'];
        } else {
            // Crear nuevo
            return $this->create($resultadoData);
        }
    }
    
    /**
     * Verifica si un valor está fuera de rango
     * 
     * @param array $resultadoData
     * @return bool
     */
    private function verificarFueraDeRango($resultadoData) {
        if (empty($resultadoData['valor_numerico'])) {
            return false;
        }
        
        $min = $resultadoData['valor_referencia_min'];
        $max = $resultadoData['valor_referencia_max'];
        
        if ($min === null || $max === null) {
            return false;
        }
        
        $valor = $resultadoData['valor_numerico'];
        return $valor < $min || $valor > $max;
    }
    
    /**
     * Verifica si un valor es crítico
     * 
     * @param array $resultadoData
     * @return bool
     */
    private function verificarValorCritico($resultadoData) {
        if (empty($resultadoData['valor_numerico'])) {
            return false;
        }
        
        // Obtener valores críticos del parámetro
        $sql = "SELECT valor_critico_min, valor_critico_max
                FROM parametro_valores_referencia
                WHERE parametro_id = ?
                LIMIT 1";
        
        $valoresCriticos = $this->queryOne($sql, [$resultadoData['parametro_id']]);
        
        if (!$valoresCriticos) {
            return false;
        }
        
        $valor = $resultadoData['valor_numerico'];
        $criticoMin = $valoresCriticos['valor_critico_min'];
        $criticoMax = $valoresCriticos['valor_critico_max'];
        
        if ($criticoMin !== null && $valor <= $criticoMin) {
            return true;
        }
        
        if ($criticoMax !== null && $valor >= $criticoMax) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Valida técnicamente un estudio
     * 
     * @param int $ordenEstudioId
     * @param int $usuarioId
     * @param string|null $observaciones
     * @return bool
     */
    public function validarTecnico($ordenEstudioId, $usuarioId, $observaciones = null) {
        $this->db->beginTransaction();
        
        try {
            // Actualizar estatus del estudio
            $sql = "UPDATE orden_estudios
                    SET estatus = ?,
                        fecha_validacion = NOW(),
                        usuario_validacion_id = ?
                    WHERE id = ?";
            
            $this->query($sql, [ESTUDIO_VALIDADO, $usuarioId, $ordenEstudioId]);
            
            // Registrar en validaciones
            $sql = "INSERT INTO orden_estudio_validaciones
                    (orden_estudio_id, tipo_validacion, usuario_id, observaciones)
                    VALUES (?, ?, ?, ?)";
            
            $this->query($sql, [$ordenEstudioId, VALIDACION_TECNICA, $usuarioId, $observaciones]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Libera médicamente un estudio
     * 
     * @param int $ordenEstudioId
     * @param int $usuarioId
     * @param string|null $observaciones
     * @return bool
     */
    public function liberarMedico($ordenEstudioId, $usuarioId, $observaciones = null) {
        $this->db->beginTransaction();
        
        try {
            // Actualizar estatus del estudio
            $sql = "UPDATE orden_estudios
                    SET estatus = ?,
                        fecha_liberacion = NOW(),
                        usuario_liberacion_id = ?
                    WHERE id = ?";
            
            $this->query($sql, [ESTUDIO_LIBERADO, $usuarioId, $ordenEstudioId]);
            
            // Registrar en validaciones
            $sql = "INSERT INTO orden_estudio_validaciones
                    (orden_estudio_id, tipo_validacion, usuario_id, observaciones)
                    VALUES (?, ?, ?, ?)";
            
            $this->query($sql, [$ordenEstudioId, VALIDACION_MEDICA, $usuarioId, $observaciones]);
            
            // Verificar si todos los estudios están liberados para actualizar estatus de orden
            $this->verificarEstadoOrden($ordenEstudioId);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Verifica y actualiza el estado de la orden basado en sus estudios
     * 
     * @param int $ordenEstudioId
     */
    private function verificarEstadoOrden($ordenEstudioId) {
        // Obtener la orden
        $sql = "SELECT orden_id FROM orden_estudios WHERE id = ?";
        $result = $this->queryOne($sql, [$ordenEstudioId]);
        $ordenId = $result['orden_id'];
        
        // Verificar si todos los estudios están liberados
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN estatus = ? THEN 1 ELSE 0 END) as liberados
                FROM orden_estudios
                WHERE orden_id = ? AND cancelado = 0";
        
        $estadoEstudios = $this->queryOne($sql, [ESTUDIO_LIBERADO, $ordenId]);
        
        if ($estadoEstudios['total'] == $estadoEstudios['liberados']) {
            // Todos liberados, cambiar estatus de orden
            $sql = "UPDATE ordenes SET estatus = ? WHERE id = ?";
            $this->query($sql, [ORDEN_LIBERADA, $ordenId]);
        }
    }
    
    /**
     * Obtiene órdenes pendientes de captura por área
     * 
     * @param int $areaId
     * @param int|null $sucursalId
     * @return array
     */
    public function obtenerPendientesPorArea($areaId, $sucursalId = null) {
        $sql = "SELECT DISTINCT o.id, o.folio, o.fecha_registro, o.prioridad,
                       CONCAT(p.nombres, ' ', p.apellido_paterno) as paciente_nombre,
                       p.expediente,
                       COUNT(DISTINCT oe.id) as total_estudios,
                       SUM(CASE WHEN oe.estatus = ? THEN 1 ELSE 0 END) as estudios_pendientes
                FROM ordenes o
                JOIN pacientes p ON o.paciente_id = p.id
                JOIN orden_estudios oe ON o.id = oe.orden_id
                JOIN estudios e ON oe.estudio_id = e.id
                WHERE e.area_id = ?
                AND oe.estatus IN (?, ?)
                AND oe.cancelado = 0";
        
        $params = [ESTUDIO_PENDIENTE, $areaId, ESTUDIO_PENDIENTE, ESTUDIO_CAPTURADO];
        
        if ($sucursalId) {
            $sql .= " AND o.sucursal_id = ?";
            $params[] = $sucursalId;
        }
        
        $sql .= " GROUP BY o.id
                  ORDER BY o.prioridad DESC, o.fecha_registro ASC";
        
        return $this->queryAll($sql, $params);
    }
}
