<?php

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    /**
     * Mostrar página de login
     */
    public function showLogin() {
        // Si ya está logueado, redirigir según su rol
        if ($this->auth->isLoggedIn()) {
            $currentUser = $this->auth->getCurrentUser();
            $roleModel = new Role();
            
            // Si es super administrador o administrador, redirigir al panel de administración
            if ($roleModel->userHasRole($currentUser['user_id'], Role::SUPER_ADMIN) || 
                $roleModel->userHasRole($currentUser['user_id'], Role::ADMIN)) {
                Utils::redirect('admin');
            }
            // Si es líder de clan, redirigir al dashboard del clan leader
            elseif ($roleModel->userHasRole($currentUser['user_id'], Role::LIDER_CLAN)) {
                Utils::redirect('clan_leader');
            } else {
                Utils::redirect('dashboard');
            }
        }
        
        // Cargar vista de login
        $this->loadView('login');
    }
    
    /**
     * Procesar login
     */
    public function processLogin() {
        // Solo procesar POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('login');
        }
        
        // Verificar si ya está logueado
        if ($this->auth->isLoggedIn()) {
            $currentUser = $this->auth->getCurrentUser();
            $roleModel = new Role();
            $redirectUrl = 'dashboard'; // Por defecto
            
            // Si es super administrador o administrador, redirigir al panel de administración
            if ($roleModel->userHasRole($currentUser['user_id'], Role::SUPER_ADMIN) || 
                $roleModel->userHasRole($currentUser['user_id'], Role::ADMIN)) {
                $redirectUrl = 'admin';
            }
            // Si es líder de clan, redirigir al dashboard del clan leader
            elseif ($roleModel->userHasRole($currentUser['user_id'], Role::LIDER_CLAN)) {
                $redirectUrl = 'clan_leader';
            }
            
            Utils::jsonResponse([
                'success' => true,
                'message' => 'Ya tienes una sesión activa',
                'redirect' => $redirectUrl
            ]);
        }
        
        // Obtener IP del cliente
        $ipAddress = Utils::getClientIP();
        
        // Obtener y limpiar datos del formulario
        $username = Utils::sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'true';
        
        // Array para almacenar errores
        $errors = [];
        $response = [
            'success' => false,
            'message' => '',
            'errors' => [],
            'redirect' => ''
        ];
        
        // Validación de datos
        if (empty($username)) {
            $errors['username'] = 'El usuario es requerido';
        }
        
        if (empty($password)) {
            $errors['password'] = 'La contraseña es requerida';
        }
        
        if (strlen($username) < 3 && !empty($username)) {
            $errors['username'] = 'El usuario debe tener al menos 3 caracteres';
        }
        
        if (strlen($password) < 6 && !empty($password)) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar formato de email si contiene @
        if (strpos($username, '@') !== false && !Utils::isValidEmail($username)) {
            $errors['username'] = 'Formato de email inválido';
        }
        
        // Si hay errores de validación, devolver respuesta
        if (!empty($errors)) {
            $response['message'] = 'Por favor corrige los errores en el formulario';
            $response['errors'] = $errors;
            Utils::jsonResponse($response, 400);
        }
        
        // Verificar límite de intentos de login
        if (!$this->auth->checkLoginAttempts($username, $ipAddress)) {
            $response['message'] = 'Demasiados intentos fallidos. Intenta de nuevo en 15 minutos.';
            Utils::jsonResponse($response, 429);
        }
        
        // Intentar login
        $user = $this->auth->login($username, $password, $rememberMe);
        
        if ($user) {
            // Login exitoso
            $this->auth->logLoginAttempt($username, true, $ipAddress);
            
            // Determinar la redirección basada en el rol del usuario
            $roleModel = new Role();
            $redirectUrl = 'dashboard'; // Por defecto
            
            // Si es super administrador o administrador, redirigir al panel de administración
            if ($roleModel->userHasRole($user['user_id'], Role::SUPER_ADMIN) || 
                $roleModel->userHasRole($user['user_id'], Role::ADMIN)) {
                $redirectUrl = 'admin';
            }
            // Si es líder de clan, redirigir al dashboard del clan leader
            elseif ($roleModel->userHasRole($user['user_id'], Role::LIDER_CLAN)) {
                $redirectUrl = 'clan_leader';
            }
            
            $response['success'] = true;
            $response['message'] = '¡Bienvenido, ' . Utils::escape($user['full_name'] ?: $user['username']) . '!';
            $response['redirect'] = $redirectUrl;
            
            Utils::jsonResponse($response);
        } else {
            // Login fallido
            $this->auth->logLoginAttempt($username, false, $ipAddress);
            
            $response['message'] = 'Usuario o contraseña incorrectos';
            Utils::jsonResponse($response, 401);
        }
    }
    
    /**
     * Procesar logout
     */
    public function logout() {
        // Verificar si el usuario está autenticado
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
        
        // Cerrar sesión
        $this->auth->logout();
        
        // Redirigir al login con mensaje opcional
        $message = $_GET['message'] ?? '';
        if (!empty($message)) {
            Utils::redirect('login?message=' . urlencode($message));
        } else {
            Utils::redirect('login');
        }
    }
    
    /**
     * Verificar autenticación (middleware)
     */
    public function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
        return true;
    }
    
    /**
     * Cargar vista
     */
    private function loadView($view, $data = []) {
        // Extraer variables para la vista
        extract($data);
        
        // Incluir archivo de vista
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die('Vista no encontrada: ' . $view);
        }
    }
}