<?php
// Verificar que las variables necesarias existan
if (!isset($clan)) {
    die('Error: Variable $clan no está definida');
}

if (!isset($summary)) {
    die('Error: Variable $summary no está definida');
}

if (!isset($view)) {
    $view = 'calendar';
}

if (!isset($all_tasks)) {
    $all_tasks = [];
}

// Guardar el contenido en una variable
ob_start();
?>

<div class="collaborator-availability-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="title-content">
                    <h1>Disponibilidad de Colaboradores</h1>
                    <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?> - <?php echo htmlspecialchars($clan['clan_departamento']); ?></span>
                </div>
            </div>
            
            <div class="actions-minimal">
                <div class="view-toggle">
                    <a href="?route=clan_leader/collaborator-availability&view=calendar" class="btn-minimal <?= ($view === 'calendar') ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Calendario</span>
                    </a>
                    <a href="?route=clan_leader/collaborator-availability&view=gantt" class="btn-minimal <?= ($view === 'gantt') ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Diagrama Gantt</span>
                    </a>
                </div>
                <div class="navigation-actions">
                    <a href="?route=clan_leader/dashboard" class="btn-minimal secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Dashboard</span>
                    </a>
                    <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Reglas de Disponibilidad -->
    <div class="content-minimal">
        <section class="availability-rules">
            <div class="rules-card">
                <div class="rules-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Lógica de Disponibilidad</h3>
                </div>
                <div class="rules-content">
                    <p>Disponibilidad basada en tareas de proyectos <strong>ACTIVOS</strong> en los próximos 15 días:</p>
                    <ul>
                        <li><span class="rule-badge disponible">0 tareas</span> - Disponible ahora</li>
                        <li><span class="rule-badge poco-ocupado">1-3 tareas</span> - Poco ocupado</li>
                        <li><span class="rule-badge ocupado">4-9 tareas</span> - Ocupado</li>
                        <li><span class="rule-badge sobrecargado">10+ tareas</span> - Sobrecargado</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Vista de Calendario -->
        <section class="calendar-view">
            <div class="calendar-header-section">
                <h3>Calendario de Tareas</h3>
                <button class="btn-collapse" onclick="toggleCalendar()" id="calendarToggle">
                    <i class="fas fa-chevron-up" id="calendarIcon"></i>
                    <span id="calendarToggleText">Ocultar</span>
                </button>
            </div>
            
            <div class="calendar-container" id="calendarContainer">
                <div class="calendar-header">
                    <button class="btn-calendar-nav" onclick="previousMonth()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h4 id="currentMonth"><?= date('F Y') ?></h4>
                    <button class="btn-calendar-nav" onclick="nextMonth()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div class="weekday">Dom</div>
                        <div class="weekday">Lun</div>
                        <div class="weekday">Mar</div>
                        <div class="weekday">Mié</div>
                        <div class="weekday">Jue</div>
                        <div class="weekday">Vie</div>
                        <div class="weekday">Sáb</div>
                    </div>
                    
                    <div class="calendar-days" id="calendarDays">
                        <!-- Los días se generarán con JavaScript -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Resumen de Disponibilidad -->
        <section class="availability-summary">
            <h3>Resumen de Disponibilidad</h3>
            <div class="summary-grid">
                <div class="summary-card disponible">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['disponibles']; ?></span>
                        <span class="summary-label">Disponibles ahora</span>
                    </div>
                </div>
                
                <div class="summary-card poco-ocupado">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['poco_ocupados']; ?></span>
                        <span class="summary-label">Poco ocupados</span>
                    </div>
                </div>
                
                <div class="summary-card ocupado">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['ocupados']; ?></span>
                        <span class="summary-label">Ocupados</span>
                    </div>
                </div>
                
                <div class="summary-card sobrecargado">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['sobrecargados']; ?></span>
                        <span class="summary-label">Sobrecargados</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Lista de Colaboradores -->
        <section class="collaborators-list">
            <h3>Colaboradores del Clan</h3>
            <div class="collaborators-grid">
                <?php if (empty($availability_data)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No hay colaboradores en el clan</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($availability_data as $collaborator): ?>
                        <div class="collaborator-card <?php echo $collaborator['availability']; ?>">
                            <div class="collaborator-header">
                                <div class="collaborator-avatar">
                                    <?php if (isset($collaborator['member']['profile_picture']) && $collaborator['member']['profile_picture']): ?>
                                        <img src="<?php echo htmlspecialchars($collaborator['member']['profile_picture']); ?>" alt="<?php echo htmlspecialchars($collaborator['member']['full_name']); ?>">
                                    <?php else: ?>
                                        <?php 
                                        $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                                        $memberColor = $colors[$collaborator['member']['user_id'] % count($colors)];
                                        ?>
                                        <div class="avatar-initial" style="background-color: <?php echo $memberColor; ?>">
                                            <?php echo strtoupper(substr($collaborator['member']['full_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="collaborator-info">
                                    <div class="collaborator-name"><?php echo htmlspecialchars($collaborator['member']['full_name']); ?></div>
                                    <div class="availability-badge <?php echo $collaborator['availability']; ?>">
                                        <i class="fas fa-clock"></i>
                                        <?php 
                                        switch($collaborator['availability']) {
                                            case 'disponible': echo 'Disponible ahora'; break;
                                            case 'poco_ocupado': echo 'Poco ocupado'; break;
                                            case 'ocupado': echo 'Ocupado'; break;
                                            case 'sobrecargado': echo 'Sobrecargado'; break;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="collaborator-metrics">
                                <div class="metric-item">
                                    <i class="fas fa-tasks"></i>
                                    <span class="metric-label">Tareas activas:</span>
                                    <span class="metric-value"><?php echo $collaborator['task_count']; ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($collaborator['tasks'])): ?>
                                <div class="upcoming-tasks">
                                    <h4>Próximas tareas:</h4>
                                    <div class="tasks-list">
                                        <?php foreach (array_slice($collaborator['tasks'], 0, 3) as $task): ?>
                                            <div class="task-item">
                                                <i class="fas fa-circle"></i>
                                                <span class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></span>
                                                <?php if ($task['due_date']): ?>
                                                    <span class="task-date"><?php echo date('d/m/Y', strtotime($task['due_date'])); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Modal para mostrar tareas del día -->
<div class="task-modal" id="taskModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tareas del día</h3>
            <button class="modal-close" onclick="closeTaskModal()">&times;</button>
        </div>
        <div class="task-list" id="modalTaskList">
            <!-- Las tareas se cargarán dinámicamente -->
        </div>
    </div>
</div>

<!-- Script para pasar datos de tareas al JavaScript -->
<script>
// Datos de tareas para el calendario
window.calendarTasksData = <?= json_encode($all_tasks) ?>;

// Esperar a que el DOM esté listo y las funciones estén disponibles
document.addEventListener('DOMContentLoaded', function() {
    if (typeof setTasksData === 'function') {
        setTasksData(window.calendarTasksData);
    } else {
        console.error('La función setTasksData no está disponible');
        // Fallback: establecer directamente la variable global
        window.tasksData = window.calendarTasksData;
        if (typeof generateCalendar === 'function') {
            generateCalendar();
        }
    }
});
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 