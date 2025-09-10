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
            <div id="my-tasks-kanban-content" class="kanban-tab-content active">
                <div id="my-tasks-kanban-board" class="kanban-board-compact">
                    <!-- El contenido se carga dinámicamente -->
                    <div class="loading-message">
                        <i class="fas fa-spinner fa-spin"></i>
                        Cargando mis tareas...
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Equipo (tablero dinámico) -->
            <div id="team-tasks-kanban-content" class="kanban-tab-content">
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
    
    // Ocultar todos los tab contents
    document.querySelectorAll('.kanban-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remover active de todos los tab buttons
    document.querySelectorAll('.kanban-tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Mostrar el tab content seleccionado
    const targetContent = document.getElementById(tabName + '-kanban-content');
    if (targetContent) {
        targetContent.classList.add('active');
        console.log('✅ Tab content activado:', tabName + '-kanban-content');
    } else {
        console.error('🔴 No se encontró tab content:', tabName + '-kanban-content');
    }
    
    // Activar el tab button seleccionado
    const targetButton = document.getElementById(tabName + '-kanban-tab');
    if (targetButton) {
        targetButton.classList.add('active');
        console.log('✅ Tab button activado:', tabName + '-kanban-tab');
    } else {
        console.error('🔴 No se encontró tab button:', tabName + '-kanban-tab');
    }
    
    // Cargar datos según el tab
    if (tabName === 'my-tasks') {
        loadMyKanbanTasks();
    } else if (tabName === 'team-tasks') {
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
    
    fetch('?route=clan_leader/get-my-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🟡 === RESPUESTA TEAM KANBAN ===');
            console.log('🟡 Success:', data.success);
            console.log('🟡 Data received:', data);
            
            if (data.success) {
                // Para el tab de equipo, mostrar mensaje informativo por ahora
                const emptyKanbanTasks = {
                    'vencidas': [],
                    'hoy': [],
                    'semana1': [],
                    'semana2': []
                };
                renderTeamKanbanBoard(emptyKanbanTasks, true);
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
            html += `<div class="task-card-compact ${columnClass}" data-task-id="${task.task_id}">
                <div class="task-compact-row">
                    <input type="checkbox" class="task-checkbox-compact" ${task.status === 'completed' ? 'checked' : ''}>
                    <div class="task-name-compact">${task.task_name || 'Sin nombre'}</div>
                </div>
                <div class="task-info-compact">
                    <span class="project-name-compact">${task.project_name || 'Sin proyecto'}</span>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    });
    
    kanbanBoard.innerHTML = html;
}

function renderTeamKanbanBoard(kanbanTasks, showInfoMessage = false) {
    console.log('🟠 renderTeamKanbanBoard() iniciado');
    const kanbanBoard = document.getElementById('team-tasks-kanban-board');
    if (!kanbanBoard) return;
    
    if (showInfoMessage) {
        kanbanBoard.innerHTML = `
            <div class="loading-message" style="color: #666; font-size: 16px; padding: 40px; text-align: center;">
                <i class="fas fa-info-circle" style="font-size: 48px; color: #3498db; margin-bottom: 20px;"></i>
                <h3 style="margin-bottom: 10px;">Tareas del Equipo</h3>
                <p>Esta sección mostrará las tareas asignadas a los miembros de tu equipo.</p>
                <p style="color: #888; font-size: 14px;">Funcionalidad en desarrollo</p>
            </div>
        `;
        return;
    }
    
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

<?php
// Guardar el contenido generado
$content = ob_get_clean();

// Incluir el layout con el contenido
include __DIR__ . '/../layout.php';
?>