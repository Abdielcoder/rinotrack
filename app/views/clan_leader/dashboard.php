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
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
        text-align: center;
    }
    
    .header-left {
        flex: none;
    }
    
    .header-actions {
        width: 100%;
        justify-content: center;
    }
    
    .btn-create {
        padding: 0.75rem 2rem;
        font-size: 1rem;
    }
}

/* Estilos específicos para el Kanban - Expandido para máxima visibilidad */
.kanban-board-compact {
    display: flex;
    gap: 16px; /* Gap más generoso con el espacio extra */
    padding: 24px; /* Padding más generoso */
    background: #f8fafc;
    border-radius: 12px;
    min-height: 500px;
    width: 100%;
    box-sizing: border-box;
    overflow-x: hidden; /* Sin scroll horizontal */
    margin: 0 -1rem; /* Expande más allá del contenedor principal */
    padding-left: 2rem; /* Compensa el margen negativo */
    padding-right: 2rem; /* Compensa el margen negativo */
}

.kanban-column-compact {
    flex: 1;
    min-width: 280px; /* Ancho mínimo más generoso */
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
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
    flex: 1; /* Ocupa el espacio disponible en la columna */
}

/* Task Cards - Expandidas para mejor visibilidad */
.task-card-mini {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px; /* Padding más generoso con el espacio extra */
    margin-bottom: 8px; /* Margen restaurado */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    font-size: 14px; /* Texto normal para mejor legibilidad */
    word-wrap: break-word;
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
    padding: 12px; /* Padding más generoso */
    margin-bottom: 6px; /* Margen restaurado */
    font-size: 13px; /* Texto más legible */
    opacity: 0.9;
    word-wrap: break-word;
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
    font-size: 14px; /* Restaurado para mejor legibilidad */
    line-height: 1.4;
    display: flex;
    word-break: break-word;
    align-items: center;
    gap: 6px; /* Gap más generoso */
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
    gap: 6px; /* Gap más generoso */
    margin-top: 10px; /* Margen restaurado */
}

.task-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px; /* Padding más generoso */
    border-radius: 12px;
    font-size: 11px; /* Tamaño más legible */
    font-weight: 600;
    white-space: nowrap;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.project-tag.personal {
    background: #fef2f2;
    color: #dc2626;
}

.project-tag.recurrent {
    background: #ecfdf5;
    color: #10b981;
}

.project-tag.eventual {
    background: #fef3c7;
    color: #f59e0b;
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
@media (max-width: 1400px) {
    .kanban-board-compact {
        margin: 0; /* Sin expansión negativa en pantallas medianas */
        padding: 20px; /* Padding normal */
    }
}

@media (max-width: 1200px) {
    .kanban-board-compact {
        gap: 12px;
        padding: 18px;
        margin: 0;
    }
    
    .kanban-column-compact {
        min-width: 250px; /* Ancho mínimo reducido */
    }
    
    .task-card-mini {
        padding: 12px;
        font-size: 13px;
    }
    
    .subtask-card-micro {
        padding: 10px;
        font-size: 12px;
    }
    
    .task-name-mini {
        font-size: 13px;
    }
    
    .task-tag {
        font-size: 10px;
        padding: 3px 6px;
    }
}

@media (max-width: 768px) {
    .kanban-board-compact {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }
    
    .kanban-column-compact {
        min-width: auto;
        max-width: none;
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
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 5;
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
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(30, 58, 138, 0.25);
    white-space: nowrap;
    z-index: 10;
    position: relative;
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

/* Contenido Principal - Expandido para mejor visibilidad */
.main-content {
    max-width: 1400px; /* Expandido de 1200px a 1400px */
    margin: 0 auto;
    padding: 0 1rem 2rem 1rem; /* Padding reducido para aprovechar más espacio */
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
                    Agregar Tarea Personal
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
            
            <!-- Tab Content: Mis Tareas DESHABILITADO -->
            <div id="my-tasks-kanban-content" class="kanban-tab-content active" style="display: block;">
                <div id="simple-kanban-board" class="kanban-board-compact">
                    <div style="text-align: center; padding: 100px 20px; background: #f3f4f6; border-radius: 10px; margin: 20px;">
                        <h1 style="color: #ef4444; font-size: 48px;">🚫</h1>
                        <h2 style="color: #374151; margin: 20px 0;">Tablero Kanban Vacío</h2>
                        <p style="color: #6b7280; font-size: 16px; line-height: 1.6;">
                            El tablero está completamente deshabilitado.<br>
                            No se cargan tareas automáticamente.<br>
                            Toda la lógica JavaScript ha sido eliminada.<br><br>
                            <strong>Configuración:</strong><br>
                            <code style="background: #fee2e2; padding: 5px 10px; border-radius: 4px; color: #dc2626;">
                                Solo Tasks.assigned_to_user_id = tu usuario
                            </code>
                        </p>
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
            <!-- Campo oculto para marcar como tarea personal -->
            <input type="hidden" name="is_personal" value="1">
            <!-- Campo oculto para asignar la tarea al usuario actual -->
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <div class="form-group">
                <label for="task_name">
                    <i class="fas fa-tasks"></i>
                    Nombre de la Tarea *
                </label>
                <input type="text" id="task_name" name="task_name" required 
                       placeholder="Ej: Revisar documentación del proyecto" 
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Descripción
                </label>
                <textarea id="description" name="description" 
                          placeholder="Descripción detallada de la tarea (opcional)"
                          class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="due_date">
                    <i class="fas fa-calendar"></i>
                    Fecha de Vencimiento *
                </label>
                <input type="date" id="due_date" name="due_date" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="priority">
                    <i class="fas fa-exclamation-triangle"></i>
                    Prioridad
                </label>
                <select id="priority" name="priority" class="form-control">
                    <option value="">Seleccionar prioridad...</option>
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                </select>
            </div>
            
            <!-- Campo oculto para status por defecto -->
            <input type="hidden" name="status" value="pending">
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
        // NO CARGAR NADA - TABLERO DESHABILITADO
        console.log('🚫 Tab my-tasks seleccionado pero NO se cargan tareas');
        const kanbanBoard = document.getElementById('simple-kanban-board');
        if (kanbanBoard) {
            kanbanBoard.innerHTML = `
                <div style="text-align: center; padding: 100px 20px; background: #f3f4f6; border-radius: 10px; margin: 20px;">
                    <h1 style="color: #ef4444; font-size: 48px;">🚫</h1>
                    <h2 style="color: #374151; margin: 20px 0;">Tablero Kanban Completamente Vacío</h2>
                    <p style="color: #6b7280; font-size: 16px; line-height: 1.6;">
                        Toda la lógica anterior ha sido eliminada.<br>
                        No se están cargando tareas.<br>
                        No hay filtros, no hay condiciones.<br><br>
                        <strong>Solo se mostrarán tareas donde:</strong><br>
                        <code style="background: #fee2e2; padding: 5px 10px; border-radius: 4px; color: #dc2626;">
                            Tasks.assigned_to_user_id = ${<?php echo $_SESSION['user_id'] ?? '?' ?>}
                        </code>
                    </p>
                </div>
            `;
        }
    } else if (tabName === 'team-tasks') {
        loadTeamKanbanTasks();
    }
}

// TABLERO VACÍO - NO CARGAR NADA
function loadMyKanbanTasks() {
    console.log('🚫 TABLERO DESHABILITADO - No se cargan tareas');
    const kanbanBoard = document.getElementById('simple-kanban-board');
    
    if (kanbanBoard) {
        kanbanBoard.innerHTML = `
            <div style="text-align: center; padding: 50px; color: #666;">
                <h2>🚫 Tablero Kanban Deshabilitado</h2>
                <p>El tablero está vacío - Sin lógica de carga</p>
                <p style="margin-top: 20px; font-size: 14px; color: #999;">
                    Solo se mostrarán tareas donde assigned_to_user_id = tu usuario<br>
                    Toda la lógica anterior ha sido eliminada
                </p>
            </div>
        `;
    }
    
    // NO HACER NINGUNA LLAMADA AL SERVIDOR
    // NO CARGAR NINGUNA TAREA
    // TABLERO COMPLETAMENTE VACÍO
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

// FUNCIÓN VACÍA - NO RENDERIZAR NADA
function renderSimpleKanban(kanban, total, userId) {
    console.log('🚫 RENDERIZADO DESHABILITADO');
    // NO HACER NADA
    // FUNCIÓN COMPLETAMENTE VACÍA
}

// FUNCIÓN ELIMINADA - NO USAR
function renderMyKanbanBoard(kanbanTasks) {
    console.log('🚫 FUNCIÓN ELIMINADA - renderMyKanbanBoard ya no existe');
    // FUNCIÓN COMPLETAMENTE ELIMINADA
    // NO RENDERIZAR NADA
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
            const isRecurrent = task.project_type === 'recurrent' || task.project_name === 'Mis Tareas Recurrentes';
            const isEventual = task.project_name === 'Tareas Eventuales';
            
            html += `<div class="${cardClass}" data-task-id="${task.task_id}" data-item-type="${task.item_type}">
                <div class="task-header-mini">
                    <input type="checkbox" class="task-checkbox-mini" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatusKanban(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                    <div class="task-name-mini">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon"></i>' : ''}
                        ${isRecurrent ? '<i class="fas fa-sync-alt" title="Tarea Recurrente" style="color: #10b981; margin-right: 4px; font-size: 12px;"></i>' : ''}
                        ${isEventual ? '<i class="fas fa-star" title="Tarea Eventual" style="color: #f59e0b; margin-right: 4px; font-size: 12px;"></i>' : ''}
                        <a href="#" onclick="goToTaskDetail(${isSubtask ? (task.parent_task_id || task.task_id) : task.task_id}, '${isSubtask ? 'subtask' : 'task'}'); return false;" class="task-name-link">
                            ${task.task_name || 'Sin nombre'}
                        </a>
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-hint" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                </div>
                <div class="task-tags-mini">
                    <span class="task-tag project-tag ${isRecurrent ? 'recurrent' : isEventual ? 'eventual' : 'team'}">
                        ${isRecurrent ? 'Recurrente' : isEventual ? 'Eventual' : (task.project_name || 'Proyecto')}
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

// Función para obtener el ID del proyecto personal (ya no necesaria para tareas personales)
function getPersonalProjectId() {
    // Las tareas personales no necesitan proyecto específico
    console.log('✅ Tarea personal - no se requiere proyecto específico');
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

// Función para crear tarea personal
function createTask() {
    const form = document.getElementById('createTaskForm');
    const formData = new FormData(form);
    
    // Validaciones básicas
    const taskName = formData.get('task_name');
    const dueDate = formData.get('due_date');
    
    if (!taskName || taskName.trim().length < 3) {
        alert('El nombre de la tarea debe tener al menos 3 caracteres');
        return;
    }
    
    if (!dueDate) {
        alert('La fecha de vencimiento es requerida');
        return;
    }
    
    console.log('📝 Creando tarea personal:', taskName);
    
    fetch('?route=clan_leader/create-personal-task', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Tarea personal creada exitosamente');
            closeCreateTaskModal();
            
            // Recargar el tab activo
            const activeTab = document.querySelector('.tab-minimal.active');
            if (activeTab && activeTab.id === 'my-tasks-kanban-tab') {
                loadMyKanbanTasks();
            } else if (activeTab && activeTab.id === 'team-tasks-kanban-tab') {
                loadTeamKanbanTasks();
            }
            
            // Mostrar mensaje de éxito
            alert('Tarea personal creada exitosamente');
        } else {
            console.error('❌ Error:', data.message);
            alert('Error al crear tarea personal: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error de conexión al crear tarea personal');
    });
}

// Inicializar dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo - Iniciando dashboard');
    // DESHABILITADO - No cargar ningún tab automáticamente
    // switchKanbanTab('my-tasks');
    
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
