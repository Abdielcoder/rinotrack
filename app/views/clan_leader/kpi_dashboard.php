<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-kpi-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Dashboard KPI del Clan</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? 'Clan'); ?></span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=clan_leader/dashboard" class="btn-minimal primary">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Información del Trimestre KPI -->
    <div class="content-minimal">
        <?php if ($currentKPI): ?>
            <section class="kpi-quarter-info">
                <div class="quarter-card">
                                <div class="quarter-header">
                <h3>Límite de Puntos del Clan</h3>
                <span class="quarter-badge">Q<?php echo $currentKPI['quarter']; ?> <?php echo $currentKPI['year']; ?></span>
            </div>
                    <div class="quarter-stats">
                        <div class="stat-item">
                            <span class="stat-label">Puntos Totales del Clan</span>
                            <span class="stat-value"><?php echo number_format($clanTotalPoints); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Puntos Asignados</span>
                            <span class="stat-value"><?php echo number_format($clanAssignedPoints); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Puntos Disponibles</span>
                            <span class="stat-value"><?php echo number_format($clanAvailablePoints); ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Snake Path para Miembros del Clan -->
            <?php if ($snakePathData && !empty($snakePathData['members_data'])): ?>
                <section class="snake-path-section">
                    <div class="snake-path-container">
                        <div class="snake-path-header">
                            <h3>Progreso de Miembros del Clan</h3>
                            <div class="quarter-info">
                                <span class="quarter-label"><?php echo htmlspecialchars($snakePathData['quarter_info']['display_name'] ?? 'Trimestre'); ?></span>
                                <span class="quarter-period">
                                    <?php
                                    $quarterMonths = [
                                        'Q1' => ['ENE', 'FEB', 'MAR'],
                                        'Q2' => ['ABR', 'MAY', 'JUN'],
                                        'Q3' => ['JUL', 'AGO', 'SEP'],
                                        'Q4' => ['OCT', 'NOV', 'DIC']
                                    ];
                                    $months = $quarterMonths[$snakePathData['quarter_info']['quarter'] ?? 'Q1'] ?? ['MES1', 'MES2', 'MES3'];
                                    echo implode(' - ', $months);
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="snake-path-board">
                            <div class="path-grid" id="snakePathGrid">
                                <!-- El camino se generará dinámicamente con JavaScript -->
                            </div>
                            
                            <div class="member-markers" id="memberMarkers">
                                <!-- Los marcadores de miembros se generarán dinámicamente -->
                            </div>
                        </div>
                        
                        <div class="snake-path-legend">
                            <div class="legend-title">Miembros del Clan</div>
                            <div class="legend-items" id="memberLegend">
                                <!-- La leyenda se generará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Proyectos con KPI -->
            <section class="kpi-projects">
                <h3>Proyectos con KPI Asignado</h3>
                <?php if (!empty($projects)): ?>
                    <div class="projects-kpi-list">
                        <?php foreach ($projects as $project): ?>
                            <div class="project-kpi-item">
                                <div class="project-info">
                                    <div class="project-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div class="project-details">
                                        <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                        <div class="project-description"><?php echo htmlspecialchars($project['description']); ?></div>
                                        <div class="project-kpi-info">
                                            <span class="kpi-points">
                                                <i class="fas fa-chart-line"></i>
                                                <?php echo number_format($project['kpi_points']); ?> puntos KPI
                                                <?php if ($project['kpi_points'] > 0 && $clanTotalPoints > 0): ?>
                                                    <span class="percentage-info">
                                                        (<?php echo round(($project['kpi_points'] / $clanTotalPoints) * 100, 1); ?>% de <?php echo number_format($clanTotalPoints); ?> puntos del clan)
                                                    </span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="project-status status-<?php echo $project['status']; ?>">
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="project-progress">
                                    <?php
                                    // Calcular porcentaje del proyecto basado en los 1000 puntos del clan
                                    $projectPercentage = 0;
                                    if ($project['kpi_points'] > 0 && $clanTotalPoints > 0) {
                                        $projectPercentage = round(($project['kpi_points'] / $clanTotalPoints) * 100, 1);
                                    }
                                    ?>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $projectPercentage; ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?php echo $projectPercentage; ?>% del clan</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-minimal">
                        <span>📊 No hay proyectos con KPI asignado</span>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Proyectos sin KPI -->
            <section class="projects-without-kpi">
                <h3>Proyectos sin KPI</h3>
                <?php if (!empty($projectsWithoutKPI)): ?>
                    <div class="projects-no-kpi-list">
                        <?php foreach ($projectsWithoutKPI as $project): ?>
                            <div class="project-no-kpi-item">
                                <div class="project-info">
                                    <div class="project-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div class="project-details">
                                        <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                        <div class="project-description"><?php echo htmlspecialchars($project['description']); ?></div>
                                        <div class="project-meta">
                                            <span class="project-status status-<?php echo $project['status']; ?>">
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                            <span class="project-date">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('d/m/Y', strtotime($project['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="project-actions">
                                    <button class="btn-minimal primary" onclick="openAssignKPIModal(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['project_name']); ?>')">
                                        <i class="fas fa-chart-line"></i>
                                        Asignar KPI
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-minimal">
                        <span>✅ Todos los proyectos tienen KPI asignado</span>
                    </div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <div class="empty-minimal">
                <span>📅 No hay trimestre KPI activo</span>
                <p>Contacta al administrador para crear un nuevo trimestre KPI.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para asignar KPI -->
<div id="assignKPIModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Asignar KPI al Proyecto</h3>
            <button class="modal-close" onclick="closeAssignKPIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="assignKPIForm" class="modal-form">
                <input type="hidden" id="assignProjectId" name="projectId">
                
                <div class="form-group">
                    <label for="projectNameDisplay">
                        <i class="fas fa-project-diagram"></i>
                        Proyecto
                    </label>
                    <input type="text" id="projectNameDisplay" readonly>
                </div>
                
                <div class="form-group">
                    <label for="kpiPoints">
                        <i class="fas fa-chart-line"></i>
                        Puntos KPI
                    </label>
                    <input type="number" id="kpiPoints" name="kpiPoints" required 
                           min="1" max="1000" placeholder="Ingrese los puntos KPI">
                    <small class="form-help">Puntos disponibles: <span id="availablePoints">0</span></small>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="action-btn secondary" onclick="closeAssignKPIModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="assignKPIForm" class="action-btn primary">
                <i class="fas fa-chart-line"></i>
                <span>Asignar KPI</span>
            </button>
        </div>
    </div>
</div>

<!-- Script para pasar datos del snake path al JavaScript -->
<script>
window.snakePathData = <?= json_encode($snakePathData ?? []) ?>;
console.log('Datos del snake path pasados desde PHP:', window.snakePathData);
console.log('Miembros disponibles:', window.snakePathData?.members_data);
</script>

<script>
// Funciones para el modal de asignar KPI
function openAssignKPIModal(projectId, projectName) {
    document.getElementById('assignProjectId').value = projectId;
    document.getElementById('projectNameDisplay').value = projectName;
    document.getElementById('assignKPIModal').style.display = 'flex';
    loadAvailablePoints();
}

function closeAssignKPIModal() {
    document.getElementById('assignKPIModal').style.display = 'none';
    document.getElementById('assignKPIForm').reset();
}

// Cargar puntos disponibles
function loadAvailablePoints() {
    fetch('?route=clan_leader/get-available-points', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('availablePoints').textContent = data.available_points;
            document.getElementById('kpiPoints').max = data.available_points;
        }
    })
    .catch(error => {
        console.error('Error al cargar puntos disponibles:', error);
        showToast('Error al cargar puntos disponibles', 'error');
    });
}

// Manejar envío del formulario de asignar KPI
document.getElementById('assignKPIForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('?route=clan_leader/assign-kpi', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeAssignKPIModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    });
});

// Cerrar modal al hacer clic fuera
document.getElementById('assignKPIModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssignKPIModal();
    }
});

// Validar puntos KPI
document.getElementById('kpiPoints').addEventListener('input', function() {
    const availablePoints = parseInt(document.getElementById('availablePoints').textContent);
    const inputPoints = parseInt(this.value);
    
    if (inputPoints > availablePoints) {
        this.setCustomValidity(`No puedes asignar más de ${availablePoints} puntos`);
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js',
    APP_URL . 'assets/js/clan-leader-snake-path.js'
];

// Agregar estilos CSS adicionales para el porcentaje
$content .= '
<style>
.percentage-info {
    font-size: 0.85em;
    color: #6b7280;
    font-weight: 400;
    margin-left: 8px;
}

.kpi-points {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.project-progress .progress-text {
    font-size: 0.9em;
    color: #374151;
    font-weight: 600;
}

.progress-container {
    position: relative;
    background: #f3f4f6;
    border-radius: 8px;
    overflow: hidden;
    height: 12px;
}

.progress-fill {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 8px;
}

.progress-bar {
    width: 100%;
    height: 100%;
    position: relative;
}
</style>
';

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 