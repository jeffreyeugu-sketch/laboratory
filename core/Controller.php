<?php
/**
 * Clase Controller Base
 * Todos los controladores heredan de esta clase
 */

abstract class Controller {
    protected $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config/app.php';
        
        // Verificar autenticación para todas las páginas excepto login
        if (!$this->isPublicRoute() && !Auth::check()) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Renderizar una vista
     * 
     * @param string $viewPath Ruta de la vista (sin extensión .php)
     * @param array $data Datos a pasar a la vista
     */
    protected function view($viewPath, $data = []) {
        // Extraer datos para que estén disponibles en la vista
        extract($data);
        
        // Capturar el contenido de la vista
        ob_start();
        require __DIR__ . "/../views/{$viewPath}.php";
        $content = ob_get_clean();
        
        // Renderizar con el layout
        require __DIR__ . '/../views/layouts/main.php';
    }
    
    /**
     * Retornar respuesta JSON
     * 
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Redirigir a otra URL
     * 
     * @param string $url
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Obtener valor de entrada (POST o GET)
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    /**
     * Obtener todos los datos POST
     * 
     * @return array
     */
    protected function all() {
        return $_POST;
    }
    
    /**
     * Verificar si la petición es POST
     * 
     * @return bool
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verificar si la petición es GET
     * 
     * @return bool
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Verificar si la petición es AJAX
     * 
     * @return bool
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Establecer mensaje flash en sesión
     * 
     * @param string $type (success, error, warning, info)
     * @param string $message
     */
    protected function flash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Validar datos con reglas básicas
     * 
     * @param array $data
     * @param array $rules
     * @return array Errores de validación (vacío si todo OK)
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            
            foreach ($rulesArray as $rule) {
                // Regla: required
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field] = "El campo {$field} es requerido";
                    break;
                }
                
                // Regla: email
                if ($rule === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "El campo {$field} debe ser un email válido";
                    break;
                }
                
                // Regla: numeric
                if ($rule === 'numeric' && !empty($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field] = "El campo {$field} debe ser numérico";
                    break;
                }
                
                // Regla: min:x
                if (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (!empty($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field] = "El campo {$field} debe tener al menos {$min} caracteres";
                        break;
                    }
                }
                
                // Regla: max:x
                if (strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    if (!empty($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field] = "El campo {$field} no debe exceder {$max} caracteres";
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Verificar si la ruta actual es pública (no requiere autenticación)
     * 
     * @return bool
     */
    protected function isPublicRoute() {
        $publicRoutes = ['/login', '/recuperar-password'];
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        return in_array($currentPath, $publicRoutes);
    }
    
    /**
     * Verificar si el usuario tiene un permiso
     * 
     * @param string $permiso
     * @return bool
     */
    protected function can($permiso) {
        return Auth::can($permiso);
    }
    
    /**
     * Verificar permiso o abortar con error 403
     * 
     * @param string $permiso
     */
    protected function authorize($permiso) {
        if (!$this->can($permiso)) {
            http_response_code(403);
            die('No tiene permisos para realizar esta acción');
        }
    }
}
