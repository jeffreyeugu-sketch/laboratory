<?php
/**
 * AreaController.php
 * Controlador para gestión de áreas del laboratorio
 * 
 * Maneja CRUD completo de áreas (Química Clínica, Hematología, etc.)
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Area.php';

class AreaController extends Controller
{
    private $areaModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->areaModel = new Area();
    }
    
    /**
     * Listado de áreas
     */
    public function index()
    {
        $this->checkAuth();
        
        $data = [
            'titulo' => 'Áreas del Laboratorio',
            'breadcrumb' => [
                ['nombre' => 'Catálogos', 'url' => '#'],
                ['nombre' => 'Áreas', 'url' => '']
            ]
        ];
        
        $this->render('catalogos/areas/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function crear()
    {
        $this->checkAuth();
        
        $data = [
            'titulo' => 'Nueva Área',
            'breadcrumb' => [
                ['nombre' => 'Catálogos', 'url' => '#'],
                ['nombre' => 'Áreas', 'url' => '/catalogos/areas'],
                ['nombre' => 'Nueva', 'url' => '']
            ]
        ];
        
        $this->render('catalogos/areas/crear', $data);
    }
    
    /**
     * Guardar nueva área
     */
    public function guardar()
    {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/catalogos/areas');
            return;
        }
        
        try {
            // Validar datos
            $errores = $this->validarDatos($_POST);
            
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                $this->redirect('/catalogos/areas/crear');
                return;
            }
            
            // Preparar datos
            $datos = [
                'codigo' => trim($_POST['codigo']),
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            // Crear área
            $areaId = $this->areaModel->crear($datos);
            
            if ($areaId) {
                // Registrar auditoría
                $this->registrarAuditoria('crear', 'areas', $areaId, null, $datos);
                
                $this->setFlash('success', 'Área creada exitosamente');
                $this->redirect('/catalogos/areas');
            } else {
                throw new Exception('Error al crear el área');
            }
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Error: ' . $e->getMessage());
            $_SESSION['old'] = $_POST;
            $this->redirect('/catalogos/areas/crear');
        }
    }
    
    /**
     * Formulario de edición
     */
    public function editar($id)
    {
        $this->checkAuth();
        
        $area = $this->areaModel->obtenerPorId($id);
        
        if (!$area) {
            $this->setFlash('error', 'Área no encontrada');
            $this->redirect('/catalogos/areas');
            return;
        }
        
        $data = [
            'titulo' => 'Editar Área',
            'area' => $area,
            'breadcrumb' => [
                ['nombre' => 'Catálogos', 'url' => '#'],
                ['nombre' => 'Áreas', 'url' => '/catalogos/areas'],
                ['nombre' => 'Editar', 'url' => '']
            ]
        ];
        
        $this->render('catalogos/areas/editar', $data);
    }
    
    /**
     * Actualizar área existente
     */
    public function actualizar($id)
    {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/catalogos/areas');
            return;
        }
        
        try {
            $areaAnterior = $this->areaModel->obtenerPorId($id);
            
            if (!$areaAnterior) {
                throw new Exception('Área no encontrada');
            }
            
            // Validar datos
            $errores = $this->validarDatos($_POST, $id);
            
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                $this->redirect('/catalogos/areas/editar/' . $id);
                return;
            }
            
            // Preparar datos
            $datos = [
                'codigo' => trim($_POST['codigo']),
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            // Actualizar área
            $resultado = $this->areaModel->actualizar($id, $datos);
            
            if ($resultado) {
                // Registrar auditoría
                $this->registrarAuditoria('actualizar', 'areas', $id, $areaAnterior, $datos);
                
                $this->setFlash('success', 'Área actualizada exitosamente');
                $this->redirect('/catalogos/areas');
            } else {
                throw new Exception('Error al actualizar el área');
            }
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Error: ' . $e->getMessage());
            $_SESSION['old'] = $_POST;
            $this->redirect('/catalogos/areas/editar/' . $id);
        }
    }
    
    /**
     * Eliminar (desactivar) área
     */
    public function eliminar($id)
    {
        $this->checkAuth();
        
        try {
            $area = $this->areaModel->obtenerPorId($id);
            
            if (!$area) {
                throw new Exception('Área no encontrada');
            }
            
            // Verificar si tiene estudios relacionados
            $tieneEstudios = $this->areaModel->tieneEstudiosRelacionados($id);
            
            if ($tieneEstudios) {
                // Solo desactivar, no eliminar
                $resultado = $this->areaModel->actualizar($id, ['activo' => 0]);
                $mensaje = 'Área desactivada (tiene estudios relacionados)';
            } else {
                // Eliminar permanentemente
                $resultado = $this->areaModel->eliminar($id);
                $mensaje = 'Área eliminada exitosamente';
            }
            
            if ($resultado) {
                // Registrar auditoría
                $this->registrarAuditoria('eliminar', 'areas', $id, $area);
                
                $this->setFlash('success', $mensaje);
            } else {
                throw new Exception('Error al eliminar el área');
            }
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Error: ' . $e->getMessage());
        }
        
        $this->redirect('/catalogos/areas');
    }
    
    /**
     * API para DataTables
     */
    public function listar()
    {
        $this->checkAuth();
        
        try {
            // Parámetros de DataTables
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 10;
            $searchValue = $_GET['search']['value'] ?? '';
            $orderColumn = $_GET['order'][0]['column'] ?? 0;
            $orderDir = $_GET['order'][0]['dir'] ?? 'asc';
            
            // Columnas disponibles para ordenar
            $columnas = ['id', 'codigo', 'nombre', 'descripcion', 'activo'];
            $orderBy = $columnas[$orderColumn] ?? 'id';
            
            // Obtener datos
            $filtros = [
                'search' => $searchValue,
                'order_by' => $orderBy,
                'order_dir' => $orderDir,
                'limit' => $length,
                'offset' => $start
            ];
            
            $areas = $this->areaModel->listar($filtros);
            $totalFiltrados = $this->areaModel->contarFiltrados($filtros);
            $totalRegistros = $this->areaModel->contarTotal();
            
            // Formatear datos para DataTables
            $data = [];
            foreach ($areas as $area) {
                $data[] = [
                    'id' => $area['id'],
                    'codigo' => htmlspecialchars($area['codigo']),
                    'nombre' => htmlspecialchars($area['nombre']),
                    'descripcion' => htmlspecialchars($area['descripcion'] ?? ''),
                    'activo' => $area['activo'] ? 
                        '<span class="badge bg-success">Activo</span>' : 
                        '<span class="badge bg-secondary">Inactivo</span>',
                    'acciones' => $this->generarBotonesAccion($area)
                ];
            }
            
            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $totalRegistros,
                'recordsFiltered' => $totalFiltrados,
                'data' => $data
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Validar datos del formulario
     */
    private function validarDatos($datos, $idExcluir = null)
    {
        $errores = [];
        
        // Código requerido
        if (empty($datos['codigo'])) {
            $errores['codigo'] = 'El código es requerido';
        } elseif (strlen($datos['codigo']) > 20) {
            $errores['codigo'] = 'El código no puede tener más de 20 caracteres';
        } else {
            // Verificar código único
            $existe = $this->areaModel->existeCodigo($datos['codigo'], $idExcluir);
            if ($existe) {
                $errores['codigo'] = 'El código ya está en uso';
            }
        }
        
        // Nombre requerido
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede tener más de 100 caracteres';
        }
        
        // Descripción opcional
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $errores['descripcion'] = 'La descripción no puede tener más de 500 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Generar botones de acción para DataTables
     */
    private function generarBotonesAccion($area)
    {
        $html = '<div class="btn-group btn-group-sm" role="group">';
        
        // Botón editar
        $html .= '<a href="/catalogos/areas/editar/' . $area['id'] . '" 
                    class="btn btn-warning" 
                    title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>';
        
        // Botón eliminar
        $html .= '<button type="button" 
                    class="btn btn-danger btn-eliminar" 
                    data-id="' . $area['id'] . '"
                    data-nombre="' . htmlspecialchars($area['nombre']) . '"
                    title="Eliminar">
                    <i class="fas fa-trash"></i>
                  </button>';
        
        $html .= '</div>';
        
        return $html;
    }
}
