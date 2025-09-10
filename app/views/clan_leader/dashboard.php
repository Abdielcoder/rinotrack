<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large"><?php echo $clanIcon ?? ''; ?></div>
                <h1><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_departamento'] ?? ''); ?></span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Tablero Kanban de Tareas del Clan -->
    <div class="content-minimal">
        <section class="kanban-section animate-fade-in">
            <div class="kanban-header">
                <div class="kanban-title">
                    <h3><i class="fas fa-tasks icon-gradient"></i> Tareas</h3>
                </div>
                <div class="kanban-actions">
                    <button class="btn-add-task" onclick="openAddTaskModal()">
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
                    <div class="loading-message">
                        <i class="fas fa-spinner fa-spin"></i>
                        Cargando mis tareas...
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Equipo (tablero dinámico) -->
            <div id="team-tasks-kanban-content" class="kanban-tab-content" style="display: none;">
                <div id="team-tasks-kanban-board" class="kanban-board-compact">
                    <!-- El contenido se carga dinámicamente mediante JavaScript -->
                </div>
            </div> <!-- Cierre del team-tasks-kanban-content -->
        </section>
    </div> <!-- Cierre de content-minimal -->
</div> <!-- Cierre de clan-leader-dashboard minimal -->

<!-- Modales y Scripts -->

<!-- Modal para agregar tarea -->
<div id="addTaskModal" class="modal-overlay">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>
                <i class="fas fa-plus-circle"></i> 
                Agregar Nueva Tarea Personal
            </h3>
            <button class="modal-close" onclick="closeAddTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Contenido del modal aquí -->
    </div>
</div>

<!-- Scripts -->
<script>
console.log('🚀 Dashboard.php JavaScript cargado - Debug activo v4.0');

// Función para cambiar entre tabs del Kanban
function switchKanbanTab(tabName) {
    console.log('🔄 switchKanbanTab llamado con:', tabName);
    
    // Ocultar TODOS los tab contents
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
        console.log('✅ Tab content activado:', tabName + '-kanban-content');
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
        console.log('🔵 Cargando MIS tareas...');
        loadMyKanbanTasks();
    } else if (tabName === 'team-tasks') {
        console.log('🟡 Cargando tareas del EQUIPO...');
        loadTeamKanbanTasks();
    }
}

// Función para cargar mis tareas en el Kanban
function loadMyKanbanTasks() {
    console.log('🔵 loadMyKanbanTasks() iniciado');
    const kanbanBoard = document.getElementById('my-tasks-kanban-board');
    if (!kanbanBoard) {
        console.error('🔴 No se encontró my-tasks-kanban-board');
        return;
    }
    
    kanbanBoard.innerHTML = '<div class="loading-message"><i class="fas fa-spinner fa-spin"></i> Cargando mis tareas...</div>';
    
    fetch('?route=clan_leader/get-my-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🔵 === RESPUESTA KANBAN ===');
            console.log('🔵 Success:', data.success);
            console.log('🔵 Kanban Tasks:', data.kanbanTasks);
            
            if (data.success) {
                renderMyKanbanBoard(data.kanbanTasks);
            } else {
                kanbanBoard.innerHTML = '<div class="loading-message text-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
            }
        })
        .catch(error => {
            console.error('🔴 Error:', error);
            kanbanBoard.innerHTML = '<div class="loading-message text-danger">Error de conexión</div>';
        });
}

// Función para cargar tareas del equipo
function loadTeamKanbanTasks() {
    console.log('🟡 loadTeamKanbanTasks() iniciado');
    const kanbanBoard = document.getElementById('team-tasks-kanban-board');
    if (!kanbanBoard) {
        console.error('🔴 No se encontró team-tasks-kanban-board');
        return;
    }
    
    kanbanBoard.innerHTML = '<div class="loading-message"><i class="fas fa-spinner fa-spin"></i> Cargando tareas del equipo...</div>';
    
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🟡 === RESPUESTA TEAM KANBAN ===');
            console.log('🟡 Success:', data.success);
            console.log('🟡 Debug:', data.debug);
            console.log('🟡 Kanban Tasks:', data.kanbanTasks);
            
            if (data.success && data.kanbanTasks) {
                console.log('🟡 Total tareas del equipo:', data.total);
                renderTeamKanbanBoard(data.kanbanTasks);
            } else {
                kanbanBoard.innerHTML = '<div class="loading-message text-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
            }
        })
        .catch(error => {
            console.error('🔴 Error:', error);
            kanbanBoard.innerHTML = '<div class="loading-message text-danger">Error de conexión</div>';
        });
}

// Función para renderizar el tablero Kanban
function renderMyKanbanBoard(kanbanTasks) {
    console.log('🟢 renderMyKanbanBoard() iniciado');
    const kanbanBoard = document.getElementById('my-tasks-kanban-board');
    if (!kanbanBoard) return;
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnTitles = {
        'vencidas': 'Vencidas',
        'hoy': 'Hoy', 
        'semana1': '1 Semana',
        'semana2': '2+ Semanas'
    };
    
    let html = '';
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        const columnClass = column === 'vencidas' ? 'overdue' : column === 'hoy' ? 'today' : column === 'semana1' ? 'week1' : 'week2';
        
        html += `<div class="kanban-column-compact">
            <div class="column-header ${columnClass}">
                <h4>${columnTitles[column]}</h4>
                <span class="task-count">${tasks.length}</span>
            </div>
            <div class="column-content-compact">`;
        
        tasks.forEach(task => {
            const isPersonal = (task.is_personal == 1);
            const isSubtask = (task.item_type === 'subtask');
            
            // Log detallado de cada tarea que se renderiza
            console.log(`🟢 Renderizando: ID=${task.task_id}, Name="${task.task_name}", Type=${task.item_type}, Personal=${isPersonal}, Project="${task.project_name}"`);
            
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            
            html += `<div class="${cardClass} ${columnClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-header-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''}>
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        ${task.task_name || 'Sin nombre'}
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag ${isPersonal ? 'personal' : 'clan'} ${isSubtask ? 'subtask-tag' : ''}">
                        ${isPersonal ? 
                            '<i class="fas fa-user"></i>' : 
                            '<i class="fas fa-users"></i>'
                        }
                    </span>
                    ${task.assigned_user_name ? `<span class="task-tag assignee-tag ${isSubtask ? 'subtask-tag' : ''}" title="${task.assigned_user_name}"><i class="fas fa-user-tag"></i></span>` : ''}
                    <span class="task-tag due-tag ${columnClass} ${isSubtask ? 'subtask-tag' : ''}">
                        ${column === 'vencidas' ? '<i class="fas fa-exclamation-triangle"></i>' : 
                          column === 'hoy' ? '<i class="fas fa-clock"></i>' :
                          column === 'semana1' ? '<i class="fas fa-calendar"></i>' :
                          '<i class="fas fa-calendar-plus"></i>'}
                    </span>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    });
    
    kanbanBoard.innerHTML = html;
}

function renderTeamKanbanBoard(kanbanTasks) {
    console.log('🟠 renderTeamKanbanBoard() iniciado');
    const kanbanBoard = document.getElementById('team-tasks-kanban-board');
    if (!kanbanBoard) return;
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnTitles = {
        'vencidas': 'Vencidas',
        'hoy': 'Hoy', 
        'semana1': '1 Semana',
        'semana2': '2+ Semanas'
    };
    
    let html = '';
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        const columnClass = column === 'vencidas' ? 'overdue' : column === 'hoy' ? 'today' : column === 'semana1' ? 'week1' : 'week2';
        
        html += `<div class="kanban-column-compact">
            <div class="column-header ${columnClass}">
                <h4>${columnTitles[column]}</h4>
                <span class="task-count">${tasks.length}</span>
            </div>
            <div class="column-content-compact">`;
        
        tasks.forEach(task => {
            const isSubtask = (task.item_type === 'subtask');
            
            console.log(`🟡 Renderizando Team: ID=${task.task_id}, Name="${task.task_name}", Type=${task.item_type}, Assigned="${task.assigned_user_name}"`);
            
            const cardClass = isSubtask ? 'subtask-card-micro' : 'task-card-mini';
            
            html += `<div class="${cardClass} ${columnClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-header-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''}>
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
                        ${column === 'vencidas' ? '<i class="fas fa-exclamation-triangle"></i>' : 
                          column === 'hoy' ? '<i class="fas fa-clock"></i>' :
                          column === 'semana1' ? '<i class="fas fa-calendar"></i>' :
                          '<i class="fas fa-calendar-plus"></i>'}
                    </span>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    });
    
    kanbanBoard.innerHTML = html;
}

// Función para organizar tareas en columnas Kanban
function organizeTasksInKanban(tasks) {
    const kanbanTasks = {
        'vencidas': [],
        'hoy': [],
        'semana1': [],
        'semana2': []
    };
    
    tasks.forEach(task => {
        // Calcular días hasta vencimiento
        let daysUntilDue = 999;
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            dueDate.setHours(0, 0, 0, 0);
            daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
        }
        
        // Asignar a la columna correspondiente
        if (daysUntilDue < 0) {
            kanbanTasks['vencidas'].push(task);
        } else if (daysUntilDue === 0) {
            kanbanTasks['hoy'].push(task);
        } else if (daysUntilDue <= 7) {
            kanbanTasks['semana1'].push(task);
        } else {
            kanbanTasks['semana2'].push(task);
        }
    });
    
    console.log('🟡 Tareas organizadas en Kanban:', {
        vencidas: kanbanTasks.vencidas.length,
        hoy: kanbanTasks.hoy.length,
        semana1: kanbanTasks.semana1.length,
        semana2: kanbanTasks.semana2.length
    });
    
    return kanbanTasks;
}

// Funciones auxiliares
function toggleTaskStatus(taskId, isChecked) {
    console.log('🔧 toggleTaskStatus:', taskId, isChecked);
}

function openAddTaskModal() {
    console.log('🔧 openAddTaskModal');
    const modal = document.getElementById('addTaskModal');
    if (modal) modal.style.display = 'flex';
}

function closeAddTaskModal() {
    console.log('🔧 closeAddTaskModal');
    const modal = document.getElementById('addTaskModal');
    if (modal) modal.style.display = 'none';
}


// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo - Iniciando dashboard');
    switchKanbanTab('my-tasks');
});
</script>

<!-- Estilos para los tabs del Kanban -->
<style>
/* Contenedor principal de tabs - FORZADO */
.kanban-section .kanban-tabs {
    display: flex !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 15px 15px 0 0 !important;
    padding: 12px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
    position: relative;
    overflow: hidden;
    border: none !important;
}

.kanban-tabs::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

/* Botones de los tabs - FORZADO */
.kanban-section .kanban-tab-button {
    flex: 1 !important;
    padding: 18px 28px !important;
    border: none !important;
    background: rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.9) !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border-radius: 12px !important;
    margin: 0 6px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    letter-spacing: 0.8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(15px) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    text-transform: uppercase;
}

.kanban-tab-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.kanban-tab-button:hover::before {
    left: 100%;
}

.kanban-tab-button:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Tab activo - FORZADO */
.kanban-section .kanban-tab-button.active {
    background: linear-gradient(135deg, #ffffff 0%, #f1f3f4 100%) !important;
    color: #2c3e50 !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25) !important;
    transform: translateY(-4px) !important;
    border: 2px solid rgba(255, 255, 255, 0.8) !important;
    font-weight: 800 !important;
}

.kanban-section .kanban-tab-button.active i {
    color: #3498db !important;
    transform: scale(1.2) !important;
    text-shadow: 0 2px 4px rgba(52, 152, 219, 0.3) !important;
}

/* Iconos de los tabs - FORZADO */
.kanban-section .kanban-tab-button i {
    font-size: 20px !important;
    transition: all 0.3s ease !important;
    color: rgba(255, 255, 255, 0.9) !important;
}

.kanban-section .kanban-tab-button:hover i {
    transform: scale(1.1) !important;
    color: white !important;
}

.kanban-section .kanban-tab-button:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    color: white !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2) !important;
}

/* Contenido de los tabs - FORZADO */
.kanban-section .kanban-tab-content {
    background: white !important;
    border-radius: 0 0 15px 15px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    overflow: hidden;
    transition: all 0.4s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    margin-top: 0 !important;
}

.kanban-tab-content.active {
    animation: fadeInUp 0.4s ease-out;
}

/* Efectos específicos por tab */
.kanban-section #my-tasks-kanban-tab.active {
    background: linear-gradient(135deg, #e8f4fd 0%, #ffffff 100%) !important;
    color: #2980b9 !important;
    border: 2px solid #3498db !important;
}

.kanban-section #my-tasks-kanban-tab.active i {
    color: #2980b9 !important;
    filter: drop-shadow(0 2px 4px rgba(41, 128, 185, 0.3));
}

.kanban-section #team-tasks-kanban-tab.active {
    background: linear-gradient(135deg, #fdf2e8 0%, #ffffff 100%) !important;
    color: #e67e22 !important;
    border: 2px solid #f39c12 !important;
}

.kanban-section #team-tasks-kanban-tab.active i {
    color: #e67e22 !important;
    filter: drop-shadow(0 2px 4px rgba(230, 126, 34, 0.3));
}

/* Efecto de brillos en hover */
.kanban-section .kanban-tab-button:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    color: white !important;
    transform: translateY(-3px) scale(1.02) !important;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25) !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mejorar el tablero Kanban */
.kanban-section .kanban-board-compact {
    padding: 20px 16px !important;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
    min-height: 500px !important;
    display: flex !important;
    gap: 12px !important;
    overflow-x: auto !important;
    justify-content: space-between !important;
}

/* Columnas del Kanban ultra compactas para máxima eficiencia */
.kanban-section .kanban-column-compact {
    background: white !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06) !important;
    flex: 1 !important;
    min-width: 200px !important;
    max-width: 220px !important;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    margin: 0 !important;
}

.kanban-column-compact:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

/* Headers de columnas ultra compactos */
.kanban-section .column-header {
    padding: 12px 10px !important;
    font-weight: 700 !important;
    color: white !important;
    text-align: center !important;
    position: relative;
    overflow: hidden;
}

.column-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
}

.kanban-section .column-header h4 {
    margin: 0 !important;
    font-size: 14px !important;
    letter-spacing: 0.4px !important;
    position: relative;
    z-index: 1;
}

.kanban-section .task-count {
    background: rgba(255, 255, 255, 0.3) !important;
    padding: 4px 8px !important;
    border-radius: 16px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    margin-left: 8px !important;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(10px) !important;
    display: inline-block !important;
    min-width: 24px !important;
    text-align: center !important;
}

/* Colores específicos por columna */
.column-header.overdue {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.column-header.today {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.column-header.week1 {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.column-header.week2 {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
}

/* Tarjetas de tareas mini optimizadas (aumentadas 0.5x) */
.kanban-section .task-card-mini {
    margin: 3px 6px !important;
    padding: 6px 8px !important;
    background: white !important;
    border-radius: 6px !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.2s ease !important;
    border-left: 3px solid #3498db !important;
    position: relative;
    overflow: hidden;
    min-height: 42px !important;
    width: calc(100% - 12px) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
}

.task-card-mini:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.10);
}

.task-card-mini.overdue {
    border-left-color: #e74c3c;
}

.task-card-mini.today {
    border-left-color: #f39c12;
}

.task-card-mini.week1 {
    border-left-color: #3498db;
}

.task-card-mini.week2 {
    border-left-color: #27ae60;
}

/* Header de la tarea mini optimizado */
.kanban-section .task-header-mini {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    min-height: 20px !important;
}

.kanban-section .task-checkbox-mini {
    margin: 0 !important;
    transform: scale(0.9) !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
}

/* Nombre de la tarea mini optimizado */
.kanban-section .task-name-mini {
    font-weight: 600 !important;
    color: #2c3e50 !important;
    font-size: 12px !important;
    line-height: 1.3 !important;
    flex: 1 !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    margin: 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Contenedor de etiquetas optimizado */
.kanban-section .task-tags-mini {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 2px !important;
    align-items: center !important;
    max-height: 18px !important;
    overflow: hidden !important;
}

/* Etiquetas mini optimizadas */
.kanban-section .task-tag {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    padding: 2px 5px !important;
    border-radius: 8px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    border: 1px solid transparent !important;
    transition: all 0.2s ease !important;
    max-width: 75px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.task-tag i {
    font-size: 8px !important;
}

/* Etiqueta de proyecto */
.kanban-section .project-tag.personal {
    background: rgba(231, 76, 60, 0.15) !important;
    color: #c0392b !important;
    border-color: rgba(231, 76, 60, 0.3) !important;
}

.kanban-section .project-tag.clan {
    background: rgba(52, 152, 219, 0.15) !important;
    color: #2980b9 !important;
    border-color: rgba(52, 152, 219, 0.3) !important;
}

.kanban-section .project-tag.team {
    background: rgba(243, 156, 18, 0.15) !important;
    color: #d68910 !important;
    border-color: rgba(243, 156, 18, 0.3) !important;
}

/* Etiqueta de asignado */
.kanban-section .assignee-tag {
    background: rgba(155, 89, 182, 0.15) !important;
    color: #8e44ad !important;
    border-color: rgba(155, 89, 182, 0.3) !important;
}

/* Etiquetas de vencimiento */
.kanban-section .due-tag.overdue {
    background: rgba(231, 76, 60, 0.2) !important;
    color: #e74c3c !important;
    border-color: rgba(231, 76, 60, 0.4) !important;
    animation: pulse-red 2s infinite !important;
}

.kanban-section .due-tag.today {
    background: rgba(243, 156, 18, 0.2) !important;
    color: #f39c12 !important;
    border-color: rgba(243, 156, 18, 0.4) !important;
    animation: pulse-orange 2s infinite !important;
}

.kanban-section .due-tag.week1 {
    background: rgba(52, 152, 219, 0.15) !important;
    color: #3498db !important;
    border-color: rgba(52, 152, 219, 0.3) !important;
}

.kanban-section .due-tag.week2 {
    background: rgba(39, 174, 96, 0.15) !important;
    color: #27ae60 !important;
    border-color: rgba(39, 174, 96, 0.3) !important;
}

/* Animaciones de pulsación para urgencia */
@keyframes pulse-red {
    0%, 100% { 
        background: rgba(231, 76, 60, 0.2);
        transform: scale(1); 
    }
    50% { 
        background: rgba(231, 76, 60, 0.35);
        transform: scale(1.05); 
    }
}

@keyframes pulse-orange {
    0%, 100% { 
        background: rgba(243, 156, 18, 0.2);
        transform: scale(1); 
    }
    50% { 
        background: rgba(243, 156, 18, 0.35);
        transform: scale(1.05); 
    }
}

/* Efectos hover para las etiquetas ultra mini */
.kanban-section .task-tag:hover {
    transform: scale(1.1) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12) !important;
    z-index: 10 !important;
}

/* ========== ESTILOS PARA SUBTAREAS ========== */

/* Cards de subtareas del mismo tamaño que tareas principales */
.kanban-section .subtask-card-micro {
    margin: 3px 6px !important;
    padding: 6px 8px !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-radius: 6px !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.2s ease !important;
    border-left: 3px solid #6c757d !important;
    position: relative;
    overflow: hidden;
    min-height: 42px !important;
    width: calc(100% - 12px) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
    opacity: 0.95 !important;
    border: 1px solid rgba(108, 117, 125, 0.2) !important;
}

.subtask-card-micro:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.10);
    opacity: 1 !important;
}

/* Colores específicos para subtareas por columna */
.subtask-card-micro.overdue {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%) !important;
}

.subtask-card-micro.today {
    border-left-color: #fd7e14;
    background: linear-gradient(135deg, #fff8f0 0%, #ffeaa7 100%) !important;
}

.subtask-card-micro.week1 {
    border-left-color: #0d6efd;
    background: linear-gradient(135deg, #f0f7ff 0%, #cce7ff 100%) !important;
}

.subtask-card-micro.week2 {
    border-left-color: #198754;
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%) !important;
}

/* Icono de subtarea */
.subtask-icon {
    font-size: 8px !important;
    color: #6c757d !important;
    margin-right: 4px !important;
    opacity: 0.8 !important;
}

/* Indicador de tarea padre */
.parent-task-hint {
    font-size: 9px !important;
    color: #6c757d !important;
    margin-left: 5px !important;
    opacity: 0.7 !important;
    cursor: help !important;
}

/* Etiquetas de subtareas del mismo tamaño que las principales */
.kanban-section .subtask-tag {
    font-size: 8px !important;
    padding: 2px 4px !important;
    border-radius: 6px !important;
    max-width: 65px !important;
    opacity: 0.9 !important;
}

.kanban-section .subtask-tag i {
    font-size: 7px !important;
}

/* Ajustes específicos para subtareas en el header */
.subtask-card-micro .task-header-mini {
    min-height: 20px !important;
    gap: 6px !important;
}

.subtask-card-micro .task-name-mini {
    font-size: 11px !important;
    line-height: 1.3 !important;
    color: #495057 !important;
    font-weight: 600 !important;
}

.subtask-card-micro .task-checkbox-mini {
    transform: scale(0.9) !important;
}

/* Contenedor de etiquetas para subtareas */
.subtask-card-micro .task-tags-mini {
    max-height: 18px !important;
    gap: 2px !important;
}

/* Animación sutil para subtareas */
.subtask-card-micro {
    animation: subtaskFadeIn 0.3s ease-out;
}

@keyframes subtaskFadeIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 0.9;
        transform: translateX(0);
    }
}

/* Responsive para subtareas */
@media (max-width: 768px) {
    .subtask-card-micro {
        margin: 2px 4px !important;
        padding: 4px 6px !important;
        min-height: 36px !important;
    }
    
    .subtask-card-micro .task-name-mini {
        font-size: 10px !important;
    }
    
    .kanban-section .subtask-tag {
        font-size: 7px !important;
        max-width: 50px !important;
    }
}

/* Responsive design mejorado */
@media (max-width: 1200px) {
    .kanban-section .kanban-column-compact {
        min-width: 220px !important;
        max-width: 250px !important;
    }
}

@media (max-width: 992px) {
    .kanban-section .kanban-board-compact {
        gap: 8px !important;
        padding: 16px 12px !important;
    }
    
    .kanban-section .kanban-column-compact {
        min-width: 200px !important;
        max-width: 230px !important;
    }
}

@media (max-width: 768px) {
    .kanban-section .kanban-tabs {
        flex-direction: column !important;
        gap: 8px !important;
    }
    
    .kanban-section .kanban-tab-button {
        margin: 0 !important;
    }
    
    .kanban-section .kanban-board-compact {
        padding: 12px 8px !important;
        flex-direction: column !important;
        gap: 16px !important;
    }
    
    .kanban-section .kanban-column-compact {
        min-width: auto !important;
        max-width: none !important;
        width: 100% !important;
    }
}

/* Animación de carga mejorada */
.kanban-section .loading-message {
    text-align: center !important;
    padding: 80px 30px !important;
    color: #5a6c7d !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-radius: 16px !important;
    margin: 30px !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.kanban-section .loading-message i {
    font-size: 48px !important;
    margin-bottom: 20px !important;
    color: #3498db !important;
    animation: pulse 2s infinite !important;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
}

/* Efecto de transición entre tabs */
.kanban-section .kanban-tab-content {
    animation: slideInFromRight 0.5s ease-out;
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

<?php
// Guardar el contenido generado
$content = ob_get_clean();

// Incluir el layout con el contenido
include __DIR__ . '/../layout.php';
?>