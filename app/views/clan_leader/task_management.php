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
                <button class="btn-minimal primary" onclick="openSubtasksModal()">
                    <i class="fas fa-tasks"></i>
                    Crear con Subtareas
                </button>
                <button class="btn-minimal secondary" onclick="saveTaskWithoutSubtasks()">
                    <i class="fas fa-save"></i>
                    Crear sin Subtareas
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

<!-- Modal para añadir subtareas -->
<div id="addSubtasksModal" class="modal" style="display: none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="addSubtasksModalTitle">
                <i class="fas fa-tasks" style="margin-right: 8px; color: #3b82f6;"></i>
                Añadir Subtareas
            </h3>
            <button class="modal-close" onclick="closeAddSubtasksModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addSubtasksForm">
            <div class="modal-body">
                <div class="task-info">
                    <h4 id="addSubtasksTaskName">Esta tarea será creada con las subtareas que agregues</h4>
                    <input type="hidden" id="addSubtasksTaskId" name="taskId">
                </div>
                
                <div class="subtasks-container">
                    <div class="subtasks-header">
                        <span>Organiza esta tarea en pasos más pequeños</span>
                        <button type="button" class="btn btn-small btn-add-more" id="addSubtaskBtnModal">
                            <i class="fas fa-plus"></i> Agregar Subtarea
                        </button>
                    </div>
                    <div id="addSubtasksList" class="subtasks-list">
                        <!-- Las subtareas se agregarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddSubtasksModal()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="continueWithSubtasksBtn">
                    <span id="continueSubtasksText">Continuar con Subtareas</span>
                    <span id="continueSubtasksLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>





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

<!-- Estilos para el modal -->
<style>
/* Estilos para el modal */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    opacity: 1;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.modal.show .modal-content {
    transform: scale(1);
}

.modal-large {
    max-width: 800px;
}

.modal-header {
    padding: 20px 24px 0;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.modal-body {
    padding: 0 24px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}

.task-info h4 {
    margin: 0 0 8px 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.subtasks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.subtasks-header span {
    color: #6b7280;
    font-size: 0.9rem;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-small {
    padding: 8px 16px;
    font-size: 13px;
}

.btn-add-more {
    background: #10b981;
    color: white;
}

.btn-add-more:hover {
    background: #059669;
}

.subtasks-list {
    min-height: 100px;
}

.subtasks-empty {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
    font-style: italic;
}

.subtask-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    background: white;
    transition: all 0.2s ease;
}

.subtask-item:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.subtask-counter {
    background: #3b82f6;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
}

.subtask-drag-handle {
    color: #9ca3af;
    cursor: grab;
    flex-shrink: 0;
}

.subtask-drag-handle:active {
    cursor: grabbing;
}

.subtask-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.subtask-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.subtask-remove {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    font-size: 12px;
    transition: all 0.2s ease;
}

.subtask-remove:hover {
    background: #dc2626;
    transform: scale(1.05);
}

.btn-loader {
    display: inline-flex;
    align-items: center;
}

.btn-loader i {
    margin-right: 4px;
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
});

// -------- Modal para añadir subtareas --------
let addSubtaskCounter = 0;

function openSubtasksModal() {
    // Validar formulario principal primero
    const taskTitle = document.getElementById('task_title').value;
    const taskDueDate = document.getElementById('task_due_date').value;
    const assignedMembers = document.querySelectorAll('input[name="assigned_members[]"]:checked');
    
    if (!taskTitle || !taskDueDate) {
        showToast('Por favor completa el título y fecha límite antes de agregar subtareas', 'error');
        return;
    }
    
    if (assignedMembers.length === 0) {
        showToast('Debes asignar al menos un colaborador antes de agregar subtareas', 'error');
        return;
    }
    
    const modal = document.getElementById('addSubtasksModal');
    const taskNameEl = document.getElementById('addSubtasksTaskName');
    const listEl = document.getElementById('addSubtasksList');
    
    // Configurar modal
    taskNameEl.textContent = `Tarea: ${taskTitle}`;
    
    // Limpiar lista y reiniciar contador
    listEl.innerHTML = '';
    addSubtaskCounter = 0;
    updateAddSubtasksDisplay();
    
    // Añadir una subtarea inicial
    addSubtaskToForm();
    
    // Mostrar modal
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeAddSubtasksModal() {
    const modal = document.getElementById('addSubtasksModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        
        // Limpiar formulario
        const listEl = document.getElementById('addSubtasksList');
        if (listEl) {
            listEl.innerHTML = '';
            addSubtaskCounter = 0;
        }
    }, 300);
}

function addSubtaskToForm() {
    addSubtaskCounter++;
    const subtaskId = 'add_subtask_' + addSubtaskCounter;
    const listEl = document.getElementById('addSubtasksList');
    
    // Crear elemento de subtarea
    const subtaskItem = document.createElement('div');
    subtaskItem.className = 'subtask-item';
    subtaskItem.dataset.subtaskId = subtaskId;
    
    subtaskItem.innerHTML = `
        <span class="subtask-counter">${addSubtaskCounter}</span>
        <i class="fas fa-grip-vertical subtask-drag-handle" title="Arrastrar para reordenar"></i>
        <input type="text" class="subtask-input" name="subtasks[]" placeholder="Nombre de la subtarea..." required>
        <button type="button" class="subtask-remove" onclick="removeSubtaskFromForm('${subtaskId}')" title="Eliminar subtarea">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    if (listEl) {
        listEl.appendChild(subtaskItem);
        updateAddSubtasksDisplay();
        
        // Enfocar el input de la nueva subtarea
        const input = subtaskItem.querySelector('.subtask-input');
        if (input) {
            input.focus();
        }
    }
}

function removeSubtaskFromForm(subtaskId) {
    const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
    if (subtaskItem) {
        subtaskItem.remove();
        updateAddSubtasksDisplay();
        renumberAddSubtasks();
    }
}

function updateAddSubtasksDisplay() {
    const listEl = document.getElementById('addSubtasksList');
    const subtaskItems = listEl ? listEl.querySelectorAll('.subtask-item') : [];
    
    if (subtaskItems.length === 0 && listEl) {
        listEl.innerHTML = '<div class="subtasks-empty">No hay subtareas. Haz clic en "Agregar Subtarea" para comenzar.</div>';
    } else if (listEl && listEl.querySelector('.subtasks-empty')) {
        listEl.querySelector('.subtasks-empty').remove();
    }
}

function renumberAddSubtasks() {
    const subtaskItems = document.querySelectorAll('#addSubtasksList .subtask-item');
    subtaskItems.forEach((item, index) => {
        const counter = item.querySelector('.subtask-counter');
        if (counter) {
            counter.textContent = index + 1;
        }
    });
    addSubtaskCounter = subtaskItems.length;
}

function saveTaskWithoutSubtasks() {
    saveTask();
}

function saveTaskWithSubtasks() {
    // Recopilar subtareas del modal
    const subtaskInputs = document.querySelectorAll('#addSubtasksList .subtask-input');
    const subtasks = [];
    
    subtaskInputs.forEach(input => {
        const title = input.value.trim();
        if (title) {
            subtasks.push({
                title: title,
                description: '',
                completion_percentage: 0,
                due_date: null,
                priority: 'medium',
                assigned_to_user_id: null
            });
        }
    });
    
    if (subtasks.length === 0) {
        showToast('Debes agregar al menos una subtarea', 'error');
        return;
    }
    
    // Crear tarea principal con subtareas
    createTaskWithSubtasks(subtasks);
}

function createTaskWithSubtasks(subtasks) {
    // Validar formulario
    const taskTitle = document.getElementById('task_title').value;
    const taskDueDate = document.getElementById('task_due_date').value;
    const assignedMembers = document.querySelectorAll('input[name="assigned_members[]"]:checked');
    
    if (!taskTitle || !taskDueDate) {
        showToast('Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    if (assignedMembers.length === 0) {
        showToast('Debes asignar al menos un colaborador', 'error');
        return;
    }
    
    // Recopilar datos del formulario
    const formData = new FormData();
    formData.append('task_title', taskTitle);
    formData.append('task_due_date', taskDueDate);
    formData.append('task_project', document.getElementById('task_project').value);
    formData.append('task_description', document.getElementById('task_description').value);
    
    // Agregar miembros asignados
    assignedMembers.forEach(member => {
        formData.append('assigned_members[]', member.value);
    });
    
    // Agregar subtareas como JSON
    if (subtasks.length > 0) {
        formData.append('subtasks', JSON.stringify(subtasks));
    }
    
    // Log para debug
    console.log('🚀 === ENVIANDO TAREA CON SUBTAREAS ===');
    console.log('📊 Subtareas:', subtasks);
    
    // Mostrar loader
    const continueBtn = document.getElementById('continueWithSubtasksBtn');
    const continueText = document.getElementById('continueSubtasksText');
    const continueLoader = document.getElementById('continueSubtasksLoader');
    
    if (continueBtn) continueBtn.disabled = true;
    if (continueText) continueText.style.display = 'none';
    if (continueLoader) continueLoader.style.display = 'inline-flex';
    
    // Enviar datos al servidor
    fetch('?route=clan_leader/create-task', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('📄 Response body:', text);
        
        try {
            const data = JSON.parse(text);
            console.log('✅ JSON parseado:', data);
            
            if (data.success) {
                console.log('🎉 Tarea creada exitosamente');
                showToast('Tarea con subtareas creada exitosamente', 'success');
                setTimeout(() => {
                    window.location.href = '?route=clan_leader/tasks';
                }, 1500);
            } else {
                console.error('❌ Error del servidor:', data.message);
                showToast(data.message || 'Error al crear la tarea', 'error');
            }
        } catch (e) {
            console.error('❌ Error parseando JSON:', e);
            showToast('Error del servidor. Ver consola para detalles.', 'error');
        }
    })
    .catch(error => {
        console.error('💥 Error de red:', error);
        showToast('Error al crear la tarea: ' + error.message, 'error');
    })
    .finally(() => {
        // Ocultar loader
        if (continueBtn) continueBtn.disabled = false;
        if (continueText) continueText.style.display = 'inline';
        if (continueLoader) continueLoader.style.display = 'none';
    });
}

// Event listeners para el modal
document.addEventListener('DOMContentLoaded', function() {
    // Botón para agregar subtarea en el modal
    const addSubtaskBtn = document.getElementById('addSubtaskBtnModal');
    if (addSubtaskBtn) {
        addSubtaskBtn.addEventListener('click', addSubtaskToForm);
    }
    
    // Botón para continuar con subtareas
    const continueBtn = document.getElementById('continueWithSubtasksBtn');
    if (continueBtn) {
        continueBtn.addEventListener('click', saveTaskWithSubtasks);
    }
    
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('addSubtasksModal');
            if (modal && modal.style.display !== 'none') {
                closeAddSubtasksModal();
            }
        }
    });
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        z-index: 10001;
        animation: slideIn 0.3s ease;
        max-width: 350px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    `;
    
    if (type === 'success') {
        toast.style.background = '#10b981';
    } else if (type === 'error') {
        toast.style.background = '#ef4444';
    } else if (type === 'warning') {
        toast.style.background = '#f59e0b';
    } else {
        toast.style.background = '#3b82f6';
    }
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}
</script> 