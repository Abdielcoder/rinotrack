<?php

class ClanMemberController {
    private $auth;
    private $userModel;
    private $projectModel;
    private $clanModel;
    private $roleModel;
    private $kpiModel;
    private $taskModel;
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

        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            $data = [
                'currentPage' => 'clan_member',
                'user' => $this->currentUser,
                'clan' => ['clan_name' => 'Sin clan asignado', 'clan_departamento' => '-', 'clan_id' => null],
                'userTaskStats' => ['total_tasks' => 0, 'completed_tasks' => 0, 'completion_percentage' => 0],
                'ownContribution' => []
            ];
            $this->loadView('clan_member/dashboard', $data);
            return;
        }

        // Solo estadísticas del usuario
        $userTaskStats = $this->getUserTaskStats($this->currentUser['user_id'], $this->userClan['clan_id']);
        $ownContribution = $this->getOwnContribution($this->currentUser, $userTaskStats);
        $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
        $ownTasksDetails = $this->getUserTasksForModal($this->currentUser['user_id'], $this->userClan['clan_id']);
        
        // Obtener tareas para el tablero Kanban
        $kanbanTasks = $this->getKanbanTasks($this->currentUser['user_id'], $this->userClan['clan_id']);

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'userTaskStats' => $userTaskStats,
            'ownContribution' => $ownContribution,
            'projects' => $projects,
            'ownContributionDetails' => $ownTasksDetails,
            'kanbanTasks' => $kanbanTasks
        ];
        $this->loadView('clan_member/dashboard', $data);
    }

    public function projects() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $projects = $this->userClan ? $this->projectModel->getByClan($this->userClan['clan_id']) : [];
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
        if (!$project || (int)$project['clan_id'] !== (int)$this->userClan['clan_id']) {
            die('Acceso denegado al proyecto');
        }
        $tasks = $this->taskModel->getByProject($projectId);
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
        if (!$project || ((int)$project['clan_id'] !== (int)$this->userClan['clan_id'] && !$isAssigned)) { die('Acceso denegado'); }

        $subtasks = $this->taskModel->getSubtasks($taskId);
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

        // Tabla: SOLO tareas donde el usuario esté asignado
        // - Proyectos del clan: solo tareas asignadas al usuario
        // - Proyectos lógicos: solo sus propias tareas
        $clanPart = $this->userClan ? $this->taskModel->getUserTasks($this->currentUser['user_id'], $page, $perPage, $search, $status) : ['tasks' => [], 'total' => 0, 'page' => 1, 'per_page' => $perPage, 'total_pages' => 0];
        $ownLogical = $this->taskModel->getUserTasksByProjectNames($this->currentUser['user_id'], ['Tareas Recurrentes','Tareas Eventuales']);
        $merged = [];
        foreach ($clanPart['tasks'] as $t) { $merged[$t['task_id']] = $t; }
        foreach ($ownLogical as $t) { $merged[$t['task_id']] = $t; }
        $result = $clanPart;
        $result['tasks'] = array_values($merged);

        // Resumen de proyectos: SOLO proyectos donde el usuario esté asignado a tareas
        $projectsSummary = [];
        if ($this->userClan) {
            // Solo proyectos del clan donde el usuario tenga tareas asignadas
            $clanProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
            foreach ($clanProjects as $p) {
                $pid = (int)$p['project_id'];
                // Verificar si el usuario tiene tareas asignadas en este proyecto
                $userTasksInProject = $this->taskModel->getUserTasksByProject($this->currentUser['user_id'], $pid);
                if (!empty($userTasksInProject)) {
                    $projectTasks = $this->taskModel->getByProject($pid);
                    $total = count($projectTasks);
                    $completed = 0;
                    foreach ($projectTasks as $t) {
                        if (($t['status'] ?? '') === 'completed' || ($t['is_completed'] ?? 0) == 1) { $completed++; }
                    }
                    $progress = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                    $projectsSummary[] = [
                        'project_id' => $pid,
                        'project_name' => $p['project_name'],
                        'status' => $p['status'],
                        'total_tasks' => $total,
                        'completed_tasks' => $completed,
                        'progress_percentage' => $progress
                    ];
                }
            }
            // Agregar proyectos lógicos donde el usuario tenga tareas asignadas
            $tasks = $result['tasks'] ?? [];
            $byProject = [];
            foreach ($tasks as $t) {
                $pid = (int)$t['project_id'];
                if (!isset($byProject[$pid])) {
                    $byProject[$pid] = [
                        'project_id' => $pid,
                        'project_name' => $t['project_name'],
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
        }

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'tasksData' => $result,
            'search' => $search,
            'status' => $status,
            'perPage' => $perPage,
            'projectsSummary' => $projectsSummary
        ];
        $this->loadView('clan_member/tasks', $data);
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }

        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        // Verificar clan o asignación
        $project = $this->projectModel->findById($task['project_id']);
        $isAssignedToUser = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        if (!$project || ($project['clan_id'] != $this->userClan['clan_id'] && !$isAssignedToUser)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        // Solo permitir actualizar si está asignado, es dueño (assigned_to_user_id) o creador
        $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
        $isOwner = (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
        $isCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
        if (!($isAssigned || $isOwner || $isCreator)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes actualizar tareas que te corresponden'], 403);
        }

        $taskName = Utils::sanitizeInput($_POST['task_name'] ?? null);
        $description = Utils::sanitizeInput($_POST['description'] ?? null);
        $priority = $_POST['priority'] ?? null;
        $dueDate = $_POST['due_date'] ?? null;
        $status = $_POST['status'] ?? null;

        // No permitir cambios de asignación ni porcentajes desde el rol miembro
        $assignedUserId = null;
        $assignedPercentage = null;

        $ok = $this->taskModel->update($taskId, $taskName, $description, $assignedUserId, $priority, $dueDate, $assignedPercentage, $status);
        if ($ok) {
            Utils::jsonResponse(['success' => true, 'message' => 'Tarea actualizada']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'No se pudo actualizar la tarea'], 500);
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); }
        $this->requireAuth();
        if (!$this->hasMemberAccess()) { Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); }
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        if ($username==='' || $email==='' || $fullName==='') { Utils::jsonResponse(['success'=>false,'message'=>'Campos requeridos'],400); }
        $ok = $this->userModel->update($this->currentUser['user_id'], $username, $email, $fullName, 1);
        if ($ok) {
            // refrescar sesión en memoria
            $_SESSION['username'] = $username; $_SESSION['email'] = $email; $_SESSION['full_name'] = $fullName;
            Utils::jsonResponse(['success'=>true,'message'=>'Perfil actualizado']);
        }
        Utils::jsonResponse(['success'=>false,'message'=>'No se pudo actualizar']);
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
        // Mínimo rol: usuario normal
        return $this->roleModel->userHasMinimumRole($this->currentUser['user_id'], Role::USUARIO_NORMAL);
    }

    private function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
    }

    private function getKanbanTasks($userId, $clanId) {
        try {
            // Obtener tareas del clan
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
                       AND t.is_subtask = 0
                       AND t.status != 'completed'
                       AND (t.assigned_to_user_id = ? OR ta.user_id = ?)
                     GROUP BY t.task_id
                     ORDER BY t.due_date ASC"
                );
                $stmt->execute([$clanId, $userId, $userId]);
                $clanTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Obtener tareas personales
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
                    'Tarea Personal' as project_name,
                    NULL as project_id,
                    DATEDIFF(t.due_date, CURDATE()) as days_until_due
                 FROM Tasks t
                 WHERE t.is_personal = 1
                   AND t.assigned_to_user_id = ?
                   AND t.status != 'completed'
                   AND t.is_subtask = 0
                 ORDER BY t.due_date ASC"
            );
            $stmt->execute([$userId]);
            $personalTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Combinar ambas listas
            $allTasks = array_merge($clanTasks, $personalTasks);

            // Organizar tareas por columnas del Kanban
            $kanbanColumns = [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];

            foreach ($allTasks as $task) {
                $daysUntilDue = (int)($task['days_until_due'] ?? 0);
                
                if ($daysUntilDue < 0) {
                    $kanbanColumns['vencidas'][] = $task;
                } elseif ($daysUntilDue === 0) {
                    $kanbanColumns['hoy'][] = $task;
                } elseif ($daysUntilDue <= 7) {
                    $kanbanColumns['1_semana'][] = $task;
                } elseif ($daysUntilDue <= 14) {
                    $kanbanColumns['2_semanas'][] = $task;
                }
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

            // Log de datos recibidos
            error_log("Datos procesados: task_name=$taskName, due_date=$dueDate, user_id=$userId");

            // Validaciones
            if (empty($taskName)) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la tarea es requerido']);
                return;
            }

            if (empty($dueDate)) {
                echo json_encode(['success' => false, 'message' => 'La fecha de vencimiento es requerida']);
                return;
            }

            if ($userId !== (int)$this->currentUser['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
                return;
            }

            // Crear la tarea personal con solo campos básicos
            $taskData = [
                'task_name' => $taskName,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $dueDate,
                'status' => $status,
                'assigned_to_user_id' => $userId,
                'created_by' => $userId,
                'completion_percentage' => $status === 'completed' ? 100 : 0
            ];

            error_log('Task data a crear: ' . print_r($taskData, true));

            // Intentar primero con el método simplificado
            $taskId = $this->taskModel->createPersonalTaskSimple($taskData);
            
            if (!$taskId) {
                error_log('Método simple falló, intentando método completo');
                $taskId = $this->taskModel->createPersonalTask($taskData);
            }

            if ($taskId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Tarea personal creada exitosamente',
                    'task_id' => $taskId
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

            // Crear el proyecto personal
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

    public function testDatabaseConnection() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        try {
            $db = Database::getConnection();
            
            // Test 1: Verificar conexión
            $stmt = $db->prepare("SELECT 1 as test");
            $stmt->execute();
            $test1 = $stmt->fetch();
            
            // Test 2: Verificar que el usuario existe
            $stmt = $db->prepare("SELECT user_id, username, full_name FROM Users WHERE user_id = ?");
            $stmt->execute([$this->currentUser['user_id']]);
            $user = $stmt->fetch();
            
            // Test 3: Verificar que el usuario pertenece a un clan
            $stmt = $db->prepare("
                SELECT cm.clan_id, c.clan_name 
                FROM Clan_Members cm 
                JOIN Clans c ON c.clan_id = cm.clan_id 
                WHERE cm.user_id = ?
            ");
            $stmt->execute([$this->currentUser['user_id']]);
            $clan = $stmt->fetch();
            
            // Test 4: Verificar estructura de la tabla Projects
            $stmt = $db->prepare("DESCRIBE Projects");
            $stmt->execute();
            $projectColumns = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'message' => 'Test de base de datos completado',
                'data' => [
                    'connection_test' => $test1 ? 'OK' : 'FAIL',
                    'user_exists' => $user ? 'SÍ' : 'NO',
                    'user_data' => $user,
                    'user_has_clan' => $clan ? 'SÍ' : 'NO',
                    'clan_data' => $clan,
                    'projects_table_columns' => array_column($projectColumns, 'Field')
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error en test de base de datos: ' . $e->getMessage()
            ]);
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
}

?>


