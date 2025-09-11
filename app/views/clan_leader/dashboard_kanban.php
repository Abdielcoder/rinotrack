<?php
// Vista del Dashboard Kanban para Clan Leader
// El header "Polaris Líder" se carga automáticamente desde layout.php
?>

<!-- Cargar CSS igual que tasks -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/clan-leader-redesign.css">

<style>
.dashboard-container {
    padding: 24px;
    background: #f8fafc;
    min-height: calc(100vh - 58px);
}

.dashboard-header {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 8px;
}

.dashboard-stats {
    display: flex;
    gap: 24px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    color: #6b7280;
    font-size: 0.875rem;
    text-align: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    text-align: center;
}

.stat-value {
    font-weight: 600;
    color: #374151;
    font-size: 1.5rem;
    display: block;
    text-align: center;
}

/* Kanban Board - 4 columnas fijas */
.kanban-board {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 20px;
}

.kanban-column {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.column-header {
    padding: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.column-header.vencidas {
    background: linear-gradient(135deg, #ffb3ba 0%, #ff9aa2 100%);
    color: #8b0000;
}

.column-header.hoy {
    background: linear-gradient(135deg, #fff2b3 0%, #ffe066 100%);
    color: #b8860b;
}

.column-header.semana {
    background: linear-gradient(135deg, #b3d9ff 0%, #87ceeb 100%);
    color: #1e40af;
}

.column-header.futuras {
    background: linear-gradient(135deg, #c7f7c7 0%, #98fb98 100%);
    color: #2d5016;
}

.task-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.column-content {
    padding: 20px;
    max-height: 500px;
    overflow-y: auto;
}

.task-card {
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.task-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ffb3ba, #fff2b3, #b3d9ff, #c7f7c7);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.task-card:hover::before {
    opacity: 1;
}

.task-card:hover {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border-color: rgba(102, 126, 234, 0.3);
}

.task-id {
    font-weight: 700;
    color: #8b5cf6;
    font-size: 0.75rem;
    margin-bottom: 6px;
    background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
    padding: 2px 8px;
    border-radius: 8px;
    display: inline-block;
}

.task-name {
    color: #1f2937;
    font-weight: 600;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 12px;
}

.task-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.task-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-completed {
    background: linear-gradient(135deg, #c7f7c7, #98fb98);
    color: #2d5016;
}

.status-pending {
    background: linear-gradient(135deg, #fff2b3, #ffe066);
    color: #b8860b;
}

.status-in_progress {
    background: linear-gradient(135deg, #b3d9ff, #87ceeb);
    color: #1e40af;
}

.priority {
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.priority-high {
    background: linear-gradient(135deg, #ffb3ba, #ff9aa2);
    color: #8b0000;
}

.priority-medium {
    background: linear-gradient(135deg, #ffd1a9, #ffb366);
    color: #cc5500;
}

.priority-low {
    background: linear-gradient(135deg, #b3d9ff, #87ceeb);
    color: #1e40af;
}

.task-date {
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 4px;
    font-weight: 500;
    background: rgba(107, 114, 128, 0.1);
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-block;
}

.empty-column {
    text-align: center;
    color: #9ca3af;
    padding: 40px 20px;
    font-style: italic;
    background: linear-gradient(145deg, #f9fafb, #f3f4f6);
    border-radius: 12px;
    border: 2px dashed #d1d5db;
    margin: 8px 0;
    font-weight: 500;
}

/* Tabs Minimalistas - Igual que en tasks */
.tabs-container-minimal {
    margin-bottom: 24px;
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

.tab-icon-minimal {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

.tab-icon-minimal i {
    font-size: 16px;
    transition: all 0.3s ease;
}

.tab-minimal.active .tab-icon-minimal i {
    transform: scale(1.1);
}

.tab-text-minimal {
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.025em;
}

/* Tab Content */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Responsive - Mantener 4 columnas */
@media (max-width: 1200px) {
    .kanban-board {
        gap: 15px;
    }
    
    .kanban-column {
        min-width: 250px;
    }
    
    .column-header {
        padding: 12px 16px;
        font-size: 0.75rem;
    }
    
    .task-card {
        padding: 10px;
    }
}

@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .kanban-column {
        min-width: auto;
    }
    
    .dashboard-stats {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .dashboard-container {
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">🎯 Panel de Tareas</h1>
        <p style="color: #6b7280; margin-bottom: 20px;">Gestiona tus tareas de manera visual y eficiente</p>
        
        <!-- Tabs Minimalistas igual que en tasks -->
        <div class="tabs-container-minimal">
            <div class="tabs-wrapper-minimal">
                <button class="tab-minimal active" onclick="switchDashboardTab('my-tasks')" id="my-tasks-dashboard-tab">
                    <div class="tab-icon-minimal">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="tab-text-minimal">Mis Tareas</span>
                </button>
                <button class="tab-minimal" onclick="switchDashboardTab('team-tasks')" id="team-tasks-dashboard-tab">
                    <div class="tab-icon-minimal">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="tab-text-minimal">Equipo</span>
                </button>
            </div>
        </div>
        
        <!-- Estadísticas para tab activo -->
        <div id="my-tasks-stats" class="dashboard-stats">
            <div class="stat-item">
                <span>Total:</span>
                <span class="stat-value"><?= $totalTasks ?></span>
            </div>
            <div class="stat-item">
                <span>• Vencidas:</span>
                <span class="stat-value"><?= count($vencidas) ?></span>
            </div>
            <div class="stat-item">
                <span>• Hoy:</span>
                <span class="stat-value"><?= count($hoy) ?></span>
            </div>
            <div class="stat-item">
                <span>• Semana:</span>
                <span class="stat-value"><?= count($semana) ?></span>
            </div>
            <div class="stat-item">
                <span>• Futuras:</span>
                <span class="stat-value"><?= count($futuras) ?></span>
            </div>
        </div>
        
        <div id="team-tasks-stats" class="dashboard-stats" style="display: none;">
            <span>Tablero del equipo - <span class="stat-value">Próximamente</span></span>
        </div>
    </div>
    
    <!-- Contenido del tab MIS TAREAS -->
    <div id="my-tasks-content" class="tab-content active">
        <div class="kanban-board">
        <!-- Columna VENCIDAS -->
        <div class="kanban-column">
            <div class="column-header vencidas">
                <span>⚠️ VENCIDAS</span>
                <span class="task-count"><?= count($vencidas) ?></span>
            </div>
            <div class="column-content">
                <?php if (count($vencidas) > 0): ?>
                    <?php foreach ($vencidas as $task): ?>
                        <?php 
                        $statusClass = "status-" . str_replace(' ', '_', $task['status']);
                        $priorityClass = "priority-" . $task['priority'];
                        ?>
                        <div class="task-card">
                            <div class="task-id">#<?= $task['task_id'] ?></div>
                            <div class="task-name"><?= htmlspecialchars($task['task_name']) ?></div>
                            <div class="task-meta">
                                <span class="task-status <?= $statusClass ?>"><?= $task['status'] ?></span>
                                <span class="priority <?= $priorityClass ?>"><?= $task['priority'] ?></span>
                            </div>
                            <div class="task-date">📅 <?= $task['due_date'] ?> (<?= $task['days_until_due'] ?> días)</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-column">Sin tareas vencidas</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna HOY -->
        <div class="kanban-column">
            <div class="column-header hoy">
                <span>📅 HOY</span>
                <span class="task-count"><?= count($hoy) ?></span>
            </div>
            <div class="column-content">
                <?php if (count($hoy) > 0): ?>
                    <?php foreach ($hoy as $task): ?>
                        <?php 
                        $statusClass = "status-" . str_replace(' ', '_', $task['status']);
                        $priorityClass = "priority-" . $task['priority'];
                        ?>
                        <div class="task-card">
                            <div class="task-id">#<?= $task['task_id'] ?></div>
                            <div class="task-name"><?= htmlspecialchars($task['task_name']) ?></div>
                            <div class="task-meta">
                                <span class="task-status <?= $statusClass ?>"><?= $task['status'] ?></span>
                                <span class="priority <?= $priorityClass ?>"><?= $task['priority'] ?></span>
                            </div>
                            <div class="task-date">📅 <?= $task['due_date'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-column">Sin tareas para hoy</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna ESTA SEMANA -->
        <div class="kanban-column">
            <div class="column-header semana">
                <span>📆 ESTA SEMANA</span>
                <span class="task-count"><?= count($semana) ?></span>
            </div>
            <div class="column-content">
                <?php if (count($semana) > 0): ?>
                    <?php foreach ($semana as $task): ?>
                        <?php 
                        $statusClass = "status-" . str_replace(' ', '_', $task['status']);
                        $priorityClass = "priority-" . $task['priority'];
                        ?>
                        <div class="task-card">
                            <div class="task-id">#<?= $task['task_id'] ?></div>
                            <div class="task-name"><?= htmlspecialchars($task['task_name']) ?></div>
                            <div class="task-meta">
                                <span class="task-status <?= $statusClass ?>"><?= $task['status'] ?></span>
                                <span class="priority <?= $priorityClass ?>"><?= $task['priority'] ?></span>
                            </div>
                            <div class="task-date">📅 <?= $task['due_date'] ?> (<?= $task['days_until_due'] ?> días)</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-column">Sin tareas esta semana</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna FUTURAS -->
        <div class="kanban-column">
            <div class="column-header futuras">
                <span>🚀 FUTURAS</span>
                <span class="task-count"><?= count($futuras) ?></span>
            </div>
            <div class="column-content">
                <?php if (count($futuras) > 0): ?>
                    <?php foreach (array_slice($futuras, 0, 8) as $task): ?>
                        <?php 
                        $statusClass = "status-" . str_replace(' ', '_', $task['status']);
                        $priorityClass = "priority-" . $task['priority'];
                        ?>
                        <div class="task-card">
                            <div class="task-id">#<?= $task['task_id'] ?></div>
                            <div class="task-name"><?= htmlspecialchars($task['task_name']) ?></div>
                            <div class="task-meta">
                                <span class="task-status <?= $statusClass ?>"><?= $task['status'] ?></span>
                                <span class="priority <?= $priorityClass ?>"><?= $task['priority'] ?></span>
                            </div>
                            <div class="task-date">📅 <?= $task['due_date'] ?> (<?= $task['days_until_due'] ?> días)</div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($futuras) > 8): ?>
                        <div class="empty-column">... y <?= count($futuras) - 8 ?> más</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-column">Sin tareas futuras</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenido del tab EQUIPO -->
    <div id="team-tasks-content" class="tab-content">
        <div class="kanban-board">
            <div class="kanban-column" style="grid-column: 1 / -1;">
                <div class="column-header" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                    <span>👥 TAREAS DEL EQUIPO</span>
                    <span class="task-count">0</span>
                </div>
                <div class="column-content">
                    <div class="empty-column">
                        <h3 style="color: #374151; margin-bottom: 10px;">🚧 Próximamente</h3>
                        <p style="color: #6b7280;">Las tareas del equipo se mostrarán aquí</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
// Función para cambiar entre tabs del dashboard
function switchDashboardTab(tabName) {
    console.log('🔄 Cambiando tab dashboard:', tabName);
    
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-minimal').forEach(button => {
        button.classList.remove('active');
    });
    
    // Ocultar todas las estadísticas
    document.querySelectorAll('.dashboard-stats').forEach(stats => {
        stats.style.display = 'none';
    });
    
    // Mostrar contenido del tab seleccionado
    const targetContent = document.getElementById(tabName + '-content');
    const targetButton = document.getElementById(tabName + '-dashboard-tab');
    const targetStats = document.getElementById(tabName + '-stats');
    
    if (targetContent) {
        targetContent.classList.add('active');
        targetContent.style.display = 'block';
    }
    
    if (targetButton) {
        targetButton.classList.add('active');
    }
    
    if (targetStats) {
        targetStats.style.display = 'flex';
    }
    
    console.log('✅ Tab dashboard cambiado a:', tabName);
}

// Inicializar con "Mis Tareas" activo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard Kanban cargado');
    switchDashboardTab('my-tasks');
});
</script>
