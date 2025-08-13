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
                <div class="contribution-card clickable" onclick="openOwnContributionModal()">
                    <div class="member-info">
                        <div class="member-name"><?php echo htmlspecialchars($ownContribution['full_name'] ?? ($user['full_name'] ?? '')); ?></div>
                        <div class="member-tasks">
                            <span class="task-count"><?php echo (int)($ownContribution['completed_tasks'] ?? 0); ?> tareas</span>
                        </div>
                    </div>
                </div>
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
// Inyectar los datos de la contribución propia para el modal
$content .= '<script>window.ownContributionDetails = ' . json_encode($ownContributionDetails ?? []) . ';</script>';
// Script mínimo para modal propio
$content .= '<script>
function openOwnContributionModal(){
  var data = window.ownContributionDetails || {stats:{},tasks:[]};
  var modal = document.getElementById("ownContributionModal");
  if(!modal){
    var html = '\n<div id="ownContributionModal" class="modal" style="display:flex">\n  <div class="modal-content" style="max-width:700px;">\n    <div class="modal-header">\n      <h3>Mis tareas</h3>\n      <button class="modal-close" onclick="closeOwnContributionModal()">&times;</button>\n    </div>\n    <div class="modal-body">\n      <div id="ownContributionStats" style="margin-bottom:12px;"></div>\n      <div id="ownContributionList" class="tasks-list"></div>\n    </div>\n  </div>\n</div>\n';
    document.body.insertAdjacentHTML('beforeend', html);
    modal = document.getElementById("ownContributionModal");
  } else {
    modal.style.display = "flex";
  }
  var stats = data.stats||{};
  document.getElementById("ownContributionStats").innerHTML = 
    'Total: '+(stats.total||0)+' | Completadas: '+(stats.completed||0)+' | En progreso: '+(stats.in_progress||0)+' | Pendientes: '+(stats.pending||0);
  var list = document.getElementById("ownContributionList");
  if(!list) return;
  if(!data.tasks || data.tasks.length===0){ list.innerHTML = '<div class="empty-minimal">Sin tareas</div>'; return; }
  list.innerHTML = data.tasks.map(function(t){
    var due = t.due_date ? new Date(t.due_date).toLocaleDateString() : '';
    return '<div class="task-item">'
      +'<div class="task-title">'+(t.task_name||'')+'</div>'
      +'<div class="task-meta"><span>Proyecto: '+(t.project_name||'')+'</span> '
      + (due? '<span>Vence: '+due+'</span> ':'')
      + '<span>Estado: '+(t.status||'')+'</span></div>'
      +'</div>';
  }).join('');
}
function closeOwnContributionModal(){
  var modal = document.getElementById("ownContributionModal");
  if(modal) modal.style.display = "none";
}
</script>';
require_once __DIR__ . '/../layout.php';
?>


