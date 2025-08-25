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
        // Ocultar proyectos lógicos de tareas
        $hiddenNames = ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales'];
        $projects = array_values(array_filter($projects, function($p) use ($hiddenNames) {
            return !in_array($p['project_name'], $hiddenNames, true);
        }));
        $clans = $this->clanModel->getAll();
        
        // Estadísticas globales de tareas (todos los clanes)
        $taskStats = [
            'total' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0,
        ];
        $recentActivity = [];
        try {
            $db = Database::getConnection();
            $stmt = $db->query(
                "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                    SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN t.status != 'completed' AND t.due_date IS NOT NULL AND t.due_date < CURDATE() THEN 1 ELSE 0 END) AS overdue
                 FROM Tasks t
                 JOIN Projects p ON t.project_id = p.project_id
                 JOIN Clans c ON p.clan_id = c.clan_id
                 WHERE t.is_subtask = 0"
            );
            $row = $stmt ? $stmt->fetch() : null;
            if ($row) {
                $taskStats['total'] = (int)($row['total'] ?? 0);
                $taskStats['pending'] = (int)($row['pending'] ?? 0);
                $taskStats['in_progress'] = (int)($row['in_progress'] ?? 0);
                $taskStats['completed'] = (int)($row['completed'] ?? 0);
                $taskStats['overdue'] = (int)($row['overdue'] ?? 0);
            }
            
            $stmt2 = $db->prepare(
                "SELECT th.*, t.task_name, p.project_name, c.clan_name, u.full_name, u.username
                 FROM Task_History th
                 JOIN Tasks t ON th.task_id = t.task_id
                 JOIN Projects p ON t.project_id = p.project_id
                 LEFT JOIN Clans c ON p.clan_id = c.clan_id
                 LEFT JOIN Users u ON th.user_id = u.user_id
                 ORDER BY th.created_at DESC
                 LIMIT 10"
            );
            $stmt2->execute();
            $recentActivity = $stmt2->fetchAll();
        } catch (Exception $e) {
            error_log('Error al calcular stats o actividad reciente en admin/projects: ' . $e->getMessage());
        }
        
        $data = [
            'projects' => $projects,
            'clans' => $clans,
            'taskStats' => $taskStats,
            'recentActivity' => $recentActivity,
            'currentPage' => 'admin',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/projects', $data);
    }

    /**
     * Gestión de tareas globales (ADMIN) usando clan Olympo
     */
    public function tasks() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }

        $currentUser = $this->auth->getCurrentUser();

        // Asegurar clan "Olympo"
        $olympo = $this->clanModel->findByName('Olympo');
        if (!$olympo) {
            $clanId = $this->clanModel->create('Olympo', 'Dirección');
            $olympo = $this->clanModel->findById($clanId);
        }

        // Asegurar proyectos lógicos en Olympo
        $recurrentProject = $this->ensureProjectForClan($olympo['clan_id'], 'Tareas Recurrentes', $currentUser['user_id'] ?? 1);
        $eventualProject = $this->ensureProjectForClan($olympo['clan_id'], 'Tareas Eventuales', $currentUser['user_id'] ?? 1);

        // Miembros del clan Olympo
        $members = $this->clanModel->getMembers((int)$olympo['clan_id']);

        // Obtener filtros desde la URL
        $search = trim($_GET['search'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $assignedId = isset($_GET['assigned']) && $_GET['assigned'] !== '' ? (int)$_GET['assigned'] : null;
        $fromDate = isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) ? $_GET['from'] : null;
        $toDate = isset($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) ? $_GET['to'] : null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(100, max(10, (int)($_GET['perPage'] ?? 20)));

        // Obtener tareas del clan Olympo (todas para stats, filtradas para listado)
        $taskModel = new Task();
        $clanId = (int)$olympo['clan_id'];

        // Para estadísticas usamos todas
        $allForStats = $taskModel->getAllTasksByClanStrict($clanId, 1, 2000, '', '');
        $allTasks = $allForStats['tasks'] ?? [];

        // Para listado usamos filtros y paginación
        $tasksResult = $taskModel->getAllTasksByClanStrict($clanId, $page, $perPage, $search, $statusFilter, $assignedId, $fromDate, $toDate);
        $tasks = $tasksResult['tasks'] ?? [];

        // Calcular métricas de tareas del clan (sobre todas)
        $stats = [
            'total' => 0,
            'completed' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'overdue' => 0,
            'progress' => 0.0,
        ];
        $today = date('Y-m-d');
        foreach ($allTasks as $t) {
            $stats['total']++;
            $status = $t['status'] ?? 'pending';
            if (isset($stats[$status])) { $stats[$status]++; }
            if (!empty($t['due_date']) && $status !== 'completed' && $t['due_date'] < $today) {
                $stats['overdue']++;
            }
        }
        $stats['progress'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0.0;

        // Actividad reciente del clan
        $db = Database::getConnection();
        $history = [];
        try {
            $stmt = $db->prepare(
                "SELECT th.*, t.task_name, u.full_name, u.username
                 FROM Task_History th
                 JOIN Tasks t ON th.task_id = t.task_id
                 JOIN Projects p ON t.project_id = p.project_id
                 LEFT JOIN Users u ON th.user_id = u.user_id
                 WHERE p.clan_id = ?
                 ORDER BY th.created_at DESC
                 LIMIT 20"
            );
            $stmt->execute([$clanId]);
            $history = $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Error al obtener actividad del clan: ' . $e->getMessage());
        }

        $data = [
            'currentPage' => 'admin',
            'user' => $currentUser,
            'olympo' => $olympo,
            'recurrentProject' => $recurrentProject,
            'eventualProject' => $eventualProject,
            'members' => $members,
            'tasks' => $tasks,
            'pagination' => [
                'page' => (int)($tasksResult['page'] ?? $page),
                'per_page' => (int)($tasksResult['per_page'] ?? $perPage),
                'total' => (int)($tasksResult['total'] ?? count($tasks)),
                'total_pages' => (int)($tasksResult['total_pages'] ?? 1),
            ],
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'assigned' => $assignedId,
                'from' => $fromDate,
                'to' => $toDate,
                'page' => $page,
                'perPage' => $perPage,
            ],
            'stats' => $stats,
            'history' => $history
        ];

        $this->loadView('admin/tasks', $data);
    }

    private function ensureProjectForClan($clanId, $projectName, $createdByUserId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM Projects WHERE clan_id = ? AND project_name = ? LIMIT 1");
        $stmt->execute([$clanId, $projectName]);
        $project = $stmt->fetch();
        if ($project) { return $project; }

        $projectId = $this->projectModel->create($projectName, $projectName, (int)$clanId, (int)$createdByUserId, null, 0, 'automatic', null);
        return $this->projectModel->findById($projectId);
    }

    /**
     * Obtener datos de un proyecto (JSON)
     */
    public function getProject() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        $projectId = (int)($_GET['projectId'] ?? 0);
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID inválido'], 400);
        }
        $project = $this->projectModel->findById($projectId);
        if (!$project) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        Utils::jsonResponse(['success' => true, 'project' => [
            'project_id' => (int)$project['project_id'],
            'project_name' => $project['project_name'],
            'description' => $project['description'],
            'clan_id' => (int)$project['clan_id'],
            'status' => $project['status'],
            'time_limit' => $project['time_limit'] ?? null
        ]]);
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

        // Tareas del proyecto con conteo de subtareas
        $taskModel = new Task();
        $tasks = $taskModel->getByProject($projectId);
        
        // Añadir conteo de subtareas para cada tarea
        foreach ($tasks as &$task) {
            $subtasks = $taskModel->getSubtasks($task['task_id']);
            $task['subtasks_count'] = count($subtasks);
            $task['subtasks'] = $subtasks; // Para usar en el modal
            
            // Log de depuración
            error_log("Tarea {$task['task_id']} ({$task['task_name']}): {$task['subtasks_count']} subtareas");
        }
        unset($task); // Limpiar referencia

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
     * Obtener detalles completos de una tarea (AJAX)
     */
    public function getTaskDetails() {
        // Evitar cualquier salida que no sea JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        
        // Verificar autenticación para AJAX
        if (!$this->auth->isLoggedIn()) {
            Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
            return;
        }
        
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
            return;
        }

        $taskId = (int)($_GET['taskId'] ?? 0);
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
            return;
        }

        try {
            error_log("=== DEBUG getTaskDetails ===");
            error_log("Task ID: " . $taskId);
            
            $taskModel = new Task();
            $task = $taskModel->findById($taskId);
            
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
                return;
            }

            error_log("Task encontrada: " . $task['task_name']);

            // Obtener subtareas
            $subtasks = $taskModel->getSubtasks($taskId);
            error_log("Subtareas encontradas: " . count($subtasks));
            
            // Inicializar arrays
            $comments = [];
            $attachments = [];
            $subtaskComments = [];
            $subtaskAttachments = [];
            
            $db = Database::getConnection();
            
            // Obtener comentarios de la tarea principal
            try {
                $stmt = $db->prepare("
                    SELECT tc.comment_id, tc.comment_text, tc.created_at, tc.comment_type,
                           u.username, u.full_name, u.email
                    FROM Task_Comments tc
                    LEFT JOIN Users u ON tc.user_id = u.user_id
                    WHERE tc.task_id = ?
                    ORDER BY tc.created_at DESC
                ");
                $stmt->execute([$taskId]);
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                error_log("Comentarios de tarea encontrados: " . count($comments));
            } catch (Exception $e) {
                error_log('Error al obtener comentarios de tarea: ' . $e->getMessage());
                $comments = [];
            }

            // Obtener adjuntos de la tarea principal
            try {
                $stmt = $db->prepare("
                    SELECT ta.attachment_id, ta.filename, ta.file_path, ta.uploaded_at,
                           u.username, u.full_name
                    FROM Task_Attachments ta
                    LEFT JOIN Users u ON ta.uploaded_by = u.user_id
                    WHERE ta.task_id = ?
                    ORDER BY ta.uploaded_at DESC
                ");
                $stmt->execute([$taskId]);
                $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                error_log("Adjuntos de tarea encontrados: " . count($attachments));
            } catch (Exception $e) {
                error_log('Error al obtener adjuntos de tarea: ' . $e->getMessage());
                $attachments = [];
            }

            // Obtener comentarios y adjuntos de subtareas
            if (!empty($subtasks)) {
                $subtaskIds = array_column($subtasks, 'subtask_id');
                
                if (!empty($subtaskIds)) {
                    // Comentarios de subtareas
                    try {
                        $placeholders = implode(',', array_fill(0, count($subtaskIds), '?'));
                        $stmt = $db->prepare("
                            SELECT sc.comment_id, sc.subtask_id, sc.comment_text, sc.created_at,
                                   u.username, u.full_name, u.email,
                                   s.title as subtask_title
                            FROM Subtask_Comments sc
                            LEFT JOIN Users u ON sc.user_id = u.user_id
                            LEFT JOIN Subtasks s ON sc.subtask_id = s.subtask_id
                            WHERE sc.subtask_id IN ($placeholders)
                            ORDER BY sc.created_at DESC
                        ");
                        $stmt->execute($subtaskIds);
                        $subtaskComments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                        error_log("Comentarios de subtareas encontrados: " . count($subtaskComments));
                    } catch (Exception $e) {
                        error_log('Error al obtener comentarios de subtareas: ' . $e->getMessage());
                        $subtaskComments = [];
                    }

                    // Adjuntos de subtareas
                    try {
                        $placeholders = implode(',', array_fill(0, count($subtaskIds), '?'));
                        $stmt = $db->prepare("
                            SELECT sa.attachment_id, sa.subtask_id, sa.filename, sa.file_path, sa.uploaded_at,
                                   u.username, u.full_name,
                                   s.title as subtask_title
                            FROM Subtask_Attachments sa
                            LEFT JOIN Users u ON sa.uploaded_by = u.user_id
                            LEFT JOIN Subtasks s ON sa.subtask_id = s.subtask_id
                            WHERE sa.subtask_id IN ($placeholders)
                            ORDER BY sa.uploaded_at DESC
                        ");
                        $stmt->execute($subtaskIds);
                        $subtaskAttachments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                        error_log("Adjuntos de subtareas encontrados: " . count($subtaskAttachments));
                    } catch (Exception $e) {
                        error_log('Error al obtener adjuntos de subtareas: ' . $e->getMessage());
                        $subtaskAttachments = [];
                    }
                }
            }

            // Agrupar comentarios y adjuntos por subtarea
            $subtaskCommentsGrouped = [];
            $subtaskAttachmentsGrouped = [];

            foreach ($subtaskComments as $comment) {
                $subtaskId = $comment['subtask_id'];
                if (!isset($subtaskCommentsGrouped[$subtaskId])) {
                    $subtaskCommentsGrouped[$subtaskId] = [];
                }
                $subtaskCommentsGrouped[$subtaskId][] = $comment;
            }

            foreach ($subtaskAttachments as $attachment) {
                $subtaskId = $attachment['subtask_id'];
                if (!isset($subtaskAttachmentsGrouped[$subtaskId])) {
                    $subtaskAttachmentsGrouped[$subtaskId] = [];
                }
                $subtaskAttachmentsGrouped[$subtaskId][] = $attachment;
            }

            // Agregar comentarios y adjuntos a cada subtarea
            foreach ($subtasks as &$subtask) {
                $subtaskId = $subtask['subtask_id'];
                $subtask['comments'] = $subtaskCommentsGrouped[$subtaskId] ?? [];
                $subtask['attachments'] = $subtaskAttachmentsGrouped[$subtaskId] ?? [];
            }
            unset($subtask);

            // Obtener información del proyecto
            $projectModel = new Project();
            $project = $projectModel->findById($task['project_id']);
            
            // Añadir información del proyecto a la tarea
            $task['project_name'] = $project['project_name'] ?? '';
            
            // Obtener información del creador y asignado
            try {
                if (!empty($task['created_by'])) {
                    $stmt = $db->prepare("SELECT full_name, username FROM Users WHERE user_id = ?");
                    $stmt->execute([$task['created_by']]);
                    $creator = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($creator) {
                        $task['created_by_name'] = $creator['full_name'] ?: $creator['username'];
                    }
                }
                
                if (!empty($task['assigned_to'])) {
                    $stmt = $db->prepare("SELECT full_name, username FROM Users WHERE user_id = ?");
                    $stmt->execute([$task['assigned_to']]);
                    $assigned = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($assigned) {
                        $task['assigned_to_fullname'] = $assigned['full_name'] ?: $assigned['username'];
                    }
                }
            } catch (Exception $e) {
                error_log('Error al obtener usuarios: ' . $e->getMessage());
            }

            // Combinar todos los comentarios y adjuntos (tarea principal + subtareas)
            $allComments = array_merge($comments, $subtaskComments);
            $allAttachments = array_merge($attachments, $subtaskAttachments);

            $response = [
                'success' => true,
                'task' => $task,
                'subtasks' => $subtasks,
                'comments' => $allComments,
                'attachments' => $allAttachments,
                'taskComments' => $comments,
                'taskAttachments' => $attachments,
                'subtaskComments' => $subtaskCommentsGrouped,
                'subtaskAttachments' => $subtaskAttachmentsGrouped
            ];
            
            error_log("Respuesta preparada, enviando JSON...");
            
            Utils::jsonResponse($response);
            
        } catch (Exception $e) {
            error_log('Error general en getTaskDetails: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
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
        
        // Fecha límite opcional
        $timeLimitRaw = trim($_POST['timeLimit'] ?? '');
        $timeLimit = ($timeLimitRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimitRaw)) ? $timeLimitRaw : null;

        $projectId = $this->projectModel->create($projectName, $description, $clanId, $currentUser['user_id'], null, 0, 'automatic', $timeLimit);
        
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
     * Actualizar proyecto (ADMIN)
     */
    public function updateProject() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('admin/projects');
        }

        $projectId = (int)($_POST['projectId'] ?? 0);
        $projectName = Utils::sanitizeInput($_POST['projectName'] ?? '');
        $description = Utils::sanitizeInput($_POST['description'] ?? '');
        $clanId = (int)($_POST['clanId'] ?? 0);
        $status = isset($_POST['status']) ? Utils::sanitizeInput($_POST['status']) : null;
        $timeLimitRaw = trim($_POST['timeLimit'] ?? '');
        $timeLimit = ($timeLimitRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimitRaw)) ? $timeLimitRaw : null;

        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID inválido'], 400);
        }
        if (empty($projectName)) {
            Utils::jsonResponse(['success' => false, 'errors' => ['projectName' => 'El nombre del proyecto es requerido']], 400);
        }
        if ($clanId <= 0) {
            Utils::jsonResponse(['success' => false, 'errors' => ['clanId' => 'Debe seleccionar un clan válido']], 400);
        }

        $ok = $this->projectModel->update($projectId, $projectName, $description, $clanId, $status, $timeLimit);
        if ($ok) {
            Utils::jsonResponse(['success' => true, 'message' => 'Proyecto actualizado exitosamente']);
        }
        Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar proyecto'], 500);
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
        $dueDateRaw = trim($_POST['dueDate'] ?? '');
        $dueDate = ($dueDateRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDateRaw)) ? $dueDateRaw : null;
        // Nueva interfaz: lista de checkboxes "assignedUsers[]" (opcional)
        $assignedUsers = [];
        if (isset($_POST['assignedUsers'])) {
            $assignedUsers = is_array($_POST['assignedUsers']) ? array_map('intval', $_POST['assignedUsers']) : [ (int)$_POST['assignedUsers'] ];
        }
        // Subtareas (opcional)
        $subtasks = [];
        if (isset($_POST['subtasks']) && is_array($_POST['subtasks'])) {
            foreach ($_POST['subtasks'] as $subtaskTitle) {
                $subtaskTitle = trim(Utils::sanitizeInput($subtaskTitle));
                if ($subtaskTitle !== '') {
                    $subtasks[] = [
                        'title' => $subtaskTitle,
                        'description' => '',
                        'percentage' => 0,
                        'due_date' => null,
                        'priority' => Task::PRIORITY_MEDIUM,
                        'assigned_user_id' => null
                    ];
                }
            }
        }
        // Repeticiones para tareas recurrentes (opcional, fechas AAAA-MM-DD)
        $repeatDates = [];
        if (isset($_POST['repeatDates'])) {
            $repeatDates = is_array($_POST['repeatDates']) ? array_values(array_filter($_POST['repeatDates'], function($d){ return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d); })) : [];
        }

        if ($projectId <= 0 || $taskName === '') {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        // Validar proyecto y obtener clan
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT clan_id, project_name FROM Projects WHERE project_id = ?');
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();
        if (!$project) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        $clanId = (int)$project['clan_id'];
        $projectName = $project['project_name'];
        
        // Log para debugging de tareas recurrentes/eventuales
        error_log("=== ADMIN CREANDO TAREA ===");
        error_log("Project ID: $projectId");
        error_log("Project Name: $projectName");
        error_log("Task Name: $taskName");
        error_log("Subtasks: " . print_r($subtasks, true));
        error_log("Repeat Dates: " . print_r($repeatDates, true));
        error_log("Es Tarea Recurrente: " . ($projectName === 'Tareas Recurrentes' ? 'SÍ' : 'NO'));
        error_log("Es Tarea Eventual: " . ($projectName === 'Tareas Eventuales' ? 'SÍ' : 'NO'));

        // validación de assignedUsers contra clan
        if (!empty($assignedUsers)) {
            $placeholders = implode(',', array_fill(0, count($assignedUsers), '?'));
            $check = $db->prepare("SELECT user_id FROM Clan_Members WHERE clan_id = ? AND user_id IN ($placeholders)");
            $params = array_merge([$clanId], $assignedUsers);
            $check->execute($params);
            $validIds = array_column($check->fetchAll(), 'user_id');
            $assignedUsers = array_values(array_intersect($assignedUsers, $validIds));
        }

        // Crear tarea y asignar múltiples usuarios si se enviaron
        $taskModel = new Task();
        $currentUser = $this->auth->getCurrentUser();
        try {
            if (!empty($assignedUsers) || !empty($subtasks)) {
                $taskId = $taskModel->createAdvanced(
                    $projectId, 
                    $taskName, 
                    $description, 
                    $dueDate, 
                    $clanId, 
                    Task::PRIORITY_MEDIUM, 
                    $currentUser['user_id'] ?? null, 
                    $assignedUsers,
                    $subtasks  // Agregar subtareas al método
                );
            } else {
                $taskId = $taskModel->create($projectId, $taskName, $description, null, Task::PRIORITY_MEDIUM, $dueDate, $currentUser['user_id'] ?? null);
            }

            // Si se recibieron repeatDates válidas, crear duplicados
            if ($taskId && !empty($repeatDates)) {
                error_log("Creando tareas duplicadas para proyecto: $projectName");
                foreach ($repeatDates as $rDate) {
                    if ($rDate === $dueDate) { 
                        error_log("Saltando fecha duplicada: $rDate (igual a fecha base)");
                        continue; 
                    }
                    error_log("Creando tarea duplicada para fecha: $rDate");
                    if (!empty($assignedUsers) || !empty($subtasks)) {
                        $duplicateTaskId = $taskModel->createAdvanced($projectId, $taskName, $description, $rDate, $clanId, Task::PRIORITY_MEDIUM, $currentUser['user_id'] ?? null, $assignedUsers, $subtasks);
                        error_log("Tarea duplicada creada con ID: $duplicateTaskId");
                    } else {
                        $duplicateTaskId = $taskModel->create($projectId, $taskName, $description, null, Task::PRIORITY_MEDIUM, $rDate, $currentUser['user_id'] ?? null);
                        error_log("Tarea duplicada creada con ID: $duplicateTaskId");
                    }
                }
            }

            if ($taskId) {
                // Notificar asignación si aplica
                try {
                    if (!empty($assignedUsers)) {
                        $sent = (new NotificationService())->notifyTaskAssigned((int)$taskId, $assignedUsers);
                        error_log('task_assigned notifications sent: ' . (int)$sent);
                    }
                } catch (Exception $e) { error_log('Notif error (task_assigned): ' . $e->getMessage()); }
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
     * Obtener subtareas de una tarea
     */
    public function getTaskSubtasks() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $taskId = (int)($_GET['taskId'] ?? 0);
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        try {
            $taskModel = new Task();
            
            // Verificar que la tarea existe
            $task = $taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }

            // Obtener subtareas
            $subtasks = $taskModel->getSubtasks($taskId);

            Utils::jsonResponse([
                'success' => true, 
                'task' => [
                    'task_id' => $task['task_id'],
                    'task_name' => $task['task_name'],
                    'project_name' => $task['project_name']
                ],
                'subtasks' => $subtasks
            ]);

        } catch (Exception $e) {
            error_log('Error al obtener subtareas: ' . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno'], 500);
        }
    }

    /**
     * Añadir subtareas a una tarea existente
     */
    public function addSubtasksToTask() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $taskId = (int)($_POST['taskId'] ?? 0);
        $subtasks = $_POST['subtasks'] ?? [];

        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        if (empty($subtasks) || !is_array($subtasks)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Debe proporcionar al menos una subtarea'], 400);
        }

        try {
            $taskModel = new Task();
            
            // Verificar que la tarea existe
            $task = $taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }

            $currentUser = $this->auth->getCurrentUser();
            $createdByUserId = $currentUser['user_id'] ?? 1;
            
            // Crear cada subtarea
            $createdCount = 0;
            foreach ($subtasks as $subtaskTitle) {
                $subtaskTitle = trim(Utils::sanitizeInput($subtaskTitle));
                if ($subtaskTitle !== '') {
                    $subtaskId = $taskModel->createSubtaskAdvanced(
                        $taskId,
                        $subtaskTitle,
                        $createdByUserId,
                        '', // descripción vacía
                        0,  // porcentaje inicial
                        null, // sin fecha límite
                        Task::PRIORITY_MEDIUM,
                        null  // sin usuario asignado inicialmente
                    );
                    
                    if ($subtaskId) {
                        $createdCount++;
                    }
                }
            }

            if ($createdCount > 0) {
                Utils::jsonResponse([
                    'success' => true, 
                    'message' => "Se crearon {$createdCount} subtareas exitosamente",
                    'created_count' => $createdCount
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'No se pudo crear ninguna subtarea'], 500);
            }

        } catch (Exception $e) {
            error_log('Error al añadir subtareas: ' . $e->getMessage());
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
     * Eliminar usuario (ADMIN)
     */
    public function deleteUser() {
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

        // No permitir eliminar al super admin (opcional) ni a sí mismo
        $currentUser = $this->auth->getCurrentUser();
        if ($currentUser && (int)$currentUser['user_id'] === $userId) {
            Utils::jsonResponse(['success' => false, 'message' => 'No puedes eliminar tu propio usuario'], 400);
        }
        $userRole = $this->roleModel->getUserRole($userId);
        if ($userRole && $userRole['role_name'] === 'super_admin') {
            Utils::jsonResponse(['success' => false, 'message' => 'No se puede eliminar al super administrador'], 403);
        }

        // Verificar existencia
        $exists = $this->userModel->findByIdAnyStatus($userId);
        if (!$exists) {
            Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Eliminar en cascada mínima (roles y pertenencias)
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $db->prepare('DELETE FROM User_Roles WHERE user_id = ?')->execute([$userId]);
            $db->prepare('DELETE FROM Clan_Members WHERE user_id = ?')->execute([$userId]);
            $db->prepare('DELETE FROM Task_Assignments WHERE user_id = ?')->execute([$userId]);
            // Opcional: mantener historial, no borrar Task_History/Comments
            $ok = $this->userModel->delete($userId);
            if (!$ok) { throw new Exception('Error al eliminar usuario'); }
            $db->commit();
            Utils::jsonResponse(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) { $db->rollBack(); }
            Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Ejecutar trabajos de notificación manualmente (debug)
     */
    public function runNotificationJobs() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        try {
            $service = new NotificationService();
            $due = $service->notifyTaskDueSoonMulti();
            $over = $service->notifyTaskOverdue();
            Utils::jsonResponse(['success' => true, 'dueSoonSent' => (int)$due, 'overdueSent' => (int)$over]);
        } catch (Exception $e) {
            Utils::jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
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
                ['label' => 'Sistema', 'value' => 'Polaris'],
                ['label' => 'Fecha', 'value' => date('Y-m-d H:i')]
            ],
            ['label' => 'Ir al panel', 'url' => APP_URL . '?route=admin']
        );
                    $ok = $mailer->sendHtml($to, 'Polaris • Notificación de prueba', $html);

        if ($ok) {
            Utils::jsonResponse(['success' => true, 'message' => 'Correo enviado correctamente']);
        }
        Utils::jsonResponse(['success' => false, 'message' => 'Fallo al enviar: ' . $mailer->getLastError()], 500);
    }

    // Ya no se usa: se reemplazó por EmailTemplate::render
}