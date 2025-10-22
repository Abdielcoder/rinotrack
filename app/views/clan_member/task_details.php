<?php
ob_start();

// Agregar dependencias de Quill.js al layout
$additionalCSS[] = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
$additionalJS[] = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
?>

<div class="cm-task-details minimal">
  <div class="content-minimal" style="max-width:1100px;">
    
    <!-- Botones de Acción Superior -->
    <div class="header-actions" style="display: flex; gap: 12px; margin-bottom: 20px; padding: 0 0 15px 0; border-bottom: 1px solid #e5e7eb; justify-content: space-between; align-items: center;">
      <div style="display: flex; gap: 12px;">
        <button onclick="history.back()" style="background: #f3f4f6; color: #374151; padding: 12px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
          <i class="fas fa-arrow-left"></i> Volver Atrás
        </button>
        <?php 
          // Solo mostrar botón editar si la tarea fue creada por el usuario actual (tareas personales)
          $canEditTask = (int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id']; 
        ?>
        <?php if ($canEditTask): ?>
          <button class="btn-minimal primary" onclick="openEditTaskModal()" style="padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-edit"></i> Editar Tarea
          </button>
        <?php endif; ?>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="?route=logout" style="background: #dc2626; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;" title="Cerrar Sesión" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
      </div>
    </div>
    
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

        <!-- Botón para agregar subtarea -->
        <div style="margin-bottom: 20px;">
            <button onclick="showAddSubtaskModal()" style="background: #1e3a8a; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.2s;">
                <i class="fas fa-plus"></i>
                Agregar Subtarea
            </button>
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
                  <div class="subtask-title" title="<?php echo htmlspecialchars($subtask['title']); ?>"><?php echo htmlspecialchars($subtask['title']); ?></div>
                  <div class="subtask-actions">
                    <button class="btn-icon-small btn-with-badge" id="comments-btn-<?php echo $subtask['subtask_id']; ?>" onclick="showSubtaskComments(<?php echo $subtask['subtask_id']; ?>)" title="Ver comentarios">
                      <i class="fas fa-comments"></i>
                      <span class="badge" id="comments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                    </button>
                    <button class="btn-icon-small btn-with-badge" id="attachments-btn-<?php echo $subtask['subtask_id']; ?>" onclick="showSubtaskAttachments(<?php echo $subtask['subtask_id']; ?>)" title="Ver adjuntos">
                      <i class="fas fa-paperclip"></i>
                      <span class="badge" id="attachments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                    </button>
                    <?php 
                      // Permitir editar subtareas a cualquier miembro del clan (se controlará en backend)
                      $canEditSubtask = true; // Los permisos específicos se verifican en el backend
                    ?>
                    <?php if ($canEditSubtask): ?>
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
                  <?php if (!empty($subtask['all_assigned_users'])): ?>
                  <?php 
                  // Convertir la cadena de usuarios en un array
                  $assignedUsers = explode(', ', $subtask['all_assigned_users']);
                  ?>
                  <div class="subtask-collaborators">
                    <span style="font-size: 12px; color: #6b7280;">Colaboradores:</span>
                    <div class="collaborators-chips">
                      <?php foreach ($assignedUsers as $userName): ?>
                      <div class="collaborator-chip">
                        <span><?php echo htmlspecialchars(trim($userName)); ?></span>
                        <button class="remove-collaborator" onclick="removeCollaboratorFromSubtask(<?php echo $subtask['subtask_id']; ?>, '<?php echo htmlspecialchars(trim($userName), ENT_QUOTES); ?>')" title="Remover colaborador">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if ($subtask['due_date']): ?>
                  <span>Vence: <?php echo Utils::formatDate($subtask['due_date']); ?></span>
                  <?php endif; ?>
                  <!-- Botón para asignar usuarios a subtarea (disponible para clan members) -->
                  <div class="subtask-assignment-controls" style="margin-top: 8px;">
                    <button class="btn-icon-small" onclick="showSubtaskAssignmentModal(<?php echo $subtask['subtask_id']; ?>)" title="Asignar usuarios a subtarea">
                      <i class="fas fa-user-plus"></i>
                      Asignar
                    </button>
                  </div>
                </div>
                <?php if (!empty($subtask['description'])): ?>
                <div class="subtask-description">
                  <?php echo htmlspecialchars($subtask['description']); ?>
                </div>
                <?php endif; ?>
              </div>
              <div class="subtask-controls">
                <div class="subtask-progress" style="flex: 1;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="progress-bar" 
                         style="flex: 1; height: 20px; cursor: pointer; position: relative;" 
                         onclick="updateSubtaskProgressFromClick(event, <?php echo $subtask['subtask_id']; ?>)"
                         data-subtask-id="<?php echo $subtask['subtask_id']; ?>">
                      <div class="progress-fill" style="width: <?php echo $subtask['completion_percentage']; ?>%; height: 100%; background: linear-gradient(90deg, #10b981, #22c55e); border-radius: 4px;"></div>
                      <span class="progress-percentage" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: #374151; text-shadow: 0 0 2px white;">
                        <?php echo intval($subtask['completion_percentage']); ?>%
                      </span>
                    </div>
                    <input type="range" 
                           class="subtask-progress-slider" 
                           data-subtask-id="<?php echo $subtask['subtask_id']; ?>"
                           min="0" 
                           max="100" 
                           value="<?php echo intval($subtask['completion_percentage']); ?>" 
                           oninput="updateSubtaskProgress(<?php echo $subtask['subtask_id']; ?>, this.value)"
                           style="width: 100px;">
                  </div>
                </div>
                <div class="subtask-status-controls">
                  <select class="status-select" onchange="updateSubtaskStatus(<?php echo $subtask['subtask_id']; ?>, this.value)">
                    <option value="pending" <?php echo $subtask['status'] === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="in_progress" <?php echo $subtask['status'] === 'in_progress' ? 'selected' : ''; ?>>En Progreso</option>
                    <?php 
                      // Solo mostrar opción "completada" si es creador, asignado, o es tarea recurrente/eventual
                      $isTaskCreator = ((int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id']);
                      $isRecurrentTask = (bool)($task['is_recurrent'] ?? false);
                      $isRecurrentProject = in_array($project['project_type'] ?? '', ['recurrent', 'eventual']);
                      $canComplete = $canEdit || $isTaskCreator || $isRecurrentTask || $isRecurrentProject;
                    ?>
                    <?php if ($canComplete): ?>
                    <option value="completed" <?php echo $subtask['status'] === 'completed' ? 'selected' : ''; ?>>Completada</option>
                    <?php else: ?>
                    <?php if ($subtask['status'] === 'completed'): ?>
                    <option value="completed" selected disabled>Completada (Solo lectura)</option>
                    <?php endif; ?>
                    <?php endif; ?>
                  </select>
                  <span class="status-display">
                    Estado: <?php echo ucfirst(str_replace('_', ' ', $subtask['status'])); ?>
                  </span>
                  <?php if (!$canComplete && $subtask['status'] !== 'completed'): ?>
                  <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                    <i class="fas fa-info-circle"></i> Solo el creador/asignado o tareas recurrentes/eventuales pueden completarse
                </div>
                <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Código defensivo: Asegurar que no se muestren etiquetas en vista de miembro de clan -->
        <?php 
        // IMPORTANTE: Los miembros de clan NO deben ver etiquetas de tareas
        // Esta sección está comentada intencionalmente para prevenir bugs
        /*
        if (!empty($labels)): 
            // Esta sección está deshabilitada para miembros de clan
            // Solo los líderes de clan pueden ver etiquetas
        endif; 
        */
        ?>

        <div class="summary-card">
          <h3>Comentarios (<?php echo count($comments); ?>)</h3>
          <?php if ($canEdit): ?>
          <form id="tdCommentForm" class="comment-composer" enctype="multipart/form-data">
            <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>" />
            <input type="hidden" name="comment_text" id="task-comment-content" />
            <div class="rich-editor-container" style="position: relative;">
                <div id="task-comment-editor" style="margin-bottom: 10px;"></div>
                <button type="button" class="emoji-picker-btn" onclick="toggleEmojiPicker('task-comment')" title="Agregar emoji">
                    <i class="fas fa-smile"></i>
                </button>
                <div id="task-comment-emoji-picker" class="emoji-picker-container" style="display: none;"></div>
            </div>
            <div class="form-group inline" style="margin-top: 30px;">
              <input type="file" name="attachments[]" multiple />
              <button class="action-btn primary" type="submit"><i class="fas fa-paper-plane"></i> Enviar</button>
            </div>
          </form>
          <?php endif; ?>
          <div class="comments-list" style="margin-top:10px;">
            <?php if (empty($comments)): ?>
              <div class="empty-minimal">Sin comentarios</div>
            <?php else: foreach ($comments as $c): ?>
              <div class="comment-item" data-comment-id="<?php echo (int)($c['comment_id'] ?? 0); ?>">
                <div class="comment-meta"><span class="author"><?php echo htmlspecialchars($c['full_name'] ?? $c['username'] ?? ''); ?></span><span class="date"><?php echo htmlspecialchars($c['created_at'] ?? ''); ?></span></div>
                <div class="comment-content comment-text"><?php echo Utils::sanitizeHtml($c['comment_text'] ?? ''); ?></div>
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
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">Historial</h3>
            <?php 
              // Mostrar botón si hay historial Y el usuario es creador de la tarea O tiene permisos de edición
              $canDownloadHistory = !empty($history) && ($canEditTask || ($canEdit ?? false));
            ?>
            <?php if ($canDownloadHistory): ?>
              <button onclick="downloadTaskHistory(<?php echo (int)$task['task_id']; ?>)" 
                      style="background: #059669; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;"
                      onmouseover="this.style.background='#047857'"
                      onmouseout="this.style.background='#059669'"
                      title="Descargar historial en CSV (Excel)">
                <i class="fas fa-download"></i>
                <i class="fas fa-file-csv"></i>
                CSV
              </button>
            <?php endif; ?>
          </div>
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
            <?php 
                // Permitir editar progreso si la tarea fue creada por el usuario (personal) O si está asignada al usuario
                $isTaskCreator = (int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id'];
                $isAssignedToUser = $canEdit; // $canEdit ya verifica si está asignado
                $canEditProgress = $isTaskCreator || $isAssignedToUser;
                $currentProgress = (int)($task['completion_percentage'] ?? 0);
            ?>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              <strong>Progreso de la Tarea:</strong>
              <?php if ($canEditProgress): ?>
                <div class="task-progress-container">
                  <div class="task-progress-bar" onclick="updateTaskProgress(event)">
                    <div class="task-progress-fill" style="width: <?php echo $currentProgress; ?>%"></div>
                    <span class="task-progress-text"><?php echo $currentProgress; ?>%</span>
                  </div>
                  <input type="range" id="taskProgressSlider" min="0" max="100" value="<?php echo $currentProgress; ?>" 
                         onchange="updateTaskProgressFromSlider(this.value)" 
                         style="width: 100%; margin-top: 5px;">
                </div>
              <?php else: ?>
                <div class="task-progress-readonly">
                  <div class="task-progress-bar">
                    <div class="task-progress-fill" style="width: <?php echo $currentProgress; ?>%"></div>
                    <span class="task-progress-text"><?php echo $currentProgress; ?>%</span>
                  </div>
                  <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Solo lectura: No tienes permisos para editar esta tarea
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>

<script>
document.getElementById('tdCommentForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  
  console.log('=== DEBUG: Envío de comentario de tarea principal ===');
  console.log('Editor disponible:', !!taskCommentEditor);
  
  let commentText = '';
  
  // Obtener contenido del editor Quill o textarea fallback
  if (taskCommentEditor) {
    const editorContent = taskCommentEditor.getContents();
    const htmlContent = taskCommentEditor.root.innerHTML;
    
    console.log('Editor content:', editorContent);
    console.log('HTML content:', htmlContent);
    
    // Validar que hay contenido (más flexible)
    const textContent = taskCommentEditor.getText().trim();
    if (!textContent || textContent.length === 0) {
      alert('Por favor escribe un comentario');
      return;
    }
    
    // Establecer el contenido en el campo oculto
    document.getElementById('task-comment-content').value = htmlContent;
    commentText = htmlContent;
  } else {
    console.log('Editor no disponible, usando fallback');
    // Fallback para textarea normal
    const textarea = this.querySelector('textarea[name="comment_text"]');
    if (textarea) {
      const content = textarea.value.trim();
      if (!content) {
        alert('Por favor escribe un comentario');
        return;
      }
      document.getElementById('task-comment-content').value = content;
      commentText = content;
    } else {
      alert('Error: No se pudo obtener el contenido del comentario');
      return;
    }
  }
  
  console.log('Comentario a enviar:', commentText);
  
  const fd = new FormData(this);
  console.log('FormData creado, enviando petición...');
  
  fetch('?route=clan_member/add-task-comment', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r => { 
      console.log('Response status:', r.status);
      const t = await r.text(); 
      console.log('Response text:', t);
      try{ 
        return JSON.parse(t); 
      } catch(e){ 
        console.error('Error parsing JSON:', e);
        console.error('Raw response:', t); 
        return {success:false,message:'Respuesta inválida del servidor'}; 
      } 
    })
    .then(d => { 
      console.log('Parsed response:', d);
      if(!d.success){ 
        console.error('Error del servidor:', d.message);
        alert(d.message||'Error al enviar comentario'); 
        return; 
      } 
      
      console.log('Comentario enviado exitosamente');
      
      // Limpiar editor después del éxito
      if (taskCommentEditor) {
        taskCommentEditor.setContents([]);
      }
      
      alert('Comentario agregado exitosamente');
      location.reload(); 
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert('Error de conexión: ' + error.message);
    });
});

function noPermissionModal(){
  if (window.confirmInfo) {
    window.confirmInfo('No tienes permisos para modificar esta tarea.', function(){}, function(){});
  } else {
    alert('No tienes permisos para modificar esta tarea.');
  }
}

// Ejecutar función preventiva cuando la página se carga
document.addEventListener('DOMContentLoaded', function() {
    console.log('Vista de miembro de clan cargada - ejecutando función preventiva');
    preventIncorrectLabels();
    
    // También ejecutar cada 5 segundos como medida adicional
    setInterval(preventIncorrectLabels, 5000);
});
</script>

<style>
.motivational-card{background:linear-gradient(90deg, rgba(99,102,241,.12), rgba(59,130,246,.10)); border:1px solid var(--bg-accent)}
.motivation{display:flex; align-items:center; gap:12px}
.motivation-icon{width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--primary-gradient); color:#fff}
.mot-quote{font-weight:600; color:var(--text-primary)}
.mot-author{font-size:.9rem; color:var(--text-secondary); margin-top:2px}

/* Estilos para checkboxes HTML en comentarios */
.checkbox-container {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 4px 0 !important;
    line-height: 1.5 !important;
}

.checkbox-container input[type="checkbox"] {
    width: 16px !important;
    height: 16px !important;
    cursor: pointer !important;
    accent-color: #10b981 !important;
    margin: 0 !important;
    flex-shrink: 0 !important;
}

.checkbox-container span {
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
}

/* Hover effect para mejor UX */
.checkbox-container:hover {
    background-color: rgba(16, 185, 129, 0.05);
    border-radius: 4px;
    padding: 2px 4px;
    margin: 2px -4px;
}

/* Estilos para el scroll del modal de comentarios */
#subtask-comments-list {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#subtask-comments-list::-webkit-scrollbar {
    width: 8px;
}

#subtask-comments-list::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 4px;
}

#subtask-comments-list::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

#subtask-comments-list::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Mejorar espaciado de comentarios en el modal */
#subtask-comments-list .comment-item {
    margin-bottom: 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    background: #ffffff;
    transition: box-shadow 0.2s ease;
}

#subtask-comments-list .comment-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

#subtask-comments-list .comment-item:last-child {
    margin-bottom: 0;
}

/* Indicadores visuales para scroll */
#subtask-comments-list::before {
    content: '';
    position: sticky;
    top: 0;
    height: 2px;
    background: linear-gradient(to right, transparent, #3b82f6, transparent);
    margin-bottom: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

#subtask-comments-list::after {
    content: '';
    position: sticky;
    bottom: 0;
    height: 2px;
    background: linear-gradient(to right, transparent, #3b82f6, transparent);
    margin-top: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

#subtask-comments-list.has-scroll::before {
    opacity: var(--scroll-top-opacity, 0.2);
}

#subtask-comments-list.has-scroll::after {
    opacity: var(--scroll-bottom-opacity, 0.6);
}

/* Scroll suave */
#subtask-comments-list {
    scroll-behavior: smooth;
}

/* Efecto de fade en los bordes cuando hay scroll */
#subtask-comments-list.has-scroll {
    mask: 
        linear-gradient(to bottom, transparent 0%, black 8px, black calc(100% - 8px), transparent 100%);
    -webkit-mask: 
        linear-gradient(to bottom, transparent 0%, black 8px, black calc(100% - 8px), transparent 100%);
}

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
  max-width: 400px; /* Limitar ancho máximo */
  white-space: nowrap; /* No permitir saltos de línea */
  overflow: hidden; /* Ocultar texto que sobresale */
  text-overflow: ellipsis; /* Mostrar ... cuando el texto es muy largo */
  display: inline-block; /* Para que respete el max-width */
}

.subtask-actions {
  display: flex;
  gap: 5px;
}

/* Estilos para los colaboradores en chips */
.subtask-collaborators {
  margin-top: 8px;
}

.collaborators-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 4px;
}

.collaborator-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: #e0e7ff;
  border: 1px solid #c7d2fe;
  border-radius: 12px;
  font-size: 12px;
  color: #4338ca;
}

.collaborator-chip span {
  max-width: 150px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.remove-collaborator {
  background: none;
  border: none;
  color: #6366f1;
  cursor: pointer;
  padding: 0;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  transition: all 0.2s;
}

.remove-collaborator:hover {
  background: #4338ca;
  color: white;
}

.remove-collaborator i {
  font-size: 10px;
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

// Función para actualizar el progreso de una subtarea
function updateSubtaskProgress(subtaskId, newProgress) {
  console.log('Updating subtask progress:', subtaskId, newProgress);
  
  // Actualizar la UI inmediatamente
  const progressBar = document.querySelector(`.progress-bar[data-subtask-id="${subtaskId}"] .progress-fill`);
  const progressText = document.querySelector(`.progress-bar[data-subtask-id="${subtaskId}"] .progress-percentage`);
  
  if (progressBar) {
    progressBar.style.width = newProgress + '%';
  }
  if (progressText) {
    progressText.textContent = newProgress + '%';
  }
  
  // Enviar al servidor
  fetch('?route=clan_member/update-subtask-progress', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `subtask_id=${subtaskId}&completion_percentage=${newProgress}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification('Progreso actualizado', 'success');
    } else {
      showNotification('Error al actualizar progreso: ' + data.message, 'error');
      // Revertir cambios si hay error
      location.reload();
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error al actualizar progreso', 'error');
  });
}

// Función para actualizar el progreso haciendo click en la barra
function updateSubtaskProgressFromClick(event, subtaskId) {
  const progressBar = event.currentTarget;
  const rect = progressBar.getBoundingClientRect();
  const clickX = event.clientX - rect.left;
  const percentage = Math.round((clickX / rect.width) * 100);
  
  if (percentage >= 0 && percentage <= 100) {
    // Actualizar el slider también
    const slider = document.querySelector(`.subtask-progress-slider[data-subtask-id="${subtaskId}"]`);
    if (slider) {
      slider.value = percentage;
    }
    updateSubtaskProgress(subtaskId, percentage);
  }
}

// Funciones para subtareas
function updateSubtaskStatus(subtaskId, status) {
  // Verificar si el usuario está intentando cambiar a completado sin permisos
  if (status === 'completed') {
    const isTaskCreator = <?php echo json_encode((int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id']); ?>;
    const isRecurrentTask = <?php echo json_encode((bool)($task['is_recurrent'] ?? false)); ?>;
    const isRecurrentProject = <?php echo json_encode(in_array($project['project_type'] ?? '', ['recurrent', 'eventual'])); ?>;
    const canComplete = <?php echo json_encode($canEdit); ?> || isTaskCreator || isRecurrentTask || isRecurrentProject;
    
    if (!canComplete) {
      alert('Solo el creador/asignado de la tarea o miembros en tareas recurrentes/eventuales pueden marcar subtareas como completadas');
      // Revertir el select al estado anterior
      const statusSelect = document.querySelector(`[data-subtask-id="${subtaskId}"] .status-select`);
      if (statusSelect) {
        // Buscar el estado anterior en las opciones
        for (let i = 0; i < statusSelect.options.length; i++) {
          if (statusSelect.options[i].value !== 'completed') {
            statusSelect.selectedIndex = i;
            break;
          }
        }
      }
      return;
    }
  }
  
  // NO vincular el estado con el porcentaje - se manejan independientemente
  const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
  
  // Preparar el cuerpo de la petición (sin enviar porcentaje)
  let requestBody = `subtask_id=${subtaskId}&status=${status}`;
  
  fetch('?route=clan_member/update-subtask-status', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: requestBody
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
        
        // NO actualizar la barra de progreso (se maneja independientemente)
        
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
    console.log('=== EDIT SUBTASK DEBUG ===');
    console.log('Subtask ID:', subtaskId);
    
    // Obtener datos actuales de la subtarea
    const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
    console.log('Subtask element found:', !!subtaskElement);
    
    if (!subtaskElement) {
        console.error('No se encontró el elemento de subtarea con ID:', subtaskId);
        return;
    }
    
    const currentTitle = subtaskElement.querySelector('.subtask-title').textContent.trim();
    const currentDescription = subtaskElement.querySelector('.subtask-description')?.textContent.trim() || '';
    
    // Obtener estado actual
    const statusSelect = subtaskElement.querySelector('.status-select');
    const currentStatus = statusSelect ? statusSelect.value : 'pending';
    console.log('Current status:', currentStatus);
    
    // Obtener progreso actual
    const progressElement = subtaskElement.querySelector('.progress-percentage');
    const progressText = progressElement ? progressElement.textContent : '0%';
    const currentProgress = parseInt(progressText.replace('%', '')) || 0;
    console.log('Progress element found:', !!progressElement);
    console.log('Current progress:', currentProgress);
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Editar Subtarea (Nuevo Modal v2.0)</h3>
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
                    <div class="form-group">
                        <label for="edit-subtask-status">Estado:</label>
                        <select id="edit-subtask-status" onchange="toggleProgressBar()" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-bottom: 15px;">
                            <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>Pendiente</option>
                            <option value="in_progress" ${currentStatus === 'in_progress' ? 'selected' : ''}>En Progreso</option>
                        </select>
                        <div id="completion-restriction-message" style="font-size: 12px; color: #6b7280; margin-top: 5px; display: none;">
                            <i class="fas fa-info-circle"></i> Solo el creador/asignado o tareas recurrentes/eventuales pueden completarse
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit-subtask-progress">Progreso:</label>
                        <div class="progress-control-container" style="margin-bottom: 15px;">
                            <div class="progress-bar-edit" onclick="updateProgressFromClick(event)" style="position: relative; width: 100%; height: 24px; background: #f3f4f6; border-radius: 12px; border: 2px solid #e5e7eb; cursor: pointer; margin-bottom: 8px;">
                                <div class="progress-fill-edit" id="progressFillEdit" style="height: 100%; background: linear-gradient(90deg, #10b981, #22c55e); border-radius: 10px; width: ${currentProgress}%; transition: width 0.3s ease;"></div>
                                <span class="progress-text-edit" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 12px; font-weight: 600; color: #374151;">${currentProgress}%</span>
                            </div>
                            <input type="range" id="edit-subtask-progress" min="0" max="100" value="${currentProgress}" oninput="updateProgressDisplay(this.value)" style="width: 100%;">
                        </div>
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
    
    console.log('Modal HTML agregado al DOM');
    console.log('Verificando elementos del modal:');
    console.log('Estado select:', !!document.getElementById('edit-subtask-status'));
    console.log('Progress container:', !!document.querySelector('.progress-control-container'));
    console.log('Progress bar:', !!document.querySelector('.progress-bar-edit'));
    
    // Verificar permisos para agregar opción de completado dinámicamente
    setTimeout(() => {
        const statusSelect = document.getElementById('edit-subtask-status');
        const messageDiv = document.getElementById('completion-restriction-message');
        
        // Obtener información de permisos del usuario (desde PHP)
        const isTaskCreator = <?php echo json_encode((int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id']); ?>;
        const isRecurrentTask = <?php echo json_encode((bool)($task['is_recurrent'] ?? false)); ?>;
        const isRecurrentProject = <?php echo json_encode(in_array($project['project_type'] ?? '', ['recurrent', 'eventual'])); ?>;
        const canComplete = <?php echo json_encode($canEdit); ?> || isTaskCreator || isRecurrentTask || isRecurrentProject;
        
        if (canComplete) {
            // Agregar opción de completado si tiene permisos
            const completedOption = document.createElement('option');
            completedOption.value = 'completed';
            completedOption.textContent = 'Completado';
            if (currentStatus === 'completed') {
                completedOption.selected = true;
            }
            statusSelect.appendChild(completedOption);
        } else {
            // Mostrar mensaje informativo si no tiene permisos
            messageDiv.style.display = 'block';
        }
    
    // Configurar estado inicial de la barra de progreso
    toggleProgressBar();
    }, 100);
    
    document.getElementById('edit-subtask-title').focus();
}

// Función para habilitar/deshabilitar la barra de progreso según el estado
function toggleProgressBar() {
    // NO vincular el estado con el porcentaje - función simplificada
    // Esta función ya no es necesaria pero la mantenemos por compatibilidad
}

// Función para actualizar el progreso desde el click en la barra
function updateProgressFromClick(event) {
    const status = document.getElementById('edit-subtask-status').value;
    if (status === 'pending') return; // No permitir cambios si está pendiente
    
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const clickX = event.clientX - rect.left;
    const percentage = Math.round((clickX / rect.width) * 100);
    
    if (percentage >= 0 && percentage <= 100) {
        updateProgressDisplay(percentage);
        document.getElementById('edit-subtask-progress').value = percentage;
    }
}

// Función para actualizar la visualización del progreso
function updateProgressDisplay(percentage) {
    const progressFill = document.getElementById('progressFillEdit');
    const progressText = document.querySelector('.progress-text-edit');
    
    if (progressFill) progressFill.style.width = percentage + '%';
    if (progressText) progressText.textContent = percentage + '%';
}

function saveSubtaskChanges(subtaskId) {
    const title = document.getElementById('edit-subtask-title').value.trim();
    const description = document.getElementById('edit-subtask-description').value.trim();
    const status = document.getElementById('edit-subtask-status').value;
    const progress = parseInt(document.getElementById('edit-subtask-progress').value);
    
    if (!title) {
        alert('El título es requerido');
        return;
    }
    
    // Preparar el objeto de datos (estado y porcentaje independientes)
    const requestData = {
        subtask_id: subtaskId,
        title: title,
        description: description,
        status: status,
        completion_percentage: progress // usar el valor del slider tal cual
    };
    
    fetch('?route=clan_member/edit-subtask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar en la página sin recargar
            const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            subtaskElement.querySelector('.subtask-title').textContent = title;
            
            // Actualizar descripción
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
            
            // Actualizar estado en la meta
            const statusSpan = subtaskElement.querySelector('.subtask-meta span:first-child');
            if (statusSpan) {
                statusSpan.textContent = `Estado: ${status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')}`;
            }
            
            // Actualizar progreso con el valor del slider
            const progressFill = subtaskElement.querySelector('.progress-fill');
            const progressPercentage = subtaskElement.querySelector('.progress-percentage');
            
            if (progressFill) progressFill.style.width = progress + '%';
            if (progressPercentage) progressPercentage.textContent = progress + '%';
            
            // Actualizar el select de estado
            const statusSelect = subtaskElement.querySelector('.status-select');
            if (statusSelect) statusSelect.value = status;
            
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
  // Prevenir recursión infinita
  if (window._showingNotification) {
    console.warn('showNotification: Evitando recursión infinita');
    return;
  }
  window._showingNotification = true;
  
  try {
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
    }, 5000);
  } finally {
    // Resetear la bandera para permitir futuras notificaciones
    window._showingNotification = false;
  }
}

function closeCMNotification() {
  const notification = document.getElementById('cm-notification');
  if (notification) {
    notification.style.display = 'none';
  }
}
</script>

<!-- Modal editar tarea -->
<?php if ($canEditTask): ?>
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
            <option value="critical" <?php echo ($task['priority']==='critical')?'selected':''; ?>>Urgente</option>
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
  
  // Debug: Log form data
  console.log("=== ACTUALIZACIÓN DE TAREA ===");
  for (let [key, value] of fd.entries()) {
    console.log(`${key}: ${value}`);
  }
  
  fetch('?route=clan_member/update-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r => { 
      console.log("Response status:", r.status);
      const t = await r.text(); 
      console.log("Response text:", t);
      try{ 
        return JSON.parse(t); 
      } catch(e){ 
        console.error("JSON parse error:", e);
        console.error("Raw response:", t);
        return {success:false,message:'Respuesta inválida'}; 
      } 
    })
    .then(d => {
      console.log("Parsed response:", d);
      if(!d.success){
        errorBox.style.display='block'; 
        errorBox.textContent=d.message||'No se pudo guardar'; 
        btn.classList.remove('is-loading'); 
        return;
      }
      // Siempre regresar al listado después de guardar
      window.location.href='?route=clan_member/tasks';
    })
    .catch(error => {
      console.error("Fetch error:", error);
      errorBox.style.display='block'; 
      errorBox.textContent='Error de red: ' + error.message; 
      btn.classList.remove('is-loading'); 
    });
});

// Ya no auto-abrimos el panel al cargar, solo al presionar "Editar Tarea"

// Funciones para comentarios y adjuntos de subtareas
function showSubtaskComments(subtaskId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.id = 'subtaskCommentsModal'; // Agregar ID para detección de checkboxes
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px; max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h3><i class="fas fa-comments"></i> Comentarios de Subtarea</h3>
                <button class="btn-close" onclick="this.closest('.modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
                <div id="subtask-comments-list" style="flex: 1; overflow-y: auto; max-height: 400px; margin-bottom: 20px; padding-right: 8px;">
                    <div class="loading">Cargando comentarios...</div>
                </div>
                <div class="add-comment-section" style="flex-shrink: 0; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <h4>Agregar Comentario</h4>
                    <div class="comment-form">
                        <div class="rich-editor-container" style="position: relative;">
                            <div id="subtask-comment-editor-${subtaskId}" style="margin-bottom: 10px;"></div>
                            <button type="button" class="emoji-picker-btn" onclick="toggleEmojiPicker('subtask-comment-${subtaskId}')" title="Agregar emoji">
                                <i class="fas fa-smile"></i>
                            </button>
                            <div id="subtask-comment-${subtaskId}-emoji-picker" class="emoji-picker-container" style="display: none;"></div>
                        </div>
                        <input type="hidden" id="subtask-comment-content-${subtaskId}" />
                        <div id="file-preview-${subtaskId}" class="file-preview" style="display: none; margin: 10px 0; padding: 10px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-file" style="color: #0ea5e9;"></i>
                                    <span id="file-name-${subtaskId}" style="color: #0369a1; font-size: 14px;"></span>
                                    <span id="file-size-${subtaskId}" style="color: #64748b; font-size: 12px;"></span>
                                </div>
                                <button type="button" onclick="removeAttachment(${subtaskId})" class="btn-icon-small" style="background: #fee2e2; color: #dc2626;" title="Quitar archivo">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="comment-actions">
                            <input type="file" id="subtask-comment-file-${subtaskId}" style="display: none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif" onchange="showFilePreview(${subtaskId})">
                            <button type="button" onclick="document.getElementById('subtask-comment-file-${subtaskId}').click()" class="btn btn-secondary">
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
    
    // Inicializar editor de comentarios para esta subtarea
    setTimeout(() => {
        console.log('Intentando inicializar editor después de crear modal');
            initializeSubtaskCommentEditor(subtaskId);
    }, 500);
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

// Mostrar vista previa del archivo adjunto
function showFilePreview(subtaskId) {
    const fileInput = document.getElementById(`subtask-comment-file-${subtaskId}`);
    const filePreview = document.getElementById(`file-preview-${subtaskId}`);
    const fileName = document.getElementById(`file-name-${subtaskId}`);
    const fileSize = document.getElementById(`file-size-${subtaskId}`);
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        fileName.textContent = file.name;
        
        // Formatear tamaño del archivo
        const sizeInKB = (file.size / 1024).toFixed(2);
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        fileSize.textContent = file.size > 1024 * 1024 ? `(${sizeInMB} MB)` : `(${sizeInKB} KB)`;
        
        filePreview.style.display = 'block';
    }
}

// Quitar archivo adjunto
function removeAttachment(subtaskId) {
    const fileInput = document.getElementById(`subtask-comment-file-${subtaskId}`);
    const filePreview = document.getElementById(`file-preview-${subtaskId}`);
    
    if (fileInput) {
        fileInput.value = '';
    }
    if (filePreview) {
        filePreview.style.display = 'none';
    }
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
                        <div class="comment-item" data-comment-id="${comment.comment_id}">
                            ${comment.is_attachment_only ? `
                                <div class="comment-header">
                                    <strong><i class="fas fa-paperclip"></i> ${comment.full_name}</strong>
                                </div>
                            ` : `
                                <div class="comment-header">
                                    <strong>${comment.full_name}</strong>
                                    <span class="comment-date">${new Date(comment.created_at).toLocaleString()}</span>
                                </div>
                                <div class="comment-content comment-text">${comment.comment_text}</div>
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
                    
                    // Procesar checkboxes en los comentarios de subtarea cargados
                    setTimeout(() => {
                        console.log('=== PROCESANDO CHECKBOXES EN SUBTAREAS ===');
                        const subtaskComments = container.querySelectorAll('.comment-item .comment-content');
                        console.log('Comentarios encontrados:', subtaskComments.length);
                        
                        subtaskComments.forEach((element, index) => {
                            console.log(`Procesando comentario ${index + 1}:`, element);
                            const commentItem = element.closest('.comment-item');
                            console.log('Comment item:', commentItem);
                            console.log('Comment ID:', commentItem?.dataset.commentId);
                            makeChecklistInteractive(element);
                        });
                        
                        // Configurar scroll mejorado
                        setupScrollEnhancements(container, data.comments.length);
                        
                        // Auto-scroll al final si hay muchos comentarios
                        if (data.comments.length > 3) {
                            container.scrollTop = container.scrollHeight;
                        }
                    }, 200); // Aumentar timeout para asegurar que el DOM esté listo
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
                    container.innerHTML = data.attachments.map(att => {
                        const isImage = att.file_type && att.file_type.startsWith('image/');
                        const isPdf = att.file_type === 'application/pdf';
                        const isViewable = isImage || isPdf || (att.file_type && att.file_type.startsWith('text/'));
                        
                        return `
                            <div class="attachment-item">
                                <div class="attachment-info">
                                    <div class="attachment-header">
                                        <div class="attachment-icon">
                                            ${isImage ? '<i class="fas fa-image" style="color: #10b981;"></i>' : 
                                              isPdf ? '<i class="fas fa-file-pdf" style="color: #ef4444;"></i>' : 
                                              '<i class="fas fa-file" style="color: #6b7280;"></i>'}
                                        </div>
                                        <div class="attachment-name">
                                            <strong>${att.file_name}</strong>
                                            ${isViewable ? '<span class="viewable-badge">Vista previa disponible</span>' : ''}
                                        </div>
                                    </div>
                                    <div class="attachment-meta">
                                        <span>Subido por: ${att.uploaded_by_name}</span>
                                        <span>Fecha: ${new Date(att.uploaded_at).toLocaleString()}</span>
                                        ${att.file_size ? `<span>Tamaño: ${formatFileSize(att.file_size)}</span>` : ''}
                                    </div>
                                    ${att.description ? `<div class="attachment-description">${att.description}</div>` : ''}
                                    <div class="attachment-actions">
                                        <button onclick="openFileViewer(${att.attachment_id})" class="btn-view">
                                            <i class="fas fa-eye"></i> ${isViewable ? 'Ver' : 'Descargar'}
                                        </button>
                                        <button onclick="downloadFile(${att.attachment_id}, '${att.file_name}')" class="btn-download">
                                            <i class="fas fa-download"></i> Descargar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
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
    console.log('=== DEBUG: addSubtaskComment llamada ===');
    console.log('Subtask ID:', subtaskId);
    
    let commentText = '';
    
    // Obtener contenido del editor Quill de la subtarea
    const subtaskEditor = window[`subtaskCommentEditor_${subtaskId}`];
    console.log('Editor encontrado:', !!subtaskEditor);
    
    if (subtaskEditor) {
        const editorContent = subtaskEditor.getContents();
        const htmlContent = subtaskEditor.root.innerHTML;
        
        console.log('Editor content:', editorContent);
        console.log('HTML content:', htmlContent);
        
        // Validar que hay contenido (más flexible)
        const textContent = subtaskEditor.getText().trim();
        if (!textContent || textContent.length === 0) {
            alert('Por favor escribe un comentario');
            return;
        }
        
        commentText = htmlContent;
    } else {
        // Fallback para textarea normal (si el editor no se inicializó)
        console.log('Editor no encontrado, buscando textarea fallback...');
        const textareaFallback = document.getElementById('subtask-comment-text');
        if (textareaFallback) {
            commentText = textareaFallback.value.trim();
            if (!commentText) {
                alert('Por favor escribe un comentario');
                return;
            }
        } else {
            alert('Error: No se pudo obtener el contenido del comentario. Editor no inicializado.');
            console.error('Ni editor Quill ni textarea encontrados');
            return;
        }
    }
    
    console.log('Texto del comentario obtenido:', commentText);
    
    const fileInput = document.getElementById(`subtask-comment-file-${subtaskId}`);

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
    console.log('=== DEBUG: addSubtaskCommentWithText ===');
    console.log('Subtask ID:', subtaskId);
    console.log('Comment Text:', commentText);
    console.log('Attachment ID:', attachmentId);
    
    const requestData = {
        subtask_id: subtaskId,
        comment_text: commentText,
        attachment_id: attachmentId
    };
    
    console.log('Request data:', requestData);
    
    fetch('?route=clan_member/add-subtask-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text(); // Primero obtener como texto
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed response:', data);
            
            if (data.success) {
                console.log('Comentario agregado exitosamente');
                
                // Limpiar editor Quill o textarea fallback
                const subtaskEditor = window[`subtaskCommentEditor_${subtaskId}`];
                if (subtaskEditor) {
                    subtaskEditor.setContents([]);
                } else {
                    const textareaFallback = document.getElementById('subtask-comment-text');
                    if (textareaFallback) {
                        textareaFallback.value = '';
                    }
                }
                
                const fileInputToClean = document.getElementById(`subtask-comment-file-${subtaskId}`);
                if (fileInputToClean) {
                    fileInputToClean.value = '';
                }
                
                // Limpiar vista previa del archivo
                const filePreview = document.getElementById(`file-preview-${subtaskId}`);
                if (filePreview) {
                    filePreview.style.display = 'none';
                }
                
                // Recargar comentarios y hacer scroll al final
                loadSubtaskComments(subtaskId);
                loadSubtaskCounts(subtaskId); // Actualizar conteos
                
                // Hacer scroll al final después de recargar comentarios
                setTimeout(() => {
                    const container = document.getElementById('subtask-comments-list');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                        
                        // Reconfigurar mejoras de scroll después de agregar comentario
                        setupScrollEnhancements(container, container.querySelectorAll('.comment-item').length);
                    }
                }, 200);
                
                alert('Comentario agregado exitosamente');
            } else {
                console.error('Error del servidor:', data.message);
                alert('Error al agregar comentario: ' + data.message);
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            console.error('Raw response:', text);
            alert('Error: Respuesta inválida del servidor');
            }
        })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error de conexión: ' + error.message);
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
    
    // Mostrar indicador de carga
    const uploadBtn = event.target;
    const originalText = uploadBtn.innerHTML;
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
    
    fetch('?route=clan_member/upload-subtask-attachment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Restaurar botón
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = originalText;
        
        if (data.success) {
            // ABRIR VISTA PREVIA INMEDIATAMENTE
            if (data.attachment_id) {
                openFileViewer(data.attachment_id);
            }
            
            // Limpiar formulario
            fileInput.value = '';
            document.getElementById('subtask-attachment-description').value = '';
            
            // Actualizar lista de adjuntos en background
            loadSubtaskAttachments(subtaskId);
            loadSubtaskCounts(subtaskId);
        } else {
            alert('Error al subir archivo: ' + data.message);
        }
    })
    .catch(error => {
        // Restaurar botón en caso de error
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = originalText;
        alert('Error de conexión');
    });
}

    // Función para descargar archivos
    function downloadFile(attachmentId, filename) {
        const link = document.createElement('a');
        link.href = `file-viewer.php?id=${attachmentId}&action=download`;
        link.download = filename;
        link.click();
    }

// Función ULTRA SIMPLE - solo abre en nueva pestaña
window.openFileViewer = function(attachmentId) {
    window.open(`file-viewer.php?id=${attachmentId}`, '_blank');
};

// NO MÁS VISOR COMPLEJO - ELIMINADO COMPLETAMENTE

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
            console.log('Counts for subtask ' + subtaskId + ':', data);
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
        // Usar comment_count (sin s) que es lo que devuelve el modelo
        const commentCount = counts.comment_count || counts.comments_count || 0;
        if (commentCount > 0) {
            commentsBadge.textContent = commentCount;
            commentsBadge.style.display = 'flex';
        } else {
            commentsBadge.style.display = 'none';
        }
    }
    
    if (attachmentsBadge) {
        // Usar attachment_count (sin s) que es lo que devuelve el modelo
        const attachmentCount = counts.attachment_count || counts.attachments_count || 0;
        if (attachmentCount > 0) {
            attachmentsBadge.textContent = attachmentCount;
            attachmentsBadge.style.display = 'flex';
        } else {
            attachmentsBadge.style.display = 'none';
        }
    }
}

// Cargar conteos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadAllSubtaskCounts();
});

// Funciones para actualizar el progreso de la tarea principal
function updateTaskProgress(event) {
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const clickX = event.clientX - rect.left;
    const percentage = Math.round((clickX / rect.width) * 100);
    
    if (percentage >= 0 && percentage <= 100) {
        updateTaskProgressValue(percentage);
    }
}

function updateTaskProgressFromSlider(percentage) {
    updateTaskProgressValue(parseInt(percentage));
}

function updateTaskProgressValue(percentage) {
    // Actualizar UI inmediatamente
    const progressFill = document.querySelector('.task-progress-fill');
    const progressText = document.querySelector('.task-progress-text');
    const slider = document.getElementById('taskProgressSlider');
    
    if (progressFill) progressFill.style.width = percentage + '%';
    if (progressText) progressText.textContent = percentage + '%';
    if (slider) slider.value = percentage;
    
    // Enviar actualización al servidor
    const taskId = <?php echo (int)$task['task_id']; ?>;
    
    fetch('?route=clan_member/update-task-progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&completion_percentage=${percentage}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Progreso actualizado correctamente', 'success');
        } else {
            // Revertir cambios en caso de error
            const originalProgress = <?php echo $currentProgress; ?>;
            if (progressFill) progressFill.style.width = originalProgress + '%';
            if (progressText) progressText.textContent = originalProgress + '%';
            if (slider) slider.value = originalProgress;
            
            showNotification('Error al actualizar progreso: ' + data.message, 'error');
        }
    })
    .catch(error => {
        // Revertir cambios en caso de error
        const originalProgress = <?php echo $currentProgress; ?>;
        if (progressFill) progressFill.style.width = originalProgress + '%';
        if (progressText) progressText.textContent = originalProgress + '%';
        if (slider) slider.value = originalProgress;
        
        console.error('Error:', error);
        showNotification('Error de conexión al actualizar progreso', 'error');
    });
}

// Función para descargar historial de tarea en Excel
function downloadTaskHistory(taskId) {
    console.log('Descargando historial de tarea:', taskId);
    
    // Mostrar indicador de carga
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    button.disabled = true;
    
    // Crear enlace de descarga
    const downloadUrl = `?route=clan_member/export-task-history&task_id=${taskId}`;
    
    fetch(downloadUrl, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.blob();
    })
    .then(blob => {
        // Crear URL del blob y descargar
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `historial_tarea_${taskId}_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showNotification('Historial descargado exitosamente', 'success');
    })
    .catch(error => {
        console.error('Error descargando historial:', error);
        showNotification('Error al descargar historial: ' + error.message, 'error');
    })
    .finally(() => {
        // Restaurar botón
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}
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
    border-radius: 10px;
    min-width: 20px;
    height: 20px;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    padding: 0 6px;
    box-sizing: border-box;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    animation: badgePulse 0.3s ease-out;
}

@keyframes badgePulse {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Colores específicos para cada tipo de badge */
.btn-with-badge[id*="comments-btn"] .badge {
    background: #3b82f6;
}

.btn-with-badge[id*="attachments-btn"] .badge {
    background: #10b981;
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

/* Estilos para adjuntos mejorados */
.attachment-item {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    background: white;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    border-color: #1e3a8a;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
}

.attachment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.attachment-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 8px;
    font-size: 1.2rem;
}

.attachment-name {
    flex: 1;
}

.attachment-name strong {
    display: block;
    color: #1f2937;
    font-size: 1rem;
    margin-bottom: 2px;
}

.viewable-badge {
    display: inline-block;
    background: #d1fae5;
    color: #065f46;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}

.attachment-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 8px;
}

.attachment-description {
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #374151;
    margin-bottom: 8px;
    border-left: 3px solid #1e3a8a;
}

.attachment-actions {
    display: flex;
    gap: 8px;
}

.btn-view, .btn-download {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-view {
    background: #1e3a8a;
    color: white;
}

.btn-view:hover {
    background: #1e40af;
    transform: translateY(-1px);
}

.btn-download {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-download:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .attachment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .attachment-meta {
        flex-direction: column;
        gap: 4px;
    }
    
    .attachment-actions {
        width: 100%;
    }
    
    .btn-view, .btn-download {
        flex: 1;
        justify-content: center;
    }
}

/* Estilos para la barra de progreso de la tarea principal */
.task-progress-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.task-progress-bar {
    position: relative;
    width: 100%;
    height: 24px;
    background: #f3f4f6;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease;
}

.task-progress-bar:hover {
    border-color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.task-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #22c55e);
    transition: width 0.3s ease;
    border-radius: 8px;
}

.task-progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
}

.task-progress-readonly .task-progress-bar {
    cursor: default;
    opacity: 0.7;
}

.task-progress-readonly .task-progress-bar:hover {
    border-color: #e5e7eb;
    transform: none;
    box-shadow: none;
}

/* Estilos para el slider */
#taskProgressSlider {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    outline: none;
}

#taskProgressSlider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #1e3a8a;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
}

#taskProgressSlider::-webkit-slider-thumb:hover {
    background: #1e40af;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

#taskProgressSlider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #1e3a8a;
    border-radius: 50%;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

#taskProgressSlider::-moz-range-thumb:hover {
    background: #1e40af;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}
</style>

<script>
// Función preventiva para evitar etiquetas incorrectas en vista de miembro de clan
function preventIncorrectLabels() {
    // Remover cualquier elemento con clase 'labels-list' o 'label' que pueda aparecer incorrectamente
    const labelElements = document.querySelectorAll('.labels-list, .label, [class*="label"], [class*="etiqueta"]');
    labelElements.forEach(element => {
        // Solo remover si contiene la palabra "Dirección" o similar
        if (element.textContent && element.textContent.includes('Dirección')) {
            console.warn('Removiendo etiqueta incorrecta:', element.textContent);
            element.remove();
        }
    });
    
    // Remover cualquier sección de etiquetas que pueda aparecer
    const labelSections = document.querySelectorAll('h3');
    labelSections.forEach(section => {
        if (section.textContent && (section.textContent.includes('Etiquetas') || section.textContent.includes('Tags'))) {
            console.warn('Removiendo sección de etiquetas incorrecta');
            section.closest('.summary-card')?.remove();
        }
    });
}

// Función para mostrar modal de agregar subtarea
function showAddSubtaskModal() {
    closeExistingModals();
    
    const modalHTML = `
        <div class="modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
            <div class="modal-content" style="background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 600;">Agregar Nueva Subtarea</h3>
                    <button onclick="closeExistingModals()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
                </div>
                
                <form id="new-subtask-form">
                    <div style="margin-bottom: 15px;">
                        <label for="new-subtask-title" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Título *</label>
                        <input type="text" id="new-subtask-title" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s;" placeholder="Ingresa el título de la subtarea">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="new-subtask-description" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Descripción</label>
                        <textarea id="new-subtask-description" rows="3" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s; resize: vertical;" placeholder="Descripción opcional"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label for="new-subtask-status" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Estado</label>
                            <select id="new-subtask-status" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; outline: none;">
                                <option value="pending">Pendiente</option>
                                <option value="in_progress">En Progreso</option>
                                <option value="completed">Completada</option>
                                <option value="blocked">Bloqueada</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="new-subtask-percentage" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Progreso (%)</label>
                            <input type="number" id="new-subtask-percentage" min="0" max="100" value="0" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; outline: none;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="new-subtask-due-date" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Fecha límite</label>
                        <input type="date" id="new-subtask-due-date" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; outline: none;">
                    </div>
                    
                    <!-- NOTA: Las etiquetas NO están disponibles para miembros de clan -->
                    <!-- Solo los líderes de clan pueden gestionar etiquetas -->
                    
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="closeExistingModals()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Cancelar
                        </button>
                        <button type="button" onclick="saveNewSubtaskForMember()" style="background: #1e3a8a; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Crear Subtarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Focus en el campo título después de un pequeño delay
    setTimeout(() => {
        document.getElementById('new-subtask-title').focus();
    }, 100);
    
    // Ejecutar función preventiva
    preventIncorrectLabels();
}

// Función para guardar nueva subtarea (específica para miembros de clan)
function saveNewSubtaskForMember() {
    const title = document.getElementById('new-subtask-title').value.trim();
    const description = document.getElementById('new-subtask-description').value.trim();
    const status = document.getElementById('new-subtask-status').value;
    const percentage = parseInt(document.getElementById('new-subtask-percentage').value);
    const dueDate = document.getElementById('new-subtask-due-date').value;
    
    if (!title) {
        alert('El título es requerido');
        return;
    }
    
    const formData = new FormData();
    formData.append('task_id', '<?php echo $task['task_id']; ?>');
    formData.append('title', title);
    formData.append('description', description);
    formData.append('status', status);
    formData.append('completion_percentage', percentage);
    if (dueDate) {
        formData.append('due_date', dueDate);
    }
    
    fetch('?route=clan_member/add-subtask', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeExistingModals();
            alert('Subtarea creada exitosamente');
            // Ejecutar función preventiva antes de recargar
            preventIncorrectLabels();
            // Recargar la página para mostrar la nueva subtarea
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Error al crear subtarea: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

// Función auxiliar para cerrar modales existentes
function closeExistingModals() {
    const existingModals = document.querySelectorAll('.modal-overlay');
    existingModals.forEach(modal => modal.remove());
}

// Variables globales para editores
let taskCommentEditor = null;

// Función para inicializar editor de comentario de tarea principal
function initializeTaskCommentEditor() {
    const editorElement = document.getElementById('task-comment-editor');
    
    console.log('=== DEBUG: Inicializando editor de tarea principal ===');
    console.log('Elemento encontrado:', !!editorElement);
    console.log('Quill disponible:', typeof Quill !== 'undefined');
    
    if (!editorElement) {
        console.error('Elemento task-comment-editor no encontrado');
        return;
    }
    
    if (taskCommentEditor) {
        console.log('Editor de tarea ya existe');
        return;
    }
    
    if (typeof Quill === 'undefined') {
        console.error('Quill no está disponible, creando textarea fallback');
        editorElement.innerHTML = '<textarea name="comment_text" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="Escribe tu comentario..."></textarea>';
        return;
    }
    
    try {
        taskCommentEditor = new Quill('#task-comment-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['checklist-btn'], // Botón personalizado para checklist
                        [{ 'color': [] }, { 'background': [] }],
                        ['link'],
                        ['clean']
                    ],
                    handlers: {
                        'checklist-btn': function() {
                            insertChecklist(taskCommentEditor);
                        }
                    }
                }
            },
            placeholder: 'Escribe tu comentario...\n\nTip: Usa "???" + espacio para crear checklist rápido'
        });
        
        // Agregar el botón personalizado al toolbar
        addChecklistButton(taskCommentEditor, 'task');
        
        // Agregar detector de atajo ???
        setupChecklistShortcut(taskCommentEditor);
        
        console.log('✅ Editor de tarea principal inicializado exitosamente');
        
        // Verificar que el editor funciona
        setTimeout(() => {
            if (taskCommentEditor && taskCommentEditor.root) {
                console.log('✅ Editor de tarea principal verificado');
            } else {
                console.error('❌ Editor de tarea principal no funciona');
            }
        }, 100);
        
    } catch (error) {
        console.error('Error inicializando editor de tarea principal:', error);
        // Fallback a textarea
        editorElement.innerHTML = '<textarea name="comment_text" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="Escribe tu comentario..."></textarea>';
    }
}

// Función para inicializar editor de comentario de subtarea
function initializeSubtaskCommentEditor(subtaskId) {
    const editorId = `subtask-comment-editor-${subtaskId}`;
    const editorElement = document.getElementById(editorId);
    
    console.log(`=== DEBUG: Inicializando editor para subtarea ${subtaskId} ===`);
    console.log(`Elemento encontrado:`, editorElement);
    console.log(`Quill disponible:`, typeof Quill !== 'undefined');
    
    if (!editorElement) {
        console.error(`Elemento ${editorId} no encontrado`);
        return;
    }
    
    if (window[`subtaskCommentEditor_${subtaskId}`]) {
        console.log(`Editor ya existe para subtarea ${subtaskId}`);
        return;
    }
    
    if (typeof Quill === 'undefined') {
        console.error('Quill no está disponible, creando textarea fallback');
        editorElement.innerHTML = `<textarea id="subtask-comment-text" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="Escribe tu comentario..."></textarea>`;
        return;
    }
    
    try {
        window[`subtaskCommentEditor_${subtaskId}`] = new Quill(`#${editorId}`, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['checklist-btn'], // Botón personalizado para checklist
                        [{ 'color': [] }, { 'background': [] }],
                        ['link'],
                        ['clean']
                    ],
                    handlers: {
                        'checklist-btn': function() {
                            insertChecklist(window[`subtaskCommentEditor_${subtaskId}`]);
                        }
                    }
                }
            },
            placeholder: 'Escribe tu comentario...\n\nTip: Usa "???" + espacio para crear checklist rápido'
        });
        
        // Agregar el botón personalizado al toolbar
        addChecklistButton(window[`subtaskCommentEditor_${subtaskId}`], 'subtask');
        
        // Agregar detector de atajo ???
        setupChecklistShortcut(window[`subtaskCommentEditor_${subtaskId}`]);
        
        console.log(`✅ Editor de subtarea ${subtaskId} inicializado exitosamente`);
        
        // Verificar que el editor funciona
        setTimeout(() => {
            const editor = window[`subtaskCommentEditor_${subtaskId}`];
            if (editor && editor.root) {
                console.log(`✅ Editor verificado para subtarea ${subtaskId}`);
            } else {
                console.error(`❌ Editor no funciona para subtarea ${subtaskId}`);
            }
        }, 100);
        
    } catch (error) {
        console.error(`Error inicializando editor para subtarea ${subtaskId}:`, error);
        // Fallback a textarea
        editorElement.innerHTML = `<textarea id="subtask-comment-text" rows="4" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;" placeholder="Escribe tu comentario..."></textarea>`;
    }
}

// Función para agregar botón de checklist personalizado
function addChecklistButton(editor, type) {
    const toolbar = editor.getModule('toolbar');
    const toolbarElement = toolbar.container;
    
    // Buscar el botón checklist-btn y reemplazarlo
    const checklistBtn = toolbarElement.querySelector('.ql-checklist-btn');
    if (checklistBtn) {
        checklistBtn.innerHTML = '<i class="fas fa-tasks"></i>';
        checklistBtn.title = 'Insertar Lista de Tareas';
        checklistBtn.style.cssText = `
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        `;
        
        checklistBtn.addEventListener('mouseenter', () => {
            checklistBtn.style.background = '#e5e7eb';
        });
        
        checklistBtn.addEventListener('mouseleave', () => {
            checklistBtn.style.background = '#f3f4f6';
        });
    }
}

// Función para configurar atajo de teclado ???
function setupChecklistShortcut(editor) {
    editor.keyboard.addBinding({
        key: ' ',
        prefix: /^\?\?\?$/
    }, function(range, context) {
        // Eliminar el ??? y agregar checklist
        editor.deleteText(range.index - 3, 3);
        insertChecklist(editor);
    });
}

// Función para insertar checklist
function insertChecklist(editor) {
    const range = editor.getSelection(true);
    const checklistHTML = `
        <p>☐ Tarea 1</p>
        <p>☐ Tarea 2</p>
        <p>☐ Tarea 3</p>
    `;
    
    editor.clipboard.dangerouslyPasteHTML(range.index, checklistHTML);
    editor.setSelection(range.index + 2);
}

// Función para esperar a que Quill esté disponible
function waitForQuill(callback, maxAttempts = 20) {
    let attempts = 0;
    
    function check() {
        attempts++;
        if (typeof Quill !== 'undefined') {
            callback();
        } else if (attempts < maxAttempts) {
            setTimeout(check, 100);
        } else {
            console.error('Quill no se pudo cargar después de', maxAttempts, 'intentos');
            // Fallback: mostrar textarea normal
            const editorContainer = document.getElementById('task-comment-editor');
            if (editorContainer) {
                editorContainer.innerHTML = '<textarea name="comment_text" placeholder="Escribe un comentario..." style="width: 100%; height: 120px; border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; font-size: 14px; resize: vertical;"></textarea>';
            }
        }
    }
    
    check();
}

// Inicializar editor cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar editor de comentario de tarea principal directamente
        initializeTaskCommentEditor();
        console.log('Editor Quill inicializado correctamente');
    
    // Procesar checkboxes existentes en comentarios
    processExistingComments();
});

// Función para procesar comentarios existentes y convertir checkboxes
function processExistingComments() {
    console.log('Procesando comentarios existentes para checkboxes...');
    
    // Procesar comentarios de tarea principal
    const taskComments = document.querySelectorAll('.comments-list .comment-item .comment-content');
    taskComments.forEach(element => {
        makeChecklistInteractive(element);
    });
}

// Función para convertir contenido a checklist interactivo con checkboxes HTML
function makeChecklistInteractive(element) {
    console.log('=== PROCESANDO CHECKLISTS ===');
    console.log('Elemento:', element);
    
    // Obtener información del comentario
    const commentContent = element.closest('.comment-item');
    let commentId = 'unknown';
    let commentType = 'task'; // Por defecto task
    
    console.log('Elemento padre .comment-item:', commentContent);
    
    if (commentContent) {
        // Intentar extraer ID del comentario del elemento padre
        commentId = extractCommentId(commentContent);
        commentType = detectCommentType(commentContent);
    }
    
    console.log('CommentID detectado:', commentId, 'Tipo:', commentType);
    console.log('¿Está en modal de subtarea?', !!element.closest('#subtaskCommentsModal'));
    
    // Obtener todo el HTML del elemento
    let html = element.innerHTML;
    console.log('HTML original:', html);
    
    // Buscar y reemplazar patrones de checkbox en el HTML
    const checkboxPattern = /(☐|☑)\s*([^<\n]*)/g;
    let checkboxIndex = 0;
    
    html = html.replace(checkboxPattern, function(match, checkbox, text) {
        const isChecked = checkbox === '☑';
        const taskText = text.trim();
        
        console.log('Encontrado checkbox:', checkbox, 'texto:', taskText, 'marcado:', isChecked);
        
        // Crear ID único para cada checkbox
        const checkboxId = 'checkbox_' + Math.random().toString(36).substr(2, 9);
        const currentIndex = checkboxIndex++;
        
        return `<span class="checkbox-container" style="display: inline-flex; align-items: center; gap: 8px; margin: 4px 0; user-select: none;">
            <input type="checkbox" id="${checkboxId}" ${isChecked ? 'checked' : ''} 
                   data-comment-id="${commentId}" 
                   data-comment-type="${commentType}" 
                   data-checkbox-index="${currentIndex}"
                   data-checkbox-text="${taskText}"
                   style="width: 16px; height: 16px; cursor: pointer; accent-color: #10b981; margin: 0; flex-shrink: 0;" 
                   onchange="saveCheckboxState(this)" />
            <span onclick="toggleCheckbox('${checkboxId}')" 
                  style="cursor: pointer; transition: all 0.2s ease; font-size: 14px; line-height: 1.4; ${isChecked ? 'text-decoration: line-through; color: #9ca3af;' : ''}">${taskText}</span>
        </span>`;
    });
    
    // Actualizar el HTML del elemento
    element.innerHTML = html;
    console.log('HTML procesado:', html);
    
    // Cargar estados guardados si tenemos commentId
    if (commentId && commentType !== 'unknown' && commentId !== 'unknown') {
        loadCheckboxStates(commentId, commentType);
    }
}

// Función para toggle del checkbox desde el texto
function toggleCheckbox(checkboxId) {
    const checkbox = document.getElementById(checkboxId);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        toggleTaskText(checkbox);
        saveCheckboxState(checkbox);
    }
}

// Función para aplicar/quitar tachado del texto
function toggleTaskText(checkbox) {
    const label = checkbox.nextElementSibling;
    if (label) {
        if (checkbox.checked) {
            label.style.textDecoration = 'line-through';
            label.style.color = '#9ca3af';
            console.log('Marcando tarea como completada');
        } else {
            label.style.textDecoration = 'none';
            label.style.color = 'inherit';
            console.log('Desmarcando tarea');
        }
        
        // Animación sutil
        label.style.transform = 'scale(1.05)';
        setTimeout(() => {
            label.style.transform = 'scale(1)';
        }, 100);
    }
}

// Función para guardar el estado del checkbox en la base de datos
function saveCheckboxState(checkbox) {
    const commentId = checkbox.dataset.commentId;
    const commentType = checkbox.dataset.commentType;
    const checkboxIndex = checkbox.dataset.checkboxIndex;
    const checkboxText = checkbox.dataset.checkboxText;
    const isChecked = checkbox.checked;
    
    console.log('=== CHECKBOX STATE DEBUG ===');
    console.log('Checkbox element:', checkbox);
    console.log('Dataset completo:', checkbox.dataset);
    console.log('commentId:', commentId, 'tipo:', typeof commentId);
    console.log('commentType:', commentType, 'tipo:', typeof commentType);
    console.log('checkboxIndex:', checkboxIndex, 'tipo:', typeof checkboxIndex);
    console.log('checkboxText:', checkboxText, 'tipo:', typeof checkboxText);
    console.log('isChecked:', isChecked, 'tipo:', typeof isChecked);
    console.log('Elemento padre más cercano con data-comment-id:', checkbox.closest('[data-comment-id]'));
    
    if (!commentId || !commentType || commentId === 'null' || commentId === 'undefined' || commentId === 'unknown') {
        console.error('❌ No se puede guardar: faltan datos del comentario');
        return;
    }
    
    // Preparar datos para enviar
    const payload = {
        comment_id: parseInt(commentId),
        comment_type: commentType,
        checkbox_index: parseInt(checkboxIndex),
        checkbox_text: checkboxText,
        is_checked: isChecked
    };
    
    console.log('📤 Enviando datos:', payload);
    console.log('📤 URL:', '?route=clan_member/save-checkbox-state');
    
    // Guardar en la base de datos
    fetch('?route=clan_member/save-checkbox-state', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        console.log('📥 Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('📥 Response completa:', data);
        console.log('📥 Datos de respuesta:', data);
        if (data.success) {
            console.log('✅ Estado guardado correctamente');
        } else {
            console.error('❌ Error al guardar estado:', data.message);
            showNotification('Error al guardar estado del checkbox: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error de fetch:', error);
        showNotification('Error de conexión al guardar estado', 'error');
    });
}

// Función para cargar estados guardados de checkboxes
function loadCheckboxStates(commentId, commentType) {
    console.log('=== CARGANDO ESTADOS DE CHECKBOX ===');
    console.log('CommentID:', commentId, 'Tipo:', commentType);
    
    const url = `?route=clan_member/get-checkbox-states&comment_ids=${commentId}&comment_type=${commentType}`;
    console.log('URL:', url);
    
    fetch(url)
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success && data.states[commentId]) {
            const states = data.states[commentId];
            console.log('Estados cargados para comentario', commentId, ':', states);
            
            // Aplicar estados a los checkboxes
            Object.keys(states).forEach(index => {
                const state = states[index];
                const checkbox = document.querySelector(`[data-comment-id="${commentId}"][data-checkbox-index="${index}"]`);
                
                if (checkbox) {
                    checkbox.checked = state.is_checked;
                    const label = checkbox.nextElementSibling;
                    if (label) {
                        if (state.is_checked) {
                            label.style.textDecoration = 'line-through';
                            label.style.color = '#9ca3af';
                        } else {
                            label.style.textDecoration = 'none';
                            label.style.color = 'inherit';
                        }
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Error al cargar estados de checkbox:', error);
    });
}

// Función para extraer ID del comentario del DOM
function extractCommentId(commentElement) {
    // Buscar en clases, IDs o atributos data
    if (commentElement.dataset.commentId) {
        return commentElement.dataset.commentId;
    }
    
    // Buscar en el elemento padre si no se encuentra
    let parent = commentElement.closest('[data-comment-id]');
    if (parent && parent.dataset.commentId) {
        return parent.dataset.commentId;
    }
    
    // Buscar en el HTML interno por patrones
    const htmlContent = commentElement.innerHTML;
    const match = htmlContent.match(/comment[_-]?id["\s]*[:=]\s*["']?(\d+)/i);
    if (match) {
        return match[1];
    }
    
    console.warn('No se pudo extraer comment ID del elemento:', commentElement);
    return 'unknown';
}

// Función para detectar tipo de comentario
function detectCommentType(commentElement) {
    // Si está dentro de un modal de subtarea, es subtask
    if (commentElement.closest('#subtaskCommentsModal')) {
        return 'subtask';
    }
    
    // Si está en la sección principal, es task
    if (commentElement.closest('.comments-section')) {
        return 'task';
    }
    
    return 'task'; // Por defecto
}

// Función para mostrar notificaciones ya está definida anteriormente
// No redefinir para evitar recursión infinita

// Función para configurar mejoras de scroll en el modal de comentarios
function setupScrollEnhancements(container, commentsCount) {
    // Detectar si hay scroll disponible
    if (container.scrollHeight > container.clientHeight) {
        container.classList.add('has-scroll');
        
        // Agregar indicador de cantidad de comentarios si hay muchos
        if (commentsCount > 5) {
            const indicator = document.createElement('div');
            indicator.className = 'comments-count-indicator';
            indicator.innerHTML = `<i class="fas fa-comments"></i> ${commentsCount} comentarios`;
            indicator.style.cssText = `
                position: sticky;
                top: 5px;
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
                text-align: center;
                margin-bottom: 10px;
                z-index: 2;
                backdrop-filter: blur(4px);
                border: 1px solid rgba(59, 130, 246, 0.2);
            `;
            container.insertBefore(indicator, container.firstChild);
        }
        
        // Agregar listener para scroll suave y efectos
        container.addEventListener('scroll', function() {
            const scrollTop = this.scrollTop;
            const scrollHeight = this.scrollHeight;
            const clientHeight = this.clientHeight;
            const scrollPercent = scrollTop / (scrollHeight - clientHeight);
            
            // Actualizar opacidad de indicadores basado en posición de scroll
            const beforeElement = container.querySelector('::before');
            const afterElement = container.querySelector('::after');
            
            // Mostrar indicador superior solo si no estamos al principio
            if (scrollTop > 10) {
                container.style.setProperty('--scroll-top-opacity', '0.8');
            } else {
                container.style.setProperty('--scroll-top-opacity', '0.2');
            }
            
            // Mostrar indicador inferior solo si no estamos al final
            if (scrollTop < scrollHeight - clientHeight - 10) {
                container.style.setProperty('--scroll-bottom-opacity', '0.8');
            } else {
                container.style.setProperty('--scroll-bottom-opacity', '0.2');
            }
        });
        
        // Inicializar variables CSS
        container.style.setProperty('--scroll-top-opacity', '0.2');
        container.style.setProperty('--scroll-bottom-opacity', '0.8');
    }
}

// Función para mostrar modal de asignación de usuarios a subtarea
function showSubtaskAssignmentModal(subtaskId) {
    // Crear modal dinámicamente
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.style.cssText = `
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
    `;
    
    modal.innerHTML = `
        <div class="modal-content" style="background: white; padding: 24px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">Asignar usuarios a subtarea</h3>
                <button onclick="closeSubtaskAssignmentModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div id="assignment-loading" style="text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin"></i> Cargando miembros del clan...
            </div>
            <div id="assignment-content" style="display: none;">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Seleccionar usuarios:</label>
                    <div id="users-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px;">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button onclick="closeSubtaskAssignmentModal()" style="background: #f3f4f6; color: #374151; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button onclick="assignUsersToSubtask(${subtaskId})" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                        <i class="fas fa-user-plus"></i> Asignar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cargar lista de miembros del clan
    loadClanMembers(subtaskId);
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeSubtaskAssignmentModal();
        }
    });
}

function closeSubtaskAssignmentModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

function loadClanMembers(subtaskId) {
    fetch('?route=clan_member/get-clan-members', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const loadingDiv = document.getElementById('assignment-loading');
        const contentDiv = document.getElementById('assignment-content');
        
        if (data.success) {
            const usersList = document.getElementById('users-list');
            usersList.innerHTML = '';
            
            data.members.forEach(member => {
                const userDiv = document.createElement('div');
                userDiv.style.cssText = `
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background-color 0.2s;
                `;
                userDiv.onmouseover = () => userDiv.style.backgroundColor = '#f3f4f6';
                userDiv.onmouseout = () => userDiv.style.backgroundColor = 'transparent';
                
                userDiv.innerHTML = `
                    <input type="checkbox" id="user-${member.user_id}" value="${member.user_id}" style="margin-right: 8px;">
                    <label for="user-${member.user_id}" style="cursor: pointer; flex: 1;">
                        <strong>${member.full_name}</strong>
                        <div style="font-size: 12px; color: #6b7280;">${member.username}</div>
                    </label>
                `;
                
                usersList.appendChild(userDiv);
            });
            
            loadingDiv.style.display = 'none';
            contentDiv.style.display = 'block';
        } else {
            loadingDiv.innerHTML = '<div style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error al cargar miembros</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const loadingDiv = document.getElementById('assignment-loading');
        loadingDiv.innerHTML = '<div style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error de conexión</div>';
    });
}

// Función para remover un colaborador de una subtarea
function removeCollaboratorFromSubtask(subtaskId, userName) {
    if (!confirm(`¿Estás seguro de que deseas remover a ${userName} de esta subtarea?`)) {
        return;
    }
    
    // Primero necesitamos obtener el user_id basado en el nombre
    // Por ahora, enviamos el nombre y lo resolvemos en el backend
    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('user_name', userName);
    
    fetch('?route=clan_member/unassign-subtask-user', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Usuario removido exitosamente', 'success');
            // Recargar la página para mostrar los cambios
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Error al remover usuario', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión al remover usuario', 'error');
    });
}

function assignUsersToSubtask(subtaskId) {
    const selectedUsers = [];
    const checkboxes = document.querySelectorAll('#users-list input[type="checkbox"]:checked');
    
    checkboxes.forEach(checkbox => {
        selectedUsers.push(parseInt(checkbox.value));
    });
    
    if (selectedUsers.length === 0) {
        showNotification('Debes seleccionar al menos un usuario', 'error');
        return;
    }
    
    // Mostrar loading
    const assignBtn = event.target;
    const originalText = assignBtn.innerHTML;
    assignBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
    assignBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('user_ids', JSON.stringify(selectedUsers));
    
    fetch('?route=clan_member/assign-subtask-users', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.text().then(text => {
            console.log('Raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            showNotification(data.message, 'success');
            closeSubtaskAssignmentModal();
            // Recargar la página para mostrar los cambios
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Error al asignar usuarios', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        assignBtn.innerHTML = originalText;
        assignBtn.disabled = false;
    });
}
</script>


<style>
/* Estilos para el editor de texto enriquecido */
.rich-editor-container {
    margin-bottom: 10px;
}

.ql-editor {
    min-height: 100px;
    font-size: 14px;
    line-height: 1.5;
}

.ql-toolbar {
    border: 1px solid #d1d5db;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    background: #f9fafb;
}

.ql-container {
    border: 1px solid #d1d5db;
    border-radius: 0 0 8px 8px;
    font-family: inherit;
}

.ql-editor.ql-blank::before {
    color: #9ca3af;
    font-style: italic;
}

/* Estilos para checklist personalizado */
.ql-editor p {
    margin: 0.5em 0;
}

.ql-editor p:contains("☐"),
.ql-editor p:contains("☑") {
    cursor: pointer;
    padding: 4px 0;
    transition: background-color 0.2s ease;
}

.ql-editor p:contains("☐"):hover,
.ql-editor p:contains("☑"):hover {
    background-color: #f3f4f6;
    border-radius: 4px;
    padding-left: 8px;
}

/* Botón personalizado de checklist */
.ql-checklist-btn {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}

/* Mejorar apariencia general del editor */
.ql-snow .ql-tooltip {
    z-index: 1000;
}

.ql-snow .ql-picker-options {
    z-index: 1000;
}

/* Estilos para el selector de emojis */
.emoji-picker-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 10;
}

.emoji-picker-btn:hover {
    background: #e5e7eb;
    transform: scale(1.05);
}

.emoji-picker-btn i {
    font-size: 18px;
    color: #6b7280;
}

.emoji-picker-container {
    position: absolute;
    bottom: 50px;
    right: 10px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 10px;
    width: 320px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 4px;
}

.emoji-item {
    background: none;
    border: none;
    padding: 6px;
    font-size: 20px;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s;
    line-height: 1;
}

.emoji-item:hover {
    background: #f3f4f6;
    transform: scale(1.2);
}

.rich-editor-container .ql-container {
    padding-bottom: 40px;
}

.rich-editor-container textarea {
    padding-bottom: 40px !important;
}

.emoji-picker-container::-webkit-scrollbar {
    width: 8px;
}

.emoji-picker-container::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
}

.emoji-picker-container::-webkit-scrollbar-thumb {
    background: #9ca3af;
    border-radius: 4px;
}

.emoji-picker-container::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>

<script>
// Selector de Emojis
const emojis = [
    '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '😊', '😇', '🥰', '😍', '🤩', '😘',
    '😗', '😚', '😙', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨',
    '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒',
    '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕',
    '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭',
    '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀',
    '☠️', '💩', '🤡', '👹', '👺', '👻', '👽', '👾', '🤖', '😺', '😸', '😹', '😻', '😼', '😽',
    '🙀', '😿', '😾', '👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙',
    '👈', '👉', '👆', '👇', '☝️', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '👐',
    '🤲', '🤝', '🙏', '✍️', '💅', '🤳', '💪', '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤',
    '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟'
];

function initializeEmojiPicker(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '';
    const emojiGrid = document.createElement('div');
    emojiGrid.className = 'emoji-grid';
    
    emojis.forEach(emoji => {
        const emojiBtn = document.createElement('button');
        emojiBtn.type = 'button';
        emojiBtn.className = 'emoji-item';
        emojiBtn.textContent = emoji;
        emojiBtn.onclick = () => insertEmoji(emoji, containerId.replace('-emoji-picker', ''));
        emojiGrid.appendChild(emojiBtn);
    });
    
    container.appendChild(emojiGrid);
}

function toggleEmojiPicker(editorId) {
    const pickerId = editorId + '-emoji-picker';
    const picker = document.getElementById(pickerId);
    
    if (!picker) {
        console.error('No se encontró el picker:', pickerId);
        return;
    }
    
    if (picker.style.display === 'none') {
        initializeEmojiPicker(pickerId);
        picker.style.display = 'block';
        
        document.querySelectorAll('.emoji-picker-container').forEach(p => {
            if (p.id !== pickerId) {
                p.style.display = 'none';
            }
        });
        
        setTimeout(() => {
            document.addEventListener('click', function closeEmoji(e) {
                if (!e.target.closest('.emoji-picker-container') && !e.target.closest('.emoji-picker-btn')) {
                    picker.style.display = 'none';
                    document.removeEventListener('click', closeEmoji);
                }
            });
        }, 100);
    } else {
        picker.style.display = 'none';
    }
}

function insertEmoji(emoji, editorId) {
    console.log('=== INSERTAR EMOJI ===');
    console.log('Emoji:', emoji);
    console.log('Editor ID:', editorId);
    
    // Método 1: Insertar directamente en el div contenteditable de Quill
    let editorContainer = null;
    
    if (editorId === 'task-comment') {
        editorContainer = document.querySelector('#task-comment-editor .ql-editor');
    } else if (editorId.startsWith('subtask-comment-')) {
        const subtaskId = editorId.replace('subtask-comment-', '');
        editorContainer = document.querySelector(`#subtask-comment-editor-${subtaskId} .ql-editor`);
    }
    
    console.log('Editor container encontrado:', editorContainer);
    
    if (editorContainer) {
        // Método directo: insertar en el contenido HTML
        editorContainer.focus();
        
        // Obtener la selección actual o crear una nueva
        const selection = window.getSelection();
        let range;
        
        try {
            range = selection.getRangeAt(0);
        } catch(e) {
            // Si no hay selección, crear un rango al final del contenido
            range = document.createRange();
            range.selectNodeContents(editorContainer);
            range.collapse(false); // false = colapsar al final
        }
        
        // Crear un nodo de texto con el emoji
        const emojiNode = document.createTextNode(emoji);
        
        // Insertar el emoji en la posición del cursor
        range.deleteContents();
        range.insertNode(emojiNode);
        
        // Mover el cursor después del emoji
        range.setStartAfter(emojiNode);
        range.setEndAfter(emojiNode);
        selection.removeAllRanges();
        selection.addRange(range);
        
        // Enfocar el editor
        editorContainer.focus();
        
        console.log('✅ Emoji insertado directamente en el editor');
        
        // Disparar evento input para que Quill detecte el cambio
        editorContainer.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Método 2: Si tenemos acceso al objeto Quill, sincronizar
        let quillEditor = null;
        
        if (editorId === 'task-comment') {
            quillEditor = window.taskCommentEditor;
        } else if (editorId.startsWith('subtask-comment-')) {
            const subtaskId = editorId.replace('subtask-comment-', '');
            quillEditor = window[`subtaskCommentEditor_${subtaskId}`];
        }
        
        if (quillEditor) {
            // Sincronizar el contenido HTML con Quill
            const html = editorContainer.innerHTML;
            const delta = quillEditor.clipboard.convert(html);
            quillEditor.setContents(delta, 'silent');
            console.log('✅ Contenido sincronizado con Quill');
        }
    } else {
        // Fallback: buscar cualquier textarea
        console.log('Buscando textarea como fallback...');
        
        let textarea = null;
        
        if (editorId === 'task-comment') {
            textarea = document.querySelector('#task-comment-editor textarea') ||
                      document.querySelector('textarea[name="comment_text"]');
        } else if (editorId.startsWith('subtask-comment-')) {
            const subtaskId = editorId.replace('subtask-comment-', '');
            textarea = document.querySelector(`#subtask-comment-editor-${subtaskId} textarea`) ||
                      document.querySelector(`#subtask-comment-text`);
        }
        
        console.log('Textarea encontrado:', textarea);
        
        if (textarea) {
            const start = textarea.selectionStart || 0;
            const end = textarea.selectionEnd || 0;
            const text = textarea.value || '';
            
            textarea.value = text.substring(0, start) + emoji + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
            textarea.focus();
            
            // Disparar eventos para actualizar
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
            
            console.log('✅ Emoji insertado en textarea');
        } else {
            console.error('❌ No se encontró ningún editor para insertar el emoji');
            
            // Último intento: copiar al portapapeles
            navigator.clipboard.writeText(emoji).then(() => {
                alert(`El emoji ${emoji} se copió al portapapeles. Pégalo con Ctrl+V o Cmd+V`);
            });
        }
    }
    
    // Cerrar el picker
    const pickerId = editorId + '-emoji-picker';
    const picker = document.getElementById(pickerId);
    if (picker) {
        picker.style.display = 'none';
    }
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [
    APP_URL . 'assets/css/clan-member.css',
    'https://cdn.quilljs.com/1.3.6/quill.snow.css'
];
$additionalJS = [
    'https://cdn.quilljs.com/1.3.6/quill.min.js'
];
require_once __DIR__ . '/../layout.php';
?>


