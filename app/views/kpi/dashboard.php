<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="kpi-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>KPI Dashboard</h1>
                <span class="subtitle">Indicadores de rendimiento</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=kpi/quarters" class="btn-minimal">
                    <i class="fas fa-calendar-alt"></i>
                    Trimestres
                </a>
                <a href="?route=kpi/projects" class="btn-minimal primary">
                    <i class="fas fa-bullseye"></i>
                    Asignar
                </a>
            </div>
        </div>
        
        <!-- Período Actual Minimalista -->
        <?php if ($currentKPI): ?>
            <div class="period-minimal">
                <div class="period-info">
                    <span class="period-name"><?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?></span>
                    <?php 
                    $percentage = $currentKPI['total_points'] > 0 ? 
                        round((($currentKPI['assigned_points'] ?? 0) / $currentKPI['total_points']) * 100, 1) : 0;
                    ?>
                    <div class="period-progress">
                        <span class="progress-label"><?php echo $percentage; ?>% asignado</span>
                        <div class="progress-minimal">
                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-minimal">
                <span>⚠️ No hay trimestre KPI activo</span>
                <a href="?route=kpi/quarters" class="link-minimal">Crear trimestre</a>
            </div>
        <?php endif; ?>
    </header>

    <?php if ($currentKPI): ?>
        <!-- Estadísticas Minimalistas -->
        <div class="content-minimal">
            <section class="stats-minimal">
                <div class="stats-row">
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['total_points']); ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['assigned_points']); ?></div>
                        <div class="stat-label">Asignados</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['available_points']); ?></div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['completed_points'] ?? 0); ?></div>
                        <div class="stat-label">Completados</div>
                    </div>
                </div>
            </section>
            
            <!-- Ranking Minimalista -->
            <section class="ranking-minimal">
                <h3>Ranking de Clanes</h3>
                
                <?php if (!empty($clanRanking)): ?>
                    <div class="ranking-list">
                        <?php foreach (array_slice($clanRanking, 0, 5) as $index => $clan): ?>
                            <div class="rank-item">
                                <div class="rank-position">
                                    <span class="rank-number"><?php echo $index + 1; ?></span>
                                    <?php if ($index < 3): ?>
                                        <span class="medal">
                                            <?php echo $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="clan-info">
                                    <div class="clan-name"><?php echo Utils::escape($clan['clan_name']); ?></div>
                                    <?php if (!empty($clan['clan_departamento'])): ?>
                                        <div class="clan-dept"><?php echo Utils::escape($clan['clan_departamento']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="clan-score">
                                    <span class="points"><?php echo number_format($clan['earned_points'] ?? 0); ?></span>
                                    <span class="label">pts</span>
                                </div>
                                
                                <div class="clan-progress">
                                    <?php 
                                    $efficiency = $clan['total_assigned'] > 0 ? 
                                        round((($clan['earned_points'] ?? 0) / $clan['total_assigned']) * 100, 1) : 0;
                                    ?>
                                    <div class="progress-bar-small">
                                        <div class="progress-fill-small" style="width: <?php echo min($efficiency, 100); ?>%"></div>
                                    </div>
                                    <span class="progress-percent"><?php echo $efficiency; ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-minimal">
                        <span>📊 No hay datos disponibles</span>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Actividad Reciente Minimalista -->
            <section class="activity-minimal">
                <h3>Actividad Reciente</h3>
                
                <?php if (!empty($recentProjects)): ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($recentProjects, 0, 3) as $project): ?>
                            <div class="activity-item">
                                <div class="project-info">
                                    <div class="project-name"><?php echo Utils::escape($project['project_name']); ?></div>
                                    <div class="project-clan"><?php echo Utils::escape($project['clan_name']); ?></div>
                                </div>
                                
                                <div class="project-metrics">
                                    <span class="kpi-points"><?php echo number_format($project['kpi_points'] ?? 0); ?> pts</span>
                                    <span class="task-count"><?php echo $project['tasks_completed'] ?? 0; ?> tareas</span>
                                </div>
                                
                                <div class="project-progress">
                                    <?php $projectProgress = $project['progress_percentage'] ?? 0; ?>
                                    <div class="progress-bar-tiny">
                                        <div class="progress-fill-tiny" style="width: <?php echo $projectProgress; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo round($projectProgress, 1); ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="activity-footer">
                        <a href="?route=kpi/projects" class="link-minimal">Ver todos los proyectos →</a>
                    </div>
                <?php else: ?>
                    <div class="empty-minimal">
                        <span>📋 No hay proyectos recientes</span>
                        <a href="?route=kpi/projects" class="link-minimal">Asignar KPIs</a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
</div>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para KPIs
$additionalCSS = [
    APP_URL . 'assets/css/kpi.css'
];

// JavaScript adicional para KPIs  
$additionalJS = [
    APP_URL . 'assets/js/kpi-dashboard.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?>