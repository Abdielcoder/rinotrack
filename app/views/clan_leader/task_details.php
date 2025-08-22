<?php
ob_start();
?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    
    <!-- Botones de Acción -->
    <div style="display: flex; gap: 12px; margin-bottom: 30px;">
        <a href="?route=clan_leader/tasks" style="background: #f3f4f6; color: #374151; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Volver a Tareas
        </a>
        <a href="?route=clan_leader/tasks&action=edit&task_id=<?php echo $task['task_id']; ?>" style="background: #1e3a8a; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-edit"></i> Editar Tarea
        </a>
        <button onclick="deleteTask(<?php echo $task['task_id']; ?>)" style="background: #ef4444; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-trash"></i> Eliminar
        </button>
    </div>
    
    <div class="task-layout" style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
        
        <!-- Columna Principal -->
        <div class="main-column">
            
    <!-- Encabezado de la Tarea -->
    <div class="task-header" style="background: #e0e7ff; color: #1e3a8a; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #c7d2fe;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-folder-open"></i>
                    </div>
            <div>
                <h1 style="margin: 0; font-size: 24px; font-weight: 700;"><?php echo htmlspecialchars($task['task_name']); ?></h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Proyecto: <?php echo htmlspecialchars($task['project_name'] ?? 'N/A'); ?></p>
                    </div>
        </div>
        <div>
            <span class="status-badge" style="background: #fbbf24; color: #1f2937; padding: 8px 16px; border-radius: 20px; font-weight: 600; text-transform: uppercase; font-size: 12px;">
                <?php 
                    $estadosTarea = [
                        'pending' => 'PENDIENTE',
                        'in_progress' => 'EN PROGRESO', 
                        'completed' => 'COMPLETADA',
                        'blocked' => 'BLOQUEADA',
                        'cancelled' => 'CANCELADA'
                    ];
                    echo $estadosTarea[$task['status']] ?? strtoupper(str_replace('_',' ', (string)$task['status'])); 
                ?>
            </span>
                </div>
                </div>

    <!-- Frase Motivacional -->
    <div class="motivational-section" style="background: #e0e7ff; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; border: 1px solid #c7d2fe;">
        <div style="width: 50px; height: 50px; background: #fbbf24; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #1f2937;">
            <i class="fas fa-lightbulb"></i>
                </div>
        <div>
            <div id="motQuote" style="font-weight: 600; color: #1e3a8a; font-size: 16px; margin-bottom: 5px;">"La excelencia no es un acto, es un hábito."</div>
            <div id="motAuthor" style="color: #6b7280; font-size: 14px;">— Aristóteles</div>
                </div>
                </div>
        
    <!-- Información del Creador -->
    <div class="creator-info" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user" style="color: #1e3a8a;"></i>
        <span style="color: #374151; font-weight: 500;">Creado por: <?php echo htmlspecialchars($task['created_by_name'] ?? 'Usuario Administrador'); ?></span>
        </div>
        
    <!-- Botón para agregar subtarea -->
    <div style="margin-bottom: 20px;">
        <button onclick="showAddSubtaskModal()" style="background: #1e3a8a; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.2s;">
            <i class="fas fa-plus"></i>
            Agregar Subtarea
        </button>
    </div>
        
    <!-- Descripción -->
                <?php if (!empty($task['description'])): ?>
    <div class="description-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px; font-weight: 600;">Descripción</h3>
        <div style="color: #374151; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($task['description'])); ?></div>
                </div>
                <?php endif; ?>
                
    <!-- Subtareas -->
                <?php if (!empty($subtasks)): ?>
    <div class="subtasks-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #1f2937; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-tasks"></i> Subtareas (<?php echo count($subtasks); ?>)
        </h3>
        
                        <?php foreach ($subtasks as $subtask): ?>
        <div class="subtask-card" data-subtask-id="<?php echo $subtask['subtask_id']; ?>" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 15px; background: #f9fafb;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #1f2937;"><?php echo htmlspecialchars($subtask['title']); ?></h4>
                <div style="display: flex; gap: 8px;">
                    <button class="btn-icon-small btn-with-badge" onclick="showSubtaskComments(<?php echo $subtask['subtask_id']; ?>)" title="Ver comentarios" style="position: relative; width: 32px; height: 32px; border: none; border-radius: 6px; background: #f3f4f6; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-comments"></i>
                      <span class="badge" id="comments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                                        </button>
                    <button class="btn-icon-small btn-with-badge" onclick="showSubtaskAttachments(<?php echo $subtask['subtask_id']; ?>)" title="Ver adjuntos" style="position: relative; width: 32px; height: 32px; border: none; border-radius: 6px; background: #f3f4f6; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-paperclip"></i>
                      <span class="badge" id="attachments-badge-<?php echo $subtask['subtask_id']; ?>" style="display: none;">0</span>
                                        </button>
                    <button class="btn-icon-small" onclick="editSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Editar" style="width: 32px; height: 32px; border: none; border-radius: 6px; background: #f3f4f6; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                    <button class="btn-icon-small" onclick="deleteSubtask(<?php echo $subtask['subtask_id']; ?>)" title="Eliminar" style="width: 32px; height: 32px; border: none; border-radius: 6px; background: #fee2e2; color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
            
            <div style="margin-bottom: 15px;">
                <span class="subtask-status-text" style="color: #6b7280; font-size: 14px;">Estado: <?php 
                    $estados = [
                        'pending' => 'Pendiente',
                        'in_progress' => 'En Progreso', 
                        'completed' => 'Completada',
                        'blocked' => 'Bloqueada'
                    ];
                    echo $estados[$subtask['status']] ?? ucfirst(str_replace('_', ' ', $subtask['status'])); 
                ?></span>
                                    <?php if (!empty($subtask['assigned_user_name'])): ?>
                <span style="color: #6b7280; font-size: 14px; margin-left: 20px;">Asignado: <?php echo htmlspecialchars($subtask['assigned_user_name']); ?></span>
                                    <?php endif; ?>
                                </div>
            
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="flex: 1; margin-right: 20px;">
                    <div style="background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div class="progress-fill" style="background: #10b981; height: 100%; width: <?php echo $subtask['completion_percentage']; ?>%; transition: width 0.3s ease;"></div>
                    </div>
                    <span class="progress-text" style="font-size: 14px; font-weight: 600; color: #374151;"><?php echo $subtask['completion_percentage']; ?>%</span>
                </div>
                <select onchange="updateSubtaskStatus(<?php echo $subtask['subtask_id']; ?>, this.value)" style="padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background: white;">
                    <option value="pending" <?php echo $subtask['status'] === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="in_progress" <?php echo $subtask['status'] === 'in_progress' ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="completed" <?php echo $subtask['status'] === 'completed' ? 'selected' : ''; ?>>Completada</option>
                                    </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
        <!-- Comentarios de la Tarea (solo si NO hay subtareas) -->
    <?php if (empty($subtasks)): ?>
    <div class="comments-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #1f2937; font-size: 18px; font-weight: 600;">Comentarios (<?php echo count($comments); ?>)</h3>
        
        <form id="tdCommentForm" class="comment-form" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>" />
            <textarea name="comment_text" placeholder="Escribe un comentario..." style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical; margin-bottom: 10px;" rows="3"></textarea>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <input type="file" name="attachments[]" multiple style="font-size: 14px;" />
                <button type="submit" style="background: #1e3a8a; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-paper-plane"></i> Enviar
                </button>
            </div>
        </form>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <div style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">Sin comentarios</div>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                <div class="comment-item" style="border-bottom: 1px solid #f3f4f6; padding: 15px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-weight: 600; color: #374151;"><?php echo htmlspecialchars($c['full_name'] ?? $c['username'] ?? ''); ?></span>
                        <span style="font-size: 12px; color: #6b7280;"><?php echo htmlspecialchars($c['created_at'] ?? ''); ?></span>
                    </div>
                    <div style="color: #374151; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($c['comment_text'] ?? '')); ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

        </div>
            
        <!-- Columna Lateral -->
        <div class="sidebar-column">
            
    <!-- Colaboradores -->
    <div class="collaborators-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #1e3a8a; font-size: 18px; font-weight: 600;">Colaboradores (<?php echo count($assignedUsers); ?>)</h3>
        
        <button onclick="showAddCollaboratorModal()" style="background: #1e3a8a; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-bottom: 15px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fas fa-plus"></i> Agregar Colaborador
                        </button>
                    
        <?php if (empty($assignedUsers)): ?>
            <div style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">Sin colaboradores</div>
        <?php else: ?>
            <?php foreach ($assignedUsers as $au): ?>
            <div data-user-id="<?php echo $au['user_id']; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid #f3f4f6; border-radius: 6px; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: #1e3a8a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        <?php echo strtoupper(substr($au['full_name'] ?? $au['username'] ?? '', 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #374151;"><?php echo htmlspecialchars($au['full_name'] ?? $au['username'] ?? ''); ?></div>
                        <div style="font-size: 12px; color: #6b7280;"><?php echo htmlspecialchars($au['username'] ?? ''); ?></div>
                    </div>
                                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" min="0" max="100" value="<?php echo $au['assigned_percentage'] ?? 0; ?>" 
                           onchange="updateUserPercentage(<?php echo $au['user_id']; ?>, this.value)"
                           style="width: 60px; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center;">
                    <span style="font-size: 14px;">%</span>
                    <button onclick="removeCollaborator(<?php echo $au['user_id']; ?>)" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 8px; border-radius: 4px; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
        <?php endif; ?>
                </div>
                
    <!-- Historial -->
    <div class="history-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #1e3a8a; font-size: 18px; font-weight: 600;">Historial</h3>
        
            <?php if (empty($history)): ?>
            <div style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">Sin historial</div>
        <?php else: ?>
            <?php foreach ($history as $h): ?>
            <div style="border-bottom: 1px solid #f3f4f6; padding: 12px 0;">
                <div style="font-weight: 600; color: #1e3a8a; margin-bottom: 4px;"><?php 
                    $accionesHistorial = [
                        'created' => 'Creado',
                        'updated' => 'Actualizado',
                        'assigned' => 'Asignado',
                        'completed' => 'Completado',
                        'commented' => 'Comentado',
                        'attached' => 'Archivo adjuntado'
                    ];
                    $accion = $h['action_type'] ?? $h['notes'] ?? '';
                    echo $accionesHistorial[strtolower($accion)] ?? htmlspecialchars(ucfirst($accion)); 
                ?></div>
                <div style="font-size: 12px; color: #6b7280;">Por: <?php echo htmlspecialchars($h['full_name'] ?? $h['username'] ?? ''); ?> — <?php echo htmlspecialchars($h['created_at'] ?? ''); ?></div>
                        </div>
            <?php endforeach; ?>
        <?php endif; ?>
                        </div>

    <!-- Información -->
    <div class="info-section" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #1e3a8a; font-size: 18px; font-weight: 600;">Información</h3>
        
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <div><strong style="color: #374151;">Creado:</strong> <span style="color: #6b7280;"><?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?></span></div>
                        <?php if ($task['updated_at'] !== $task['created_at']): ?>
            <div><strong style="color: #374151;">Actualizado:</strong> <span style="color: #6b7280;"><?php echo date('d/m/Y H:i', strtotime($task['updated_at'])); ?></span></div>
                        <?php endif; ?>
            <div><strong style="color: #374151;">Progreso:</strong> <span style="color: #6b7280;"><?php echo (int)($task['completion_percentage'] ?? 0); ?>%</span></div>
                        <?php if ($task['actual_hours']): ?>
            <div><strong style="color: #374151;">Horas reales:</strong> <span style="color: #6b7280;"><?php echo $task['actual_hours']; ?>h</span></div>
                        <?php endif; ?>
                    </div>
                </div>
    
            </div>
        </div>
    </div>
    
    <!-- Modal para agregar colaborador -->
<div id="addCollaboratorModal" class="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 1000;">
    <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px; max-width: 500px; width: 90%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1f2937;">Agregar Colaborador</h3>
            <button onclick="closeAddCollaboratorModal()" style="background: none; border: none; font-size: 24px; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        <div id="availableUsersList" style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 20px;">
            <!-- Los usuarios se cargarán dinámicamente -->
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button onclick="closeAddCollaboratorModal()" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer;">Cancelar</button>
            <button onclick="addSelectedCollaborators()" style="background: #1e3a8a; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer;">Agregar Seleccionados</button>
        </div>
    </div>
</div>

<style>
/* Estilos para badges de contadores */
.btn-with-badge {
    position: relative;
}

.btn-with-badge .badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    min-width: 18px;
    padding: 0;
}

/* Estilos para notificaciones toast */
#cl-notification {
    background: #10b981 !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

#cl-notification * {
    color: white !important;
    background: none !important;
    border: none !important;
}

#cl-notification button {
    background: none !important;
    border: none !important;
    color: white !important;
}

.btn-icon-small:hover {
    background: #e5e7eb !important;
    color: #374151 !important;
}

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
                
                <form id="add-subtask-form">
                    <div class="form-group">
                        <label for="new-subtask-title" style="display: block; margin-bottom: 5px; color: #374151; font-weight: 600;">Título de la Subtarea:</label>
                        <input type="text" id="new-subtask-title" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 15px; font-size: 14px;">
                    </div>
                    
                    <div class="form-group">
                        <label for="new-subtask-description" style="display: block; margin-bottom: 5px; color: #374151; font-weight: 600;">Descripción:</label>
                        <textarea id="new-subtask-description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 15px; resize: vertical; font-size: 14px;" placeholder="Descripción opcional de la subtarea..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="new-subtask-status" style="display: block; margin-bottom: 5px; color: #374151; font-weight: 600;">Estado:</label>
                        <select id="new-subtask-status" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 15px; font-size: 14px;">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En Progreso</option>
                            <option value="completed">Completada</option>
                            <option value="blocked">Bloqueada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="new-subtask-percentage" style="display: block; margin-bottom: 5px; color: #374151; font-weight: 600;">Porcentaje de Completación:</label>
                        <input type="number" id="new-subtask-percentage" min="0" max="100" value="0" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                    </div>
                    
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="closeExistingModals()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Cancelar
                        </button>
                        <button type="button" onclick="saveNewSubtask()" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Crear Subtarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Enfocar el campo de título
    setTimeout(() => {
        document.getElementById('new-subtask-title').focus();
    }, 100);
}

// Función para guardar nueva subtarea
function saveNewSubtask() {
    const title = document.getElementById('new-subtask-title').value.trim();
    const description = document.getElementById('new-subtask-description').value.trim();
    const status = document.getElementById('new-subtask-status').value;
    const percentage = parseInt(document.getElementById('new-subtask-percentage').value);
    
    if (!title) {
        showNotification('El título es requerido', 'error');
        return;
    }
    
    // Datos a enviar
    const formData = new FormData();
    formData.append('task_id', <?php echo $task['task_id']; ?>);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('status', status);
    formData.append('completion_percentage', percentage);
    
    fetch('?route=clan_leader/add-subtask', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeExistingModals();
            showNotification('Subtarea creada exitosamente', 'success');
            // Recargar la página para mostrar la nueva subtarea
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('Error al crear subtarea: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Cargar contadores al inicializar la página
    loadSubtaskCounters();
});

// Función auxiliar para limpiar modales existentes
function closeExistingModals() {
    const existingModals = document.querySelectorAll('.modal-overlay');
    existingModals.forEach(modal => modal.remove());
}

// Funciones para subtareas
function showSubtaskComments(subtaskId) {
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
    // Cerrar modales existentes antes de abrir uno nuevo
    closeExistingModals();
    
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
        showNotification('Por favor escribe un comentario', 'error');
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
            // Cerrar modal actual antes de recargar
            closeExistingModals();
            // Recargar comentarios y actualizar contadores
            showSubtaskComments(subtaskId);
            loadSubtaskCounters();
        } else {
            showNotification('Error al agregar comentario: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
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
            closeExistingModals();
            // Actualizar contadores sin recargar la página
            loadSubtaskCounters();
            showNotification('Comentario eliminado exitosamente', 'success');
        } else {
            showNotification('Error al eliminar comentario: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

function showSubtaskAttachments(subtaskId) {
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
    // Cerrar modales existentes antes de abrir uno nuevo
    closeExistingModals();
    
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
                                            <a href="${attachment.file_path}" target="_blank" style="color: #1e3a8a; text-decoration: none;">
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
        showNotification('Por favor selecciona un archivo', 'error');
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
            // Cerrar modal actual antes de recargar
            closeExistingModals();
            // Recargar adjuntos y actualizar contadores
            showSubtaskAttachments(subtaskId);
            loadSubtaskCounters();
        } else {
            showNotification('Error al subir archivo: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
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
            closeExistingModals();
            // Actualizar contadores sin recargar la página
            loadSubtaskCounters();
            showNotification('Archivo eliminado exitosamente', 'success');
        } else {
            showNotification('Error al eliminar archivo: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

function editSubtask(subtaskId) {
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
    // Cerrar modales existentes antes de abrir uno nuevo
    closeExistingModals();
    
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
        showNotification('El título es requerido', 'error');
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
            closeExistingModals();
            showNotification('Subtarea actualizada exitosamente', 'success');
            // Recargar la página para mostrar los cambios
            location.reload();
        } else {
            showNotification('Error al actualizar subtarea: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    });
}

function deleteSubtask(subtaskId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta subtarea?')) {
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
    // Calcular el porcentaje de completación basado en el estado
    let completionPercentage = 0;
    if (newStatus === 'pending') {
        completionPercentage = 0;
    } else if (newStatus === 'in_progress') {
        completionPercentage = 50;
    } else if (newStatus === 'completed') {
        completionPercentage = 100;
    }
    
    fetch('?route=clan_leader/update-subtask-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'subtask_id=' + subtaskId + '&status=' + newStatus + '&completion_percentage=' + completionPercentage
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar el estado visual inmediatamente
            const subtaskCard = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            if (subtaskCard) {
                // Actualizar el texto del estado
                const estadoSpan = subtaskCard.querySelector('.subtask-status-text');
                if (estadoSpan) {
                    const estados = {
                        'pending': 'Pendiente',
                        'in_progress': 'En Progreso',
                        'completed': 'Completada',
                        'blocked': 'Bloqueada'
                    };
                    estadoSpan.textContent = 'Estado: ' + estados[newStatus];
                }
                
                // Actualizar la barra de progreso según el estado
                const progressBar = subtaskCard.querySelector('.progress-fill');
                const progressText = subtaskCard.querySelector('.progress-text');
                
                if (progressBar) {
                    progressBar.style.width = completionPercentage + '%';
                }
                if (progressText) {
                    progressText.textContent = completionPercentage + '%';
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
                    if (commentsBadge) {
                        if (data.counts.comments_count > 0) {
                            commentsBadge.textContent = data.counts.comments_count;
                            commentsBadge.style.display = 'inline';
                        } else {
                            commentsBadge.style.display = 'none';
                        }
                    }
                    
                    // Actualizar badge de adjuntos
                    const attachmentsBadge = document.getElementById('attachments-badge-' + subtaskId);
                    if (attachmentsBadge) {
                        if (data.counts.attachments_count > 0) {
                            attachmentsBadge.textContent = data.counts.attachments_count;
                            attachmentsBadge.style.display = 'inline';
                        } else {
                            attachmentsBadge.style.display = 'none';
                        }
                    }
                } else {
                    console.error('Error al cargar contadores:', data.message);
                }
            })
            .catch(error => {
                console.error('Error cargando contadores para subtarea', subtaskId, ':', error);
            });
    });
}

// Formulario de comentarios de tarea
document.getElementById('tdCommentForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    fetch('?route=clan_leader/add-task-comment', { method:'POST', body: fd, credentials:'same-origin' })
        .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
        .then(d=>{ if(!d.success){ showNotification(d.message||'Error', 'error'); return; } location.reload(); });
});
        
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
          <div style="width: 32px; height: 32px; border-radius: 50%; background: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
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
            showNotification('Colaborador removido exitosamente', 'success');
                    } else {
                        showNotification('Error al remover colaborador: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al remover colaborador', 'error');
                });
}

function deleteTask(taskId) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) return;
    
    fetch('?route=clan_leader/delete-task', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'task_id=' + taskId
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showNotification('Tarea eliminada exitosamente', 'success');
        setTimeout(() => { window.location.href = '?route=clan_leader/tasks'; }, 800);
      } else {
        showNotification('Error al eliminar la tarea: ' + (data.message || 'Error desconocido'), 'error');
      }
    })
    .catch(() => showNotification('Error al eliminar la tarea', 'error'));
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
                padding: 15px 20px;
                border-radius: 8px;
                color: white !important;
                font-weight: 600;
                display: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                border: none;
                font-family: inherit;
                font-size: 14px;
                max-width: 350px;
                word-wrap: break-word;
            ">
                <span id="cl-notificationMessage" style="color: white !important; background: none !important;"></span>
                <button onclick="closeCLNotification()" style="
                    background: none !important;
                    border: none !important;
                    color: white !important;
                    margin-left: 10px;
                    cursor: pointer;
                    font-size: 18px;
                    padding: 0;
                    line-height: 1;
                ">&times;</button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', notificationHTML);
        notification = document.getElementById('cl-notification');
    }
    
    const messageElement = document.getElementById('cl-notificationMessage');
    messageElement.textContent = message;
    
    // Limpiar estilos previos
    notification.style.background = '';
    notification.style.backgroundColor = '';
    
    if (type === 'success') {
        notification.style.background = '#10b981 !important';
        notification.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        notification.style.background = '#ef4444 !important';
        notification.style.backgroundColor = '#ef4444';
    } else {
        notification.style.background = '#1e3a8a !important';
        notification.style.backgroundColor = '#1e3a8a';
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

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
require_once __DIR__ . '/../layout.php';
?>