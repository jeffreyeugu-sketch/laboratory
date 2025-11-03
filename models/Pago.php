<?php
/**
 * Modelo Pago
 * 
 * Maneja todas las operaciones relacionadas con pagos
 */

require_once CORE_PATH . '/Model.php';

class Pago extends Model {
    
    protected $table = 'pagos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'orden_id',
        'folio_pago',
        'fecha_pago',
        'monto',
        'forma_pago_id',
        'referencia',
        'banco',
        'usuario_registro_id',
        'sucursal_id',
        'caja_id',
        'turno_id',
        'notas',
        'recibo_impreso'
    ];
    
    /**
     * Genera un folio de pago
     * 
     * @param int $sucursalId
     * @return string
     */
    public function generarFolioPago($sucursalId) {
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
        return "PAG-{$fecha}{$sucursal}{$consecutivoStr}";
    }
    
    /**
     * Registra un pago
     * 
     * @param array $pagoData
     * @param array|null $formasPago Para pagos múltiples
     * @return int ID del pago
     */
    public function registrarPago($pagoData, $formasPago = null) {
        $this->db->beginTransaction();
        
        try {
            // Generar folio de pago
            $pagoData['folio_pago'] = $this->generarFolioPago($pagoData['sucursal_id']);
            $pagoData['fecha_pago'] = date('Y-m-d H:i:s');
            
            // Crear el pago
            $pagoId = $this->create($pagoData);
            
            // Si hay múltiples formas de pago
            if ($formasPago && is_array($formasPago)) {
                foreach ($formasPago as $forma) {
                    $this->registrarFormaPagoMultiple($pagoId, $forma);
                }
            }
            
            // Actualizar totales de la orden
            $this->actualizarTotalesOrden($pagoData['orden_id']);
            
            $this->db->commit();
            return $pagoId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Registra una forma de pago en pago múltiple
     * 
     * @param int $pagoId
     * @param array $forma
     */
    private function registrarFormaPagoMultiple($pagoId, $forma) {
        $sql = "INSERT INTO pago_formas_multiple 
                (pago_id, forma_pago_id, monto, referencia, banco)
                VALUES (?, ?, ?, ?, ?)";
        
        $this->query($sql, [
            $pagoId,
            $forma['forma_pago_id'],
            $forma['monto'],
            $forma['referencia'] ?? null,
            $forma['banco'] ?? null
        ]);
    }
    
    /**
     * Actualiza los totales de la orden después de un pago
     * 
     * @param int $ordenId
     */
    private function actualizarTotalesOrden($ordenId) {
        $sql = "UPDATE ordenes o
                SET o.total_pagado = (
                    SELECT COALESCE(SUM(monto), 0)
                    FROM pagos
                    WHERE orden_id = ? AND cancelado = 0
                ),
                o.saldo = o.total - o.total_pagado,
                o.estatus_pago = CASE
                    WHEN o.total - o.total_pagado = 0 THEN ?
                    WHEN o.total_pagado > 0 THEN ?
                    ELSE ?
                END
                WHERE o.id = ?";
        
        $this->query($sql, [
            $ordenId,
            PAGO_PAGADO,
            PAGO_PARCIAL,
            PAGO_PENDIENTE,
            $ordenId
        ]);
    }
    
    /**
     * Obtiene el historial de pagos de una orden
     * 
     * @param int $ordenId
     * @return array
     */
    public function obtenerPorOrden($ordenId) {
        $sql = "SELECT p.*,
                       fp.nombre as forma_pago_nombre,
                       u.nombres as usuario_nombres,
                       u.apellido_paterno as usuario_apellido
                FROM {$this->table} p
                JOIN formas_pago fp ON p.forma_pago_id = fp.id
                JOIN usuarios u ON p.usuario_registro_id = u.id
                WHERE p.orden_id = ? AND p.cancelado = 0
                ORDER BY p.fecha_pago DESC";
        
        $pagos = $this->queryAll($sql, [$ordenId]);
        
        // Para cada pago, obtener formas múltiples si las hay
        foreach ($pagos as &$pago) {
            $sql = "SELECT pfm.*,
                           fp.nombre as forma_pago_nombre
                    FROM pago_formas_multiple pfm
                    JOIN formas_pago fp ON pfm.forma_pago_id = fp.id
                    WHERE pfm.pago_id = ?";
            
            $pago['formas_multiple'] = $this->queryAll($sql, [$pago['id']]);
        }
        
        return $pagos;
    }
    
    /**
     * Cancela un pago
     * 
     * @param int $pagoId
     * @param string $motivo
     * @param int $usuarioId
     * @return bool
     */
    public function cancelarPago($pagoId, $motivo, $usuarioId) {
        $this->db->beginTransaction();
        
        try {
            // Obtener datos del pago
            $pago = $this->findById($pagoId);
            
            if (!$pago) {
                throw new Exception("Pago no encontrado");
            }
            
            // Cancelar el pago
            $sql = "UPDATE {$this->table}
                    SET cancelado = 1,
                        fecha_cancelacion = NOW(),
                        motivo_cancelacion = ?,
                        usuario_cancelacion_id = ?
                    WHERE id = ?";
            
            $this->query($sql, [$motivo, $usuarioId, $pagoId]);
            
            // Actualizar totales de la orden
            $this->actualizarTotalesOrden($pago['orden_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtiene el total de ingresos por día
     * 
     * @param int $sucursalId
     * @param string $fecha
     * @return float
     */
    public function obtenerIngresosDia($sucursalId, $fecha) {
        $sql = "SELECT COALESCE(SUM(monto), 0) as total
                FROM {$this->table}
                WHERE sucursal_id = ?
                AND DATE(fecha_pago) = ?
                AND cancelado = 0";
        
        $result = $this->queryOne($sql, [$sucursalId, $fecha]);
        return $result['total'];
    }
    
    /**
     * Obtiene resumen de ingresos por forma de pago
     * 
     * @param int $sucursalId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function obtenerResumenPorFormaPago($sucursalId, $fechaInicio, $fechaFin) {
        $sql = "SELECT fp.nombre as forma_pago,
                       COUNT(p.id) as cantidad,
                       SUM(p.monto) as total
                FROM {$this->table} p
                JOIN formas_pago fp ON p.forma_pago_id = fp.id
                WHERE p.sucursal_id = ?
                AND DATE(p.fecha_pago) BETWEEN ? AND ?
                AND p.cancelado = 0
                GROUP BY fp.id, fp.nombre
                ORDER BY total DESC";
        
        return $this->queryAll($sql, [$sucursalId, $fechaInicio, $fechaFin]);
    }
}
