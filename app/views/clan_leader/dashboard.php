<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="kanban-section">
    <div class="kanban-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-columns"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">Dashboard Kanban</h1>
                    <p class="page-subtitle">Gestiona tus tareas de manera visual y eficiente</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="stats-summary">
                    <div class="stat-item">
                        <div class="stat-number" id="total-tasks">0</div>
                        <div class="stat-label">Tareas Totales</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="completed-tasks">0</div>
                        <div class="stat-label">Completadas</div>
                    </div>
                </div>
                <div class="header-buttons">
                    <button class="btn-primary" onclick="openCreateTaskModal()">
                        <i class="fas fa-plus"></i>
                        Agregar Tarea
                    </button>
                </div>
            </div>
            
            <!-- Tabs para separar Mis Tareas y Equipo -->
            <div class="kanban-tabs">
                <button class="kanban-tab-button active" onclick="switchKanbanTab('my-tasks')" id="my-tasks-kanban-tab">
                    <i class="fas fa-user"></i>
                    Mis Tareas
                </button>
                <button class="kanban-tab-button" onclick="switchKanbanTab('team-tasks')" id="team-tasks-kanban-tab">
                    <i class="fas fa-users"></i>
                    Equipo
                </button>
            </div>
            
            <!-- Tab Content: Mis Tareas -->
            <div id="my-tasks-kanban-content" class="kanban-tab-content active" style="display: block;">
                <div id="my-tasks-kanban-board" class="kanban-board-compact">
                    <!-- El contenido se carga dinámicamente -->
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando tus tareas...</p>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Equipo (tablero dinámico) -->
            <div id="team-tasks-kanban-content" class="kanban-tab-content" style="display: none;">
                <div id="team-tasks-kanban-board" class="kanban-board-compact">
                    <!-- El contenido se carga dinámicamente mediante JavaScript -->
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando tareas del equipo...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let myKanbanTasks = [];
let teamKanbanTasks = [];

// Función para cambiar entre tabs del Kanban
function switchKanbanTab(tabName) {
    console.log('🔄 Cambiando a tab:', tabName);
    
    // Ocultar todos los tab contents
    document.querySelectorAll('.kanban-tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // Remover active de TODOS los tab buttons
    document.querySelectorAll('.kanban-tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Mostrar SOLO el tab seleccionado
    const targetContent = document.getElementById(tabName + '-kanban-content');
    if (targetContent) {
        targetContent.classList.add('active');
        targetContent.style.display = 'block';
        console.log('✅ Tab content mostrado:', tabName + '-kanban-content');
    } else {
        console.error('🔴 No se encontró tab content:', tabName + '-kanban-content');
    }
    
    // Activar SOLO el tab button seleccionado
    const targetButton = document.getElementById(tabName + '-kanban-tab');
    if (targetButton) {
        targetButton.classList.add('active');
        console.log('✅ Tab button activado:', tabName + '-kanban-tab');
    } else {
        console.error('🔴 No se encontró tab button:', tabName + '-kanban-tab');
    }
    
    // Cargar datos SOLO del tab activo
    if (tabName === 'my-tasks') {
        loadMyKanbanTasks();
    } else if (tabName === 'team-tasks') {
        loadTeamKanbanTasks();
    }
}

// Cargar tareas personales para Kanban
function loadMyKanbanTasks() {
    console.log('🔄 Cargando mis tareas Kanban...');
    fetch('?route=clan_leader/my-kanban-tasks')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            myKanbanTasks = data.tasks || [];
            console.log('✅ Mis tareas Kanban cargadas:', myKanbanTasks.length);
            renderMyKanbanBoard(myKanbanTasks);
            updateKanbanStats();
        } else {
            console.error('❌ Error al cargar mis tareas Kanban:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ Error de conexión al cargar mis tareas Kanban:', error);
    });
}

// Cargar tareas del equipo para Kanban
function loadTeamKanbanTasks() {
    console.log('🔄 Cargando tareas del equipo Kanban...');
    fetch('?route=clan_leader/team-kanban-tasks')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            teamKanbanTasks = data.tasks || [];
            console.log('✅ Tareas del equipo Kanban cargadas:', teamKanbanTasks.length);
            renderTeamKanbanBoard(teamKanbanTasks);
            updateKanbanStats();
        } else {
            console.error('❌ Error al cargar tareas del equipo Kanban:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ Error de conexión al cargar tareas del equipo Kanban:', error);
    });
}

// Renderizar tablero Kanban personal
function renderMyKanbanBoard(tasks) {
    console.log('🎨 Renderizando tablero Kanban personal con', tasks.length, 'tareas');
    const board = document.getElementById('my-tasks-kanban-board');
    if (!board) return;

    const columns = {
        'overdue': [],
        'today': [],
        'week1': [],
        'week2plus': []
    };

    const columnTitles = {
        'overdue': 'Vencidas',
        'today': 'Hoy',
        'week1': '1 Semana',
        'week2plus': '2+ Semanas'
    };

    // Clasificar tareas por columnas
    tasks.forEach(task => {
        const column = getKanbanColumn(task.due_date);
        if (columns[column]) {
            columns[column].push(task);
        }
    });

    let html = '';
    Object.keys(columns).forEach(column => {
        const tasks = columns[column];
        const columnClass = `column-${column}`;
        
        html += `
            <div class="kanban-column-compact ${columnClass}">
                <div class="column-header ${columnClass}">
                    <h4>${columnTitles[column]}</h4>
                    <span class="task-count">${tasks.length}</span>
                </div>
                <div class="column-content">`;
        
        tasks.forEach(task => {
            const isSubtask = task.item_type === 'subtask';
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            const isPersonal = true; // Es el tablero personal
            
            console.log(`🟢 Renderizando: ID=${task.task_id}, Name="${task.task_name}", Type=${task.item_type}, Personal=${isPersonal}, Project="${task.project_name}"`);
            
            html += `<div class="${cardClass} ${columnClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-content-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatusKanban(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        ${task.task_name || 'Sin nombre'}
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag ${isPersonal ? 'personal' : 'clan'} ${isSubtask ? 'subtask-tag' : ''}">
                        ${task.project_name ? 
                            (isPersonal ? '<i class="fas fa-user"></i>' : '<i class="fas fa-users"></i>') :
                            '<i class="fas fa-inbox"></i>'
                        }
                    </span>
                    ${task.assigned_user_name ? `<span class="task-tag assignee-tag ${isSubtask ? 'subtask-tag' : ''}" title="${task.assigned_user_name}"><i class="fas fa-user-tag"></i></span>` : ''}
                    <span class="task-tag due-tag ${columnClass} ${isSubtask ? 'subtask-tag' : ''}">
                        ${column === 'overdue' ? '<i class="fas fa-exclamation-triangle"></i>' :
                          column === 'today' ? '<i class="fas fa-clock"></i>' :
                          column === 'week1' ? '<i class="fas fa-calendar-week"></i>' :
                          '<i class="fas fa-calendar-plus"></i>'}
                    </span>
                </div>
            </div>`;
        });

        html += '</div></div>';
    });

    board.innerHTML = html;
}

// Renderizar tablero Kanban del equipo
function renderTeamKanbanBoard(tasks) {
    console.log('🎨 Renderizando tablero Kanban del equipo con', tasks.length, 'tareas');
    const board = document.getElementById('team-tasks-kanban-board');
    if (!board) return;

    const columns = {
        'overdue': [],
        'today': [],
        'week1': [],
        'week2plus': []
    };

    const columnTitles = {
        'overdue': 'Vencidas',
        'today': 'Hoy',
        'week1': '1 Semana',
        'week2plus': '2+ Semanas'
    };

    // Clasificar tareas por columnas
    tasks.forEach(task => {
        const column = getKanbanColumn(task.due_date);
        if (columns[column]) {
            columns[column].push(task);
        }
    });

    let html = '';
    Object.keys(columns).forEach(column => {
        const tasks = columns[column];
        const columnClass = `column-${column}`;
        
        html += `
            <div class="kanban-column-compact ${columnClass}">
                <div class="column-header ${columnClass}">
                    <h4>${columnTitles[column]}</h4>
                    <span class="task-count">${tasks.length}</span>
                </div>
                <div class="column-content">`;
        
        tasks.forEach(task => {
            const isSubtask = task.item_type === 'subtask';
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            
            console.log(`🟡 Renderizando Team: ID=${task.task_id}, Name="${task.task_name}", Type=${task.item_type}, Assigned="${task.assigned_user_name}"`);
            
            html += `<div class="${cardClass} ${columnClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-content-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatusKanban(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        ${task.task_name || 'Sin nombre'}
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag team ${isSubtask ? 'subtask-tag' : ''}" title="${task.project_name || 'Sin proyecto'}">
                        <i class="fas fa-users"></i>
                    </span>
                    ${task.assigned_user_name ? `<span class="task-tag assignee-tag ${isSubtask ? 'subtask-tag' : ''}" title="${task.assigned_user_name}"><i class="fas fa-user-tag"></i></span>` : ''}
                    <span class="task-tag due-tag ${columnClass} ${isSubtask ? 'subtask-tag' : ''}">
                        ${column === 'overdue' ? '<i class="fas fa-exclamation-triangle"></i>' :
                          column === 'today' ? '<i class="fas fa-clock"></i>' :
                          column === 'week1' ? '<i class="fas fa-calendar-week"></i>' :
                          '<i class="fas fa-calendar-plus"></i>'}
                    </span>
                </div>
            </div>`;
        });

        html += '</div></div>';
    });

    board.innerHTML = html;
}

// Determinar columna Kanban según fecha de vencimiento
function getKanbanColumn(dueDate) {
    if (!dueDate) return 'week2plus';
    
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'overdue';
    if (diffDays === 0) return 'today';
    if (diffDays <= 7) return 'week1';
    return 'week2plus';
}

// Actualizar estadísticas del Kanban
function updateKanbanStats() {
    const activeTab = document.querySelector('.kanban-tab-button.active');
    const tasks = activeTab && activeTab.id === 'my-tasks-kanban-tab' ? myKanbanTasks : teamKanbanTasks;
    
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.status === 'completed').length;
    
    document.getElementById('total-tasks').textContent = totalTasks;
    document.getElementById('completed-tasks').textContent = completedTasks;
}

// Toggle status de tarea en Kanban
function toggleTaskStatusKanban(taskId, isChecked, itemType = 'task') {
    console.log(`🔄 Toggle status Kanban: ID=${taskId}, Checked=${isChecked}, Type=${itemType}`);
    
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
            console.log(`✅ Status actualizado: ${taskId} -> ${isChecked ? 'completed' : 'pending'}`);
            
            const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskCard && isChecked) {
                // Animar la desaparición del card
                taskCard.style.transition = 'all 0.5s ease-out';
                taskCard.style.opacity = '0';
                taskCard.style.transform = 'translateX(100px)';
                
                setTimeout(() => {
                    taskCard.remove();
                    // Actualizar contador de la columna
                    updateColumnCount(taskCard.closest('.kanban-column-compact'));
                }, 500);
            } else if (taskCard && !isChecked) {
                // Si se desmarca, recargar para que aparezca en la columna correcta
                setTimeout(() => {
                    const activeTab = document.querySelector('.kanban-tab-button.active');
                    if (activeTab && activeTab.id === 'my-tasks-kanban-tab') {
                        loadMyKanbanTasks();
                    } else if (activeTab && activeTab.id === 'team-tasks-kanban-tab') {
                        loadTeamKanbanTasks();
                    }
                }, 300);
            }
            
        } else {
            console.error('❌ Error al actualizar status:', data.message);
            // Revertir checkbox si hay error
            const checkbox = document.querySelector(`[data-task-id="${taskId}"] .task-checkbox-mini`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
        }
    })
    .catch(error => {
        console.error('❌ Error de conexión:', error);
        // Revertir checkbox si hay error
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

// Función para abrir modal de crear tarea (placeholder)
function openCreateTaskModal() {
    console.log('🔄 Abrir modal crear tarea');
    // TODO: Implementar modal
}

// Inicializar dashboard al cargar DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo - Iniciando dashboard');
    switchKanbanTab('my-tasks');
});
</script>

<!-- Estilos para los tabs del Kanban -->
<style>
/* Contenedor principal de tabs - Solo minimalista para los botones */
.kanban-section .kanban-tabs {
    display: flex !important;
    background: #f8fafc !important;
    border-radius: 12px !important;
    padding: 6px !important;
    margin-bottom: 25px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    max-width: 400px !important;
    margin: 0 auto 25px auto !important;
    border: none !important;
}

/* Botones de los tabs - Minimalistas */
.kanban-section .kanban-tab-button {
    flex: 1 !important;
    padding: 12px 20px !important;
    border: none !important;
    background: transparent !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    position: relative !important;
    overflow: hidden !important;
    letter-spacing: 0.025em !important;
}

.kanban-section .kanban-tab-button:hover {
    background: rgba(100, 116, 139, 0.1) !important;
    color: #475569 !important;
}

/* Tab activo - Minimalista */
.kanban-section .kanban-tab-button.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
    transform: translateY(-1px) !important;
    font-weight: 600 !important;
}

/* Iconos de los tabs */
.kanban-section .kanban-tab-button i {
    font-size: 16px !important;
    transition: all 0.3s ease !important;
}

.kanban-section .kanban-tab-button.active i {
    transform: scale(1.1) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .kanban-section .kanban-tabs {
        max-width: 100% !important;
        margin: 0 16px 25px 16px !important;
    }
    
    .kanban-section .kanban-tab-button {
        padding: 10px 16px !important;
        gap: 6px !important;
        font-size: 13px !important;
    }
    
    .kanban-section .kanban-tab-button i {
        font-size: 14px !important;
    }
}
</style>

<?php
// Guardar el contenido generado
$content = ob_get_clean();

// Incluir el layout con el contenido
include __DIR__ . '/../layout.php';
?>