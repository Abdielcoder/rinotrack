<?php

class KPIController {
    private $db;
    private $auth;
    private $kpiModel;
    private $projectModel;
    private $clanModel;
    private $taskModel;
    private $roleModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->auth = new Auth();
        $this->kpiModel = new KPI();
        $this->projectModel = new Project();
        $this->clanModel = new Clan();
        $this->taskModel = new Task();
        $this->roleModel = new Role();
    }

    /**
     * Verificar si el usuario tiene acceso de administrador
     */
    private function hasAdminAccess() {
        $auth = new Auth();
        $currentUser = $auth->getCurrentUser();
        
        if (!$currentUser) {
            error_log("hasAdminAccess: No current user found");
            return false;
        }
        
        error_log("hasAdminAccess: Checking user ID " . $currentUser['user_id'] . " for admin role");
        $hasAccess = $this->roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
        error_log("hasAdminAccess: Result = " . ($hasAccess ? 'true' : 'false'));
        
        return $hasAccess;
    }

    /**
     * Dashboard principal de KPIs
     */
    public function dashboard() {
        if (!$this->hasAdminAccess()) {
            header('Location: ?route=dashboard');
            exit;
        }

        // Obtener KPI actual
        $currentKPI = $this->kpiModel->getCurrentQuarter();
        
        // Estadísticas generales
        $stats = $this->getKPIStats($currentKPI);
        
        // Ranking de clanes
        $clanRanking = $this->kpiModel->getClanRankingByQuarterId($currentKPI['kpi_quarter_id'] ?? null);
        
        // Debug: Log para verificar datos del ranking
        error_log("DEBUG clanRanking raw data: " . print_r($clanRanking, true));
        
        // Eliminar duplicados por clan_id y agregar total_points
        if ($clanRanking && $currentKPI) {
            $uniqueClans = [];
            $seenClanIds = [];
            
            foreach ($clanRanking as $clan) {
                if (!in_array($clan['clan_id'], $seenClanIds)) {
                    $clan['total_points'] = 1000; // Base fija para el cálculo de porcentaje
                    $uniqueClans[] = $clan;
                    $seenClanIds[] = $clan['clan_id'];
                }
            }
            
            $clanRanking = $uniqueClans;
        }
        
        // Proyectos con progreso
        $projectsProgress = $this->getProjectsProgress($currentKPI['kpi_quarter_id'] ?? null);
        
        // Datos para el camino tipo serpiente
        $snakePathData = $this->getSnakePathData($currentKPI);

        require_once __DIR__ . '/../views/kpi/dashboard.php';
    }

    /**
     * Gestión de KPIs trimestrales
     */
    public function quarters() {
        if (!$this->hasAdminAccess()) {
            header('Location: ?route=dashboard');
            exit;
        }

        $quarters = $this->kpiModel->getAllQuarters();
        $currentKPI = $this->kpiModel->getCurrentQuarter();

        require_once __DIR__ . '/../views/kpi/quarters.php';
    }

    /**
     * Crear nuevo trimestre KPI
     */
    public function createQuarter() {
        error_log("=== CREATE QUARTER DEBUG ===");
        error_log("createQuarter called - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("Raw input: " . file_get_contents('php://input'));
        
        if (!$this->hasAdminAccess()) {
            error_log("Access denied - user doesn't have admin access");
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $quarter = Utils::sanitizeInput($_POST['quarter'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $totalPoints = (int)($_POST['total_points'] ?? 1000);
        $activateImmediately = isset($_POST['activate_immediately']) && $_POST['activate_immediately'] === 'on';

        error_log("Parsed data - quarter: '$quarter', year: $year, totalPoints: $totalPoints, activateImmediately: " . ($activateImmediately ? 'true' : 'false'));

        // Validaciones
        if (empty($quarter) || !in_array($quarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
            error_log("Invalid quarter: '$quarter'");
            Utils::jsonResponse(['success' => false, 'message' => 'Trimestre inválido: ' . $quarter], 400);
        }

        if ($year < 2020 || $year > 2030) {
            error_log("Invalid year: $year");
            Utils::jsonResponse(['success' => false, 'message' => 'Año inválido: ' . $year], 400);
        }

        if ($totalPoints < 100 || $totalPoints > 100000) {
            error_log("Invalid totalPoints: $totalPoints");
            Utils::jsonResponse(['success' => false, 'message' => 'Los puntos deben estar entre 100 y 100,000'], 400);
        }

        // Verificar si ya existe un trimestre con el mismo período
        try {
            $existingQuarter = $this->kpiModel->getByYearAndQuarter($year, $quarter);
            if ($existingQuarter) {
                error_log("Quarter already exists: $quarter $year");
                Utils::jsonResponse(['success' => false, 'message' => "Ya existe un trimestre {$quarter} para el año {$year}"], 400);
            }
        } catch (Exception $e) {
            error_log("Error checking existing quarter: " . $e->getMessage());
        }

        try {
            error_log("Creating quarter: $quarter, $year, $totalPoints, " . ($activateImmediately ? 'true' : 'false'));
            $result = $this->kpiModel->createQuarter($quarter, $year, $totalPoints, $activateImmediately);

            if ($result) {
                error_log("Quarter created successfully with ID: $result");
                Utils::jsonResponse(['success' => true, 'message' => 'Trimestre KPI creado exitosamente']);
            } else {
                error_log("Failed to create quarter - model returned false");
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el trimestre KPI'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in createQuarter: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 400);
        }
        error_log("=== END CREATE QUARTER DEBUG ===");
    }
    
    /**
     * Actualizar trimestre KPI
     */
    public function updateQuarter() {
        error_log("updateQuarter called - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        if (!$this->hasAdminAccess()) {
            error_log("Access denied - user doesn't have admin access");
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
            Utils::jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $quarterId = (int)($_POST['quarterId'] ?? 0);
        $quarter = Utils::sanitizeInput($_POST['quarter'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $totalPoints = (int)($_POST['total_points'] ?? 1000);

        error_log("Parsed data - quarterId: $quarterId, quarter: '$quarter', year: $year, totalPoints: $totalPoints");

        // Validaciones
        if ($quarterId <= 0) {
            error_log("Invalid quarterId: $quarterId");
            Utils::jsonResponse(['success' => false, 'message' => 'ID de trimestre inválido: ' . $quarterId], 400);
        }

        if (empty($quarter) || !in_array($quarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
            error_log("Invalid quarter: '$quarter'");
            Utils::jsonResponse(['success' => false, 'message' => 'Trimestre inválido: ' . $quarter], 400);
        }

        if ($year < 2020 || $year > 2030) {
            error_log("Invalid year: $year");
            Utils::jsonResponse(['success' => false, 'message' => 'Año inválido: ' . $year], 400);
        }

        if ($totalPoints < 100 || $totalPoints > 100000) {
            error_log("Invalid totalPoints: $totalPoints");
            Utils::jsonResponse(['success' => false, 'message' => 'Los puntos deben estar entre 100 y 100,000'], 400);
        }

        try {
            error_log("Updating quarter: ID=$quarterId, quarter=$quarter, year=$year, points=$totalPoints");
            $result = $this->kpiModel->updateQuarter($quarterId, $quarter, $year, $totalPoints);

            if ($result) {
                error_log("Quarter updated successfully");
                Utils::jsonResponse(['success' => true, 'message' => 'Trimestre actualizado exitosamente']);
            } else {
                error_log("Failed to update quarter - model returned false");
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar el trimestre'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in updateQuarter: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Utils::jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 400);
        }
    }
    
    /**
     * Eliminar trimestre KPI
     */
    public function deleteQuarter() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quarterId = (int)($_POST['quarterId'] ?? 0);

            if ($quarterId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de trimestre inválido'], 400);
            }

            try {
                $result = $this->kpiModel->deleteQuarter($quarterId);

                if ($result) {
                    Utils::jsonResponse(['success' => true, 'message' => 'Trimestre eliminado exitosamente']);
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar el trimestre'], 500);
                }
            } catch (Exception $e) {
                Utils::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }
    
    /**
     * Obtener detalles de un trimestre específico
     */
    public function getQuarterDetails() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $quarterId = (int)($_GET['id'] ?? 0);

        if ($quarterId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de trimestre inválido'], 400);
        }

        $quarter = $this->kpiModel->findQuarterById($quarterId);
        if (!$quarter) {
            Utils::jsonResponse(['success' => false, 'message' => 'Trimestre no encontrado'], 404);
        }

        // Obtener proyectos del trimestre
        $projects = $this->projectModel->getByKPIQuarter($quarterId);
        
        // Obtener estadísticas por clan
        $clans = $this->getClanStatsByQuarter($quarterId);

        Utils::jsonResponse([
            'success' => true,
            'quarter' => $quarter,
            'projects' => $projects,
            'clans' => $clans
        ]);
    }

    /**
     * Activar trimestre KPI
     */
    public function activateQuarter() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quarterId = (int)($_POST['quarterId'] ?? 0);

            if ($quarterId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de trimestre inválido'], 400);
            }

            try {
                $result = $this->kpiModel->activateQuarter($quarterId);

                if ($result) {
                    Utils::jsonResponse(['success' => true, 'message' => 'Trimestre activado exitosamente']);
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error al activar el trimestre'], 500);
                }
            } catch (Exception $e) {
                Utils::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }

    /**
     * Gestión de proyectos con KPIs
     */
    public function projects() {
        if (!$this->hasAdminAccess()) {
            header('Location: ?route=dashboard');
            exit;
        }

        $currentKPI = $this->kpiModel->getCurrentQuarter();
        $projects = [];
        $projectsWithoutKPI = [];
        $clans = $this->clanModel->getAll();

        if ($currentKPI) {
            $projects = $this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id']);
            $projectsWithoutKPI = $this->projectModel->getProjectsWithoutKPI();
        }

        // Usar los datos calculados por el modelo KPI
        if ($currentKPI) {
            $assignedPoints = $currentKPI['assigned_points'] ?? 0;
            $remainingPoints = $currentKPI['total_points'] - $assignedPoints;
            
            // Debug: Log para verificar cálculos
            error_log("DEBUG projects: kpi_quarter_id = " . $currentKPI['kpi_quarter_id']);
            error_log("DEBUG projects: total_points = " . $currentKPI['total_points']);
            error_log("DEBUG projects: assigned_points (from model) = " . $assignedPoints);
            error_log("DEBUG projects: remainingPoints = " . $remainingPoints);
        } else {
            $assignedPoints = 0;
            $remainingPoints = 0;
            error_log("DEBUG projects: No hay KPI activo");
        }

        // Datos adicionales para la vista
        $data = [
            'currentKPI' => $currentKPI,
            'projects' => $projects,
            'projectsWithoutKPI' => $projectsWithoutKPI,
            'clans' => $clans,
            'remainingPoints' => $remainingPoints,
            'assignedPoints' => $assignedPoints,
            'currentPage' => 'kpi',
            'user' => (new Auth())->getCurrentUser(),
            'loadKpiProjectsJs' => true, // Flag to load specific JS
            'additionalJS' => [
                APP_URL . 'assets/js/kpi-projects.js'
            ]
        ];

        // Extraer variables para la vista
        extract($data);

        require_once __DIR__ . '/../views/kpi/projects.php';
    }

    /**
     * Asignar KPI a proyecto
     */
    public function assignKPI() {
        // Debug: Log para debugging
        error_log("DEBUG assignKPI: Iniciando método");
        error_log("DEBUG assignKPI: REQUEST_METHOD = " . $_SERVER['REQUEST_METHOD']);
        error_log("DEBUG assignKPI: POST data = " . print_r($_POST, true));
        
        if (!$this->hasAdminAccess()) {
            error_log("DEBUG assignKPI: Sin acceso admin");
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = (int)($_POST['project_id'] ?? 0);
            $kpiPoints = (int)($_POST['kpi_points'] ?? 0);
            
            error_log("DEBUG assignKPI: projectId = $projectId, kpiPoints = $kpiPoints");

            if ($projectId <= 0 || $kpiPoints <= 0) {
                error_log("DEBUG assignKPI: Datos inválidos - projectId: $projectId, kpiPoints: $kpiPoints");
                header('HTTP/1.1 400 Bad Request');
                echo json_encode([
                    'success' => false, 
                    'message' => "Datos inválidos: ID proyecto=$projectId, puntos=$kpiPoints"
                ]);
                exit;
            }

            $currentKPI = $this->kpiModel->getCurrentQuarter();
            error_log("DEBUG assignKPI: currentKPI = " . print_r($currentKPI, true));
            
            if (!$currentKPI) {
                error_log("DEBUG assignKPI: No hay trimestre KPI activo");
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'No hay trimestre KPI activo']);
                exit;
            }

            try {
                $result = $this->projectModel->assignKPI($projectId, $currentKPI['kpi_quarter_id'], $kpiPoints);
                error_log("DEBUG assignKPI: result = " . print_r($result, true));

                if ($result && isset($result['success']) && $result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message'] ?? 'KPI asignado exitosamente']);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode([
                        'success' => false, 
                        'message' => $result['message'] ?? 'Error desconocido al asignar KPI'
                    ]);
                }
            } catch (Exception $e) {
                error_log("DEBUG assignKPI: Excepción = " . $e->getMessage());
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error interno: ' . $e->getMessage()
                ]);
            }
            exit;
        }
        
        // Si no es POST
        error_log("DEBUG assignKPI: Método no POST");
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    /**
     * Obtener puntos disponibles para el trimestre actual
     */
    public function getAvailablePoints() {
        if (!$this->hasAdminAccess()) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        $currentKPI = $this->kpiModel->getCurrentQuarter();
        if (!$currentKPI) {
            echo json_encode(['success' => false, 'message' => 'No hay trimestre KPI activo']);
            exit;
        }

        try {
            // Calcular puntos asignados
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(kpi_points), 0) as total_assigned 
                FROM Projects 
                WHERE kpi_quarter_id = ?
            ");
            $stmt->execute([$currentKPI['kpi_quarter_id']]);
            $totalAssigned = $stmt->fetch()['total_assigned'];
            
            $availablePoints = $currentKPI['total_points'] - $totalAssigned;
            
            echo json_encode([
                'success' => true, 
                'available_points' => (int)$availablePoints,
                'total_points' => (int)$currentKPI['total_points'],
                'assigned_points' => (int)$totalAssigned
            ]);
        } catch (Exception $e) {
            error_log("Error al obtener puntos disponibles: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al calcular puntos disponibles']);
        }
        exit;
    }

    /**
     * Obtener tareas de un proyecto (para AJAX)
     */
    public function getProjectTasks() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $projectId = (int)($_GET['project_id'] ?? 0);
        if ($projectId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de proyecto inválido'], 400);
        }

        $tasks = $this->taskModel->getByProject($projectId);
        $project = $this->projectModel->findById($projectId);
        
        Utils::jsonResponse(['success' => true, 'tasks' => $tasks, 'project' => $project]);
    }

    /**
     * Agregar una nueva tarea a un proyecto (para AJAX)
     */
    public function addTask() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $projectId = (int)($_POST['project_id'] ?? 0);
                $taskName = Utils::sanitizeInput($_POST['task_name'] ?? '');
                $assignedPercentage = (float)($_POST['assigned_percentage'] ?? 0);
                $currentUser = $this->auth->getCurrentUser();

                if (!$currentUser || !isset($currentUser['user_id'])) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error de sesión: No se pudo identificar al usuario.'], 401);
                }

                if ($projectId <= 0 || empty($taskName)) {
                    Utils::jsonResponse(['success' => false, 'message' => 'Datos de tarea inválidos'], 400);
                }

                $taskId = $this->taskModel->create(
                    $projectId,
                    $taskName,
                    '', // Descripción vacía por ahora
                    null, // Sin asignar por ahora
                    'medium',
                    null,
                    $currentUser['user_id'],
                    $assignedPercentage
                );

                if ($taskId) {
                    Utils::jsonResponse(['success' => true, 'message' => 'Tarea agregada exitosamente']);
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error al agregar la tarea'], 500);
                }
            } catch (Exception $e) {
                // Capturar cualquier error inesperado y registrarlo
                error_log("Error fatal en addTask: " . $e->getMessage());
                Utils::jsonResponse(['success' => false, 'message' => 'Ocurrió un error interno en el servidor.'], 500);
            }
        }
    }

    /**
     * Cambiar estado de una tarea (para AJAX)
     */
    public function toggleTaskStatus() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = (int)($_POST['task_id'] ?? 0);
            $isCompleted = isset($_POST['is_completed']) && $_POST['is_completed'] === 'true';

            if ($taskId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de tarea inválido'], 400);
            }

            $result = $this->taskModel->toggleStatus($taskId, $isCompleted);

            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Estado de la tarea actualizado']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar la tarea'], 500);
            }
        }
    }
    
    /**
     * Debug temporal para verificar datos de KPI
     */
    public function debug() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        // Test database connection
        $dbTest = 'FAILED';
        try {
            $stmt = $this->db->query("SELECT 1");
            $dbTest = 'OK';
        } catch (Exception $e) {
            $dbTest = 'ERROR: ' . $e->getMessage();
        }

        // Test KPI_Quarters table
        $tableTest = 'FAILED';
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM KPI_Quarters");
            $count = $stmt->fetchColumn();
            $tableTest = "OK - $count records";
        } catch (Exception $e) {
            $tableTest = 'ERROR: ' . $e->getMessage();
        }

        $debugInfo = [
            'database_connection' => $dbTest,
            'kpi_quarters_table' => $tableTest,
            'current_user' => $this->auth->getCurrentUser(),
            'admin_access' => $this->hasAdminAccess(),
            'kpi_model' => $this->kpiModel ? 'OK' : 'FAILED',
            'server_time' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'post_data' => $_POST,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ];

        Utils::jsonResponse(['success' => true, 'debug' => $debugInfo]);
    }

    /**
     * Cambiar modalidad de distribución de tareas
     */
    public function changeDistributionMode() {
        if (!$this->hasAdminAccess()) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = (int)($_POST['project_id'] ?? 0);
            $mode = $_POST['mode'] ?? '';

            if ($projectId <= 0 || !in_array($mode, ['automatic', 'percentage'])) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
                exit;
            }

            $result = $this->projectModel->changeTaskDistributionMode($projectId, $mode);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Modalidad cambiada exitosamente']);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(['success' => false, 'message' => 'Error al cambiar la modalidad']);
            }
            exit;
        }
    }

    /**
     * Obtener estadísticas de KPI
     */
    private function getKPIStats($currentKPI) {
        if (!$currentKPI) {
            return [
                'total_points' => 0,
                'assigned_points' => 0,
                'available_points' => 0,
                'remaining_points' => 0,
                'completed_points' => 0,
                'progress_percentage' => 0,
                'total_projects' => 0,
                'active_clans' => 0
            ];
        }

        // Usar los datos calculados por el modelo KPI
        $assignedPoints = $currentKPI['assigned_points'] ?? 0;
        
        $totalProjects = count($this->projectModel->getByKPIQuarter($currentKPI['kpi_quarter_id']));
        $activeClans = count($this->kpiModel->getClanRankingByQuarterId($currentKPI['kpi_quarter_id']));
        
        // Calcular puntos completados basado en el progreso de proyectos
        $completedPoints = $this->calculateCompletedPoints($currentKPI['kpi_quarter_id']);
        
        $remainingPoints = $currentKPI['total_points'] - $assignedPoints;
        $progressPercentage = $currentKPI['total_points'] > 0 ? 
            ($assignedPoints / $currentKPI['total_points']) * 100 : 0;

        return [
            'total_points' => (int)$currentKPI['total_points'],
            'assigned_points' => (int)$assignedPoints,
            'available_points' => (int)$remainingPoints,
            'remaining_points' => (int)$remainingPoints, // Mantener por compatibilidad
            'completed_points' => (int)$completedPoints,
            'progress_percentage' => round($progressPercentage, 1),
            'total_projects' => (int)$totalProjects,
            'active_clans' => (int)$activeClans
        ];
    }

    /**
     * Obtener progreso de proyectos
     */
    private function getProjectsProgress($kpiQuarterId) {
        if (!$kpiQuarterId) {
            return [];
        }

        $projects = $this->projectModel->getByKPIQuarter($kpiQuarterId);
        
        foreach ($projects as &$project) {
            // Calcular progreso del proyecto
            $progress = $this->projectModel->calculateKPIProgress($project['project_id']);
            $project['progress_percentage'] = $progress['progress_percentage'];
            $project['completed_points'] = $progress['earned_points'];
            
            // Agregar campos que necesita la vista
            $project['status'] = $this->determineProjectStatus($project['progress_percentage']);
            $project['tasks_completed'] = $this->getCompletedTasksCount($project['project_id']);
        }

        return $projects;
    }
    
    /**
     * Determinar el estado del proyecto basado en su progreso
     */
    private function determineProjectStatus($progressPercentage) {
        if ($progressPercentage == 0) {
            return 'pending';
        } elseif ($progressPercentage < 50) {
            return 'in-progress';
        } elseif ($progressPercentage < 100) {
            return 'near-completion';
        } else {
            return 'completed';
        }
    }
    
    /**
     * Obtener cantidad de tareas completadas de un proyecto
     */
    private function getCompletedTasksCount($projectId) {
        try {
            // Usar la conexión de base de datos del modelo KPI
            $stmt = $this->kpiModel->getDB()->prepare("
                SELECT COUNT(*) as completed_count 
                FROM Tasks 
                WHERE project_id = ? AND is_completed = 1
            ");
            $stmt->execute([$projectId]);
            $result = $stmt->fetch();
            return (int)($result['completed_count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al obtener tareas completadas: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener estadísticas de clanes por trimestre
     */
    private function getClanStatsByQuarter($quarterId) {
        if (!$quarterId) {
            return [];
        }

        try {
            // Obtener proyectos del trimestre agrupados por clan
            $projects = $this->projectModel->getByKPIQuarter($quarterId);
            
            $clanStats = [];
            foreach ($projects as $project) {
                $clanId = $project['clan_id'];
                $clanName = $project['clan_name'];
                
                if (!isset($clanStats[$clanId])) {
                    $clanStats[$clanId] = [
                        'clan_id' => $clanId,
                        'clan_name' => $clanName,
                        'projects_count' => 0,
                        'total_points' => 0,
                        'completed_points' => 0
                    ];
                }
                
                $clanStats[$clanId]['projects_count']++;
                $clanStats[$clanId]['total_points'] += $project['kpi_points'];
                
                // Calcular puntos completados
                $progress = $this->projectModel->calculateKPIProgress($project['project_id']);
                $clanStats[$clanId]['completed_points'] += $progress['earned_points'];
            }
            
            return array_values($clanStats);
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas de clanes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calcular puntos completados para un trimestre KPI
     */
    private function calculateCompletedPoints($kpiQuarterId) {
        if (!$kpiQuarterId) {
            return 0;
        }
        
        try {
            // Obtener todos los proyectos del trimestre
            $projects = $this->projectModel->getByKPIQuarter($kpiQuarterId);
            $totalCompletedPoints = 0;
            
            foreach ($projects as $project) {
                // Calcular progreso de cada proyecto
                $progress = $this->projectModel->calculateKPIProgress($project['project_id']);
                $totalCompletedPoints += $progress['earned_points'];
            }
            
            return (int)$totalCompletedPoints;
        } catch (Exception $e) {
            error_log("Error al calcular puntos completados: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener progreso actual de un proyecto específico
     */
    public function getProjectProgress() {
        if (!$this->hasAdminAccess()) {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        $projectId = $_GET['project_id'] ?? null;
        
        if (!$projectId) {
            echo json_encode(['success' => false, 'message' => 'ID de proyecto requerido']);
            return;
        }

        try {
            // Calcular el progreso actual del proyecto
            $progress = $this->projectModel->calculateKPIProgress($projectId);
            
            echo json_encode([
                'success' => true,
                'progress_percentage' => round($progress['progress_percentage'], 1),
                'earned_points' => $progress['earned_points'],
                'total_points' => $progress['total_points']
            ]);
        } catch (Exception $e) {
            error_log("Error al obtener progreso del proyecto: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error al obtener progreso del proyecto'
            ]);
        }
    }

    /**
     * Obtener datos actualizados de proyectos para refrescar la vista
     */
    public function getProjectsData() {
        if (!$this->hasAdminAccess()) {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            return;
        }

        try {
            error_log("getProjectsData - Starting");
            // Obtener KPI actual
            $currentKPI = $this->kpiModel->getCurrentQuarter();
            
            if (!$currentKPI) {
                error_log("getProjectsData - No active KPI quarter found");
                echo json_encode([
                    'success' => true,
                    'projects' => [],
                    'projectsWithoutKPI' => [],
                    'message' => 'No hay período KPI activo'
                ]);
                return;
            }

            error_log("getProjectsData - Getting projects for quarter ID: " . $currentKPI['kpi_quarter_id']);
            // Obtener proyectos con KPI del trimestre actual
            $projects = $this->getProjectsProgress($currentKPI['kpi_quarter_id']);
            
            error_log("getProjectsData - Getting projects without KPI");
            // Obtener proyectos sin KPI asignado
            $projectsWithoutKPI = $this->projectModel->getProjectsWithoutKPI();
            
            error_log("getProjectsData - Success, returning data");
            echo json_encode([
                'success' => true,
                'projects' => $projects,
                'projectsWithoutKPI' => $projectsWithoutKPI,
                'currentKPI' => $currentKPI
            ]);
        } catch (Exception $e) {
            error_log("getProjectsData - Error: " . $e->getMessage());
            error_log("getProjectsData - Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'message' => 'Error al obtener datos de proyectos'
            ]);
        }
    }

    /**
     * Obtener datos para el camino tipo serpiente
     */
    private function getSnakePathData($currentKPI) {
        if (!$currentKPI) {
            return [
                'quarter_progress' => 0,
                'clans_data' => [],
                'total_points' => 1000,
                'quarter_info' => []
            ];
        }

        // Calcular progreso del trimestre basado en la fecha actual
        $quarterProgress = $this->calculateQuarterProgress($currentKPI['quarter'], $currentKPI['year']);
        
        // Obtener datos de clanes con sus puntos
        $clansData = $this->getClansSnakePathData($currentKPI['kpi_quarter_id']);
        
        // Información del trimestre
        $quarterInfo = $this->getQuarterInfo($currentKPI['quarter'], $currentKPI['year']);

        return [
            'quarter_progress' => $quarterProgress,
            'clans_data' => $clansData,
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
     * Obtener datos de clanes para el camino tipo serpiente
     */
    private function getClansSnakePathData($kpiQuarterId) {
        try {
            // Consulta simplificada para debugging
            $stmt = $this->db->prepare("
                SELECT 
                    c.clan_id,
                    c.clan_name,
                    c.clan_departamento,
                    COALESCE(SUM(p.kpi_points), 0) as total_assigned,
                    COALESCE(SUM(
                        CASE 
                            WHEN p.task_distribution_mode = 'automatic' THEN 
                                (SELECT COALESCE(SUM(t.automatic_points), 0) 
                                 FROM Tasks t 
                                 WHERE t.project_id = p.project_id AND t.is_completed = 1)
                            ELSE 
                                (SELECT COALESCE(SUM(t.assigned_percentage * p.kpi_points / 100), 0) 
                                 FROM Tasks t 
                                 WHERE t.project_id = p.project_id AND t.is_completed = 1)
                        END
                    ), 0) as earned_points,
                    COUNT(p.project_id) as total_projects
                FROM Clans c
                LEFT JOIN Projects p ON c.clan_id = p.clan_id AND p.kpi_quarter_id = ?
                GROUP BY c.clan_id, c.clan_name, c.clan_departamento
                ORDER BY earned_points DESC, total_assigned DESC
            ");
            $stmt->execute([$kpiQuarterId]);
            $clans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Solo incluir clanes que tienen proyectos asignados (total_assigned > 0)
            $clans = array_filter($clans, function($clan) {
                return $clan['total_assigned'] > 0;
            });
            
            // Si no hay clanes con datos, retornar array vacío
            if (empty($clans)) {
                return [];
            }
            
            // Asignar colores e iconos específicos para cada clan
            $clanIcons = [
                'ZEUS' => 'fas fa-bolt',
                'Artemisa' => 'fas fa-moon',
                'Afrodita' => 'fas fa-heart',
                'Kratos' => 'fas fa-fist-raised',
                'Hermes' => 'fas fa-running',
                'Persefone' => 'fas fa-seedling',
                'Aura' => 'fas fa-star',
                'GAIA' => 'fas fa-leaf',
                'Apollo' => 'fas fa-sun',
                'Athena' => 'fas fa-shield-alt',
                'Ares' => 'fas fa-sword',
                'Poseidon' => 'fas fa-water',
                'Hades' => 'fas fa-skull',
                'Hera' => 'fas fa-crown',
                'Servicio' => 'fas fa-cogs',
                'Nexus' => 'fas fa-network-wired',
                'Pulse' => 'fas fa-heartbeat',
                'Vortex' => 'fas fa-wind',
                'Phoenix' => 'fas fa-fire',
                'Dragon' => 'fas fa-dragon',
                'Eagle' => 'fas fa-eagle',
                'Wolf' => 'fas fa-paw',
                'Lion' => 'fas fa-crown'
            ];
            
            $clanColors = [
                'ZEUS' => '#ef4444',      // Rojo
                'Artemisa' => '#8b5cf6',  // Púrpura
                'Afrodita' => '#ec4899',  // Rosa
                'Kratos' => '#dc2626',    // Rojo oscuro
                'Hermes' => '#10b981',    // Verde
                'Persefone' => '#059669', // Verde esmeralda
                'Aura' => '#f97316',      // Naranja brillante
                'GAIA' => '#16a34a',      // Verde bosque
                'Apollo' => '#f59e0b',    // Naranja
                'Athena' => '#3b82f6',    // Azul
                'Ares' => '#991b1b',      // Rojo muy oscuro
                'Poseidon' => '#0891b2',  // Azul marino
                'Hades' => '#6b7280',     // Gris
                'Hera' => '#fbbf24',      // Amarillo
                'Servicio' => '#6366f1',  // Índigo
                'Nexus' => '#06b6d4',     // Cian
                'Pulse' => '#be185d',     // Rosa oscuro
                'Vortex' => '#7c3aed',    // Púrpura oscuro
                'Phoenix' => '#ea580c',   // Naranja rojizo
                'Dragon' => '#047857',    // Verde teal
                'Eagle' => '#1e40af',     // Azul oscuro
                'Wolf' => '#4b5563',      // Gris medio
                'Lion' => '#d97706'       // Amarillo oscuro
            ];
            
            foreach ($clans as &$clan) {
                // Asignar color específico para el clan
                if (empty($clan['clan_color'])) {
                    $clan['clan_color'] = $clanColors[$clan['clan_name']] ?? '#3b82f6';
                }
                
                // Asignar icono específico para el clan
                if (empty($clan['clan_icon'])) {
                    $clan['clan_icon'] = $clanIcons[$clan['clan_name']] ?? 'fas fa-users';
                }
                
                // Asegurar que los valores numéricos sean correctos
                $clan['earned_points'] = (int)($clan['earned_points'] ?? 0);
                $clan['total_assigned'] = (int)($clan['total_assigned'] ?? 0);
                $clan['total_points'] = 1000; // Base fija para el cálculo de porcentaje
                
                // Calcular posición en el camino (sin límite de 1000)
                $clan['path_position'] = max(0, $clan['earned_points']);
                $clan['progress_percentage'] = 1000 > 0 ? 
                    round(($clan['earned_points'] / 1000) * 100, 1) : 0;
                
                // Log para debugging
                error_log("Clan {$clan['clan_name']}: earned_points={$clan['earned_points']}, total_assigned={$clan['total_assigned']}, path_position={$clan['path_position']}, icon={$clan['clan_icon']}");
            }
            
            return $clans;
        } catch (PDOException $e) {
            error_log("Error al obtener datos de clanes para snake path: " . $e->getMessage());
            return [];
        }
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