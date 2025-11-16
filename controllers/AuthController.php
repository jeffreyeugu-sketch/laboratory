<?php
/**
 * AuthController
 * 
 * Controlador para manejo de autenticación de usuarios
 */

require_once CORE_PATH . '/Controller.php';

class AuthController extends Controller {
    
    /**
     * Muestra el formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect(url('/dashboard'));
        }
        
        // Si es POST, procesar el login
        if ($this->isPost()) {
            $username = $this->input('username');
            $password = $this->input('password');
            
            // Validar campos
            if (empty($username) || empty($password)) {
                $data['error'] = 'Por favor ingrese usuario y contraseña';
                $this->view('auth/login', $data, false);
                return;
            }
            
            // Intentar autenticar
            if (Auth::login($username, $password)) {
                // Login exitoso
                $this->redirect(url('/dashboard'));
            } else {
                // Login fallido
                $data['error'] = 'Usuario o contraseña incorrectos';
                $this->view('auth/login', $data, false);
            }
        } else {
            // Mostrar formulario de login
            $this->view('auth/login', [], false);
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        Auth::logout();
        $this->redirect(url('/login'));
    }
    
    /**
     * Recuperación de contraseña
     */
    public function recuperarPassword() {
        if ($this->isPost()) {
            $email = $this->input('email');
            
            // TODO: Implementar lógica de recuperación de contraseña
            $this->redirectWith(url('/login'), 
                'Se ha enviado un correo con instrucciones para recuperar tu contraseña', 
                'info');
        } else {
            $this->view('auth/recuperar-password', [], false);
        }
    }
    
    /**
     * Cambio de contraseña
     */
    public function cambiarPassword() {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $passwordActual = $this->input('password_actual');
            $passwordNuevo = $this->input('password_nuevo');
            $passwordConfirm = $this->input('password_confirm');
            
            // Validar que las contraseñas coincidan
            if ($passwordNuevo !== $passwordConfirm) {
                $data['error'] = 'Las contraseñas no coinciden';
                $this->view('auth/cambiar-password', $data);
                return;
            }
            
            // Validar longitud mínima
            if (strlen($passwordNuevo) < 6) {
                $data['error'] = 'La contraseña debe tener al menos 6 caracteres';
                $this->view('auth/cambiar-password', $data);
                return;
            }
            
            // Verificar contraseña actual
            $userId = $_SESSION['user']['id'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!password_verify($passwordActual, $user['password'])) {
                $data['error'] = 'La contraseña actual es incorrecta';
                $this->view('auth/cambiar-password', $data);
                return;
            }
            
            // Actualizar contraseña
            $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET password = ?, updated_at = NOW() 
                                 WHERE id = ?");
            $stmt->execute([$passwordHash, $userId]);
            
            // Registrar en auditoría
            $stmt = $db->prepare("INSERT INTO auditoria (usuario_id, accion, tabla, registro_id, 
                                 descripcion, ip, user_agent, created_at) 
                                 VALUES (?, 'cambio_password', 'usuarios', ?, 
                                 'Usuario cambió su contraseña', ?, ?, NOW())");
            $stmt->execute([
                $userId,
                $userId,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
            
            $this->redirectWith(url('/dashboard'), 
                'Contraseña actualizada correctamente', 
                'success');
        } else {
            $this->view('auth/cambiar-password');
        }
    }
    
    /**
     * Verificar si la ruta actual es pública
     * 
     * @return bool
     */
    protected function isPublicRoute() {
        $publicRoutes = ['/login', '/recuperar-password'];
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($publicRoutes as $route) {
            if (strpos($currentPath, $route) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
