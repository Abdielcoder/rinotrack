<?php

class AdminController {
    private $auth;
    private $userModel;
    private $projectModel;
    private $clanModel;
    private $roleModel;
    private $notificationModel;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->clanModel = new Clan();
        $this->roleModel = new Role();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Página principal del panel de administración
     */
    public function index() {
        // Verificar autenticación y permisos
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        // Obtener estadísticas generales
        $data = [
            'userStats' => $this->userModel->getStats(),
            'projectStats' => $this->projectModel->getStats(),
            'clanStats' => $this->clanModel->getStats(),
            'roleStats' => $this->roleModel->getStats(),
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/dashboard', $data);
    }
    
    /**
     * Gestión de usuarios
     */
    public function users() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $search = $_GET['search'] ?? '';
        $users = empty($search) ? 
            $this->userModel->getAllWithRoles() : 
            $this->userModel->search($search);
        
        $data = [
            'users' => $users,
            'roles' => $this->roleModel->getAll(),
            'search' => $search,
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/users', $data);
    }
    
    /**
     * Crear nuevo usuario
     */
    public function createUser() {
        // DEBUG: Log para diagnosticar el problema
        error_log("=== CREATE USER DEBUG ===");
        error_log("createUser called - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            error_log("Access denied - user doesn't have admin access");
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
            Utils::redirect('admin/users');
        }
        
        // Validar datos
        $username = Utils::sanitizeInput($_POST['username'] ?? '');
        $email = Utils::sanitizeInput($_POST['email'] ?? '');
        $fullName = Utils::sanitizeInput($_POST['fullName'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['roleId'] ?? 0);
        
        error_log("Parsed data - username: '$username', email: '$email', fullName: '$fullName', roleId: $roleId, password length: " . strlen($password));
        
        $errors = [];
        
        // Validaciones
        if (empty($username) || strlen($username) < 3) {
            $errors['username'] = 'El nombre de usuario debe tener al menos 3 caracteres';
        }
        
        if (empty($email) || !Utils::isValidEmail($email)) {
            $errors['email'] = 'Debe proporcionar un email válido';
        }
        
        if (empty($fullName)) {
            $errors['fullName'] = 'El nombre completo es requerido';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($roleId <= 0) {
            $errors['roleId'] = 'Debe seleccionar un rol válido';
        }
        
        // Verificar si ya existe
        if ($this->userModel->exists($username, $email)) {
            $errors['general'] = 'Ya existe un usuario con ese nombre de usuario o email';
        }
        
        if (!empty($errors)) {
            error_log("Validation errors: " . print_r($errors, true));
            Utils::jsonResponse(['success' => false, 'errors' => $errors], 400);
        }
        
        // Crear usuario
        $userId = $this->userModel->create($username, $email, $password, $fullName);
        
        if ($userId) {
            // Asignar rol
            $roleAssigned = $this->roleModel->assignToUser($userId, $roleId);
            
            if ($roleAssigned) {
                Utils::jsonResponse(['success' => true, 'message' => 'Usuario creado exitosamente']);
            } else {
                // Si falla la asignación de rol, eliminar el usuario creado
                $this->userModel->delete($userId);
                Utils::jsonResponse(['success' => false, 'message' => 'Error al asignar rol al usuario'], 500);
            }
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al crear usuario'], 500);
        }
    }
    
    /**
     * Actualizar usuario
     */
    public function updateUser() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/users');
        }
        
        $userId = (int)($_POST['userId'] ?? 0);
        $username = Utils::sanitizeInput($_POST['username'] ?? '');
        $email = Utils::sanitizeInput($_POST['email'] ?? '');
        $fullName = Utils::sanitizeInput($_POST['fullName'] ?? '');
        $roleId = (int)($_POST['roleId'] ?? 0);
        $isActive = isset($_POST['isActive']) ? 1 : 0;
        
        if ($userId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de usuario inválido'], 400);
        }
        
        // Actualizar usuario
        $result = $this->userModel->update($userId, $username, $email, $fullName, $isActive);
        
        if ($result && $roleId > 0) {
            // Actualizar rol
            $this->roleModel->assignToUser($userId, $roleId);
        }
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar usuario'], 500);
        }
    }
    
    /**
     * Cambiar estado activo/inactivo del usuario
     */
    public function toggleUserStatus() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/users');
        }
        
        $userId = (int)($_POST['userId'] ?? 0);
        
        if ($userId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de usuario inválido'], 400);
        }
        
        // Verificar que el usuario existe (incluyendo usuarios desactivados)
        $user = $this->userModel->findByIdAnyStatus($userId);
        if (!$user) {
            Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        
        // Protección: No permitir desactivar al super admin
        $userRole = $this->roleModel->getUserRole($userId);
        if ($userRole && $userRole['role_name'] === 'super_admin') {
            Utils::jsonResponse(['success' => false, 'message' => 'No se puede desactivar al super administrador'], 403);
        }
        
        // Cambiar el estado (toggle)
        $newStatus = $user['is_active'] ? 0 : 1;
        $result = $this->userModel->updateStatus($userId, $newStatus);
        
        if ($result) {
            $statusText = $newStatus ? 'activado' : 'desactivado';
            Utils::jsonResponse(['success' => true, 'message' => "Usuario {$statusText} exitosamente"]);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al cambiar estado del usuario'], 500);
        }
    }
    
    /**
     * Gestión de proyectos
     */
    public function projects() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $projects = $this->projectModel->getAllWithClanInfo();
        $clans = $this->clanModel->getAll();
        
        $data = [
            'projects' => $projects,
            'clans' => $clans,
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/projects', $data);
    }

    /**
     * Detalles de un proyecto con estado de tareas
     */
    public function projectDetails() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }

        $projectId = (int)($_GET['projectId'] ?? 0);
        if ($projectId <= 0) {
            Utils::redirect('admin/projects');
        }

        // Obtener datos del proyecto
        $project = $this->projectModel->findById($projectId);
        if (!$project) {
            Utils::redirect('admin/projects');
        }

        // Tareas del proyecto
        $tasks = (new Task())->getByProject($projectId);

        // Calcular métricas de tareas
        $stats = [
            'total' => 0,
            'completed' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'overdue' => 0,
        ];
        $today = date('Y-m-d');
        foreach ($tasks as $t) {
            $stats['total']++;
            $status = $t['status'] ?? 'pending';
            if (isset($stats[$status])) $stats[$status]++;
            if (!empty($t['due_date']) && $status !== 'completed' && $t['due_date'] < $today) {
                $stats['overdue']++;
            }
        }
        $stats['progress'] = $project['progress_percentage'] ?? 0;

        // Actividad reciente (historial)
        $db = Database::getConnection();
        $history = [];
        try {
            $stmt = $db->prepare(
                "SELECT th.*, t.task_name, u.full_name, u.username
                 FROM Task_History th
                 JOIN Tasks t ON th.task_id = t.task_id
                 LEFT JOIN Users u ON th.user_id = u.user_id
                 WHERE t.project_id = ?
                 ORDER BY th.created_at DESC
                 LIMIT 15"
            );
            $stmt->execute([$projectId]);
            $history = $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Error al obtener actividad del proyecto: ' . $e->getMessage());
        }

        $data = [
            'project' => $project,
            'tasks' => $tasks,
            'stats' => $stats,
            'history' => $history,
            'clanMembers' => $this->clanModel->getMembers((int)$project['clan_id']),
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];

        $this->loadView('admin/project_details', $data);
    }

    /**
     * Eliminar proyecto (ADMIN)
     */
    public function deleteProject() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/projects');
        }

        $projectId = (int)($_POST['projectId'] ?? 0);
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
        }

        try {
            // Validar existencia
            $projects = $this->projectModel->getByClan(0); // placeholder para mantener interfaz
            // El modelo Project no tiene findById visible aquí; eliminamos por SQL directo seguro
            $db = Database::getConnection();
            $check = $db->prepare('SELECT project_id FROM Projects WHERE project_id = ?');
            $check->execute([$projectId]);
            if (!$check->fetch()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
            }

            // Eliminar dependencias básicas
            $db->beginTransaction();
            $db->prepare('DELETE FROM Task_Attachments WHERE task_id IN (SELECT task_id FROM Tasks WHERE project_id = ?)')->execute([$projectId]);
            $db->prepare('DELETE FROM Task_Comments WHERE task_id IN (SELECT task_id FROM Tasks WHERE project_id = ?)')->execute([$projectId]);
            $db->prepare('DELETE FROM Task_Assignments WHERE task_id IN (SELECT task_id FROM Tasks WHERE project_id = ?)')->execute([$projectId]);
            $db->prepare('DELETE FROM Task_History WHERE task_id IN (SELECT task_id FROM Tasks WHERE project_id = ?)')->execute([$projectId]);
            $db->prepare('DELETE FROM Tasks WHERE project_id = ?')->execute([$projectId]);
            $db->prepare('DELETE FROM Projects WHERE project_id = ?')->execute([$projectId]);
            $db->commit();

            Utils::jsonResponse(['success' => true, 'message' => 'Proyecto eliminado exitosamente']);
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) { $db->rollBack(); }
            error_log('Error al eliminar proyecto: ' . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar proyecto'], 500);
        }
    }
    
    /**
     * Crear nuevo proyecto
     */
    public function createProject() {
        // DEBUG temporal
        error_log("DEBUG createProject - Iniciando método");
        error_log("DEBUG createProject - SESSION: " . json_encode($_SESSION ?? []));
        
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/projects');
        }
        
        $projectName = Utils::sanitizeInput($_POST['projectName'] ?? '');
        $description = Utils::sanitizeInput($_POST['description'] ?? '');
        $clanId = (int)($_POST['clanId'] ?? 0);
        $currentUser = $this->auth->getCurrentUser();
        
        // DEBUG temporal
        error_log("DEBUG createProject - currentUser: " . json_encode($currentUser));
        error_log("DEBUG createProject - user_id: " . ($currentUser['user_id'] ?? 'NULL'));
        
        $errors = [];
        
        if (empty($projectName)) {
            $errors['projectName'] = 'El nombre del proyecto es requerido';
        }
        
        if ($clanId <= 0) {
            $errors['clanId'] = 'Debe seleccionar un clan válido';
        }
        
        if (!empty($errors)) {
            Utils::jsonResponse(['success' => false, 'errors' => $errors], 400);
        }
        
        $projectId = $this->projectModel->create($projectName, $description, $clanId, $currentUser['user_id']);
        
        if ($projectId) {
            // Disparar notificación: proyecto asignado a un clan
            try {
                (new NotificationService())->notifyProjectAssignedToClan($projectId);
            } catch (Exception $e) {
                error_log('Notif error (project_assigned_to_clan): ' . $e->getMessage());
            }
            Utils::jsonResponse(['success' => true, 'message' => 'Proyecto creado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al crear proyecto'], 500);
        }
    }
    
    /**
     * Gestión de clanes
     */
    public function clans() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $clans = $this->clanModel->getAll();
        $users = $this->userModel->getAllWithRoles();
        
        $data = [
            'clans' => $clans,
            'users' => $users,
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/clans', $data);
    }
    
    /**
     * Crear nuevo clan
     */
    public function createClan() {
        try {
            error_log("=== CREATE CLAN DEBUG ===");
            error_log("createClan called - Method: " . $_SERVER['REQUEST_METHOD']);
            error_log("POST data: " . print_r($_POST, true));
            error_log("SESSION data: " . print_r($_SESSION ?? [], true));
            
            // Verificar autenticación básica
            if (!$this->auth->isLoggedIn()) {
                error_log("Auth failed: User not logged in");
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no autenticado'], 401);
            }
            error_log("Basic auth check passed");
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            }
            
            // Obtener datos del formulario
            $clanName = Utils::sanitizeInput($_POST['clanName'] ?? '');
            $clanDepartamento = Utils::sanitizeInput($_POST['clanDepartamento'] ?? '');
            
            error_log("Parsed data - clanName: '$clanName', clanDepartamento: '$clanDepartamento'");
            
            // Validar datos básicos
            if (empty($clanName)) {
                error_log("Validation failed: Empty clan name");
                Utils::jsonResponse(['success' => false, 'message' => 'El nombre del clan es requerido'], 400);
            }
            
            // Verificar si ya existe (opcional, para evitar duplicados)
            try {
                if ($this->clanModel->exists($clanName)) {
                    error_log("Validation failed: Clan name already exists");
                    Utils::jsonResponse(['success' => false, 'message' => 'Ya existe un clan con ese nombre'], 400);
                }
            } catch (Exception $e) {
                error_log("Warning: Could not check if clan exists: " . $e->getMessage());
                // Continuar sin esta validación si falla
            }
            
            error_log("About to create clan in database");
            $clanId = $this->clanModel->create($clanName, $clanDepartamento);
            error_log("Database create result: " . ($clanId ? $clanId : 'false'));
            
            if ($clanId) {
                error_log("Clan created successfully with ID: $clanId");
                Utils::jsonResponse(['success' => true, 'message' => 'Clan creado exitosamente']);
            } else {
                error_log("Database create failed");
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear clan en la base de datos'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Exception in createClan: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        } catch (Error $e) {
            error_log("Fatal error in createClan: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error fatal del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Actualizar clan
     */
    public function updateClan() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/clans');
        }
        
        $clanId = (int)($_POST['clanId'] ?? 0);
        $clanName = Utils::sanitizeInput($_POST['clanName'] ?? '');
        $clanDepartamento = Utils::sanitizeInput($_POST['clanDepartamento'] ?? '');
        
        if ($clanId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan inválido'], 400);
        }
        
        if (empty($clanName)) {
            Utils::jsonResponse(['success' => false, 'message' => 'El nombre del clan es requerido'], 400);
        }
        
        // Verificar que el clan existe
        $clan = $this->clanModel->findById($clanId);
        if (!$clan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Clan no encontrado'], 404);
        }
        
        // Verificar si el nuevo nombre ya existe (excepto para el clan actual)
        if ($clan['clan_name'] !== $clanName && $this->clanModel->exists($clanName)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Ya existe un clan con ese nombre'], 400);
        }
        
        $result = $this->clanModel->update($clanId, $clanName, $clanDepartamento);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Clan actualizado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar clan'], 500);
        }
    }
    
    /**
     * Eliminar clan
     */
    public function deleteClan() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/clans');
        }
        
        $clanId = (int)($_POST['clanId'] ?? 0);
        
        if ($clanId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan inválido'], 400);
        }
        
        // Verificar que el clan existe
        $clan = $this->clanModel->findById($clanId);
        if (!$clan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Clan no encontrado'], 404);
        }
        
        // Intentar eliminar el clan
        $result = $this->clanModel->delete($clanId);
        
        if ($result === true) {
            Utils::jsonResponse(['success' => true, 'message' => 'Clan eliminado exitosamente']);
        } elseif (is_array($result) && isset($result['error'])) {
            Utils::jsonResponse(['success' => false, 'message' => $result['error']], 400);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar clan'], 500);
        }
    }

    /**
     * Agregar tarea a un proyecto (ADMIN)
     * Respeta miembros del clan del proyecto
     */
    public function addTask() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/projects');
        }

        $projectId = (int)($_POST['projectId'] ?? 0);
        $taskName = Utils::sanitizeInput($_POST['taskName'] ?? '');
        $description = Utils::sanitizeInput($_POST['description'] ?? '');
        // Nueva interfaz: lista de checkboxes "assignedUsers[]" (opcional)
        $assignedUsers = [];
        if (isset($_POST['assignedUsers'])) {
            $assignedUsers = is_array($_POST['assignedUsers']) ? array_map('intval', $_POST['assignedUsers']) : [ (int)$_POST['assignedUsers'] ];
        }

        if ($projectId <= 0 || $taskName === '') {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        // Validar proyecto y obtener clan
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT clan_id FROM Projects WHERE project_id = ?');
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();
        if (!$project) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        $clanId = (int)$project['clan_id'];

        // Si hay usuario asignado, validar que pertenece al clan
        if ($assignedToUserId) {
            $stmt = $db->prepare('SELECT 1 FROM Clan_Members WHERE clan_id = ? AND user_id = ?');
            $stmt->execute([$clanId, $assignedToUserId]);
            if (!$stmt->fetch()) {
                Utils::jsonResponse(['success' => false, 'message' => 'El usuario asignado no pertenece al clan del proyecto'], 400);
            }
        }

        // Crear tarea y asignar múltiples usuarios si se enviaron
        $taskModel = new Task();
        $currentUser = $this->auth->getCurrentUser();
        try {
            if (!empty($assignedUsers)) {
                $taskId = $taskModel->createAdvanced($projectId, $taskName, $description, null, $clanId, Task::PRIORITY_MEDIUM, $currentUser['user_id'] ?? null, $assignedUsers);
            } else {
                $taskId = $taskModel->create($projectId, $taskName, $description, null, Task::PRIORITY_MEDIUM, null, $currentUser['user_id'] ?? null);
            }

            if ($taskId) {
                Utils::jsonResponse(['success' => true, 'message' => 'Tarea creada exitosamente', 'task_id' => (int)$taskId]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear tarea'], 500);
            }
        } catch (Exception $e) {
            error_log('addTask exception: ' . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtener miembros de un clan
     */
    public function getClanMembers() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        $clanId = (int)($_GET['clanId'] ?? 0);
        
        if ($clanId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan inválido'], 400);
        }
        
        $members = $this->clanModel->getMembers($clanId);
        Utils::jsonResponse(['success' => true, 'members' => $members]);
    }
    
    /**
     * Obtener detalles completos de un clan
     */
    public function getClanDetails() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        $clanId = (int)($_GET['clanId'] ?? 0);
        
        if ($clanId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan inválido'], 400);
        }
        
        // Obtener información básica del clan
        $clan = $this->clanModel->findById($clanId);
        if (!$clan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Clan no encontrado'], 404);
        }
        
        // Obtener miembros del clan con información adicional
        $members = $this->clanModel->getMembers($clanId);
        
        // Obtener proyectos del clan
        $projects = $this->projectModel->getByClan($clanId);
        
        // Preparar datos completos del clan
        $clanDetails = [
            'clan_id' => $clan['clan_id'],
            'clan_name' => $clan['clan_name'],
            'created_at' => $clan['created_at'],
            'member_count' => $clan['member_count'],
            'project_count' => $clan['project_count'],
            'members' => $members,
            'projects' => $projects
        ];
        
        Utils::jsonResponse(['success' => true, 'clan' => $clanDetails]);
    }
    
    /**
     * Agregar miembro a clan
     */
    public function addClanMember() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/clans');
        }
        
        $clanId = (int)($_POST['clanId'] ?? 0);
        $userId = (int)($_POST['userId'] ?? 0);
        
        if ($clanId <= 0 || $userId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan o usuario inválido'], 400);
        }
        
        // Verificar que el clan existe
        $clan = $this->clanModel->findById($clanId);
        if (!$clan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Clan no encontrado'], 404);
        }
        
        // Verificar que el usuario existe
        $user = $this->userModel->findById($userId);
        if (!$user) {
            Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        
        // Agregar miembro al clan
        $result = $this->clanModel->addMember($clanId, $userId);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Miembro agregado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar miembro'], 500);
        }
    }
    
    /**
     * Remover miembro de clan
     */
    public function removeClanMember() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/clans');
        }
        
        $clanId = (int)($_POST['clanId'] ?? 0);
        $userId = (int)($_POST['userId'] ?? 0);
        
        if ($clanId <= 0 || $userId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de clan o usuario inválido'], 400);
        }
        
        // Remover miembro del clan
        $result = $this->clanModel->removeMember($clanId, $userId);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Miembro removido exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al remover miembro'], 500);
        }
    }
    
    /**
     * Verificar si el usuario tiene acceso de administración
     */
    private function hasAdminAccess() {
        $currentUser = $this->auth->getCurrentUser();
        if (!$currentUser) {
            return false;
        }
        
        return $this->roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
    }
    
    /**
     * Verificar autenticación
     */
    private function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
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

    /**
     * Panel de notificaciones (Admin)
     */
    public function notifications() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }

        $settings = Notification::getAllSettings();

        $data = [
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser(),
            'settings' => $settings
        ];
        $this->loadView('admin/notifications', $data);
    }

    /**
     * Guardar ajustes de notificaciones
     */
    public function updateNotificationSettings() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/notifications');
        }

        $keys = [
            'project_assigned_to_clan' => null,
            'task_due_soon' => null,
            'task_overdue' => null,
            // slots configurables de días
            'task_due_soon_1' => 'task_due_soon_1_days',
            'task_due_soon_2' => 'task_due_soon_2_days',
            'task_due_soon_3' => 'task_due_soon_3_days'
        ];

        foreach ($keys as $key => $daysField) {
            $enabled = isset($_POST[$key]) ? 1 : 0;
            $recipients = isset($_POST[$key . '_recipients']) ? trim($_POST[$key . '_recipients']) : null;
            $valueInt = null;
            if ($daysField) {
                $val = isset($_POST[$daysField]) ? (int)$_POST[$daysField] : null;
                $valueInt = ($val !== null && $val >= 0 && $val <= 365) ? $val : null;
            }
            Notification::setSetting($key, $enabled, $recipients ?: null, $valueInt);
        }

        Utils::jsonResponse(['success' => true, 'message' => 'Ajustes guardados']);
    }

    /**
     * Enviar correo de prueba
     */
    public function sendTestNotification() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $to = Utils::sanitizeInput($_POST['to'] ?? $this->auth->getCurrentUser()['email'] ?? '');
        if (!$to || !Utils::isValidEmail($to)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Email destino inválido'], 400);
        }

        $mailer = new Mailer();
        $html = EmailTemplate::render(
            'Notificación de prueba',
            '<p>Este es un correo de prueba con el nuevo diseño.</p>',
            [
                ['label' => 'Sistema', 'value' => 'RinoTrack'],
                ['label' => 'Fecha', 'value' => date('Y-m-d H:i')]
            ],
            ['label' => 'Ir al panel', 'url' => APP_URL . '?route=admin']
        );
        $ok = $mailer->sendHtml($to, 'RinoTrack • Notificación de prueba', $html);

        if ($ok) {
            Utils::jsonResponse(['success' => true, 'message' => 'Correo enviado correctamente']);
        }
        Utils::jsonResponse(['success' => false, 'message' => 'Fallo al enviar: ' . $mailer->getLastError()], 500);
    }

    // Ya no se usa: se reemplazó por EmailTemplate::render
}