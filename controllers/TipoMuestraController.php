<?php
/**
 * TipoMuestraController.php
 * Controlador para gestión de tipos de muestra
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/TipoMuestra.php';

class TipoMuestraController extends Controller
{
    private $tipoMuestraModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->tipoMuestraModel = new TipoMuestra();
    }
    
    /**
     * Verificar autenticación
     */
    private function verificarAuth()
    {
        if (!Auth::check()) {
            redirect('/login');
            exit;
        }
    }
    
    /**
     * Listado
     */
    public function index()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Tipos de Muestra'
        ];
        
        $this->view('catalogos/tipos-muestra/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function crear()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Nuevo Tipo de Muestra',
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/tipos-muestra/crear', $data);
    }
    
    /**
     * Guardar
     */
    public function guardar()
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/tipos-muestra');
            exit;
        }
        
        try {
            $errores = $this->validarDatos($_POST);
            
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                redirect('/catalogos/tipos-muestra/crear');
                exit;
            }
            
            $datos = [
                'codigo' => strtoupper(trim($_POST['codigo'])),
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'requiere_ayuno' => isset($_POST['requiere_ayuno']) ? 1 : 0,
                'requiere_refrigeracion' => isset($_POST['requiere_refrigeracion']) ? 1 : 0,
                'tiempo_estabilidad_horas' => intval($_POST['tiempo_estabilidad_horas'] ?? 24),
                'temperatura_almacenamiento' => trim($_POST['temperatura_almacenamiento'] ?? ''),
                'instrucciones_recoleccion' => trim($_POST['instrucciones_recoleccion'] ?? ''),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            $id = $this->tipoMuestraModel->crear($datos);
            
            if ($id) {
                $_SESSION['flash_message'] = 'Tipo de muestra creado exitosamente';
                $_SESSION['flash_type'] = 'success';
                redirect('/catalogos/tipos-muestra');
                exit;
            } else {
                throw new Exception('Error al crear el tipo de muestra');
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $_POST;
            redirect('/catalogos/tipos-muestra/crear');
            exit;
        }
    }
    
    /**
     * Formulario de edición
     */
    public function editar($id)
    {
        $this->verificarAuth();
        
        $tipo = $this->tipoMuestraModel->obtenerPorId($id);
        
        if (!$tipo) {
            $_SESSION['flash_message'] = 'Tipo de muestra no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/tipos-muestra');
            exit;
        }
        
        $data = [
            'title' => 'Editar Tipo de Muestra',
            'tipo' => $tipo,
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/tipos-muestra/editar', $data);
    }
    
    /**
     * Actualizar
     */
    public function actualizar($id)
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/tipos-muestra');
            exit;
        }
        
        try {
            $tipoAnterior = $this->tipoMuestraModel->obtenerPorId($id);
            
            if (!$tipoAnterior) {
                throw new Exception('Tipo de muestra no encontrado');
            }
            
            $errores = $this->validarDatos($_POST, $id);
            
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                redirect('/catalogos/tipos-muestra/editar/' . $id);
                exit;
            }
            
            $datos = [
                'codigo' => strtoupper(trim($_POST['codigo'])),
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'requiere_ayuno' => isset($_POST['requiere_ayuno']) ? 1 : 0,
                'requiere_refrigeracion' => isset($_POST['requiere_refrigeracion']) ? 1 : 0,
                'tiempo_estabilidad_horas' => intval($_POST['tiempo_estabilidad_horas'] ?? 24),
                'temperatura_almacenamiento' => trim($_POST['temperatura_almacenamiento'] ?? ''),
                'instrucciones_recoleccion' => trim($_POST['instrucciones_recoleccion'] ?? ''),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            $resultado = $this->tipoMuestraModel->actualizar($id, $datos);
            
            if ($resultado) {
                $_SESSION['flash_message'] = 'Tipo de muestra actualizado exitosamente';
                $_SESSION['flash_type'] = 'success';
                redirect('/catalogos/tipos-muestra');
                exit;
            } else {
                throw new Exception('Error al actualizar el tipo de muestra');
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $_POST;
            redirect('/catalogos/tipos-muestra/editar/' . $id);
            exit;
        }
    }
    
    /**
     * Eliminar
     */
    public function eliminar($id)
    {
        $this->verificarAuth();
        
        header('Content-Type: application/json');
        
        try {
            $tipo = $this->tipoMuestraModel->obtenerPorId($id);
            
            if (!$tipo) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de muestra no encontrado'
                ]);
                exit;
            }
            
            $tieneEstudios = $this->tipoMuestraModel->tieneEstudiosRelacionados($id);
            
            if ($tieneEstudios) {
                $resultado = $this->tipoMuestraModel->desactivar($id);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'El tipo de muestra tiene estudios relacionados, se desactivó'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se pudo desactivar el tipo de muestra'
                    ]);
                }
            } else {
                $resultado = $this->tipoMuestraModel->eliminar($id);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tipo de muestra eliminado exitosamente'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se pudo eliminar el tipo de muestra'
                    ]);
                }
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    
    /**
     * Listar para DataTables (AJAX)
     */
    public function listar()
    {
        $this->verificarAuth();
        
        try {
            $draw = $_POST['draw'] ?? 1;
            $start = $_POST['start'] ?? 0;
            $length = $_POST['length'] ?? 25;
            $searchValue = $_POST['search']['value'] ?? '';
            $orderColumn = $_POST['order'][0]['column'] ?? 0;
            $orderDir = $_POST['order'][0]['dir'] ?? 'DESC';
            
            $columnas = ['id', 'codigo', 'nombre', 'activo'];
            $orderBy = $columnas[$orderColumn] ?? 'id';
            
            $tipos = $this->tipoMuestraModel->listar($start, $length, $searchValue, $orderBy, $orderDir);
            $totalRegistros = $this->tipoMuestraModel->contarTotal();
            $totalFiltrados = $this->tipoMuestraModel->contarFiltrados($searchValue);
            
            $data = [];
            foreach ($tipos as $tipo) {
                // Formatear campos
                $requisitos = [];
                if ($tipo['requiere_ayuno']) {
                    $requisitos[] = '<span class="badge bg-warning text-dark">Ayuno</span>';
                }
                if ($tipo['requiere_refrigeracion']) {
                    $requisitos[] = '<span class="badge bg-info">Refrigeración</span>';
                }
                $requisitosHtml = !empty($requisitos) ? implode(' ', $requisitos) : '<span class="text-muted">Ninguno</span>';
                
                $data[] = [
                    'id' => $tipo['id'],
                    'codigo' => htmlspecialchars($tipo['codigo'] ?? 'N/A'),
                    'nombre' => htmlspecialchars($tipo['nombre']),
                    'requisitos' => $requisitosHtml,
                    'activo' => $tipo['activo'] ? 
                        '<span class="badge bg-success">Activo</span>' : 
                        '<span class="badge bg-secondary">Inactivo</span>',
                    'acciones' => $this->generarBotonesAccion($tipo)
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
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
        
        exit;
    }
    
    /**
     * Validar datos
     */
    private function validarDatos($datos, $idExcluir = null)
    {
        $errores = [];
        
        if (empty($datos['codigo'])) {
            $errores['codigo'] = 'El código es requerido';
        } elseif (strlen($datos['codigo']) > 20) {
            $errores['codigo'] = 'El código no puede tener más de 20 caracteres';
        } else {
            $existe = $this->tipoMuestraModel->existeCodigo(strtoupper(trim($datos['codigo'])), $idExcluir);
            if ($existe) {
                $errores['codigo'] = 'El código ya está en uso';
            }
        }
        
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede tener más de 100 caracteres';
        }
        
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $errores['descripcion'] = 'La descripción no puede tener más de 500 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Generar botones de acción
     */
    private function generarBotonesAccion($tipo)
    {
        $nombreEscapado = htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8');
        
        $html = '<div class="btn-group btn-group-sm" role="group">';
        
        $html .= '<a href="' . url('/catalogos/tipos-muestra/editar/' . $tipo['id']) . '" ';
        $html .= 'class="btn btn-warning btn-sm" ';
        $html .= 'title="Editar">';
        $html .= '<i class="fas fa-edit"></i>';
        $html .= '</a>';
        
        $html .= '<button type="button" ';
        $html .= 'class="btn btn-danger btn-sm btn-eliminar" ';
        $html .= 'data-id="' . $tipo['id'] . '" ';
        $html .= 'data-nombre="' . $nombreEscapado . '" ';
        $html .= 'title="Eliminar">';
        $html .= '<i class="fas fa-trash"></i>';
        $html .= '</button>';
        
        $html .= '</div>';
        
        return $html;
    }
}