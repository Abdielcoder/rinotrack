<?php
ob_start();
?>

<div class="clan-member-projects minimal">
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Proyectos del Clan</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            <div class="actions-minimal">
                <a href="?route=clan_member/tasks" class="btn-minimal"><i class="fas fa-tasks"></i> Tareas</a>
                <a href="?route=logout" class="btn-minimal danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
    </header>

    <div class="content-minimal">
        <?php if (empty($projects)): ?>
            <div class="empty-minimal">No hay proyectos</div>
        <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($projects as $p): ?>
                    <a class="project-card" href="?route=clan_member/tasks&project_id=<?php echo $p['project_id']; ?>">
                        <div class="project-name"><?php echo htmlspecialchars($p['project_name']); ?></div>
                        <div class="project-description"><?php echo htmlspecialchars($p['description']); ?></div>
                        <div class="project-meta">
                            <span>Estado: <?php echo htmlspecialchars($p['status']); ?></span>
                            <span><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


