<?php
// Vista del Dashboard Kanban para Clan Leader
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
    color: #1f2937;
    margin-bottom: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard-stats {
    display: flex;
    gap: 24px;
    align-items: center;
    flex-wrap: wrap;
    color: #6b7280;
    font-size: 0.875rem;
}

.stat-value {
    font-weight: 600;
    color: #374151;
}

/* Kanban Board */
.kanban-board {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.kanban-column {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.column-header {
    padding: 16px 20px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.column-header.vencidas {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.column-header.hoy {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.column-header.semana {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.column-header.futuras {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.task-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.column-content {
    padding: 16px;
    max-height: 500px;
    overflow-y: auto;
}

.task-card {
    background: #fafbfc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.task-card:hover {
    background: #f0f4ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.task-id {
    font-weight: 600;
    color: #667eea;
    font-size: 0.75rem;
    margin-bottom: 4px;
}

.task-name {
    color: #1f2937;
    font-weight: 500;
    font-size: 0.875rem;
    line-height: 1.4;
    margin-bottom: 8px;
}

.task-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.task-status {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-in_progress {
    background: #dbeafe;
    color: #1e40af;
}

.priority {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.priority-high {
    background: #fee2e2;
    color: #991b1b;
}

.priority-medium {
    background: #fed7aa;
    color: #c2410c;
}

.priority-low {
    background: #dbeafe;
    color: #1d4ed8;
}

.task-date {
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 4px;
}

.empty-column {
    text-align: center;
    color: #9ca3af;
    padding: 40px 20px;
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }
    
    .dashboard-stats {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">🎯 Mi Tablero Kanban</h1>
        <div class="dashboard-stats">
            <span>Total: <span class="stat-value"><?= $totalTasks ?></span></span>
            <span>• Vencidas: <span class="stat-value"><?= count($vencidas) ?></span></span>
            <span>• Hoy: <span class="stat-value"><?= count($hoy) ?></span></span>
            <span>• Semana: <span class="stat-value"><?= count($semana) ?></span></span>
            <span>• Futuras: <span class="stat-value"><?= count($futuras) ?></span></span>
        </div>
    </div>
    
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
</div>
