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
            return false;
        }
        
        return $this->roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
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
        
        // Proyectos con progreso
        $projectsProgress = $this->getProjectsProgress($currentKPI['kpi_quarter_id'] ?? null);
        
        // Proyectos recientes para actividad
        $recentProjects = $projectsProgress; // Usar los mismos datos por ahora

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
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quarter = Utils::sanitizeInput($_POST['quarter'] ?? '');
            $year = (int)($_POST['year'] ?? date('Y'));
            $totalPoints = (int)($_POST['total_points'] ?? 1000);
            $activateImmediately = isset($_POST['activate_immediately']) && $_POST['activate_immediately'] === 'on';

            // Validaciones
            if (empty($quarter) || !in_array($quarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
                Utils::jsonResponse(['success' => false, 'message' => 'Trimestre inválido'], 400);
            }

            if ($year < 2020 || $year > 2030) {
                Utils::jsonResponse(['success' => false, 'message' => 'Año inválido'], 400);
            }

            if ($totalPoints < 100 || $totalPoints > 10000) {
                Utils::jsonResponse(['success' => false, 'message' => 'Los puntos deben estar entre 100 y 10,000'], 400);
            }

            try {
                $result = $this->kpiModel->createQuarter($quarter, $year, $totalPoints, $activateImmediately);

                if ($result) {
                    Utils::jsonResponse(['success' => true, 'message' => 'Trimestre KPI creado exitosamente']);
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el trimestre KPI'], 500);
                }
            } catch (Exception $e) {
                Utils::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }
    
    /**
     * Actualizar trimestre KPI
     */
    public function updateQuarter() {
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quarterId = (int)($_POST['quarterId'] ?? 0);
            $quarter = Utils::sanitizeInput($_POST['quarter'] ?? '');
            $year = (int)($_POST['year'] ?? date('Y'));
            $totalPoints = (int)($_POST['total_points'] ?? 1000);

            // Validaciones
            if ($quarterId <= 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'ID de trimestre inválido'], 400);
            }

            if (empty($quarter) || !in_array($quarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
                Utils::jsonResponse(['success' => false, 'message' => 'Trimestre inválido'], 400);
            }

            if ($year < 2020 || $year > 2030) {
                Utils::jsonResponse(['success' => false, 'message' => 'Año inválido'], 400);
            }

            if ($totalPoints < 100 || $totalPoints > 10000) {
                Utils::jsonResponse(['success' => false, 'message' => 'Los puntos deben estar entre 100 y 10,000'], 400);
            }

            try {
                $result = $this->kpiModel->updateQuarter($quarterId, $quarter, $year, $totalPoints);

                if ($result) {
                    Utils::jsonResponse(['success' => true, 'message' => 'Trimestre actualizado exitosamente']);
                } else {
                    Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar el trimestre'], 500);
                }
            } catch (Exception $e) {
                Utils::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
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
            'loadKpiProjectsJs' => true // Flag to load specific JS
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
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }

        $currentKPI = $this->kpiModel->getCurrentQuarter();
        
        if ($currentKPI) {
            // Verificar proyectos con KPI asignado
            $stmt = $this->db->prepare("
                SELECT project_id, project_name, kpi_points, kpi_quarter_id 
                FROM Projects 
                WHERE kpi_quarter_id = ?
            ");
            $stmt->execute([$currentKPI['kpi_quarter_id']]);
            $projectsWithKPI = $stmt->fetchAll();
            
            $totalAssigned = 0;
            foreach ($projectsWithKPI as $project) {
                $totalAssigned += $project['kpi_points'];
            }
            
            $data = [
                'currentKPI' => $currentKPI,
                'projectsWithKPI' => $projectsWithKPI,
                'totalAssigned' => $totalAssigned,
                'remainingPoints' => $currentKPI['total_points'] - $totalAssigned,
                'assignedPointsFromModel' => $currentKPI['assigned_points'] ?? 0
            ];
            
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        } else {
            echo "No hay trimestre KPI activo";
        }
        exit;
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
                $clanStats[$clanId]['completed_points'] += $progress['completed_points'];
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
}