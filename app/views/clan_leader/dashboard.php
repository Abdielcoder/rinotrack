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
// Funciones JavaScript aquí
</script>

<?php
// Guardar el contenido generado
$content = ob_get_clean();

// Incluir el layout con el contenido
include __DIR__ . '/../layout.php';
?>