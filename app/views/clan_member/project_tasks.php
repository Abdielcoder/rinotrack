<?php
ob_start();
?>

<div class="cm-project-tasks minimal">
    <div class="project-hero">
        <div class="hero-inner">
            <a href="?route=clan_member/tasks" class="hero-back"><i class="fas fa-arrow-left"></i> Volver a Proyectos</a>
            <h1 class="hero-title">Tareas del Proyecto</h1>
            <div class="hero-subtitle"><?php echo htmlspecialchars($project['project_name']); ?></div>
            <div class="hero-actions">
                <button class="btn-hero" onclick="openCreateTaskModal()"><i class="fas fa-plus"></i> Nueva Tarea</button>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="num"><?php echo count($tasks ?? []); ?></div>
                    <div class="label">Total Tareas</div>
                </div>
                <div class="hero-stat">
                    <div class="num"><?php echo count(array_filter($tasks ?? [], function($t){ return ($t['status'] ?? '')==='completed'; })); ?></div>
                    <div class="label">Completadas</div>
                </div>
                <div class="hero-stat">
                    <div class="num"><?php echo count(array_filter($tasks ?? [], function($t){ return ($t['status'] ?? '')==='pending'; })); ?></div>
                    <div class="label">Pendientes</div>
                </div>
                <?php if (!empty($project['kpi_points'])): ?>
                <div class="hero-stat">
                    <div class="num"><?php echo number_format((float)$project['kpi_points']); ?></div>
                    <div class="label">Puntos KPI</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="content-minimal">

        <?php if (!empty($project['description'])): ?>
        <section class="summary-card">
            <h3>Descripción del Proyecto</h3>
            <div><?php echo htmlspecialchars($project['description']); ?></div>
        </section>
        <?php endif; ?>

        <section class="filters-minimal" style="margin-top:8px;">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div>
                    <label style="display:block;font-weight:700;color:#6b7280;font-size:.9rem">Estado:</label>
                    <select id="statusFilter" onchange="filterCards()">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-weight:700;color:#6b7280;font-size:.9rem">Prioridad:</label>
                    <select id="priorityFilter" onchange="filterCards()">
                        <option value="">Todas las prioridades</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="tasks-list" style="grid-template-columns:1fr;">
            <?php if (empty($tasks)): ?>
                <div class="empty-minimal">No hay tareas en este proyecto</div>
            <?php else: ?>
                <?php foreach ($tasks as $t): ?>
                <div class="task-item task-card <?php echo htmlspecialchars($t['priority']); ?>" data-status="<?php echo htmlspecialchars($t['status']); ?>" data-priority="<?php echo htmlspecialchars($t['priority']); ?>">
                    <div class="task-main">
                        <div class="task-title">
                            <?php 
                                $userId = (int)($user['user_id'] ?? 0);
                                $assignedIds = array_filter(explode(',', (string)($t['all_assigned_user_ids'] ?? '')));
                                $canEditTask = in_array((string)$userId, $assignedIds, true) || (int)($t['assigned_to_user_id'] ?? 0) === $userId || (int)($t['created_by_user_id'] ?? 0) === $userId;
                            ?>
                            <span class="task-check"><input type="checkbox" <?php echo (($t['status'] ?? '')==='completed')?'checked':''; ?> <?php echo $canEditTask? '' : 'disabled'; ?> onchange="toggleCardStatus(<?php echo (int)$t['task_id']; ?>, this.checked, <?php echo $canEditTask? 'true':'false'; ?>)" /></span>
                            <div class="title-text"><?php echo htmlspecialchars($t['task_name']); ?></div>
                        </div>
                        <div class="task-meta">
                            <?php if (!empty($t['description'])): ?><div class="desc"><?php echo htmlspecialchars($t['description']); ?></div><?php endif; ?>
                            <div class="meta-row">
                                <?php if (!empty($t['assigned_to_fullname'])): ?>
                                    <span class="meta"><i class="fas fa-user"></i> <?php echo htmlspecialchars($t['assigned_to_fullname']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($t['due_date'])): ?>
                                    <span class="meta"><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($t['due_date'])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="task-actions">
                        <?php if ($canEditTask): ?>
                            <select class="status-select" onchange="setCardTaskStatus(<?php echo (int)$t['task_id']; ?>, this.value, true)">
                                <option value="pending" <?php echo ($t['status']==='pending')?'selected':''; ?>>Pendiente</option>
                                <option value="in_progress" <?php echo ($t['status']==='in_progress')?'selected':''; ?>>En progreso</option>
                                <option value="completed" <?php echo ($t['status']==='completed')?'selected':''; ?>>Completada</option>
                            </select>
                        <?php else: ?>
                            <span class="status-badge <?php echo htmlspecialchars($t['status']); ?>"><?php echo strtoupper(str_replace('_',' ', (string)$t['status'])); ?></span>
                        <?php endif; ?>
                        <div class="actions-right">
                            <a class="btn-chip info" href="?route=clan_member/task-details&task_id=<?php echo (int)$t['task_id']; ?>"><i class="fas fa-eye"></i> Ver</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- Modal crear tarea -->
<div class="modal create-task-modal" id="createTaskModal">
  <div class="modal-content modal-lg">
    <div class="modal-header modal-header-gradient">
      <div class="modal-title-wrap">
        <h3>Nueva Tarea</h3>
        <span class="modal-subtitle">Agrega una tarea al proyecto y define su prioridad y fecha</span>
      </div>
      <button class="modal-close" onclick="closeCreateTaskModal()">&times;</button>
    </div>
    <form id="createTaskForm" class="modal-body create-task-body">
      <input type="hidden" name="project_id" value="<?php echo (int)$project['project_id']; ?>" />
      <div class="form-grid">
        <div class="form-group form-span-2">
          <label>Título</label>
          <input type="text" name="task_name" placeholder="Escribe un título claro y conciso" required />
          <small class="field-help">Ejemplo: Implementar autenticación con tokens</small>
        </div>
        <div class="form-group form-span-2">
          <label>Descripción</label>
          <textarea name="description" rows="4" placeholder="Contexto, entregables y consideraciones"></textarea>
        </div>
        <div class="form-group">
          <label>Prioridad</label>
          <select name="priority">
            <option value="low">Baja</option>
            <option value="medium" selected>Media</option>
            <option value="high">Alta</option>
            <option value="urgent">Urgente</option>
          </select>
          <small class="field-help">Afecta la visibilidad en las listas</small>
        </div>
        <div class="form-group">
          <label>Fecha límite</label>
          <input type="date" name="due_date" />
          <small class="field-help">Opcional</small>
        </div>
      </div>
      <div class="form-actions">
        <button class="action-btn secondary" type="button" onclick="closeCreateTaskModal()">Cancelar</button>
        <button id="createTaskSubmitBtn" class="action-btn primary" type="submit">
          <span class="btn-text">Crear</span>
          <span class="btn-loader" aria-hidden="true"></span>
        </button>
      </div>
      <div id="createTaskErrors" class="form-errors" style="display:none;"></div>
    </form>
  </div>
</div>

<script>
function openCreateTaskModal(){ document.getElementById('createTaskModal').classList.add('open'); }
function closeCreateTaskModal(){ document.getElementById('createTaskModal').classList.remove('open'); document.getElementById('createTaskForm').reset(); }

document.getElementById('createTaskForm').addEventListener('submit', function(e){
  e.preventDefault();
  const title = this.querySelector('input[name="task_name"]').value.trim();
  const errorBox = document.getElementById('createTaskErrors');
  if(!title){
    errorBox.style.display = 'block';
    errorBox.textContent = 'El título es requerido.';
    return;
  }
  errorBox.style.display = 'none';
  const submitBtn = document.getElementById('createTaskSubmitBtn');
  submitBtn.classList.add('is-loading');
  const fd = new FormData(this);
  fetch('?route=clan_member/create-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ errorBox.style.display='block'; errorBox.textContent=d.message||'Error al crear la tarea'; submitBtn.classList.remove('is-loading'); return; } location.reload(); })
    .catch(()=>{ errorBox.style.display='block'; errorBox.textContent='Error de red'; submitBtn.classList.remove('is-loading'); });
});

// Auto abrir modal si viene desde dashboard con open_create=1
(function(){
  try {
    var params = new URLSearchParams(window.location.search);
    if (params.get('open_create') === '1') { openCreateTaskModal(); }
  } catch(e){}
})();

function filterCards(){
  var st = document.getElementById('statusFilter').value;
  var pr = document.getElementById('priorityFilter').value;
  document.querySelectorAll('.task-card').forEach(function(el){
    var ok = (!st || el.getAttribute('data-status')===st) && (!pr || el.getAttribute('data-priority')===pr);
    el.style.display = ok ? '' : 'none';
  });
}

function toggleCardStatus(taskId, isChecked, allowed){
  if(!allowed){ noPermissionModal && noPermissionModal(); return; }
  const fd = new FormData(); fd.append('task_id', taskId); fd.append('is_completed', isChecked ? 'true' : 'false');
  fetch('?route=clan_member/toggle-task-status', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false, message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ alert(d.message||'Error'); location.reload(); } else { location.reload(); } });
}

function setCardTaskStatus(taskId, newStatus, allowed){
  if(!allowed){ noPermissionModal && noPermissionModal(); return; }
  if(newStatus==='completed' || newStatus==='pending'){
    toggleCardStatus(taskId, newStatus==='completed', allowed);
    return;
  }
  const fd = new FormData(); fd.append('task_id', taskId); fd.append('status', 'in_progress');
  fetch('?route=clan_member/update-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false, message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ alert(d.message||'Error'); } location.reload(); });
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


