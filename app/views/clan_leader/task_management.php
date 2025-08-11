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

                <!-- Sección de Subtareas - OCULTA -->
                <!-- <div class="form-section">
                    <div class="section-header">
                        <h3>Subtareas</h3>
                        <button class="btn-minimal small" onclick="addSubtask()">
                            <i class="fas fa-plus"></i>
                            Agregar Subtarea
                        </button>
                    </div>
                    
                    <div id="subtasks-container">
                        <!-- Las subtareas se agregarán dinámicamente aquí -->
                    </div>
                </div> -->

                <!-- Sección de Asignación de Colaboradores -->
                <div class="form-section">
                    <h3>Asignar colaboradores:</h3>
                    
                    <div class="collaborators-grid">
                        <?php foreach ($members as $member): ?>
                            <div class="collaborator-card" data-user-id="<?php echo $member['user_id']; ?>">
                                <div class="collaborator-checkbox">
                                    <input type="checkbox" 
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
                                                        <span>Vence: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></span>
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

<!-- Template para Subtareas - OCULTO -->
<!-- <template id="subtask-template">
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
                <div class="form-group full-width">
                    <label>Descripción</label>
                    <textarea name="subtasks[{index}][description]" rows="2" placeholder="Descripción de la subtarea..."></textarea>
                </div>
            </div>
        </div>
    </div>
</template> -->



<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js',
    APP_URL . 'assets/js/task-management.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 