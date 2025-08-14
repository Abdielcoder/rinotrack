<?php
ob_start();
?>

<div class="cm-task-details minimal">
  <div class="project-hero" style="max-width:1100px;">
    <div class="hero-inner">
      <a href="?route=clan_member/project-tasks&project_id=<?php echo (int)$project['project_id']; ?>" class="hero-back"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
        <div>
          <h1 class="hero-title" style="margin-bottom:4px;"><?php echo htmlspecialchars($task['task_name']); ?></h1>
          <div class="hero-subtitle">Proyecto: <?php echo htmlspecialchars($project['project_name']); ?></div>
        </div>
        <span class="status-badge <?php echo htmlspecialchars($task['status']); ?>"><?php echo strtoupper(str_replace('_',' ', (string)$task['status'])); ?></span>
      </div>
    </div>
  </div>

  <div class="content-minimal" style="max-width:1100px;">
    <div class="task-details-grid">
      <div class="left-pane">
        <div class="summary-card">
          <div class="meta-row">
            <?php if (!empty($task['due_date'])): ?>
              <div class="meta"><i class="fas fa-calendar"></i> Fecha límite: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></div>
            <?php endif; ?>
            <?php if (!empty($task['created_by_fullname'])): ?>
              <div class="meta"><i class="fas fa-user"></i> Creado por: <?php echo htmlspecialchars($task['created_by_fullname']); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="summary-card">
          <h3>Descripción</h3>
          <div><?php echo nl2br(htmlspecialchars($task['description'] ?? '')); ?></div>
        </div>

        <div class="summary-card">
          <h3>Comentarios (<?php echo count($comments); ?>)</h3>
          <?php if ($canEdit): ?>
          <form id="tdCommentForm" class="comment-composer" enctype="multipart/form-data">
            <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>" />
            <textarea name="comment_text" placeholder="Escribe un comentario..."></textarea>
            <div class="form-group inline">
              <input type="file" name="attachments[]" multiple />
              <button class="action-btn primary" type="submit"><i class="fas fa-paper-plane"></i> Enviar</button>
            </div>
          </form>
          <?php endif; ?>
          <div class="comments-list" style="margin-top:10px;">
            <?php if (empty($comments)): ?>
              <div class="empty-minimal">Sin comentarios</div>
            <?php else: foreach ($comments as $c): ?>
              <div class="comment-item">
                <div class="comment-meta"><span class="author"><?php echo htmlspecialchars($c['full_name'] ?? $c['username'] ?? ''); ?></span><span class="date"><?php echo htmlspecialchars($c['created_at'] ?? ''); ?></span></div>
                <div class="comment-text"><?php echo nl2br(htmlspecialchars($c['comment_text'] ?? '')); ?></div>
                <?php if (!empty($c['attachments'])): ?>
                <div class="comment-atts">
                  <?php foreach (($c['attachments'] ?? []) as $a): $url = Utils::asset($a['file_path'] ?? ''); $name = htmlspecialchars($a['file_name'] ?? 'archivo'); $type = strtolower($a['file_type'] ?? ''); ?>
                    <?php if (strpos($type,'image')===0 || in_array($type, ['png','jpg','jpeg','gif','webp','bmp','svg'])): ?>
                      <a class="att att-img" href="<?php echo $url; ?>" target="_blank" rel="noopener"><img src="<?php echo $url; ?>" alt="<?php echo $name; ?>"/></a>
                    <?php elseif ($type==='pdf' || $type==='application/pdf'): ?>
                      <a class="att att-file" href="<?php echo $url; ?>" target="_blank" rel="noopener"><i class="fas fa-file-pdf"></i> <?php echo $name; ?></a>
                    <?php else: ?>
                      <a class="att att-file" href="<?php echo $url; ?>" target="_blank" rel="noopener"><i class="fas fa-paperclip"></i> <?php echo $name; ?></a>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <div class="footer-actions" style="display:flex; gap:10px; margin-top:12px;">
          <a href="?route=clan_member/tasks" class="btn-minimal"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
          <?php if ($canEdit): ?>
            <button class="btn-minimal primary" onclick="openEditTaskModal()"><i class="fas fa-edit"></i> Editar Tarea</button>
          <?php else: ?>
            <button class="btn-minimal" onclick="noPermissionModal()"><i class="fas fa-edit"></i> Editar Tarea</button>
          <?php endif; ?>
        </div>
      </div>

      <aside class="right-pane">
        <div class="summary-card">
          <h3>Colaboradores (<?php echo count($assignedUsers); ?>)</h3>
          <div class="assignees-list">
            <?php foreach ($assignedUsers as $au): ?>
              <div class="assignee-item">
                <span class="avatar-initial"><?php echo strtoupper(substr($au['full_name'] ?? $au['username'] ?? '', 0, 1)); ?></span>
                <div class="assignee-info">
                  <div class="name"><?php echo htmlspecialchars($au['full_name'] ?? $au['username'] ?? ''); ?></div>
                  <div class="meta"><?php echo htmlspecialchars($au['username'] ?? ''); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($assignedUsers)): ?><div class="empty-minimal">Sin colaboradores</div><?php endif; ?>
          </div>
        </div>

        <div class="summary-card">
          <h3>Historial</h3>
          <div class="history-list">
            <?php if (empty($history)): ?>
              <div class="empty-minimal">Sin historial</div>
            <?php else: foreach ($history as $h): ?>
              <div class="history-item">
                <div class="title"><?php echo htmlspecialchars(ucfirst($h['action_type'])); ?></div>
                <div class="meta">Por: <?php echo htmlspecialchars($h['full_name'] ?? $h['username'] ?? ''); ?> — <?php echo htmlspecialchars($h['created_at'] ?? ''); ?></div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <div class="summary-card">
          <h3>Información</h3>
          <div class="info-grid">
            <div><strong>Creado:</strong> <?php echo htmlspecialchars($task['created_at'] ?? ''); ?></div>
            <div><strong>Actualizado:</strong> <?php echo htmlspecialchars($task['updated_at'] ?? ''); ?></div>
            <div><strong>Progreso:</strong> <?php echo (int)($task['completion_percentage'] ?? 0); ?>%</div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>

<script>
document.getElementById('tdCommentForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_member/add-task-comment', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ alert(d.message||'Error'); return; } location.reload(); });
});

function noPermissionModal(){
  if (window.confirmInfo) {
    window.confirmInfo('No tienes permisos para modificar esta tarea.', function(){}, function(){});
  } else {
    alert('No tienes permisos para modificar esta tarea.');
  }
}
</script>

<!-- Modal editar tarea -->
<?php if ($canEdit): ?>
<div class="modal" id="editTaskModal">
  <div class="modal-content modal-lg">
    <div class="modal-header modal-header-gradient">
      <div class="modal-title-wrap">
        <h3>Editar Tarea</h3>
        <span class="modal-subtitle">Actualiza los campos permitidos</span>
      </div>
      <button class="modal-close" onclick="closeEditTaskModal()">&times;</button>
    </div>
    <form id="editTaskForm" class="modal-body create-task-body">
      <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>" />
      <div class="form-grid">
        <div class="form-group form-span-2">
          <label>Título</label>
          <input type="text" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required />
        </div>
        <div class="form-group form-span-2">
          <label>Descripción</label>
          <textarea name="description" rows="4"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
          <label>Prioridad</label>
          <select name="priority">
            <option value="low" <?php echo ($task['priority']==='low')?'selected':''; ?>>Baja</option>
            <option value="medium" <?php echo ($task['priority']==='medium')?'selected':''; ?>>Media</option>
            <option value="high" <?php echo ($task['priority']==='high')?'selected':''; ?>>Alta</option>
            <option value="urgent" <?php echo ($task['priority']==='urgent')?'selected':''; ?>>Urgente</option>
          </select>
        </div>
        <div class="form-group">
          <label>Fecha límite</label>
          <input type="date" name="due_date" value="<?php echo !empty($task['due_date']) ? date('Y-m-d', strtotime($task['due_date'])) : ''; ?>" />
        </div>
        <div class="form-group">
          <label>Estado</label>
          <select name="status">
            <option value="pending" <?php echo ($task['status']==='pending')?'selected':''; ?>>Pendiente</option>
            <option value="in_progress" <?php echo ($task['status']==='in_progress')?'selected':''; ?>>En progreso</option>
            <option value="completed" <?php echo ($task['status']==='completed')?'selected':''; ?>>Completada</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button class="action-btn secondary" type="button" onclick="closeEditTaskModal()">Cancelar</button>
        <button id="editTaskSubmitBtn" class="action-btn primary" type="submit"><span class="btn-text">Guardar</span><span class="btn-loader" aria-hidden="true"></span></button>
      </div>
      <div id="editTaskErrors" class="form-errors" style="display:none;"></div>
    </form>
  </div>
  </div>
<?php endif; ?>

<script>
function openEditTaskModal(){ document.getElementById('editTaskModal').classList.add('open'); }
function closeEditTaskModal(){ document.getElementById('editTaskModal').classList.remove('open'); }

document.getElementById('editTaskForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const errorBox = document.getElementById('editTaskErrors');
  const btn = document.getElementById('editTaskSubmitBtn');
  errorBox.style.display = 'none';
  btn.classList.add('is-loading');
  const fd = new FormData(this);
  fetch('?route=clan_member/update-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ errorBox.style.display='block'; errorBox.textContent=d.message||'No se pudo guardar'; btn.classList.remove('is-loading'); return; } location.reload(); })
    .catch(()=>{ errorBox.style.display='block'; errorBox.textContent='Error de red'; btn.classList.remove('is-loading'); });
});
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


