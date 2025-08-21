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
                  <div class="subtask-actions">
                    <button class="btn-icon-small btn-with-badge" id="comments-btn-<?php echo $subtask['subtask_id']; ?>" onclick="showSubtaskComments(<?php echo $subtask['subtask_id']; ?>)" title="Ver comentarios">
                      <i class="fas fa-comments"></i>
                      <span class="badge" id="comments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                    </button>
                    <button class="btn-icon-small btn-with-badge" id="attachments-btn-<?php echo $subtask['subtask_id']; ?>" onclick="showSubtaskAttachments(<?php echo $subtask['subtask_id']; ?>)" title="Ver adjuntos">
                      <i class="fas fa-paperclip"></i>
                      <span class="badge" id="attachments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                    </button>
                    <?php if ($canEdit): ?>
                    <button class="btn-icon-small" onclick="editSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <?php endif; ?>
                  </div>
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
    // Obtener datos actuales de la subtarea
    const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
    const currentTitle = subtaskElement.querySelector('.subtask-title').textContent.trim();
    const currentDescription = subtaskElement.querySelector('.subtask-description')?.textContent.trim() || '';
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Editar Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="edit-form">
                    <div class="form-group">
                        <label for="edit-subtask-title">Título:</label>
                        <input type="text" id="edit-subtask-title" value="${currentTitle}" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 10px;">
                    </div>
                    <div class="form-group">
                        <label for="edit-subtask-description">Descripción:</label>
                        <textarea id="edit-subtask-description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 15px;">${currentDescription}</textarea>
                    </div>
                    <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="this.closest('.modal-overlay').remove()" class="btn btn-secondary">Cancelar</button>
                        <button type="button" onclick="saveSubtaskChanges(${subtaskId})" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;
    
    document.body.appendChild(modal);
    document.getElementById('edit-subtask-title').focus();
}

function saveSubtaskChanges(subtaskId) {
    const title = document.getElementById('edit-subtask-title').value.trim();
    const description = document.getElementById('edit-subtask-description').value.trim();
    
    if (!title) {
        alert('El título es requerido');
        return;
    }
    
    fetch('?route=clan_member/edit-subtask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subtask_id: subtaskId,
            title: title,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar en la página sin recargar
            const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            subtaskElement.querySelector('.subtask-title').textContent = title;
            
            let descElement = subtaskElement.querySelector('.subtask-description');
            if (description) {
                if (!descElement) {
                    descElement = document.createElement('div');
                    descElement.className = 'subtask-description';
                    descElement.style.cssText = 'margin-top: 8px; font-size: 13px; color: #6b7280;';
                    subtaskElement.querySelector('.subtask-info').appendChild(descElement);
                }
                descElement.textContent = description;
            } else if (descElement) {
                descElement.remove();
            }
            
            document.querySelector('.modal-overlay').remove();
            showNotification('Subtarea actualizada exitosamente', 'success');
        } else {
            alert('Error al actualizar subtarea: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
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

// Funciones para comentarios y adjuntos de subtareas
function showSubtaskComments(subtaskId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3><i class="fas fa-comments"></i> Comentarios de Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="subtask-comments-list">
                    <div class="loading">Cargando comentarios...</div>
                </div>
                <div class="add-comment-section">
                    <h4>Agregar Comentario</h4>
                    <div class="comment-form">
                        <textarea id="subtask-comment-text" placeholder="Escribe tu comentario..." rows="3"></textarea>
                        <div class="comment-actions">
                            <input type="file" id="subtask-comment-file" style="display: none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                            <button type="button" onclick="document.getElementById('subtask-comment-file').click()" class="btn btn-secondary">
                                <i class="fas fa-paperclip"></i> Adjuntar
                            </button>
                            <button type="button" onclick="addSubtaskComment(${subtaskId})" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;
    
    document.body.appendChild(modal);
    loadSubtaskComments(subtaskId);
}

function showSubtaskAttachments(subtaskId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3><i class="fas fa-paperclip"></i> Archivos Adjuntos de Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="subtask-attachments-list">
                    <div class="loading">Cargando adjuntos...</div>
                </div>
                <div class="add-attachment-section">
                    <h4>Subir Archivo</h4>
                    <div class="attachment-form">
                        <input type="file" id="subtask-attachment-file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                        <textarea id="subtask-attachment-description" placeholder="Descripción del archivo (opcional)" rows="2"></textarea>
                        <button type="button" onclick="uploadSubtaskAttachment(${subtaskId})" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Subir Archivo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;
    
    document.body.appendChild(modal);
    loadSubtaskAttachments(subtaskId);
}

function loadSubtaskComments(subtaskId) {
    fetch('?route=clan_member/get-subtask-comments&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('subtask-comments-list');
            if (data.success) {
                if (data.comments.length === 0) {
                    container.innerHTML = '<p class="no-data">No hay comentarios aún. ¡Sé el primero en comentar!</p>';
                } else {
                    container.innerHTML = data.comments.map(comment => `
                        <div class="comment-item">
                            ${comment.is_attachment_only ? `
                                <div class="comment-header">
                                    <strong><i class="fas fa-paperclip"></i> ${comment.full_name}</strong>
                                </div>
                            ` : `
                                <div class="comment-header">
                                    <strong>${comment.full_name}</strong>
                                    <span class="comment-date">${new Date(comment.created_at).toLocaleString()}</span>
                                </div>
                                <div class="comment-text">${comment.comment_text}</div>
                            `}
                            ${comment.attachments && comment.attachments.length > 0 ? `
                                <div class="comment-attachments">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #6b7280; font-size: 12px;">
                                        📎 Archivos adjuntos (${comment.attachments.length}):
                                    </div>
                                    ${comment.attachments.map(att => `
                                        <a href="${att.file_path}" target="_blank" class="attachment-link">
                                            <i class="fas fa-file"></i> ${att.file_name}
                                            ${att.uploaded_at ? `<span class="attachment-date">(${new Date(att.uploaded_at).toLocaleDateString()})</span>` : ''}
                                        </a>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    `).join('');
                }
            } else {
                container.innerHTML = '<p class="error">Error al cargar comentarios: ' + data.message + '</p>';
            }
        })
        .catch(error => {
            document.getElementById('subtask-comments-list').innerHTML = '<p class="error">Error de conexión</p>';
        });
}

function loadSubtaskAttachments(subtaskId) {
    fetch('?route=clan_member/get-subtask-attachments&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('subtask-attachments-list');
            if (data.success) {
                if (data.attachments.length === 0) {
                    container.innerHTML = '<p class="no-data">No hay archivos adjuntos aún.</p>';
                } else {
                    container.innerHTML = data.attachments.map(att => `
                        <div class="attachment-item">
                            <div class="attachment-info">
                                <a href="${att.file_path}" target="_blank" class="attachment-link">
                                    <i class="fas fa-file"></i> ${att.file_name}
                                </a>
                                <div class="attachment-meta">
                                    <span>Subido por: ${att.uploaded_by_name}</span>
                                    <span>Fecha: ${new Date(att.uploaded_at).toLocaleString()}</span>
                                    ${att.file_size ? `<span>Tamaño: ${formatFileSize(att.file_size)}</span>` : ''}
                                </div>
                                ${att.description ? `<div class="attachment-description">${att.description}</div>` : ''}
                            </div>
                        </div>
                    `).join('');
                }
            } else {
                container.innerHTML = '<p class="error">Error al cargar adjuntos: ' + data.message + '</p>';
            }
        })
        .catch(error => {
            document.getElementById('subtask-attachments-list').innerHTML = '<p class="error">Error de conexión</p>';
        });
}

function addSubtaskComment(subtaskId) {
    const commentText = document.getElementById('subtask-comment-text').value.trim();
    const fileInput = document.getElementById('subtask-comment-file');
    
    if (!commentText) {
        alert('Por favor escribe un comentario');
        return;
    }

    // Si hay archivo, primero subir el archivo
    if (fileInput.files.length > 0) {
        const formData = new FormData();
        formData.append('subtask_id', subtaskId);
        formData.append('file', fileInput.files[0]);
        
        fetch('?route=clan_member/upload-subtask-attachment', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ahora agregar el comentario con referencia al archivo
                addSubtaskCommentWithText(subtaskId, commentText, data.attachment_id);
            } else {
                alert('Error al subir archivo: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error de conexión al subir archivo');
        });
    } else {
        // Solo comentario sin archivo
        addSubtaskCommentWithText(subtaskId, commentText);
    }
}

function addSubtaskCommentWithText(subtaskId, commentText, attachmentId = null) {
    fetch('?route=clan_member/add-subtask-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subtask_id: subtaskId,
            comment_text: commentText,
            attachment_id: attachmentId
        })
    })
    .then(response => response.json())
            .then(data => {
            if (data.success) {
                document.getElementById('subtask-comment-text').value = '';
                document.getElementById('subtask-comment-file').value = '';
                loadSubtaskComments(subtaskId);
                loadSubtaskCounts(subtaskId); // Actualizar conteos
            } else {
                alert('Error al agregar comentario: ' + data.message);
            }
        })
    .catch(error => {
        alert('Error de conexión');
    });
}

function uploadSubtaskAttachment(subtaskId) {
    const fileInput = document.getElementById('subtask-attachment-file');
    const description = document.getElementById('subtask-attachment-description').value.trim();
    
    if (fileInput.files.length === 0) {
        alert('Por favor selecciona un archivo');
        return;
    }

    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('file', fileInput.files[0]);
    formData.append('description', description);
    
    fetch('?route=clan_member/upload-subtask-attachment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
            .then(data => {
            if (data.success) {
                fileInput.value = '';
                document.getElementById('subtask-attachment-description').value = '';
                loadSubtaskAttachments(subtaskId);
                loadSubtaskCounts(subtaskId); // Actualizar conteos
            } else {
                alert('Error al subir archivo: ' + data.message);
            }
        })
    .catch(error => {
        alert('Error de conexión');
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Cargar conteos para todas las subtareas al cargar la página
function loadAllSubtaskCounts() {
    const subtaskItems = document.querySelectorAll('[data-subtask-id]');
    subtaskItems.forEach(item => {
        const subtaskId = item.getAttribute('data-subtask-id');
        loadSubtaskCounts(subtaskId);
    });
}

function loadSubtaskCounts(subtaskId) {
    fetch('?route=clan_member/get-subtask-counts&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBadges(subtaskId, data.counts);
            }
        })
        .catch(error => {
            console.error('Error loading counts for subtask ' + subtaskId, error);
        });
}

function updateBadges(subtaskId, counts) {
    const commentsBadge = document.getElementById('comments-badge-' + subtaskId);
    const attachmentsBadge = document.getElementById('attachments-badge-' + subtaskId);
    
    if (commentsBadge) {
        if (counts.comments_count > 0) {
            commentsBadge.textContent = counts.comments_count;
            commentsBadge.style.display = 'inline-block';
        } else {
            commentsBadge.style.display = 'none';
        }
    }
    
    if (attachmentsBadge) {
        if (counts.attachments_count > 0) {
            attachmentsBadge.textContent = counts.attachments_count;
            attachmentsBadge.style.display = 'inline-block';
        } else {
            attachmentsBadge.style.display = 'none';
        }
    }
}

// Cargar conteos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadAllSubtaskCounts();
});
</script>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-body {
    padding: 20px;
}

.comment-item, .attachment-item {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.comment-date {
    color: #6b7280;
    font-size: 12px;
}

.comment-form, .attachment-form {
    margin-top: 15px;
}

.comment-form textarea, .attachment-form textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 8px;
    margin-bottom: 10px;
}

.comment-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.attachment-link {
    color: #3b82f6;
    text-decoration: none;
    display: inline-block;
    margin-right: 10px;
}

.attachment-link:hover {
    text-decoration: underline;
}

.attachment-meta {
    font-size: 12px;
    color: #6b7280;
    margin-top: 5px;
}

.attachment-meta span {
    margin-right: 15px;
}

.loading, .no-data, .error {
    text-align: center;
    padding: 20px;
    color: #6b7280;
}

.error {
    color: #dc2626;
}

.btn-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6b7280;
}

.btn-close:hover {
    color: #374151;
}

/* Estilos para badges de contadores */
.btn-with-badge {
    position: relative;
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 11px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    min-width: 18px;
    padding: 0 4px;
    box-sizing: border-box;
}

.badge:empty {
    display: none !important;
}

/* Estilos para archivos adjuntos en comentarios */
.comment-attachments {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e5e7eb;
}

.comment-attachments .attachment-link {
    display: inline-block;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 2px 4px 2px 0;
    font-size: 12px;
}

.attachment-date {
    color: #9ca3af;
    font-size: 10px;
    margin-left: 4px;
}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


