<?php
ob_start();
?>

<div class="clan-member-availability minimal">
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Mi disponibilidad</h1>
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
                <li><a class="cm-subnav-link" href="?route=clan_member/kpi-dashboard"><i class="fas fa-chart-bar"></i> KPI</a></li>
                <li><a class="cm-subnav-link active" href="?route=clan_member/availability"><i class="fas fa-user-clock"></i> Disponibilidad</a></li>
            </ul>
        </div>
    </nav>

    <div class="content-minimal">
        <section class="availability-summary">
            <div class="summary-card">
                <div class="summary-header"><h3>Tareas activas próximas 15 días</h3></div>
                <div class="summary-value"><?php echo (int)($summary['task_count'] ?? 0); ?></div>
            </div>
        </section>

        <section class="tasks-upcoming">
            <h3>Mis tareas</h3>
            <?php if (empty($tasks)): ?>
                <div class="empty-minimal">Sin tareas próximas</div>
            <?php else: ?>
                <div class="tasks-list">
                    <?php foreach ($tasks as $t): ?>
                        <div class="task-item">
                            <div class="task-title"><?php echo htmlspecialchars($t['task_name']); ?></div>
                            <div class="task-meta">
                                <span>Proyecto: <?php echo htmlspecialchars($t['project_name']); ?></span>
                                <?php if (!empty($t['due_date'])): ?>
                                    <span>Vence: <?php echo date('d/m/Y', strtotime($t['due_date'])); ?></span>
                                <?php endif; ?>
                                <span>Estado: <?php echo htmlspecialchars($t['status']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css', APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


