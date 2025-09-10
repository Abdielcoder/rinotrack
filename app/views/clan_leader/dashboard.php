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
            
            // Log detallado de cada tarea que se renderiza
            console.log(`🟢 Renderizando: ID=${task.task_id}, Name="${task.task_name}", Personal=${isPersonal}, Project="${task.project_name}"`);
            
            html += `<div class="task-card-compact ${columnClass}" data-task-id="${task.task_id}">
                <div class="task-compact-row">
                    <input type="checkbox" class="task-checkbox-compact" ${task.status === 'completed' ? 'checked' : ''}>
                    <div class="task-name-compact">
                        ${isPersonal ? 
                            '<i class="fas fa-user-circle" style="color: #e74c3c; margin-right: 5px;" title="Tarea Personal"></i>' : 
                            '<i class="fas fa-users" style="color: #3498db; margin-right: 5px;" title="Tarea de Clan"></i>'
                        }
                        ${task.task_name || 'Sin nombre'}
                    </div>
                </div>
                <div class="task-info-compact">
                    <span class="project-name-compact" style="${isPersonal ? 'color: #e74c3c; font-weight: bold;' : 'color: #3498db; font-weight: bold;'}">${task.project_name || 'Sin proyecto'}</span>
                    ${task.assigned_user_name ? `<span class="assignee-info-compact"><i class="fas fa-user"></i> ${task.assigned_user_name}</span>` : ''}
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
            html += `<div class="task-card-compact ${columnClass}" data-task-id="${task.task_id}">
                <div class="task-compact-row">
                    <input type="checkbox" class="task-checkbox-compact" ${task.status === 'completed' ? 'checked' : ''}>
                    <div class="task-name-compact">${task.task_name || 'Sin nombre'}</div>
                </div>
                <div class="task-info-compact">
                    <span class="project-name-compact">${task.project_name || 'Sin proyecto'}</span>
                    ${task.assigned_user_name ? `<span class="assignee-info-compact"><i class="fas fa-user"></i> ${task.assigned_user_name}</span>` : ''}
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
.kanban-board-compact {
    padding: 24px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 400px;
}

/* Columnas del Kanban más atractivas */
.kanban-column-compact {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    margin: 0 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.kanban-column-compact:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

/* Headers de columnas más atractivos */
.column-header {
    padding: 20px 24px;
    font-weight: 700;
    color: white;
    text-align: center;
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

.column-header h4 {
    margin: 0;
    font-size: 16px;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
}

.task-count {
    background: rgba(255, 255, 255, 0.25);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-left: 10px;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(10px);
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

/* Tarjetas de tareas más atractivas */
.task-card-compact {
    margin: 16px;
    padding: 18px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #3498db;
    position: relative;
    overflow: hidden;
}

.task-card-compact::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(52, 152, 219, 0.03) 0%, rgba(52, 152, 219, 0.01) 100%);
    pointer-events: none;
}

.task-card-compact:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.task-card-compact.overdue {
    border-left-color: #e74c3c;
}

.task-card-compact.today {
    border-left-color: #f39c12;
}

.task-card-compact.week1 {
    border-left-color: #3498db;
}

.task-card-compact.week2 {
    border-left-color: #27ae60;
}

/* Nombres de tareas más legibles */
.task-name-compact {
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
    line-height: 1.4;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Información del proyecto más clara */
.project-name-compact {
    font-size: 13px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 6px;
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.2);
}

/* Información del asignado */
.assignee-info-compact {
    font-size: 12px;
    color: #7f8c8d;
    margin-left: 8px;
    padding: 2px 6px;
    background: rgba(127, 140, 141, 0.1);
    border-radius: 4px;
}

/* Responsive design */
@media (max-width: 768px) {
    .kanban-tabs {
        flex-direction: column;
        gap: 8px;
    }
    
    .kanban-tab-button {
        margin: 0;
    }
    
    .kanban-board-compact {
        padding: 16px;
    }
    
    .kanban-column-compact {
        margin: 0 8px;
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