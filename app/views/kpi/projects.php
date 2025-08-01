<?php
// Configurar archivos adicionales para layout
$additionalCSS = [
    '/RinoTrack/public/assets/css/kpi.css'
];

$additionalJS = [
    '/RinoTrack/public/assets/js/kpi-projects.js'
];

// Calcular puntos
$assignedPoints = 0;
foreach ($projects as $project) {
    $assignedPoints += $project['kpi_points'];
}
$remainingPoints = $currentKPI ? $currentKPI['total_points'] - $assignedPoints : 0;

// Iniciar captura de contenido
ob_start();
?>

<div class="kpi-projects-container minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Asignación de KPIs</h1>
                <span class="subtitle">Gestión de proyectos y puntos KPI</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=admin" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Admin
                </a>
                <a href="?route=kpi/dashboard" class="btn-minimal">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="?route=kpi/quarters" class="btn-minimal">
                    <i class="fas fa-calendar-alt"></i>
                    Trimestres
                </a>
            </div>
        </div>
        
        <!-- Período Actual Minimalista -->
        <?php if ($currentKPI): ?>
            <div class="period-minimal">
                <div class="period-info">
                    <span class="period-name">
                        ✅ <?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?> (Activo)
                    </span>
                    <div class="period-stats">
                        <span class="stat-item"><?php echo number_format($remainingPoints); ?> disponibles</span>
                        <span class="stat-separator">de</span>
                        <span class="stat-item"><?php echo number_format($currentKPI['total_points']); ?> totales</span>
                    </div>
                    <?php if (isset($_GET['debug'])): ?>
                    <!-- Debug info -->
                    <div style="background: #f0f0f0; padding: 5px; margin: 5px 0; font-size: 12px; color: #666;">
                        Debug: remainingPoints=<?php echo $remainingPoints; ?>, 
                        assignedPoints=<?php echo $assignedPoints; ?>, 
                        total_points=<?php echo $currentKPI['total_points']; ?>
                    </div>
                    <?php endif; ?>
                    <div class="period-progress">
                        <span class="progress-label"><?php echo $currentKPI['total_points'] > 0 ? round(($assignedPoints / $currentKPI['total_points']) * 100, 1) : 0; ?>% asignado</span>
                        <div class="progress-minimal">
                            <div class="progress-fill" style="width: <?php echo $currentKPI['total_points'] > 0 ? ($assignedPoints / $currentKPI['total_points']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-minimal">
                <span>⚠️ No hay período KPI activo</span>
                <a href="?route=kpi/quarters" class="link-minimal">Gestionar Trimestres</a>
            </div>
        <?php endif; ?>
    </header>

    <?php if ($currentKPI): ?>
        <!-- Contenido Principal Minimalista -->
        <main class="content-minimal">
            <!-- Proyectos Pendientes -->
            <?php if (!empty($projectsWithoutKPI)): ?>
                <section class="projects-minimal">
                    <div class="section-header-minimal">
                        <h3>Proyectos Pendientes (<?php echo count($projectsWithoutKPI); ?>)</h3>
                    </div>
                
                    <div class="projects-list">
                        <?php foreach ($projectsWithoutKPI as $project): ?>
                            <div class="project-item pending" data-project-id="<?php echo $project['project_id']; ?>">
                                <div class="project-info">
                                    <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                    <div class="project-clan"><?php echo htmlspecialchars($project['clan_name'] ?? 'Sin asignar'); ?></div>
                                </div>
                                
                                <div class="project-meta">
                                    <span class="project-date"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span>
                                    <span class="project-status">⏳ Pendiente</span>
                                </div>
                                
                                <div class="project-action">
                                    <button class="btn-minimal primary" onclick="openAssignKPIModal(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['project_name']); ?>')">
                                        <i class="fas fa-plus"></i>
                                        Asignar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
        <?php endif; ?>

            <!-- Proyectos con KPI -->
            <?php if (!empty($projects)): ?>
                <section class="projects-minimal">
                    <div class="section-header-minimal">
                        <h3>Proyectos con KPI (<?php echo count($projects); ?>)</h3>
                    </div>
                
                    <div class="projects-list">
                        <?php foreach ($projects as $project): ?>
                            <?php 
                            // Calcular progreso simplificado
                            $progressPercentage = $project['progress_percentage'];
                            ?>
                            <div class="project-item assigned" data-project-id="<?php echo $project['project_id']; ?>">
                                <div class="project-info">
                                    <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                    <div class="project-clan"><?php echo htmlspecialchars($project['clan_name'] ?? 'Sin asignar'); ?></div>
                                </div>
                                
                                <div class="project-kpi">
                                    <span class="kpi-points"><?php echo number_format($project['kpi_points']); ?> pts</span>
                                    <span class="distribution-mode">
                                        <?php echo $project['task_distribution_mode'] === 'automatic' ? '🔄 Auto' : '📊 %'; ?>
                                    </span>
                                </div>
                                
                                <div class="project-progress">
                                    <div class="progress-bar-small">
                                        <div class="progress-fill-small" style="width: <?php echo $progressPercentage; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo round($progressPercentage, 1); ?>%</span>
                                </div>
                                
                                <div class="project-actions">
                                    <button class="btn-action" onclick='openTasksModal(<?php echo $project['project_id']; ?>, <?php echo json_encode($project['project_name']); ?>)' title="Gestionar Tareas">
                                        <i class="fas fa-tasks"></i>
                                    </button>
                                    <button class="btn-action" onclick='openEditKPIModal(<?php echo $project['project_id']; ?>, <?php echo json_encode($project['project_name']); ?>, <?php echo $project['kpi_points']; ?>)' title="Editar KPI">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" onclick='toggleDistributionMode(<?php echo $project['project_id']; ?>, <?php echo json_encode($project['task_distribution_mode']); ?>)' title="Cambiar distribución">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
        <?php endif; ?>

            <!-- Estado Vacío -->
            <?php if (empty($projects) && empty($projectsWithoutKPI)): ?>
                <section class="empty-minimal">
                    <span>📋 No hay proyectos disponibles</span>
                    <a href="?route=admin/projects" class="btn-minimal primary">
                        <i class="fas fa-plus"></i>
                        Crear Proyecto
                    </a>
                </section>
            <?php endif; ?>
        </main>
    <?php endif; ?>
</div>

<!-- Modales modernizados -->

<!-- Modal Asignar KPI -->
<div id="assignKPIModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content large">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-plus-circle"></i>
                <span>Asignar KPI al Proyecto</span>
            </div>
            <button class="modal-close" onclick="closeAssignKPIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="assignKPIForm" onsubmit="assignKPI(event)">
            <input type="hidden" id="assign_project_id" name="project_id">
            
            <div class="modal-body">
                <div class="project-display-card">
                    <div class="project-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="project-info">
                        <h4 id="assign_project_name">Nombre del Proyecto</h4>
                        <p>Asigna puntos KPI a este proyecto para el período activo</p>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="kpi_points" class="form-label">
                            <i class="fas fa-star"></i>
                            Puntos KPI a Asignar
                        </label>
                        <input type="number" 
                               id="kpi_points" 
                               name="kpi_points" 
                               class="form-input"
                               min="1" 
                               max="<?php echo $currentKPI['total_points'] ?? 1000; ?>" 
                               placeholder="Ingresa los puntos..."
                               required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Puntos disponibles: <span id="available_points" class="highlight"><?php echo number_format($remainingPoints); ?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cogs"></i>
                            Modalidad de Distribución
                        </label>
                        <div class="distribution-options">
                            <label class="distribution-option">
                                <input type="radio" name="distribution_mode" value="automatic" checked>
                                <div class="option-content">
                                    <div class="option-header">
                                        <span class="option-icon">🤖</span>
                                        <span class="option-title">Distribución Automática</span>
                                    </div>
                                    <p class="option-description">
                                        Los puntos se distribuyen equitativamente entre todas las tareas del proyecto
                                    </p>
                                </div>
                            </label>
                            
                            <label class="distribution-option">
                                <input type="radio" name="distribution_mode" value="percentage">
                                <div class="option-content">
                                    <div class="option-header">
                                        <span class="option-icon">📊</span>
                                        <span class="option-title">Por Porcentaje Manual</span>
                                    </div>
                                    <p class="option-description">
                                        Defines manualmente qué porcentaje de puntos vale cada tarea
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeAssignKPIModal()">
                    <i class="fas fa-times"></i>
                    <span>Cancelar</span>
                </button>
                <button type="submit" class="action-btn primary">
                    <i class="fas fa-check"></i>
                    <span>Asignar KPI</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar KPI -->
<div id="editKPIModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit"></i>
                <span>Editar KPI del Proyecto</span>
            </div>
            <button class="modal-close" onclick="closeEditKPIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editKPIForm" onsubmit="editKPI(event)">
            <input type="hidden" id="edit_project_id" name="project_id">
            
            <div class="modal-body">
                <div class="project-display-card">
                    <div class="project-icon edit">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="project-info">
                        <h4 id="edit_project_name">Nombre del Proyecto</h4>
                        <p>Modifica los puntos KPI asignados a este proyecto</p>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="edit_kpi_points" class="form-label">
                            <i class="fas fa-star"></i>
                            Nuevos Puntos KPI
                        </label>
                        <input type="number" 
                               id="edit_kpi_points" 
                               name="kpi_points" 
                               class="form-input"
                               min="1" 
                               placeholder="Ingresa los nuevos puntos..."
                               required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Puntos actuales: <span id="current_points" class="highlight">0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeEditKPIModal()">
                    <i class="fas fa-times"></i>
                    <span>Cancelar</span>
                </button>
                <button type="submit" class="action-btn primary">
                    <i class="fas fa-save"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tareas -->
<div id="tasksModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content large">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-tasks"></i>
                <span id="tasksModalTitle">Gestionar Tareas</span>
            </div>
            <button class="modal-close" onclick="closeTasksModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="tasksModalBody">
            <!-- El contenido se cargará aquí vía AJAX -->
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Cargando...
            </div>
        </div>
    </div>
</div>

<?php
// Capturar contenido y pasarlo al layout
$content = ob_get_clean();
require_once __DIR__ . '/../admin/layout.php';
?>