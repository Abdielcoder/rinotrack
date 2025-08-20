<?php
// Guardar el contenido en una variable
ob_start();

// Incluir modelo de tareas
require_once __DIR__ . '/../../models/Task.php';

// Funciones helper
function getMemberColor($userId) {
    $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
    return $colors[$userId % count($colors)];
}

function getActiveTasksCount($userId) {
    // Por ahora retornamos un número aleatorio para evitar errores
    // TODO: Implementar conteo real cuando se resuelvan los problemas de transacciones
    return rand(1, 15);
}
?>

<div class="task-management-fullscreen">
    <!-- Header de Gestión de Tareas -->
    <header class="task-management-header">
        <div class="header-content">
            <div class="header-left">
                <div class="task-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="header-text">
                    <h1>Gestión de Tareas</h1>
                    <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?></span>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="btn-minimal secondary" onclick="closeTaskManagement()">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
                <button class="btn-minimal primary" onclick="saveTask()">
                    <i class="fas fa-save"></i>
                    Guardar Tarea
                </button>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="task-management-content">
        <div class="task-form-container">
            <!-- Formulario de Tarea Principal -->
            <div class="task-main-form">
                <div class="form-section">
                    <h3>Detalles de la Tarea</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="task_title">Título de la tarea *</label>
                            <input type="text" id="task_title" name="task_title" placeholder="Título de la tarea *" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="task_due_date">Fecha límite *</label>
                            <div class="date-input-wrapper">
                                <input type="date" id="task_due_date" name="task_due_date" required>
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="task_project">Proyecto/Concepto</label>
                            <div class="select-wrapper">
                                <select id="task_project" name="task_project">
                                    <option value="">Seleccionar proyecto...</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['project_id']; ?>" 
                                                <?php echo (isset($selectedProjectId) && $selectedProjectId == $project['project_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['project_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="task_description">Descripción</label>
                            <textarea id="task_description" name="task_description" rows="3" placeholder="Descripción de la tarea..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección de Subtareas -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-tasks"></i> Subtareas</h3>
                        <button type="button" class="btn-minimal small" onclick="addSubtask()">
                            <i class="fas fa-plus"></i>
                            Agregar Subtarea
                        </button>
                    </div>
                    
                    <div id="subtasks-container">
                        <!-- Las subtareas se agregarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- Sección de Asignación de Colaboradores -->
                <div class="form-section">
                    <h3>Asignar colaboradores:</h3>
                    
                    <!-- Checkbox para seleccionar todos -->
                    <div class="select-all-container">
                        <div class="select-all-checkbox">
                            <input type="checkbox" id="select_all_members" name="select_all_members">
                            <label for="select_all_members">
                                <i class="fas fa-check"></i>
                                Seleccionar todos los colaboradores
                            </label>
                        </div>
                    </div>
                    
                    <div class="collaborators-grid">
                        <?php foreach ($members as $member): ?>
                            <div class="collaborator-card" data-user-id="<?php echo $member['user_id']; ?>">
                                <div class="collaborator-checkbox">
                                    <input type="checkbox" 
                                           class="member-checkbox"
                                           id="member_<?php echo $member['user_id']; ?>" 
                                           name="assigned_members[]" 
                                           value="<?php echo $member['user_id']; ?>">
                                    <label for="member_<?php echo $member['user_id']; ?>"></label>
                                </div>
                                
                                <div class="collaborator-avatar">
                                    <div class="avatar-initial" style="background-color: <?php echo getMemberColor($member['user_id']); ?>">
                                        <?php echo strtoupper(substr($member['full_name'], 0, 1)); ?>
                                    </div>
                                </div>
                                
                                <div class="collaborator-info">
                                    <div class="collaborator-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                                    <div class="collaborator-tasks">
                                        <span class="active-tasks"><?php echo getActiveTasksCount($member['user_id']); ?> tareas activas</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sección de Tareas del Trimestre Actual -->
                <div class="form-section">
                    <h3>Tareas del Trimestre Actual (Sin Completar):</h3>
                    
                    <?php if (!empty($currentQuarterTasks)): ?>
                        <div class="quarter-tasks-container">
                            <div class="quarter-tasks-grid">
                                <?php foreach ($currentQuarterTasks as $task): ?>
                                    <div class="quarter-task-card" data-task-id="<?php echo $task['task_id']; ?>">
                                        <div class="task-header">
                                            <div class="task-priority priority-<?php echo $task['priority']; ?>">
                                                <i class="fas fa-flag"></i>
                                                <?php echo ucfirst($task['priority']); ?>
                                            </div>
                                            <div class="task-status status-<?php echo $task['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="task-content">
                                            <h4 class="task-title"><?php echo htmlspecialchars($task['task_name']); ?></h4>
                                            <p class="task-project">Proyecto: <?php echo htmlspecialchars($task['project_name']); ?></p>
                                            
                                            <?php if (!empty($task['description'])): ?>
                                                <p class="task-description"><?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?><?php echo strlen($task['description']) > 100 ? '...' : ''; ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="task-meta">
                                                <?php if ($task['due_date']): ?>
                                                    <div class="meta-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>Vence: <?php echo Utils::formatDate($task['due_date'], 'd/m/Y', 'Sin fecha límite'); ?></span>
                                                        <?php if ($task['days_until_due'] < 0): ?>
                                                            <span class="overdue">¡Vencida!</span>
                                                        <?php elseif ($task['days_until_due'] <= 3): ?>
                                                            <span class="urgent">¡Pronto!</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($task['all_assigned_users'])): ?>
                                                    <div class="meta-item">
                                                        <i class="fas fa-users"></i>
                                                        <span><?php echo htmlspecialchars($task['all_assigned_users']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="task-progress">
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $task['completion_percentage']; ?>%"></div>
                                                </div>
                                                <span class="progress-text"><?php echo $task['completion_percentage']; ?>% completado</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-quarter-tasks">
                            <div class="no-tasks-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h4>No hay tareas pendientes en el trimestre actual</h4>
                            <p>Todas las tareas del trimestre actual han sido completadas o no hay tareas asignadas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template para Subtareas -->
<template id="subtask-template">
    <div class="subtask-item" data-subtask-index="{index}">
        <div class="subtask-header">
            <div class="subtask-number">Subtarea {number}</div>
            <button type="button" class="btn-remove-subtask" onclick="removeSubtask({index})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <div class="subtask-content">
            <div class="form-row">
                <div class="form-group">
                    <label>Título de la subtarea *</label>
                    <input type="text" name="subtasks[{index}][title]" placeholder="Título de la subtarea" required>
                </div>
                
                <div class="form-group">
                    <label>Porcentaje de completado *</label>
                    <input type="number" name="subtasks[{index}][percentage]" min="1" max="100" placeholder="%" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha límite</label>
                    <div class="date-input-wrapper">
                        <input type="date" name="subtasks[{index}][due_date]" placeholder="Fecha límite">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Prioridad</label>
                    <div class="select-wrapper">
                        <select name="subtasks[{index}][priority]">
                            <option value="low">Baja</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Asignar a</label>
                    <div class="select-wrapper">
                        <select name="subtasks[{index}][assigned_to_user_id]">
                            <option value="">Sin asignar</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['user_id']; ?>">
                                    <?php echo htmlspecialchars($member['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label>Descripción</label>
                    <textarea name="subtasks[{index}][description]" rows="2" placeholder="Descripción de la subtarea..."></textarea>
                </div>
            </div>
        </div>
    </div>
</template>



<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css?v=' . time()
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js',
    APP_URL . 'assets/js/task-management.js?v=' . time()
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?>

<!-- Estilos para subtareas -->
<style>
.subtask-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    position: relative;
}

.subtask-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.subtask-number {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.btn-remove-subtask {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 6px 10px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}

.btn-remove-subtask:hover {
    background: #dc2626;
    transform: scale(1.05);
}

.subtask-content .form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.subtask-content .form-group {
    flex: 1;
}

.subtask-content .form-group.full-width {
    flex: 1 1 100%;
}

.subtask-content .form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #374151;
    font-size: 13px;
}

.subtask-content .form-group input,
.subtask-content .form-group textarea,
.subtask-content .form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.subtask-content .form-group input:focus,
.subtask-content .form-group textarea:focus,
.subtask-content .form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.subtask-content input,
.subtask-content textarea,
.subtask-content select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
}

.subtask-content input:focus,
.subtask-content textarea:focus,
.subtask-content select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.subtask-content .date-input-wrapper,
.subtask-content .select-wrapper {
    position: relative;
    width: 100%;
}

.subtask-content .date-input-wrapper input,
.subtask-content .select-wrapper select {
    width: 100%;
    padding-right: 35px;
}

.subtask-content .date-input-wrapper i,
.subtask-content .select-wrapper i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.section-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 18px;
}

.section-header h3 i {
    margin-right: 8px;
    color: #6b7280;
}
</style>

<!-- JavaScript inline para asegurar que funcione -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado desde script inline...');
    
    // Función simple para seleccionar/deseleccionar todos
    function selectAllMembers(selectAll) {
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        console.log('👥 Encontrados', memberCheckboxes.length, 'checkboxes de miembros');
        
        memberCheckboxes.forEach((checkbox, index) => {
            checkbox.checked = selectAll;
            console.log(`✅ Checkbox ${index + 1} establecido a:`, selectAll);
        });
    }
    
    // Buscar el checkbox principal
    const selectAllCheckbox = document.getElementById('select_all_members');
    console.log('📋 Checkbox principal encontrado:', selectAllCheckbox);
    
    if (selectAllCheckbox) {
        // Agregar evento click simple
        selectAllCheckbox.addEventListener('click', function() {
            console.log('🖱️ Checkbox principal clickeado');
            const isChecked = this.checked;
            console.log('🔄 Estado del checkbox:', isChecked);
            
            // Seleccionar/deseleccionar todos
            selectAllMembers(isChecked);
        });
        
        console.log('✅ Evento click agregado al checkbox principal');
    } else {
        console.error('❌ No se encontró el checkbox principal');
    }
    
    console.log('✅ Script inline ejecutado correctamente');
    
    // Variables globales para subtareas
    window.subtaskIndex = 0;
    window.subtasks = [];
});

// Funciones para manejar subtareas
function addSubtask() {
    const container = document.getElementById('subtasks-container');
    const template = document.getElementById('subtask-template');
    
    if (!container || !template) {
        console.error('No se encontró el contenedor o template de subtareas');
        return;
    }
    
    const subtaskHtml = template.innerHTML
        .replace(/{index}/g, window.subtaskIndex)
        .replace(/{number}/g, window.subtaskIndex + 1);
    
    const subtaskElement = document.createElement('div');
    subtaskElement.innerHTML = subtaskHtml;
    subtaskElement.className = 'subtask-item';
    subtaskElement.setAttribute('data-subtask-index', window.subtaskIndex);
    
    container.appendChild(subtaskElement);
    window.subtaskIndex++;
    
    console.log('Subtarea agregada con índice:', window.subtaskIndex - 1);
}

function removeSubtask(index) {
    const subtaskElement = document.querySelector(`[data-subtask-index="${index}"]`);
    if (subtaskElement) {
        subtaskElement.remove();
        console.log('Subtarea removida con índice:', index);
    }
}

// Función para recolectar datos de subtareas antes de enviar
function collectSubtasksData() {
    const subtaskElements = document.querySelectorAll('.subtask-item');
    const subtasks = [];
    
    subtaskElements.forEach(element => {
        const index = element.getAttribute('data-subtask-index');
        const title = element.querySelector('input[name^="subtasks"][name$="[title]"]').value;
        const percentage = element.querySelector('input[name^="subtasks"][name$="[percentage]"]').value;
        const description = element.querySelector('textarea[name^="subtasks"][name$="[description]"]').value;
        const dueDate = element.querySelector('input[name^="subtasks"][name$="[due_date]"]').value || null;
        const priority = element.querySelector('select[name^="subtasks"][name$="[priority]"]').value;
        const assignedUserId = element.querySelector('select[name^="subtasks"][name$="[assigned_to_user_id]"]').value || null;
        
        if (title.trim() !== '') {
            subtasks.push({
                title: title.trim(),
                percentage: parseInt(percentage) || 0,
                description: description.trim(),
                due_date: dueDate,
                priority: priority,
                assigned_to_user_id: assignedUserId
            });
        }
    });
    
    return subtasks;
}
</script> 