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
            $this->redirect('/laboratorio-clinico/public/dashboard');
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
                $this->redirect('/laboratorio-clinico/public/dashboard');
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
        $this->redirect('/laboratorio-clinico/public/login');
    }
    
    /**
     * Recuperación de contraseña
     */
    public function recuperarPassword() {
        if ($this->isPost()) {
            $email = $this->input('email');
            
            // TODO: Implementar lógica de recuperación de contraseña
            $this->redirectWith('/laboratorio-clinico/public/login', 
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
            
            // Validar fortaleza de la contraseña
            $validacion = Auth::validatePassword($passwordNuevo);
            if (!$validacion['valid']) {
                $data['error'] = implode('<br>', $validacion['errors']);
                $this->view('auth/cambiar-password', $data);
                return;
            }
            
            // Verificar contraseña actual
            $user = Auth::user();
            if (!password_verify($passwordActual, $user['password_hash'])) {
                $data['error'] = 'La contraseña actual es incorrecta';
                $this->view('auth/cambiar-password', $data);
                return;
            }
            
            // Cambiar contraseña
            if (Auth::changePassword($user['id'], $passwordNuevo)) {
                $this->redirectWith('/laboratorio-clinico/public/dashboard', 
                    'Contraseña actualizada correctamente', 
                    'success');
            } else {
                $data['error'] = 'Error al cambiar la contraseña';
                $this->view('auth/cambiar-password', $data);
            }
        } else {
            $this->view('auth/cambiar-password');
        }
    }
}
