<?php
/**
 * Controlador: MetodologiaController
 * 
 * Gestiona el CRUD del catálogo de Metodologías.
 * 
 * Rutas:
 * - GET  /catalogos/metodologias          -> index()
 * - GET  /catalogos/metodologias/crear    -> crear()
 * - POST /catalogos/metodologias/guardar  -> guardar()
 * - GET  /catalogos/metodologias/editar/:id  -> editar($id)
 * - POST /catalogos/metodologias/actualizar/:id -> actualizar($id)
 * - POST /catalogos/metodologias/eliminar/:id -> eliminar($id)
 * - POST /catalogos/metodologias/listar   -> listar() [AJAX DataTables]
 * 
 * @version 1.0.0
 * @author Sistema de Laboratorio Clínico
 */

class MetodologiaController extends Controller
{
    private $metodologiaModel;

    public function __construct()
    {
        parent::__construct();
        $this->metodologiaModel = new Metodologia();
    }

    /**
     * Verifica autenticación
     * Redirige a login si no está autenticado
     */
    private function verificarAuth()
    {
        if (!Auth::check()) {
            redirect('/login');
            exit;
        }
    }

    /**
     * Vista principal con listado de metodologías (DataTables)
     */
    public function index()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Metodologías',
            'seccion' => 'Catálogos',
            'subseccion' => 'Metodologías'
        ];
        
        $this->view('catalogos/metodologias/index', $data);
    }

    /**
     * Formulario para crear nueva metodología
     */
    public function crear()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Nueva Metodología',
            'seccion' => 'Catálogos',
            'subseccion' => 'Metodologías',
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        // Limpiar datos de sesión
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/metodologias/crear', $data);
    }

    /**
     * Procesa la creación de una nueva metodología (POST)
     */
    public function guardar()
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/metodologias');
            exit;
        }
        
        // Obtener datos del formulario
        $datos = [
            'codigo' => !empty($_POST['codigo']) ? strtoupper(trim($_POST['codigo'])) : null,
            'nombre' => trim($_POST['nombre'] ?? ''),
            'abreviatura' => !empty($_POST['abreviatura']) ? strtoupper(trim($_POST['abreviatura'])) : null,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'orden' => intval($_POST['orden'] ?? $this->metodologiaModel->obtenerSiguienteOrden()),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        // Validar datos
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/metodologias/crear');
            exit;
        }
        
        // Crear metodología
        if ($this->metodologiaModel->crear($datos)) {
            $_SESSION['flash_message'] = 'Metodología creada exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/metodologias');
        } else {
            $_SESSION['flash_message'] = 'Error al crear la metodología';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/metodologias/crear');
        }
        
        exit;
    }

    /**
     * Formulario para editar metodología existente
     */
    public function editar($id)
    {
        $this->verificarAuth();
        
        $metodologia = $this->metodologiaModel->obtenerPorId($id);
        
        if (!$metodologia) {
            $_SESSION['flash_message'] = 'Metodología no encontrada';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/metodologias');
            exit;
        }
        
        $data = [
            'title' => 'Editar Metodología',
            'seccion' => 'Catálogos',
            'subseccion' => 'Metodologías',
            'metodologia' => $metodologia,
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        // Limpiar datos de sesión
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/metodologias/editar', $data);
    }

    /**
     * Procesa la actualización de una metodología (POST)
     */
    public function actualizar($id)
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/metodologias');
            exit;
        }
        
        $metodologia = $this->metodologiaModel->obtenerPorId($id);
        
        if (!$metodologia) {
            $_SESSION['flash_message'] = 'Metodología no encontrada';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/metodologias');
            exit;
        }
        
        // Obtener datos del formulario
        $datos = [
        'codigo' => !empty($_POST['codigo']) ? strtoupper(trim($_POST['codigo'])) : null,
        'nombre' => trim($_POST['nombre'] ?? ''),
        'abreviatura' => !empty($_POST['abreviatura']) ? strtoupper(trim($_POST['abreviatura'])) : null,
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'orden' => intval($_POST['orden'] ?? $metodologia['orden'] ?? 0),
        'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        // Validar datos
        $errores = $this->validarDatos($datos, $id);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/metodologias/editar/' . $id);
            exit;
        }
        
        // Actualizar metodología
        if ($this->metodologiaModel->actualizar($id, $datos)) {
            $_SESSION['flash_message'] = 'Metodología actualizada exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/metodologias');
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar la metodología';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/metodologias/editar/' . $id);
        }
        
        exit;
    }

    /**
     * Elimina o desactiva una metodología (AJAX - POST)
     * 
     * Si tiene estudios relacionados, la desactiva en lugar de eliminar
     * 
     * @param int $id ID de la metodología
     * @return JSON
     */
    public function eliminar($id)
    {
        $this->verificarAuth();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            exit;
        }
        
        $metodologia = $this->metodologiaModel->obtenerPorId($id);
        
        if (!$metodologia) {
            echo json_encode([
                'success' => false,
                'message' => 'Metodología no encontrada'
            ]);
            exit;
        }
        
        // Verificar si tiene estudios relacionados
        if ($this->metodologiaModel->tieneEstudiosRelacionados($id)) {
            // Desactivar en lugar de eliminar
            if ($this->metodologiaModel->desactivar($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'La metodología tiene estudios asociados y fue desactivada en lugar de eliminarse'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al desactivar la metodología'
                ]);
            }
        } else {
            // Eliminar
            if ($this->metodologiaModel->eliminar($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Metodología eliminada exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar la metodología'
                ]);
            }
        }
        
        exit;
    }

    /**
     * Listado AJAX para DataTables (server-side processing)
     * 
     * @return JSON
     */
    public function listar()
    {
        $this->verificarAuth();
        header('Content-Type: application/json');
        
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';
        $orderColumnIndex = intval($_POST['order'][0]['column'] ?? 0);
        $orderDir = $_POST['order'][0]['dir'] ?? 'ASC';
        
        // Mapeo de columnas
        $columns = ['id', 'orden', 'codigo', 'nombre', 'abreviatura', 'activo'];
        $orderBy = $columns[$orderColumnIndex] ?? 'orden';
        
        $metodologias = $this->metodologiaModel->listar($start, $length, $search, $orderBy, $orderDir);
        $totalRegistros = $this->metodologiaModel->contarTotal();
        $totalFiltrados = $this->metodologiaModel->contarFiltrados($search);
        
        $data = [];
        foreach ($metodologias as $metodologia) {
            $data[] = [
                'id' => $metodologia['id'],
                'orden' => $metodologia['orden'] ?? 0,
                'codigo' => htmlspecialchars($metodologia['codigo'] ?? 'N/A'),
                'nombre' => htmlspecialchars($metodologia['nombre']),
                'abreviatura' => htmlspecialchars($metodologia['abreviatura'] ?? 'N/A'),
                'descripcion' => htmlspecialchars($metodologia['descripcion'] ?? ''),
                'activo' => $metodologia['activo'] ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-secondary">Inactivo</span>',
                'acciones' => $this->generarBotonesAccion($metodologia)
            ];
        }
        
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $totalRegistros,
            'recordsFiltered' => $totalFiltrados,
            'data' => $data
        ]);
        
        exit;
    }

    /**
     * Valida los datos del formulario
     * 
     * @param array $datos Datos a validar
     * @param int|null $id ID para excluir en validación de código único
     * @return array Arreglo de errores (vacío si no hay errores)
     */
    private function validarDatos($datos, $id = null)
    {
        $errores = [];
        
        // Código es OPCIONAL ahora
        if (!empty($datos['codigo'])) {
            if (strlen($datos['codigo']) > 20) {
                $errores['codigo'] = 'El código no puede tener más de 20 caracteres';
            } elseif ($this->metodologiaModel->existeCodigo($datos['codigo'], $id)) {
                $errores['codigo'] = 'El código ya existe';
            }
        }
        
        // Abreviatura es OPCIONAL
        if (!empty($datos['abreviatura']) && strlen($datos['abreviatura']) > 20) {
            $errores['abreviatura'] = 'La abreviatura no puede tener más de 20 caracteres';
        }
        
        // Nombre es OBLIGATORIO
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede tener más de 100 caracteres';
        }
        
        // Descripción OPCIONAL
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $errores['descripcion'] = 'La descripción no puede tener más de 500 caracteres';
        }
        
        // Orden debe ser número positivo
        if (isset($datos['orden']) && $datos['orden'] < 0) {
            $errores['orden'] = 'El orden debe ser un número positivo';
        }
        
        return $errores;
    }

    /**
     * Genera los botones de acción para cada registro
     * 
     * @param array $metodologia Datos de la metodología
     * @return string HTML con los botones
     */
    private function generarBotonesAccion($metodologia)
    {
        $id = $metodologia['id'];
        $nombre = htmlspecialchars($metodologia['nombre']);
        
        return '
            <div class="btn-group btn-group-sm" role="group">
                <a href="' . url('/catalogos/metodologias/editar/' . $id) . '" 
                   class="btn btn-outline-primary" 
                   title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" 
                        class="btn btn-outline-danger btn-eliminar" 
                        data-id="' . $id . '" 
                        data-nombre="' . $nombre . '"
                        title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
