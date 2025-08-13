<?php
ob_start();
?>

<div class="clan-member-kpi-dashboard minimal">
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Dashboard KPI</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            <div class="actions-minimal">
                <a href="?route=clan_member/dashboard" class="btn-minimal"><i class="fas fa-arrow-left"></i> Volver</a>
                <a href="?route=logout" class="btn-minimal danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
    </header>

    <div class="content-minimal">
        <?php if (!$currentKPI): ?>
            <div class="empty-minimal">No hay trimestre KPI activo</div>
        <?php else: ?>
            <section class="kpi-quarter-info">
                <div class="quarter-card">
                    <div class="quarter-header">
                        <h3>Trimestre</h3>
                        <span class="quarter-badge">Q<?php echo $currentKPI['quarter']; ?> <?php echo $currentKPI['year']; ?></span>
                    </div>
                    <div class="quarter-stats">
                        <div class="stat-item">
                            <span class="stat-label">Puntos Totales del Clan</span>
                            <span class="stat-value"><?php echo number_format($clanTotalPoints); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Puntos Completados (estimado)</span>
                            <span class="stat-value"><?php echo number_format($clanCompletedPoints); ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="kpi-projects">
                <h3>Proyectos con KPI</h3>
                <?php if (empty($projects)): ?>
                    <div class="empty-minimal">No hay proyectos con KPI</div>
                <?php else: ?>
                    <div class="projects-kpi-list">
                        <?php foreach ($projects as $project): ?>
                            <div class="project-kpi-item">
                                <div class="project-info">
                                    <div class="project-details">
                                        <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                        <div class="project-description"><?php echo htmlspecialchars($project['description']); ?></div>
                                        <div class="project-kpi-info">
                                            <span class="kpi-points"><i class="fas fa-chart-line"></i> <?php echo number_format($project['kpi_points']); ?> puntos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="project-progress">
                                    <?php $progress = $project['progress_percentage'] ?? 0; ?>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo round($progress,1); ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?php echo round($progress,1); ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


