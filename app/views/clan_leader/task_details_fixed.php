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
                                    <select onchange="updateSubtaskStatus(<?php echo $subtask['subtask_id']; ?>, this.value)" class="status-select">
                                        <option value="pending" <?php echo $subtask['status'] === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="in_progress" <?php echo $subtask['status'] === 'in_progress' ? 'selected' : ''; ?>>En Progreso</option>
                                        <option value="completed" <?php echo $subtask['status'] === 'completed' ? 'selected' : ''; ?>>Completada</option>
                                        <option value="blocked" <?php echo $subtask['status'] === 'blocked' ? 'selected' : ''; ?>>Bloqueada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <!-- Sección de Comentarios -->
                <?php if (!empty($comments)): ?>
        <div class="summary-card">
          <h3><i class="fas fa-comments"></i> Comentarios (<?php echo count($comments); ?>)</h3>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-author"><?php echo htmlspecialchars($comment['full_name'] ?? $comment['username']); ?></div>
                                <div class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></div>
                            </div>
                            <div class="comment-content"><?php echo htmlspecialchars($comment['comment_text']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <!-- Sección de Historial -->
                <?php if (!empty($history)): ?>
        <div class="summary-card">
          <h3><i class="fas fa-history"></i> Historial</h3>
                    <div class="history-list">
                        <?php foreach ($history as $entry): ?>
            <div class="history-item">
                            <div class="history-date"><?php echo date('d/m/Y H:i', strtotime($entry['created_at'])); ?></div>
                            <div class="history-action"><?php echo htmlspecialchars($entry['action_description']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="right-pane">
        <!-- Sección de Usuarios Asignados -->
                <?php if (!empty($assignedUsers)): ?>
        <div class="summary-card">
          <h3><i class="fas fa-users"></i> Usuarios Asignados</h3>
                    <div class="assigned-users-list">
                        <?php foreach ($assignedUsers as $user): ?>
            <div class="assigned-user-item">
                            <div class="user-info">
                                <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                <div class="user-role"><?php echo htmlspecialchars($user['role_name'] ?? 'Sin rol'); ?></div>
                            </div>
                            <div class="user-percentage">
                                <?php echo $user['assigned_percentage']; ?>%
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <!-- Sección de Etiquetas -->
                <?php if (!empty($labels)): ?>
        <div class="summary-card">
          <h3><i class="fas fa-tags"></i> Etiquetas</h3>
                    <div class="labels-list">
                        <?php foreach ($labels as $label): ?>
            <span class="label" style="background-color: <?php echo htmlspecialchars($label['color']); ?>">
                            <?php echo htmlspecialchars($label['name']); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
        <!-- Sección de Acciones -->
        <div class="summary-card">
          <h3><i class="fas fa-cogs"></i> Acciones</h3>
                    <div class="actions-list">
            <button class="btn btn-primary" onclick="addComment()">
                            <i class="fas fa-comment"></i> Agregar Comentario
                        </button>
            <button class="btn btn-secondary" onclick="addAttachment()">
                            <i class="fas fa-paperclip"></i> Agregar Adjunto
                        </button>
            <button class="btn btn-success" onclick="assignUsers()">
                            <i class="fas fa-user-plus"></i> Asignar Usuarios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 10000;
        padding: 12px 20px; border-radius: 8px; color: white;
        font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%); transition: transform 0.3s ease;
    `;
    
    // Configurar colores según el tipo
    switch(type) {
        case 'success':
            notification.style.backgroundColor = '#10b981';
            break;
        case 'error':
            notification.style.backgroundColor = '#ef4444';
            break;
        case 'warning':
            notification.style.backgroundColor = '#f59e0b';
            break;
        default:
            notification.style.backgroundColor = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Cargar contadores de comentarios y adjuntos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadSubtaskCounters();
});

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
            // Recargar comentarios
            showSubtaskComments(subtaskId);
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
            // Recargar comentarios
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
            // Recargar la página para actualizar contadores
            location.reload();
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
            // Recargar adjuntos
            showSubtaskAttachments(subtaskId);
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
            // Recargar adjuntos
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
            // Recargar la página para actualizar contadores
            location.reload();
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
            const progressText = subtaskElement.querySelector('.progress-percentage');
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
                const progressText = document.querySelector(`[data-subtask-id="${subtaskId}"] .progress-percentage`);
                
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
                // Actualizar badges de comentarios
                const commentsBadge = document.getElementById('comments-badge-' + subtaskId);
                if (commentsBadge && data.counts.comments_count > 0) {
                    commentsBadge.textContent = data.counts.comments_count;
                    commentsBadge.style.display = 'inline';
                }
                
                // Actualizar badges de adjuntos
                const attachmentsBadge = document.getElementById('attachments-badge-' + subtaskId);
                if (attachmentsBadge && data.counts.attachments_count > 0) {
                    attachmentsBadge.textContent = data.counts.attachments_count;
                    attachmentsBadge.style.display = 'inline';
                }
                    } else {
                console.error('Error al cargar contadores:', data.message);
                    }
                })
                .catch(error => {
            console.error('Error al cargar contadores:', error);
        });
    });
}

// Cargar frase motivacional
function loadMotivationalQuote() {
    const quotes = [
        { text: "El éxito no es final, el fracaso no es fatal: es el coraje para continuar lo que cuenta.", author: "Winston Churchill" },
        { text: "La única forma de hacer un gran trabajo es amar lo que haces.", author: "Steve Jobs" },
        { text: "El futuro pertenece a aquellos que creen en la belleza de sus sueños.", author: "Eleanor Roosevelt" },
        { text: "La innovación distingue entre un líder y un seguidor.", author: "Steve Jobs" },
        { text: "El trabajo en equipo es la capacidad de trabajar juntos hacia una visión común.", author: "Andrew Carnegie" }
    ];
    
    const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
    document.getElementById('motQuote').textContent = randomQuote.text;
    document.getElementById('motAuthor').textContent = `- ${randomQuote.author}`;
}

// Cargar frase motivacional al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadMotivationalQuote();
});
</script>

<?php
$content = ob_get_clean();
?>
