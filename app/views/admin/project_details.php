<?php
// Buffer de salida
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
  <nav class="modern-nav glass">
    <div class="nav-container">
      <div class="nav-brand">
        <div class="brand-icon gradient-bg"><i class="fas fa-project-diagram"></i></div>
        <span class="brand-text">Detalle de Proyecto</span>
      </div>
      <ul class="nav-menu">
        <li class="nav-item"><a href="?route=admin" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li class="nav-item active"><a href="?route=admin/projects" class="nav-link"><i class="fas fa-project-diagram"></i><span>Proyectos</span></a></li>
      </ul>
      <div class="user-menu">
        <div class="user-avatar modern-avatar"><span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span></div>
        <div class="user-info"><span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span><span class="user-role">Administrador</span></div>
        <div class="user-actions"><a href="?route=logout" class="action-btn logout" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a></div>
      </div>
    </div>
  </nav>

  <main class="main-content">
    <header class="page-header animate-fade-in">
      <div class="header-content">
        <h1 class="page-title"><i class="fas fa-folder-open"></i> <?php echo Utils::escape($project['project_name']); ?></h1>
        <div class="header-actions"><a class="btn btn-secondary" href="?route=admin/projects"><i class="fas fa-arrow-left"></i> Volver</a></div>
      </div>
    </header>

    <section class="content-section animate-fade-in">
      <div class="project-overview">
        <div class="overview-card">
          <h4>Resumen</h4>
          <div class="overview-grid">
            <div><span class="label">Clan:</span> <span class="value"><?php echo Utils::escape($project['clan_name'] ?? ''); ?></span></div>
            <div><span class="label">Estado:</span> <span class="value badge status-<?php echo $project['status']; ?>"><?php echo $project['status']; ?></span></div>
            <div><span class="label">Progreso:</span> <span class="value"><?php echo number_format($stats['progress'], 1); ?>%</span></div>
            <div><span class="label">Creado:</span> <span class="value"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span></div>
          </div>
          <div class="progress-bar large"><div class="progress-fill" style="width: <?php echo (float)$stats['progress']; ?>%"></div></div>
        </div>

        <div class="overview-card">
          <h4>Estado de Tareas</h4>
          <div class="stats-row">
            <div class="stat"><span class="num"><?php echo (int)$stats['total']; ?></span><span class="cap">Total</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['pending']; ?></span><span class="cap">Pendientes</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['in_progress']; ?></span><span class="cap">En progreso</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['completed']; ?></span><span class="cap">Completadas</span></div>
            <div class="stat"><span class="num warn"><?php echo (int)$stats['overdue']; ?></span><span class="cap">Vencidas</span></div>
          </div>
        </div>
      </div>

      <div class="two-cols">
        <div class="col">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
            <h4 style="margin:0">Tareas</h4>
            <button class="btn btn-primary" id="openAddTaskModal"><i class="fas fa-plus"></i> Agregar tarea</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr><th>Nombre</th><th>Asignado</th><th>Estado</th><th>Vence</th></tr>
              </thead>
              <tbody>
                <?php if (empty($tasks)): ?>
                <tr><td colspan="4" class="empty">Sin tareas</td></tr>
                <?php else: foreach ($tasks as $t): ?>
                <tr>
                  <td><?php echo Utils::escape($t['task_name']); ?></td>
                  <td><?php echo Utils::escape($t['assigned_to_fullname'] ?: ($t['all_assigned_users'] ?? '-')); ?></td>
                  <td><span class="badge status-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
                  <td><?php echo $t['due_date'] ? date('d/m/Y', strtotime($t['due_date'])) : '-'; ?></td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col">
          <h4>Actividad Reciente</h4>
          <div class="activity-list">
            <?php if (empty($history)): ?>
              <div class="empty">Sin actividad reciente</div>
            <?php else: foreach ($history as $h): ?>
              <div class="activity-item">
                <div class="meta">
                  <span class="task"><?php echo Utils::escape($h['task_name']); ?></span>
                  <span class="time"><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></span>
                </div>
                <div class="desc">
                  <span class="user"><?php echo Utils::escape($h['full_name'] ?: $h['username'] ?: 'Sistema'); ?></span>
                  <span>→</span>
                  <strong><?php echo Utils::escape($h['action_type']); ?></strong>
                  <?php if ($h['field_name']): ?>
                    <span class="field">(<?php echo Utils::escape($h['field_name']); ?>)</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<style>
.project-overview {display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-xl);margin-bottom:var(--spacing-xl)}
.overview-card{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:var(--radius-xl);padding:var(--spacing-xl)}
.overview-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 18px;margin-bottom:14px}
.overview-grid .label{color:var(--text-muted)}
.overview-grid .value{color:var(--text-primary);font-weight:600}
.badge{padding:4px 8px;border-radius:8px;font-size:.8rem}
.status-open{background:var(--info);color:#fff}
.status-completed{background:var(--success);color:#fff}
.status-paused{background:var(--warning);color:#fff}
.status-cancelled{background:var(--error);color:#fff}
.progress-bar.large{height:10px;background:var(--bg-accent);border-radius:999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
.stats-row{display:flex;gap:18px}
.stat{background:var(--bg-tertiary);border:1px solid var(--bg-accent);border-radius:12px;padding:12px 16px;flex:1;text-align:center}
.stat .num{font-weight:800;color:var(--text-primary);font-size:1.1rem}
.stat .num.warn{color:#e53e3e}
.stat .cap{display:block;color:var(--text-muted);font-size:.85rem;margin-top:4px}
.two-cols{display:grid;grid-template-columns:2fr 1fr;gap:var(--spacing-xl)}
.table-wrapper{border:1px solid var(--bg-accent);border-radius:12px;overflow:hidden;background:var(--bg-primary)}
.data-table{width:100%;border-collapse:collapse}
.data-table th,.data-table td{padding:12px 14px;border-bottom:1px solid var(--bg-accent)}
.data-table th{background:var(--bg-tertiary);text-align:left}
.activity-list{display:flex;flex-direction:column;gap:12px}
.activity-item{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:12px;padding:12px}
.activity-item .meta{display:flex;justify-content:space-between;margin-bottom:6px;color:var(--text-muted)}
.activity-item .desc{display:flex;gap:8px;flex-wrap:wrap;color:var(--text-secondary)}
.activity-item .user{font-weight:600;color:var(--text-primary)}
.empty{color:var(--text-muted);text-align:center;padding:16px}
@media(max-width:900px){.project-overview{grid-template-columns:1fr}.two-cols{grid-template-columns:1fr}}
</style>

<!-- Modal Crear Tarea -->
<div id="addTaskModal" class="modal" style="display:none">
  <div class="modal-content" style="max-width:600px">
    <div class="modal-header">
      <h3><i class="fas fa-tasks"></i> Crear nueva tarea</h3>
      <button class="modal-close" id="closeAddTaskModal">&times;</button>
    </div>
    <div class="modal-body">
      <form id="addTaskForm">
        <input type="hidden" name="projectId" value="<?php echo (int)$project['project_id']; ?>">
        <div class="form-group">
          <label>Nombre de la tarea</label>
          <input type="text" name="taskName" class="input" required>
        </div>
        <div class="form-group">
          <label>Descripción</label>
          <textarea name="description" class="textarea" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Asignar a colaboradores del clan</label>
          <div class="checkbox-list" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <?php foreach (($clanMembers ?? []) as $m): ?>
              <label style="display:flex;align-items:center;gap:8px;border:1px solid var(--bg-accent);padding:8px;border-radius:10px;background:var(--bg-primary)">
                <input type="checkbox" name="assignedUsers[]" value="<?php echo (int)$m['user_id']; ?>">
                <span><?php echo Utils::escape($m['full_name'] ?: $m['username']); ?> (<?php echo Utils::escape($m['role_name'] ?? ''); ?>)</span>
              </label>
            <?php endforeach; ?>
            <?php if (empty($clanMembers)): ?>
              <div class="empty">No hay miembros en el clan</div>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" id="closeAddTaskModal2">Cancelar</button>
      <button class="btn btn-primary" id="submitAddTask">Crear</button>
    </div>
  </div>
  <div class="modal-backdrop" id="addTaskBackdrop"></div>
  <style>
    .modal{position:fixed;inset:0;z-index:1000}
    .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.35)}
    .modal-content{position:relative;margin:8vh auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid var(--bg-accent)}
    .modal-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--bg-tertiary);border-bottom:1px solid var(--bg-accent)}
    .modal-body{padding:16px}
    .modal-footer{display:flex;justify-content:flex-end;gap:10px;padding:12px 16px;background:var(--bg-tertiary);border-top:1px solid var(--bg-accent)}
    .modal-close{background:transparent;border:none;font-size:22px;cursor:pointer}
    .form-group{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
    .input,.textarea{border:1px solid #e5e7eb;border-radius:10px;padding:10px;width:100%}
  </style>
</div>

<script>
(function(){
  const openBtn = document.getElementById('openAddTaskModal');
  const modal = document.getElementById('addTaskModal');
  const closers = [document.getElementById('closeAddTaskModal'), document.getElementById('closeAddTaskModal2'), document.getElementById('addTaskBackdrop')];
  const submitBtn = document.getElementById('submitAddTask');
  function open(){ modal.style.display='block'; }
  function close(){ modal.style.display='none'; }
  if(openBtn) openBtn.addEventListener('click', open);
  closers.forEach(el=>{ if(el) el.addEventListener('click', close); });
  async function post(url, data){
    return fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data)}).then(r=>r.json());
  }
  if(submitBtn){
    submitBtn.addEventListener('click', async ()=>{
      const form = document.getElementById('addTaskForm');
      const data = new FormData(form);
      const obj = {};
      for (const [k,v] of data.entries()){
        if (obj[k]){
          if(!Array.isArray(obj[k])) obj[k] = [obj[k]];
          obj[k].push(v);
        } else {
          obj[k] = v;
        }
      }
      const res = await post('?route=admin/add-task', obj);
      if(res.success){ location.reload(); }
      else { alert(res.message || 'Error al crear tarea'); }
    });
  }
})();
</script>

<?php
$content = ob_get_clean();
$title = 'Detalle de Proyecto - ' . APP_NAME;
include __DIR__ . '/layout.php';
?>


