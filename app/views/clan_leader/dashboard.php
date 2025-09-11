<?php
// Guardar el contenido en una variable
ob_start();
?>

<!-- Cargar CSS de rediseño -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/clan-leader-redesign.css">

<style>
/* Tabs Minimalistas para Dashboard */
.tabs-container-minimal {
    margin-bottom: 32px;
}

.tabs-wrapper-minimal {
    display: flex;
    background: #f8fafc;
    border-radius: 12px;
    padding: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    margin: 0 auto;
}

.tab-minimal {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    background: transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    color: #64748b;
    position: relative;
    overflow: hidden;
}

.tab-minimal:hover {
    background: rgba(100, 116, 139, 0.1);
    color: #475569;
}

.tab-minimal.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transform: translateY(-1px);
}

.tab-minimal i {
    font-size: 16px;
    transition: all 0.3s ease;
}

.tab-minimal.active i {
    transform: scale(1.1);
}

.tab-minimal span {
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.025em;
}

/* Responsive */
@media (max-width: 768px) {
    .tabs-wrapper-minimal {
        max-width: 100%;
        margin: 0 16px;
    }
    
    .tab-minimal {
        padding: 10px 16px;
        gap: 6px;
    }
    
    .tab-minimal span {
        font-size: 13px;
    }
    
    .tab-minimal i {
        font-size: 14px;
    }
}

/* Estilos específicos para el Kanban */
.kanban-board-compact {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    overflow-x: auto;
    min-height: 500px;
}

.kanban-column-compact {
    flex: 1;
    min-width: 280px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.column-header {
    padding: 16px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e2e8f0;
}

.column-header.overdue {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}

.column-header.today {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.column-header.week1 {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.column-header.week2 {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #16a34a;
}

.task-count {
    background: rgba(255, 255, 255, 0.8);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
}

.column-content-compact {
    padding: 12px;
    max-height: 600px;
    overflow-y: auto;
}

/* Task Cards */
.task-card-mini {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.task-card-mini:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.subtask-card-micro {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #64748b;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 6px;
    font-size: 13px;
    opacity: 0.9;
}

.task-header-mini {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.task-checkbox-mini {
    margin-top: 2px;
    cursor: pointer;
}

.task-name-mini {
    flex: 1;
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 4px;
}

.task-name-link {
    color: inherit;
    text-decoration: none;
    cursor: pointer;
    transition: color 0.2s ease;
    flex: 1;
}

.task-name-link:hover {
    color: #667eea;
    text-decoration: underline;
}

.task-name-link:visited {
    color: inherit;
}

.subtask-icon {
    color: #64748b;
    font-size: 12px;
    margin-right: 4px;
}

.parent-task-hint {
    color: #64748b;
    font-size: 11px;
    margin-left: 4px;
}

.task-tags-mini {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 8px;
}

.task-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.project-tag.personal {
    background: #fef2f2;
    color: #dc2626;
}

.project-tag.clan, .project-tag.team {
    background: #eff6ff;
    color: #2563eb;
}

.assignee-tag {
    background: #f3e8ff;
    color: #7c3aed;
}

.due-tag.overdue {
    background: #fef2f2;
    color: #dc2626;
}

.due-tag.today {
    background: #fefbeb;
    color: #d97706;
}

.due-tag.week1 {
    background: #eff6ff;
    color: #2563eb;
}

.due-tag.week2 {
    background: #f0fdf4;
    color: #16a34a;
}

.loading-message {
    text-align: center;
    padding: 40px;
    color: #64748b;
    font-size: 16px;
}

.loading-message i {
    font-size: 24px;
    margin-bottom: 8px;
    color: #3b82f6;
}

/* Responsive para Kanban */
@media (max-width: 768px) {
    .kanban-board-compact {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }
    
    .kanban-column-compact {
        min-width: auto;
    }
}

/* Estilos para el Modal */
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
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    color: #374151;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-close {
    background: none;
    border: none;
    color: #6b7280;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #374151;
    transform: scale(1.1);
}

.modal-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group label i {
    color: #667eea;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-row {
    display: flex;
    gap: 16px;
}

.form-row .form-group {
    flex: 1;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f8fafc;
}

.btn-primary, .btn-secondary {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
}

/* Header Mejorado - Copiado exacto de tasks.php */
.page-header {
    background: transparent;
    color: inherit;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #e0e7ff;
    border: 1px solid #c7d2fe;
    border-radius: 12px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: #1e3a8a;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #374151;
    margin: 0;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-create {
    background: #1e3a8a;
    color: #ffffff !important;
    padding: 0.6rem 1.1rem;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
}

.btn-create:hover {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
}

.btn-create i {
    font-size: 0.85rem;
}

/* Reset y Base - Copiado exacto de tasks.php */
.clan-leader-tasks-container {
    min-height: 100vh;
    background: transparent;
    padding: 0;
    margin: 0;
}

/* Contenido Principal */
.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem 2rem 1.5rem;
}

/* Responsive para Modal */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .modal-header, .modal-body, .modal-footer {
        padding: 16px 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
    }
    
    .header-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    .main-content {
        padding: 0 1rem 2rem 1rem;
    }
}
</style>

<div class="clan-leader-tasks-container">
    <!-- Header Mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">Panel de Tareas</h1>
                <p class="page-subtitle">Gestiona tus tareas de manera visual y eficiente</p>
            </div>
            <div class="header-actions">
                <button class="btn-create" onclick="openCreateTaskModal()">
                    <i class="fas fa-plus"></i>
                    Nueva Tarea
                </button>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Tabs Minimalistas -->
        <div class="tabs-container-minimal">
            <div class="tabs-wrapper-minimal">
                <button class="tab-minimal active" onclick="switchKanbanTab('my-tasks')" id="my-tasks-kanban-tab">
                    <i class="fas fa-user"></i>
                    <span>Mis Tareas</span>
                </button>
                <button class="tab-minimal" onclick="switchKanbanTab('team-tasks')" id="team-tasks-kanban-tab">
                    <i class="fas fa-users"></i>
                    <span>Equipo</span>
                </button>
            </div>
        </div>

        <!-- Tab Content: Mis Tareas -->
        <div id="my-tasks-kanban-content" class="kanban-tab-content active" style="display: block;">
            <div id="my-tasks-kanban-board" class="kanban-board-compact">
                <div class="loading-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    <br>Cargando mis tareas...
                </div>
            </div>
        </div>

        <!-- Tab Content: Equipo -->
        <div id="team-tasks-kanban-content" class="kanban-tab-content" style="display: none;">
            <div id="team-tasks-kanban-board" class="kanban-board-compact">
                <div class="loading-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    <br>Cargando tareas del equipo...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear tarea -->
<div id="createTaskModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Agregar Nueva Tarea Personal</h3>
            <button type="button" class="modal-close" onclick="closeCreateTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="createTaskForm" class="modal-body">
            <!-- Campo oculto para asignar la tarea al usuario actual -->
            <input type="hidden" name="assigned_members[]" value="<?php echo $_SESSION['user_id']; ?>">
            <!-- Campo oculto para el proyecto personal -->
            <input type="hidden" id="personal_project_id" name="task_project" value="">
            
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-tasks"></i>
                    Nombre de la Tarea *
                </label>
                <input type="text" id="title" name="task_title" required 
                       placeholder="Ej: Revisar documentación del proyecto" 
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Descripción
                </label>
                <textarea id="description" name="task_description" 
                          placeholder="Descripción detallada de la tarea (opcional)"
                          class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="due_date">
                    <i class="fas fa-calendar"></i>
                    Fecha de Vencimiento *
                </label>
                <input type="date" id="due_date" name="task_due_date" required class="form-control">
            </div>
            
            <!-- Campo oculto para prioridad por defecto -->
            <input type="hidden" name="priority" value="medium">
        </form>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeCreateTaskModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button type="button" class="btn-primary" onclick="createTask()">
                <i class="fas fa-plus"></i>
                Crear Tarea
            </button>
        </div>
    </div>
</div>

<script>
console.log('🚀 Dashboard Kanban cargado');

// Función para cambiar entre tabs del Kanban
function switchKanbanTab(tabName) {
    console.log('🔄 Cambiando a tab:', tabName);
    
    // Ocultar todos los tab contents
    document.querySelectorAll('.kanban-tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // Remover active de todos los tab buttons
    document.querySelectorAll('.tab-minimal').forEach(button => {
        button.classList.remove('active');
    });
    
    // Mostrar el tab seleccionado
    const targetContent = document.getElementById(tabName + '-kanban-content');
    if (targetContent) {
        targetContent.classList.add('active');
        targetContent.style.display = 'block';
        console.log('✅ Tab content mostrado:', tabName + '-kanban-content');
    }
    
    // Activar el tab button seleccionado
    const targetButton = document.getElementById(tabName + '-kanban-tab');
    if (targetButton) {
        targetButton.classList.add('active');
        console.log('✅ Tab button activado:', tabName + '-kanban-tab');
    }
    
    // Cargar datos del tab activo
    if (tabName === 'my-tasks') {
        loadMyKanbanTasks();
    } else if (tabName === 'team-tasks') {
        loadTeamKanbanTasks();
    }
}

// Cargar mis tareas para Kanban
function loadMyKanbanTasks() {
    console.log('🔄 Cargando mis tareas Kanban...');
    const kanbanBoard = document.getElementById('my-tasks-kanban-board');
    
    kanbanBoard.innerHTML = '<div class="loading-message"><i class="fas fa-spinner fa-spin"></i><br>Cargando mis tareas...</div>';
    
    fetch('?route=clan_leader/get-my-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('📋 Respuesta mis tareas:', data);
            if (data.success && data.kanbanTasks) {
                renderMyKanbanBoard(data.kanbanTasks);
            } else {
                kanbanBoard.innerHTML = '<div class="loading-message">Error: ' + (data.message || 'Error desconocido') + '</div>';
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            kanbanBoard.innerHTML = '<div class="loading-message">Error de conexión</div>';
        });
}

// Cargar tareas del equipo para Kanban
function loadTeamKanbanTasks() {
    console.log('🔄 Cargando tareas del equipo Kanban...');
    const kanbanBoard = document.getElementById('team-tasks-kanban-board');
    
    kanbanBoard.innerHTML = '<div class="loading-message"><i class="fas fa-spinner fa-spin"></i><br>Cargando tareas del equipo...</div>';
    
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('👥 Respuesta equipo:', data);
            if (data.success && data.kanbanTasks) {
                renderTeamKanbanBoard(data.kanbanTasks);
            } else {
                kanbanBoard.innerHTML = '<div class="loading-message">Error: ' + (data.message || 'Error desconocido') + '</div>';
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            kanbanBoard.innerHTML = '<div class="loading-message">Error de conexión</div>';
        });
}

// Renderizar tablero Kanban personal
function renderMyKanbanBoard(kanbanTasks) {
    console.log('🎨 Renderizando tablero personal');
    const kanbanBoard = document.getElementById('my-tasks-kanban-board');
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnTitles = {
        'vencidas': 'Vencidas',
        'hoy': 'Hoy',
        'semana1': '1 Semana',
        'semana2': '2+ Semanas'
    };
    const columnClasses = {
        'vencidas': 'overdue',
        'hoy': 'today',
        'semana1': 'week1',
        'semana2': 'week2'
    };
    
    let html = '';
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        const columnClass = columnClasses[column];
        
        html += `<div class="kanban-column-compact">
            <div class="column-header ${columnClass}">
                <h4>${columnTitles[column]}</h4>
                <span class="task-count">${tasks.length}</span>
            </div>
            <div class="column-content-compact">`;
        
        tasks.forEach(task => {
            const isSubtask = task.item_type === 'subtask';
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            const isPersonal = task.is_personal == 1;
            
            html += `<div class="${cardClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-header-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatusKanban(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        <a href="#" onclick="goToTaskDetail(${isSubtask ? (task.parent_task_id || task.task_id) : task.task_id}, '${isSubtask ? 'subtask' : 'task'}'); return false;" class="task-name-link">
                            ${task.task_name || 'Sin nombre'}
                        </a>
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag ${isPersonal ? 'personal' : 'clan'}">
                        ${isPersonal ? 'Personal' : (task.project_name || 'Proyecto')}
                    </span>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    });
    
    kanbanBoard.innerHTML = html;
}

// Renderizar tablero Kanban del equipo
function renderTeamKanbanBoard(kanbanTasks) {
    console.log('🎨 Renderizando tablero del equipo');
    const kanbanBoard = document.getElementById('team-tasks-kanban-board');
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnTitles = {
        'vencidas': 'Vencidas',
        'hoy': 'Hoy',
        'semana1': '1 Semana',
        'semana2': '2+ Semanas'
    };
    const columnClasses = {
        'vencidas': 'overdue',
        'hoy': 'today',
        'semana1': 'week1',
        'semana2': 'week2'
    };
    
    let html = '';
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        const columnClass = columnClasses[column];
        
        html += `<div class="kanban-column-compact">
            <div class="column-header ${columnClass}">
                <h4>${columnTitles[column]}</h4>
                <span class="task-count">${tasks.length}</span>
            </div>
            <div class="column-content-compact">`;
        
        tasks.forEach(task => {
            const isSubtask = task.item_type === 'subtask';
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            
            html += `<div class="${cardClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-header-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatusKanban(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        <a href="#" onclick="goToTaskDetail(${isSubtask ? (task.parent_task_id || task.task_id) : task.task_id}, '${isSubtask ? 'subtask' : 'task'}'); return false;" class="task-name-link">
                            ${task.task_name || 'Sin nombre'}
                        </a>
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag team">
                        ${task.project_name || 'Proyecto'}
                    </span>
                    ${task.assigned_user_name ? `<span class="task-tag assignee-tag">${task.assigned_user_name}</span>` : ''}
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    });
    
    kanbanBoard.innerHTML = html;
}

// Toggle status de tarea en Kanban
function toggleTaskStatusKanban(taskId, isChecked, itemType = 'task') {
    console.log(`🔄 Toggle status: ID=${taskId}, Checked=${isChecked}, Type=${itemType}`);
    
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('status', isChecked ? 'completed' : 'pending');
    formData.append('item_type', itemType);
    
    fetch('?route=clan_leader/update-task-status', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Status actualizado');
            
            const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskCard && isChecked) {
                // Animar desaparición
                taskCard.style.transition = 'all 0.5s ease';
                taskCard.style.opacity = '0';
                taskCard.style.transform = 'translateX(100px)';
                
                setTimeout(() => {
                    taskCard.remove();
                    updateColumnCount(taskCard.closest('.kanban-column-compact'));
                }, 500);
            } else if (taskCard && !isChecked) {
                // Recargar si se desmarca
                setTimeout(() => {
                    const activeTab = document.querySelector('.tab-minimal.active');
                    if (activeTab && activeTab.id === 'my-tasks-kanban-tab') {
                        loadMyKanbanTasks();
                    } else if (activeTab && activeTab.id === 'team-tasks-kanban-tab') {
                        loadTeamKanbanTasks();
                    }
                }, 300);
            }
        } else {
            console.error('❌ Error:', data.message);
            // Revertir checkbox
            const checkbox = document.querySelector(`[data-task-id="${taskId}"] .task-checkbox-mini`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        // Revertir checkbox
        const checkbox = document.querySelector(`[data-task-id="${taskId}"] .task-checkbox-mini`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
    });
}

// Actualizar contador de columna
function updateColumnCount(column) {
    if (!column) return;
    
    const taskCount = column.querySelectorAll('.task-card-mini, .subtask-card-micro').length;
    const countElement = column.querySelector('.task-count');
    if (countElement) {
        countElement.textContent = taskCount;
    }
}

// Función para abrir modal de crear tarea
function openCreateTaskModal() {
    console.log('🔄 Abrir modal crear tarea');
    const modal = document.getElementById('createTaskModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Establecer fecha mínima como hoy
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').min = today;
        
        // Obtener ID del proyecto personal
        getPersonalProjectId();
    }
}

// Función para obtener el ID del proyecto personal
function getPersonalProjectId() {
    fetch('?route=clan_leader/get-personal-project-id')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.project_id) {
                document.getElementById('personal_project_id').value = data.project_id;
                console.log('✅ Proyecto personal ID:', data.project_id);
            } else {
                console.error('❌ Error obteniendo proyecto personal:', data.message);
                // Mantener valor vacío si no se puede obtener
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión obteniendo proyecto personal:', error);
        });
}


// Función para cerrar modal
function closeCreateTaskModal() {
    const modal = document.getElementById('createTaskModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        // Limpiar formulario
        document.getElementById('createTaskForm').reset();
    }
}

// Función para ir al detalle de tarea/subtarea
function goToTaskDetail(taskId, itemType = 'task') {
    console.log(`🔗 Navegando al detalle: ${itemType} - Task ID ${taskId}`);
    
    if (!taskId || taskId <= 0) {
        console.error('❌ ID de tarea inválido:', taskId);
        return;
    }
    
    // Usar la ruta correcta para detalles de tarea
    let url;
    if (itemType === 'subtask') {
        // Para subtareas, navegar al detalle de la tarea padre
        console.log(`📋 Subtarea detectada - navegando a tarea padre ID: ${taskId}`);
        url = `?route=clan_leader/get-task-details&task_id=${taskId}&type=subtask`;
    } else {
        // Para tareas normales
        console.log(`📝 Tarea normal - navegando a tarea ID: ${taskId}`);
        url = `?route=clan_leader/get-task-details&task_id=${taskId}`;
    }
    
    console.log('🚀 Redirigiendo a:', url);
    window.location.href = url;
}

// Función para crear tarea
function createTask() {
    const form = document.getElementById('createTaskForm');
    const formData = new FormData(form);
    
    // Validaciones básicas
    const title = formData.get('task_title');
    const dueDate = formData.get('task_due_date');
    
    if (!title || title.trim().length < 3) {
        alert('El nombre de la tarea debe tener al menos 3 caracteres');
        return;
    }
    
    if (!dueDate) {
        alert('La fecha de vencimiento es requerida');
        return;
    }
    
    console.log('📝 Creando tarea:', title);
    
    fetch('?route=clan_leader/create-task', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Tarea creada exitosamente');
            closeCreateTaskModal();
            
            // Recargar el tab activo
            const activeTab = document.querySelector('.tab-minimal.active');
            if (activeTab && activeTab.id === 'my-tasks-kanban-tab') {
                loadMyKanbanTasks();
            } else if (activeTab && activeTab.id === 'team-tasks-kanban-tab') {
                loadTeamKanbanTasks();
            }
            
            // Mostrar mensaje de éxito
            alert('Tarea creada exitosamente');
        } else {
            console.error('❌ Error:', data.message);
            alert('Error al crear tarea: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error de conexión al crear tarea');
    });
}

// Inicializar dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo - Iniciando dashboard');
    switchKanbanTab('my-tasks');
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('createTaskModal');
            if (modal && modal.style.display === 'flex') {
                closeCreateTaskModal();
            }
        }
    });
    
    // Cerrar modal al hacer clic fuera de él
    document.getElementById('createTaskModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreateTaskModal();
        }
    });
});
</script>

<?php
// Guardar el contenido generado
$content = ob_get_clean();

// Incluir el layout con el contenido
include __DIR__ . '/../layout.php';
?>
