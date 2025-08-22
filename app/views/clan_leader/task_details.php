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
              <div class="project-subtitle">Proyecto: <?php echo htmlspecialchars($task['project_name'] ?? 'N/A'); ?></div>
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
            <?php if (!empty($task['created_by_name'])): ?>
              <div class="meta"><i class="fas fa-user"></i> Creado por: <?php echo htmlspecialchars($task['created_by_name']); ?></div>
            <?php endif; ?>
            <?php if ($task['estimated_hours']): ?>
              <div class="meta"><i class="fas fa-clock"></i> Estimado: <?php echo $task['estimated_hours']; ?>h</div>
                <?php endif; ?>
            </div>
        </div>
        
                <?php if (!empty($task['description'])): ?>
        <div class="summary-card">
          <h3>Descripción</h3>
          <div><?php echo nl2br(htmlspecialchars($task['description'])); ?></div>
                </div>
                <?php endif; ?>
                
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
                    <button class="btn-icon-small" onclick="editSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                    <button class="btn-icon-small" onclick="deleteSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="subtask-meta">
                  <span>Estado: <?php echo ucfirst($subtask['status']); ?></span>
                                    <?php if (!empty($subtask['assigned_user_name'])): ?>
                  <span>Asignado: <?php echo htmlspecialchars($subtask['assigned_user_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($subtask['due_date']): ?>
                  <span>Vence: <?php echo date('d/m/Y', strtotime($subtask['due_date'])); ?></span>
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
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <div class="summary-card">
          <h3>Comentarios (<?php echo count($comments); ?>)</h3>
          <form id="tdCommentForm" class="comment-composer" enctype="multipart/form-data">
            <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>" />
            <textarea name="comment_text" placeholder="Escribe un comentario..."></textarea>
            <div class="form-group inline">
              <input type="file" name="attachments[]" multiple />
              <button class="action-btn primary" type="submit"><i class="fas fa-paper-plane"></i> Enviar</button>
                            </div>
          </form>
          <div class="comments-list" style="margin-top:10px;">
            <?php if (empty($comments)): ?>
              <div class="empty-minimal">Sin comentarios</div>
            <?php else: foreach ($comments as $c): ?>
                            <div class="comment-item">
                <div class="comment-meta"><span class="author"><?php echo htmlspecialchars($c['full_name'] ?? $c['username'] ?? ''); ?></span><span class="date"><?php echo htmlspecialchars($c['created_at'] ?? ''); ?></span></div>
                <div class="comment-text"><?php echo nl2br(htmlspecialchars($c['comment_text'] ?? '')); ?></div>
                <?php if (!empty($c['attachments'])): ?>
                <div class="comment-atts">
                  <?php foreach (($c['attachments'] ?? []) as $a): $url = htmlspecialchars($a['file_path'] ?? ''); $name = htmlspecialchars($a['file_name'] ?? 'archivo'); $type = strtolower($a['file_type'] ?? ''); ?>
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
          <a href="?route=clan_leader/tasks" class="btn-minimal"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
          <a href="?route=clan_leader/tasks&action=edit&task_id=<?php echo $task['task_id']; ?>" class="btn-minimal primary"><i class="fas fa-edit"></i> Editar Tarea</a>
          <button class="btn-minimal danger" onclick="deleteTask(<?php echo $task['task_id']; ?>)"><i class="fas fa-trash"></i> Eliminar</button>
                </div>
            </div>
            
      <aside class="right-pane">
        <div class="summary-card">
          <h3>Colaboradores (<?php echo count($assignedUsers); ?>)</h3>
                    
                    <!-- Botón para agregar colaborador -->
          <div style="margin-bottom: 15px;">
            <button onclick="showAddCollaboratorModal()" class="btn-minimal primary" style="width: 100%;">
                            <i class="fas fa-plus"></i>
                            Agregar Colaborador
                        </button>
                    </div>
                    
          <div class="assignees-list">
            <?php foreach ($assignedUsers as $au): ?>
              <div class="assignee-item" data-user-id="<?php echo $au['user_id']; ?>">
                <span class="avatar-initial"><?php echo strtoupper(substr($au['full_name'] ?? $au['username'] ?? '', 0, 1)); ?></span>
                <div class="assignee-info">
                  <div class="name"><?php echo htmlspecialchars($au['full_name'] ?? $au['username'] ?? ''); ?></div>
                  <div class="meta"><?php echo htmlspecialchars($au['username'] ?? ''); ?></div>
                                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                  <div style="display: flex; align-items: center; gap: 4px;">
                    <input type="number" min="0" max="100" value="<?php echo $au['assigned_percentage'] ?? 0; ?>" 
                           style="width: 50px; padding: 4px 6px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; text-align: center;" 
                           onchange="updateUserPercentage(<?php echo $au['user_id']; ?>, this.value)">
                    <span style="font-size: 12px;">%</span>
                                </div>
                  <button onclick="removeCollaborator(<?php echo $au['user_id']; ?>)" class="btn-icon-small" title="Remover" style="background: #fee2e2; color: #dc2626;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
            <?php if (empty($assignedUsers)): ?><div class="empty-minimal">Sin colaboradores</div><?php endif; ?>
                    </div>
                </div>
                
                <!-- Etiquetas -->
                <?php if (!empty($labels)): ?>
        <div class="summary-card">
          <h3>Etiquetas</h3>
          <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($labels as $label): ?>
            <span style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; background: <?php echo htmlspecialchars($label['label_color'] ?? '#e5e7eb'); ?>; color: #fff;">
              <?php echo htmlspecialchars($label['label_name'] ?? ''); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <div class="summary-card">
          <h3>Historial</h3>
          <div class="history-list">
            <?php if (empty($history)): ?>
              <div class="empty-minimal">Sin historial</div>
            <?php else: foreach ($history as $h): ?>
                    <div class="history-item">
                <div class="title"><?php echo htmlspecialchars(ucfirst($h['action_type'] ?? $h['notes'] ?? '')); ?></div>
                <div class="meta">Por: <?php echo htmlspecialchars($h['full_name'] ?? $h['username'] ?? ''); ?> — <?php echo htmlspecialchars($h['created_at'] ?? ''); ?></div>
                        </div>
            <?php endforeach; endif; ?>
                            </div>
                        </div>

        <div class="summary-card">
          <h3>Información</h3>
          <div class="info-grid">
            <div><strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?></div>
                        <?php if ($task['updated_at'] !== $task['created_at']): ?>
            <div><strong>Actualizado:</strong> <?php echo date('d/m/Y H:i', strtotime($task['updated_at'])); ?></div>
                        <?php endif; ?>
            <div><strong>Progreso:</strong> <?php echo (int)($task['completion_percentage'] ?? 0); ?>%</div>
                        <?php if ($task['actual_hours']): ?>
            <div><strong>Horas reales:</strong> <?php echo $task['actual_hours']; ?>h</div>
                        <?php endif; ?>
                    </div>
                </div>
      </aside>
            </div>
        </div>
    </div>
    
    <!-- Modal para agregar colaborador -->
    <div id="addCollaboratorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Agregar Colaborador</h3>
                <span class="close" onclick="closeAddCollaboratorModal()">&times;</span>
            </div>
            <div class="modal-body">
      <div id="availableUsersList" style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <!-- Los usuarios se cargarán dinámicamente -->
                </div>
      <div style="margin-top: 20px; text-align: right;">
        <button onclick="closeAddCollaboratorModal()" class="btn-minimal secondary">Cancelar</button>
        <button onclick="addSelectedCollaborators()" class="btn-minimal primary">Agregar Seleccionados</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
document.getElementById('tdCommentForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_leader/add-task-comment', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ alert(d.message||'Error'); return; } location.reload(); });
});

// Frase motivacional por sesión
(function(){
  const qEl = document.getElementById('motQuote');
  const aEl = document.getElementById('motAuthor');
  if (!qEl || !aEl) return;

  const SS_KEY = 'rt_motivational_quote';
  const cached = sessionStorage.getItem(SS_KEY);
  if (cached) {
    try { const obj = JSON.parse(cached); qEl.textContent = '"'+(obj.text||'')+'"'; aEl.textContent = obj.author? ('— '+obj.author) : ''; return; } catch(_){}
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
    qEl.textContent = '"'+(q.text||'')+'"';
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
        
        // Funciones para colaboradores
        function showAddCollaboratorModal() {
            fetch('?route=clan_leader/get-available-users')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userList = document.getElementById('availableUsersList');
                    userList.innerHTML = '';
                    
                    data.users.forEach(user => {
                        const userOption = document.createElement('div');
                        userOption.className = 'user-option';
                        userOption.innerHTML = `
                            <input type="checkbox" id="user_${user.user_id}" value="${user.user_id}">
          <div style="width: 32px; height: 32px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                ${user.full_name ? user.full_name.charAt(0).toUpperCase() : user.username.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="font-weight: 600;">${user.full_name || user.username}</div>
                                <div style="font-size: 12px; color: #6b7280;">${user.username}</div>
                            </div>
                        `;
                        userList.appendChild(userOption);
                    });
                    
      document.getElementById('addCollaboratorModal').style.display = 'flex';
                } else {
                    showNotification('Error al cargar usuarios: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al cargar usuarios', 'error');
            });
        }
        
        function closeAddCollaboratorModal() {
            document.getElementById('addCollaboratorModal').style.display = 'none';
        }
        
        function addSelectedCollaborators() {
            const checkboxes = document.querySelectorAll('#availableUsersList input[type="checkbox"]:checked');
            const userIds = Array.from(checkboxes).map(cb => cb.value);
            
            if (userIds.length === 0) {
                showNotification('Por favor selecciona al menos un usuario', 'error');
                return;
            }
            
            fetch('?route=clan_leader/add-collaborators', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
    body: `task_id=<?php echo $task['task_id']; ?>&user_ids=${userIds.join(',')}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddCollaboratorModal();
                    showNotification('Colaboradores agregados exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error al agregar colaboradores: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar colaboradores', 'error');
            });
        }
        
        function updateUserPercentage(userId, percentage) {
            fetch('?route=clan_leader/update-user-percentage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
    body: `task_id=<?php echo $task['task_id']; ?>&user_id=${userId}&percentage=${percentage}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Porcentaje actualizado', 'success');
                } else {
                    showNotification('Error al actualizar porcentaje: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar porcentaje', 'error');
            });
        }
        
        function removeCollaborator(userId) {
  if (typeof showConfirmationModal === 'function') {
            showConfirmationModal({
            title: 'Confirmar Remoción',
            message: '¿Estás seguro de que quieres remover este colaborador?',
            type: 'warning',
            confirmText: 'Remover',
            cancelText: 'Cancelar',
            onConfirm: () => {
                fetch('?route=clan_leader/remove-collaborator', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
          body: `task_id=<?php echo $task['task_id']; ?>&user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
                        if (userItem) userItem.remove();
                        showNotification('Colaborador removido', 'success');
                    } else {
                        showNotification('Error al remover colaborador: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al remover colaborador', 'error');
                });
            }
        });
  } else {
    if (!confirm('¿Estás seguro de que quieres remover este colaborador?')) return;
    
    fetch('?route=clan_leader/remove-collaborator', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
      body: `task_id=<?php echo $task['task_id']; ?>&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (userItem) userItem.remove();
        alert('Colaborador removido exitosamente');
      } else {
        alert('Error al remover colaborador: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al remover colaborador');
    });
  }
}

function deleteTask(taskId) {
  if (typeof showConfirmationModal === 'function') {
    showConfirmationModal({
      title: 'Confirmar Eliminación',
      message: '¿Estás seguro de que quieres eliminar esta tarea?',
      type: 'warning',
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      onConfirm: () => {
        fetch('?route=clan_leader/delete-task', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'task_id=' + taskId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Tarea eliminada exitosamente', 'success');
            setTimeout(() => { window.location.href = '?route=clan_leader/tasks'; }, 800);
                } else {
            showNotification('Error al eliminar la tarea: ' + data.message, 'error');
                }
            })
            .catch(error => {
          showNotification('Error al eliminar la tarea', 'error');
        });
      }
    });
  } else {
    if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) return;
    
    fetch('?route=clan_leader/delete-task', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'task_id=' + taskId
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Tarea eliminada exitosamente');
        setTimeout(() => { window.location.href = '?route=clan_leader/tasks'; }, 800);
      } else {
        alert('Error al eliminar la tarea: ' + (data.message || 'Error desconocido'));
      }
    })
    .catch(() => alert('Error al eliminar la tarea'));
  }
}

// Función simple de notificación
        function showNotification(message, type = 'info') {
  let notification = document.getElementById('cl-notification');
  if (!notification) {
    const notificationHTML = `
      <div id="cl-notification" style="
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
        <span id="cl-notificationMessage"></span>
        <button onclick="closeCLNotification()" style="
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
    notification = document.getElementById('cl-notification');
  }
  
  const messageElement = document.getElementById('cl-notificationMessage');
  messageElement.textContent = message;
  
  if (type === 'success') {
    notification.style.backgroundColor = '#10b981';
  } else if (type === 'error') {
    notification.style.backgroundColor = '#ef4444';
  } else {
    notification.style.backgroundColor = '#3b82f6';
  }
  
  notification.style.display = 'block';
  
  setTimeout(() => {
    notification.style.display = 'none';
            }, 3000);
        }
        
function closeCLNotification() {
  const notification = document.getElementById('cl-notification');
  if (notification) {
    notification.style.display = 'none';
  }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
  const modal = document.getElementById('addCollaboratorModal');
  if (event.target === modal) {
    closeAddCollaboratorModal();
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

.btn-minimal.danger {
  background: #ef4444 !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
}

.btn-minimal.danger:hover {
  background: #dc2626 !important;
  border-color: #dc2626 !important;
}

.btn-minimal.secondary {
  background: #f3f4f6 !important;
  color: #374151 !important;
  border-color: #d1d5db !important;
}

.btn-minimal.secondary:hover {
  background: #e5e7eb !important;
  border-color: #9ca3af !important;
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

/* Modal básico */
.modal { position: fixed; inset: 0; background: rgba(17,24,39,.45); display: none; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
.modal-content { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 20px 48px rgba(17,24,39,.18); width: 100%; max-width: 500px; overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.05rem; }
.modal-body { padding: 16px; }
.close { border: none; background: transparent; font-size: 20px; cursor: pointer; color: #6b7280; }
.close:hover { color: #374151; }

/* User list in modal */
.user-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  border-bottom: 1px solid #f3f4f6;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.user-option:hover {
  background-color: #f9fafb;
}

.user-option.selected {
  background-color: #dbeafe;
}

.user-option input[type="checkbox"] {
  margin: 0;
}
</style>

<script>
// Funciones para subtareas
function showSubtaskComments(subtaskId) {
    console.log('Mostrando comentarios de subtarea:', subtaskId);
    
    // Cargar comentarios desde el servidor
    fetch('?route=clan_leader/get-subtask-comments&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCommentsModal(subtaskId, data.comments);
            } else {
                showNotification('Error al cargar comentarios: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar comentarios', 'error');
        });
}

function showCommentsModal(subtaskId, comments) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px; max-height: 80vh;">
            <div class="modal-header">
                <h3><i class="fas fa-comments"></i> Comentarios de la Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="comments-section">
                    <div class="add-comment-section" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px 0;">Agregar Comentario</h4>
                        <textarea id="new-comment-text" rows="3" placeholder="Escribe tu comentario..." style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 10px; resize: vertical;"></textarea>
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button onclick="addSubtaskComment(${subtaskId})" class="btn btn-primary" style="padding: 8px 16px;">Agregar Comentario</button>
                        </div>
                    </div>
                    
                    <div class="comments-list">
                        ${comments.length === 0 ? '<p style="text-align: center; color: #6b7280; font-style: italic;">No hay comentarios aún</p>' : ''}
                        ${comments.map(comment => `
                            <div class="comment-item" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: white;">
                                <div class="comment-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <div class="comment-author" style="font-weight: 600; color: #374151;">
                                        ${comment.full_name || comment.username}
                                    </div>
                                    <div class="comment-meta" style="display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 12px; color: #6b7280;">
                                            ${new Date(comment.created_at).toLocaleString('es-ES')}
                                        </span>
                                        ${comment.user_id == <?php echo $this->currentUser['user_id'] ?? 0; ?> ? 
                                            `<button onclick="deleteSubtaskComment(${comment.comment_id})" class="btn-icon-small" style="background: #ef4444; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>` : ''
                                        }
                                    </div>
                                </div>
                                <div class="comment-content" style="color: #374151; line-height: 1.5;">
                                    ${comment.comment_text}
                                </div>
                            </div>
                        `).join('')}
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
}

function addSubtaskComment(subtaskId) {
    const commentText = document.getElementById('new-comment-text').value.trim();
    
    if (!commentText) {
        alert('Por favor escribe un comentario');
        return;
    }
    
    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('comment_text', commentText);
    
    fetch('?route=clan_leader/add-subtask-comment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar comentarios y actualizar contadores
            showSubtaskComments(subtaskId);
            loadSubtaskCounters();
        } else {
            alert('Error al agregar comentario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function deleteSubtaskComment(commentId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
        return;
    }
    
    fetch('?route=clan_leader/delete-subtask-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            comment_id: commentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal y actualizar contadores
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
            // Actualizar contadores sin recargar la página
            loadSubtaskCounters();
            showNotification('Comentario eliminado exitosamente', 'success');
        } else {
            alert('Error al eliminar comentario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function showSubtaskAttachments(subtaskId) {
    console.log('Mostrando adjuntos de subtarea:', subtaskId);
    
    // Cargar adjuntos desde el servidor
    fetch('?route=clan_leader/get-subtask-attachments&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAttachmentsModal(subtaskId, data.attachments);
            } else {
                showNotification('Error al cargar adjuntos: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar adjuntos', 'error');
        });
}

function showAttachmentsModal(subtaskId, attachments) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px; max-height: 80vh;">
            <div class="modal-header">
                <h3><i class="fas fa-paperclip"></i> Adjuntos de la Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="attachments-section">
                    <div class="add-attachment-section" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px 0;">Subir Archivo</h4>
                        <form id="upload-form" enctype="multipart/form-data">
                            <input type="file" id="attachment-file" style="margin-bottom: 10px;" accept="*/*">
                            <textarea id="attachment-description" rows="2" placeholder="Descripción del archivo (opcional)..." style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 10px; resize: vertical;"></textarea>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" onclick="uploadSubtaskAttachment(${subtaskId})" class="btn btn-primary" style="padding: 8px 16px;">Subir Archivo</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="attachments-list">
                        ${attachments.length === 0 ? '<p style="text-align: center; color: #6b7280; font-style: italic;">No hay adjuntos aún</p>' : ''}
                        ${attachments.map(attachment => `
                            <div class="attachment-item" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: white;">
                                <div class="attachment-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <div class="attachment-info">
                                        <div class="attachment-name" style="font-weight: 600; color: #374151; display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-file"></i>
                                            <a href="${attachment.file_path}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                                ${attachment.file_name}
                                            </a>
                                        </div>
                                        <div class="attachment-meta" style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                            ${formatFileSize(attachment.file_size)} • ${attachment.file_type} • ${new Date(attachment.uploaded_at).toLocaleString('es-ES')}
                                        </div>
                                    </div>
                                    <div class="attachment-actions">
                                        <a href="${attachment.file_path}" download="${attachment.file_name}" class="btn-icon-small" style="background: #10b981; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; text-decoration: none; margin-right: 5px;">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        ${attachment.user_id == <?php echo $this->currentUser['user_id'] ?? 0; ?> ? 
                                            `<button onclick="deleteSubtaskAttachment(${attachment.attachment_id})" class="btn-icon-small" style="background: #ef4444; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>` : ''
                                        }
                                    </div>
                                </div>
                                ${attachment.description ? `
                                    <div class="attachment-description" style="color: #6b7280; font-size: 14px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #f3f4f6;">
                                        ${attachment.description}
                                    </div>
                                ` : ''}
                            </div>
                        `).join('')}
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
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function uploadSubtaskAttachment(subtaskId) {
    const fileInput = document.getElementById('attachment-file');
    const description = document.getElementById('attachment-description').value.trim();
    
    if (!fileInput.files[0]) {
        alert('Por favor selecciona un archivo');
        return;
    }
    
    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('file', fileInput.files[0]);
    if (description) {
        formData.append('description', description);
    }
    
    fetch('?route=clan_leader/upload-subtask-attachment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar adjuntos y actualizar contadores
            showSubtaskAttachments(subtaskId);
            loadSubtaskCounters();
        } else {
            alert('Error al subir archivo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function deleteSubtaskAttachment(attachmentId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) {
        return;
    }
    
    fetch('?route=clan_leader/delete-subtask-attachment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            attachment_id: attachmentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal y actualizar contadores
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
            // Actualizar contadores sin recargar la página
            loadSubtaskCounters();
            showNotification('Archivo eliminado exitosamente', 'success');
        } else {
            alert('Error al eliminar archivo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function editSubtask(subtaskId) {
    console.log('Editando subtarea:', subtaskId);
    
    // Obtener datos de la subtarea
    fetch('?route=clan_leader/get-subtask-for-edit&subtask_id=' + subtaskId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEditSubtaskModal(data.subtask);
            } else {
                showNotification('Error al cargar datos de subtarea: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar datos de subtarea', 'error');
        });
}

function showEditSubtaskModal(subtask) {
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
                        <input type="text" id="edit-subtask-title" value="${subtask.title}" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 10px;">
                    </div>
                    <div class="form-group">
                        <label for="edit-subtask-description">Descripción:</label>
                        <textarea id="edit-subtask-description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 15px;">${subtask.description || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-subtask-status">Estado:</label>
                        <select id="edit-subtask-status" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 10px;">
                            <option value="pending" ${subtask.status === 'pending' ? 'selected' : ''}>Pendiente</option>
                            <option value="in_progress" ${subtask.status === 'in_progress' ? 'selected' : ''}>En Progreso</option>
                            <option value="completed" ${subtask.status === 'completed' ? 'selected' : ''}>Completada</option>
                            <option value="blocked" ${subtask.status === 'blocked' ? 'selected' : ''}>Bloqueada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-subtask-percentage">Porcentaje de Completación:</label>
                        <input type="range" id="edit-subtask-percentage" min="0" max="100" value="${subtask.completion_percentage || 0}" style="width: 100%; margin-bottom: 5px;">
                        <span id="percentage-display">${subtask.completion_percentage || 0}%</span>
                    </div>
                    <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="this.closest('.modal-overlay').remove()" class="btn btn-secondary">Cancelar</button>
                        <button type="button" onclick="saveSubtaskChanges(${subtask.subtask_id})" class="btn btn-primary">Guardar</button>
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
    
    // Configurar el slider de porcentaje
    const percentageSlider = document.getElementById('edit-subtask-percentage');
    const percentageDisplay = document.getElementById('percentage-display');
    percentageSlider.addEventListener('input', function() {
        percentageDisplay.textContent = this.value + '%';
    });
    
    document.getElementById('edit-subtask-title').focus();
}

function saveSubtaskChanges(subtaskId) {
    const title = document.getElementById('edit-subtask-title').value.trim();
    const description = document.getElementById('edit-subtask-description').value.trim();
    const status = document.getElementById('edit-subtask-status').value;
    const completionPercentage = parseInt(document.getElementById('edit-subtask-percentage').value);
    
    if (!title) {
        alert('El título es requerido');
        return;
    }
    
    fetch('?route=clan_leader/edit-subtask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subtask_id: subtaskId,
            title: title,
            description: description,
            status: status,
            completion_percentage: completionPercentage
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
            
            // Actualizar estado y progreso
            const statusElement = subtaskElement.querySelector('.subtask-status');
            if (statusElement) {
                statusElement.textContent = status;
                statusElement.className = 'subtask-status status-' + status;
            }
            
            const progressFill = subtaskElement.querySelector('.progress-fill');
            const progressText = subtaskElement.querySelector('.progress-text');
            if (progressFill) {
                progressFill.style.width = completionPercentage + '%';
            }
            if (progressText) {
                progressText.textContent = completionPercentage + '%';
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

function deleteSubtask(subtaskId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta subtarea?')) {
        console.log('Eliminando subtarea:', subtaskId);
        
        fetch('?route=clan_leader/delete-subtask', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'subtask_id=' + subtaskId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                // Remover el elemento de la vista
                const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                if (subtaskElement) {
                    subtaskElement.remove();
                }
                showNotification('Subtarea eliminada correctamente', 'success');
                } else {
                showNotification('Error al eliminar la subtarea: ' + (data.message || 'Error desconocido'), 'error');
                }
            })
            .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar la subtarea', 'error');
        });
    }
}

function updateSubtaskStatus(subtaskId, newStatus) {
    console.log('Actualizando estado de subtarea:', subtaskId, 'a:', newStatus);
    
    fetch('?route=clan_leader/update-subtask-status', {
                method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'subtask_id=' + subtaskId + '&status=' + newStatus
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
            // Actualizar el progreso si viene en la respuesta
            if (data.completion_percentage !== undefined) {
                const progressFill = document.querySelector(`[data-subtask-id="${subtaskId}"] .progress-fill`);
                const progressText = document.querySelector(`[data-subtask-id="${subtaskId}"] .progress-text`);
                
                if (progressFill) {
                    progressFill.style.width = data.completion_percentage + '%';
                }
                if (progressText) {
                    progressText.textContent = data.completion_percentage + '%';
                }
            }
            
            showNotification('Estado de subtarea actualizado correctamente', 'success');
                } else {
            showNotification('Error al actualizar el estado: ' + (data.message || 'Error desconocido'), 'error');
                }
            })
            .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar el estado de la subtarea', 'error');
    });
}

// Función auxiliar para cargar contadores de comentarios y adjuntos
function loadSubtaskCounters() {
    // Obtener todos los IDs de subtareas
    const subtaskElements = document.querySelectorAll('[data-subtask-id]');
    
    subtaskElements.forEach(element => {
        const subtaskId = element.getAttribute('data-subtask-id');
        
        // Cargar contadores usando la ruta existente
            fetch('?route=clan_leader/get-subtask-counts&subtask_id=' + subtaskId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar badge de comentarios
                        const commentsBadge = document.getElementById('comments-badge-' + subtaskId);
                        if (commentsBadge && data.counts.comments_count > 0) {
                            commentsBadge.textContent = data.counts.comments_count;
                            commentsBadge.style.display = 'inline';
                        }
                        
                        // Actualizar badge de adjuntos
                        const attachmentsBadge = document.getElementById('attachments-badge-' + subtaskId);
                        if (attachmentsBadge && data.counts.attachments_count > 0) {
                            attachmentsBadge.textContent = data.counts.attachments_count;
                            attachmentsBadge.style.display = 'inline';
                        }
                    } else {
                        console.error('Error al cargar contadores:', data.message);
                    }
                }
            })
            .catch(error => console.log('Error cargando contadores:', error));
    });
}

// Cargar contadores al inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
    loadSubtaskCounters();
        });
    </script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>
