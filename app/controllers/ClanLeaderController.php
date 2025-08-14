<?php

class ClanLeaderController {
    private $auth;
    private $userModel;
    private $projectModel;
    private $clanModel;
    private $roleModel;
    private $kpiModel;
    private $taskModel;
    private $currentUser;
    private $userClan;
    private $db;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->clanModel = new Clan();
        $this->roleModel = new Role();
        $this->kpiModel = new KPI();
        // Debug: verificar si la clase Task existe
        if (!class_exists('Task')) {
            error_log("ERROR: La clase Task no existe");
            die("Error: La clase Task no se pudo cargar");
        }
        $this->taskModel = new Task();
        $this->db = Database::getConnection();
        
        // Limpiar cualquier transacción activa al inicializar
        if ($this->db->inTransaction()) {
            $this->db->rollback();
        }
        
        // Obtener usuario actual (sin verificar autenticación inmediatamente)
        $this->currentUser = $this->auth->getCurrentUser();
        
        // Solo inicializar clan si hay usuario autenticado
        if ($this->currentUser) {
            // Verificar que es líder de clan
            if (!$this->hasClanLeaderAccess()) {
                // No redirigir aquí, dejar que cada método maneje su propia verificación
                $this->userClan = null;
            } else {
                // Obtener el clan del usuario
                $this->userClan = $this->userModel->getUserClan($this->currentUser['user_id']);
            }
        } else {
            $this->userClan = null;
        }
    }
    
    /**
     * Dashboard principal del líder de clan
     */
    public function index() {
        // Requerir autenticación
        $this->requireAuth();

        // Verificar permisos mínimos
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
            return;
        }

        // Si el usuario no tiene clan asignado, mostrar dashboard vacío seguro
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            $emptyStats = [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'pending_tasks' => 0,
                'in_progress_tasks' => 0,
                'completion_percentage' => 0
            ];

            $data = [
                'userStats' => ['total_members' => 0, 'active_members' => 0, 'recent_members' => 0],
                'projectStats' => ['total_projects' => 0, 'active_projects' => 0, 'kpi_projects' => 0],
                'clanStats' => ['member_count' => 0, 'project_count' => 0, 'active_projects' => 0, 'completed_projects' => 0],
                'taskStats' => $emptyStats,
                'memberContributions' => [],
                'clanIcon' => $this->getClanIcon(''),
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => ['clan_name' => 'Sin clan asignado', 'clan_departamento' => '-', 'clan_id' => null]
            ];
            $this->loadView('clan_leader/dashboard', $data);
            return;
        }

        $taskStats = $this->getTaskStats();
        $memberContributions = $this->getMemberContributions();

        // Debug seguro
        error_log("Clan Leader Dashboard Debug:");
        error_log("Clan ID: " . $this->userClan['clan_id']);
        error_log("Task Stats: " . json_encode($taskStats));
        error_log("Member Contributions Count: " . count($memberContributions));

        $data = [
            'userStats' => $this->getUserStats(),
            'projectStats' => $this->projectModel->getStatsByClan($this->userClan['clan_id']),
            'clanStats' => $this->getClanStats(),
            'taskStats' => $taskStats,
            'memberContributions' => $memberContributions,
            'clanIcon' => $this->getClanIcon($this->userClan['clan_name'] ?? ''),
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];

        $this->loadView('clan_leader/dashboard', $data);
    }
    
    /**
     * Gestión de miembros del clan
     */
    public function members() {
        // Asegurar autenticación y permisos
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
            return;
        }

        $search = $_GET['search'] ?? '';
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            $members = [];
        } else {
            $members = empty($search)
                ? $this->clanModel->getMembers($this->userClan['clan_id'])
                : $this->searchMembers($search);
        }
        
        $data = [
            'members' => $members,
            'search' => $search,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];
        
        $this->loadView('clan_leader/members', $data);
    }

    /**
     * Dashboard de disponibilidad de colaboradores
     */
    public function availability() {
        // Asegurar autenticación y permisos
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
            return;
        }

        $availabilityData = $this->getCollaboratorAvailability();
        
        $data = [
            'availabilityData' => $availabilityData,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];
        
        $this->loadView('clan_leader/availability', $data);
    }
    
    /**
     * Agregar miembro al clan
     */
    public function addMember() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                // Responder vacío para evitar errores en UI
                Utils::jsonResponse(['success' => true, 'users' => []]);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            $userId = (int)($_POST['userId'] ?? 0);
            
            if ($userId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de usuario inválido'], 400);
                return;
            }
            
            // Verificar que el usuario existe
            $user = $this->userModel->findById($userId);
            if (!$user) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                return;
            }
            
            // Verificar que el usuario no esté ya en el clan
            $existingMembers = $this->clanModel->getMembers($this->userClan['clan_id']);
            foreach ($existingMembers as $member) {
                if ($member['user_id'] == $userId) {
                    Utils::jsonResponse(['success' => false, 'message' => 'El usuario ya es miembro del clan'], 409);
                    return;
                }
            }
            
            // Agregar miembro al clan
            $result = $this->clanModel->addMember($this->userClan['clan_id'], $userId);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Miembro agregado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar miembro'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::addMember: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener miembros del clan para asignar tareas
     */
    public function getClanMembers() {
        try {
            $members = $this->clanModel->getMembers($this->userClan['clan_id']);
            
            Utils::jsonResponse([
                'success' => true,
                'members' => $members
            ]);
        } catch (Exception $e) {
            error_log("Error al obtener miembros del clan: " . $e->getMessage());
            Utils::jsonResponse([
                'success' => false,
                'message' => 'Error al obtener miembros del clan'
            ], 500);
        }
    }
    
    /**
     * Obtener usuarios disponibles para agregar al clan
     */
    public function getAvailableUsers() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Obtener todos los usuarios que no están en el clan
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.is_active
                FROM Users u
                WHERE u.is_active = 1 
                AND u.user_id NOT IN (
                    SELECT cm.user_id 
                    FROM Clan_Members cm 
                    WHERE cm.clan_id = ?
                )
                ORDER BY u.full_name
            ");
            $stmt->execute([$this->userClan['clan_id']]);
            $users = $stmt->fetchAll();
            
            Utils::jsonResponse([
                'success' => true,
                'users' => $users
            ]);
        } catch (Exception $e) {
            error_log("Error al obtener usuarios disponibles: " . $e->getMessage());
            Utils::jsonResponse([
                'success' => false,
                'message' => 'Error al obtener usuarios disponibles'
            ], 500);
        }
    }
    
    /**
     * Remover miembro del clan
     */
    public function removeMember() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            $userId = (int)($_POST['userId'] ?? 0);
            
            if ($userId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de usuario inválido'], 400);
                return;
            }
            
            // No permitir remover al líder del clan
            if ($userId === $this->currentUser['user_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No puedes removerte a ti mismo del clan'], 400);
                return;
            }
            
            // Verificar que el usuario existe y está en el clan
            $existingMembers = $this->clanModel->getMembers($this->userClan['clan_id']);
            $userInClan = false;
            foreach ($existingMembers as $member) {
                if ($member['user_id'] == $userId) {
                    $userInClan = true;
                    break;
                }
            }
            
            if (!$userInClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'El usuario no es miembro del clan'], 404);
                return;
            }
            
            // Remover miembro del clan
            $result = $this->clanModel->removeMember($this->userClan['clan_id'], $userId);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Miembro removido exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al remover miembro'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::removeMember: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Gestión de proyectos del clan
     */
    public function projects() {
        $search = $_GET['search'] ?? '';
        $projects = empty($search) ? 
            $this->projectModel->getByClan($this->userClan['clan_id']) : 
            $this->searchProjects($search);
        
        $data = [
            'projects' => $projects,
            'search' => $search,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];
        
        $this->loadView('clan_leader/projects', $data);
    }
    
    /**
     * Crear nuevo proyecto
     */
    public function createProject() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            // Validar datos
            $projectName = Utils::sanitizeInput($_POST['projectName'] ?? '');
            $description = Utils::sanitizeInput($_POST['description'] ?? '');
            
            // Validaciones
            if (empty($projectName) || strlen($projectName) < 3) {
                Utils::jsonResponse(['success' => false, 'message' => 'El nombre del proyecto debe tener al menos 3 caracteres'], 400);
                return;
            }
            
            if (empty($description)) {
                Utils::jsonResponse(['success' => false, 'message' => 'La descripción es requerida'], 400);
                return;
            }
            
            // Crear proyecto
            $result = $this->projectModel->create(
                $projectName, 
                $description, 
                $this->userClan['clan_id'], 
                $this->currentUser['user_id']
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Proyecto creado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear proyecto'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::createProject: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Actualizar proyecto
     */
    public function updateProject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('clan_leader/projects');
        }
        
        $projectId = (int)($_POST['projectId'] ?? 0);
        $projectName = Utils::sanitizeInput($_POST['projectName'] ?? '');
        $description = Utils::sanitizeInput($_POST['description'] ?? '');
        
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
        }
        
        // Verificar que el proyecto pertenece al clan
        $project = $this->projectModel->findById($projectId);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        
        // Actualizar proyecto
        $result = $this->projectModel->update($projectId, $projectName, $description, $this->userClan['clan_id']);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Proyecto actualizado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar proyecto'], 500);
        }
    }
    
    /**
     * Eliminar proyecto
     */
    public function deleteProject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('clan_leader/projects');
        }
        
        $projectId = (int)($_POST['projectId'] ?? 0);
        
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
        }
        
        // Verificar que el proyecto pertenece al clan
        $project = $this->projectModel->findById($projectId);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        
        // Eliminar proyecto
        $result = $this->projectModel->delete($projectId);
        
        if ($result === true) {
            Utils::jsonResponse(['success' => true, 'message' => 'Proyecto eliminado exitosamente']);
        } else {
            $message = is_array($result) ? $result['error'] : 'Error al eliminar proyecto';
            Utils::jsonResponse(['success' => false, 'message' => $message], 500);
        }
    }
    
    /**
     * Dashboard KPI del clan
     */
    public function kpiDashboard() {
        // Obtener KPI actual
        $currentKPI = $this->kpiModel->getCurrentQuarter();
        
        // Debug: Log del KPI actual
        error_log("KPI Dashboard Debug - Current KPI: " . json_encode($currentKPI));
        
        // Cada clan tiene un límite de 1000 puntos trimestrales
        $clanTotalPoints = 1000;
        
        // Obtener proyectos del clan con KPI
        $projects = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id'] ?? null);
        $clanProjects = array_filter($projects, function($project) {
            return $project['clan_id'] == $this->userClan['clan_id'];
        });
        
        // Obtener proyectos sin KPI del clan
        $projectsWithoutKPI = $this->projectModel->getProjectsWithoutKPIByClan($this->userClan['clan_id']);
        
        // Calcular puntos asignados del clan
        $assignedPoints = array_sum(array_column($clanProjects, 'kpi_points'));
        
        // Datos para el snake path de miembros
        $snakePathData = $this->getSnakePathData($currentKPI);
        
        // Debug: Log de los datos del snake path
        error_log("KPI Dashboard Debug - Snake Path Data: " . json_encode($snakePathData));
        error_log("KPI Dashboard Debug - Members Count: " . count($snakePathData['members_data'] ?? []));
        
        $data = [
            'currentKPI' => $currentKPI,
            'clanTotalPoints' => $clanTotalPoints,
            'clanAssignedPoints' => $assignedPoints,
            'clanAvailablePoints' => $clanTotalPoints - $assignedPoints,
            'projects' => $clanProjects,
            'projectsWithoutKPI' => $projectsWithoutKPI,
            'snakePathData' => $snakePathData,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];
        
        $this->loadView('clan_leader/kpi_dashboard', $data);
    }
    
    /**
     * Asignar KPI a proyecto
     */
    public function assignKPI() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $projectId = (int)($_POST['projectId'] ?? 0);
        $kpiPoints = (int)($_POST['kpiPoints'] ?? 0);
        
        if ($projectId <= 0 || $kpiPoints <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        // Verificar que el proyecto pertenece al clan
        $project = $this->projectModel->findById($projectId);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        
        // Obtener KPI actual
        $currentKPI = $this->kpiModel->getCurrentQuarter();
        if (!$currentKPI) {
            Utils::jsonResponse(['success' => false, 'message' => 'No hay trimestre KPI activo'], 400);
        }
        
        // Verificar límite de puntos del clan (1000 puntos por clan)
        $clanTotalPoints = 1000;
        $projects = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id']);
        $clanProjects = array_filter($projects, function($project) {
            return $project['clan_id'] == $this->userClan['clan_id'];
        });
        
        $assignedPoints = array_sum(array_column($clanProjects, 'kpi_points'));
        $availablePoints = $clanTotalPoints - $assignedPoints;
        
        if ($kpiPoints > $availablePoints) {
            Utils::jsonResponse([
                'success' => false, 
                'message' => "No puedes asignar más de {$availablePoints} puntos. El clan tiene un límite de {$clanTotalPoints} puntos trimestrales."
            ], 400);
        }
        
        // Asignar KPI
        $result = $this->projectModel->assignKPI($projectId, $currentKPI['kpi_quarter_id'], $kpiPoints);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'KPI asignado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al asignar KPI'], 500);
        }
    }
    
    /**
     * Obtener puntos disponibles para el clan
     */
    public function getAvailablePoints() {
        $currentKPI = $this->kpiModel->getCurrentQuarter();
        if (!$currentKPI) {
            Utils::jsonResponse(['success' => false, 'message' => 'No hay trimestre KPI activo'], 400);
        }
        
        // Cada clan tiene un límite de 1000 puntos trimestrales
        $clanTotalPoints = 1000;
        
        // Obtener proyectos del clan con KPI asignado
        $projects = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id']);
        $clanProjects = array_filter($projects, function($project) {
            return $project['clan_id'] == $this->userClan['clan_id'];
        });
        
        $assignedPoints = array_sum(array_column($clanProjects, 'kpi_points'));
        $availablePoints = $clanTotalPoints - $assignedPoints;
        
        Utils::jsonResponse([
            'success' => true,
            'total_points' => $clanTotalPoints,
            'assigned_points' => $assignedPoints,
            'available_points' => max(0, $availablePoints)
        ]);
    }
    
    /**
     * Gestión de tareas del clan
     */
    public function tasks() {
        $projectId = $_GET['project_id'] ?? null;
        $action = $_GET['action'] ?? null;
        
        if ($action === 'create') {
            // Mostrar formulario de creación de tareas
            $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
            $members = $this->clanModel->getMembers($this->userClan['clan_id']);
            
            // Obtener tareas del trimestre actual sin completar
            $currentQuarterTasks = $this->taskModel->getCurrentQuarterTasksByClan($this->userClan['clan_id']);
            
            // Obtener el proyecto pre-seleccionado si viene de un proyecto específico
            $selectedProjectId = $_GET['project_id'] ?? null;
            
            $data = [
                'projects' => $projects,
                'members' => $members,
                'currentQuarterTasks' => $currentQuarterTasks,
                'selectedProjectId' => $selectedProjectId,
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => $this->userClan
            ];
            
            $this->loadView('clan_leader/task_management', $data);
        } elseif ($action === 'edit') {
            // Mostrar formulario de edición de tareas
            $taskId = (int)($_GET['task_id'] ?? 0);
            
            if ($taskId <= 0) {
                Utils::redirect('clan_leader/tasks');
            }
            
            // Obtener la tarea
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::redirect('clan_leader/tasks');
            }
            
            // Verificar que la tarea pertenece a un proyecto del clan
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::redirect('clan_leader/tasks');
            }
            
            // Obtener datos necesarios para el formulario
            $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
            $members = $this->clanModel->getMembers($this->userClan['clan_id']);
            
            // Obtener usuarios asignados a la tarea
            $assignedUsers = $this->taskModel->getAssignedUsers($taskId);
            
            $data = [
                'task' => $task,
                'project' => $project,
                'projects' => $projects,
                'members' => $members,
                'assignedUsers' => $assignedUsers,
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => $this->userClan
            ];
            
            $this->loadView('clan_leader/task_edit', $data);
        } elseif ($projectId) {
            // Verificar que el proyecto pertenece al clan
            $project = $this->projectModel->findById($projectId);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::redirect('clan_leader/tasks');
            }
            
            $tasks = $this->taskModel->getByProject($projectId);
            $data = [
                'project' => $project,
                'tasks' => $tasks,
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => $this->userClan
            ];
            
            $this->loadView('clan_leader/project_tasks', $data);
        } else {
            // Lista de proyectos del clan con estadísticas de tareas
            $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
            
            // Calcular estadísticas de tareas para cada proyecto
            foreach ($projects as &$project) {
                $projectTasks = $this->taskModel->getByProject($project['project_id']);
                $totalTasks = count($projectTasks);
                $completedTasks = 0;
                
                foreach ($projectTasks as $task) {
                    if ($task['status'] === 'completed') {
                        $completedTasks++;
                    }
                }
                
                $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
                
                $project['total_tasks'] = $totalTasks;
                $project['completed_tasks'] = $completedTasks;
                $project['progress_percentage'] = $progressPercentage;
            }
            
            // Obtener parámetros de paginación, búsqueda y filtros
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 5);
            $search = $_GET['search'] ?? '';
            $statusFilter = $_GET['status_filter'] ?? '';
            
            // Validar perPage para evitar valores muy altos
            $perPage = max(1, min($perPage, 100));
            
            // Obtener todas las tareas del clan con paginación dinámica
            $allTasksData = $this->taskModel->getAllTasksByClan($this->userClan['clan_id'], $page, $perPage, $search, $statusFilter);
            
            $data = [
                'projects' => $projects,
                'allTasks' => $allTasksData['tasks'],
                'pagination' => [
                    'current_page' => $allTasksData['page'],
                    'total_pages' => $allTasksData['total_pages'],
                    'total_records' => $allTasksData['total'],
                    'per_page' => $allTasksData['per_page']
                ],
                'search' => $search,
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => $this->userClan
            ];
            
            $this->loadView('clan_leader/tasks', $data);
        }
    }
    
    /**
     * Actualizar tarea
     */
    public function updateTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        try {
            $taskId = (int)($_POST['task_id'] ?? 0);
            $taskName = Utils::sanitizeInput($_POST['task_name'] ?? '');
            $taskDescription = Utils::sanitizeInput($_POST['task_description'] ?? '');
            $taskProject = (int)($_POST['task_project'] ?? 0);
            $taskDueDate = $_POST['task_due_date'] ?? '';
            
            // Si la fecha está vacía, establecer como null
            if (empty($taskDueDate)) {
                $taskDueDate = null;
            }
            
            $priority = Utils::sanitizeInput($_POST['priority'] ?? 'medium');
            
            // Corregir valor de prioridad si es 'urgent' por 'critical'
            if ($priority === 'urgent') {
                $priority = 'critical';
            }
            
            $taskStatus = Utils::sanitizeInput($_POST['task_status'] ?? 'pending');
            $assignedToUserId = (int)($_POST['assigned_to_user_id'] ?? 0);
            
            // Log para debugging
            error_log("=== INICIO ACTUALIZACIÓN TAREA ===");
            error_log("taskId: " . $taskId);
            error_log("taskName: " . $taskName);
            error_log("taskProject: " . $taskProject);
            error_log("assignedToUserId: " . $assignedToUserId);
            error_log("priority: " . $priority);
            error_log("dueDate: " . ($taskDueDate ?? 'NULL'));
            
            if ($taskId <= 0 || empty($taskName) || $taskProject <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
            }
            
            // Verificar que la tarea existe y pertenece al clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                error_log("Tarea no encontrada: " . $taskId);
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            error_log("Tarea encontrada: " . json_encode($task));
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                error_log("Tarea no pertenece al clan: " . $taskId);
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            // Verificar que el nuevo proyecto pertenece al clan
            $newProject = $this->projectModel->findById($taskProject);
            if (!$newProject || $newProject['clan_id'] != $this->userClan['clan_id']) {
                error_log("Proyecto no válido: " . $taskProject);
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no válido'], 400);
            }
            
            // Verificar que el usuario asignado pertenece al clan
            if ($assignedToUserId > 0) {
                $assignedUser = $this->userModel->findById($assignedToUserId);
                if (!$assignedUser) {
                    error_log("Usuario asignado no encontrado: " . $assignedToUserId);
                    Utils::jsonResponse(['success' => false, 'message' => 'Usuario asignado no encontrado'], 404);
                }
                
                $userClan = $this->userModel->getUserClan($assignedToUserId);
                if (!$userClan || $userClan['clan_id'] != $this->userClan['clan_id']) {
                    error_log("Usuario no pertenece al clan: " . $assignedToUserId);
                    Utils::jsonResponse(['success' => false, 'message' => 'El usuario asignado no pertenece al clan'], 400);
                }
            }
            
            // Actualizar tarea
            error_log("Llamando a taskModel->update() con parámetros:");
            error_log("taskId: " . $taskId);
            error_log("taskName: " . $taskName);
            error_log("taskDescription: " . $taskDescription);
            error_log("assignedUserId: " . ($assignedToUserId > 0 ? $assignedToUserId : 'NULL'));
            error_log("priority: " . $priority);
            error_log("dueDate: " . ($taskDueDate ?? 'NULL'));
            
            $result = $this->taskModel->update(
                $taskId, 
                $taskName, 
                $taskDescription, 
                $assignedToUserId > 0 ? $assignedToUserId : null, 
                $priority, 
                $taskDueDate,
                null, // assigned_percentage
                $taskStatus // nuevo parámetro para estado
            );
            
            error_log("Resultado de actualización: " . ($result ? 'true' : 'false'));
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Tarea actualizada exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar tarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en updateTask: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Crear tarea con múltiples usuarios y subtareas
     */
    public function createTask() {
        error_log('=== ClanLeaderController::createTask - INICIO ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('FILES data: ' . print_r($_FILES, true));
        error_log('SESSION user_id: ' . ($_SESSION['user_id'] ?? 'NO SESSION'));
        error_log('Auth logged in: ' . ($this->auth->isLoggedIn() ? 'YES' : 'NO'));
        
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                error_log('createTask - Error: No autenticado');
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                error_log('createTask - Error: Sin permisos de líder de clan');
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                error_log('createTask - Error: No tiene clan asignado');
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log('createTask - Error: Método no POST');
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            // Limpiar cualquier transacción activa antes de empezar
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
        
        // Logs detallados de los datos recibidos
        error_log('createTask - Datos recibidos:');
        error_log('  task_title: ' . ($_POST['task_title'] ?? 'NOT SET'));
        error_log('  task_due_date: ' . ($_POST['task_due_date'] ?? 'NOT SET'));
        error_log('  task_project: ' . ($_POST['task_project'] ?? 'NOT SET'));
        error_log('  priority: ' . ($_POST['priority'] ?? 'NOT SET'));
        error_log('  assigned_members: ' . print_r($_POST['assigned_members'] ?? 'NOT SET', true));
        error_log('  subtasks: ' . print_r($_POST['subtasks'] ?? 'NOT SET', true));
        
        $taskTitle = Utils::sanitizeInput($_POST['task_title'] ?? '');
        $taskDueDate = $_POST['task_due_date'] ?? '';
        
        // Si la fecha está vacía, establecer como null
        if (empty($taskDueDate)) {
            $taskDueDate = null;
        }
        
        $taskProject = (int)($_POST['task_project'] ?? 0);
        $taskDescription = Utils::sanitizeInput($_POST['task_description'] ?? '');
        // Manejar assigned_members que puede ser un array (desde formulario) o JSON string
        $assignedMembersRaw = $_POST['assigned_members'] ?? [];
        if (is_string($assignedMembersRaw)) {
            $assignedMembers = json_decode($assignedMembersRaw, true) ?: [];
        } else {
            $assignedMembers = $assignedMembersRaw;
        }
        // Manejar subtasks que puede ser un array o JSON string y normalizar datos
        $subtasksRaw = $_POST['subtasks'] ?? [];
        if (is_string($subtasksRaw)) {
            $subtasks = json_decode($subtasksRaw, true) ?: [];
        } else {
            $subtasks = $subtasksRaw;
        }
        if (!empty($subtasks) && is_array($subtasks)) {
            $normalized = [];
            foreach ($subtasks as $st) {
                $title = trim($st['title'] ?? '');
                if ($title === '') { continue; }
                $desc = trim($st['description'] ?? '');
                $perc = isset($st['percentage']) && $st['percentage'] !== '' ? (float)$st['percentage'] : 0.0;
                $due  = isset($st['due_date']) && trim((string)$st['due_date']) !== '' ? trim($st['due_date']) : null;
                $prio = in_array(($st['priority'] ?? 'medium'), ['low','medium','high','urgent'], true) ? $st['priority'] : 'medium';
                $auid = isset($st['assigned_user_id']) && $st['assigned_user_id'] !== '' ? (int)$st['assigned_user_id'] : null;
                $normalized[] = [
                    'title' => $title,
                    'description' => $desc,
                    'percentage' => $perc,
                    'due_date' => $due,
                    'priority' => $prio,
                    'assigned_user_id' => $auid,
                ];
            }
            $subtasks = $normalized;
        }
        
        $priority = Utils::sanitizeInput($_POST['priority'] ?? 'medium');
        
        // Corregir valor de prioridad al enum de Tasks
        if (!in_array($priority, ['low','medium','high','critical'], true)) {
            $priority = ($priority === 'urgent') ? 'critical' : 'medium';
        }
        
        // Manejar labels que puede ser un array o JSON string
        $labelsRaw = $_POST['labels'] ?? [];
        if (is_string($labelsRaw)) {
            $labels = json_decode($labelsRaw, true) ?: [];
        } else {
            $labels = $labelsRaw;
        }
        
        error_log('createTask - Valores procesados:');
        error_log('  taskTitle: "' . $taskTitle . '"');
        error_log('  taskDueDate: ' . ($taskDueDate ?? 'NULL'));
        error_log('  taskProject: ' . $taskProject);
        error_log('  priority: ' . $priority);
        error_log('  assignedMembers count: ' . count($assignedMembers));
        error_log('  subtasks count: ' . count($subtasks));
        
        if (empty($taskTitle) || empty($taskDueDate)) {
            error_log('createTask - Error: Título o fecha vacíos');
            Utils::jsonResponse(['success' => false, 'message' => 'Título y fecha límite son requeridos'], 400);
        }
        
        if (empty($assignedMembers)) {
            error_log('createTask - Error: Sin miembros asignados');
            Utils::jsonResponse(['success' => false, 'message' => 'Debe asignar al menos un colaborador'], 400);
        }
        
        // Verificar que el proyecto pertenece al clan (si se seleccionó)
        if ($taskProject > 0) {
            error_log('createTask - Verificando proyecto ID: ' . $taskProject);
            $project = $this->projectModel->findById($taskProject);
            if (!$project) {
                error_log('createTask - Error: Proyecto no encontrado');
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 400);
            }
            if ($project['clan_id'] != $this->userClan['clan_id']) {
                error_log('createTask - Error: Proyecto de otro clan. Project clan_id: ' . $project['clan_id'] . ', User clan_id: ' . $this->userClan['clan_id']);
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no válido'], 400);
            }
        } else {
            // Si no se selecciona proyecto, usar el primer proyecto del clan
            $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
            if (empty($projects)) {
                Utils::jsonResponse(['success' => false, 'message' => 'No hay proyectos disponibles en el clan'], 400);
            }
            $taskProject = $projects[0]['project_id'];
        }
        
        // Verificar que todos los usuarios asignados pertenecen al clan
        error_log('createTask - Verificando usuarios asignados...');
        foreach ($assignedMembers as $userId) {
            error_log('  Verificando usuario ID: ' . $userId);
            $user = $this->userModel->findById($userId);
            if (!$user) {
                error_log('  Error: Usuario ' . $userId . ' no encontrado');
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            
            $userClan = $this->userModel->getUserClan($userId);
            if (!$userClan || $userClan['clan_id'] != $this->userClan['clan_id']) {
                error_log('  Error: Usuario ' . $userId . ' no pertenece al clan');
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no pertenece al clan'], 400);
            }
        }
        
        try {
            // Log para debugging
            error_log("=== LLAMANDO createAdvanced ===");
            error_log("projectId: " . $taskProject);
            error_log("title: " . $taskTitle);
            error_log("description: " . $taskDescription);
            error_log("dueDate: " . ($taskDueDate ?? 'NULL'));
            error_log("clanId: " . $this->userClan['clan_id']);
            error_log("priority: " . $priority);
            error_log("createdBy: " . $_SESSION['user_id']);
            error_log("assignedMembers: " . json_encode($assignedMembers));
            error_log("subtasks: " . json_encode($subtasks));
            error_log("priority: " . $priority);
            error_log("labels: " . json_encode($labels));
            error_log("currentUser: " . json_encode($this->currentUser));
            
            // Crear la tarea usando el nuevo método avanzado
            $createdByUserId = $_SESSION['user_id']; // Usar directamente la sesión
            error_log('createTask - createdByUserId desde SESSION: ' . $createdByUserId);
            
            // Validar datos antes de la llamada
            if (empty($taskTitle)) {
                throw new Exception('taskTitle vacío');
            }
            if (empty($taskDueDate)) {
                throw new Exception('taskDueDate vacío');
            }
            if (empty($assignedMembers) || !is_array($assignedMembers)) {
                throw new Exception('assignedMembers inválido: ' . json_encode($assignedMembers));
            }
            if ($taskProject <= 0) {
                throw new Exception('taskProject inválido: ' . $taskProject);
            }
            if (empty($createdByUserId)) {
                throw new Exception('createdByUserId vacío');
            }
            
            error_log('createTask - Validaciones pasadas, llamando createAdvanced...');
            
            if (!$this->taskModel) {
                throw new Exception('taskModel no inicializado');
            }
            
            error_log('createTask - taskModel OK, ejecutando createAdvanced...');
            
            $taskId = $this->taskModel->createAdvanced(
                $taskProject,
                $taskTitle,
                $taskDescription,
                $taskDueDate,
                $this->userClan['clan_id'],
                $priority,
                $createdByUserId,
                $assignedMembers,
                $subtasks,
                $labels
            );
            
            error_log("createTask - taskId retornado: " . ($taskId ? $taskId : 'FALSE/NULL'));
            if (!$taskId) {
                error_log("createTask - ERROR: createAdvanced retornó false/null");
                throw new Exception('createAdvanced retornó false - revisar logs del servidor');
            }
            
            error_log("createTask - ÉXITO: Tarea creada con ID " . $taskId);
            // Notificación de asignación si está activa
            try { (new NotificationService())->notifyTaskAssigned((int)$taskId, $assignedMembers); } catch (Exception $e) { error_log('Notif error (clan_leader task_assigned): ' . $e->getMessage()); }
            Utils::jsonResponse(['success' => true, 'message' => 'Tarea creada exitosamente', 'task_id' => $taskId]);
            
        } catch (Exception $e) {
            error_log("createTask - EXCEPCIÓN en try interno: " . $e->getMessage());
            error_log("createTask - Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al crear la tarea: ' . $e->getMessage()], 500);
        }
        
    } catch (Exception $e) {
        error_log("Error en ClanLeaderController::createTask: " . $e->getMessage());
        Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
    }
}
    
    /**
     * Agregar tarea
     */
    public function addTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $projectId = (int)($_POST['projectId'] ?? 0);
        $taskName = Utils::sanitizeInput($_POST['taskName'] ?? '');
        $description = Utils::sanitizeInput($_POST['description'] ?? '');
        $assignedToUserId = (int)($_POST['assignedToUserId'] ?? 0);
        
        if ($projectId <= 0 || empty($taskName)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        // Verificar que el proyecto pertenece al clan
        $project = $this->projectModel->findById($projectId);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        
        // Verificar que el usuario asignado pertenece al clan
        if ($assignedToUserId > 0) {
            $assignedUser = $this->userModel->findById($assignedToUserId);
            if (!$assignedUser) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario asignado no encontrado'], 404);
            }
            
            $userClan = $this->userModel->getUserClan($assignedToUserId);
            if (!$userClan || $userClan['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'El usuario asignado no pertenece al clan'], 400);
            }
        }
        
        // Crear tarea
        $result = $this->taskModel->create($projectId, $taskName, $description, $assignedToUserId);
        
        if ($result) {
            Utils::jsonResponse(['success' => true, 'message' => 'Tarea creada exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al crear tarea'], 500);
        }
    }
    
    /**
     * Obtener detalles completos de una tarea
     */
    public function getTaskDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_GET['task_id'] ?? 0);
        
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }
        
        try {
            // Obtener información completa de la tarea
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            // Verificar que la tarea pertenece al clan del líder
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Obtener datos relacionados
            $subtasks = $this->taskModel->getSubtasks($taskId);
            $comments = $this->taskModel->getComments($taskId);
            $history = $this->taskModel->getHistory($taskId);
            $assignedUsers = $this->taskModel->getAssignedUsers($taskId);
            $labels = $this->taskModel->getTaskLabels($taskId);
            
            // Cargar la vista con los datos
            $this->loadView('clan_leader/task_details', [
                'task' => $task,
                'subtasks' => $subtasks,
                'comments' => $comments,
                'history' => $history,
                'assignedUsers' => $assignedUsers,
                'labels' => $labels
            ]);
            
        } catch (Exception $e) {
            error_log("Error al obtener detalles de tarea: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al obtener detalles de la tarea'], 500);
        }
    }
    
    /**
     * Agregar comentario a una tarea
     */
    public function addTaskComment() {
        // Debug: Log de la petición
        error_log("addTaskComment: Iniciando petición");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . json_encode($_POST));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("addTaskComment: Método no permitido");
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $commentText = trim($_POST['comment_text'] ?? '');
        
        error_log("addTaskComment: taskId = $taskId, commentText = '$commentText'");
        
        if ($taskId <= 0 || empty($commentText)) {
            error_log("addTaskComment: Datos inválidos");
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar autenticación básica
            if (!$this->currentUser) {
                error_log("addTaskComment: Usuario no autenticado");
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no autenticado'], 401);
            }
            
            error_log("addTaskComment: Usuario autenticado, user_id: " . $this->currentUser['user_id']);
            
            // Agregar comentario y obtener comment_id
            $commentId = $this->taskModel->addComment(
                $taskId, 
                $this->currentUser['user_id'], 
                $commentText, 
                'comment'
            );
            
            if ($commentId) {
                // Manejar adjuntos (soporta uno: 'attachment' y varios: 'attachments[]')
                $files = [];
                $receivedNames = [];
                $savedNames = [];
                if (!empty($_FILES['attachments']) && isset($_FILES['attachments']['name']) && is_array($_FILES['attachments']['name'])) {
                    // Normalizar arreglo de múltiples archivos
                    $count = count($_FILES['attachments']['name']);
                    for ($i = 0; $i < $count; $i++) {
                        if (($_FILES['attachments']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                            $currentName = $_FILES['attachments']['name'][$i] ?? null;
                            $files[] = [
                                'name' => $_FILES['attachments']['name'][$i] ?? null,
                                'type' => $_FILES['attachments']['type'][$i] ?? null,
                                'tmp_name' => $_FILES['attachments']['tmp_name'][$i] ?? null,
                                'size' => $_FILES['attachments']['size'][$i] ?? null,
                            ];
                            if ($currentName) { $receivedNames[] = $currentName; }
                        }
                    }
                } elseif (!empty($_FILES['attachment']) && ($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    // Soporte legado de un solo archivo
                    $files[] = [
                        'name' => $_FILES['attachment']['name'] ?? null,
                        'type' => $_FILES['attachment']['type'] ?? null,
                        'tmp_name' => $_FILES['attachment']['tmp_name'] ?? null,
                        'size' => $_FILES['attachment']['size'] ?? null,
                    ];
                    if (!empty($_FILES['attachment']['name'])) { $receivedNames[] = $_FILES['attachment']['name']; }
                }

                if (!empty($files)) {
                    // Base pública absoluta
                    $publicRoot = dirname(__DIR__, 2) . '/public';
                    $baseUploads = $publicRoot . '/uploads';
                    $uploadDir = $baseUploads . '/task_attachments';
                    // Crear rutas si no existen
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
                            $this->taskModel->saveAttachmentRecord(
                                $taskId,
                                is_numeric($commentId) ? (int)$commentId : null,
                                $this->currentUser['user_id'],
                                $originalName,
                                $publicPath,
                                $file['type'] ?? null
                            );
                            $savedNames[] = $originalName;
                        } else {
                            error_log('addTaskComment: No se pudo mover el archivo subido a ' . $destPath);
                        }
                    }
                }
                error_log("addTaskComment: Comentario agregado exitosamente. Archivos recibidos: " . json_encode($receivedNames) . ", guardados: " . json_encode($savedNames));
                Utils::jsonResponse([
                    'success' => true,
                    'message' => 'Comentario agregado exitosamente',
                    'attachments_received' => $receivedNames,
                    'attachments_saved' => $savedNames
                ]);
            } else {
                error_log("addTaskComment: Error al agregar comentario en la base de datos");
                Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar comentario'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al agregar comentario: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar comentario: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Actualizar estado de subtarea
     */
    public function updateSubtaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $status = Utils::sanitizeInput($_POST['status'] ?? '');
        $completionPercentage = (float)($_POST['completion_percentage'] ?? 0);
        
        if ($subtaskId <= 0 || empty($status)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la subtarea pertenece a una tarea del clan
            $stmt = $this->db->prepare("
                SELECT s.*, t.project_id, p.clan_id
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
            
            if ($subtask['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Actualizar estado
            $result = $this->taskModel->updateSubtaskStatus(
                $subtaskId, 
                $status, 
                $completionPercentage, 
                $this->currentUser['user_id']
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar estado'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al actualizar estado de subtarea: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar estado'], 500);
        }
    }
    
    /**
     * Método de prueba para verificar que el controlador funciona
     */
    public function test() {
        Utils::jsonResponse(['success' => true, 'message' => 'Controlador funcionando correctamente']);
    }
    
    /**
     * Eliminar una tarea
     */
    public function deleteTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        
        if ($taskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan del líder
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Eliminar la tarea
            $result = $this->taskModel->delete($taskId);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Tarea eliminada exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar la tarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar tarea: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar la tarea'], 500);
        }
    }
    
    /**
     * Eliminar una subtarea
     */
    public function deleteSubtask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        
        if ($subtaskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
        }
        
        try {
            // Verificar que la subtarea pertenece a una tarea del clan
            $subtask = $this->taskModel->getSubtasks(null, $subtaskId);
            if (empty($subtask)) {
                Utils::jsonResponse(['success' => false, 'message' => 'Subtarea no encontrada'], 404);
            }
            
            $task = $this->taskModel->findById($subtask[0]['task_id']);
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Eliminar la subtarea
            $result = $this->taskModel->deleteSubtask($subtaskId);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Subtarea eliminada exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar la subtarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar subtarea: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar la subtarea'], 500);
        }
    }
    
    /**
     * Agregar comentario a una tarea
     */
    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $commentText = trim($_POST['comment_text'] ?? '');
        
        if ($taskId <= 0 || empty($commentText)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan del líder
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Agregar el comentario
            $result = $this->taskModel->addComment($taskId, $this->currentUser['user_id'], $commentText, 'comment');
            
            if ($result) {
                // Manejar archivo adjunto si existe
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/task_attachments/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileName = time() . '_' . $_FILES['attachment']['name'];
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $filePath)) {
                        // Guardar información del archivo adjunto
                        $this->taskModel->addCommentAttachment($result, $fileName, $filePath);
                    }
                }
                
                Utils::jsonResponse(['success' => true, 'message' => 'Comentario agregado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar el comentario'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al agregar comentario: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar el comentario'], 500);
        }
    }
    
    /**
     * Agregar colaboradores a una tarea
     */
    public function addCollaborators() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $userIds = $_POST['user_ids'] ?? '';
        
        if ($taskId <= 0 || empty($userIds)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            $userIdsArray = explode(',', $userIds);
            $result = $this->taskModel->assignMultipleUsers($taskId, $userIdsArray, $this->currentUser['user_id']);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Colaboradores agregados exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar colaboradores'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al agregar colaboradores: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar colaboradores'], 500);
        }
    }
    
    /**
     * Remover colaborador de una tarea
     */
    public function removeCollaborator() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if ($taskId <= 0 || $userId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            $result = $this->taskModel->removeUserAssignment($taskId, $userId);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Colaborador removido exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al remover colaborador'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al remover colaborador: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al remover colaborador'], 500);
        }
    }
    
    /**
     * Actualizar porcentaje de asignación de un usuario
     */
    public function updateUserPercentage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $percentage = (int)($_POST['percentage'] ?? 0);
        
        if ($taskId <= 0 || $userId <= 0 || $percentage < 0 || $percentage > 100) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            $result = $this->taskModel->updateUserAssignmentPercentage($taskId, $userId, $percentage);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Porcentaje actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar porcentaje'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error al actualizar porcentaje: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar porcentaje'], 500);
        }
    }
    
    /**
     * Obtener etiquetas disponibles para el clan
     */
    public function getAvailableLabels() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Task_Labels 
                WHERE clan_id = ? OR clan_id IS NULL
                ORDER BY label_name
            ");
            $stmt->execute([$this->userClan['clan_id']]);
            $labels = $stmt->fetchAll();
            
            Utils::jsonResponse(['success' => true, 'labels' => $labels]);
            
        } catch (Exception $e) {
            error_log("Error al obtener etiquetas: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al obtener etiquetas'], 500);
        }
    }
    
    /**
     * Actualizar estado de tarea
     */
    public function toggleTaskStatus() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            $taskId = (int)($_POST['taskId'] ?? 0);
            $isCompleted = (bool)($_POST['isCompleted'] ?? false);
            
            if ($taskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
                return;
            }
            
            // Verificar que la tarea pertenece a un proyecto del clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
                return;
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
                return;
            }
            
            // Determinar el nuevo estado basado en el estado actual
            $currentStatus = $task['status'];
            $newStatus = '';
            
            if ($isCompleted) {
                $newStatus = 'completed';
            } else {
                // Si no está completada, cambiar a pending
                $newStatus = 'pending';
            }
            
            // Actualizar estado usando el método update del modelo
            $result = $this->taskModel->update(
                $taskId,
                $task['task_name'],
                $task['description'],
                $task['assigned_to_user_id'],
                $task['priority'],
                $task['due_date'],
                null, // assigned_percentage
                $newStatus // nuevo parámetro para estado
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Estado de tarea actualizado']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar tarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::toggleTaskStatus: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Buscar miembros del clan
     */
    private function searchMembers($searchTerm) {
        $members = $this->clanModel->getMembers($this->userClan['clan_id']);
        $searchPattern = strtolower($searchTerm);
        
        return array_filter($members, function($member) use ($searchPattern) {
            return strpos(strtolower($member['full_name']), $searchPattern) !== false ||
                   strpos(strtolower($member['username']), $searchPattern) !== false ||
                   strpos(strtolower($member['email']), $searchPattern) !== false;
        });
    }
    
    /**
     * Buscar proyectos del clan
     */
    private function searchProjects($searchTerm) {
        $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
        $searchPattern = strtolower($searchTerm);
        
        return array_filter($projects, function($project) use ($searchPattern) {
            return strpos(strtolower($project['project_name']), $searchPattern) !== false ||
                   strpos(strtolower($project['description']), $searchPattern) !== false;
        });
    }
    
    /**
     * Obtener estadísticas de usuarios del clan
     */
    private function getUserStats() {
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            return [
                'total_members' => 0,
                'active_members' => 0,
                'recent_members' => 0
            ];
        }
        $members = $this->clanModel->getMembers($this->userClan['clan_id']);
        
        return [
            'total_members' => count($members),
            'active_members' => count(array_filter($members, function($member) {
                return $member['is_active'] == 1;
            })),
            'recent_members' => count(array_filter($members, function($member) {
                return isset($member['last_login']) && 
                       $member['last_login'] && 
                       strtotime($member['last_login']) >= strtotime('-30 days');
            }))
        ];
    }
    
    /**
     * Obtener estadísticas del clan
     */
    private function getClanStats() {
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            return [
                'member_count' => 0,
                'project_count' => 0,
                'active_projects' => 0,
                'completed_projects' => 0
            ];
        }
        $members = $this->clanModel->getMembers($this->userClan['clan_id']);
        $projects = $this->projectModel->getByClan($this->userClan['clan_id']);
        
        return [
            'member_count' => count($members),
            'project_count' => count($projects),
            'active_projects' => count(array_filter($projects, function($project) {
                return $project['status'] == 'active';
            })),
            'completed_projects' => count(array_filter($projects, function($project) {
                return $project['status'] == 'completed';
            }))
        ];
    }
    
    /**
     * Obtener estadísticas de tareas del clan
     */
    private function getTaskStats() {
        try {
            if (!$this->userClan || !isset($this->userClan['clan_id'])) {
                return [
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'in_progress_tasks' => 0,
                    'completion_percentage' => 0
                ];
            }
            // Incluir tareas del clan y las asignadas a sus miembros aunque pertenezcan a proyectos de otro clan (ej. Olympo)
            $allTasksData = $this->taskModel->getAllTasksByClan($this->userClan['clan_id'], 1, 10000, '', '');
            $tasks = $allTasksData['tasks'] ?? [];

            $totalTasks = count($tasks);
            $completedTasks = 0;
            $pendingTasks = 0;
            $inProgressTasks = 0;
            foreach ($tasks as $t) {
                $status = $t['status'] ?? 'pending';
                if ($status === 'completed') { $completedTasks++; }
                elseif ($status === 'in_progress') { $inProgressTasks++; }
                elseif ($status === 'pending') { $pendingTasks++; }
            }

            return [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'completion_percentage' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0
            ];
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas de tareas: " . $e->getMessage());
            return [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'pending_tasks' => 0,
                'in_progress_tasks' => 0,
                'completion_percentage' => 0
            ];
        }
    }
    
    /**
     * Obtener icono del clan basado en el dios
     */
    private function getClanIcon($clanName) {
        $icons = [
            'ZEUS' => '⚡', // Rayo de Zeus
            'Artemisa' => '🏹', // Arco y flecha de Artemisa
            'Afrodita' => '💝', // Corazón de Afrodita
            'Kratos' => '👊', // Puño de Kratos
            'Hermes' => '🏃', // Corredor de Hermes
            'Persefone' => '🌱', // Planta de Persefone
            'Aura' => '⭐', // Estrella de Aura
            'GAIA' => '🌿', // Hoja de Gaia
        ];
        
        return $icons[$clanName] ?? '⚔️'; // Icono por defecto
    }
    
    /**
     * Obtener estadísticas detalladas de un usuario específico
     */
    public function getUserDetailedStats() {
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }
            
            $userId = (int)($_GET['user_id'] ?? 0);
            
            if ($userId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de usuario inválido'], 400);
                return;
            }
            
            // Verificar que el usuario pertenece al clan
            $memberStmt = $this->db->prepare("
                SELECT u.user_id, u.username, u.full_name, u.email, u.is_active, u.last_login
                FROM Users u
                JOIN Clan_Members cm ON u.user_id = cm.user_id
                WHERE cm.clan_id = ? AND u.user_id = ?
            ");
            $memberStmt->execute([$this->userClan['clan_id'], $userId]);
            $member = $memberStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario no encontrado en el clan'], 404);
                return;
            }
            
            // Obtener proyectos del clan
            $projectStmt = $this->db->prepare("SELECT project_id, project_name FROM Projects WHERE clan_id = ?");
            $projectStmt->execute([$this->userClan['clan_id']]);
            $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $projectIds = array_column($projects, 'project_id');
            
            // Estadísticas generales de tareas
            $taskStats = [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'pending_tasks' => 0,
                'in_progress_tasks' => 0,
                'overdue_tasks' => 0,
                'completion_percentage' => 0
            ];
            
            // Tareas por proyecto
            $tasksByProject = [];
            
            if (!empty($projectIds)) {
                $placeholders = str_repeat('?,', count($projectIds) - 1) . '?';
                
                // Estadísticas generales
                $statsStmt = $this->db->prepare("\n                    SELECT \n                        COUNT(DISTINCT t.task_id) as total_tasks,\n                        SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,\n                        SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,\n                        SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,\n                        SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'completed' THEN 1 ELSE 0 END) as overdue_tasks\n                    FROM Tasks t\n                    WHERE t.project_id IN ($placeholders) AND t.is_subtask = 0\n                      AND (t.assigned_to_user_id = ? OR t.task_id IN (SELECT ta.task_id FROM Task_Assignments ta WHERE ta.user_id = ?))\n                ");
                
                $params = array_merge($projectIds, [$userId, $userId]);
                $statsStmt->execute($params);
                $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
                
                $taskStats['total_tasks'] = (int)$stats['total_tasks'];
                $taskStats['completed_tasks'] = (int)$stats['completed_tasks'];
                $taskStats['pending_tasks'] = (int)$stats['pending_tasks'];
                $taskStats['in_progress_tasks'] = (int)$stats['in_progress_tasks'];
                $taskStats['overdue_tasks'] = (int)$stats['overdue_tasks'];
                $taskStats['completion_percentage'] = $taskStats['total_tasks'] > 0 ? 
                    round(($taskStats['completed_tasks'] / $taskStats['total_tasks']) * 100, 1) : 0;
                
                // Tareas por proyecto
                foreach ($projects as $project) {
                    $projectTaskStmt = $this->db->prepare("\n                        SELECT \n                            t.task_id,\n                            t.task_name,\n                            t.description,\n                            t.status,\n                            t.priority,\n                            t.due_date,\n                            t.completed_at,\n                            t.assigned_percentage,\n                            t.automatic_points\n                        FROM Tasks t\n                        WHERE t.project_id = ? AND t.is_subtask = 0\n                          AND (t.assigned_to_user_id = ? OR t.task_id IN (SELECT ta.task_id FROM Task_Assignments ta WHERE ta.user_id = ?))\n                        ORDER BY t.due_date ASC, t.created_at DESC\n                    ");
                    $projectTaskStmt->execute([$project['project_id'], $userId, $userId]);
                    $projectTasks = $projectTaskStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($projectTasks)) {
                        $tasksByProject[] = [
                            'project_id' => $project['project_id'],
                            'project_name' => $project['project_name'],
                            'tasks' => $projectTasks
                        ];
                    }
                }
            }
            
            // Obtener historial de actividad reciente
            $activityStmt = $this->db->prepare("
                SELECT 
                    th.action_type,
                    th.field_name,
                    th.old_value,
                    th.new_value,
                    th.notes,
                    th.created_at,
                    t.task_name
                FROM Task_History th
                JOIN Tasks t ON th.task_id = t.task_id
                WHERE th.user_id = ? AND t.project_id IN ($placeholders)
                ORDER BY th.created_at DESC
                LIMIT 10
            ");
            $params = array_merge([$userId], $projectIds);
            $activityStmt->execute($params);
            $recentActivity = $activityStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener comentarios recientes
            $commentsStmt = $this->db->prepare("
                SELECT 
                    tc.comment_text,
                    tc.comment_type,
                    tc.created_at,
                    t.task_name
                FROM Task_Comments tc
                JOIN Tasks t ON tc.task_id = t.task_id
                WHERE tc.user_id = ? AND t.project_id IN ($placeholders)
                ORDER BY tc.created_at DESC
                LIMIT 5
            ");
            $params = array_merge([$userId], $projectIds);
            $commentsStmt->execute($params);
            $recentComments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Generar color de avatar
            $avatarColor = $this->getAvatarColor($userId);
            
            $response = [
                'success' => true,
                'user' => [
                    'user_id' => $member['user_id'],
                    'username' => $member['username'],
                    'full_name' => $member['full_name'],
                    'email' => $member['email'],
                    'is_active' => (bool)$member['is_active'],
                    'last_login' => $member['last_login'],
                    'avatar_color' => $avatarColor,
                    'initial' => strtoupper(substr($member['full_name'], 0, 1))
                ],
                'task_stats' => $taskStats,
                'tasks_by_project' => $tasksByProject,
                'recent_activity' => $recentActivity,
                'recent_comments' => $recentComments
            ];
            
            Utils::jsonResponse($response);
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::getUserStats: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener contribuciones por miembro del clan
     */
    private function getMemberContributions() {
        try {
            if (!$this->userClan || !isset($this->userClan['clan_id'])) {
                return [];
            }
            // Obtener todos los miembros del clan usando el modelo Clan
            $members = $this->clanModel->getMembers($this->userClan['clan_id']);
            
            if (empty($members)) {
                return [];
            }
            
            // Obtener proyectos del clan
            $projectStmt = $this->db->prepare("SELECT project_id FROM Projects WHERE clan_id = ?");
            $projectStmt->execute([$this->userClan['clan_id']]);
            $projects = $projectStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $totalCompletedTasks = 0;
            
            foreach ($members as &$member) {
                // Agregar profile_picture si no existe
                if (!isset($member['profile_picture'])) {
                    $member['profile_picture'] = null;
                }
                
                if (!empty($projects)) {
                    $placeholders = str_repeat('?,', count($projects) - 1) . '?';
                    $taskStmt = $this->db->prepare("\n                        SELECT\n                            COUNT(DISTINCT t.task_id) as total_tasks,\n                            SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,\n                            SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,\n                            SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks\n                        FROM Tasks t\n                        WHERE t.project_id IN ($placeholders)\n                          AND t.is_subtask = 0\n                          AND (\n                            t.assigned_to_user_id = ?\n                            OR t.task_id IN (SELECT ta.task_id FROM Task_Assignments ta WHERE ta.user_id = ?)\n                          )\n                    ");
                    
                    $params = array_merge($projects, [$member['user_id'], $member['user_id']]);
                    $taskStmt->execute($params);
                    $taskStats = $taskStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $member['total_tasks'] = (int)$taskStats['total_tasks'];
                    $member['completed_tasks'] = (int)$taskStats['completed_tasks'];
                    $member['pending_tasks'] = (int)$taskStats['pending_tasks'];
                    $member['in_progress_tasks'] = (int)$taskStats['in_progress_tasks'];
                    
                    $totalCompletedTasks += $member['completed_tasks'];
                } else {
                    $member['total_tasks'] = 0;
                    $member['completed_tasks'] = 0;
                    $member['pending_tasks'] = 0;
                    $member['in_progress_tasks'] = 0;
                }
                
                // Generar inicial para avatar
                $member['initial'] = strtoupper(substr($member['full_name'], 0, 1));
            }
            
            // Calcular porcentajes de contribución
            foreach ($members as &$member) {
                $member['contribution_percentage'] = $totalCompletedTasks > 0 ? 
                    round(($member['completed_tasks'] / $totalCompletedTasks) * 100, 1) : 0;
            }
            
            // Ordenar por tareas completadas descendente
            usort($members, function($a, $b) {
                return $b['completed_tasks'] - $a['completed_tasks'];
            });
            
            return $members;
        } catch (Exception $e) {
            error_log("Error al obtener contribuciones de miembros: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Vista de disponibilidad de colaboradores
     */
    public function collaboratorAvailability() {
        $view = $_GET['view'] ?? 'calendar'; // calendar o gantt
        
        // Verificar autenticación
        $this->requireAuth();
        
        // Verificar acceso de líder de clan
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
        }
        
        // Obtener datos de disponibilidad
        $availability_data = [];
        $members = $this->clanModel->getMembers($this->userClan['clan_id']);
        
        foreach ($members as $member) {
            $activeTasks = $this->taskModel->getActiveTasksByUser($member['user_id']);
            $taskCount = count($activeTasks);
            
            // Determinar nivel de disponibilidad
            $availability = 'disponible';
            if ($taskCount >= 10) {
                $availability = 'sobrecargado';
            } elseif ($taskCount >= 4) {
                $availability = 'ocupado';
            } elseif ($taskCount >= 1) {
                $availability = 'poco_ocupado';
            }
            
            $availability_data[] = [
                'member' => $member,
                'task_count' => $taskCount,
                'availability' => $availability,
                'tasks' => $activeTasks
            ];
        }
        
        // Calcular resumen
        $summary = [
            'disponibles' => 0,
            'poco_ocupados' => 0,
            'ocupados' => 0,
            'sobrecargados' => 0
        ];
        
        foreach ($availability_data as $data) {
            $availability = $data['availability'];
            if (isset($summary[$availability . 's'])) {
                $summary[$availability . 's']++;
            }
        }
        
        // Obtener todas las tareas (incluyendo asignadas desde proyectos de otros clanes) para el calendario
        $allTasks = [];
        $projects = $this->projectModel->getByClan($this->userClan['clan_id']);

        $allTasksData = $this->taskModel->getAllTasksByClan($this->userClan['clan_id'], 1, 10000, '', '');
        $calendarTasks = $allTasksData['tasks'] ?? [];
        foreach ($calendarTasks as $t) {
            if (($t['status'] ?? '') !== 'cancelled' && !empty($t['due_date'])) {
                $allTasks[] = [
                    'task' => [
                        'task_id' => $t['task_id'],
                        'task_name' => $t['task_name'],
                        'description' => $t['description'],
                        'due_date' => $t['due_date'],
                        'status' => $t['status'],
                    ],
                    'project' => [
                        'project_id' => $t['project_id'],
                        'project_name' => $t['project_name']
                    ],
                    'assigned_user' => null
                ];
            }
        }
        
        $data = [
            'availability_data' => $availability_data,
            'summary' => $summary,
            'all_tasks' => $allTasks,
            'members' => $members,
            'projects' => $projects,
            'view' => $view,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser,
            'clan' => $this->userClan
        ];
        
        if ($view === 'gantt') {
            $this->loadView('clan_leader/gantt_view', $data);
        } else {
            $this->loadView('clan_leader/collaborator_availability', $data);
        }
    }
    
    /**
     * Obtener color de avatar basado en el ID del usuario
     */
    private function getAvatarColor($userId) {
        $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
        return $colors[$userId % count($colors)];
    }
    
    /**
     * Calcular días hasta una fecha
     */
    private function getDaysUntil($date) {
        $date1 = new DateTime();
        $date2 = new DateTime($date);
        $interval = $date1->diff($date2);
        return $interval->days;
    }
    
    /**
     * Verificar si el usuario tiene acceso de líder de clan
     */
    private function hasClanLeaderAccess() {
        if (!$this->currentUser) {
            return false;
        }
        
        // Permitir acceso a Líder de Clan y también a Admin/Super Admin
        if ($this->roleModel->userHasRole($this->currentUser['user_id'], Role::LIDER_CLAN)) {
            return true;
        }
        // Admin o superior (usa jerarquía definida)
        return $this->roleModel->userHasMinimumRole($this->currentUser['user_id'], Role::ADMIN);
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
    private function loadView($viewPath, $data = []) {
        // Extraer variables para la vista, pero preservar $viewPath
        $viewPathBackup = $viewPath;
        extract($data);
        $viewPath = $viewPathBackup;
        
        // Incluir archivo de vista
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die('Vista no encontrada: ' . $viewPath);
        }
    }

    /**
     * Obtener datos de miembros para el snake path
     */
    private function getMembersSnakePathData($kpiQuarterId) {
        try {
            // Consulta revisada para calcular puntos por usuario considerando múltiples asignaciones
            $stmt = $this->db->prepare("\n                SELECT \n                    u.user_id,\n                    u.full_name,\n                    u.email,\n                    0 as total_assigned,\n                    (\n                        -- Puntos por tareas con asignaciones múltiples (Task_Assignments)\n                        COALESCE((\n                            SELECT SUM(\n                                CASE \n                                    WHEN p.task_distribution_mode = 'automatic' THEN \n                                        t.automatic_points / NULLIF((SELECT COUNT(*) FROM Task_Assignments ta2 WHERE ta2.task_id = t.task_id), 0)\n                                    ELSE \n                                        (ta.assigned_percentage * p.kpi_points / 100)\n                                END\n                            )\n                            FROM Tasks t\n                            JOIN Projects p ON p.project_id = t.project_id\n                            JOIN Task_Assignments ta ON ta.task_id = t.task_id AND ta.user_id = u.user_id\n                            WHERE p.kpi_quarter_id = ?\n                              AND p.clan_id = ?\n                              AND t.is_completed = 1\n                              AND t.is_subtask = 0\n                        ), 0)\n                        +\n                        -- Puntos por tareas con asignación directa (sin Task_Assignments)\n                        COALESCE((\n                            SELECT SUM(\n                                CASE \n                                    WHEN p.task_distribution_mode = 'automatic' THEN t.automatic_points\n                                    ELSE (t.assigned_percentage * p.kpi_points / 100)\n                                END\n                            )\n                            FROM Tasks t\n                            JOIN Projects p ON p.project_id = t.project_id\n                            WHERE p.kpi_quarter_id = ?\n                              AND p.clan_id = ?\n                              AND t.is_completed = 1\n                              AND t.is_subtask = 0\n                              AND t.assigned_to_user_id = u.user_id\n                              AND NOT EXISTS (SELECT 1 FROM Task_Assignments ta WHERE ta.task_id = t.task_id)\n                        ), 0)\n                    ) AS earned_points,\n                    0 as total_projects\n                FROM Users u\n                JOIN Clan_Members cm ON u.user_id = cm.user_id\n                WHERE cm.clan_id = ? AND u.is_active = 1\n                ORDER BY earned_points DESC\n            ");
            $stmt->execute([$kpiQuarterId, $this->userClan['clan_id'], $kpiQuarterId, $this->userClan['clan_id'], $this->userClan['clan_id']]);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asignar colores, iconos y normalizar valores
            $memberIcons = [
                'default' => 'fas fa-user',
                'admin' => 'fas fa-crown',
                'leader' => 'fas fa-star',
                'developer' => 'fas fa-code',
                'designer' => 'fas fa-palette',
                'marketing' => 'fas fa-bullhorn',
                'sales' => 'fas fa-chart-line'
            ];
            
            $memberColors = [
                '#ef4444', '#f59e0b', '#8b5cf6', '#3b82f6', '#10b981', 
                '#ec4899', '#6366f1', '#f97316', '#06b6d4', '#dc2626'
            ];
            
            foreach ($members as &$member) {
                $member['member_color'] = $memberColors[array_rand($memberColors)];
                $member['member_icon'] = $memberIcons['default'];
                $member['earned_points'] = (int)round($member['earned_points'] ?? 0);
                $member['total_assigned'] = (int)($member['total_assigned'] ?? 0);
                $member['total_points'] = 1000;
                $member['path_position'] = max(0, $member['earned_points']);
                $member['progress_percentage'] = $member['total_points'] > 0 ? 
                    round(($member['earned_points'] / $member['total_points']) * 100, 1) : 0;
            }
            
            return $members;
        } catch (PDOException $e) {
            error_log("Error al obtener datos de miembros para snake path: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener datos del snake path para miembros
     */
    private function getSnakePathData($currentKPI) {
        if (!$currentKPI) {
            return [
                'quarter_progress' => 0,
                'members_data' => [],
                'total_points' => 1000,
                'quarter_info' => []
            ];
        }

        // Calcular progreso del trimestre basado en la fecha actual
        $quarterProgress = $this->calculateQuarterProgress($currentKPI['quarter'], $currentKPI['year']);
        
        // Obtener datos de miembros con sus puntos
        $membersData = $this->getMembersSnakePathData($currentKPI['kpi_quarter_id']);
        
        // Información del trimestre
        $quarterInfo = $this->getQuarterInfo($currentKPI['quarter'], $currentKPI['year']);

        return [
            'quarter_progress' => $quarterProgress,
            'members_data' => $membersData,
            'total_points' => $currentKPI['total_points'],
            'quarter_info' => $quarterInfo
        ];
    }

    /**
     * Calcular progreso del trimestre basado en la fecha actual
     */
    private function calculateQuarterProgress($quarter, $year) {
        $quarterMonths = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12]
        ];
        
        $currentDate = new DateTime();
        $currentYear = (int)$currentDate->format('Y');
        $currentMonth = (int)$currentDate->format('n');
        $currentDay = (int)$currentDate->format('j');
        
        // Si no es el año del trimestre, retornar 0 o 100
        if ($currentYear != $year) {
            return $currentYear > $year ? 100 : 0;
        }
        
        $quarterStartMonth = $quarterMonths[$quarter][0];
        $quarterEndMonth = $quarterMonths[$quarter][2];
        
        // Si estamos antes del trimestre
        if ($currentMonth < $quarterStartMonth) {
            return 0;
        }
        
        // Si estamos después del trimestre
        if ($currentMonth > $quarterEndMonth) {
            return 100;
        }
        
        // Calcular progreso dentro del trimestre
        $daysInQuarter = 0;
        $daysElapsed = 0;
        
        foreach ($quarterMonths[$quarter] as $month) {
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $currentYear);
            $daysInQuarter += $daysInMonth;
            
            if ($month < $currentMonth) {
                $daysElapsed += $daysInMonth;
            } elseif ($month == $currentMonth) {
                $daysElapsed += $currentDay;
            }
        }
        
        return min(100, max(0, round(($daysElapsed / $daysInQuarter) * 100, 1)));
    }

    /**
     * Obtener información del trimestre
     */
    private function getQuarterInfo($quarter, $year) {
        $quarterMonths = [
            'Q1' => ['ENE', 'FEB', 'MAR'],
            'Q2' => ['ABR', 'MAY', 'JUN'],
            'Q3' => ['JUL', 'AGO', 'SEP'],
            'Q4' => ['OCT', 'NOV', 'DIC']
        ];
        
        $months = $quarterMonths[$quarter] ?? ['MES1', 'MES2', 'MES3'];
        
        return [
            'quarter' => $quarter,
            'year' => $year,
            'months' => $months,
            'display_name' => $quarter . ' ' . $year
        ];
    }
} 