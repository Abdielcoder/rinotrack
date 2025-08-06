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
                    <i class="fas fa-edit"></i>
                </div>
                <div class="header-text">
                    <h1>Editar Tarea</h1>
                    <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?></span>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="btn-minimal secondary" onclick="closeTaskEdit()">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button class="btn-minimal primary" onclick="updateTask()">
                    <i class="fas fa-save"></i>
                    Actualizar Tarea
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
                            <input type="text" id="task_title" name="task_title" placeholder="Título de la tarea *" required value="<?= htmlspecialchars($task['task_name']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="task_due_date">Fecha límite *</label>
                            <div class="date-input-wrapper">
                                <input type="date" id="task_due_date" name="task_due_date" required value="<?= $task['due_date'] ?>">
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
                                                <?php echo ($task['project_id'] == $project['project_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['project_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="priority">Prioridad</label>
                            <div class="select-wrapper">
                                <select id="priority" name="priority">
                                    <option value="low" <?= ($task['priority'] == 'low') ? 'selected' : '' ?>>Baja</option>
                                    <option value="medium" <?= ($task['priority'] == 'medium') ? 'selected' : '' ?>>Media</option>
                                    <option value="high" <?= ($task['priority'] == 'high') ? 'selected' : '' ?>>Alta</option>
                                    <option value="critical" <?= ($task['priority'] == 'critical') ? 'selected' : '' ?>>Urgente</option>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="task_status">Estado</label>
                            <div class="select-wrapper">
                                <select id="task_status" name="task_status">
                                    <option value="pending" <?= ($task['status'] == 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="in_progress" <?= ($task['status'] == 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="completed" <?= ($task['status'] == 'completed') ? 'selected' : '' ?>>Completada</option>
                                    <option value="cancelled" <?= ($task['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="assigned_to_user_id">Asignar a</label>
                            <div class="select-wrapper">
                                <select id="assigned_to_user_id" name="assigned_to_user_id">
                                    <option value="">Sin asignar</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['user_id']; ?>" 
                                                <?php echo ($task['assigned_to_user_id'] == $member['user_id']) ? 'selected' : ''; ?>>
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
                            <label for="task_description">Descripción</label>
                            <textarea id="task_description" name="task_description" rows="3" placeholder="Descripción de la tarea..."><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Información de la Tarea -->
                <div class="form-section">
                    <h3>Información de la Tarea</h3>
                    <div class="task-info-grid">
                        <div class="info-item">
                            <span class="info-label">Creada por:</span>
                            <span class="info-value"><?= htmlspecialchars($task['created_by_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de creación:</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($task['created_at'])) ?></span>
                        </div>
                        <?php if ($task['completion_percentage'] > 0): ?>
                        <div class="info-item">
                            <span class="info-label">Progreso:</span>
                            <span class="info-value"><?= $task['completion_percentage'] ?>%</span>
                        </div>
                        <?php endif; ?>
                        <?php if ($task['completed_at']): ?>
                        <div class="info-item">
                            <span class="info-label">Completada el:</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($task['completed_at'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Campo oculto para el ID de la tarea -->
<input type="hidden" id="task_id" value="<?= $task['task_id'] ?>">

<style>
/* Estilos para la edición de tareas */
.task-management-fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f8fafc;
    z-index: 1000;
    overflow-y: auto;
}

.task-management-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.task-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.header-text h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
}

.subtitle {
    color: #718096;
    font-size: 0.9rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-minimal {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-minimal.primary {
    background: #3b82f6;
    color: white;
}

.btn-minimal.primary:hover {
    background: #2563eb;
}

.btn-minimal.secondary {
    background: #f1f5f9;
    color: #64748b;
}

.btn-minimal.secondary:hover {
    background: #e2e8f0;
}

.task-management-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.task-form-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.task-main-form {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h3 {
    margin: 0 0 1.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a202c;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-row .full-width {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.select-wrapper {
    position: relative;
}

.select-wrapper select {
    width: 100%;
    appearance: none;
    padding-right: 2.5rem;
}

.select-wrapper i {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.date-input-wrapper {
    position: relative;
}

.date-input-wrapper input {
    width: 100%;
    padding-right: 2.5rem;
}

.date-input-wrapper i {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

/* Información de la tarea */
.task-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #e2e8f0;
}

.info-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.85rem;
}

.info-value {
    font-weight: 500;
    color: #1a202c;
    font-size: 0.9rem;
}

.status-pending {
    color: #d97706;
}

.status-in_progress {
    color: #3b82f6;
}

.status-completed {
    color: #059669;
}

.status-cancelled {
    color: #dc2626;
}

/* Estilos para el selector de estado */
#task_status option[value="pending"] {
    color: #92400e;
}

#task_status option[value="in_progress"] {
    color: #1e40af;
}

#task_status option[value="completed"] {
    color: #065f46;
}

#task_status option[value="cancelled"] {
    color: #991b1b;
}

/* Responsive */
@media (max-width: 768px) {
    .task-management-header {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    .task-management-content {
        padding: 1rem;
    }
    
    .task-main-form {
        padding: 1rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .task-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function closeTaskEdit() {
    // Redirigir de vuelta a la página de tareas
    window.location.href = '?route=clan_leader/tasks';
}

function updateTask() {
    // Obtener los valores del formulario
    const taskId = document.getElementById('task_id').value;
    const taskName = document.getElementById('task_title').value;
    const taskDescription = document.getElementById('task_description').value;
    const taskProject = document.getElementById('task_project').value;
    const taskDueDate = document.getElementById('task_due_date').value;
    const priority = document.getElementById('priority').value;
    const taskStatus = document.getElementById('task_status').value;
    const assignedToUserId = document.getElementById('assigned_to_user_id').value;
    
    // Validaciones básicas
    if (!taskName.trim()) {
        showToast('El título de la tarea es requerido', 'error');
        return;
    }
    
    if (!taskDueDate) {
        showToast('La fecha límite es requerida', 'error');
        return;
    }
    
    if (!taskProject) {
        showToast('Debe seleccionar un proyecto', 'error');
        return;
    }
    
    // Crear objeto con los datos
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('task_name', taskName);
    formData.append('task_description', taskDescription);
    formData.append('task_project', taskProject);
    formData.append('task_due_date', taskDueDate);
    formData.append('priority', priority);
    formData.append('task_status', taskStatus);
    formData.append('assigned_to_user_id', assignedToUserId);
    
    // Enviar solicitud
    fetch('?route=clan_leader/update-task', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Tarea actualizada exitosamente', 'success');
            setTimeout(() => {
                window.location.href = '?route=clan_leader/tasks';
            }, 1500);
        } else {
            showToast('Error al actualizar la tarea: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al actualizar la tarea', 'error');
    });
}

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
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 350px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    `;
    
    if (type === 'success') {
        toast.style.background = '#10b981';
    } else if (type === 'error') {
        toast.style.background = '#ef4444';
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

// Estilos para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php
// Obtener el contenido y limpiar el buffer
$content = ob_get_clean();
echo $content;
?> 