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
                'teamProgress' => ['total_tasks' => 0, 'completed_tasks' => 0, 'completion_percentage' => 0],
                'memberContributions' => [],
                'projects' => []
            ];
            $this->loadView('clan_member/dashboard', $data);
            return;
        }

        $teamProgress = $this->getTeamProgress($this->userClan['clan_id']);
        $memberContributions = $this->getMemberContributions($this->userClan['clan_id']);
        $projects = $this->projectModel->getByClan($this->userClan['clan_id']);

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'teamProgress' => $teamProgress,
            'memberContributions' => $memberContributions,
            'projects' => $projects
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

    public function tasks() {
        $this->requireAuth();
        if (!$this->hasMemberAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 10);
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $result = $this->userClan ? $this->taskModel->getAllTasksByClan($this->userClan['clan_id'], $page, $perPage, $search, $status) : ['tasks' => [], 'total' => 0, 'page' => 1, 'per_page' => $perPage, 'total_pages' => 0];

        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'tasksData' => $result,
            'search' => $search,
            'status' => $status
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

        // Verificar que la tarea sea del clan del usuario
        $project = $this->projectModel->findById($task['project_id']);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
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

        // Verificar clan
        $project = $this->projectModel->findById($task['project_id']);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        // Solo permitir actualizar tareas propias
        if (!$this->isTaskAssignedToUser($taskId, $this->currentUser['user_id'])) {
            Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes actualizar tus tareas'], 403);
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

        // Verificar que la tarea sea del clan del usuario
        $project = $this->projectModel->findById($task['project_id']);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $result = $this->taskModel->addComment($taskId, $this->currentUser['user_id'], $commentText, 'comment');
        if ($result) {
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/task_attachments/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = time() . '_' . $_FILES['attachment']['name'];
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $filePath)) {
                    $this->taskModel->addCommentAttachment((int)$result, $fileName, $filePath);
                }
            }
            Utils::jsonResponse(['success' => true, 'message' => 'Comentario agregado']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar comentario'], 500);
        }
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
        }
        $data = [
            'currentPage' => 'clan_member',
            'user' => $this->currentUser,
            'clan' => $this->userClan,
            'currentKPI' => $currentKPI,
            'projects' => $projects,
            'clanTotalPoints' => $clanTotalPoints,
            'clanCompletedPoints' => $clanCompletedPoints
        ];
        $this->loadView('clan_member/kpi_dashboard', $data);
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


