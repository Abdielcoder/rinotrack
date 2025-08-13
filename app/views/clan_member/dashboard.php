<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-member-dashboard minimal">
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Mi Clan</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            <div class="actions-minimal">
                <a href="?route=clan_member/kpi-dashboard" class="btn-minimal primary">
                    <i class="fas fa-chart-line"></i>
                    KPI
                </a>
                <a href="?route=clan_member/availability" class="btn-minimal">
                    <i class="fas fa-user-clock"></i>
                    Mi disponibilidad
                </a>
                <a href="?route=logout" class="btn-minimal danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </header>

    <div class="content-minimal">
        <section class="team-progress-section">
            <h3>Progreso general del equipo</h3>
            <div class="progress-card">
                <div class="progress-info">
                    <div class="progress-left">
                        <span class="progress-label">Tareas completadas</span>
                        <span class="progress-value"><?php echo (int)$teamProgress['completed_tasks']; ?> / <?php echo (int)$teamProgress['total_tasks']; ?></span>
                    </div>
                    <div class="progress-right">
                        <div class="completion-box">
                            <span class="completion-percentage"><?php echo $teamProgress['completion_percentage']; ?>%</span>
                            <span class="completion-label">Completado</span>
                        </div>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-main">
                        <div class="progress-segment" style="width: <?php echo $teamProgress['completion_percentage']; ?>%"></div>
                    </div>
                    <span class="remaining-text"><?php echo 100 - $teamProgress['completion_percentage']; ?>% restante</span>
                </div>
            </div>
        </section>

        <section class="contributions-section">
            <h3>Contribuciones por colaborador</h3>
            <div class="contributions-grid">
                <?php if (empty($memberContributions)): ?>
                    <div style="text-align:center;color:#666;padding:20px;">Sin datos</div>
                <?php else: foreach ($memberContributions as $member): ?>
                    <div class="contribution-card">
                        <div class="member-info">
                            <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                            <div class="member-tasks">
                                <span class="task-count"><?php echo (int)$member['completed_tasks']; ?> tareas (<?php echo $member['contribution_percentage']; ?>%)</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </section>

        <section class="projects-list">
            <h3>Proyectos del clan</h3>
            <?php if (empty($projects)): ?>
                <div class="empty-minimal">No hay proyectos</div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $p): ?>
                        <a class="project-card" href="?route=clan_member/tasks&project_id=<?php echo $p['project_id']; ?>">
                            <div class="project-name"><?php echo htmlspecialchars($p['project_name']); ?></div>
                            <div class="project-meta">
                                <span class="status">Estado: <?php echo htmlspecialchars($p['status']); ?></span>
                                <span class="date"><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


