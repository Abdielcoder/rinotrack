<?php

class ClanMemberController {
    private $auth;
    private $userModel;
    private $projectModel;
    private $clanModel;
    private $roleModel;
    private $kpiModel;
    private $taskModel;
    private $subtaskModel;
    private $db;
    private $currentUser;
    private $userClan;

    public function __construct() {
        $this->auth = new Auth();
        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->clanModel = new Clan();
        $this->roleModel = new Role();
        $this->kpiModel = new KPI();
        $this->taskModel = new Task();
        $this->subtaskModel = new Subtask();
        $this->db = Database::getConnection();

        $this->currentUser = $this->auth->getCurrentUser();
        $this->userClan = $this->currentUser ? $this->userModel->getUserClan($this->currentUser['user_id']) : null;
    }

    public function index() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }

        // Obtener TODAS las tareas del usuario usando la nueva función optimizada
        $allUserTasks = $this->taskModel->getAllUserTasksForDashboard($this->currentUser['user_id']);
        
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            // Usuario sin clan pero puede tener tareas asignadas de otros clanes
            $userTaskStats = $this->getUserTaskStatsForAllClans($this->currentUser['user_id'], null);
            $ownContribution = $this->getOwnContribution($this->currentUser, $userTaskStats);
            
            $data = [
                'currentPage' => 'clan_member',
                'user' => $this->currentUser,
                'clan' => ['clan_name' => 'Tareas Asignadas', 'clan_departamento' => 'Múltiples clanes', 'clan_id' => null],
                'userTaskStats' => $userTaskStats,
                'ownContribution' => $ownContribution,
                'clanMembers' => [], // Sin miembros del clan ya que no tiene clan
                'kanbanTasks' => $this->getKanbanTasksForUser($this->currentUser['user_id'], null),
                'allUserTasks' => $allUserTasks // Agregar todas las tareas del usuario
            ];
            $this->loadView('clan_member/dashboard', $data);
            return;
        }

        // Estadísticas del usuario (incluye tareas de otros clanes)
        $userTaskStats = $this->getUserTaskStatsForAllClans($this->currentUser['user_id'], $this->userClan['clan_id'] ?? null);
        $ownContribution = $this->getOwnContribution($this->currentUser, $userTaskStats);
        
        // Obtener proyectos donde el usuario tiene tareas asignadas (incluyendo de otros clanes)
        $projects = $this->projectModel->getProjectsForUser($this->currentUser['user_id'], $this->userClan['clan_id'] ?? null);
        
        $ownTasksDetails = $this->getUserTasksForModalAllClans($this->currentUser['user_id'], $this->userClan['clan_id'] ?? null);
        
        // Verificar si se quiere editar una tarea
        $editTaskId = (int)($_GET['edit_task'] ?? 0);
        $taskToEdit = null;
        
        if ($editTaskId > 0) {
            // Obtener datos de la tarea para editar
            $taskToEdit = $this->taskModel->findById($editTaskId);
            if ($taskToEdit && $taskToEdit['assigned_to_user_id'] == $this->currentUser['user_id']) {
                error_log("Tarea para editar encontrada: " . print_r($taskToEdit, true));
            } else {
                error_log("Tarea no encontrada o sin permisos para editar: $editTaskId");
                $editTaskId = 0;
                $taskToEdit = null;
            }
        }
        
        // Obtener tareas para el tablero Kanban (incluye tareas de otros clanes)
        $kanbanTasks = $this->getKanbanTasksForUser($this->currentUser['user_id'], $this->userClan['clan_id'] ?? null);

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'userTaskStats' => $userTaskStats,
            'ownContribution' => $ownContribution,
            'projects' => $projects,
            'ownContributionDetails' => $ownTasksDetails,
            'kanbanTasks' => $kanbanTasks,
            'editTaskId' => $editTaskId,
            'taskToEdit' => $taskToEdit,
            'allUserTasks' => $allUserTasks // Agregar todas las tareas del usuario con manejo de tareas personales
        ];
        $this->loadView('clan_member/dashboard', $data);
    }

    public function projects() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        // Obtener proyectos donde el usuario tiene tareas asignadas (incluyendo de otros clanes)
        // Pasar null como clan_id para obtener proyectos de todos los clanes donde tenga tareas
        $projects = $this->projectModel->getProjectsForUser($this->currentUser['user_id'], null);
        
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'projects' => $projects
        ];
        $this->loadView('clan_member/projects', $data);
    }

    public function projectTasks() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $projectId = (int)($_GET['project_id'] ?? 0);
        if ($projectId <= 0) { die('Proyecto inválido'); }
        $project = $this->projectModel->findById($projectId);
        if (!$project) {
            die('Proyecto no encontrado');
        }
        
        // Permitir acceso a proyectos especiales (Tareas Recurrentes, Eventuales, Personales)
        $specialProjects = ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales'];
        $isSpecialProject = in_array($project['project_name'], $specialProjects);
        
        // Verificar si el usuario tiene tareas asignadas en este proyecto
        $hasAssignedTasksInProject = $this->userHasAssignedTasksInProject($this->currentUser['user_id'], $projectId);
        
        // Para proyectos normales, verificar que pertenezcan al clan del usuario O que tenga tareas asignadas
        if (!$isSpecialProject && !$hasAssignedTasksInProject && (!$this->userClan || (int)$project['clan_id'] !== (int)$this->userClan['clan_id'])) {
            die('Acceso denegado al proyecto');
        }
        // Obtener solo las tareas del proyecto que están asignadas al usuario
        $tasks = $this->taskModel->getUserTasksByProject($this->currentUser['user_id'], $projectId);
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'project' => $project,
            'tasks' => $tasks
        ];
        $this->loadView('clan_member/project_tasks', $data);
    }

    public function taskDetails() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $taskId = (int)($_GET['task_id'] ?? 0);
        if ($taskId <= 0) { die('Tarea inválida'); }
        $task = $this->taskModel->findById($taskId);
        if (!$task) { die('Tarea no encontrada'); }
        $project = $this->projectModel->findById($task['project_id']);
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        
        // Solo permitir acceso si la tarea está asignada al usuario
        if (!$project || !$isAssigned) { die('Acceso denegado - Tarea no asignada'); }

        // Obtener solo las subtareas asignadas al usuario actual
        $subtasks = $this->taskModel->getSubtasksForUser($taskId, $this->currentUser['user_id']);
        $comments = $this->taskModel->getComments($taskId);
        $history = $this->taskModel->getHistory($taskId);
        $assignedUsers = $this->taskModel->getAssignedUsers($taskId);

        $canEdit = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id'])
            || (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id']
            || (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'task' => $task,
            'project' => $project,
            'subtasks' => $subtasks,
            'comments' => $comments,
            'history' => $history,
            'assignedUsers' => $assignedUsers,
            'canEdit' => $canEdit
            // IMPORTANTE: Los miembros de clan NO deben ver etiquetas
            // 'labels' => [] // Esta línea está comentada intencionalmente
        ];
        $this->loadView('clan_member/task_details', $data);
    }

    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }
        $projectId = (int)($_POST['project_id'] ?? 0);
        $taskName = trim($_POST['task_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? Task::PRIORITY_MEDIUM;
        
        // Mapear 'urgent' a 'critical' para compatibilidad con el enum de la BD
        if ($priority === 'urgent') {
            $priority = 'critical';
            error_log("createTask - Priority mapped from 'urgent' to 'critical' for DB compatibility");
        }
        
        $dueDate = $_POST['due_date'] ?? null;
        if ($projectId <= 0 || $taskName === '') {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        $project = $this->projectModel->findById($projectId);
        if (!$project || (int)$project['clan_id'] !== (int)$this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado al proyecto'], 403);
        }
        $taskId = $this->taskModel->create($projectId, $taskName, $description, $this->currentUser['user_id'], $priority, $dueDate, $this->currentUser['user_id']);
        if ($taskId) {
            Utils::jsonResponse(['success' => true, 'message' => 'Tarea creada', 'task_id' => (int)$taskId]);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'No se pudo crear la tarea'], 500);
        }
    }

    public function tasks() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 8);
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $projectFilter = trim($_GET['project'] ?? '');

        // Tabla: Incluir tanto tareas asignadas como tareas personales creadas por el usuario
        // - Proyectos del clan: solo tareas asignadas al usuario
        // - Proyectos lógicos: solo sus propias tareas  
        // - Tareas personales: tareas creadas por el usuario
        // - Si no tiene clan pero tiene tareas asignadas, mostrar todas las tareas asignadas
        $clanPart = $this->taskModel->getUserTasks($this->currentUser['user_id'], $page, $perPage, $search, $status);
        $ownLogical = $this->taskModel->getUserTasksByProjectNames($this->currentUser['user_id'], ['Tareas Recurrentes','Tareas Eventuales']);
        $personalTasks = $this->taskModel->getUserCreatedTasks($this->currentUser['user_id'], $search, $status);

        
        $merged = [];
        foreach ($clanPart['tasks'] as $t) { $merged[$t['task_id']] = $t; }
        foreach ($ownLogical as $t) { $merged[$t['task_id']] = $t; }
        foreach ($personalTasks as $t) { $merged[$t['task_id']] = $t; }
        
        // Aplicar filtro por proyecto si está especificado
        if (!empty($projectFilter)) {
            $merged = array_filter($merged, function($task) use ($projectFilter) {
                return stripos($task['project_name'] ?? '', $projectFilter) !== false;
            });
        }
        
        $result = $clanPart;
        $result['tasks'] = array_values($merged);

        // Resumen de proyectos: SOLO proyectos donde el usuario esté asignado a tareas
        $projectsSummary = [];
        // Proyectos donde el usuario tenga tareas asignadas (incluyendo de otros clanes)
        $clanProjects = $this->projectModel->getProjectsForUser($this->currentUser['user_id'], $this->userClan['clan_id'] ?? null);
        foreach ($clanProjects as $p) {
            $pid = (int)$p['project_id'];
            $projectName = $p['project_name'];
            
            // Para proyectos especiales, recalcular el conteo real de tareas del usuario
            $specialProjects = ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales'];
            if (in_array($projectName, $specialProjects)) {
                // Obtener tareas del usuario para proyectos especiales
                $userTasksInProject = $this->taskModel->getUserTasksByProject($this->currentUser['user_id'], $pid);
                $total = count($userTasksInProject);
                $completed = 0;
                foreach ($userTasksInProject as $t) {
                    if (($t['status'] ?? '') === 'completed' || ($t['is_completed'] ?? 0) == 1) { $completed++; }
                }
                $progress = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                
                error_log("DEBUG: Proyecto especial '$projectName' - Usuario {$this->currentUser['user_id']} - Total tareas: $total, Completadas: $completed");
            } else {
                // Para proyectos normales, usar el conteo de la consulta
                $userTasksInProject = $this->taskModel->getUserTasksByProject($this->currentUser['user_id'], $pid);
                $total = count($userTasksInProject);
                $completed = 0;
                foreach ($userTasksInProject as $t) {
                    if (($t['status'] ?? '') === 'completed' || ($t['is_completed'] ?? 0) == 1) { $completed++; }
                }
                $progress = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
            }
            
            $projectsSummary[] = [
                'project_id' => $pid,
                'project_name' => $projectName,
                'status' => $p['status'],
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'progress_percentage' => $progress
            ];
        }
        
        // Agregar proyectos lógicos donde el usuario tenga tareas asignadas
        $tasks = $result['tasks'] ?? [];
        $byProject = [];
        foreach ($tasks as $t) {
            $pid = (int)$t['project_id'];
            $projectName = $t['project_name'];
            
            if (!isset($byProject[$pid])) {
                $byProject[$pid] = [
                    'project_id' => $pid,
                    'project_name' => $projectName,
                    'status' => 'open',
                    'total_tasks' => 0,
                    'completed_tasks' => 0
                ];
            }
            $byProject[$pid]['total_tasks']++;
            if (($t['status'] ?? '') === 'completed') { $byProject[$pid]['completed_tasks']++; }
        }
        
        foreach ($byProject as $pid => $info) {
            // Evitar duplicar si ya existe en projectsSummary
            $exists = false;
            foreach ($projectsSummary as $ps) { if ((int)$ps['project_id'] === $pid) { $exists = true; break; } }
            if (!$exists) {
                $progress = $info['total_tasks'] > 0 ? round(($info['completed_tasks'] / $info['total_tasks']) * 100, 1) : 0;
                $projectsSummary[] = [
                    'project_id' => $pid,
                    'project_name' => $info['project_name'],
                    'status' => $info['status'],
                    'total_tasks' => $info['total_tasks'],
                    'completed_tasks' => $info['completed_tasks'],
                    'progress_percentage' => $progress
                ];
            }
        }

        // Obtener tareas recurrentes del usuario
        $recurrentTasks = $this->taskModel->getUserTasksByProjectNames($this->currentUser['user_id'], ['Tareas Recurrentes']);
        
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'tasksData' => $result,
            'search' => $search,
            'status' => $status,
            'projectFilter' => $projectFilter,
            'perPage' => $perPage,
            'projectsSummary' => $projectsSummary,
            'recurrentTasks' => $recurrentTasks
        ];
        $this->loadView('clan_member/tasks', $data);
    }

    /**
     * Obtener todas las tareas del usuario en formato JSON
     * Incluye manejo especial de tareas personales (is_personal = 1 muestra "Personal" como proyecto)
     */
    public function getMyAllTasks() {
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }
        
        try {
            // Obtener todas las tareas del usuario usando la nueva función
            $result = $this->taskModel->getAllUserTasksForDashboard($this->currentUser['user_id']);
            
            if ($result['success']) {
                Utils::jsonResponse([
                    'success' => true,
                    'tasks' => $result['tasks'],
                    'tasks_by_project' => $result['tasks_by_project'],
                    'stats' => $result['stats'],
                    'total' => $result['total'],
                    'user_id' => $this->currentUser['user_id'],
                    'user_name' => $this->currentUser['full_name'] ?? $this->currentUser['username']
                ]);
            } else {
                Utils::jsonResponse([
                    'success' => false,
                    'message' => $result['error'] ?? 'Error al obtener las tareas'
                ], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en getMyAllTasks: " . $e->getMessage());
            Utils::jsonResponse([
                'success' => false,
                'message' => 'Error al obtener las tareas del usuario'
            ], 500);
        }
    }
    
    public function toggleTaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $isCompleted = isset($_POST['is_completed']) && $_POST['is_completed'] === 'true';
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        // Permitir si pertenece al clan o está asignada al usuario
        $project = $this->projectModel->findById($task['project_id']);
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        if (!$project || ($project['clan_id'] != $this->userClan['clan_id'] && !$isAssigned)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        // Restringir cambios de estado a tareas que le correspondan
        if (!$this->isTaskAssignedToUser($taskId, $this->currentUser['user_id'])) {
            Utils::jsonResponse(['success' => false, 'message' => 'No puedes cambiar el estado de tareas que no te corresponden'], 403);
        }

        $result = $this->taskModel->toggleStatus($taskId, $isCompleted, $this->currentUser['user_id']);
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Estado de la tarea actualizado']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar la tarea'], 500);
        }
    }

    public function updateTask() {
        error_log("=== UPDATE TASK DEBUG ===");
        error_log("updateTask called - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("SESSION data: " . print_r($_SESSION ?? [], true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Method not allowed");
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        try {
            $this->requireAuth();
            error_log("Auth check passed");
            
            if (!$this->hasMemberAccess()) {
                error_log("Member access denied");
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            error_log("Member access granted");

            $taskId = (int)($_POST['task_id'] ?? 0);
            error_log("Task ID: $taskId");
            
            if ($taskId <= 0) {
                error_log("Invalid task ID");
                Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
            }

            $task = $this->taskModel->findById($taskId);
            error_log("Task found: " . ($task ? "YES" : "NO"));
            if (!$task) {
                error_log("Task not found");
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            error_log("Task data: " . print_r($task, true));

            // Verificar clan o asignación
            $project = null;
            if (!empty($task['project_id'])) {
                $project = $this->projectModel->findById($task['project_id']);
                error_log("Project found: " . ($project ? "YES" : "NO"));
                if ($project) {
                    error_log("Project data: " . print_r($project, true));
                }
            } else {
                error_log("No project_id - this might be a personal task without project");
            }
            
            $isAssignedToUser = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            
            // Verificar si es tarea personal de varias formas
            $isPersonalTask = false;
            
            // 1. Si el proyecto tiene is_personal = 1
            if ($project) {
                $isPersonalTask = (int)($project['is_personal'] ?? 0) === 1;
            }
            
            // 2. Si la tarea misma tiene el campo is_personal
            if (!$isPersonalTask) {
                $isPersonalTask = (int)($task['is_personal'] ?? 0) === 1;
            }
            
            // 3. Si no hay proyecto pero el usuario es creador y asignado (típico de tareas personales)
            if (!$isPersonalTask && empty($task['project_id'])) {
                $isCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                $isAssigned = (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                if ($isCreator && $isAssigned) {
                    $isPersonalTask = true;
                    error_log("Detected personal task by creator+assigned pattern");
                }
            }
            
            error_log("Is assigned to user: " . ($isAssignedToUser ? "YES" : "NO"));
            error_log("Is personal task: " . ($isPersonalTask ? "YES" : "NO"));
            error_log("Project is_personal: " . (isset($project['is_personal']) ? $project['is_personal'] : 'NULL'));
            error_log("Task is_personal: " . (isset($task['is_personal']) ? $task['is_personal'] : 'NULL'));
            
            // Para tareas personales, solo verificar que el usuario sea el propietario o creador
            if ($isPersonalTask) {
                $isOwner = (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                $isCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                
                error_log("Personal task permission check - isOwner: " . ($isOwner ? "YES" : "NO") . 
                         ", isCreator: " . ($isCreator ? "YES" : "NO") . 
                         ", currentUser: " . $this->currentUser['user_id'] . 
                         ", assignedTo: " . ($task['assigned_to_user_id'] ?? 'NULL') . 
                         ", createdBy: " . ($task['created_by_user_id'] ?? 'NULL'));
                
                if (!($isOwner || $isCreator)) {
                    error_log("Access denied - user is not owner or creator of personal task");
                    Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                error_log("Personal task access granted");
            } else {
                // Para tareas normales, verificar clan o asignación
                if (!$project || ($project['clan_id'] != $this->userClan['clan_id'] && !$isAssignedToUser)) {
                    error_log("Access denied - project or clan mismatch");
                    Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                error_log("Project/clan access granted");
            }

            // Para tareas personales, ya verificamos permisos arriba
            // Para tareas normales, verificar permisos adicionales
            if (!$isPersonalTask) {
                $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
                $isOwner = (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                $isCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
                
                error_log("Permission check for normal task - isAssigned: " . ($isAssigned ? "YES" : "NO") . 
                         ", isOwner: " . ($isOwner ? "YES" : "NO") . 
                         ", isCreator: " . ($isCreator ? "YES" : "NO"));
                
                if (!($isAssigned || $isOwner || $isCreator)) {
                    error_log("Permission denied - user cannot update this task");
                    Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes actualizar tareas que te corresponden'], 403);
                }
                error_log("Normal task permission granted");
            } else {
                error_log("Personal task - permissions already verified above");
            }

            // Solo procesar campos que realmente se enviaron en el POST
            // Si no se envía un campo, mantenerlo como null para no actualizarlo
            $taskName = isset($_POST['task_name']) ? Utils::sanitizeInput($_POST['task_name']) : null;
            $description = isset($_POST['description']) ? Utils::sanitizeInput($_POST['description']) : null;
            $priority = isset($_POST['priority']) ? $_POST['priority'] : null;
            $dueDate = isset($_POST['due_date']) ? $_POST['due_date'] : null;
            $status = isset($_POST['status']) ? $_POST['status'] : null;

            // Mapear 'urgent' a 'critical' para compatibilidad con el enum de la BD
            if ($priority === 'urgent') {
                $priority = 'critical';
                error_log("Priority mapped from 'urgent' to 'critical' for DB compatibility");
            }
            
            // Validar que el priority sea válido
            $validPriorities = ['low', 'medium', 'high', 'critical'];
            if ($priority !== null && !in_array($priority, $validPriorities)) {
                error_log("Invalid priority value: '$priority'. Valid values are: " . implode(', ', $validPriorities));
                Utils::jsonResponse(['success' => false, 'message' => 'Prioridad inválida'], 400);
            }

            error_log("Update data - taskName: '$taskName', description: '$description', priority: '$priority', dueDate: '$dueDate', status: '$status'");

            // No permitir cambios de asignación ni porcentajes desde el rol miembro
            $assignedUserId = null;
            $assignedPercentage = null;

            error_log("Calling taskModel->update with taskId: $taskId");
            error_log("Parameters being passed to update method:");
            error_log("- taskId: $taskId");
            error_log("- taskName: '$taskName'");
            error_log("- description: '$description'");
            error_log("- assignedUserId: " . ($assignedUserId ?? 'NULL'));
            error_log("- priority: '$priority'");
            error_log("- dueDate: '$dueDate'");
            error_log("- assignedPercentage: " . ($assignedPercentage ?? 'NULL'));
            error_log("- status: '$status'");
            
            $ok = $this->taskModel->update($taskId, $taskName, $description, $assignedUserId, $priority, $dueDate, $assignedPercentage, $status);
            error_log("Update result: " . ($ok ? "SUCCESS" : "FAILED"));
            
            if ($ok) {
                error_log("Task updated successfully");
                Utils::jsonResponse(['success' => true, 'message' => 'Tarea actualizada']);
            } else {
                error_log("Failed to update task");
                Utils::jsonResponse(['success' => false, 'message' => 'No se pudo actualizar la tarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("EXCEPTION in updateTask: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function addTaskComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $commentText = trim($_POST['comment_text'] ?? '');
        if ($taskId <= 0 || $commentText === '') {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        // Verificar que la tarea sea del clan del usuario o esté asignada al usuario
        $project = $this->projectModel->findById($task['project_id']);
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        if (!$project || ((int)$project['clan_id'] !== (int)$this->userClan['clan_id'] && !$isAssigned)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        // Agregar comentario y recibir comment_id (o true)
        $commentId = $this->taskModel->addComment($taskId, $this->currentUser['user_id'], $commentText, 'comment');
        if (!$commentId) {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar comentario'], 500);
        }

        // Manejar adjuntos: soportar uno (attachment) o múltiples (attachments[])
        $files = [];
        $received = [];
        $saved = [];
        if (!empty($_FILES['attachments']) && is_array($_FILES['attachments']['name'] ?? null)) {
            $count = count($_FILES['attachments']['name']);
            for ($i=0; $i<$count; $i++) {
                if (($_FILES['attachments']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $files[] = [
                        'name' => $_FILES['attachments']['name'][$i] ?? null,
                        'type' => $_FILES['attachments']['type'][$i] ?? null,
                        'tmp_name' => $_FILES['attachments']['tmp_name'][$i] ?? null,
                    ];
                    if (!empty($_FILES['attachments']['name'][$i])) { $received[] = $_FILES['attachments']['name'][$i]; }
                }
            }
        } elseif (!empty($_FILES['attachment']) && ($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $files[] = [
                'name' => $_FILES['attachment']['name'] ?? null,
                'type' => $_FILES['attachment']['type'] ?? null,
                'tmp_name' => $_FILES['attachment']['tmp_name'] ?? null,
            ];
            if (!empty($_FILES['attachment']['name'])) { $received[] = $_FILES['attachment']['name']; }
        }

        if (!empty($files)) {
            $publicRoot = dirname(__DIR__, 2) . '/public';
            $baseUploads = $publicRoot . '/uploads';
            $uploadDir = $baseUploads . '/task_attachments';
            if (!is_dir($baseUploads)) { @mkdir($baseUploads, 0775, true); }
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

            foreach ($files as $file) {
                if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) { continue; }
                $originalName = basename($file['name'] ?? 'archivo');
                $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                $safeName = uniqid('att_') . ($ext ? ('.' . $ext) : '');
                $destPath = $uploadDir . '/' . $safeName;
                if (@move_uploaded_file($file['tmp_name'], $destPath)) {
                    $publicPath = 'uploads/task_attachments/' . $safeName;
                    // Guardar con vínculo a comment_id si es numérico
                    $this->taskModel->saveAttachmentRecord(
                        $taskId,
                        is_numeric($commentId) ? (int)$commentId : null,
                        $this->currentUser['user_id'],
                        $originalName,
                        $publicPath,
                        $file['type'] ?? null
                    );
                    $saved[] = $originalName;
                }
            }
        }

        Utils::jsonResponse([
            'success' => true,
            'message' => 'Comentario agregado',
            'attachments_received' => $received,
            'attachments_saved' => $saved
        ]);
    }

    public function taskComments() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $taskId = (int)($_GET['task_id'] ?? 0);
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }
        // Validar que pertenece al clan del usuario o que está asignada al usuario
        $project = $this->projectModel->findById($task['project_id']);
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        if (!$project || ((int)$project['clan_id'] !== (int)$this->userClan['clan_id'] && !$isAssigned)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $comments = $this->taskModel->getComments($taskId);
        // Normalizar URLs de adjuntos
        foreach ($comments as &$c) {
            $c['attachments'] = $c['attachments'] ?? [];
            foreach ($c['attachments'] as &$a) {
                $path = $a['file_path'] ?? '';
                $a['url'] = $path ? Utils::asset($path) : null;
                if (empty($a['file_type']) && $path) {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $a['file_type'] = $ext;
                }
            }
        }
        Utils::jsonResponse(['success' => true, 'comments' => $comments]);
    }

    public function availability() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $tasks = $this->taskModel->getActiveTasksByUser($this->currentUser['user_id']);
        
        // Log para debugging de tareas recurrentes
        error_log("=== AVAILABILITY CALENDAR ===");
        error_log("Usuario ID: " . $this->currentUser['user_id']);
        error_log("Total tareas cargadas: " . count($tasks));
        
        // Log de tareas recurrentes
        $recurrentTasks = array_filter($tasks, function($task) {
            return ($task['is_recurrent'] ?? 0) == 1;
        });
        error_log("Tareas recurrentes encontradas: " . count($recurrentTasks));
        
        foreach ($recurrentTasks as $task) {
            error_log("Tarea recurrente: ID={$task['task_id']}, Nombre={$task['task_name']}, Fecha={$task['due_date']}, Proyecto={$task['project_name']}");
        }
        
        $summary = [
            'task_count' => count($tasks)
        ];
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'tasks' => $tasks,
            'summary' => $summary
        ];
        $this->loadView('clan_member/availability', $data);
    }

    public function kpiDashboard() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }

        $currentKPI = $this->kpiModel->getCurrentQuarter();
        $projects = [];
        $clanTotalPoints = 0;
        $clanCompletedPoints = 0;
        $userKPI = ['target_points' => 1000, 'earned_points' => 0, 'progress_percentage' => 0, 'completed_tasks' => 0, 'total_tasks' => 0];
        if ($currentKPI && $this->userClan && isset($this->userClan['clan_id'])) {
            $all = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id']);
            foreach ($all as $p) {
                if ((int)$p['clan_id'] === (int)$this->userClan['clan_id']) {
                    $projects[] = $p;
                    $clanTotalPoints += (int)$p['kpi_points'];
                    $progress = $this->projectModel->calculateKPIProgress($p['project_id']);
                    $clanCompletedPoints += (float)$progress['earned_points'];
                }
            }

            // KPI personal del usuario (puntos ganados en el trimestre actual del clan)
            try {
                $db = Database::getConnection();
                $sql = "
                    SELECT 
                        SUM(
                            CASE WHEN (t.status='completed' OR t.is_completed=1) THEN
                                CASE 
                                    WHEN p.task_distribution_mode = 'automatic' THEN 
                                        CASE 
                                            WHEN ta.user_id IS NOT NULL THEN t.automatic_points * (ta.assigned_percentage/100.0)
                                            WHEN t.assigned_to_user_id = ? THEN t.automatic_points
                                            ELSE 0
                                        END
                                    ELSE 
                                        (COALESCE(t.assigned_percentage,0) * p.kpi_points / 100.0) *
                                        (CASE 
                                            WHEN ta.user_id IS NOT NULL THEN (ta.assigned_percentage/100.0)
                                            WHEN t.assigned_to_user_id = ? THEN 1
                                            ELSE 0
                                        END)
                                END
                            ELSE 0 END
                        ) AS earned_points,
                        SUM(CASE WHEN (t.status='completed' OR t.is_completed=1) AND (ta.user_id IS NOT NULL OR t.assigned_to_user_id = ?) THEN 1 ELSE 0 END) AS completed_tasks,
                        COUNT(DISTINCT CASE WHEN (ta.user_id IS NOT NULL OR t.assigned_to_user_id = ?) THEN t.task_id END) AS total_tasks
                    FROM Tasks t
                    JOIN Projects p ON p.project_id = t.project_id
                    LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id AND ta.user_id = ?
                    WHERE p.clan_id = ?
                      AND p.kpi_quarter_id = ?
                      AND t.is_subtask = 0
                ";
                $params = [
                    $this->currentUser['user_id'],
                    $this->currentUser['user_id'],
                    $this->currentUser['user_id'],
                    $this->currentUser['user_id'],
                    $this->currentUser['user_id'],
                    $this->userClan['clan_id'],
                    $currentKPI['kpi_quarter_id']
                ];
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['earned_points'=>0,'completed_tasks'=>0,'total_tasks'=>0];
                $earned = (float)($row['earned_points'] ?? 0);
                $target = 1000.0;
                $userKPI['earned_points'] = round($earned, 2);
                $userKPI['progress_percentage'] = $target > 0 ? round(min(($earned / $target) * 100, 100), 1) : 0;
                $userKPI['completed_tasks'] = (int)($row['completed_tasks'] ?? 0);
                $userKPI['total_tasks'] = (int)($row['total_tasks'] ?? 0);
            } catch (Exception $e) {
                error_log('ClanMember userKPI calc error: ' . $e->getMessage());
            }
        }
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'currentKPI' => $currentKPI,
            'projects' => $projects,
            'clanTotalPoints' => $clanTotalPoints,
            'clanCompletedPoints' => $clanCompletedPoints,
            'userKPI' => $userKPI
        ];
        $this->loadView('clan_member/kpi_dashboard', $data);
    }

    // Vista de perfil del miembro
    public function profile() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) { Utils::redirect('dashboard'); return; }
        $user = $this->currentUser;
        $data = [
            'currentPage' => 'clan_member',
            'user' => $user,
            'clan' => $this->userClan,
        ];
        $this->loadView('clan_member/profile', $data);
    }

    // Actualiza datos básicos (nombre, email, username)
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); 
        }
        
        $this->requireAuth();
        if (!$this->hasMemberAccess()) { 
            Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); 
        }
        
        // Debug logging
        error_log("updateProfile called - POST data: " . print_r($_POST, true));
        
        // El username no se puede cambiar desde el perfil, usar el username actual
        $username = $this->currentUser['username'];
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        
        error_log("Profile update data - username: '$username', email: '$email', fullName: '$fullName'");
        
        // Validar campos requeridos
        if ($email === '' || $fullName === '') { 
            error_log("Validation failed - empty fields: email='$email', fullName='$fullName'");
            Utils::jsonResponse(['success'=>false,'message'=>'Email y nombre completo son requeridos'],400); 
        }
        
        // Validar email
        if (!Utils::isValidEmail($email)) {
            error_log("Validation failed - invalid email: '$email'");
            Utils::jsonResponse(['success'=>false,'message'=>'El email no es válido'],400);
        }
        
        try {
            error_log("Attempting to update user profile for user ID: " . $this->currentUser['user_id']);
            $ok = $this->userModel->update($this->currentUser['user_id'], $username, $email, $fullName, 1);
            error_log("Update result: " . ($ok ? "SUCCESS" : "FAILED"));
            
            if ($ok) {
                // refrescar sesión en memoria
                $_SESSION['username'] = $username; 
                $_SESSION['email'] = $email; 
                $_SESSION['full_name'] = $fullName;
                error_log("Profile updated successfully for user: " . $this->currentUser['user_id']);
                Utils::jsonResponse(['success'=>true,'message'=>'Perfil actualizado']);
            } else {
                error_log("Failed to update profile for user: " . $this->currentUser['user_id']);
                Utils::jsonResponse(['success'=>false,'message'=>'No se pudo actualizar el perfil']);
            }
        } catch (Exception $e) {
            error_log("Exception in updateProfile: " . $e->getMessage());
            Utils::jsonResponse(['success'=>false,'message'=>'Error interno: ' . $e->getMessage()], 500);
        }
    }

    // Actualiza contraseña en texto plano (según requerimiento)
    public function updatePasswordPlain() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) { Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); }
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        if ($new === '' || $new !== $confirm) { Utils::jsonResponse(['success'=>false,'message'=>'La confirmación no coincide'],400); }
        $ok = $this->userModel->updatePasswordPlain($this->currentUser['user_id'], $new);
        Utils::jsonResponse(['success'=>$ok,'message'=>$ok?'Contraseña actualizada':'No se pudo actualizar la contraseña']);
    }

    // Subida de avatar al directorio public/uploads y guarda en Users.avatar_path
    public function uploadAvatar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) { Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); }
        if (empty($_FILES['avatar']) || ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Utils::jsonResponse(['success'=>false,'message'=>'Archivo no recibido'],400);
        }
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) { Utils::jsonResponse(['success'=>false,'message'=>'Formato no permitido'],400); }
        $uploads = dirname(__DIR__,2) . '/public/uploads';
        if (!is_dir($uploads)) { @mkdir($uploads, 0775, true); }
        $safe = 'avatar_' . $this->currentUser['user_id'] . '_' . time() . '.' . $ext;
        $dest = $uploads . '/' . $safe;
        if (!move_uploaded_file($file['tmp_name'], $dest)) { Utils::jsonResponse(['success'=>false,'message'=>'Error al guardar archivo'],500); }
        $publicPath = 'uploads/' . $safe;
        $ok = $this->userModel->updateAvatarPath($this->currentUser['user_id'], $publicPath);
        if ($ok) {
            Utils::jsonResponse(['success'=>true,'message'=>'Avatar actualizado','avatar_url'=>Utils::asset($publicPath)]);
        }
        Utils::jsonResponse(['success'=>false,'message'=>'No se pudo actualizar avatar']);
    }

    private function isTaskAssignedToUser($taskId, $userId) {
        try {
            // Verificar asignación principal o en Task_Assignments
            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM Tasks WHERE task_id = ? AND assigned_to_user_id = ?");
            $stmt->execute([$taskId, $userId]);
            $row = $stmt->fetch();
            if ((int)($row['c'] ?? 0) > 0) { return true; }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM Task_Assignments WHERE task_id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log('Error isTaskAssignedToUser: ' . $e->getMessage());
            return false;
        }
    }

    private function getTeamProgress($clanId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total_tasks, SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) AS completed_tasks FROM Tasks t INNER JOIN Projects p ON t.project_id = p.project_id WHERE p.clan_id = ? AND t.is_subtask = 0");
            $stmt->execute([$clanId]);
            $row = $stmt->fetch();
            $total = (int)($row['total_tasks'] ?? 0);
            $completed = (int)($row['completed_tasks'] ?? 0);
            $pct = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            return [
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'completion_percentage' => $pct
            ];
        } catch (Exception $e) {
            return ['total_tasks' => 0, 'completed_tasks' => 0, 'completion_percentage' => 0];
        }
    }

    private function getMemberContributions($clanId) {
        try {
            // Contribuciones por usuarios a partir de tareas completadas (principal o en assignments)
            $sql = "SELECT u.user_id, u.full_name, 
                           SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) AS completed_tasks,
                           COUNT(DISTINCT t.task_id) AS total_tasks
                    FROM Users u
                    JOIN Clan_Members cm ON cm.user_id = u.user_id AND cm.clan_id = ?
                    LEFT JOIN Tasks t ON (t.assigned_to_user_id = u.user_id)
                    LEFT JOIN Projects p ON p.project_id = t.project_id AND p.clan_id = ?
                    GROUP BY u.user_id, u.full_name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$clanId, $clanId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ajustar porcentaje de contribución relativo a completadas del clan
            $totalCompleted = array_sum(array_map(function($r){ return (int)($r['completed_tasks'] ?? 0); }, $rows));
            foreach ($rows as &$r) {
                $r['contribution_percentage'] = $totalCompleted > 0 ? round(((int)$r['completed_tasks'] / $totalCompleted) * 100, 1) : 0;
                $r['initial'] = strtoupper(substr($r['full_name'] ?? $r['user_id'], 0, 1));
            }
            return $rows;
        } catch (Exception $e) {
            error_log('Error getMemberContributions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de tareas del usuario incluyendo de todos los clanes
     */
    private function getUserTaskStatsForAllClans($userId, $primaryClanId = null) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    COUNT(DISTINCT t.task_id) AS total_tasks,
                    SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) AS completed_tasks
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                 WHERE t.is_subtask = 0
                   AND (t.assigned_to_user_id = ? OR ta.user_id = ?)"
            );
            $stmt->execute([$userId, $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_tasks' => 0, 'completed_tasks' => 0];
            $total = (int)($row['total_tasks'] ?? 0);
            $completed = (int)($row['completed_tasks'] ?? 0);
            $pct = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            return [
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'completion_percentage' => $pct
            ];
        } catch (Exception $e) {
            error_log('Error getUserTaskStatsForAllClans: ' . $e->getMessage());
            return ['total_tasks' => 0, 'completed_tasks' => 0, 'completion_percentage' => 0];
        }
    }

    private function getUserTaskStats($userId, $clanId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    COUNT(DISTINCT t.task_id) AS total_tasks,
                    SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) AS completed_tasks
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                 WHERE p.clan_id = ?
                   AND t.is_subtask = 0
                   AND (t.assigned_to_user_id = ? OR ta.user_id = ?)"
            );
            $stmt->execute([$clanId, $userId, $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_tasks' => 0, 'completed_tasks' => 0];
            $total = (int)($row['total_tasks'] ?? 0);
            $completed = (int)($row['completed_tasks'] ?? 0);
            $pct = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            return [
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'completion_percentage' => $pct
            ];
        } catch (Exception $e) {
            error_log('Error getUserTaskStats: ' . $e->getMessage());
            return ['total_tasks' => 0, 'completed_tasks' => 0, 'completion_percentage' => 0];
        }
    }

    private function getOwnContribution($currentUser, $userTaskStats) {
        $fullName = $currentUser['full_name'] ?? ($currentUser['username'] ?? 'Usuario');
        $initial = strtoupper(substr($fullName, 0, 1));
        return [
            'user_id' => $currentUser['user_id'] ?? 0,
            'full_name' => $fullName,
            'completed_tasks' => $userTaskStats['completed_tasks'] ?? 0,
            'total_tasks' => $userTaskStats['total_tasks'] ?? 0,
            'contribution_percentage' => ($userTaskStats['total_tasks'] ?? 0) > 0 ? 100 : 0,
            'initial' => $initial
        ];
    }

    /**
     * Obtener tareas del usuario para modal incluyendo de todos los clanes
     */
    private function getUserTasksForModalAllClans($userId, $primaryClanId = null) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    t.task_id,
                    t.task_name,
                    t.status,
                    t.due_date,
                    t.created_at,
                    p.project_name,
                    p.clan_id,
                    CASE 
                        WHEN c.clan_name LIKE '%Dirección%' THEN 'Clan'
                        ELSE c.clan_name
                    END as clan_name,
                    CASE 
                        WHEN p.clan_id = ? THEN 1
                        ELSE 0
                    END as is_primary_clan
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 LEFT JOIN Clans c ON p.clan_id = c.clan_id
                 LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                 WHERE t.is_subtask = 0
                   AND (t.assigned_to_user_id = ? OR ta.user_id = ?)
                 GROUP BY t.task_id
                 ORDER BY is_primary_clan DESC, t.created_at DESC
                 LIMIT 20"
            );
            $stmt->execute([$primaryClanId, $userId, $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Error getUserTasksForModalAllClans: ' . $e->getMessage());
            return [];
        }
    }

    private function getUserTasksForModal($userId, $clanId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    t.task_id,
                    t.task_name,
                    t.status,
                    t.due_date,
                    t.created_at,
                    p.project_name
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                 WHERE p.clan_id = ?
                   AND t.is_subtask = 0
                   AND (t.assigned_to_user_id = ? OR ta.user_id = ?)
                 GROUP BY t.task_id
                 ORDER BY t.created_at DESC
                 LIMIT 50"
            );
            $stmt->execute([$clanId, $userId, $userId]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stats = [
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'pending' => 0
            ];
            foreach ($tasks as $t) {
                $stats['total']++;
                if ($t['status'] === 'completed') $stats['completed']++;
                elseif ($t['status'] === 'in_progress') $stats['in_progress']++;
                else $stats['pending']++;
            }
            return [
                'stats' => $stats,
                'tasks' => $tasks
            ];
        } catch (Exception $e) {
            error_log('Error getUserTasksForModal: ' . $e->getMessage());
            return ['stats' => ['total' => 0,'completed' => 0,'in_progress' => 0,'pending' => 0], 'tasks' => []];
        }
    }

    private function hasMemberAccess() {
        if (!$this->currentUser) { return false; }
        
        // Verificar rol mínimo
        if (!$this->roleModel->userHasMinimumRole($this->currentUser['user_id'], Role::USUARIO_NORMAL)) {
            return false;
        }
        
        // Permitir acceso si el usuario tiene tareas asignadas (de cualquier clan)
        if ($this->userHasAssignedTasks($this->currentUser['user_id'])) {
            return true;
        }
        
        // Permitir acceso si es miembro de algún clan
        if ($this->userClan && isset($this->userClan['clan_id'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verificar si el usuario tiene tareas asignadas (de cualquier clan)
     */
    private function userHasAssignedTasks($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as task_count
                FROM Tasks t
                LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                WHERE t.assigned_to_user_id = ? OR ta.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['task_count'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log("Error checking user assigned tasks: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si el usuario tiene tareas asignadas en un proyecto específico
     */
    private function userHasAssignedTasksInProject($userId, $projectId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as task_count
                FROM Tasks t
                LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                WHERE (t.assigned_to_user_id = ? OR ta.user_id = ?) AND t.project_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId, $userId, $projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['task_count'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log("Error checking user assigned tasks in project: " . $e->getMessage());
            return false;
        }
    }

    private function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
    }

    /**
     * Obtener datos de una tarea para clonación
     */
    public function getTaskData() {
        $this->requireAuth();
        
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        $taskId = (int)($_GET['task_id'] ?? 0);
        
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
            return;
        }

        try {
            // Obtener datos de la tarea
            $task = $this->taskModel->findById($taskId);
            
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
                return;
            }

            // Verificar que el usuario es dueño de la tarea
            if ((int)($task['created_by_user_id'] ?? 0) !== (int)$this->currentUser['user_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes clonar tus propias tareas'], 403);
                return;
            }

            // Obtener proyectos disponibles para el usuario
            $projects = $this->projectModel->getProjectsForUser($this->currentUser['user_id'], null);

            Utils::jsonResponse([
                'success' => true,
                'task' => $task,
                'projects' => $projects
            ]);

        } catch (Exception $e) {
            error_log("Error en getTaskData: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Clonar una tarea
     */
    public function cloneTask() {
        $this->requireAuth();
        
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        try {
            $originalTaskId = (int)($_POST['originalTaskId'] ?? 0);
            $taskName = trim($_POST['task_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $projectId = (int)($_POST['project_id'] ?? 0);
            $priority = $_POST['priority'] ?? 'medium';
            $dueDate = $_POST['due_date'] ?? null;
            $cloneSubtasks = ($_POST['clone_subtasks'] ?? '0') === '1';

            // Validaciones
            if ($originalTaskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea original inválido'], 400);
                return;
            }

            if (empty($taskName)) {
                Utils::jsonResponse(['success' => false, 'message' => 'El nombre de la tarea es requerido'], 400);
                return;
            }

            if ($projectId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'Debe seleccionar un proyecto'], 400);
                return;
            }

            // Verificar que la tarea original existe y pertenece al usuario
            $originalTask = $this->taskModel->findById($originalTaskId);
            
            if (!$originalTask) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea original no encontrada'], 404);
                return;
            }

            if ((int)($originalTask['created_by_user_id'] ?? 0) !== (int)$this->currentUser['user_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes clonar tus propias tareas'], 403);
                return;
            }

            // Verificar que el proyecto destino existe y el usuario tiene acceso
            $targetProject = $this->projectModel->findById($projectId);
            
            if (!$targetProject) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto destino no encontrado'], 404);
                return;
            }

            // Validar fecha límite si se proporciona
            if (!empty($dueDate) && !strtotime($dueDate)) {
                Utils::jsonResponse(['success' => false, 'message' => 'Fecha límite inválida'], 400);
                return;
            }

            // Crear la nueva tarea
            $newTaskId = $this->taskModel->create(
                $projectId,
                $taskName,
                $description,
                $this->currentUser['user_id'], // creador
                $priority,
                $dueDate,
                $this->currentUser['user_id'] // asignado inicialmente al creador
            );

            if (!$newTaskId) {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear la tarea clonada'], 500);
                return;
            }

            // Clonar subtareas si se solicitó
            if ($cloneSubtasks) {
                $subtasks = $this->taskModel->getSubtasks($originalTaskId);
                
                foreach ($subtasks as $subtask) {
                    $this->taskModel->createSubtaskAdvanced(
                        $newTaskId,
                        $subtask['title'],
                        $this->currentUser['user_id'], // creador
                        $subtask['description'] ?? '',
                        0, // percentage inicial
                        $subtask['due_date'] ?? null,
                        $subtask['priority'] ?? 'medium',
                        $this->currentUser['user_id'] // asignado inicialmente al creador
                    );
                }
            }

            Utils::jsonResponse([
                'success' => true,
                'message' => 'Tarea clonada exitosamente',
                'new_task_id' => $newTaskId
            ]);

        } catch (Exception $e) {
            error_log("Error en cloneTask: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * SOLUCIÓN DEFINITIVA: Obtener tareas Kanban para el usuario
     * Versión simplificada que SÍ funciona
     */
    private function getKanbanTasksForUser($userId, $primaryClanId = null) {
        try {
            error_log("=== getKanbanTasksForUser INICIO ===");
            error_log("Usuario ID: $userId, Clan primario: $primaryClanId");
            
            // Usar la nueva función optimizada para obtener TODAS las tareas
            $result = $this->taskModel->getAllUserTasksForDashboard($userId);
            
            if (!$result['success']) {
                error_log('Error obteniendo tareas para Kanban: ' . ($result['error'] ?? 'Error desconocido'));
                return [
                    'vencidas' => [],
                    'hoy' => [],
                    '1_semana' => [],
                    '2_semanas' => []
                ];
            }
            
            error_log("Tareas obtenidas para Kanban: " . count($result['tasks']));
            
            // NO FILTRAR NADA - MOSTRAR TODAS LAS TAREAS
            $allTasks = $result['tasks'];
            
            // Log para ver cuántas tareas tenemos
            error_log("TODAS las tareas sin filtrar: " . count($allTasks));
            
            error_log("Tareas no completadas para Kanban: " . count($allTasks));
            
            // Agregar información adicional necesaria para el Kanban
            $allTasks = array_map(function($task) use ($primaryClanId) {
                $task['is_primary_clan'] = ($task['clan_id'] == $primaryClanId) ? 1 : 0;
                $task['item_type'] = 'task';
                // La función ya maneja el project_name correctamente (muestra "Personal" cuando is_personal = 1)
                return $task;
            }, $allTasks);
            
            // Obtener también las subtareas asignadas al usuario (simplificado)
            $subtasks = [];
            try {
                $subtaskStmt = $this->db->prepare(
                    "SELECT 
                        s.subtask_id as task_id,
                        s.task_id as parent_task_id,
                        s.title as task_name,
                        t.task_name as parent_task_name,
                        s.description,
                        s.due_date,
                        'medium' as priority,
                        s.status,
                        s.completion_percentage,
                        0 as automatic_points,
                        CONCAT('Subtarea de: ', t.task_name) as project_name,
                        t.project_id,
                        p.clan_id,
                        CASE 
                            WHEN c.clan_name LIKE '%Dirección%' THEN 'Clan'
                            ELSE c.clan_name
                        END as clan_name,
                        CASE 
                            WHEN s.due_date IS NULL THEN 999
                            ELSE DATEDIFF(s.due_date, CURDATE())
                        END as days_until_due,
                        CASE 
                            WHEN p.clan_id = ? THEN 1
                            ELSE 0
                        END as is_primary_clan,
                        'subtask' as item_type
                     FROM Subtasks s
                     INNER JOIN Tasks t ON s.task_id = t.task_id
                     INNER JOIN Projects p ON t.project_id = p.project_id
                     LEFT JOIN Clans c ON p.clan_id = c.clan_id
                     WHERE s.assigned_to_user_id = ?
                       AND s.status != 'completed'
                     ORDER BY s.due_date ASC"
                );
                $subtaskStmt->execute([$primaryClanId, $userId]);
                $subtasks = $subtaskStmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Subtareas encontradas: " . count($subtasks));
            } catch (Exception $e) {
                error_log("Error obteniendo subtareas: " . $e->getMessage());
                $subtasks = [];
            }
            
            // Combinar tareas principales y subtareas
            $allCombinedTasks = array_merge($allTasks, $subtasks);
            error_log("Total tareas combinadas: " . count($allCombinedTasks));
            
            // Clasificar TODAS las tareas - SIN LÍMITES DE TIEMPO
            $kanbanColumns = [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];
            
            foreach ($allCombinedTasks as $task) {
                $daysUntilDue = (int)$task['days_until_due'];
                
                // Log de cada tarea para debugging
                error_log("Clasificando tarea ID={$task['task_id']}, días={$daysUntilDue}, proyecto={$task['project_name']}, estado={$task['status']}");
                
                if ($daysUntilDue < 0) {
                    $kanbanColumns['vencidas'][] = $task;
                } elseif ($daysUntilDue <= 0) {
                    $kanbanColumns['hoy'][] = $task;
                } elseif ($daysUntilDue <= 7) {
                    $kanbanColumns['1_semana'][] = $task;
                } else {
                    // TODAS las demás tareas van a 2+ semanas - SIN LÍMITE
                    $kanbanColumns['2_semanas'][] = $task;
                }
            }
            
            // Log del resultado final
            foreach ($kanbanColumns as $column => $tasks) {
                error_log("Columna '{$column}': " . count($tasks) . " tareas");
            }
            
            // Ordenar cada columna por prioridad y fecha
            $priorityOrder = ['critical' => 1, 'high' => 2, 'medium' => 3, 'low' => 4];
            
            foreach ($kanbanColumns as $column => &$tasks) {
                usort($tasks, function($a, $b) use ($priorityOrder) {
                    $priorityA = $priorityOrder[$a['priority']] ?? 5;
                    $priorityB = $priorityOrder[$b['priority']] ?? 5;
                    
                    if ($priorityA === $priorityB) {
                        return strtotime($a['due_date'] ?? '9999-12-31') - strtotime($b['due_date'] ?? '9999-12-31');
                    }
                    
                    return $priorityA - $priorityB;
                });
            }
            
            error_log("=== getKanbanTasksForUser FIN ===");
            
            return $kanbanColumns;
        } catch (Exception $e) {
            error_log('Error getKanbanTasksForUser: ' . $e->getMessage());
            return [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];
        }
    }

    private function getKanbanTasks($userId, $clanId) {
        try {
            error_log("=== INICIO getKanbanTasks ===");
            error_log("Usuario ID: $userId, Clan ID: $clanId");
            
            // Obtener tareas del clan (excluyendo las especiales y personales para evitar duplicados)
            $clanTasks = [];
            if ($clanId) {
                $stmt = $this->db->prepare(
                    "SELECT 
                        t.task_id,
                        t.task_name,
                        t.description,
                        t.due_date,
                        t.priority,
                        t.status,
                        t.completion_percentage,
                        t.automatic_points,
                        p.project_name,
                        p.project_id,
                        DATEDIFF(t.due_date, CURDATE()) as days_until_due
                     FROM Tasks t
                     INNER JOIN Projects p ON p.project_id = t.project_id
                     LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                     WHERE p.clan_id = ?
                       AND p.project_name NOT IN ('Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales')
                       AND t.is_subtask = 0
                       AND t.is_personal = 0
                       AND t.status != 'completed'
                       AND (t.assigned_to_user_id = ? OR ta.user_id = ?)
                     GROUP BY t.task_id
                     ORDER BY t.due_date ASC"
                );
                $stmt->execute([$clanId, $userId, $userId]);
                $clanTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Tareas del clan encontradas: " . count($clanTasks));
            }

            // Obtener tareas personales usando el campo is_personal
            $personalTasks = [];
            $stmt = $this->db->prepare(
                "SELECT 
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.due_date,
                    t.priority,
                    t.status,
                    t.completion_percentage,
                    t.automatic_points,
                    p.project_name,
                    p.project_id,
                    CASE 
                        WHEN t.due_date IS NULL THEN 999
                        ELSE DATEDIFF(t.due_date, CURDATE())
                    END as days_until_due
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 WHERE t.is_personal = 1
                   AND t.assigned_to_user_id = ?
                   AND t.status != 'completed'
                   AND t.is_subtask = 0
                 ORDER BY t.due_date ASC"
            );
            $stmt->execute([$userId]);
            $personalTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Tareas personales encontradas: " . count($personalTasks));
            error_log("Detalle de tareas personales: " . print_r($personalTasks, true));
            
            // Log detallado de cada tarea personal
            foreach ($personalTasks as $task) {
                error_log("TAREA PERSONAL - ID: {$task['task_id']}, Nombre: {$task['task_name']}, Proyecto: {$task['project_name']}, Project ID: {$task['project_id']}");
            }

            // Obtener tareas de proyectos especiales (Tareas Recurrentes y Eventuales)
            $specialTasks = [];
            $stmt = $this->db->prepare(
                "SELECT 
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.due_date,
                    t.priority,
                    t.status,
                    t.completion_percentage,
                    t.automatic_points,
                    p.project_name,
                    p.project_id,
                    CASE 
                        WHEN t.due_date IS NULL THEN 999
                        ELSE DATEDIFF(t.due_date, CURDATE())
                    END as days_until_due
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 WHERE p.project_name IN ('Tareas Recurrentes', 'Tareas Eventuales')
                   AND t.assigned_to_user_id = ?
                   AND t.status != 'completed'
                   AND t.is_subtask = 0
                 ORDER BY t.due_date ASC"
            );
            $stmt->execute([$userId]);
            $specialTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Tareas especiales (recurrentes/eventuales) encontradas: " . count($specialTasks));
            error_log("Detalle de tareas especiales: " . print_r($specialTasks, true));
            
            // Log detallado de cada tarea especial
            foreach ($specialTasks as $task) {
                error_log("TAREA ESPECIAL - ID: {$task['task_id']}, Nombre: {$task['task_name']}, Proyecto: {$task['project_name']}, Project ID: {$task['project_id']}");
            }
            
            // Verificar que las tareas especiales tengan el project_name correcto
            foreach ($specialTasks as $task) {
                if (!in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales'])) {
                    error_log("ERROR: Tarea especial con project_name incorrecto: {$task['project_name']}");
                }
            }
            
            // Verificar en la base de datos directamente
            $verifyStmt = $this->db->prepare("SELECT project_id, project_name FROM Projects WHERE project_name IN ('Tareas Recurrentes', 'Tareas Eventuales')");
            $verifyStmt->execute();
            $verifyProjects = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Proyectos especiales en BD: " . print_r($verifyProjects, true));

            // Combinar todas las listas
            $allTasks = array_merge($clanTasks, $personalTasks, $specialTasks);
            error_log("Total de tareas combinadas: " . count($allTasks));
            
            // Verificar duplicados por task_id
            $taskIds = [];
            $duplicates = [];
            foreach ($allTasks as $task) {
                $taskId = $task['task_id'];
                if (in_array($taskId, $taskIds)) {
                    $duplicates[] = $taskId;
                    error_log("DUPLICADO ENCONTRADO - Task ID: $taskId, Proyecto: {$task['project_name']}, Tipo: " . ($task['is_personal'] ?? 'N/A'));
                } else {
                    $taskIds[] = $taskId;
                }
            }
            
            if (!empty($duplicates)) {
                error_log("TAREAS DUPLICADAS ENCONTRADAS: " . implode(', ', $duplicates));
            } else {
                error_log("No se encontraron duplicados");
            }
            
            // Log de resumen por tipo
            $clanCount = count($clanTasks);
            $personalCount = count($personalTasks);
            $specialCount = count($specialTasks);
            error_log("RESUMEN - Clan: $clanCount, Personales: $personalCount, Especiales: $specialCount, Total: " . count($allTasks));

            // Organizar tareas por columnas del Kanban
            $kanbanColumns = [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];

            foreach ($allTasks as $task) {
                $daysUntilDue = (int)($task['days_until_due'] ?? 0);
                error_log("Tarea: {$task['task_name']} - Días hasta vencimiento: $daysUntilDue");
                
                if ($daysUntilDue < 0) {
                    $kanbanColumns['vencidas'][] = $task;
                    error_log("  -> Agregada a 'vencidas'");
                } elseif ($daysUntilDue === 0) {
                    $kanbanColumns['hoy'][] = $task;
                    error_log("  -> Agregada a 'hoy'");
                } elseif ($daysUntilDue <= 7) {
                    $kanbanColumns['1_semana'][] = $task;
                    error_log("  -> Agregada a '1_semana'");
                } elseif ($daysUntilDue <= 14) {
                    $kanbanColumns['2_semanas'][] = $task;
                    error_log("  -> Agregada a '2_semanas'");
                } else {
                    error_log("  -> No agregada a ninguna columna (días: $daysUntilDue)");
                }
            }

            error_log("Columnas del Kanban:");
            foreach ($kanbanColumns as $column => $tasks) {
                error_log("  $column: " . count($tasks) . " tareas");
            }

            // Ordenar cada columna por prioridad y fecha
            foreach ($kanbanColumns as $column => &$tasks) {
                usort($tasks, function($a, $b) {
                    // Primero por prioridad (crítica primero)
                    $priorityScores = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
                    $aScore = $priorityScores[$a['priority']] ?? 2;
                    $bScore = $priorityScores[$b['priority']] ?? 2;
                    
                    if ($aScore !== $bScore) {
                        return $bScore - $aScore; // Prioridad más alta primero
                    }
                    
                    // Luego por fecha de vencimiento (más cercana primero)
                    $aDate = $a['due_date'] ? strtotime($a['due_date']) : PHP_INT_MAX;
                    $bDate = $b['due_date'] ? strtotime($b['due_date']) : PHP_INT_MAX;
                    return $aDate - $bDate;
                });
                
                error_log("  $column ordenada por prioridad y fecha");
            }

            return $kanbanColumns;
        } catch (Exception $e) {
            error_log('Error getKanbanTasks: ' . $e->getMessage());
            return [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];
        }
    }

    /**
     * Crear tarea en proyecto (personal o delegado)
     */
    public function createProjectTask() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $projectId = (int)($_POST['project_id'] ?? 0);
            $taskName = trim($_POST['task_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            
            // Mapear 'urgent' a 'critical' para compatibilidad con el enum de la BD
            if ($priority === 'urgent') {
                $priority = 'critical';
                error_log("createProjectTask - Priority mapped from 'urgent' to 'critical' for DB compatibility");
            }
            
            $dueDate = $_POST['due_date'] ?? '';
            $userId = $this->currentUser['user_id'];
            
            // Campos de recurrencia
            $isRecurrent = isset($_POST['is_recurrent']) ? 1 : 0;
            $recurrenceType = $isRecurrent ? trim($_POST['recurrence_type'] ?? '') : null;
            $recurrenceStart = $isRecurrent ? trim($_POST['recurrence_start_date'] ?? '') : null;
            $recurrenceEnd = $isRecurrent ? trim($_POST['recurrence_end_date'] ?? '') : null;
            
            error_log('createProjectTask - Recurrencia: is_recurrent=' . $isRecurrent . ', type=' . ($recurrenceType ?? 'NULL') . ', start=' . ($recurrenceStart ?? 'NULL') . ', end=' . ($recurrenceEnd ?? 'NULL'));
            
            // Validaciones básicas
            if (empty($taskName)) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la tarea es requerido']);
                return;
            }
            
            if ($projectId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Proyecto inválido']);
                return;
            }
            
            // Verificar que el proyecto existe y tiene permisos
            $project = $this->projectModel->findById($projectId);
            if (!$project) {
                echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado']);
                return;
            }
            
            // Los miembros del clan pueden crear tareas en cualquier proyecto
            // (restricción de permisos eliminada según solicitud del usuario)
            
            // Validaciones de recurrencia
            if ($isRecurrent) {
                if (empty($recurrenceType)) {
                    echo json_encode(['success' => false, 'message' => 'El tipo de recurrencia es requerido']);
                    return;
                }
                
                if (empty($recurrenceStart)) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de inicio de recurrencia es requerida']);
                    return;
                }
                
                $validRecurrenceTypes = ['daily', 'weekly', 'monthly', 'quarterly'];
                if (!in_array($recurrenceType, $validRecurrenceTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Tipo de recurrencia inválido']);
                    return;
                }
                
                // Validar que la fecha de inicio no sea anterior a hoy
                if (strtotime($recurrenceStart) < strtotime(date('Y-m-d'))) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de inicio no puede ser anterior a hoy']);
                    return;
                }
                
                // Si hay fecha de fin, validar que sea posterior a la de inicio
                if (!empty($recurrenceEnd) && strtotime($recurrenceEnd) <= strtotime($recurrenceStart)) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de vigencia debe ser posterior a la fecha de inicio']);
                    return;
                }
            }
            
            // Si es recurrente, usar la fecha de inicio de recurrencia como due_date
            $taskDueDate = $isRecurrent ? $recurrenceStart : (!empty($dueDate) ? $dueDate : null);
            
            // Crear la tarea
            $taskData = [
                'task_name' => $taskName,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $taskDueDate,
                'status' => 'pending',
                'project_id' => $projectId,
                'assigned_to_user_id' => $userId,
                'created_by_user_id' => $userId,
                'is_recurrent' => $isRecurrent,
                'recurrence_type' => $recurrenceType,
                'recurrence_start_date' => $recurrenceStart,
                'recurrence_end_date' => $recurrenceEnd
            ];
            
            $taskId = $this->taskModel->createProjectTaskWithRecurrence($taskData);
            
            if ($taskId) {
                $message = 'Tarea creada exitosamente';
                $generatedInstances = 0;
                
                // Si es tarea recurrente, generar instancias inmediatamente
                if ($isRecurrent) {
                    $generatedInstances = $this->taskModel->generateInstancesForTask($taskId);
                    if ($generatedInstances > 0) {
                        $message .= ". Se generaron $generatedInstances instancias recurrentes";
                    }
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'task_id' => $taskId,
                    'generated_instances' => $generatedInstances
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la tarea']);
            }
            
        } catch (Exception $e) {
            error_log('Error createProjectTask: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    public function createPersonalTask() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Log para debugging
        error_log('createPersonalTask llamado con POST data: ' . print_r($_POST, true));

        try {
            $taskName = trim($_POST['task_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            $dueDate = $_POST['due_date'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $userId = (int)($_POST['user_id'] ?? 0);
            
            // Campos de recurrencia
            $isRecurrent = isset($_POST['is_recurrent']) ? 1 : 0;
            $recurrenceType = $isRecurrent ? trim($_POST['recurrence_type'] ?? '') : null;
            $recurrenceStart = $isRecurrent ? trim($_POST['recurrence_start_date'] ?? '') : null;
            $recurrenceEnd = $isRecurrent ? trim($_POST['recurrence_end_date'] ?? '') : null;

            // Log de datos recibidos
            error_log("Datos procesados: task_name=$taskName, priority=$priority, due_date=$dueDate, user_id=$userId, is_recurrent=$isRecurrent");

            // Validaciones básicas
            if (empty($taskName)) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la tarea es requerido']);
                return;
            }

            // Validar fecha límite SOLO si NO es recurrente
            if (!$isRecurrent && empty($dueDate)) {
                echo json_encode(['success' => false, 'message' => 'La fecha de vencimiento es requerida']);
                return;
            }

            if ($userId !== (int)$this->currentUser['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
                return;
            }
            
            // Validaciones de recurrencia
            if ($isRecurrent) {
                if (empty($recurrenceType)) {
                    echo json_encode(['success' => false, 'message' => 'El tipo de recurrencia es requerido']);
                    return;
                }
                
                if (empty($recurrenceStart)) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de inicio de recurrencia es requerida']);
                    return;
                }
                
                $validRecurrenceTypes = ['daily', 'weekly', 'monthly', 'quarterly'];
                if (!in_array($recurrenceType, $validRecurrenceTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Tipo de recurrencia inválido']);
                    return;
                }
                
                // Validar que la fecha de inicio no sea anterior a hoy
                if (strtotime($recurrenceStart) < strtotime(date('Y-m-d'))) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de inicio no puede ser anterior a hoy']);
                    return;
                }
                
                // Si hay fecha de fin, validar que sea posterior a la de inicio
                if (!empty($recurrenceEnd) && strtotime($recurrenceEnd) <= strtotime($recurrenceStart)) {
                    echo json_encode(['success' => false, 'message' => 'La fecha de vigencia debe ser posterior a la fecha de inicio']);
                    return;
                }
            }

            // Crear la tarea personal con campos básicos y de recurrencia
            // Si es recurrente, usar la fecha de inicio de recurrencia como due_date
            $taskDueDate = $isRecurrent ? $recurrenceStart : $dueDate;
            
            $taskData = [
                'task_name' => $taskName,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $taskDueDate, // Usar fecha de inicio si es recurrente
                'status' => $status,
                'assigned_to_user_id' => $userId,
                'is_recurrent' => $isRecurrent,
                'recurrence_type' => $recurrenceType,
                'recurrence_start_date' => $recurrenceStart,
                'recurrence_end_date' => $recurrenceEnd
            ];

            error_log('Task data a crear: ' . print_r($taskData, true));

            // Crear tarea personal directamente
            $taskId = $this->taskModel->createPersonalTaskSimple($taskData);

            if ($taskId) {
                $message = 'Tarea personal creada exitosamente';
                $generatedInstances = 0;
                
                // Si es tarea recurrente, generar instancias inmediatamente
                if ($isRecurrent) {
                    $generatedInstances = $this->taskModel->generateInstancesForTask($taskId);
                    if ($generatedInstances > 0) {
                        $message .= ". Se generaron $generatedInstances instancias recurrentes";
                    }
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'task_id' => $taskId,
                    'generated_instances' => $generatedInstances
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la tarea - revisar logs del servidor']);
            }

        } catch (Exception $e) {
            error_log('Error createPersonalTask: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
    }

    public function testPersonalTask() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Test exitoso',
            'user_id' => $this->currentUser['user_id'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function createPersonalProject() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            $projectName = trim($_POST['project_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $userId = (int)($_POST['user_id'] ?? 0);

            // Validaciones
            if (empty($projectName)) {
                echo json_encode(['success' => false, 'message' => 'El nombre del proyecto es requerido']);
                return;
            }

            if ($userId !== (int)$this->currentUser['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
                return;
            }

            // Crear el proyecto personal usando el modelo Project
            $projectData = [
                'project_name' => $projectName,
                'description' => $description,
                'user_id' => $userId
            ];

            $projectId = $this->projectModel->createPersonalProject($projectData);

            if ($projectId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Proyecto personal creado exitosamente',
                    'project_id' => $projectId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el proyecto personal']);
            }

        } catch (Exception $e) {
            error_log('Error createPersonalProject: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Crear subtarea para usuarios normales (incluyendo tareas de otros clanes)
     */
    public function addSubtask() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $title = trim(Utils::sanitizeInput($_POST['title'] ?? ''));
        $description = trim(Utils::sanitizeInput($_POST['description'] ?? ''));
        $status = Utils::sanitizeInput($_POST['status'] ?? 'pending');
        $completionPercentage = (float)($_POST['completion_percentage'] ?? 0);
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        if (empty($title)) {
            Utils::jsonResponse(['success' => false, 'message' => 'El título es requerido'], 400);
        }

        // Validar estado
        $validStatuses = ['pending', 'in_progress', 'completed', 'blocked'];
        if (!in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        // Validar porcentaje
        if ($completionPercentage < 0 || $completionPercentage > 100) {
            $completionPercentage = 0;
        }

        try {
            // Verificar que la tarea existe
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }

            // Verificar que el usuario tiene acceso a la tarea (asignado directamente o por tabla Task_Assignments)
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            if (!$isAssigned) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para agregar subtareas a esta tarea'], 403);
            }

            // Crear la subtarea usando el método existente
            $subtaskId = $this->taskModel->createSubtaskAdvanced(
                $taskId,
                $title,
                $this->currentUser['user_id'],
                $description,
                $completionPercentage,
                $dueDate,
                'medium', // prioridad por defecto
                $this->currentUser['user_id']  // asignar al usuario actual
            );

            if ($subtaskId) {
                // Actualizar el estado si no es 'pending'
                if ($status !== 'pending') {
                    $this->taskModel->updateSubtaskStatus($subtaskId, $status, $completionPercentage, $this->currentUser['user_id']);
                }

                Utils::jsonResponse([
                    'success' => true, 
                    'message' => 'Subtarea creada exitosamente',
                    'subtask_id' => $subtaskId
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear la subtarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al crear subtarea (member): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al crear la subtarea'], 500);
        }
    }
    
    private function loadView($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die('Vista no encontrada: ' . $view);
        }
    }
    
    /**
     * Actualizar estado de subtarea (para clan members)
     */
    /**
     * Actualizar progreso de subtarea
     */
    public function updateSubtaskProgress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        // Obtener usuario actual si existe, sino usar usuario por defecto
        $userId = $this->currentUser['user_id'] ?? 18; // Usuario por defecto si no hay sesión
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $completionPercentage = isset($_POST['completion_percentage']) ? (float)$_POST['completion_percentage'] : null;
        
        if ($subtaskId <= 0 || $completionPercentage === null) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // SIN RESTRICCIONES - TODOS PUEDEN ACTUALIZAR PROGRESO
            // QUITADO - Sin verificación de clan
            
            // Actualizar solo el progreso
            $result = $this->taskModel->updateSubtaskStatus(
                $subtaskId, 
                null, // no cambiar estado
                $completionPercentage, 
                $userId
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Progreso actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar progreso'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en updateSubtaskProgress: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    public function updateSubtaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        // Obtener usuario actual si existe, sino usar usuario por defecto
        $userId = $this->currentUser['user_id'] ?? 18; // Usuario por defecto si no hay sesión
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $status = Utils::sanitizeInput($_POST['status'] ?? '');
        $completionPercentage = isset($_POST['completion_percentage']) ? (float)$_POST['completion_percentage'] : null;
        
        if ($subtaskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
        }
        
        // Si no se envía status pero sí completion_percentage, solo actualizar porcentaje
        if (empty($status) && $completionPercentage === null) {
            Utils::jsonResponse(['success' => false, 'message' => 'Debe proporcionar estado o porcentaje de completación'], 400);
        }
        
        try {
            // Verificar que la subtarea pertenece a una tarea del clan del usuario
            $stmt = $this->db->prepare("
                SELECT s.*, t.project_id, t.created_by_user_id, t.assigned_to_user_id, t.is_recurrent, 
                       p.clan_id, p.project_type
                FROM Subtasks s
                JOIN Tasks t ON s.task_id = t.task_id
                JOIN Projects p ON t.project_id = p.project_id
                WHERE s.subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            $subtask = $stmt->fetch();
            
            if (!$subtask) {
                Utils::jsonResponse(['success' => false, 'message' => 'Subtarea no encontrada'], 404);
            }
            
            // SIN RESTRICCIONES - TODOS PUEDEN CAMBIAR ESTADOS
            // QUITADO - Sin verificación de clan ni permisos
            
            // Actualizar estado
            $result = $this->taskModel->updateSubtaskStatus(
                $subtaskId, 
                $status, 
                $completionPercentage, 
                $userId
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar estado'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al actualizar estado de subtarea (clan member): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar estado'], 500);
        }
    }

    /**
     * Agregar comentario a una subtarea
     */
    public function addSubtaskComment() {
        // SIN RESTRICCIONES - TODOS PUEDEN COMENTAR
        
        try {
            // Obtener usuario actual si existe, sino usar usuario por defecto
            $userId = $this->currentUser['user_id'] ?? 18; // Usuario por defecto si no hay sesión
            
            $input = json_decode(file_get_contents('php://input'), true);
            $subtaskId = $input['subtask_id'] ?? null;
            $commentText = trim($input['comment_text'] ?? '');

            if (!$subtaskId || !$commentText) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y texto del comentario son requeridos']);
                return;
            }

            // SIN VERIFICAR PERMISOS - TODOS PUEDEN COMENTAR
            // QUITADO - Sin restricciones

            $commentId = $this->subtaskModel->addComment(
                $subtaskId, 
                $userId, 
                $commentText
            );

            if ($commentId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Comentario agregado exitosamente',
                    'comment_id' => $commentId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al agregar comentario']);
            }

        } catch (Exception $e) {
            error_log("Error en addSubtaskComment (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener comentarios de una subtarea
     */
    public function getSubtaskComments() {
        // SIN RESTRICCIONES - TODOS PUEDEN VER COMENTARIOS
        // QUITADO - Sin verificación de acceso

        try {
            $subtaskId = $_GET['subtask_id'] ?? null;

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            // SIN VERIFICAR PERMISOS - TODOS PUEDEN VER
            // QUITADO - Sin restricciones

            $comments = $this->subtaskModel->getComments($subtaskId);
            
            // Procesar HTML de comentarios para visualización segura
            foreach ($comments as &$comment) {
                if (!empty($comment['comment_text'])) {
                    $comment['comment_text'] = Utils::sanitizeHtml($comment['comment_text']);
                }
            }
            
            echo json_encode(['success' => true, 'comments' => $comments]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskComments (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Subir archivo adjunto a una subtarea
     */
    public function uploadSubtaskAttachment() {
        // SIN RESTRICCIONES - TODOS PUEDEN SUBIR
        
        try {
            // Obtener usuario actual si existe, sino usar usuario por defecto
            $userId = $this->currentUser['user_id'] ?? 18; // Usuario por defecto si no hay sesión
            error_log("=== INICIO uploadSubtaskAttachment (ClanMember) ===");
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            $subtaskId = $_POST['subtask_id'] ?? null;
            $commentId = $_POST['comment_id'] ?? null;
            $description = trim($_POST['description'] ?? '');

            if (!$subtaskId || !isset($_FILES['file'])) {
                error_log("Error: Faltan datos requeridos - subtaskId: " . ($subtaskId ?? 'NULL') . ", file: " . (isset($_FILES['file']) ? 'OK' : 'NULL'));
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y archivo son requeridos']);
                return;
            }

            // Verificar que la subtarea existe
            if (!$this->subtaskModel) {
                error_log("Error: subtaskModel no está inicializado");
                throw new Exception("Modelo de subtarea no disponible");
            }

            // NO VERIFICAR PERMISOS - TODOS PUEDEN SUBIR ARCHIVOS
            // $permissions = $this->subtaskModel->checkUserPermissions($subtaskId, $this->currentUser['user_id']);
            // QUITADO - Sin restricciones

            $file = $_FILES['file'];
            
            // Verificar errores del archivo
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                    UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                    UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
                    UPLOAD_ERR_CANT_WRITE => 'Error escribiendo archivo al disco',
                    UPLOAD_ERR_EXTENSION => 'Extensión PHP detuvo la subida'
                ];
                $errorMsg = $errorMessages[$file['error']] ?? 'Error desconocido en upload';
                error_log("Error en upload de archivo: " . $errorMsg);
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $errorMsg]);
                return;
            }
            
            // Validar tamaño de archivo
            $maxSize = 50 * 1024 * 1024; // 50MB
            if ($file['size'] > $maxSize) {
                error_log("Error: Archivo demasiado grande - " . $file['size'] . " bytes");
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 50MB)']);
                return;
            }

            // Guardar archivo
            error_log("Intentando guardar archivo...");
            $fileInfo = $this->subtaskModel->saveUploadedFile($file, $subtaskId, $userId);
            if (!$fileInfo) {
                error_log("Error: saveUploadedFile retornó false");
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo en el servidor']);
                return;
            }
            
            error_log("Archivo guardado exitosamente: " . print_r($fileInfo, true));

            // Guardar en base de datos
            error_log("Intentando guardar en base de datos...");
            
            // Usar el nombre guardado (saved_name) como file_path para que file-viewer.php lo encuentre
            $dbFilePath = $fileInfo['saved_name'];
            
            error_log("📁 Guardando en BD - file_name: " . $fileInfo['original_name']);
            error_log("📁 Guardando en BD - file_path: " . $dbFilePath);
            
            $attachmentId = $this->subtaskModel->addAttachment(
                $subtaskId,
                $userId,
                $fileInfo['original_name'],
                $dbFilePath,  // Usar el nombre del archivo físico, no la URL
                $fileInfo['file_size'],
                $fileInfo['file_type'],
                $description,
                $commentId
            );

            if ($attachmentId) {
                error_log("✅ Adjunto guardado exitosamente con ID: " . $attachmentId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Archivo adjuntado exitosamente',
                    'attachment_id' => $attachmentId,
                    'file_info' => $fileInfo
                ]);
            } else {
                error_log("Error: addAttachment retornó false");
                echo json_encode(['success' => false, 'message' => 'Error al guardar el adjunto en la base de datos']);
            }

        } catch (Exception $e) {
            error_log("❌ Error en uploadSubtaskAttachment (member): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'debug' => APP_DEBUG ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * Obtener adjuntos de una subtarea
     */
    public function getSubtaskAttachments() {
        // SIN RESTRICCIONES - TODOS PUEDEN VER ARCHIVOS
        // $this->requireAuth();
        // QUITADO - Sin verificación de acceso

        try {
            $subtaskId = $_GET['subtask_id'] ?? null;
            $attachmentId = $_GET['attachment_id'] ?? null;

            // Si se solicita un attachment específico
            if ($attachmentId) {
                $attachment = $this->subtaskModel->getAttachmentById($attachmentId);
                if (!$attachment) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
                    return;
                }
                
                // SIN VERIFICAR PERMISOS - TODOS PUEDEN VER
                // QUITADO - Sin restricciones
                
                echo json_encode(['success' => true, 'attachments' => [$attachment]]);
                return;
            }

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            // SIN VERIFICAR PERMISOS - TODOS PUEDEN VER
            // QUITADO - Sin restricciones

            $attachments = $this->subtaskModel->getAttachments($subtaskId);
            echo json_encode(['success' => true, 'attachments' => $attachments]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskAttachments (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar comentario de subtarea
     */
    public function deleteSubtaskComment() {
        // SIN RESTRICCIONES - TODOS PUEDEN ELIMINAR COMENTARIOS
        // QUITADO - Sin verificación de acceso

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $commentId = $input['comment_id'] ?? null;

            if (!$commentId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Comment ID requerido']);
                return;
            }

            $result = $this->subtaskModel->deleteComment($commentId, $this->currentUser['user_id']);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Comentario eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar comentario o no tienes permisos']);
            }

        } catch (Exception $e) {
            error_log("Error en deleteSubtaskComment (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar adjunto de subtarea
     */
    public function deleteSubtaskAttachment() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $attachmentId = $input['attachment_id'] ?? null;

            if (!$attachmentId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Attachment ID requerido']);
                return;
            }

            $result = $this->subtaskModel->deleteAttachment($attachmentId, $this->currentUser['user_id']);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Adjunto eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar adjunto o no tienes permisos']);
            }

        } catch (Exception $e) {
            error_log("Error en deleteSubtaskAttachment (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener conteos de comentarios y adjuntos de una subtarea
     */
    public function getSubtaskCounts() {
        // SIN RESTRICCIONES - TODOS PUEDEN VER CONTEOS
        // QUITADO - Sin verificación de acceso

        try {
            $subtaskId = $_GET['subtask_id'] ?? null;

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            $counts = $this->subtaskModel->getSubtaskCounts($subtaskId);
            echo json_encode(['success' => true, 'counts' => $counts]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskCounts (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Editar subtarea
     */
    public function editSubtask() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $subtaskId = $input['subtask_id'] ?? null;
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $status = $input['status'] ?? null;
            $completionPercentage = $input['completion_percentage'] ?? null;

            if (!$subtaskId || !$title) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y título son requeridos']);
                return;
            }

            // Construir query dinámicamente
            $fields = ['title = ?', 'description = ?', 'updated_at = CURRENT_TIMESTAMP'];
            $params = [$title, $description];
            
            if ($status !== null) {
                $fields[] = 'status = ?';
                $params[] = $status;
            }
            
            if ($completionPercentage !== null) {
                $fields[] = 'completion_percentage = ?';
                $params[] = $completionPercentage;
            }
            
            $params[] = $subtaskId; // Para el WHERE

            // Actualizar subtarea en base de datos
            $stmt = $this->db->prepare("
                UPDATE Subtasks 
                SET " . implode(', ', $fields) . "
                WHERE subtask_id = ?
            ");
            
            $result = $stmt->execute($params);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Subtarea actualizada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar subtarea']);
            }

        } catch (Exception $e) {
            error_log("Error en editSubtask (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function updateTaskProgress() {
        // Debug log
        error_log("=== updateTaskProgress called ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $completionPercentage = (int)($_POST['completion_percentage'] ?? 0);

        if ($taskId <= 0 || $completionPercentage < 0 || $completionPercentage > 100) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        // Verificar que la tarea existe y pertenece al clan del usuario
        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        $project = $this->projectModel->findById($task['project_id']);
        if (!$project || (int)$project['clan_id'] !== (int)$this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado al proyecto'], 403);
        }

        // Verificar permisos: debe ser el creador O estar asignado a la tarea
        $isCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id'])
            || (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
        
        if (!$isCreator && !$isAssigned) {
            Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para editar el progreso de esta tarea'], 403);
        }

        // Actualizar solo el porcentaje de completion
        $success = $this->taskModel->updateTaskProgress($taskId, $completionPercentage);

        if ($success) {
            Utils::jsonResponse(['success' => true, 'message' => 'Progreso actualizado correctamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar el progreso'], 500);
        }
    }
    
    /**
     * Método para actualizar estado de subtarea
     */
    public function simpleToggleSubtask() {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        
        error_log("=== SIMPLE TOGGLE SUBTASK (MEMBER) ===");
        error_log("POST: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        
        if (!$subtaskId || $subtaskId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de subtarea inválido: ' . $subtaskId]);
            exit;
        }
        
        if (!in_array($status, ['pending', 'completed', 'in_progress', 'cancelled'])) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido: ' . $status]);
            exit;
        }
        
        try {
            // Buscar la subtarea
            $stmt = $this->db->prepare("SELECT * FROM Subtasks WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            $subtask = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subtask) {
                echo json_encode(['success' => false, 'message' => 'Subtarea no encontrada']);
                exit;
            }
            
            // Verificar que el usuario tenga permisos (debe estar asignado a la subtarea)
            if ($subtask['assigned_to_user_id'] != $this->currentUser['user_id']) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar esta subtarea']);
                exit;
            }
            
            // Calcular completion_percentage - 100% si completada, 0% si no completada
            $completionPercentage = ($status === 'completed') ? 100 : 0;
            
            // Actualizar la subtarea
            $updateStmt = $this->db->prepare("
                UPDATE Subtasks 
                SET status = ?, completion_percentage = ?, updated_at = NOW() 
                WHERE subtask_id = ?
            ");
            
            $result = $updateStmt->execute([$status, $completionPercentage, $subtaskId]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Subtarea actualizada correctamente',
                    'completion_percentage' => $completionPercentage,
                    'status' => $status
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la subtarea']);
            }
            
        } catch (Exception $e) {
            error_log("Error en simpleToggleSubtask (member): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Exportar historial de tarea a Excel
     */
    public function exportTaskHistory() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        try {
            $taskId = (int)($_GET['task_id'] ?? 0);
            
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de tarea requerido']);
                return;
            }

            // Verificar que la tarea existe y el usuario tiene acceso
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
                return;
            }

            // Verificar permisos: debe ser creador de la tarea O tener permisos de edición
            $isTaskCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
            $isAssigned = (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
            
            if (!$isTaskCreator && !$isAssigned) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para descargar el historial de esta tarea']);
                return;
            }

            // Obtener historial de la tarea
            $history = $this->taskModel->getHistory($taskId);
            
            // Crear contenido CSV (compatible con Excel)
            $csvContent = $this->generateHistoryExcel($task, $history);
            
            // Configurar headers para descarga CSV
            $filename = "historial_tarea_{$taskId}_" . date('Y-m-d') . ".csv";
            
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            // Agregar BOM UTF-8 para Excel
            echo "\xEF\xBB\xBF";
            echo $csvContent;
            exit;

        } catch (Exception $e) {
            error_log("Error en exportTaskHistory (member): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Generar contenido CSV del historial (compatible con Excel)
     */
    private function generateHistoryExcel($task, $history) {
        $csv = [];
        
        // Encabezado del archivo
        $csv[] = "HISTORIAL DE TAREA";
        $csv[] = "";
        $csv[] = "Tarea," . $this->escapeCsvValue($task['task_name']);
        $csv[] = "Proyecto," . $this->escapeCsvValue($task['project_name'] ?? 'N/A');
        $csv[] = "Estado," . $this->escapeCsvValue($task['status']);
        $csv[] = "Creado," . $this->escapeCsvValue($task['created_at']);
        $csv[] = "Generado," . date('Y-m-d H:i:s');
        $csv[] = "";
        
        // Encabezados de columnas
        $csv[] = "Fecha,Usuario,Acción,Campo,Valor Anterior,Valor Nuevo,Notas";
        
        // Datos del historial
        if (!empty($history)) {
            foreach ($history as $h) {
                $row = [
                    $this->escapeCsvValue($h['created_at'] ?? ''),
                    $this->escapeCsvValue($h['full_name'] ?? $h['username'] ?? ''),
                    $this->escapeCsvValue($this->translateActionType($h['action_type'] ?? '')),
                    $this->escapeCsvValue($this->translateFieldName($h['field_name'] ?? '')),
                    $this->escapeCsvValue($h['old_value'] ?? ''),
                    $this->escapeCsvValue($h['new_value'] ?? ''),
                    $this->escapeCsvValue($h['notes'] ?? '')
                ];
                $csv[] = implode(',', $row);
            }
        } else {
            $csv[] = "Sin historial disponible,,,,,";
        }
        
        return implode("\r\n", $csv);
    }

    /**
     * Escapar valores para CSV
     */
    private function escapeCsvValue($value) {
        // Convertir a string y limpiar
        $value = (string)$value;
        $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        
        // Si contiene comas, comillas o espacios, envolver en comillas
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, ' ') !== false) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        
        return $value;
    }

    /**
     * Traducir tipos de acción
     */
    private function translateActionType($actionType) {
        $translations = [
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'status_changed' => 'Estado Cambiado',
            'assigned' => 'Asignado',
            'commented' => 'Comentario Agregado',
            'completed' => 'Completado',
            'deleted' => 'Eliminado',
            'attached' => 'Archivo Adjuntado'
        ];
        
        return $translations[$actionType] ?? ucfirst($actionType);
    }

    /**
     * Traducir nombres de campos
     */
    private function translateFieldName($fieldName) {
        $translations = [
            'status' => 'Estado',
            'assigned_to_user_id' => 'Usuario Asignado',
            'completion_percentage' => 'Porcentaje Completado',
            'due_date' => 'Fecha Límite',
            'description' => 'Descripción',
            'task_name' => 'Nombre de Tarea',
            'priority' => 'Prioridad'
        ];
        
        return $translations[$fieldName] ?? ucfirst($fieldName);
    }

    /**
     * Guardar estado de checkbox en comentario
     */
    public function saveCheckboxState() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $rawInput = file_get_contents('php://input');
        error_log("=== CHECKBOX BACKEND DEBUG ===");
        error_log("Raw input: " . $rawInput);
        
        $input = json_decode($rawInput, true);
        error_log("Parsed input: " . print_r($input, true));
        
        $commentId = (int)($input['comment_id'] ?? 0);
        $commentType = $input['comment_type'] ?? ''; // 'task' o 'subtask'
        $checkboxIndex = (int)($input['checkbox_index'] ?? 0);
        $checkboxText = trim($input['checkbox_text'] ?? '');
        $isChecked = (bool)($input['is_checked'] ?? false);
        
        error_log("Processed data: commentId=$commentId, commentType=$commentType, checkboxIndex=$checkboxIndex, isChecked=" . ($isChecked ? 'true' : 'false'));
        
        if ($commentId <= 0 || !in_array($commentType, ['task', 'subtask']) || empty($checkboxText)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar permisos según el tipo de comentario
            if ($commentType === 'task') {
                // Verificar que el comentario pertenece a una tarea accesible
                $stmt = $this->db->prepare("
                    SELECT tc.*, t.project_id, t.created_by_user_id, t.assigned_to_user_id
                    FROM Task_Comments tc
                    JOIN Tasks t ON tc.task_id = t.task_id
                    WHERE tc.comment_id = ?
                ");
                $stmt->execute([$commentId]);
                $comment = $stmt->fetch();
                
                if (!$comment) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Comentario no encontrado'], 404);
                }
                
                // Verificar que el usuario tiene acceso a la tarea
                $isCreator = (int)$comment['created_by_user_id'] === (int)$this->currentUser['user_id'];
                $isAssigned = (int)$comment['assigned_to_user_id'] === (int)$this->currentUser['user_id'];
                
                if (!$isCreator && !$isAssigned) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos para modificar este comentario'], 403);
                }
                
            } else { // subtask
                // Verificar que el comentario pertenece a una subtarea accesible
                $stmt = $this->db->prepare("
                    SELECT sc.*, s.task_id, t.created_by_user_id, t.assigned_to_user_id, p.clan_id
                    FROM Subtask_Comments sc
                    JOIN Subtasks s ON sc.subtask_id = s.subtask_id
                    JOIN Tasks t ON s.task_id = t.task_id
                    JOIN Projects p ON t.project_id = p.project_id
                    WHERE sc.comment_id = ?
                ");
                $stmt->execute([$commentId]);
                $comment = $stmt->fetch();
                
                if (!$comment) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Comentario no encontrado'], 404);
                }
                
                // Para subtareas: permitir a cualquier miembro del clan marcar checkboxes
                // Solo verificar que el usuario tenga acceso general a la tarea (creador, asignado, o miembro del clan)
                $isCreator = (int)$comment['created_by_user_id'] === (int)$this->currentUser['user_id'];
                $isAssigned = (int)$comment['assigned_to_user_id'] === (int)$this->currentUser['user_id'];
                $isClanMember = $this->hasMemberAccess(); // Verificar si es miembro del clan
                
                if (!$isCreator && !$isAssigned && !$isClanMember) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos para modificar este comentario'], 403);
                }
            }
            
            // Cargar el modelo CheckboxState
            if (!class_exists('CheckboxState')) {
                require_once __DIR__ . '/../models/CheckboxState.php';
            }
            
            // Guardar estado del checkbox
            $checkboxModel = new CheckboxState();
            $checkboxModel->createTableIfNotExists();
            
            $result = $checkboxModel->saveCheckboxState(
                $commentId,
                $commentType,
                $checkboxIndex,
                $checkboxText,
                $isChecked,
                $this->currentUser['user_id']
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Estado guardado correctamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al guardar estado'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al guardar estado de checkbox (member): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener estados de checkboxes para comentarios
     */
    public function getCheckboxStates() {
        $this->requireAuth();
        
        $commentIds = $_GET['comment_ids'] ?? '';
        $commentType = $_GET['comment_type'] ?? '';
        
        if (empty($commentIds) || !in_array($commentType, ['task', 'subtask'])) {
            Utils::jsonResponse(['success' => false, 'message' => 'Parámetros inválidos'], 400);
        }
        
        try {
            // Cargar el modelo CheckboxState
            if (!class_exists('CheckboxState')) {
                require_once __DIR__ . '/../models/CheckboxState.php';
            }
            
            $commentIdsArray = explode(',', $commentIds);
            $checkboxModel = new CheckboxState();
            $allStates = [];
            
            foreach ($commentIdsArray as $commentId) {
                $commentId = (int)trim($commentId);
                if ($commentId > 0) {
                    $states = $checkboxModel->getCheckboxStates($commentId, $commentType);
                    if (!empty($states)) {
                        $allStates[$commentId] = $states;
                    }
                }
            }
            
            Utils::jsonResponse(['success' => true, 'states' => $allStates]);
            
        } catch (Exception $e) {
            error_log("Error al obtener estados de checkbox (member): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Asignar múltiples usuarios a una subtarea (para clan members)
     */
    public function assignSubtaskUsers() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_POST['subtask_id'] ?? 0);
            $userIdsRaw = $_POST['user_ids'] ?? [];
            
            // Log para debug
            error_log("DEBUG assignSubtaskUsers - subtaskId: $subtaskId, userIdsRaw: " . print_r($userIdsRaw, true));
            
            // Si user_ids viene como JSON string, decodificarlo
            if (is_string($userIdsRaw)) {
                $userIds = json_decode($userIdsRaw, true);
                error_log("DEBUG assignSubtaskUsers - userIds después de json_decode: " . print_r($userIds, true));
            } else {
                $userIds = $userIdsRaw;
            }

            if ($subtaskId <= 0 || empty($userIds) || !is_array($userIds)) {
                error_log("DEBUG assignSubtaskUsers - Datos inválidos: subtaskId=$subtaskId, userIds=" . print_r($userIds, true));
                Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
            }

            // Verificar que la subtarea existe y obtener información del proyecto/clan
            $stmt = $this->db->prepare("
                SELECT s.*, t.task_id, t.project_id, p.clan_id, t.task_name as parent_task_name
                FROM Subtasks s
                JOIN Tasks t ON s.task_id = t.task_id
                JOIN Projects p ON t.project_id = p.project_id
                WHERE s.subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            $subtask = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subtask) {
                Utils::jsonResponse(['success' => false, 'message' => 'Subtarea no encontrada'], 404);
            }

            // Verificar permisos: el usuario debe estar asignado a la tarea padre o ser del mismo clan
            $isAssignedToTask = $this->isTaskAssignedToUser($subtask['task_id'], $this->currentUser['user_id']);
            $isSameClan = $this->userClan && ((int)$subtask['clan_id'] === (int)$this->userClan['clan_id']);

            error_log("DEBUG assignSubtaskUsers - Permisos: isAssignedToTask=" . ($isAssignedToTask ? 'true' : 'false') . 
                     ", isSameClan=" . ($isSameClan ? 'true' : 'false') . 
                     ", userClan=" . print_r($this->userClan, true) . 
                     ", subtask_clan_id=" . $subtask['clan_id']);

            if (!$isAssignedToTask && !$isSameClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para asignar usuarios a esta subtarea'], 403);
            }

            // Inicializar modelo de asignaciones de subtareas
            if (!isset($this->subtaskAssignmentModel)) {
                require_once __DIR__ . '/../models/SubtaskAssignment.php';
                $this->subtaskAssignmentModel = new SubtaskAssignment();
            }

            // Validar que todos los usuarios existen y pertenecen al clan
            $userNames = [];
            foreach ($userIds as $userId) {
                $user = $this->userModel->findById($userId);
                if (!$user) {
                    Utils::jsonResponse(['success' => false, 'message' => "Usuario con ID $userId no encontrado"], 400);
                }
                
                // Verificar que el usuario pertenece al mismo clan
                $userClan = $this->userModel->getUserClan($userId);
                if (!$userClan || (int)$userClan['clan_id'] !== (int)$this->userClan['clan_id']) {
                    Utils::jsonResponse(['success' => false, 'message' => "El usuario {$user['full_name']} no pertenece al mismo clan"], 400);
                }
                $userNames[] = $user['full_name'] ?? $user['username'];
            }

            // Asignar usuarios usando el modelo de asignaciones
            error_log("DEBUG assignSubtaskUsers - Antes de asignar: subtaskId=$subtaskId, userIds=" . print_r($userIds, true));
            $newUsersAssigned = $this->subtaskAssignmentModel->assignUsers($subtaskId, $userIds, $this->currentUser['user_id']);
            error_log("DEBUG assignSubtaskUsers - Resultado asignación: " . print_r($newUsersAssigned, true));

            if ($newUsersAssigned !== false) {
                // Registrar en el historial de la tarea padre
                $historyStmt = $this->db->prepare("
                    INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                    VALUES (?, ?, 'assigned', 'subtask_assignment', 'múltiples usuarios', ?, ?)
                ");
                $historyStmt->execute([
                    $subtask['task_id'],
                    $this->currentUser['user_id'],
                    implode(', ', $userNames),
                    'Subtarea "' . $subtask['title'] . '" - ' . $newUsersAssigned . ' usuario(s) nuevo(s) asignado(s): ' . implode(', ', $userNames)
                ]);

                if ($newUsersAssigned > 0) {
                    Utils::jsonResponse([
                        'success' => true,
                        'message' => $newUsersAssigned . ' usuario(s) asignado(s) exitosamente',
                        'new_assignments' => $newUsersAssigned,
                        'assigned_users' => $userNames
                    ]);
                } else {
                    Utils::jsonResponse([
                        'success' => true,
                        'message' => 'Todos los usuarios ya estaban asignados',
                        'new_assignments' => 0
                    ]);
                }
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al asignar usuarios'], 500);
            }

        } catch (Exception $e) {
            error_log("Error en assignSubtaskUsers (ClanMember): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Desasignar un usuario específico de una subtarea
     */
    public function unassignSubtaskUser() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_POST['subtask_id'] ?? 0);
            $userId = (int)($_POST['user_id'] ?? 0);
            $userName = $_POST['user_name'] ?? '';

            // Si no se proporciona user_id pero sí user_name, buscar el ID por nombre
            if ($userId <= 0 && !empty($userName)) {
                $stmt = $this->db->prepare("SELECT user_id FROM Users WHERE full_name = ? OR username = ? LIMIT 1");
                $stmt->execute([$userName, $userName]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $userId = (int)$user['user_id'];
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                }
            }

            if ($subtaskId <= 0 || $userId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
            }

            // Verificar que la subtarea existe y obtener información del proyecto/clan
            $stmt = $this->db->prepare("
                SELECT s.*, t.task_id, t.project_id, p.clan_id, t.task_name as parent_task_name
                FROM Subtasks s
                JOIN Tasks t ON s.task_id = t.task_id
                JOIN Projects p ON t.project_id = p.project_id
                WHERE s.subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            $subtask = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subtask) {
                Utils::jsonResponse(['success' => false, 'message' => 'Subtarea no encontrada'], 404);
            }

            // Verificar permisos: el usuario debe estar asignado a la tarea padre o ser del mismo clan
            $isAssignedToTask = $this->isTaskAssignedToUser($subtask['task_id'], $this->currentUser['user_id']);
            $isSameClan = $this->userClan && ((int)$subtask['clan_id'] === (int)$this->userClan['clan_id']);

            if (!$isAssignedToTask && !$isSameClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para desasignar usuarios de esta subtarea'], 403);
            }

            // Inicializar modelo de asignaciones de subtareas
            if (!isset($this->subtaskAssignmentModel)) {
                require_once __DIR__ . '/../models/SubtaskAssignment.php';
                $this->subtaskAssignmentModel = new SubtaskAssignment();
            }

            // Obtener información del usuario para el historial
            $user = $this->userModel->findById($userId);
            if (!$user) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }

            // Desasignar usuario
            $result = $this->subtaskAssignmentModel->removeUser($subtaskId, $userId);

            if ($result) {
                // Registrar en el historial de la tarea padre
                $historyStmt = $this->db->prepare("
                    INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                    VALUES (?, ?, 'unassigned', 'subtask_assignment', ?, '', ?)
                ");
                $historyStmt->execute([
                    $subtask['task_id'],
                    $this->currentUser['user_id'],
                    $user['full_name'] ?? $user['username'],
                    'Subtarea "' . $subtask['title'] . '" - usuario desasignado: ' . ($user['full_name'] ?? $user['username'])
                ]);

                Utils::jsonResponse([
                    'success' => true,
                    'message' => 'Usuario desasignado exitosamente'
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al desasignar usuario'], 500);
            }

        } catch (Exception $e) {
            error_log("Error en unassignSubtaskUser (ClanMember): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener miembros del clan para asignación de subtareas
     */
    public function getClanMembers() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        try {
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no pertenece a ningún clan'], 400);
            }

            // Inicializar modelo de clan si no existe
            if (!isset($this->clanModel)) {
                require_once __DIR__ . '/../models/Clan.php';
                $this->clanModel = new Clan();
            }

            $members = $this->clanModel->getMembers($this->userClan['clan_id']);

            Utils::jsonResponse([
                'success' => true,
                'members' => $members
            ]);

        } catch (Exception $e) {
            error_log("Error en getClanMembers (ClanMember): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
}

?>


