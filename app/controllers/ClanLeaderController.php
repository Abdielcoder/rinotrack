<?php

use PDO;
use Exception;

class ClanLeaderController {
    private $auth;
    private $userModel;
    private $projectModel;
    private $clanModel;
    private $roleModel;
    private $kpiModel;
    private $taskModel;
    private $subtaskModel;
    private $subtaskAssignmentModel;
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
        $this->subtaskModel = new Subtask();
        
        // Cargar SubtaskAssignment si existe, sino crear una implementación básica
        if (class_exists('SubtaskAssignment')) {
            $this->subtaskAssignmentModel = new SubtaskAssignment();
        } else {
            // Fallback temporal hasta que se cargue el modelo
            require_once __DIR__ . '/../models/SubtaskAssignment.php';
            $this->subtaskAssignmentModel = new SubtaskAssignment();
        }
        
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
            exit;
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
            exit;
        }

        $taskStats = $this->getTaskStats();
        $memberContributions = $this->getMemberContributions();

        // Debug seguro
        error_log("Clan Leader Dashboard Debug:");
        error_log("Clan ID: " . $this->userClan['clan_id']);
        error_log("Task Stats: " . json_encode($taskStats));
        error_log("Member Contributions Count: " . count($memberContributions));

        // Obtener tareas para el tablero Kanban (incluye tareas de otros clanes, EXCLUYENDO tareas personales del líder)
        $kanbanTasks = $this->getKanbanTasksForLeader($this->currentUser['user_id'], $this->userClan['clan_id'], true);
        
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
            exit;
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
            exit;
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
            if (!$this->auth || !$this->auth->isLoggedIn()) {
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan || !isset($this->userClan['clan_id'])) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes un clan asignado'], 403);
                return;
            }
            
            // Consulta corregida usando la estructura real de la base de datos
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.is_active,
                    COALESCE(r.role_name, 'usuario_normal') as role,
                    CASE 
                        WHEN cm.user_id IS NOT NULL THEN 'Miembro del clan'
                        ELSE 'Usuario externo'
                    END as membership_status
                FROM Users u
                LEFT JOIN Clan_Members cm ON u.user_id = cm.user_id AND cm.clan_id = ?
                LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
                LEFT JOIN Roles r ON ur.role_id = r.role_id
                WHERE u.is_active = 1 
                ORDER BY 
                    CASE WHEN cm.user_id IS NOT NULL THEN 0 ELSE 1 END,
                    u.full_name ASC
            ");
            $stmt->execute([$this->userClan['clan_id']]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Utils::jsonResponse([
                'success' => true,
                'users' => $users
            ]);
        } catch (Exception $e) {
            error_log("Error al obtener usuarios disponibles: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse([
                'success' => false,
                'message' => 'Error al obtener usuarios disponibles: ' . $e->getMessage()
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
        // Logs de debug comentados para producción
        // error_log("=== MÉTODO PROJECTS INICIADO ===");
        // error_log("Usuario actual: " . json_encode($this->currentUser));
        // error_log("Clan del usuario: " . json_encode($this->userClan));
        // error_log("¿Tiene acceso de líder?: " . ($this->hasClanLeaderAccess() ? 'SÍ' : 'NO'));
        
        $search = $_GET['search'] ?? '';
        
        // Verificar que el usuario tiene clan asignado
        if (!$this->userClan || !isset($this->userClan['clan_id'])) {
            $allProjects = [];
        } else {
            // Obtener todos los proyectos del clan
            $allProjects = empty($search) ? 
                $this->projectModel->getByClan($this->userClan['clan_id']) : 
                $this->searchProjects($search);
        }
        
        // Los proyectos personales ya están filtrados en el modelo
        $projects = $allProjects;
        
        // Logs de debug comentados para producción
        // error_log("Total de proyectos después del filtro: " . count($projects));
        // error_log("=== FIN DEL FILTRADO ===");
        
        // Log del resultado del filtrado - comentado para producción
        $totalProjects = count($allProjects);
        $filteredProjects = count($projects);
        // error_log("Filtrado de proyectos - Total: $totalProjects, Filtrados: $filteredProjects");
        
        // Log detallado de cada proyecto - comentado para producción
        // foreach ($allProjects as $project) {
        //     $isPersonal = ($project['is_personal'] ?? 0) == 1;
        //     $projectName = $project['project_name'] ?? 'N/A';
        //     error_log("DEBUG Proyecto: '$projectName' - ID: {$project['project_id']}, is_personal: " . ($isPersonal ? 'SÍ' : 'NO') . ", Clan: {$project['clan_id']}");
        // }
        
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
        error_log("=== CREATE PROJECT DEBUG ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("User logged in: " . ($this->auth->isLoggedIn() ? 'YES' : 'NO'));
        error_log("Has clan leader access: " . ($this->hasClanLeaderAccess() ? 'YES' : 'NO'));
        error_log("User clan: " . print_r($this->userClan, true));
        
        try {
            // Verificar autenticación
            if (!$this->auth->isLoggedIn()) {
                error_log("ERROR: User not authenticated");
                Utils::jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
                return;
            }
            
            // Verificar permisos de líder de clan
            if (!$this->hasClanLeaderAccess()) {
                error_log("ERROR: User doesn't have clan leader access");
                Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos de líder de clan'], 403);
                return;
            }
            
            // Verificar que el usuario tiene clan asignado
            if (!$this->userClan) {
                error_log("ERROR: User has no clan assigned");
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
            $timeLimit = !empty($_POST['timeLimit']) ? $_POST['timeLimit'] : null;
            $isEditable = isset($_POST['isEditable']) ? 1 : 0;
            
            error_log("Parsed data: projectName='$projectName', description='$description', timeLimit='$timeLimit', isEditable=$isEditable");
            
            // Validaciones
            if (empty($projectName) || strlen($projectName) < 3) {
                error_log("ERROR: Project name validation failed - empty or less than 3 chars");
                Utils::jsonResponse(['success' => false, 'message' => 'El nombre del proyecto debe tener al menos 3 caracteres'], 400);
                return;
            }
            
            if (empty($description)) {
                error_log("ERROR: Description validation failed - empty");
                Utils::jsonResponse(['success' => false, 'message' => 'La descripción es requerida'], 400);
                return;
            }
            
            // Validar fecha límite si se proporciona
            if ($timeLimit && !strtotime($timeLimit)) {
                error_log("ERROR: Time limit validation failed - invalid date format");
                Utils::jsonResponse(['success' => false, 'message' => 'La fecha límite no es válida'], 400);
                return;
            }
            
            error_log("All validations passed, creating project...");
            
            // Crear proyecto con fecha límite (usando método existente)
            $result = $this->projectModel->create(
                $projectName, 
                $description, 
                $this->userClan['clan_id'], 
                $this->currentUser['user_id'],
                null, // kpiQuarterId 
                0,    // kpiPoints
                'automatic', // taskDistributionMode
                $timeLimit
            );
            
            // TODO: Implementar funcionalidad de is_editable cuando se defina la estructura
            
            error_log("Project creation result: " . ($result ? 'SUCCESS' : 'FAILED'));
            
            if ($result) {
                error_log("SUCCESS: Project created successfully");
                Utils::jsonResponse(['success' => true, 'message' => 'Proyecto creado exitosamente']);
            } else {
                error_log("ERROR: Failed to create project in database");
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear proyecto'], 500);
            }
            
        } catch (Exception $e) {
            error_log("=== EXCEPTION IN CREATE PROJECT ===");
            error_log("Error en ClanLeaderController::createProject: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
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
        $timeLimit = !empty($_POST['timeLimit']) ? $_POST['timeLimit'] : null;
        
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
        }
        
        // Validar fecha límite si se proporciona
        if ($timeLimit && !strtotime($timeLimit)) {
            Utils::jsonResponse(['success' => false, 'message' => 'La fecha límite no es válida'], 400);
            exit;
        }
        
        // Verificar que el proyecto pertenece al clan
        $project = $this->projectModel->findById($projectId);
        if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
            Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }
        
        // Actualizar proyecto con fecha límite
        $result = $this->projectModel->update($projectId, $projectName, $description, $this->userClan['clan_id'], null, $timeLimit);
        
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
     * Actualizar estado de delegación de un proyecto
     */
    public function updateProjectDelegation() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }
        
        $projectId = (int)($_POST['project_id'] ?? 0);
        $allowDelegation = (int)($_POST['allow_delegation'] ?? 0);
        
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
            return;
        }
        
        try {
            // Verificar que el proyecto pertenece al clan del líder
            $stmt = $this->db->prepare("
                SELECT project_id, project_name 
                FROM Projects 
                WHERE project_id = ? AND clan_id = ?
            ");
            $stmt->execute([$projectId, $this->userClan['clan_id']]);
            $project = $stmt->fetch();
            
            if (!$project) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado o sin permisos'], 404);
                return;
            }
            
            // Actualizar el estado de delegación
            $updateStmt = $this->db->prepare("
                UPDATE Projects 
                SET allow_delegation = ? 
                WHERE project_id = ?
            ");
            $updateStmt->execute([$allowDelegation, $projectId]);
            
            $message = $allowDelegation ? 
                "Los colaboradores ahora pueden agregar tareas a '{$project['project_name']}'" : 
                "Se ha desactivado la delegación para '{$project['project_name']}'";
            
            Utils::jsonResponse([
                'success' => true, 
                'message' => $message,
                'project_id' => $projectId,
                'allow_delegation' => $allowDelegation
            ]);
            
        } catch (Exception $e) {
            error_log("Error en updateProjectDelegation: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar delegación'], 500);
        }
    }
    
    /**
     * Gestión de tareas del clan
     */
    public function tasks() {
        // Asegurar autenticación y permisos para evitar accesos nulos
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::redirect('dashboard');
            exit;
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
            $taskProgress = isset($_POST['task_progress']) ? (float)$_POST['task_progress'] : null;
            
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
                $taskStatus, // nuevo parámetro para estado
                $taskProgress // porcentaje de progreso
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
        // Suprimir warnings temporalmente para evitar interferencia con JSON
        error_reporting(E_ALL & ~E_WARNING);
        
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
        
        // Logs EXTREMADAMENTE DETALLADOS de los datos recibidos
        error_log('=== createTask - INICIO DE LOGS ===');
        error_log('createTask - $_POST completo: ' . print_r($_POST, true));
        error_log('createTask - $_FILES completo: ' . print_r($_FILES, true));
        error_log('createTask - Datos recibidos:');
        error_log('  task_title: ' . ($_POST['task_title'] ?? 'NOT SET'));
        error_log('  task_due_date: ' . ($_POST['task_due_date'] ?? 'NOT SET'));
        error_log('  task_project: ' . ($_POST['task_project'] ?? 'NOT SET'));
        error_log('  priority: ' . ($_POST['priority'] ?? 'NOT SET'));
        error_log('  assigned_members: ' . print_r($_POST['assigned_members'] ?? 'NOT SET', true));
        error_log('  subtasks: ' . print_r($_POST['subtasks'] ?? 'NOT SET', true));
        
        // Log SUPER DETALLADO para subtareas
        error_log('=== VERIFICACIÓN DE SUBTAREAS ===');
        error_log('createTask - $_POST completo: ' . print_r($_POST, true));
        error_log('createTask - Claves en $_POST: ' . implode(', ', array_keys($_POST)));
        
        if (isset($_POST['subtasks'])) {
            error_log('createTask - ✅ Subtareas recibidas (raw): ' . $_POST['subtasks']);
            error_log('createTask - ✅ Tipo de subtareas: ' . gettype($_POST['subtasks']));
            error_log('createTask - ✅ Longitud de subtareas: ' . strlen($_POST['subtasks']));
            
            $decodedSubtasks = json_decode($_POST['subtasks'], true);
            error_log('createTask - ✅ Subtareas decodificadas: ' . print_r($decodedSubtasks, true));
            error_log('createTask - ✅ Tipo de subtareas decodificadas: ' . gettype($decodedSubtasks));
            
            if (is_array($decodedSubtasks)) {
                error_log('createTask - ✅ Cantidad de subtareas: ' . count($decodedSubtasks));
                foreach ($decodedSubtasks as $index => $subtask) {
                    error_log("createTask - ✅ Subtarea {$index}: " . print_r($subtask, true));
                }
            } else {
                error_log('createTask - ❌ Subtareas decodificadas NO es array');
                error_log('createTask - ❌ JSON error: ' . json_last_error_msg());
            }
        } else {
            error_log('createTask - ❌ NO se recibieron subtareas en $_POST');
            error_log('createTask - ❌ Claves disponibles en $_POST: ' . implode(', ', array_keys($_POST)));
            
            // Verificar si hay algún campo similar
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'subtask') !== false || strpos($key, 'sub') !== false) {
                    error_log("createTask - 🔍 Campo similar encontrado: {$key} = " . print_r($value, true));
                }
            }
        }
        
        $taskTitle = Utils::sanitizeInput($_POST['task_title'] ?? '');
        $taskDueDate = $_POST['task_due_date'] ?? '';
        
        // Si la fecha está vacía, establecer como null
        if (empty($taskDueDate)) {
            $taskDueDate = null;
        }
        
        $taskProject = (int)($_POST['task_project'] ?? 0);
        $taskDescription = Utils::sanitizeInput($_POST['task_description'] ?? '');
        
        // Campos de recurrencia
        $isRecurrent = isset($_POST['is_recurrent']) ? 1 : 0;
        $recurrenceType = $isRecurrent ? trim($_POST['recurrence_type'] ?? '') : null;
        $recurrenceStart = $isRecurrent ? trim($_POST['recurrence_start_date'] ?? '') : null;
        $recurrenceEnd = $isRecurrent ? trim($_POST['recurrence_end_date'] ?? '') : null;
        
        // Si es recurrente, usar la fecha de inicio de recurrencia como due_date
        if ($isRecurrent && !empty($recurrenceStart)) {
            $taskDueDate = $recurrenceStart;
        }
        
        error_log('createTask - Campos de recurrencia: is_recurrent=' . $isRecurrent . ', type=' . ($recurrenceType ?? 'NULL') . ', start=' . ($recurrenceStart ?? 'NULL') . ', end=' . ($recurrenceEnd ?? 'NULL'));
        error_log('createTask - taskDueDate final: ' . ($taskDueDate ?? 'NULL'));
        // Manejar assigned_members que puede ser un array (desde formulario) o JSON string
        $assignedMembersRaw = $_POST['assigned_members'] ?? [];
        if (is_string($assignedMembersRaw)) {
            $assignedMembers = json_decode($assignedMembersRaw, true) ?: [];
        } else {
            $assignedMembers = $assignedMembersRaw;
        }
        // Manejar subtasks que puede ser un array o JSON string y normalizar datos
        error_log('=== createTask - PROCESAMIENTO DE SUBTAREAS ===');
        $subtasksRaw = $_POST['subtasks'] ?? [];
        error_log('createTask - subtasksRaw: ' . print_r($subtasksRaw, true));
        error_log('createTask - Tipo de subtasksRaw: ' . gettype($subtasksRaw));
        
        if (is_string($subtasksRaw)) {
            error_log('createTask - subtasksRaw es string, decodificando JSON...');
            $subtasks = json_decode($subtasksRaw, true) ?: [];
            error_log('createTask - ✅ subtasks decodificadas de JSON: ' . print_r($subtasks, true));
            error_log('createTask - Tipo de subtasks decodificadas: ' . gettype($subtasks));
        } else {
            error_log('createTask - subtasksRaw NO es string, usando como array');
            $subtasks = $subtasksRaw;
            error_log('createTask - ✅ subtasks ya es array: ' . print_r($subtasks, true));
        }
        
        if (!empty($subtasks) && is_array($subtasks)) {
            error_log('createTask - ✅ Procesando ' . count($subtasks) . ' subtareas');
            $normalized = [];
            foreach ($subtasks as $index => $st) {
                error_log('createTask - 🔄 Procesando subtarea ' . ($index + 1) . ': ' . print_r($st, true));
                
                $title = trim($st['title'] ?? '');
                error_log('createTask - Subtarea ' . ($index + 1) . ' - title: "' . $title . '"');
                
                if ($title === '') { 
                    error_log('createTask - ❌ Subtarea ' . ($index + 1) . ' sin título, saltando');
                    continue; 
                }
                
                $desc = trim($st['description'] ?? '');
                error_log('createTask - Subtarea ' . ($index + 1) . ' - description: "' . $desc . '"');
                
                $perc = isset($st['completion_percentage']) && $st['completion_percentage'] !== '' ? (float)$st['completion_percentage'] : (isset($st['percentage']) && $st['percentage'] !== '' ? (float)$st['percentage'] : 0.0);
                error_log('createTask - Subtarea ' . ($index + 1) . ' - percentage procesado: ' . $perc);
                
                // Validar que el porcentaje esté en el rango correcto
                if ($perc < 0 || $perc > 100) {
                    error_log('createTask - ⚠️ Porcentaje fuera de rango: ' . $perc . ', ajustando a 0.0');
                    $perc = 0.0;
                }
                
                $due  = isset($st['due_date']) && trim((string)$st['due_date']) !== '' ? trim($st['due_date']) : null;
                error_log('createTask - Subtarea ' . ($index + 1) . ' - due_date: ' . ($due ?? 'NULL'));
                
                $prio = in_array(($st['priority'] ?? 'medium'), ['low','medium','high','urgent'], true) ? ($st['priority'] ?? 'medium') : 'medium';
                error_log('createTask - Subtarea ' . ($index + 1) . ' - priority: ' . $prio);
                
                $auid = isset($st['assigned_to_user_id']) && $st['assigned_to_user_id'] !== '' ? (int)$st['assigned_to_user_id'] : null;
                error_log('createTask - Subtarea ' . ($index + 1) . ' - assigned_to_user_id: ' . ($auid ?? 'NULL'));
                
                $normalized[] = [
                    'title' => $title,
                    'description' => $desc,
                    'completion_percentage' => $perc,
                    'due_date' => $due,
                    'priority' => $prio,
                    'assigned_to_user_id' => $auid,
                ];
                error_log('createTask - ✅ Subtarea ' . ($index + 1) . ' normalizada: ' . print_r($normalized[count($normalized)-1], true));
            }
            $subtasks = $normalized;
            error_log('createTask - ✅ Total de subtareas normalizadas: ' . count($subtasks));
        } else {
            error_log('createTask - ❌ No hay subtareas para procesar o no es un array válido');
            error_log('createTask - ❌ subtasks está vacío: ' . (empty($subtasks) ? 'SÍ' : 'NO'));
            error_log('createTask - ❌ subtasks es array: ' . (is_array($subtasks) ? 'SÍ' : 'NO'));
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
        
        // Validar título siempre
        if (empty($taskTitle)) {
            error_log('createTask - Error: Título vacío');
            Utils::jsonResponse(['success' => false, 'message' => 'El título es requerido'], 400);
            return;
        }
        
        // Validar fecha límite SOLO si NO es recurrente
        if (!$isRecurrent && empty($taskDueDate)) {
            error_log('createTask - Error: Fecha vacía y no es recurrente');
            Utils::jsonResponse(['success' => false, 'message' => 'La fecha límite es requerida para tareas no recurrentes'], 400);
            return;
        }
        
        // Si es recurrente, validar que tenga fecha de inicio
        if ($isRecurrent && empty($recurrenceStart)) {
            error_log('createTask - Error: Es recurrente pero sin fecha de inicio');
            Utils::jsonResponse(['success' => false, 'message' => 'La fecha de inicio es requerida para tareas recurrentes'], 400);
            return;
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
            
            error_log('=== createTask - LLAMADA AL MODELO ===');
            error_log('createTask - Llamando a createAdvanced con subtasks: ' . print_r($subtasks, true));
            error_log('createTask - Cantidad de subtareas a enviar: ' . count($subtasks));
            error_log('createTask - taskProject: ' . $taskProject);
            error_log('createTask - taskTitle: ' . $taskTitle);
            error_log('createTask - taskDueDate: ' . ($taskDueDate ?? 'NULL'));
            error_log('createTask - clan_id: ' . $this->userClan['clan_id']);
            error_log('createTask - priority: ' . $priority);
            error_log('createTask - createdByUserId: ' . $createdByUserId);
            error_log('createTask - assignedMembers: ' . print_r($assignedMembers, true));
            error_log('createTask - labels: ' . print_r($labels, true));
            
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
            
            $message = 'Tarea creada exitosamente';
            $generatedInstances = 0;
            
            // Si es tarea recurrente, generar instancias inmediatamente
            if ($isRecurrent) {
                // Primero actualizar la tarea creada con los campos de recurrencia
                $updateStmt = $this->db->prepare("
                    UPDATE Tasks 
                    SET is_recurrent = ?, recurrence_type = ?, recurrence_start_date = ?, 
                        recurrence_end_date = ?, last_generated_date = ?
                    WHERE task_id = ?
                ");
                $updateStmt->execute([
                    1, $recurrenceType, $recurrenceStart, $recurrenceEnd, 
                    $recurrenceStart, $taskId
                ]);
                
                // Generar instancias
                $generatedInstances = $this->taskModel->generateInstancesForTask($taskId);
                if ($generatedInstances > 0) {
                    $message .= ". Se generaron $generatedInstances instancias recurrentes";
                }
                
                error_log("createTask - Instancias recurrentes generadas: $generatedInstances");
            }
            
            // Notificación de asignación si está activa
            try { (new NotificationService())->notifyTaskAssigned((int)$taskId, $assignedMembers); } catch (Exception $e) { error_log('Notif error (clan_leader task_assigned): ' . $e->getMessage()); }
            Utils::jsonResponse([
                'success' => true, 
                'message' => $message, 
                'task_id' => $taskId,
                'generated_instances' => $generatedInstances
            ]);
            
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
        
        // Si no es líder de clan, redirigir a la vista de miembro
        if (!$this->hasClanLeaderAccess()) {
            header("Location: ?route=clan_member/task-details&task_id=" . $taskId);
            exit;
        }
        
        try {
            // Obtener información completa de la tarea
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            // Verificar acceso: pertenece al clan del líder, la tarea está asignada al usuario actual, 
            // o el usuario es miembro del clan y la tarea tiene asignados miembros del clan
            $project = $this->projectModel->findById($task['project_id']);
            $isAssigned = $this->isTaskAssignedToUser($taskId, $this->currentUser['user_id']);
            $isProjectFromClan = ((int)$project['clan_id'] === (int)$this->userClan['clan_id']);
            
            // Verificar si es miembro del clan y hay miembros del clan asignados a la tarea
            $hasTeamMembersAssigned = false;
            if (!$isProjectFromClan && !$isAssigned && $this->userClan) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM Task_Assignments ta 
                    INNER JOIN Clan_Members cm ON ta.user_id = cm.user_id 
                    WHERE ta.task_id = ? AND cm.clan_id = ?
                    UNION
                    SELECT COUNT(*) as count 
                    FROM Tasks t 
                    INNER JOIN Clan_Members cm ON t.assigned_to_user_id = cm.user_id 
                    WHERE t.task_id = ? AND cm.clan_id = ?
                ");
                $stmt->execute([$taskId, $this->userClan['clan_id'], $taskId, $this->userClan['clan_id']]);
                $results = $stmt->fetchAll();
                $hasTeamMembersAssigned = array_sum(array_column($results, 'count')) > 0;
            }
            
            if (!$project || (!$isProjectFromClan && !$isAssigned && !$hasTeamMembersAssigned)) {
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
     * Actualizar progreso de tarea principal
     */
    public function updateTaskProgress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        $completionPercentage = isset($_POST['completion_percentage']) ? (float)$_POST['completion_percentage'] : null;
        
        if ($taskId <= 0 || $completionPercentage === null) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar que la tarea pertenece al clan
            $stmt = $this->db->prepare("
                SELECT t.*, p.clan_id
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                WHERE t.task_id = ?
            ");
            $stmt->execute([$taskId]);
            $task = $stmt->fetch();
            
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }
            
            if ($task['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            
            // Actualizar solo el progreso (sin cambiar el estado)
            $result = $this->taskModel->update(
                $taskId,
                $task['task_name'],
                $task['description'],
                $task['assigned_to_user_id'],
                $task['priority'],
                $task['due_date'],
                $task['assigned_percentage'],
                null, // no cambiar estado
                $completionPercentage
            );
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Progreso actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar progreso'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en updateTaskProgress: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Actualizar progreso de subtarea
     */
    public function updateSubtaskProgress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $completionPercentage = isset($_POST['completion_percentage']) ? (float)$_POST['completion_percentage'] : null;
        
        if ($subtaskId <= 0 || $completionPercentage === null) {
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
            
            // Actualizar solo el progreso
            $result = $this->taskModel->updateSubtaskStatus(
                $subtaskId, 
                null, // no cambiar estado
                $completionPercentage, 
                $this->currentUser['user_id']
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
    
    /**
     * Actualizar estado de subtarea
     */
    public function updateSubtaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
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
     * Agregar una subtarea individual
     */
    public function addSubtask() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
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

            // Verificar que el usuario tiene acceso a la tarea
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
            }
            
            // Permitir si es líder del clan del proyecto O si está asignado a la tarea
            $isProjectClanLeader = ($project['clan_id'] == $this->userClan['clan_id']);
            $isTaskAssigned = ($task['assigned_to_user_id'] == $this->currentUser['user_id']);
            
            // Verificar también si está asignado via Task_Assignments
            if (!$isTaskAssigned) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM Task_Assignments WHERE task_id = ? AND user_id = ?");
                $stmt->execute([$taskId, $this->currentUser['user_id']]);
                $isTaskAssigned = $stmt->fetchColumn() > 0;
            }
            
            if (!$isProjectClanLeader && !$isTaskAssigned) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para agregar subtareas a esta tarea'], 403);
            }

            // Crear la subtarea usando el método existente
            $subtaskId = $this->taskModel->createSubtaskAdvanced(
                $taskId,
                $title,
                $this->currentUser['user_id'],
                $description,
                $completionPercentage,
                $dueDate, // fecha límite
                'medium', // prioridad por defecto
                null  // sin usuario asignado inicialmente
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
            error_log('Error al crear subtarea: ' . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Añadir subtareas a una tarea existente
     */
    public function addSubtasksToTask() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
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
            // Verificar que la tarea existe y pertenece al clan
            $task = $this->taskModel->findById($taskId);
            if (!$task) {
                Utils::jsonResponse(['success' => false, 'message' => 'Tarea no encontrada'], 404);
            }

            // Verificar que la tarea pertenece a un proyecto del clan
            $project = $this->projectModel->findById($task['project_id']);
            if (!$project || $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }

            $createdByUserId = $this->currentUser['user_id'];
            
            // Crear cada subtarea
            $createdCount = 0;
            foreach ($subtasks as $subtaskTitle) {
                $subtaskTitle = trim(Utils::sanitizeInput($subtaskTitle));
                if ($subtaskTitle !== '') {
                    $subtaskId = $this->taskModel->createSubtaskAdvanced(
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
            error_log('Error al añadir subtareas (clan leader): ' . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
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
        // Debug logging
        error_log("=== DELETE TASK DEBUG ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("Raw input: " . file_get_contents('php://input'));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $taskId = (int)($_POST['task_id'] ?? 0);
        error_log("Parsed task_id: " . $taskId);
        
        if ($taskId <= 0) {
            error_log("Task ID inválido: " . $taskId);
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
            $result = $this->taskModel->addCollaborators($taskId, $userIdsArray, $this->currentUser['user_id']);
            
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
            
            // Debug logging para identificar el problema
            error_log("=== TOGGLE TASK STATUS DEBUG ===");
            error_log("POST data: " . print_r($_POST, true));
            error_log("Raw input: " . file_get_contents('php://input'));
            
            $taskId = (int)($_POST['task_id'] ?? 0);
            $newStatus = $_POST['status'] ?? '';
            $isCompleted = ($newStatus === 'completed');
            
            error_log("Parsed taskId: " . $taskId);
            error_log("Parsed newStatus: " . $newStatus);
            error_log("isCompleted: " . ($isCompleted ? 'true' : 'false'));
            
            if ($taskId <= 0) {
                error_log("Task ID validation failed. taskId: " . $taskId);
                Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido - ID recibido: ' . $taskId], 400);
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
            
            // Calcular el completion_percentage basado en el estado
            $completionPercentage = ($newStatus === 'completed') ? 100 : 0;
            
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
            
            // SIEMPRE actualizar el completion_percentage después de cambiar el estado
            if ($result) {
                $this->taskModel->updateTaskProgress($taskId, $completionPercentage);
                error_log("Progreso actualizado: task_id=$taskId, completion_percentage=$completionPercentage%");
            }
            
            if ($result) {
                // Si se completó la tarea, actualizar el progreso del proyecto
                if ($newStatus === 'completed') {
                    $this->projectModel->updateProgress($task['project_id']);
                }
                
                Utils::jsonResponse([
                    'success' => true, 
                    'message' => 'Estado de tarea actualizado (status, is_completed y completion_percentage)',
                    'completion_percentage' => $completionPercentage,
                    'status' => $newStatus,
                    'is_completed' => ($newStatus === 'completed') ? 1 : 0
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar tarea'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Error en ClanLeaderController::toggleTaskStatus: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
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
        
        // Logging detallado
        error_log("=== SIMPLE TOGGLE SUBTASK ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        // Obtener parámetros
        $subtaskId = (int)($_POST['subtask_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        
        error_log("Final subtaskId: " . $subtaskId);
        error_log("Final status: " . $status);
        
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
                return;
            }
            
            // Calcular completion_percentage - 100% si completada, 0% si no completada
            $completionPercentage = ($status === 'completed') ? 100 : 0;
            
            error_log("Actualizando subtarea con: status=$status, completion_percentage=$completionPercentage%");
            
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
                    'message' => 'Subtarea actualizada correctamente (status y completion_percentage)',
                    'completion_percentage' => $completionPercentage,
                    'status' => $status
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la subtarea']);
            }
            
        } catch (Exception $e) {
            error_log("Error en simpleToggleSubtask: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Método simple para cambiar estado de tarea
     */
    public function simpleToggleTask() {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        
        // Logging detallado
        error_log("=== SIMPLE TOGGLE TASK ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST: " . print_r($_POST, true));
        error_log("GET: " . print_r($_GET, true));
        error_log("Raw input: " . file_get_contents('php://input'));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        // Intentar obtener task_id de diferentes formas
        $taskId = null;
        if (isset($_POST['task_id'])) {
            $taskId = (int)$_POST['task_id'];
            error_log("task_id desde POST: " . $taskId);
        } else {
            // Intentar desde raw input
            $rawInput = file_get_contents('php://input');
            parse_str($rawInput, $parsedData);
            if (isset($parsedData['task_id'])) {
                $taskId = (int)$parsedData['task_id'];
                error_log("task_id desde raw input: " . $taskId);
            }
        }
        
        $status = $_POST['status'] ?? $parsedData['status'] ?? '';
        
        error_log("Final taskId: " . $taskId);
        error_log("Final status: " . $status);
        
        if (!$taskId || $taskId <= 0) {
            $debugInfo = [
                'received_task_id' => $_POST['task_id'] ?? 'no POST task_id',
                'parsed_task_id' => $parsedData['task_id'] ?? 'no parsed task_id',
                'final_task_id' => $taskId,
                'post_data' => $_POST,
                'parsed_data' => $parsedData ?? null,
                'raw_input' => file_get_contents('php://input'),
                'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'no content type'
            ];
            
            error_log("Task ID validation failed. Debug info: " . print_r($debugInfo, true));
            
            echo json_encode([
                'success' => false, 
                'message' => 'ID de tarea inválido. Recibido: ' . var_export($taskId, true) . '. Verifique que está enviando un número válido mayor que 0.',
                'debug' => $debugInfo
            ]);
            exit;
        }
        
        if (!in_array($status, ['pending', 'completed', 'in_progress', 'cancelled'])) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido: ' . $status]);
            exit;
        }
        
        try {
            // Buscar la tarea usando el modelo
            $task = $this->taskModel->findById($taskId);
            
            if (!$task) {
                echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
                return;
            }
            
            error_log("Tarea encontrada: " . print_r($task, true));
            
            // Calcular completion_percentage - 100% si completada, 0% si no completada
            $completionPercentage = ($status === 'completed') ? 100 : 0;
            
            error_log("Actualizando tarea con: status=$status, completion_percentage=$completionPercentage%");
            
            // Usar el método update del modelo que maneja correctamente status, is_completed, completed_at, etc.
            $result = $this->taskModel->update(
                $taskId,
                $task['task_name'],
                $task['description'],
                $task['assigned_to_user_id'],
                $task['priority'],
                $task['due_date'],
                $task['assigned_percentage'],
                $status  // Este parámetro hará que se actualice status, is_completed y completed_at automáticamente
            );
            
            // SIEMPRE actualizar el completion_percentage después de cambiar el estado
            if ($result) {
                $this->taskModel->updateTaskProgress($taskId, $completionPercentage);
                error_log("Progreso actualizado: task_id=$taskId, completion_percentage=$completionPercentage%");
            }
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarea actualizada correctamente (status, is_completed y completion_percentage)',
                    'completion_percentage' => $completionPercentage,
                    'status' => $status,
                    'is_completed' => ($status === 'completed') ? 1 : 0
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la tarea']);
            }
            
        } catch (Exception $e) {
            error_log("Error en simpleToggleTask: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
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
        
        // TEMPORAL: Sin verificaciones para debug
        error_log("collaboratorAvailability: ACCESO DIRECTO SIN VERIFICACIONES - view: $view");
        
        // Obtener datos de disponibilidad
        $availability_data = [];
        
        // TEMPORAL: Usar clan_id = 1 si no hay userClan
        $clanId = $this->userClan['clan_id'] ?? 1;
        error_log("collaboratorAvailability: Usando clan_id: $clanId");
        
        $members = $this->clanModel->getMembers($clanId);
        
        // Definir currentUserId una sola vez
        $currentUserId = $this->currentUser['user_id'] ?? 1;
        
        foreach ($members as $member) {
            $activeTasks = $this->taskModel->getActiveTasksByUserForClanLeader($member['user_id'], $currentUserId);
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
        $clanTasksData = $this->taskModel->getAllTasksByClanStrict($clanId, 1, 10000, '', '');
        $clanTasks = $clanTasksData['tasks'] ?? [];
        
        // Obtener tareas personales del líder actual
        $ownPersonalTasks = $this->taskModel->getPersonalTasksForClanLeader(
            $currentUserId, 
            $clanId
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
        
        // Obtener proyectos del clan para mostrar en la vista
        try {
            $projects = $this->projectModel->getByClan($clanId) ?: [];
        } catch (Exception $e) {
            error_log("Error obteniendo proyectos: " . $e->getMessage());
            $projects = [];
        }
        
        $data = [
            'availability_data' => $availability_data,
            'summary' => $summary,
            'all_tasks' => $allTasks,
            'members' => $members,
            'projects' => $projects,
            'view' => $view,
            'currentPage' => 'clan_leader',
            'user' => $this->currentUser ?? ['user_id' => 1, 'full_name' => 'Usuario Temporal'],
            'clan' => $this->userClan ?? ['clan_id' => $clanId, 'clan_name' => 'Clan Temporal']
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
     * Obtener datos de una tarea para clonación
     */
    public function getTaskData() {
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
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

            // Obtener proyectos disponibles para el líder (proyectos del clan)
            $projects = $this->projectModel->getByClan($this->userClan['clan_id']);

            Utils::jsonResponse([
                'success' => true,
                'task' => $task,
                'projects' => $projects
            ]);

        } catch (Exception $e) {
            error_log("Error en getTaskData (ClanLeader): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Clonar una tarea
     */
    public function cloneTask() {
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
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

            // Verificar que el proyecto destino existe y pertenece al clan del líder
            $targetProject = $this->projectModel->findById($projectId);
            
            if (!$targetProject) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto destino no encontrado'], 404);
                return;
            }

            if ((int)$targetProject['clan_id'] !== (int)$this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Solo puedes clonar a proyectos de tu clan'], 403);
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
            error_log("Error en cloneTask (ClanLeader): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener datos de proyecto para clonación
     */
    public function getProjectData() {
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        $projectId = (int)($_GET['project_id'] ?? 0);
        
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
            return;
        }

        try {
            // Obtener datos del proyecto
            $project = $this->projectModel->findById($projectId);
            
            if (!$project) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
                return;
            }

            // Verificar que el proyecto pertenezca al clan del líder
            if ($this->userClan && $project['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para clonar este proyecto'], 403);
                return;
            }

            Utils::jsonResponse([
                'success' => true,
                'project' => $project
            ]);

        } catch (Exception $e) {
            error_log("Error en getProjectData (ClanLeader): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Clonar proyecto
     */
    public function cloneProject() {
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $originalProjectId = (int)($_POST['originalProjectId'] ?? 0);
        $projectName = trim($_POST['project_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $cloneTasks = ($_POST['clone_tasks'] ?? '0') === '1';

        if ($originalProjectId <= 0 || empty($projectName)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
            return;
        }

        try {
            // Obtener proyecto original
            $originalProject = $this->projectModel->findById($originalProjectId);
            
            if (!$originalProject) {
                Utils::jsonResponse(['success' => false, 'message' => 'Proyecto original no encontrado'], 404);
                return;
            }

            // Verificar permisos
            if ($this->userClan && $originalProject['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para clonar este proyecto'], 403);
                return;
            }

            // Crear el nuevo proyecto
            $newProjectId = $this->projectModel->create(
                $projectName,
                $description,
                $originalProject['clan_id'],
                $this->currentUser['user_id']
            );

            if (!$newProjectId) {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el proyecto'], 500);
                return;
            }

            // Clonar tareas si se solicita
            if ($cloneTasks) {
                $originalTasks = $this->taskModel->getByProject($originalProjectId);
                
                foreach ($originalTasks as $task) {
                    if ($task['is_subtask'] == 0) { // Solo tareas principales
                        $newTaskId = $this->taskModel->create(
                            $newProjectId,
                            $task['task_name'],
                            $task['description'] ?? '',
                            $this->currentUser['user_id'],
                            $task['priority'] ?? 'medium',
                            $task['due_date'] ?? null,
                            $this->currentUser['user_id']
                        );

                        // Clonar subtareas si existen
                        if ($newTaskId) {
                            $subtasks = $this->taskModel->getSubtasks($task['task_id']);
                            foreach ($subtasks as $subtask) {
                                $this->taskModel->createSubtaskAdvanced(
                                    $newTaskId,
                                    $subtask['title'],
                                    $this->currentUser['user_id'],
                                    $subtask['description'] ?? '',
                                    0,
                                    $subtask['due_date'] ?? null,
                                    $subtask['priority'] ?? 'medium',
                                    $this->currentUser['user_id']
                                );
                            }
                        }
                    }
                }
            }

            Utils::jsonResponse([
                'success' => true,
                'message' => 'Proyecto clonado exitosamente',
                'new_project_id' => $newProjectId
            ]);

        } catch (Exception $e) {
            error_log("Error en cloneProject (ClanLeader): " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener MIS tareas (personales + asignadas a mí)
     */
    public function getMyTasks() {
        // Asegurar que la respuesta sea JSON
        header('Content-Type: application/json');
        
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        try {
            $userId = $this->currentUser['user_id'];
            $clanId = $this->currentUser['clan_id'];
            
            if (!$userId || !$clanId) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario o clan no válido'], 400);
                return;
            }
            
            // Usar consulta directa para tener control total
            $tasks = null;
            
            // Usar consulta directa simplificada
            if (true) {
                $db = Database::getInstance();
                
                $query = "
                    SELECT DISTINCT
                        t.task_id,
                        t.task_name,
                        t.description,
                        t.status,
                        t.priority,
                        t.due_date,
                        t.completion_percentage,
                        t.created_by_user_id,
                        p.project_id,
                        p.project_name,
                        p.is_personal,
                        u.full_name as assigned_user_name,
                        DATEDIFF(t.due_date, CURDATE()) as days_until_due
                    FROM Tasks t
                    INNER JOIN Projects p ON t.project_id = p.project_id
                    LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                    WHERE 
                        p.clan_id = :clan_id
                        AND (t.is_subtask = 0 OR t.is_subtask IS NULL)
                        AND (
                            (p.is_personal = 1 AND p.created_by_user_id = :user_id1)
                            OR
                            (t.assigned_to_user_id = :user_id2)
                        )
                    ORDER BY t.due_date ASC
                    LIMIT 200
                ";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':clan_id', $clanId, PDO::PARAM_INT);
                $stmt->bindParam(':user_id1', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':user_id2', $userId, PDO::PARAM_INT);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error ejecutando consulta de tareas");
                }
                
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Asegurar que tasks es un array
            if (!is_array($tasks)) {
                $tasks = [];
            }

            Utils::jsonResponse([
                'success' => true,
                'tasks' => $tasks,
                'total' => count($tasks)
            ]);

        } catch (Exception $e) {
            error_log("Error en getMyTasks: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error al obtener tareas'], 500);
        }
    }
    
    /**
     * Obtener tareas del equipo/clan (TODAS las tareas del clan excepto personales)
     */
    public function getTeamTasks() {
        // Asegurar que la respuesta sea JSON
        header('Content-Type: application/json');
        
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        try {
            $clanId = $this->currentUser['clan_id'];
            $userId = $this->currentUser['user_id'];
            
            if (!$clanId) {
                Utils::jsonResponse(['success' => false, 'message' => 'Usuario sin clan asignado'], 400);
                return;
            }

            $db = Database::getInstance();
            
            // Consulta simplificada para tareas del equipo
            $query = "
                SELECT DISTINCT
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.status,
                    t.priority,
                    t.due_date,
                    t.completion_percentage,
                    t.created_by_user_id,
                    p.project_id,
                    p.project_name,
                    u.full_name as assigned_user_name,
                    DATEDIFF(t.due_date, CURDATE()) as days_until_due
                FROM Tasks t
                INNER JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                WHERE 
                    p.clan_id = :clan_id
                    AND (p.is_personal = 0 OR p.is_personal IS NULL)
                    AND (t.is_subtask = 0 OR t.is_subtask IS NULL)
                ORDER BY t.due_date ASC
                LIMIT 200
            ";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':clan_id', $clanId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta de tareas del equipo");
            }
            
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que tasks es un array
            if (!is_array($tasks)) {
                $tasks = [];
            }

            Utils::jsonResponse([
                'success' => true,
                'tasks' => $tasks,
                'total' => count($tasks)
            ]);

        } catch (Exception $e) {
            error_log("Error en getTeamTasks: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtener mis tareas organizadas para Kanban (solo las tareas donde el líder es asignado)
     */
    public function getMyKanbanTasks() {
        $this->requireAuth();
        
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            return;
        }

        try {
            $userId = $this->currentUser['user_id'];
            error_log("DEBUG getMyKanbanTasks - userId: $userId");
            
            if (!$userId) {
                throw new Exception("Usuario no válido");
            }
            
            // Usar el método existente getUserTasks que ya sabemos que funciona, pero filtrar completadas
            $userTasksData = $this->taskModel->getUserTasks($userId, 1, 100, '', '');
            $allTasks = $userTasksData['tasks'] ?? [];
            
            // Filtrar tareas completadas (tanto por status como por completion_percentage)
            $allTasks = array_filter($allTasks, function($task) {
                return $task['status'] !== 'completed' && ($task['completion_percentage'] ?? 0) < 100;
            });
            
            // También obtener subtareas asignadas al usuario que no estén completadas
            $subtaskStmt = $this->db->prepare("
                SELECT 
                    s.subtask_id as task_id,
                    s.title as task_name,
                    s.status,
                    s.due_date,
                    s.completion_percentage,
                    p.project_name,
                    u.full_name as assigned_to_name,
                    CASE 
                        WHEN s.due_date IS NULL THEN 999
                        ELSE DATEDIFF(s.due_date, CURDATE())
                    END as days_until_due,
                    'subtask' as item_type,
                    0 as is_completed
                FROM Subtasks s
                LEFT JOIN Tasks t ON s.task_id = t.task_id
                LEFT JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u ON s.assigned_to_user_id = u.user_id
                WHERE s.assigned_to_user_id = ? 
                  AND s.status != 'completed' 
                  AND s.completion_percentage < 100
                ORDER BY s.due_date ASC
            ");
            
            $subtaskStmt->execute([$userId]);
            $subtasks = $subtaskStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Combinar tareas y subtareas
            $allTasks = array_merge($allTasks, $subtasks);
            
            error_log("DEBUG getMyKanbanTasks - Total tareas encontradas: " . count($allTasks) . " (incluyendo " . count($subtasks) . " subtareas)");
            
            // Procesar las tareas para agregar days_until_due si no existe
            foreach ($allTasks as &$task) {
                if (!isset($task['days_until_due'])) {
                    if ($task['due_date']) {
                        $task['days_until_due'] = (new DateTime($task['due_date']))->diff(new DateTime())->format('%r%a');
                    } else {
                        $task['days_until_due'] = 999;
                    }
                }
                $task['item_type'] = 'task';
                $task['is_completed'] = 0;
            }
            
            // Organizar tareas en columnas Kanban
            $kanbanTasks = [
                'vencidas' => [],
                'hoy' => [],
                'semana1' => [],
                'semana2' => []
            ];
            
            foreach ($allTasks as $task) {
                $daysUntilDue = (int)($task['days_until_due'] ?? 999);
                
                if ($daysUntilDue < 0) {
                    $kanbanTasks['vencidas'][] = $task;
                } elseif ($daysUntilDue == 0) {
                    $kanbanTasks['hoy'][] = $task;
                } elseif ($daysUntilDue <= 7) {
                    $kanbanTasks['semana1'][] = $task;
                } else {
                    $kanbanTasks['semana2'][] = $task;
                }
            }
            
            error_log("DEBUG getMyKanbanTasks - Kanban distribution: vencidas=" . count($kanbanTasks['vencidas']) . ", hoy=" . count($kanbanTasks['hoy']) . ", semana1=" . count($kanbanTasks['semana1']) . ", semana2=" . count($kanbanTasks['semana2']));

            Utils::jsonResponse([
                'success' => true,
                'kanbanTasks' => $kanbanTasks,
                'total' => count($allTasks)
            ]);

        } catch (Exception $e) {
            error_log("Error en getMyKanbanTasks (ClanLeader): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
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
        
        // Agregar estilos CSS específicos del clan leader
        $additionalCSS = [
            Utils::asset('assets/css/clan-leader.css')
        ];
        
        // Agregar JavaScript específico del clan leader
        $additionalJS = [
            Utils::asset('assets/js/clan-leader.js')
        ];
        
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
     * Obtener tareas para el tablero Kanban del líder (incluye tareas de otros clanes donde hay miembros asignados)
     */
    private function getKanbanTasksForLeader($userId, $primaryClanId, $excludePersonalTasks = false) {
        try {
            
            // Obtener TODAS las tareas donde miembros del clan estén asignados + tareas del clan
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
                    p.clan_id,
                    c.clan_name,
                    u_assigned.full_name as assigned_to_name,
                    u_assigned.username as assigned_to_username,
                    CASE 
                        WHEN t.due_date IS NULL THEN 999
                        ELSE DATEDIFF(t.due_date, CURDATE())
                    END as days_until_due,
                    CASE 
                        WHEN p.clan_id = ? THEN 1
                        ELSE 0
                    END as is_primary_clan,
                    'task' as item_type
                 FROM Tasks t
                 INNER JOIN Projects p ON p.project_id = t.project_id
                 LEFT JOIN Clans c ON p.clan_id = c.clan_id
                 LEFT JOIN Task_Assignments ta ON ta.task_id = t.task_id
                 LEFT JOIN Clan_Members cm ON (t.assigned_to_user_id = cm.user_id OR ta.user_id = cm.user_id)
                 LEFT JOIN Users u_assigned ON t.assigned_to_user_id = u_assigned.user_id
                 WHERE (
                        -- Tareas del clan principal (todas)
                        (p.clan_id = ? AND (p.is_personal IS NULL OR p.is_personal != 1))
                        " . ($excludePersonalTasks ? "" : "
                        -- Tareas personales del clan principal asignadas a miembros del clan
                        OR (p.clan_id = ? AND p.is_personal = 1 AND p.created_by_user_id = ? AND (
                            t.assigned_to_user_id IN (SELECT user_id FROM Clan_Members WHERE clan_id = ?) 
                            OR ta.user_id IN (SELECT user_id FROM Clan_Members WHERE clan_id = ?)
                            OR t.assigned_to_user_id = ?
                            OR t.created_by_user_id = ?
                        ))") . "
                        -- Tareas especiales del clan principal asignadas a miembros del clan
                        OR (p.clan_id = ? AND p.project_name IN ('Tareas Recurrentes', 'Tareas Eventuales') AND (
                            t.assigned_to_user_id IN (SELECT user_id FROM Clan_Members WHERE clan_id = ?)
                            OR ta.user_id IN (SELECT user_id FROM Clan_Members WHERE clan_id = ?)
                            OR t.assigned_to_user_id = ?
                            OR ta.user_id = ?
                        ))
                        -- Tareas de OTROS clanes donde CUALQUIER miembro del clan esté asignado
                        OR (p.clan_id != ? AND cm.clan_id = ? AND cm.clan_id IS NOT NULL)
                       )
                   AND t.is_subtask = 0
                   AND t.status != 'completed'
                   AND t.status != 'cancelled'
                 GROUP BY t.task_id
                 ORDER BY is_primary_clan DESC, t.due_date ASC, t.task_id ASC"
            );
            
            if ($excludePersonalTasks) {
                $params = [
                    $primaryClanId, // is_primary_clan CASE
                    $primaryClanId, // tareas del clan principal
                    $primaryClanId, // tareas especiales del clan principal  
                    $primaryClanId, $primaryClanId, // miembros asignados a tareas especiales
                    $userId, $userId, // líder asignado a tareas especiales
                    $primaryClanId, // para excluir clan principal de otros clanes
                    $primaryClanId // clan de los miembros asignados en otros clanes
                ];
            } else {
                $params = [
                    $primaryClanId, // is_primary_clan CASE
                    $primaryClanId, // tareas del clan principal
                    $primaryClanId, // tareas personales del clan principal
                    $userId, // creador de tareas personales
                    $primaryClanId, $primaryClanId, // miembros asignados a tareas personales
                    $userId, $userId, // líder asignado a tareas personales
                    $primaryClanId, // tareas especiales del clan principal  
                    $primaryClanId, $primaryClanId, // miembros asignados a tareas especiales
                    $userId, $userId, // líder asignado a tareas especiales
                    $primaryClanId, // para excluir clan principal de otros clanes
                    $primaryClanId // clan de los miembros asignados en otros clanes
                ];
            }
            
            $stmt->execute($params);
            $allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener subtareas asignadas a miembros del clan
            $subtaskStmt = $this->db->prepare(
                "SELECT 
                    s.subtask_id as task_id,
                    s.task_id as parent_task_id,
                    s.title as task_name,
                    s.description,
                    s.due_date,
                    'medium' as priority,  -- Las subtareas tendrán prioridad media por defecto
                    s.status,
                    s.completion_percentage,
                    0 as automatic_points,
                    CONCAT('Subtarea de: ', t.task_name) as project_name,
                    t.project_id,
                    p.clan_id,
                    c.clan_name,
                    u_assigned.full_name as assigned_to_name,
                    u_assigned.username as assigned_to_username,
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
                 LEFT JOIN Users u_assigned ON s.assigned_to_user_id = u_assigned.user_id
                 LEFT JOIN Clan_Members cm ON s.assigned_to_user_id = cm.user_id
                 WHERE s.status != 'completed' AND s.completion_percentage < 100
                   AND (
                       -- Subtareas del clan principal
                       (p.clan_id = ? AND cm.clan_id = ?)
                       -- Subtareas de otros clanes donde miembros del clan estén asignados
                       OR (p.clan_id != ? AND cm.clan_id = ?)
                   )
                 ORDER BY s.due_date ASC"
            );
            
            $subtaskParams = [
                $primaryClanId, // is_primary_clan CASE
                $primaryClanId, // subtareas del clan principal
                $primaryClanId, // miembros del clan principal
                $primaryClanId, // para excluir clan principal de otros clanes
                $primaryClanId  // clan de los miembros asignados en otros clanes
            ];
            
            $subtaskStmt->execute($subtaskParams);
            $allSubtasks = $subtaskStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Combinar tareas y subtareas
            $allTasks = array_merge($allTasks, $allSubtasks);
            
            // Organizar en columnas Kanban
            $kanbanTasks = [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];

            foreach ($allTasks as $task) {
                $daysUntilDue = (int)$task['days_until_due'];
                
                if ($daysUntilDue < 0) {
                    $kanbanTasks['vencidas'][] = $task;
                } elseif ($daysUntilDue <= 0) {
                    $kanbanTasks['hoy'][] = $task;
                } elseif ($daysUntilDue <= 7) {
                    $kanbanTasks['1_semana'][] = $task;
                } elseif ($daysUntilDue <= 14) {
                    $kanbanTasks['2_semanas'][] = $task;
                }
            }

            // Ordenar cada columna por prioridad
            $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            
            foreach ($kanbanTasks as $column => &$tasks) {
                usort($tasks, function($a, $b) use ($priorityOrder) {
                    $priorityA = $priorityOrder[$a['priority']] ?? 4;
                    $priorityB = $priorityOrder[$b['priority']] ?? 4;
                    
                    if ($priorityA === $priorityB) {
                        return strtotime($a['due_date']) - strtotime($b['due_date']);
                    }
                    
                    return $priorityA - $priorityB;
                });
            }



            return $kanbanTasks;
        } catch (Exception $e) {
            error_log('Error getKanbanTasksForLeader: ' . $e->getMessage());
            return [
                'vencidas' => [],
                'hoy' => [],
                '1_semana' => [],
                '2_semanas' => []
            ];
        }
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
            exit;
        }

        if (!$this->userClan) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin clan asignado'], 400);
            exit;
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
            exit;
        }
        error_log('Usuario tiene acceso de líder correctamente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('ERROR: Método HTTP incorrecto: ' . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
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

    /**
     * Agregar comentario a una subtarea
     */
    public function addSubtaskComment() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            // Manejar tanto JSON como FormData
            $subtaskId = null;
            $commentText = '';
            
            if ($_SERVER['CONTENT_TYPE'] && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
                $subtaskId = $input['subtask_id'] ?? null;
                $commentText = trim($input['comment_text'] ?? '');
            } else {
                $subtaskId = $_POST['subtask_id'] ?? null;
                $commentText = trim($_POST['comment_text'] ?? '');
            }

            if (!$subtaskId || !$commentText) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y texto del comentario son requeridos']);
                return;
            }

            // Verificar permisos
            $permissions = $this->subtaskModel->checkUserPermissions($subtaskId, $this->currentUser['user_id']);
            if (!$permissions['can_comment']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para comentar en esta subtarea']);
                return;
            }

            $commentId = $this->subtaskModel->addComment(
                $subtaskId, 
                $this->currentUser['user_id'], 
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
            error_log("Error en addSubtaskComment: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener comentarios de una subtarea
     */
    public function getSubtaskComments() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            $subtaskId = $_GET['subtask_id'] ?? null;

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            // Verificar permisos
            $permissions = $this->subtaskModel->checkUserPermissions($subtaskId, $this->currentUser['user_id']);
            if (!$permissions['can_view']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para ver esta subtarea']);
                return;
            }

            $comments = $this->subtaskModel->getComments($subtaskId);
            echo json_encode(['success' => true, 'comments' => $comments]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskComments: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Subir archivo adjunto a una subtarea
     */
    public function uploadSubtaskAttachment() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            $subtaskId = $_POST['subtask_id'] ?? null;
            $commentId = $_POST['comment_id'] ?? null;
            $description = trim($_POST['description'] ?? '');

            if (!$subtaskId || !isset($_FILES['file'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y archivo son requeridos']);
                return;
            }

            // Verificar permisos
            $permissions = $this->subtaskModel->checkUserPermissions($subtaskId, $this->currentUser['user_id']);
            if (!$permissions['can_attach']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para adjuntar archivos en esta subtarea']);
                return;
            }

            $file = $_FILES['file'];
            
            // Validar archivo
            $maxSize = 50 * 1024 * 1024; // 50MB
            if ($file['size'] > $maxSize) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 50MB)']);
                return;
            }

            // Guardar archivo
            $fileInfo = $this->subtaskModel->saveUploadedFile($file, $subtaskId, $this->currentUser['user_id']);
            if (!$fileInfo) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
                return;
            }

            // Guardar en base de datos
            $attachmentId = $this->subtaskModel->addAttachment(
                $subtaskId,
                $this->currentUser['user_id'],
                $fileInfo['original_name'],
                $fileInfo['public_path'],
                $fileInfo['file_size'],
                $fileInfo['file_type'],
                $description,
                $commentId
            );

            if ($attachmentId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Archivo adjuntado exitosamente',
                    'attachment_id' => $attachmentId,
                    'file_info' => $fileInfo
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar el adjunto en la base de datos']);
            }

        } catch (Exception $e) {
            error_log("Error en uploadSubtaskAttachment: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener adjuntos de una subtarea
     */
    public function getSubtaskAttachments() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            $subtaskId = $_GET['subtask_id'] ?? null;

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            // Verificar permisos
            $permissions = $this->subtaskModel->checkUserPermissions($subtaskId, $this->currentUser['user_id']);
            if (!$permissions['can_view']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para ver esta subtarea']);
                return;
            }

            $attachments = $this->subtaskModel->getAttachments($subtaskId);
            echo json_encode(['success' => true, 'attachments' => $attachments]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskAttachments: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar comentario de subtarea
     */
    public function deleteSubtaskComment() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

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
            error_log("Error en deleteSubtaskComment: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar adjunto de subtarea
     */
    public function deleteSubtaskAttachment() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
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
            error_log("Error en deleteSubtaskAttachment: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener conteos de comentarios y adjuntos de una subtarea
     */
    public function getSubtaskCounts() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

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
            error_log("Error en getSubtaskCounts: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Guardar estado de checkbox en comentario
     */
    public function saveCheckboxState() {
        // Log para debugging
        error_log("saveCheckboxState: Iniciando función");
        error_log("saveCheckboxState: REQUEST_METHOD = " . $_SERVER['REQUEST_METHOD']);
        error_log("saveCheckboxState: Content-Type = " . ($_SERVER['CONTENT_TYPE'] ?? 'no-set'));
        
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("saveCheckboxState: Método no permitido");
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $rawInput = file_get_contents('php://input');
        error_log("saveCheckboxState: Raw input = " . $rawInput);
        
        $input = json_decode($rawInput, true);
        error_log("saveCheckboxState: Parsed input = " . print_r($input, true));
        
        $commentId = (int)($input['comment_id'] ?? 0);
        $commentType = $input['comment_type'] ?? ''; // 'task' o 'subtask'
        $checkboxIndex = (int)($input['checkbox_index'] ?? 0);
        $checkboxText = trim($input['checkbox_text'] ?? '');
        $isChecked = (bool)($input['is_checked'] ?? false);
        
        error_log("saveCheckboxState: commentId=$commentId, commentType=$commentType, checkboxIndex=$checkboxIndex, isChecked=" . ($isChecked ? 'true' : 'false'));
        
        if ($commentId <= 0 || !in_array($commentType, ['task', 'subtask']) || empty($checkboxText)) {
            Utils::jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
        
        try {
            // Verificar permisos según el tipo de comentario
            if ($commentType === 'task') {
                // Verificar que el comentario pertenece a una tarea del clan
                $stmt = $this->db->prepare("
                    SELECT tc.*, t.project_id, p.clan_id
                    FROM Task_Comments tc
                    JOIN Tasks t ON tc.task_id = t.task_id
                    JOIN Projects p ON t.project_id = p.project_id
                    WHERE tc.comment_id = ?
                ");
                $stmt->execute([$commentId]);
                $comment = $stmt->fetch();
                
                if (!$comment || $comment['clan_id'] != $this->userClan['clan_id']) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
            } else {
                // Verificar que el comentario pertenece a una subtarea del clan
                $stmt = $this->db->prepare("
                    SELECT sc.*, s.task_id, t.project_id, p.clan_id
                    FROM Subtask_Comments sc
                    JOIN Subtasks s ON sc.subtask_id = s.subtask_id
                    JOIN Tasks t ON s.task_id = t.task_id
                    JOIN Projects p ON t.project_id = p.project_id
                    WHERE sc.comment_id = ?
                ");
                $stmt->execute([$commentId]);
                $comment = $stmt->fetch();
                
                if (!$comment || $comment['clan_id'] != $this->userClan['clan_id']) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
            }
            
            // Verificar que el modelo existe y cargar si es necesario
            if (!class_exists('CheckboxState')) {
                require_once __DIR__ . '/../models/CheckboxState.php';
            }
            
            // Guardar estado del checkbox
            $checkboxModel = new CheckboxState();
            $checkboxModel->createTableIfNotExists(); // Crear tabla si no existe
            
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
            error_log("Error al guardar estado de checkbox: " . $e->getMessage());
            error_log("Error al guardar estado de checkbox - Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
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
            // Verificar que el modelo existe
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
            error_log("Error al obtener estados de checkbox: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Test simple para verificar que la ruta funciona
     */
    public function testCheckboxRoute() {
        error_log("testCheckboxRoute: Función ejecutada correctamente");
        Utils::jsonResponse(['success' => true, 'message' => 'Ruta funcionando', 'timestamp' => time()]);
    }

    /**
     * Debug: Verificar funcionamiento de checkboxes
     */
    public function debugCheckboxes() {
        $this->requireAuth();
        
        try {
            // Verificar que el modelo existe
            if (!class_exists('CheckboxState')) {
                require_once __DIR__ . '/../models/CheckboxState.php';
            }
            
            // Verificar si la tabla existe
            $checkboxModel = new CheckboxState();
            $tableCreated = $checkboxModel->createTableIfNotExists();
            
            // Datos de prueba
            $testData = [
                'comment_id' => 1,
                'comment_type' => 'task',
                'checkbox_index' => 0,
                'checkbox_text' => 'Test checkbox',
                'is_checked' => true,
                'user_id' => $this->currentUser['user_id']
            ];
            
            // Intentar guardar datos de prueba
            $saved = $checkboxModel->saveCheckboxState(
                $testData['comment_id'],
                $testData['comment_type'],
                $testData['checkbox_index'],
                $testData['checkbox_text'],
                $testData['is_checked'],
                $testData['user_id']
            );
            
            // Intentar obtener datos
            $states = $checkboxModel->getCheckboxStates(1, 'task');
            
            Utils::jsonResponse([
                'success' => true,
                'debug' => [
                    'table_created' => $tableCreated,
                    'test_data' => $testData,
                    'save_result' => $saved,
                    'retrieved_states' => $states,
                    'user_id' => $this->currentUser['user_id']
                ]
            ]);
            
        } catch (Exception $e) {
            Utils::jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Obtener datos de subtarea para edición
     */
    public function getSubtaskForEdit() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $subtaskId = (int)($_GET['subtask_id'] ?? 0);
        
        if ($subtaskId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
        }

        try {
            // Obtener datos de la subtarea
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

            // Verificar que pertenece al clan
            if ($subtask['clan_id'] != $this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
            }

            Utils::jsonResponse([
                'success' => true, 
                'subtask' => [
                    'subtask_id' => $subtask['subtask_id'],
                    'title' => $subtask['title'],
                    'description' => $subtask['description'] ?? '',
                    'status' => $subtask['status'],
                    'completion_percentage' => $subtask['completion_percentage'],
                    'due_date' => $subtask['due_date']
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error al obtener subtarea para edición: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Editar subtarea
     */
    public function editSubtask() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $subtaskId = $input['subtask_id'] ?? null;
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $status = $input['status'] ?? null;
            $completionPercentage = isset($input['completion_percentage']) ? (int)$input['completion_percentage'] : null;
            $dueDate = $input['due_date'] ?? null;

            if (!$subtaskId || !$title) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID y título son requeridos']);
                return;
            }

            // Verificar que la subtarea pertenece al clan
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
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Subtarea no encontrada']);
                return;
            }

            if ($subtask['clan_id'] != $this->userClan['clan_id']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
                return;
            }

            // Actualizar subtarea en base de datos
            $updateFields = ['title = ?', 'description = ?', 'updated_at = CURRENT_TIMESTAMP'];
            $params = [$title, $description];
            
            if ($status !== null) {
                $updateFields[] = 'status = ?';
                $params[] = $status;
            }
            
            if ($completionPercentage !== null) {
                $updateFields[] = 'completion_percentage = ?';
                $params[] = $completionPercentage;
            }
            
            if ($dueDate !== null) {
                if (!empty($dueDate)) {
                    $updateFields[] = 'due_date = ?';
                    $params[] = $dueDate;
                } else {
                    $updateFields[] = 'due_date = NULL';
                }
            }
            
            $params[] = $subtaskId;
            
            $stmt = $this->db->prepare("
                UPDATE Subtasks 
                SET " . implode(', ', $updateFields) . "
                WHERE subtask_id = ?
            ");
            
            $result = $stmt->execute($params);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Subtarea actualizada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar subtarea']);
            }

        } catch (Exception $e) {
            error_log("Error en editSubtask: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Eliminar subtarea
     */
    public function deleteSubtask() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        try {
            $subtaskId = $_POST['subtask_id'] ?? null;

            if (!$subtaskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subtarea ID requerido']);
                return;
            }

            // Iniciar transacción
            $this->db->beginTransaction();

            try {
                // Eliminar comentarios de la subtarea
                $stmt = $this->db->prepare("DELETE FROM Subtask_Comments WHERE subtask_id = ?");
                $stmt->execute([$subtaskId]);

                // Eliminar adjuntos de la subtarea
                $stmt = $this->db->prepare("DELETE FROM Subtask_Attachments WHERE subtask_id = ?");
                $stmt->execute([$subtaskId]);

                // Eliminar la subtarea
                $stmt = $this->db->prepare("DELETE FROM Subtasks WHERE subtask_id = ?");
                $result = $stmt->execute([$subtaskId]);

                if ($result && $stmt->rowCount() > 0) {
                    $this->db->commit();
                    echo json_encode(['success' => true, 'message' => 'Subtarea eliminada exitosamente']);
                } else {
                    $this->db->rollback();
                    echo json_encode(['success' => false, 'message' => 'Subtarea no encontrada']);
                }

            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Error en deleteSubtask: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Asignar múltiples usuarios a una subtarea
     */
    public function assignSubtaskUsers() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_POST['subtask_id'] ?? 0);
            $userIdsJson = $_POST['user_ids'] ?? '';
            
            if ($subtaskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
            }
            
            $userIds = json_decode($userIdsJson, true);
            if (!is_array($userIds) || empty($userIds)) {
                Utils::jsonResponse(['success' => false, 'message' => 'Lista de usuarios inválida'], 400);
            }

            // Verificar que la subtarea pertenece al clan del líder
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

            if ((int)$subtask['clan_id'] !== (int)$this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta subtarea'], 403);
            }

            // Verificar que todos los usuarios existen
            $userNames = [];
            foreach ($userIds as $userId) {
                $user = $this->userModel->findById($userId);
                if (!$user) {
                    Utils::jsonResponse(['success' => false, 'message' => "Usuario con ID {$userId} no encontrado"], 404);
                }
                $userNames[] = $user['full_name'] ?? $user['username'];
            }

            // Asignar usuarios usando el modelo de asignaciones
            $result = $this->subtaskAssignmentModel->assignUsers($subtaskId, $userIds, $this->currentUser['user_id']);

            if ($result) {
                // Registrar en el historial de la tarea padre
                $historyStmt = $this->db->prepare("
                    INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                    VALUES (?, ?, 'assigned', 'subtask_assignment', 'múltiples usuarios', ?, ?)
                ");
                $historyStmt->execute([
                    $subtask['task_id'],
                    $this->currentUser['user_id'],
                    implode(', ', $userNames),
                    'Subtarea "' . $subtask['title'] . '" asignada a ' . count($userIds) . ' usuario(s): ' . implode(', ', $userNames)
                ]);

                Utils::jsonResponse([
                    'success' => true,
                    'message' => count($userIds) . ' usuario(s) asignado(s) exitosamente'
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al asignar usuarios'], 500);
            }

        } catch (Exception $e) {
            error_log("Error en assignSubtaskUsers: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Desasignar todos los usuarios de una subtarea
     */
    public function unassignSubtaskUsers() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_POST['subtask_id'] ?? 0);

            if ($subtaskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
            }

            // Verificar que la subtarea pertenece al clan del líder
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

            if ((int)$subtask['clan_id'] !== (int)$this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta subtarea'], 403);
            }

            // Obtener usuarios actualmente asignados para el historial
            $assignedUsers = $this->subtaskAssignmentModel->getAssignedUsers($subtaskId);
            $userNames = array_map(function($user) {
                return $user['full_name'] ?? $user['username'];
            }, $assignedUsers);

            // Desasignar todos los usuarios
            $result = $this->subtaskAssignmentModel->unassignAllUsers($subtaskId);

            if ($result) {
                // Registrar en el historial de la tarea padre
                $historyStmt = $this->db->prepare("
                    INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                    VALUES (?, ?, 'unassigned', 'subtask_assignment', ?, 'Sin asignar', ?)
                ");
                $historyStmt->execute([
                    $subtask['task_id'],
                    $this->currentUser['user_id'],
                    implode(', ', $userNames),
                    'Subtarea "' . $subtask['title'] . '" - todos los usuarios desasignados'
                ]);

                Utils::jsonResponse([
                    'success' => true,
                    'message' => 'Todos los usuarios desasignados exitosamente'
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al desasignar usuarios'], 500);
            }

        } catch (Exception $e) {
            error_log("Error en unassignSubtaskUsers: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener usuarios asignados a una subtarea
     */
    public function getSubtaskAssignedUsers() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_GET['subtask_id'] ?? 0);

            if ($subtaskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea inválido'], 400);
            }

            // Verificar que la subtarea pertenece al clan del líder
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

            if ((int)$subtask['clan_id'] !== (int)$this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para ver esta subtarea'], 403);
            }

            // Obtener usuarios asignados
            $assignedUsers = $this->subtaskAssignmentModel->getAssignedUsers($subtaskId);

            Utils::jsonResponse([
                'success' => true,
                'users' => $assignedUsers
            ]);

        } catch (Exception $e) {
            error_log("Error en getSubtaskAssignedUsers: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Remover un usuario específico de una subtarea
     */
    public function removeSubtaskUser() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $subtaskId = (int)($_POST['subtask_id'] ?? 0);
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($subtaskId <= 0 || $userId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de subtarea o usuario inválido'], 400);
            }

            // Verificar que la subtarea pertenece al clan del líder
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

            if ((int)$subtask['clan_id'] !== (int)$this->userClan['clan_id']) {
                Utils::jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta subtarea'], 403);
            }

            // Obtener información del usuario para el historial
            $user = $this->userModel->findById($userId);
            $userName = $user ? ($user['full_name'] ?? $user['username']) : 'Usuario desconocido';

            // Remover el usuario específico
            $result = $this->subtaskAssignmentModel->removeUser($subtaskId, $userId);

            if ($result) {
                // Registrar en el historial de la tarea padre
                $historyStmt = $this->db->prepare("
                    INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                    VALUES (?, ?, 'unassigned', 'subtask_assignment', ?, 'removido', ?)
                ");
                $historyStmt->execute([
                    $subtask['task_id'],
                    $this->currentUser['user_id'],
                    $userName,
                    'Usuario "' . $userName . '" removido de subtarea "' . $subtask['title'] . '"'
                ]);

                Utils::jsonResponse([
                    'success' => true,
                    'message' => 'Usuario removido exitosamente'
                ]);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al remover usuario'], 500);
            }

        } catch (Exception $e) {
            error_log("Error en removeSubtaskUser: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    // === Perfil de Líder de Clan ===
    public function profile() {
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) { Utils::redirect('dashboard'); return; }
        $user = $this->currentUser;
        $data = [
            'currentPage' => 'clan_leader',
            'user' => $user,
            'clan' => $this->userClan,
        ];
        $this->loadView('clan_leader/profile', $data);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); 
        }
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) { 
            Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); 
        }

        $username = $this->currentUser['username'];
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');

        if ($email === '' || $fullName === '') {
            Utils::jsonResponse(['success'=>false,'message'=>'Email y nombre completo son requeridos'],400);
        }
        if (!Utils::isValidEmail($email)) {
            Utils::jsonResponse(['success'=>false,'message'=>'El email no es válido'],400);
        }

        try {
            $ok = $this->userModel->update($this->currentUser['user_id'], $username, $email, $fullName, 1);
            if ($ok) {
                $_SESSION['username'] = $username; 
                $_SESSION['email'] = $email; 
                $_SESSION['full_name'] = $fullName;
                Utils::jsonResponse(['success'=>true,'message'=>'Perfil actualizado']);
            } else {
                Utils::jsonResponse(['success'=>false,'message'=>'No se pudo actualizar el perfil']);
            }
        } catch (Exception $e) {
            Utils::jsonResponse(['success'=>false,'message'=>'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function updatePasswordPlain() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); }
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) { Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); }
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        if ($new === '' || $new !== $confirm) { Utils::jsonResponse(['success'=>false,'message'=>'La confirmación no coincide'],400); }
        $ok = $this->userModel->updatePasswordPlain($this->currentUser['user_id'], $new);
        Utils::jsonResponse(['success'=>$ok,'message'=>$ok?'Contraseña actualizada':'No se pudo actualizar la contraseña']);
    }

    public function uploadAvatar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Utils::jsonResponse(['success'=>false,'message'=>'Método no permitido'],405); }
        $this->requireAuth();
        if (!$this->hasClanLeaderAccess()) { Utils::jsonResponse(['success'=>false,'message'=>'Acceso denegado'],403); }
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

} 