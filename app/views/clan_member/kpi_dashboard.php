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
    <nav class="cm-subnav">
        <div class="nav-inner">
            <ul>
                <li><a class="cm-subnav-link" href="?route=clan_member/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/projects"><i class="fas fa-project-diagram"></i> Proyectos</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/tasks"><i class="fas fa-tasks"></i> Tareas</a></li>
                <li><a class="cm-subnav-link active" href="?route=clan_member/kpi-dashboard"><i class="fas fa-chart-bar"></i> KPI</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/availability"><i class="fas fa-user-clock"></i> Disponibilidad</a></li>
            </ul>
        </div>
    </nav>

    <div class="content-minimal">
        <?php if (!$currentKPI): ?>
            <div class="empty-minimal">No hay trimestre KPI activo</div>
        <?php else: ?>
            <section class="user-kpi-summary" style="margin-bottom:14px;">
                <div class="quarter-card" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:center;">
                    <div>
                        <h3 style="margin:0 0 6px;">Mis indicadores</h3>
                        <div style="display:flex;gap:14px;color:#475569;font-size:.95rem;">
                            <div><strong>Meta trimestral:</strong> <?php echo number_format($userKPI['target_points'] ?? 1000); ?> pts</div>
                            <div><strong>Ganados:</strong> <?php echo number_format((float)($userKPI['earned_points'] ?? 0),2); ?> pts</div>
                            <div><strong>Tareas:</strong> <?php echo (int)($userKPI['completed_tasks'] ?? 0); ?>/<?php echo (int)($userKPI['total_tasks'] ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="progress-container" style="justify-self:end; width:100%; max-width:420px;">
                        <div class="progress-bar" style="height:10px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                            <div class="progress-fill" style="height:100%;width: <?php echo (float)($userKPI['progress_percentage'] ?? 0); ?>%;background:linear-gradient(90deg,#10b981,#22c55e);"></div>
                        </div>
                        <div style="text-align:right;font-weight:600;color:#0f766e;margin-top:6px;">
                            <?php echo number_format((float)($userKPI['progress_percentage'] ?? 0),1); ?>%
                        </div>
                    </div>
                </div>
            </section>

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
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css', APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


