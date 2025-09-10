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

        <!-- Progreso General del Equipo -->
        <section class="team-progress-section">
            <div class="progress-header">
                <h3>Progreso General del Equipo</h3>
                <button id="toggleSectionsBtn" class="btn-toggle-sections" onclick="toggleSections()">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <!-- El contenido del progreso se carga dinámicamente -->
        </section>

        <!-- Secciones ocultables -->
        <div id="hideable-sections" style="display: none;">
        
        <!-- Contribuciones por Colaborador -->
        <section class="contributions-section">
            <h3>Contribuciones por Colaborador</h3>
            <!-- El contenido se carga dinámicamente -->
        </section>
        
        </div> <!-- Cierre de hideable-sections -->
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
    
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🟡 === RESPUESTA TEAM KANBAN ===');
            console.log('🟡 Success:', data.success);
            
            if (data.success) {
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

function toggleSections() {
    console.log('🔧 toggleSections');
    const sections = document.getElementById('hideable-sections');
    const button = document.getElementById('toggleSectionsBtn');
    
    if (sections && button) {
        if (sections.style.display === 'none') {
            sections.style.display = 'block';
            button.innerHTML = '<i class="fas fa-chevron-up"></i>';
        } else {
            sections.style.display = 'none';
            button.innerHTML = '<i class="fas fa-chevron-down"></i>';
        }
    }
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