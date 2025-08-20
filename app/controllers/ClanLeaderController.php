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
                
                // Asignar el clan_id al usuario actual para que esté disponible
                if ($this->userClan && isset($this->userClan['clan_id'])) {
                    $this->currentUser['clan_id'] = $this->userClan['clan_id'];
                    error_log('Clan ID asignado al usuario: ' . $this->currentUser['clan_id']);
                } else {
                    error_log('ERROR: No se pudo obtener el clan del usuario');
                }
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

        // Obtener tareas para el tablero Kanban
        $kanbanTasks = $this->getKanbanTasksForClan($this->userClan['clan_id']);
        
        $data = [
            'userStats' => $this->getUserStats(),
            'projectStats' => $this->projectModel->getStatsByClan($this->userClan['clan_id']),
            'clanStats' => $this->getClanStats(),
            'taskStats' => $taskStats,
            'memberContributions' => $memberContributions,
            'kanbanTasks' => $kanbanTasks,
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
        error_log("=== MÉTODO PROJECTS INICIADO ===");
        error_log("Usuario actual: " . json_encode($this->currentUser));
        error_log("Clan del usuario: " . json_encode($this->userClan));
        error_log("¿Tiene acceso de líder?: " . ($this->hasClanLeaderAccess() ? 'SÍ' : 'NO'));
        
        $search = $_GET['search'] ?? '';
        
        // Obtener todos los proyectos del clan
        $allProjects = empty($search) ? 
            $this->projectModel->getByClan($this->userClan['clan_id']) : 
            $this->searchProjects($search);
        
        // Los proyectos personales ya están filtrados en el modelo
        $projects = $allProjects;
        
        error_log("Total de proyectos después del filtro: " . count($projects));
        error_log("=== FIN DEL FILTRADO ===");
        
        // Log del resultado del filtrado
        $totalProjects = count($allProjects);
        $filteredProjects = count($projects);
        error_log("Filtrado de proyectos - Total: $totalProjects, Filtrados: $filteredProjects");
        
        // Log detallado de cada proyecto para debugging
        foreach ($allProjects as $project) {
            $isPersonal = ($project['is_personal'] ?? 0) == 1;
            $projectName = $project['project_name'] ?? 'N/A';
            error_log("DEBUG Proyecto: '$projectName' - ID: {$project['project_id']}, is_personal: " . ($isPersonal ? 'SÍ' : 'NO') . ", Clan: {$project['clan_id']}");
        }
        
        // Reindexar el array después del filtro
        $projects = array_values($projects);
        
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
        $projects = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id'] ?? null) ?? [];
        $clanProjects = array_filter($projects, function($project) {
            return $project['clan_id'] == $this->userClan['clan_id'];
        });
        
        // Obtener proyectos sin KPI del clan
        $projectsWithoutKPI = $this->projectModel->getProjectsWithoutKPIByClan($this->userClan['clan_id']) ?? [];
        
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
        // Asegurar autenticación y permisos para evitar accesos nulos
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
            return;
        }
        $projectId = $_GET['project_id'] ?? null;
        $action = $_GET['action'] ?? null;
        
        if ($action === 'create') {
            // Mostrar formulario de creación de tareas
            $allProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
            
            // Filtrar proyectos personales: mostrar solo los del líder actual
            $projects = array_filter($allProjects, function($project) {
                // Si es un proyecto personal (is_personal = 1)
                if (($project['is_personal'] ?? 0) == 1) {
                    // Solo mostrar si fue creado por el líder actual
                    return ($project['created_by_user_id'] ?? 0) == $this->currentUser['user_id'];
                }
                // Mostrar todos los proyectos no personales
                return true;
            });
            
            // Reindexar el array después del filtro
            $projects = array_values($projects);
            
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
            
            // Verificar acceso: proyecto del clan O tarea asignada/creada por el líder
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project) { Utils::redirect('clan_leader/tasks'); }
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id'])
                || (int)($task['assigned_to_user_id'] ?? 0) === (int)$this->currentUser['user_id']
                || (int)($task['created_by_user_id'] ?? 0) === (int)$this->currentUser['user_id'];
            if (!($this->userClan && (int)$project['clan_id'] === (int)$this->userClan['clan_id']) && !$isAssigned) {
                Utils::redirect('clan_leader/tasks');
            }
            
            // Obtener datos necesarios para el formulario
            if ($this->userClan && (int)$project['clan_id'] === (int)$this->userClan['clan_id']) {
                $allProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
                
                // Filtrar proyectos personales: mostrar solo los del líder actual
                $projects = array_filter($allProjects, function($project) {
                    // Si es un proyecto personal (is_personal = 1)
                    if (($project['is_personal'] ?? 0) == 1) {
                        // Solo mostrar si fue creado por el líder actual
                        return ($project['created_by_user_id'] ?? 0) == $this->currentUser['user_id'];
                    }
                    // Mostrar todos los proyectos no personales
                    return true;
                });
                
                // Reindexar el array después del filtro
                $projects = array_values($projects);
                
                $members = $this->clanModel->getMembers($this->userClan['clan_id']);
            } else {
                // Proyecto externo: limitar selección
                $projects = [$project];
                $members = [];
            }
            
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
            // Permitir ver proyecto si es del clan del líder o si el líder tiene tareas asignadas en ese proyecto
            $project = $this->projectModel->findById($projectId);
            if (!$project) { Utils::redirect('clan_leader/tasks'); }

            $isLeaderClanProject = $this->userClan && ((int)$project['clan_id'] === (int)$this->userClan['clan_id']);
            $tasks = $this->taskModel->getByProjectWithPrivacy($projectId, $this->currentUser['user_id']);
            if (!$isLeaderClanProject) {
                $uid = (int)$this->currentUser['user_id'];
                $tasks = array_values(array_filter($tasks, function($t) use ($uid){
                    $primary = (int)($t['assigned_to_user_id'] ?? 0) === $uid;
                    $list = isset($t['all_assigned_user_ids']) ? array_filter(explode(',', (string)$t['all_assigned_user_ids'])) : [];
                    $inList = in_array((string)$uid, $list, true);
                    return $primary || $inList;
                }));
                if (empty($tasks)) { Utils::redirect('clan_leader/tasks'); }
            }
            $data = [
                'project' => $project,
                'tasks' => $tasks,
                'currentPage' => 'clan_leader',
                'user' => $this->currentUser,
                'clan' => $this->userClan
            ];
            
            $this->loadView('clan_leader/project_tasks', $data);
        } else {
            // Tareas propias del líder
            $ownTasksData = $this->taskModel->getUserTasks($this->currentUser['user_id'], 1, 10000, '', '');
            $ownTasks = $ownTasksData['tasks'] ?? [];
            $ownByProject = [];
            foreach ($ownTasks as $t) {
                $pid = (int)$t['project_id'];
                if (!isset($ownByProject[$pid])) {
                    $ownByProject[$pid] = [
                        'project_name' => $t['project_name'],
                        'total' => 0,
                        'completed' => 0
                    ];
                }
                $ownByProject[$pid]['total']++;
                if (($t['status'] ?? '') === 'completed') { $ownByProject[$pid]['completed']++; }
            }

            // 1) Proyectos del clan: mostrar solo proyectos no personales
            $clanProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
            $projects = [];
            $presentIds = [];
            foreach ($clanProjects as $p) {
                $pid = (int)$p['project_id'];
                $presentIds[$pid] = true;
                
                // Para proyectos del clan: mostrar métricas de TODO el proyecto
                $projectTasks = $this->taskModel->getByProjectWithPrivacy($pid, $this->currentUser['user_id']);
                $total = count($projectTasks);
                $completed = 0;
                
                // Filtrar tareas personales: solo mostrar las del usuario actual
                foreach ($projectTasks as $t) {
                    // Si es un proyecto personal, solo contar tareas del usuario actual
                    if (($p['is_personal'] ?? 0) == 1) {
                        if (($t['assigned_to_user_id'] ?? 0) == $this->currentUser['user_id'] ||
                            ($t['created_by_user_id'] ?? 0) == $this->currentUser['user_id']) {
                            if (($t['status'] ?? '') === 'completed' || ($t['is_completed'] ?? 0) == 1) { 
                                $completed++; 
                            }
                        }
                    } else {
                        // Para proyectos no personales, contar todas las tareas
                        if (($t['status'] ?? '') === 'completed' || ($t['is_completed'] ?? 0) == 1) { 
                            $completed++; 
                        }
                    }
                }
                
                $progress = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                $projects[] = [
                    'project_id' => $pid,
                    'project_name' => $p['project_name'],
                    'status' => $p['status'],
                    'total_tasks' => $total,
                    'completed_tasks' => $completed,
                    'progress_percentage' => $progress
                ];
            }

            // 2) Proyectos adicionales (p. ej., lógicos de Olympo) SOLO si el líder tiene tareas
            foreach ($ownByProject as $pid => $info) {
                if (!isset($presentIds[$pid])) {
                    $progress = $info['total'] > 0 ? round(($info['completed'] / $info['total']) * 100, 2) : 0;
                    $projects[] = [
                        'project_id' => (int)$pid,
                        'project_name' => $info['project_name'],
                        'status' => 'open',
                        'total_tasks' => $info['total'],
                        'completed_tasks' => $info['completed'],
                        'progress_percentage' => $progress
                    ];
                }
            }
            
            // Obtener parámetros de paginación, búsqueda y filtros
            $page = (int)($_GET['page'] ?? 1);
            $perPage = (int)($_GET['per_page'] ?? 5);
            $search = $_GET['search'] ?? '';
            $statusFilter = $_GET['status_filter'] ?? '';
            
            // Validar perPage para evitar valores muy altos
            $perPage = max(1, min($perPage, 100));
            
            // Tabla: combinar
            // - Tareas de TODOS del clan (estricto a proyectos del clan)
            // - MÁS tareas del líder SOLO de proyectos lógicos (Recurrentes/Eventuales)
            // - MÁS tareas personales del líder actual
            $clanTasks = $this->taskModel->getAllTasksByClanStrict($this->userClan['clan_id'], $page, $perPage, $search, $statusFilter);

            $ownLogical = $this->taskModel->getUserTasksByProjectNames(
                $this->currentUser['user_id'],
                ['Tareas Recurrentes', 'Tareas Eventuales']
            );

            // Obtener tareas personales del líder actual
            $ownPersonalTasks = $this->taskModel->getPersonalTasksForClanLeader(
                $this->currentUser['user_id'], 
                $this->userClan['clan_id']
            );

            // Fusionar evitando duplicados por task_id
            $merged = [];
            foreach ($clanTasks['tasks'] as $t) { $merged[$t['task_id']] = $t; }
            foreach ($ownLogical as $t) { $merged[$t['task_id']] = $t; }
            foreach ($ownPersonalTasks as $t) { $merged[$t['task_id']] = $t; }
            $mergedTasks = array_values($merged);

            // Reusar estructura de paginación del estricta
            $allTasksData = $clanTasks;
            $allTasksData['tasks'] = $mergedTasks;
            
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
            // Los proyectos personales ya están filtrados en el modelo
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
            
            // Verificar acceso: pertenece al clan del líder o la tarea está asignada al líder
            $project = $this->projectModel->findById($task['project_id']);
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            if (!$project || ((int)$project['clan_id'] !== (int)$this->userClan['clan_id'] && !$isAssigned)) {
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
            
            // Verificar acceso: tarea del clan o asignada al usuario
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            $project = $this->projectModel->findById($task['project_id']);
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            if (!$project || ((int)$project['clan_id'] !== (int)($this->userClan['clan_id'] ?? 0) && !$isAssigned)) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }

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
            
            // Verificar que la tarea pertenece a un proyecto del clan o asignada al líder
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
                return;
            }
            
            $project = $this->projectModel->findById($task['project_id']);
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            if (!$project || ((int)$project['clan_id'] !== (int)$this->userClan['clan_id'] && !$isAssigned)) {
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
     * Verificar si una tarea está asignada a un usuario (asignación principal o en Task_Assignments)
     */
    private function isTaskAssignedToUser($taskId, $userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM Tasks WHERE task_id = ? AND assigned_to_user_id = ?");
            $stmt->execute([$taskId, $userId]);
            $row = $stmt->fetch();
            if ((int)($row['c'] ?? 0) > 0) { return true; }

            $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM Task_Assignments WHERE task_id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            $row = $stmt->fetch();
            return (int)($row['c'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log('Error isTaskAssignedToUser (Leader): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar proyectos del clan
     */
    private function searchProjects($searchTerm) {
        $allProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
        $searchPattern = strtolower($searchTerm);
        
        // Primero filtrar por búsqueda
        $searchResults = array_filter($allProjects, function($project) use ($searchPattern) {
            return strpos(strtolower($project['project_name']), $searchPattern) !== false ||
                   strpos(strtolower($project['description']), $searchPattern) !== false;
        });
        
        // Los proyectos personales ya están filtrados en el modelo
        return array_values($searchResults);
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
        $allProjects = $this->projectModel->getByClan($this->userClan['clan_id']);
        
        // Los proyectos personales ya están filtrados en el modelo
        $filteredProjects = $allProjects;
        
        return [
            'member_count' => count($members),
            'project_count' => count($filteredProjects),
            'active_projects' => count(array_filter($filteredProjects, function($project) {
                return $project['status'] == 'active';
            })),
            'completed_projects' => count(array_filter($filteredProjects, function($project) {
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
            
            // Obtener tareas del clan (excluyendo proyectos personales)
            $clanTasksData = $this->taskModel->getAllTasksByClanStrict($this->userClan['clan_id'], 1, 10000, '', '');
            $clanTasks = $clanTasksData['tasks'] ?? [];
            
            // Obtener tareas personales del líder actual
            $ownPersonalTasks = $this->taskModel->getPersonalTasksForClanLeader(
                $this->currentUser['user_id'], 
                $this->userClan['clan_id']
            );
            
            // Combinar tareas del clan y tareas personales del líder
            $allTasks = array_merge($clanTasks, $ownPersonalTasks);

            $totalTasks = count($allTasks);
            $completedTasks = 0;
            $pendingTasks = 0;
            $inProgressTasks = 0;
            foreach ($allTasks as $t) {
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
            
            // Obtener proyectos del clan (excluyendo proyectos personales de otros usuarios)
            $projectStmt = $this->db->prepare("
                SELECT project_id, is_personal, created_by_user_id 
                FROM Projects 
                WHERE clan_id = ? 
                AND (is_personal IS NULL OR is_personal != 1 OR created_by_user_id = ?)
            ");
            $projectStmt->execute([$this->userClan['clan_id'], $this->currentUser['user_id']]);
            $projectData = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Separar proyectos personales del usuario actual y proyectos no personales
            $personalProjects = [];
            $nonPersonalProjects = [];
            
            foreach ($projectData as $project) {
                if (($project['is_personal'] ?? 0) == 1) {
                    // Solo incluir proyectos personales del usuario actual
                    if ($project['created_by_user_id'] == $this->currentUser['user_id']) {
                        $personalProjects[] = $project['project_id'];
                    }
                } else {
                    // Incluir todos los proyectos no personales
                    $nonPersonalProjects[] = $project['project_id'];
                }
            }
            
            // Combinar ambos tipos de proyectos
            $projects = array_merge($nonPersonalProjects, $personalProjects);
            
            $totalCompletedTasks = 0;
            
            foreach ($members as &$member) {
                // Agregar profile_picture si no existe
                if (!isset($member['profile_picture'])) {
                    $member['profile_picture'] = null;
                }
                
                if (!empty($projects)) {
                    $placeholders = str_repeat('?,', count($projects) - 1) . '?';
                    
                    // Consulta que respeta la privacidad de proyectos personales
                    $taskStmt = $this->db->prepare("
                        SELECT
                            COUNT(DISTINCT t.task_id) as total_tasks,
                            SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                            SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,
                            SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks
                        FROM Tasks t
                        JOIN Projects p ON t.project_id = p.project_id
                        WHERE t.project_id IN ($placeholders)
                          AND t.is_subtask = 0
                          AND (
                            t.assigned_to_user_id = ?
                            OR t.task_id IN (SELECT ta.task_id FROM Task_Assignments ta WHERE ta.user_id = ?)
                          )
                          AND (
                            p.is_personal IS NULL 
                            OR p.is_personal != 1 
                            OR (p.is_personal = 1 AND p.created_by_user_id = ?)
                          )
                    ");
                    
                    $params = array_merge($projects, [$member['user_id'], $member['user_id'], $this->currentUser['user_id']]);
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
            $activeTasks = $this->taskModel->getActiveTasksByUserForClanLeader($member['user_id'], $this->currentUser['user_id']);
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
        
        // Obtener todas las tareas (excluyendo tareas personales de otros usuarios) para el calendario
        $allTasks = [];
        
        // Obtener tareas del clan (excluyendo proyectos personales)
        $clanTasksData = $this->taskModel->getAllTasksByClanStrict($this->userClan['clan_id'], 1, 10000, '', '');
        $clanTasks = $clanTasksData['tasks'] ?? [];
        
        // Obtener tareas personales del líder actual
        $ownPersonalTasks = $this->taskModel->getPersonalTasksForClanLeader(
            $this->currentUser['user_id'], 
            $this->userClan['clan_id']
        );
        
        // Combinar tareas del clan y tareas personales del líder
        $calendarTasks = array_merge($clanTasks, $ownPersonalTasks);
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
        $quarterInfo = $this->getQuarterInfo($currentKPI['quarter'], $currentKPI['year']) ?? [];

        return [
            'quarter_progress' => $quarterProgress,
            'members_data' => $membersData,
            'total_points' => $currentKPI['total_points'] ?? 1000,
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

    /**
     * Obtener tareas para el tablero Kanban del clan
     */
    private function getKanbanTasksForClan($clanId) {
        try {
            error_log("=== DEBUG getKanbanTasksForClan ===");
            error_log("Clan ID: $clanId");
            
            // Tareas del clan del líder + tareas recurrentes/eventuales asignadas al usuario
            error_log("Filtrando tareas del clan ID: $clanId y tareas recurrentes/eventuales asignadas al usuario");
            
            // Obtener tareas del clan del líder + tareas recurrentes/eventuales asignadas al usuario
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
                 WHERE (
                        (p.clan_id = ? AND (p.is_personal IS NULL OR p.is_personal != 1))
                        OR (p.project_name IN ('Tareas Recurrentes', 'Tareas Eventuales') 
                            AND t.assigned_to_user_id = ?)
                        OR (p.clan_id = ? AND p.is_personal = 1 AND p.created_by_user_id = ? AND (t.assigned_to_user_id = ? OR t.created_by_user_id = ?))
                       )
                   AND t.is_subtask = 0
                   AND t.status != 'completed'
                 ORDER BY t.due_date ASC, t.task_id ASC"
            );
            
            $params = [$clanId, $this->currentUser['user_id'], $clanId, $this->currentUser['user_id'], $this->currentUser['user_id'], $this->currentUser['user_id']];
            error_log("Ejecutando consulta con parámetros: clanId=$clanId, userId={$this->currentUser['user_id']} (tareas del clan + recurrentes/eventuales + personales asignadas al usuario)");
            error_log("Parámetros SQL: " . implode(', ', $params));
            $stmt->execute($params);
            $allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Total tareas encontradas: " . count($allTasks));
            
            // Debug: mostrar todas las tareas encontradas
            foreach ($allTasks as $index => $task) {
                error_log("Tarea $index: ID={$task['task_id']}, Nombre='{$task['task_name']}', Proyecto='{$task['project_name']}', Due_date='{$task['due_date']}', Days_until_due='{$task['days_until_due']}'");
                
                // Debug específico para tareas personales
                if ($task['project_name'] === 'Tareas Personales') {
                    error_log("  *** TAREA PERSONAL DETECTADA *** - Necesita verificación de filtrado");
                }
            }
            
            // Debug: verificar qué proyectos únicos se encontraron
            $uniqueProjects = array_unique(array_column($allTasks, 'project_name'));
            error_log("Proyectos únicos encontrados: " . implode(', ', $uniqueProjects));
            
            // Debug: verificar tareas del clan y tareas recurrentes/eventuales asignadas al usuario
            $clanTasks = array_filter($allTasks, function($task) use ($clanId) {
                return true; // Todas las tareas son del clan del líder o recurrentes/eventuales asignadas
            });
            error_log("Total tareas encontradas (clan + recurrentes/eventuales asignadas): " . count($clanTasks));
            
            // Separar tareas por tipo para debug
            $clanSpecificTasks = array_filter($allTasks, function($task) use ($clanId) {
                return !in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales']);
            });
            $recurrentEventualTasks = array_filter($allTasks, function($task) {
                return in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales']);
            });
            $personalTasks = array_filter($allTasks, function($task) {
                return $task['project_name'] === 'Tareas Personales';
            });
            
            error_log("Tareas específicas del clan: " . count($clanSpecificTasks));
            error_log("Tareas recurrentes/eventuales asignadas al usuario: " . count($recurrentEventualTasks));
            error_log("Tareas personales del usuario: " . count($personalTasks));
            
            foreach ($recurrentEventualTasks as $task) {
                error_log("Tarea recurrente/eventual asignada: ID={$task['task_id']}, Nombre='{$task['task_name']}', Proyecto='{$task['project_name']}'");
            }
            
            foreach ($personalTasks as $task) {
                error_log("Tarea personal: ID={$task['task_id']}, Nombre='{$task['task_name']}', Proyecto='{$task['project_name']}'");
            }
            
            // Debug: verificar todas las tareas por proyecto
            $tasksByProject = [];
            foreach ($allTasks as $task) {
                $projectName = $task['project_name'];
                if (!isset($tasksByProject[$projectName])) {
                    $tasksByProject[$projectName] = 0;
                }
                $tasksByProject[$projectName]++;
            }
            error_log("Tareas por proyecto: " . json_encode($tasksByProject));

            // Organizar tareas por columnas del Kanban
            $kanbanTasks = [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];

            foreach ($allTasks as $task) {
                $daysUntilDue = (int)$task['days_until_due'];
                
                if ($daysUntilDue == 999) {
                    // Tareas sin fecha de vencimiento van a la columna de 2 semanas
                    $kanbanTasks['2_semanas'][] = $task;
                } elseif ($daysUntilDue < 0) {
                    $kanbanTasks['vencidas'][] = $task;
                } elseif ($daysUntilDue == 0) {
                    $kanbanTasks['hoy'][] = $task;
                } elseif ($daysUntilDue <= 7) {
                    $kanbanTasks['1_semana'][] = $task;
                } elseif ($daysUntilDue <= 14) {
                    $kanbanTasks['2_semanas'][] = $task;
                }
            }
            
            error_log("Tareas organizadas por columna:");
            error_log("- Vencidas: " . count($kanbanTasks['vencidas']));
            error_log("- Hoy: " . count($kanbanTasks['hoy']));
            error_log("- 1 Semana: " . count($kanbanTasks['1_semana']));
            error_log("- 2 Semanas: " . count($kanbanTasks['2_semanas']));

            return $kanbanTasks;
        } catch (Exception $e) {
            error_log('Error getKanbanTasksForClan: ' . $e->getMessage());
            return [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];
        }
    }

    /**
     * Debug: Verificar filtrado de tareas personales (temporal)
     */
    public function debugTaskFiltering() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
            return;
        }

        if (!$this->userClan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin clan asignado'], 400);
            return;
        }

        $userId = $this->currentUser['user_id'];
        $clanId = $this->userClan['clan_id'];

        // Obtener todas las tareas personales en el clan (sin filtro)
        $stmt = $this->db->prepare("
            SELECT 
                t.task_id,
                t.task_name,
                t.assigned_to_user_id,
                t.created_by_user_id,
                p.project_name,
                p.created_by_user_id as project_creator,
                p.is_personal
            FROM Tasks t
            JOIN Projects p ON t.project_id = p.project_id
            WHERE p.clan_id = ? AND p.is_personal = 1
            ORDER BY t.task_id
        ");
        $stmt->execute([$clanId]);
        $allPersonalTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener tareas personales con filtro
        $filteredTasks = $this->taskModel->getPersonalTasksForClanLeader($userId, $clanId);

        Utils::jsonResponse([
            'success' => true,
            'debug_info' => [
                'current_user_id' => $userId,
                'clan_id' => $clanId,
                'all_personal_tasks_in_clan' => count($allPersonalTasks),
                'filtered_personal_tasks' => count($filteredTasks),
                'all_tasks' => $allPersonalTasks,
                'filtered_tasks' => $filteredTasks
            ]
        ]);
    }

    /**
     * Debug: Verificar estado del usuario y clan
     */
    private function debugUserState() {
        error_log('=== DEBUG USER STATE ===');
        error_log('Current User: ' . print_r($this->currentUser, true));
        error_log('User Clan: ' . print_r($this->userClan, true));
        error_log('Has Clan Leader Access: ' . ($this->hasClanLeaderAccess() ? 'YES' : 'NO'));
        if ($this->currentUser && isset($this->currentUser['user_id'])) {
            error_log('User ID: ' . $this->currentUser['user_id']);
            $clanFromDB = $this->userModel->getUserClan($this->currentUser['user_id']);
            error_log('Clan from DB: ' . print_r($clanFromDB, true));
        }
        error_log('=== END DEBUG ===');
    }

    /**
     * Forzar recarga del clan del usuario
     */
    private function reloadUserClan() {
        if ($this->currentUser && isset($this->currentUser['user_id'])) {
            error_log('Recargando clan del usuario...');
            $this->userClan = $this->userModel->getUserClan($this->currentUser['user_id']);
            if ($this->userClan && isset($this->userClan['clan_id'])) {
                $this->currentUser['clan_id'] = $this->userClan['clan_id'];
                error_log('Clan recargado exitosamente: ' . $this->currentUser['clan_id']);
                return true;
            } else {
                error_log('ERROR: No se pudo recargar el clan del usuario');
                return false;
            }
        }
        return false;
    }

    /**
     * Crear tarea personal para el líder del clan
     */
    public function createPersonalTask() {
        // Establecer headers para JSON
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        $this->requireAuth();
        error_log('Usuario autenticado correctamente');
        
        if (!$this->hasClanLeaderAccess()) {
            error_log('ERROR: Usuario no tiene acceso de líder');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }
        error_log('Usuario tiene acceso de líder correctamente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('ERROR: Método HTTP incorrecto: ' . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        error_log('Método HTTP correcto: POST');

        try {
            // Log para debugging
            error_log('=== INICIO createPersonalTask ===');
            $this->debugUserState();
            error_log('POST data: ' . print_r($_POST, true));
            
            // Verificar que el usuario tenga clan
            if (!$this->userClan || !isset($this->userClan['clan_id'])) {
                error_log('ERROR: Usuario no tiene clan asignado, intentando recargar...');
                if (!$this->reloadUserClan()) {
                    error_log('ERROR: No se pudo recargar el clan del usuario');
                    echo json_encode(['success' => false, 'message' => 'Usuario no tiene clan asignado']);
                    return;
                }
            }
            error_log('Usuario tiene clan ID: ' . $this->userClan['clan_id']);
            
            // Verificar que el currentUser tenga clan_id
            if (!isset($this->currentUser['clan_id'])) {
                error_log('ERROR: currentUser no tiene clan_id, intentando recargar...');
                if (!$this->reloadUserClan()) {
                    error_log('ERROR: No se pudo recargar el clan del usuario');
                    echo json_encode(['success' => false, 'message' => 'Usuario no tiene clan asignado']);
                    return;
                }
            }
            error_log('Current user tiene clan_id: ' . $this->currentUser['clan_id']);
            
            // Verificar que el clan_id coincida entre currentUser y userClan
            if ($this->currentUser['clan_id'] != $this->userClan['clan_id']) {
                error_log('ERROR: clan_id no coincide entre currentUser y userClan');
                error_log('Current user clan_id: ' . $this->currentUser['clan_id']);
                error_log('User clan clan_id: ' . $this->userClan['clan_id']);
                echo json_encode(['success' => false, 'message' => 'Usuario no tiene clan asignado']);
                return;
            }
            error_log('Clan_id coincide correctamente: ' . $this->currentUser['clan_id']);
            
            $taskName = trim($_POST['task_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            $dueDate = $_POST['due_date'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            $userId = (int)($_POST['user_id'] ?? 0);

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
                error_log('ERROR: userId no coincide con currentUser');
                echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
                return;
            }
            error_log('Usuario validado correctamente: ' . $userId);
            
            // Las validaciones de clan y rol ya se hicieron en hasClanLeaderAccess()
            error_log('Validaciones de acceso completadas correctamente');
            


            
            // Crear la tarea personal con solo campos básicos
            $taskData = [
                'task_name' => $taskName,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $dueDate,
                'status' => $status,
                'assigned_to_user_id' => $userId
            ];

            error_log('Task data a crear: ' . print_r($taskData, true));
            
            // Verificar que todos los campos requeridos estén presentes
            if (empty($taskData['task_name']) || empty($taskData['due_date']) || empty($taskData['assigned_to_user_id'])) {
                error_log('ERROR: Campos requeridos faltantes en taskData');
                echo json_encode(['success' => false, 'message' => 'Campos requeridos faltantes']);
                return;
            }
            error_log('Todos los campos requeridos están presentes');

            // Crear tarea personal directamente
            error_log('Llamando a createPersonalTaskSimple...');
            error_log('Task data completo: ' . json_encode($taskData));
            
            try {
                // Verificar que el modelo esté disponible
                if (!$this->taskModel) {
                    throw new Exception('taskModel no está inicializado');
                }
                error_log('taskModel está disponible');
                
                // Verificar que la base de datos esté disponible
                if (!$this->db) {
                    throw new Exception('Base de datos no está disponible');
                }
                error_log('Base de datos está disponible');
                
                $taskId = $this->taskModel->createPersonalTaskSimple($taskData);
                error_log('Resultado de createPersonalTaskSimple: ' . $taskId);
                
                if ($taskId) {
                    $response = [
                        'success' => true, 
                        'message' => 'Tarea personal creada exitosamente',
                        'task_id' => $taskId
                    ];
                    error_log('Respuesta exitosa: ' . json_encode($response));
                    echo json_encode($response);
                } else {
                    $response = ['success' => false, 'message' => 'Error al crear la tarea - revisar logs del servidor'];
                    error_log('Respuesta de error: ' . json_encode($response));
                    echo json_encode($response);
                }
            } catch (Exception $modelException) {
                error_log('Error en createPersonalTaskSimple: ' . $modelException->getMessage());
                error_log('Stack trace: ' . $modelException->getTraceAsString());
                
                $response = ['success' => false, 'message' => 'Error en el modelo: ' . $modelException->getMessage()];
                echo json_encode($response);
            }

        } catch (Exception $e) {
            error_log('Error createPersonalTask: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
        
        // Asegurar que no se ejecute código adicional
        exit;
    }

} 