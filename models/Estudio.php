<?php
/**
 * Modelo Estudio
 * 
 * Maneja todas las operaciones relacionadas con el catálogo de estudios
 */

require_once CORE_PATH . '/Model.php';

class Estudio extends Model {
    
    protected $table = 'estudios';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'codigo_interno',
        'codigo_loinc',
        'nombre',
        'nombre_corto',
        'descripcion',
        'area_id',
        'tipo_muestra_id',
        'volumen_requerido',
        'metodologia_id',
        'dias_proceso',
        'indicaciones_paciente',
        'activo'
    ];
    
    /**
     * Busca estudios por nombre o código
     * 
     * @param string $termino
     * @param int $limit
     * @return array
     */
    public function buscar($termino, $limit = 20) {
        $sql = "SELECT e.*, 
                       a.nombre as area_nombre,
                       a.color as area_color,
                       tm.nombre as tipo_muestra_nombre,
                       m.nombre as metodologia_nombre
                FROM {$this->table} e
                LEFT JOIN areas a ON e.area_id = a.id
                LEFT JOIN tipos_muestra tm ON e.tipo_muestra_id = tm.id
                LEFT JOIN metodologias m ON e.metodologia_id = m.id
                WHERE e.activo = 1
                AND (e.codigo_interno LIKE ? 
                     OR e.nombre LIKE ? 
                     OR e.nombre_corto LIKE ?
                     OR e.codigo_loinc LIKE ?)
                ORDER BY e.nombre
                LIMIT ?";
        
        $searchTerm = "%{$termino}%";
        return $this->queryAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Obtiene un estudio con todos sus detalles
     * 
     * @param int $id
     * @return array|null
     */
    public function obtenerConDetalles($id) {
        $sql = "SELECT e.*, 
                       a.nombre as area_nombre, a.color as area_color,
                       tm.nombre as tipo_muestra_nombre,
                       m.nombre as metodologia_nombre
                FROM {$this->table} e
                LEFT JOIN areas a ON e.area_id = a.id
                LEFT JOIN tipos_muestra tm ON e.tipo_muestra_id = tm.id
                LEFT JOIN metodologias m ON e.metodologia_id = m.id
                WHERE e.id = ?";
        
        $estudio = $this->queryOne($sql, [$id]);
        
        if ($estudio) {
            // Obtener parámetros
            $estudio['parametros'] = $this->obtenerParametros($id);
            
            // Obtener indicaciones
            $estudio['indicaciones'] = $this->obtenerIndicaciones($id);
            
            // Obtener precios
            $estudio['precios'] = $this->obtenerPrecios($id);
        }
        
        return $estudio;
    }
    
    /**
     * Obtiene los parámetros de un estudio
     * 
     * @param int $estudioId
     * @return array
     */
    public function obtenerParametros($estudioId) {
        $sql = "SELECT ep.*,
                       pt.nombre as tipo_nombre,
                       pt.clave as tipo_clave
                FROM estudio_parametros ep
                JOIN parametro_tipos pt ON ep.tipo_parametro_id = pt.id
                WHERE ep.estudio_id = ? AND ep.activo = 1
                ORDER BY ep.orden";
        
        $parametros = $this->queryAll($sql, [$estudioId]);
        
        // Para cada parámetro, obtener valores de referencia y opciones
        foreach ($parametros as &$parametro) {
            $parametro['valores_referencia'] = $this->obtenerValoresReferencia($parametro['id']);
            
            if ($parametro['tipo_clave'] === 'opcion_multiple') {
                $parametro['opciones'] = $this->obtenerOpcionesParametro($parametro['id']);
            }
        }
        
        return $parametros;
    }
    
    /**
     * Obtiene los valores de referencia de un parámetro
     * 
     * @param int $parametroId
     * @return array
     */
    public function obtenerValoresReferencia($parametroId) {
        $sql = "SELECT * FROM parametro_valores_referencia
                WHERE parametro_id = ?
                ORDER BY sexo, edad_min";
        
        return $this->queryAll($sql, [$parametroId]);
    }
    
    /**
     * Obtiene las opciones de un parámetro de tipo opción múltiple
     * 
     * @param int $parametroId
     * @return array
     */
    public function obtenerOpcionesParametro($parametroId) {
        $sql = "SELECT * FROM parametro_opciones
                WHERE parametro_id = ? AND activo = 1
                ORDER BY orden";
        
        return $this->queryAll($sql, [$parametroId]);
    }
    
    /**
     * Obtiene las indicaciones de un estudio
     * 
     * @param int $estudioId
     * @return array
     */
    public function obtenerIndicaciones($estudioId) {
        $sql = "SELECT * FROM estudio_indicaciones
                WHERE estudio_id = ?
                ORDER BY orden";
        
        return $this->queryAll($sql, [$estudioId]);
    }
    
    /**
     * Obtiene los precios de un estudio en todas las listas
     * 
     * @param int $estudioId
     * @return array
     */
    public function obtenerPrecios($estudioId) {
        $sql = "SELECT ep.*,
                       lp.nombre as lista_nombre,
                       lp.tipo as lista_tipo
                FROM estudio_precios ep
                JOIN listas_precios lp ON ep.lista_precio_id = lp.id
                WHERE ep.estudio_id = ? AND lp.activa = 1
                ORDER BY lp.tipo, lp.nombre";
        
        return $this->queryAll($sql, [$estudioId]);
    }
    
    /**
     * Obtiene el precio de un estudio en una lista específica
     * 
     * @param int $estudioId
     * @param int $listaPrecioId
     * @return float|null
     */
    public function obtenerPrecio($estudioId, $listaPrecioId) {
        $sql = "SELECT precio_base FROM estudio_precios
                WHERE estudio_id = ? AND lista_precio_id = ?";
        
        $result = $this->queryOne($sql, [$estudioId, $listaPrecioId]);
        return $result ? $result['precio_base'] : null;
    }
    
    /**
     * Obtiene estudios por área
     * 
     * @param int $areaId
     * @return array
     */
    public function obtenerPorArea($areaId) {
        $sql = "SELECT e.*,
                       a.nombre as area_nombre,
                       a.color as area_color
                FROM {$this->table} e
                JOIN areas a ON e.area_id = a.id
                WHERE e.area_id = ? AND e.activo = 1
                ORDER BY e.nombre";
        
        return $this->queryAll($sql, [$areaId]);
    }
    
    /**
     * Obtiene estudios más solicitados
     * 
     * @param int $limit
     * @param int|null $sucursalId
     * @return array
     */
    public function obtenerMasSolicitados($limit = 10, $sucursalId = null) {
        $sql = "SELECT e.id, e.codigo_interno, e.nombre,
                       a.nombre as area_nombre,
                       COUNT(oe.id) as total_veces
                FROM {$this->table} e
                JOIN orden_estudios oe ON e.id = oe.estudio_id
                JOIN ordenes o ON oe.orden_id = o.id
                JOIN areas a ON e.area_id = a.id
                WHERE e.activo = 1";
        
        $params = [];
        
        if ($sucursalId) {
            $sql .= " AND o.sucursal_id = ?";
            $params[] = $sucursalId;
        }
        
        $sql .= " GROUP BY e.id
                  ORDER BY total_veces DESC
                  LIMIT ?";
        
        $params[] = $limit;
        
        return $this->queryAll($sql, $params);
    }
}
