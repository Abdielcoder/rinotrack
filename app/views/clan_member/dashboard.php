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
            <h3>Mis indicadores</h3>
            <div class="progress-card">
                <div class="progress-info">
                    <div class="progress-left">
                        <span class="progress-label">Tareas completadas</span>
                        <span class="progress-value"><?php echo (int)($userTaskStats['completed_tasks'] ?? 0); ?> / <?php echo (int)($userTaskStats['total_tasks'] ?? 0); ?></span>
                    </div>
                    <div class="progress-right">
                        <div class="completion-box">
                            <span class="completion-percentage"><?php echo $userTaskStats['completion_percentage'] ?? 0; ?>%</span>
                            <span class="completion-label">Completado</span>
                        </div>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-main">
                        <div class="progress-segment" style="width: <?php echo $userTaskStats['completion_percentage'] ?? 0; ?>%"></div>
                    </div>
                    <span class="remaining-text"><?php echo 100 - ($userTaskStats['completion_percentage'] ?? 0); ?>% restante</span>
                </div>
            </div>
        </section>

        <section class="contributions-section">
            <h3>Mi contribución</h3>
            <div class="contributions-grid">
                <div class="contribution-card">
                    <div class="member-info">
                        <div class="member-name"><?php echo htmlspecialchars($ownContribution['full_name'] ?? ($user['full_name'] ?? '')); ?></div>
                        <div class="member-tasks">
                            <span class="task-count"><?php echo (int)($ownContribution['completed_tasks'] ?? 0); ?> tareas</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


