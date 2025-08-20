<?php
ob_start();
?>

<div class="cm-task-details minimal">
  <div class="content-minimal" style="max-width:1100px;">
    <div class="task-details-grid">
      <div class="left-pane">
        <div class="summary-card project-info-card">
          <div class="project-info">
            <div class="project-icon"><i class="fas fa-folder-open"></i></div>
            <div class="project-text">
              <div class="project-title"><?php echo htmlspecialchars($task['task_name']); ?></div>
              <div class="project-subtitle">Proyecto: <?php echo htmlspecialchars($project['project_name']); ?></div>
            </div>
          </div>
          <div class="project-status">
            <span class="status-badge <?php echo htmlspecialchars($task['status']); ?>"><?php echo strtoupper(str_replace('_',' ', (string)$task['status'])); ?></span>
          </div>
        </div>
        
        <div class="summary-card motivational-card">
          <div class="motivation">
            <div class="motivation-icon"><i class="fas fa-lightbulb"></i></div>
            <div class="motivation-text">
              <div id="motQuote" class="mot-quote">Cargando frase motivacional...</div>
              <div id="motAuthor" class="mot-author"></div>
            </div>
          </div>
        </div>
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

        <!-- Sección de Subtareas -->
        <?php if (!empty($subtasks)): ?>
        <div class="summary-card">
          <h3><i class="fas fa-tasks"></i> Subtareas (<?php echo count($subtasks); ?>)</h3>
          <div class="subtasks-list">
            <?php foreach ($subtasks as $subtask): ?>
            <div class="subtask-item" data-subtask-id="<?php echo $subtask['subtask_id']; ?>">
              <div class="subtask-info">
                <div class="subtask-header">
                  <div class="subtask-title"><?php echo htmlspecialchars($subtask['title']); ?></div>
                  <?php if ($canEdit): ?>
                  <div class="subtask-actions">
                    <button class="btn-icon-small" onclick="editSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                  </div>
                  <?php endif; ?>
                </div>
                <div class="subtask-meta">
                  <span>Estado: <?php echo ucfirst($subtask['status']); ?></span>
                  <?php if (!empty($subtask['assigned_user_name'])): ?>
                  <span>Asignado: <?php echo htmlspecialchars($subtask['assigned_user_name']); ?></span>
                  <?php endif; ?>
                  <?php if ($subtask['due_date']): ?>
                  <span>Vence: <?php echo Utils::formatDate($subtask['due_date']); ?></span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($subtask['description'])): ?>
                <div class="subtask-description">
                  <?php echo htmlspecialchars($subtask['description']); ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="subtask-controls">
                <div class="subtask-progress">
                  <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $subtask['completion_percentage']; ?>%"></div>
                  </div>
                  <span class="progress-percentage">
                    <?php echo $subtask['completion_percentage']; ?>%
                  </span>
                </div>
                <?php if ($canEdit): ?>
                <div class="subtask-status-controls">
                  <select class="status-select" onchange="updateSubtaskStatus(<?php echo $subtask['subtask_id']; ?>, this.value)">
                    <option value="pending" <?php echo $subtask['status'] === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="in_progress" <?php echo $subtask['status'] === 'in_progress' ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="completed" <?php echo $subtask['status'] === 'completed' ? 'selected' : ''; ?>>Completada</option>
                  </select>
                  <span class="status-display">
                    Estado: <?php echo ucfirst(str_replace('_', ' ', $subtask['status'])); ?>
                  </span>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

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
          <a href="?route=clan_member" class="btn-minimal"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
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

<style>
.motivational-card{background:linear-gradient(90deg, rgba(99,102,241,.12), rgba(59,130,246,.10)); border:1px solid var(--bg-accent)}
.motivation{display:flex; align-items:center; gap:12px}
.motivation-icon{width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--primary-gradient); color:#fff}
.mot-quote{font-weight:600; color:var(--text-primary)}
.mot-author{font-size:.9rem; color:var(--text-secondary); margin-top:2px}

/* Estilos específicos para los botones */
.action-btn.primary {
  background: #1e3a8a !important;
  color: #ffffff !important;
  border-color: #1e3a8a !important;
  font-weight: 700;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid #1e3a8a;
  cursor: pointer;
  transition: all 0.15s ease;
}

.action-btn.primary:hover {
  background: #1e40af !important;
  border-color: #1e40af !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(30, 58, 138, 0.22);
}

.btn-minimal.primary {
  background: #1e3a8a !important;
  color: #ffffff !important;
  border-color: #1e3a8a !important;
  font-weight: 600;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #1e3a8a;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-minimal.primary:hover {
  background: #1e40af !important;
  border-color: #1e40af !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(30, 58, 138, 0.22);
}

/* Estilos para la tarjeta del proyecto */
.project-info-card {
  background: linear-gradient(90deg, rgba(99,102,241,.12), rgba(59,130,246,.10)) !important;
  border: 1px solid #e5e7eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 12px;
}

.project-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.project-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1e3a8a;
  color: #fff;
  font-size: 1.2rem;
}

.project-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.project-title {
  font-weight: 700;
  color: #1e3a8a;
  font-size: 1.1rem;
}

.project-subtitle {
  font-size: 0.9rem;
  color: #6b7280;
}

.project-status {
  margin-left: auto;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.in_progress {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.completed {
  background: #d1fae5;
  color: #065f46;
}

/* Estilos para subtareas */
.subtasks-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 15px;
}

.subtask-item {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 15px;
}

.subtask-info {
  flex: 1;
}

.subtask-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.subtask-title {
  font-weight: 600;
  color: #1f2937;
  font-size: 14px;
}

.subtask-actions {
  display: flex;
  gap: 5px;
}

.btn-icon-small {
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 4px;
  background: #f3f4f6;
  color: #6b7280;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.2s ease;
}

.btn-icon-small:hover {
  background: #e5e7eb;
  color: #374151;
}

.subtask-meta {
  display: flex;
  gap: 15px;
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 8px;
}

.subtask-description {
  margin-top: 8px;
  font-size: 13px;
  color: #6b7280;
}

.subtask-controls {
  display: flex;
  flex-direction: column;
  gap: 10px;
  min-width: 150px;
}

.subtask-progress {
  display: flex;
  align-items: center;
  gap: 10px;
}

.progress-bar {
  width: 80px;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #10b981;
  transition: width 0.3s ease;
}

.progress-percentage {
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  min-width: 35px;
}

.subtask-status-controls {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.status-select {
  padding: 4px 8px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 12px;
  background: #fff;
}

.status-display {
  font-size: 11px;
  color: #6b7280;
}
</style>

<script>
// Frase motivacional por sesión
(function(){
  const qEl = document.getElementById('motQuote');
  const aEl = document.getElementById('motAuthor');
  if (!qEl || !aEl) return;

  const SS_KEY = 'rt_motivational_quote';
  const cached = sessionStorage.getItem(SS_KEY);
  if (cached) {
    try { const obj = JSON.parse(cached); qEl.textContent = '“'+(obj.text||'')+'”'; aEl.textContent = obj.author? ('— '+obj.author) : ''; return; } catch(_){}
  }

  const apis = [
    { url: 'https://api.quotable.io/random', map: d => ({ text: d.content, author: d.author }) },
    { url: 'https://zenquotes.io/api/random', map: d => { const x=(Array.isArray(d)?d[0]:{})||{}; return { text: x.q, author: x.a }; } },
    { url: 'https://type.fit/api/quotes', map: d => { const arr = Array.isArray(d)? d : []; const r = arr[Math.floor(Math.random()*arr.length)]||{}; return { text: r.text, author: r.author||'Anónimo' }; } }
  ];

  const localFallback = [
    { text:'La excelencia no es un acto, es un hábito.', author:'Aristóteles' },
    { text:'La disciplina es el puente entre metas y logros.', author:'Jim Rohn' },
    { text:'Haz hoy lo que otros no harán y mañana vivirás como otros no pueden.', author:'Jerry Rice' }
  ];

  function applyQuote(q){
    if (!q || !q.text) q = localFallback[Math.floor(Math.random()*localFallback.length)];
    qEl.textContent = '“'+(q.text||'')+'”';
    aEl.textContent = q.author ? ('— '+q.author) : '';
    try { sessionStorage.setItem(SS_KEY, JSON.stringify(q)); } catch(_){ }
  }

  // Intentar en cadena con fallback
  (async function(){
    for (const api of apis){
      try {
        const res = await fetch(api.url, { credentials:'omit' });
        if (!res.ok) continue;
        const data = await res.json();
        const q = api.map(data) || {};
        if (q && q.text) { applyQuote(q); return; }
      } catch(_){ /* siguiente */ }
    }
    applyQuote(null);
  })();
})();

// Funciones para subtareas
function updateSubtaskStatus(subtaskId, status) {
  // Determinar porcentaje automático basado en el estado
  let completion_percentage = 0;
  if (status === 'in_progress') {
    completion_percentage = 50;
  } else if (status === 'completed') {
    completion_percentage = 100;
  } else if (status === 'pending') {
    completion_percentage = 0;
  }
  
  fetch('?route=clan_member/update-subtask-status', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `subtask_id=${subtaskId}&status=${status}&completion_percentage=${completion_percentage}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Actualizar la UI
      const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
      if (subtaskItem) {
        // Actualizar el estado en la meta
        const statusSpan = subtaskItem.querySelector('.subtask-meta span:first-child');
        if (statusSpan) {
          statusSpan.textContent = `Estado: ${status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')}`;
        }
        
        // Actualizar la barra de progreso
        const progressFill = subtaskItem.querySelector('.progress-fill');
        const percentageSpan = subtaskItem.querySelector('.progress-percentage');
        if (progressFill) {
          progressFill.style.width = completion_percentage + '%';
        }
        if (percentageSpan) {
          percentageSpan.textContent = completion_percentage + '%';
        }
        
        // Actualizar el estado en los controles
        const statusDisplaySpan = subtaskItem.querySelector('.status-display');
        if (statusDisplaySpan) {
          statusDisplaySpan.textContent = `Estado: ${status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')}`;
        }
      }
      
      showNotification('Estado de subtarea actualizado', 'success');
    } else {
      showNotification('Error al actualizar estado: ' + data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error al actualizar estado', 'error');
  });
}

function editSubtask(subtaskId) {
  // Función básica de edición de subtarea
  alert('Función de edición de subtarea en desarrollo');
}

// Función simple de notificación para el clan member
function showNotification(message, type = 'info') {
  // Crear notificación si no existe
  let notification = document.getElementById('cm-notification');
  if (!notification) {
    const notificationHTML = `
      <div id="cm-notification" style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        display: none;
        transition: all 0.3s ease;
      ">
        <span id="cm-notificationMessage"></span>
        <button onclick="closeCMNotification()" style="
          background: none;
          border: none;
          color: white;
          margin-left: 10px;
          cursor: pointer;
          font-size: 16px;
        ">&times;</button>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', notificationHTML);
    notification = document.getElementById('cm-notification');
  }
  
  const messageElement = document.getElementById('cm-notificationMessage');
  messageElement.textContent = message;
  
  // Aplicar colores según el tipo
  if (type === 'success') {
    notification.style.backgroundColor = '#10b981';
  } else if (type === 'error') {
    notification.style.backgroundColor = '#ef4444';
  } else {
    notification.style.backgroundColor = '#3b82f6';
  }
  
  notification.style.display = 'block';
  
  // Auto-ocultar después de 3 segundos
  setTimeout(() => {
    notification.style.display = 'none';
  }, 3000);
}

function closeCMNotification() {
  const notification = document.getElementById('cm-notification');
  if (notification) {
    notification.style.display = 'none';
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
function openEditTaskModal(){ document.getElementById('editTaskModal')?.classList.add('open'); }
function closeEditTaskModal(){ document.getElementById('editTaskModal')?.classList.remove('open'); }

document.getElementById('editTaskForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const errorBox = document.getElementById('editTaskErrors');
  const btn = document.getElementById('editTaskSubmitBtn');
  errorBox.style.display = 'none';
  btn.classList.add('is-loading');
  const fd = new FormData(this);
  fetch('?route=clan_member/update-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{
      if(!d.success){
        errorBox.style.display='block'; errorBox.textContent=d.message||'No se pudo guardar'; btn.classList.remove('is-loading'); return;
      }
      // Siempre regresar al listado después de guardar
      window.location.href='?route=clan_member/tasks';
    })
    .catch(()=>{ errorBox.style.display='block'; errorBox.textContent='Error de red'; btn.classList.remove('is-loading'); });
});

// Ya no auto-abrimos el panel al cargar, solo al presionar "Editar Tarea"
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


