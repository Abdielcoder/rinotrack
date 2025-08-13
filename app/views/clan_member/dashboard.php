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
    <nav class="cm-subnav">
        <div class="nav-inner">
            <ul>
                <li><a class="cm-subnav-link active" href="?route=clan_member/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/projects"><i class="fas fa-project-diagram"></i> Proyectos</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/tasks"><i class="fas fa-tasks"></i> Tareas</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/kpi-dashboard"><i class="fas fa-chart-bar"></i> KPI</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/availability"><i class="fas fa-user-clock"></i> Disponibilidad</a></li>
            </ul>
        </div>
    </nav>

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
                        <a class="project-card" href="?route=clan_member/project-tasks&project_id=<?php echo $p['project_id']; ?>&open_create=1">
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
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css', APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
// Inyectar los datos de la contribución propia para el modal
$content .= '<script>window.ownContributionDetails = ' . json_encode($ownContributionDetails ?? []) . ';</script>';
// Script mínimo para modal propio
$modalScript = <<<'JS'
<script>
function openOwnContributionModal(){
  var data = window.ownContributionDetails || {stats:{},tasks:[]};
  var modal = document.getElementById("ownContributionModal");
  if(!modal){
    var html = "\n<div id=\"ownContributionModal\" class=\"modal open\">\n  <div class=\"modal-content modal-lg\">\n    <div class=\"modal-header\">\n      <h3>Mis tareas</h3>\n      <button class=\"modal-close\" onclick=\"closeOwnContributionModal()\">&times;</button>\n    </div>\n    <div class=\"modal-toolbar\">\n      <input type=\"text\" id=\"ownSearch\" placeholder=\"Buscar tarea...\" oninput=\"filterOwnTasks()\"/>\n      <select id=\"ownStatus\" onchange=\"filterOwnTasks()\">\n        <option value=\"\">Todos</option>\n        <option value=\"completed\">Completadas</option>\n        <option value=\"in_progress\">En progreso</option>\n        <option value=\"pending\">Pendientes</option>\n      </select>\n    </div>\n    <div class=\"modal-body\">\n      <div id=\"ownContributionStats\" style=\"margin-bottom:12px;\"></div>\n      <div id=\"ownContributionList\" class=\"tasks-list-modal\"></div>\n    </div>\n  </div>\n</div>\n";
    document.body.insertAdjacentHTML('beforeend', html);
    modal = document.getElementById("ownContributionModal");
  } else {
    modal.classList.add('open');
  }
  var stats = data.stats||{};
  document.getElementById("ownContributionStats").innerHTML = 
    "Total: "+(stats.total||0)+" | Completadas: "+(stats.completed||0)+" | En progreso: "+(stats.in_progress||0)+" | Pendientes: "+(stats.pending||0);
  var list = document.getElementById("ownContributionList");
  if(!list) return;
  if(!data.tasks || data.tasks.length===0){ list.innerHTML = '<div class="empty-minimal">Sin tareas</div>'; return; }
  window._ownTasksRaw = data.tasks.slice();
  renderOwnTasks(_ownTasksRaw);
}
function closeOwnContributionModal(){
  var modal = document.getElementById("ownContributionModal");
  if(modal) modal.classList.remove('open');
}

function filterOwnTasks(){
  var q = (document.getElementById('ownSearch')?.value || '').toLowerCase();
  var st = document.getElementById('ownStatus')?.value || '';
  var tasks = (window._ownTasksRaw||[]).filter(function(t){
    var okQ = !q || (t.task_name||'').toLowerCase().includes(q) || (t.project_name||'').toLowerCase().includes(q);
    var okS = !st || (t.status===st);
    return okQ && okS;
  });
  renderOwnTasks(tasks);
}

function renderOwnTasks(tasks){
  var list = document.getElementById('ownContributionList');
  if(!list) return;
  if(tasks.length===0){ list.innerHTML = '<div class="empty-minimal">Sin resultados</div>'; return; }
  list.innerHTML = tasks.map(function(t){
    var due = t.due_date ? new Date(t.due_date).toLocaleDateString() : '';
    var chip = '<span class="chip chip-status '+(t.status||'')+'">'+(t.status||'')+'</span>';
    return '<div class="task-row">'
      + '<div class="row-main">'
      +   '<div class="task-title-strong">'+(t.task_name||'')+'</div>'
      +   '<div class="row-meta">'
      +     '<span class="task-project"><i class="fas fa-project-diagram"></i> '+(t.project_name||'')+'</span>'
      +     + (due? '<span><i class="fas fa-calendar"></i> Vence: '+due+'</span>' : '')
      +   '</div>'
      +   '<div class="divider-soft"></div>'
      +   '<div>'+chip+'</div>'
      + '</div>'
      + '<div class="row-actions">'
      + '</div>'
      + '</div>';
  }).join('');
}
</script>
JS;
$content .= $modalScript;
require_once __DIR__ . '/../layout.php';
?>


