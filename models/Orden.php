<?php
/**
 * Modelo Orden
 * 
 * Maneja todas las operaciones relacionadas con órdenes de servicio
 */

require_once CORE_PATH . '/Model.php';

class Orden extends Model {
    
    protected $table = 'ordenes';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'folio',
        'paciente_id',
        'sucursal_id',
        'fecha_registro',
        'fecha_toma_muestra',
        'usuario_registro_id',
        'procedencia_tipo',
        'procedencia_id',
        'medico_solicitante',
        'diagnostico',
        'lista_precio_id',
        'subtotal',
        'descuento_monto',
        'descuento_porcentaje',
        'cargo_monto',
        'cargo_descripcion',
        'total',
        'total_pagado',
        'saldo',
        'estatus',
        'estatus_pago',
        'prioridad',
        'notas'
    ];
    
    /**
     * Genera un nuevo folio para la orden
     * 
     * @param int $sucursalId
     * @return string
     */
    public function generarFolio($sucursalId) {
        $fecha = date('Ymd');
        $sucursal = str_pad($sucursalId, 2, '0', STR_PAD_LEFT);
        
        // Obtener el último consecutivo del día
        $sql = "SELECT ultimo_consecutivo FROM folios_control 
                WHERE sucursal_id = ? AND fecha = CURDATE()";
        $result = $this->queryOne($sql, [$sucursalId]);
        
        if ($result) {
            // Incrementar consecutivo
            $consecutivo = $result['ultimo_consecutivo'] + 1;
            $sql = "UPDATE folios_control SET ultimo_consecutivo = ? 
                    WHERE sucursal_id = ? AND fecha = CURDATE()";
            $this->query($sql, [$consecutivo, $sucursalId]);
        } else {
            // Primer folio del día
            $consecutivo = 1;
            $sql = "INSERT INTO folios_control (sucursal_id, fecha, ultimo_consecutivo) 
                    VALUES (?, CURDATE(), ?)";
            $this->query($sql, [$sucursalId, $consecutivo]);
        }
        
        $consecutivoStr = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
        return $fecha . $sucursal . $consecutivoStr;
    }
    
    /**
     * Crea una nueva orden con todos sus detalles
     * 
     * @param array $ordenData
     * @param array $estudios
     * @return int ID de la orden creada
     */
    public function crearOrden($ordenData, $estudios) {
        $this->db->beginTransaction();
        
        try {
            // Generar folio
            $ordenData['folio'] = $this->generarFolio($ordenData['sucursal_id']);
            $ordenData['fecha_registro'] = date('Y-m-d H:i:s');
            $ordenData['estatus'] = ORDEN_REGISTRADA;
            
            // Crear la orden
            $ordenId = $this->create($ordenData);
            
            // Agregar estudios
            foreach ($estudios as $estudio) {
                $this->agregarEstudio($ordenId, $estudio);
            }
            
            // Actualizar totales
            $this->actualizarTotales($ordenId);
            
            $this->db->commit();
            return $ordenId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Agrega un estudio a la orden
     * 
     * @param int $ordenId
     * @param array $estudioData
     * @return int ID del estudio agregado
     */
    public function agregarEstudio($ordenId, $estudioData) {
        $sql = "INSERT INTO orden_estudios (
                    orden_id, estudio_id, codigo_estudio, nombre_estudio,
                    precio_unitario, cantidad, descuento_porcentaje, subtotal,
                    estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $cantidad = $estudioData['cantidad'] ?? 1;
        $descuento = $estudioData['descuento_porcentaje'] ?? 0;
        $precioUnitario = $estudioData['precio_unitario'];
        
        $subtotal = $precioUnitario * $cantidad;
        $subtotal = $subtotal - ($subtotal * $descuento / 100);
        
        $this->query($sql, [
            $ordenId,
            $estudioData['estudio_id'],
            $estudioData['codigo_estudio'],
            $estudioData['nombre_estudio'],
            $precioUnitario,
            $cantidad,
            $descuento,
            $subtotal,
            ESTUDIO_PENDIENTE
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Actualiza los totales de la orden
     * 
     * @param int $ordenId
     * @return bool
     */
    public function actualizarTotales($ordenId) {
        $sql = "UPDATE ordenes o
                SET o.subtotal = (
                    SELECT COALESCE(SUM(subtotal), 0) 
                    FROM orden_estudios 
                    WHERE orden_id = ? AND cancelado = 0
                ),
                o.total = o.subtotal - o.descuento_monto + o.cargo_monto,
                o.saldo = o.total - o.total_pagado,
                o.estatus_pago = CASE
                    WHEN o.total - o.total_pagado = 0 THEN 'pagado'
                    WHEN o.total_pagado > 0 THEN 'parcial'
                    ELSE 'pendiente'
                END
                WHERE o.id = ?";
        
        $this->query($sql, [$ordenId, $ordenId]);
        return true;
    }
    
    /**
     * Obtiene una orden con todos sus detalles
     * 
     * @param int $ordenId
     * @return array|null
     */
    public function obtenerConDetalles($ordenId) {
        $sql = "SELECT o.*, 
                       p.nombres, p.apellido_paterno, p.apellido_materno,
                       p.fecha_nacimiento, p.sexo, p.expediente,
                       TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
                       TIMESTAMPDIFF(MONTH, p.fecha_nacimiento, CURDATE()) as edad_meses,
                       s.nombre as sucursal_nombre,
                       u.nombres as usuario_nombres,
                       u.apellido_paterno as usuario_apellido
                FROM ordenes o
                JOIN pacientes p ON o.paciente_id = p.id
                JOIN sucursales s ON o.sucursal_id = s.id
                JOIN usuarios u ON o.usuario_registro_id = u.id
                WHERE o.id = ?";
        
        $orden = $this->queryOne($sql, [$ordenId]);
        
        if ($orden) {
            // Obtener estudios de la orden
            $sql = "SELECT oe.*, e.nombre as estudio_nombre, 
                           e.codigo_interno, e.area_id,
                           a.nombre as area_nombre, a.color as area_color
                    FROM orden_estudios oe
                    JOIN estudios e ON oe.estudio_id = e.id
                    JOIN areas a ON e.area_id = a.id
                    WHERE oe.orden_id = ? AND oe.cancelado = 0
                    ORDER BY a.orden, e.nombre";
            
            $orden['estudios'] = $this->queryAll($sql, [$ordenId]);
            
            // Obtener indicaciones
            $sql = "SELECT * FROM orden_indicaciones 
                    WHERE orden_id = ? 
                    ORDER BY orden_impresion";
            $orden['indicaciones'] = $this->queryAll($sql, [$ordenId]);
            
            // Obtener pagos
            $sql = "SELECT * FROM pagos 
                    WHERE orden_id = ? AND cancelado = 0
                    ORDER BY fecha_pago";
            $orden['pagos'] = $this->queryAll($sql, [$ordenId]);
        }
        
        return $orden;
    }
    
    /**
     * Obtiene órdenes por sucursal y filtros
     * 
     * @param int $sucursalId
     * @param array $filtros
     * @return array
     */
    public function obtenerPorSucursal($sucursalId, $filtros = []) {
        $where = ["o.sucursal_id = ?"];
        $params = [$sucursalId];
        
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "DATE(o.fecha_registro) >= ?";
            $params[] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "DATE(o.fecha_registro) <= ?";
            $params[] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['estatus'])) {
            $where[] = "o.estatus = ?";
            $params[] = $filtros['estatus'];
        }
        
        if (!empty($filtros['estatus_pago'])) {
            $where[] = "o.estatus_pago = ?";
            $params[] = $filtros['estatus_pago'];
        }
        
        if (!empty($filtros['folio'])) {
            $where[] = "o.folio LIKE ?";
            $params[] = "%{$filtros['folio']}%";
        }
        
        if (!empty($filtros['paciente'])) {
            $where[] = "(p.nombres LIKE ? OR p.apellido_paterno LIKE ? OR p.expediente LIKE ?)";
            $busqueda = "%{$filtros['paciente']}%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        $sql = "SELECT o.*,
                       CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as paciente_nombre,
                       p.expediente,
                       COUNT(DISTINCT oe.id) as total_estudios
                FROM ordenes o
                JOIN pacientes p ON o.paciente_id = p.id
                LEFT JOIN orden_estudios oe ON o.id = oe.orden_id AND oe.cancelado = 0
                WHERE " . implode(' AND ', $where) . "
                GROUP BY o.id
                ORDER BY o.fecha_registro DESC";
        
        return $this->queryAll($sql, $params);
    }
    
    /**
     * Cancela una orden
     * 
     * @param int $ordenId
     * @param string $motivo
     * @param int $usuarioId
     * @return bool
     */
    public function cancelarOrden($ordenId, $motivo, $usuarioId) {
        $sql = "UPDATE ordenes 
                SET estatus = ?, 
                    fecha_cancelacion = NOW(),
                    motivo_cancelacion = ?,
                    usuario_cancelacion_id = ?
                WHERE id = ?";
        
        $this->query($sql, [ORDEN_CANCELADA, $motivo, $usuarioId, $ordenId]);
        return true;
    }
    
    /**
     * Cambia el estatus de una orden
     * 
     * @param int $ordenId
     * @param string $nuevoEstatus
     * @return bool
     */
    public function cambiarEstatus($ordenId, $nuevoEstatus) {
        return $this->update($ordenId, ['estatus' => $nuevoEstatus]);
    }
}
