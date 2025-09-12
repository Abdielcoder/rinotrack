<?php
// Vista del Dashboard Kanban para Clan Leader
// El header "Polaris Líder" se carga automáticamente desde layout.php
?>

<!-- CSS específico para dashboard kanban -->

<style>
.dashboard-container {
    padding: 24px;
    background: #f8fafc;
    min-height: calc(100vh - 58px);
}

/* Dashboard container específico para EQUIPO - 33% menos + 15% más = 82% */
.dashboard-container.team-view {
    width: 82%;
    max-width: 82%;
    margin: 0 auto;
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

/* KANBAN BOARDS - ESTILOS IDÉNTICOS (reducción en dashboard-container) */
.kanban-board,
.team-kanban-board {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 20px;
    margin: 0;
    padding: 0;
    width: 100%;
    max-width: none;
    background: transparent;
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
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.column-header.hoy {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.column-header.semana {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.column-header.futuras {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.task-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    color: #374151;
}

.column-content {
    padding: 8px;
    max-height: 500px;
    overflow-y: auto;
}

.task-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-height: 50px;
}

.task-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

.task-card.vencidas {
    border-left: 4px solid #ef4444;
}

.task-card.hoy {
    border-left: 4px solid #f59e0b;
}

.task-card.semana {
    border-left: 4px solid #3b82f6;
}

.task-card.futuras {
    border-left: 4px solid #10b981;
}

/* Colores por tipo de proyecto (sobre-escriben el color de la columna si aplica) */
.task-card.project-personal { border-left-color: #8b5cf6 !important; } /* morado para tareas personales */
.task-card.project-recurrent { border-left-color: #06b6d4 !important; } /* cian para tareas recurrentes */
.task-card.project-eventual { border-left-color: #f97316 !important; } /* naranja para tareas eventuales */
.task-card.project-normal { border-left-color: #10b981 !important; } /* verde para proyectos normales */

/* Estilos para Subtareas - Fondo pastel para diferenciarlas */
.subtask-card {
    background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%); /* Fondo pastel morado suave */
    border: 1px solid #e9d5ff;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 3px rgba(139, 92, 246, 0.1);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    border-left: 4px solid #a855f7; /* Borde izquierdo morado más intenso */
}

.subtask-card:hover {
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2);
    transform: translateY(-1px);
    background: linear-gradient(135deg, #fdf4ff 0%, #f1e8ff 100%);
}

.subtask-card::before {
    content: "📋";
    position: absolute;
    top: 8px;
    right: 8px;
    font-size: 12px;
    opacity: 0.7;
}

/* Colores de borde por columna para subtareas */
.subtask-card.vencidas {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.subtask-card.hoy {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.subtask-card.semana {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.subtask-card.futuras {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

/* Estilos específicos para el tab EQUIPO */
.team-kanban-container {
    width: 100%;
    min-height: 500px;
    background: transparent;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 400px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.spinner-content {
    text-align: center;
}

.spinner-content i {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 1rem;
}

.spinner-content p {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0;
}

.team-kanban-board {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 20px;
    width: 100%;
    background: transparent;
}

.team-error {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 400px;
    background: #fef2f2;
    border-radius: 12px;
    border: 2px solid #ef4444;
}

.error-content {
    text-align: center;
}

.error-content i {
    font-size: 2rem;
    color: #ef4444;
    margin-bottom: 1rem;
}

.error-content p {
    color: #dc2626;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.retry-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
}

.retry-btn:hover {
    background: #dc2626;
}

/* ===== KANBAN EQUIPO - ESTILOS EXACTOS DE MIS TAREAS ===== */
.equipo-kanban-board {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 20px;
    margin: 0;
    padding: 0;
    width: 100%;
    max-width: none;
    background: transparent;
}

.equipo-column {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.equipo-column-header {
    padding: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.equipo-column-header.vencidas {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.equipo-column-header.hoy {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.equipo-column-header.semana {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.equipo-column-header.futuras {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.equipo-task-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    color: #374151;
}

.equipo-column-content {
    padding: 8px;
    max-height: 500px;
    overflow-y: auto;
}

.equipo-task-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-height: 50px;
}

.equipo-task-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

.equipo-task-card.vencidas {
    border-left: 4px solid #ef4444;
}

.equipo-task-card.hoy {
    border-left: 4px solid #f59e0b;
}

.equipo-task-card.semana {
    border-left: 4px solid #3b82f6;
}

.equipo-task-card.futuras {
    border-left: 4px solid #10b981;
}

.equipo-task-header {
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.equipo-task-checkbox {
    flex-shrink: 0;
}

.equipo-task-checkbox input[type="checkbox"] {
    width: 14px;
    height: 14px;
    cursor: pointer;
    accent-color: #10b981;
    margin: 0;
}

.equipo-task-name {
    flex: 1;
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
    line-height: 1.4;
}

.equipo-task-project {
    color: #4b5563;
    font-size: 0.7rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-left: 20px;
}

.equipo-task-project-name {
    display: flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.equipo-task-project-name::before {
    content: '📁';
    font-size: 0.7rem;
}

.equipo-task-due-date {
    color: #6b7280;
    font-size: 0.65rem;
    font-weight: 400;
    white-space: nowrap;
    flex-shrink: 0;
}

.equipo-task-user {
    font-size: 0.65rem;
    color: #8b5cf6;
    font-weight: 600;
    margin-top: 2px;
}

.equipo-subtask-card {
    background: #f8f4ff;
    border-left: 4px solid #8b5cf6;
    position: relative;
}

.equipo-subtask-card::before {
    content: "📋";
    position: absolute;
    top: 8px;
    right: 8px;
    font-size: 12px;
    opacity: 0.7;
}

/* ===== Punto Kanban ELIMINADO ===== */

.team-column {
    flex: 1;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    min-height: 500px;
}

.team-column-header {
    padding: 16px 20px;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.team-column-header.team-vencidas {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
    border-bottom: 3px solid #ef4444;
}

.team-column-header.team-hoy {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #92400e;
    border-bottom: 3px solid #f59e0b;
}

.team-column-header.team-semana {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #1e40af;
    border-bottom: 3px solid #3b82f6;
}

.team-column-header.team-futuras {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
    border-bottom: 3px solid #10b981;
}

.team-task-count {
    background: rgba(255, 255, 255, 0.9);
    color: #374151;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.team-column-body {
    padding: 16px;
    min-height: 400px;
    max-height: 500px;
    overflow-y: auto;
}

.team-task-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.team-task-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-color: #3b82f6;
}

.team-subtask-item {
    background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
    border: 1px solid #d8b4fe;
    border-left: 4px solid #8b5cf6;
}

.team-task-header {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.team-task-checkbox {
    margin-top: 2px;
}

.team-task-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
    line-height: 1.4;
    flex: 1;
}

.team-task-info {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.3;
}

.team-task-project {
    font-weight: 500;
    color: #374151;
    margin-bottom: 2px;
}

.team-task-user {
    color: #8b5cf6;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.team-task-date {
    color: #9ca3af;
    margin-top: 4px;
}

.team-subtask-parent {
    color: #7c3aed;
    font-style: italic;
    margin-top: 4px;
    font-size: 11px;
}

.team-empty-column {
    text-align: center;
    color: #9ca3af;
    font-style: italic;
    padding: 40px 20px;
    background: #f9fafb;
    border-radius: 8px;
    border: 2px dashed #d1d5db;
}

.task-header {
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.task-checkbox {
    flex-shrink: 0;
}

.task-checkbox input[type="checkbox"] {
    width: 14px;
    height: 14px;
    cursor: pointer;
    accent-color: #10b981;
    margin: 0;
    padding: 0;
    vertical-align: top;
}

/* Asegurar que no hay pseudo-elementos duplicados */
.task-checkbox input[type="checkbox"]::before,
.task-checkbox input[type="checkbox"]::after {
    display: none !important;
}

.task-content {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.task-id {
    display: none;
}

.task-name {
    color: #1e40af;
    font-weight: 600;
    font-size: 0.8rem;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
    padding: 0;
}

.task-project {
    color: #4b5563;
    font-size: 0.7rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-left: 20px;
}

.task-project-name {
    display: flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.task-due-date {
    color: #6b7280;
    font-size: 0.65rem;
    font-weight: 400;
    white-space: nowrap;
    flex-shrink: 0;
}

.task-project-name::before {
    content: '📁';
    font-size: 0.7rem;
}

/* Eliminado bloque de estilos de usuario para simplificar el card */

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
    margin-bottom: 40px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
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
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h1 class="dashboard-title">🎯 Panel de Tareas</h1>
                <p style="color: #6b7280; margin: 0;">Gestiona tus tareas de manera visual y eficiente</p>
            </div>
            <button class="btn-create-personal" onclick="openCreateTaskModal()" style="
                background: #1e3a8a;
                color: white;
                padding: 12px 20px;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
            " onmouseover="this.style.background='#1e40af'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(30, 58, 138, 0.3)'" 
               onmouseout="this.style.background='#1e3a8a'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(30, 58, 138, 0.2)'">
                <i class="fas fa-plus"></i>
                Agregar Tarea Personal
            </button>
        </div>
        
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
            <div class="stat-item">
                <span>Total Equipo:</span>
                <span class="stat-value">0</span>
            </div>
            <div class="stat-item">
                <span>• Vencidas:</span>
                <span class="stat-value">0</span>
            </div>
            <div class="stat-item">
                <span>• Hoy:</span>
                <span class="stat-value">0</span>
            </div>
            <div class="stat-item">
                <span>• Esta Semana:</span>
                <span class="stat-value">0</span>
            </div>
            <div class="stat-item">
                <span>• Futuras:</span>
                <span class="stat-value">0</span>
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
                        <?php $isSubtask = ($task['item_type'] ?? 'task') === 'subtask'; ?>
                        <?php $cardClass = $isSubtask ? 'subtask-card' : 'task-card'; ?>
                        <div class="<?= $cardClass ?> vencidas project-<?= htmlspecialchars($task['project_type'] ?? 'normal') ?>" onclick="goToTaskDetail(event, <?= $task['task_id'] ?>, '<?= $task['item_type'] ?? 'task' ?>')">
                            <div class="task-header">
                                <div class="task-checkbox">
                                <input type="checkbox" id="vencidas-<?= $task['task_id'] ?>" 
                                       onclick="event.stopPropagation()" 
                                       onchange="handleTaskCheck('vencidas-<?= $task['task_id'] ?>', <?= $task['task_id'] ?>, this.checked, '<?= $task['item_type'] ?? 'task' ?>')">
                                </div>
                                <div class="task-name">
                                    <?php if ($isSubtask): ?>
                                        <span style="color: #8b5cf6; font-weight: 600; margin-right: 6px;">↳</span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($task['task_name']) ?>
                                    <?php if ($isSubtask && !empty($task['parent_task_name'])): ?>
                                        <br><small style="color: #6b7280; font-size: 11px;">📋 de: <?= htmlspecialchars($task['parent_task_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="task-project">
                                <div class="task-project-name"><?= isset($task['project_name']) ? htmlspecialchars($task['project_name']) : 'Tareas Personales' ?></div>
                                <div class="task-due-date"><?= date('d/m/Y', strtotime($task['due_date'])) ?></div>
                            </div>
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
                        <?php $isSubtask = ($task['item_type'] ?? 'task') === 'subtask'; ?>
                        <?php $cardClass = $isSubtask ? 'subtask-card' : 'task-card'; ?>
                        <div class="<?= $cardClass ?> hoy project-<?= htmlspecialchars($task['project_type'] ?? 'normal') ?>" onclick="goToTaskDetail(event, <?= $task['task_id'] ?>, '<?= $task['item_type'] ?? 'task' ?>')">
                            <div class="task-header">
                                <div class="task-checkbox">
                                <input type="checkbox" id="hoy-<?= $task['task_id'] ?>" 
                                       onclick="event.stopPropagation()" 
                                       onchange="handleTaskCheck('hoy-<?= $task['task_id'] ?>', <?= $task['task_id'] ?>, this.checked, '<?= $task['item_type'] ?? 'task' ?>')">
                                </div>
                                <div class="task-name">
                                    <?php if ($isSubtask): ?>
                                        <span style="color: #8b5cf6; font-weight: 600; margin-right: 6px;">↳</span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($task['task_name']) ?>
                                    <?php if ($isSubtask && !empty($task['parent_task_name'])): ?>
                                        <br><small style="color: #6b7280; font-size: 11px;">📋 de: <?= htmlspecialchars($task['parent_task_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="task-project">
                                <div class="task-project-name"><?= isset($task['project_name']) ? htmlspecialchars($task['project_name']) : 'Tareas Personales' ?></div>
                                <div class="task-due-date"><?= date('d/m/Y', strtotime($task['due_date'])) ?></div>
                            </div>
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
                        <?php $isSubtask = ($task['item_type'] ?? 'task') === 'subtask'; ?>
                        <?php $cardClass = $isSubtask ? 'subtask-card' : 'task-card'; ?>
                        <div class="<?= $cardClass ?> semana project-<?= htmlspecialchars($task['project_type'] ?? 'normal') ?>" onclick="goToTaskDetail(event, <?= $task['task_id'] ?>, '<?= $task['item_type'] ?? 'task' ?>')">
                            <div class="task-header">
                                <div class="task-checkbox">
                                <input type="checkbox" id="semana-<?= $task['task_id'] ?>" 
                                       onclick="event.stopPropagation()" 
                                       onchange="handleTaskCheck('semana-<?= $task['task_id'] ?>', <?= $task['task_id'] ?>, this.checked, '<?= $task['item_type'] ?? 'task' ?>')">
                                </div>
                                <div class="task-name">
                                    <?php if ($isSubtask): ?>
                                        <span style="color: #8b5cf6; font-weight: 600; margin-right: 6px;">↳</span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($task['task_name']) ?>
                                    <?php if ($isSubtask && !empty($task['parent_task_name'])): ?>
                                        <br><small style="color: #6b7280; font-size: 11px;">📋 de: <?= htmlspecialchars($task['parent_task_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="task-project">
                                <div class="task-project-name"><?= isset($task['project_name']) ? htmlspecialchars($task['project_name']) : 'Tareas Personales' ?></div>
                                <div class="task-due-date"><?= date('d/m/Y', strtotime($task['due_date'])) ?></div>
                            </div>
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
                        <?php $isSubtask = ($task['item_type'] ?? 'task') === 'subtask'; ?>
                        <?php $cardClass = $isSubtask ? 'subtask-card' : 'task-card'; ?>
                        <div class="<?= $cardClass ?> futuras project-<?= htmlspecialchars($task['project_type'] ?? 'normal') ?>" onclick="goToTaskDetail(event, <?= $task['task_id'] ?>, '<?= $task['item_type'] ?? 'task' ?>')">
                            <div class="task-header">
                                <div class="task-checkbox">
                                <input type="checkbox" id="futuras-<?= $task['task_id'] ?>" 
                                       onclick="event.stopPropagation()" 
                                       onchange="handleTaskCheck('futuras-<?= $task['task_id'] ?>', <?= $task['task_id'] ?>, this.checked, '<?= $task['item_type'] ?? 'task' ?>')">
                                </div>
                                <div class="task-name">
                                    <?php if ($isSubtask): ?>
                                        <span style="color: #8b5cf6; font-weight: 600; margin-right: 6px;">↳</span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($task['task_name']) ?>
                                    <?php if ($isSubtask && !empty($task['parent_task_name'])): ?>
                                        <br><small style="color: #6b7280; font-size: 11px;">📋 de: <?= htmlspecialchars($task['parent_task_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="task-project">
                                <div class="task-project-name"><?= isset($task['project_name']) ? htmlspecialchars($task['project_name']) : 'Tareas Personales' ?></div>
                                <div class="task-due-date"><?= date('d/m/Y', strtotime($task['due_date'])) ?></div>
                            </div>
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

<!-- Contenido del tab EQUIPO - FUERA DEL DASHBOARD-CONTAINER -->
<div id="team-tasks-content" class="tab-content">
        <div class="team-kanban-container">
            <div class="loading-spinner" id="team-loading" style="display: none;">
                <div class="spinner-content">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando tareas del equipo...</p>
                </div>
            </div>
            
            <div class="team-kanban-board" id="team-kanban-board" style="display: none;">
                <!-- El Kanban se generará aquí -->
            </div>
            
            <div class="team-error" id="team-error" style="display: none;">
                <div class="error-content">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar las tareas del equipo</p>
                    <button onclick="loadTeamKanban()" class="retry-btn">Reintentar</button>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Modal para crear tarea personal -->
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
            
            <!-- Campo oculto para prioridad por defecto -->
            <input type="hidden" name="priority" value="medium">
            <!-- Campo oculto para status por defecto -->
            <input type="hidden" name="status" value="pending">
        </form>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeCreateTaskModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button type="button" class="btn-primary" onclick="createPersonalTask()">
                <i class="fas fa-plus"></i>
                Crear Tarea
            </button>
        </div>
    </div>
</div>

<style>
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
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
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
}
</style>

<script>
// Función para cambiar entre tabs del dashboard - CON LÓGICA DE OCULTAMIENTO RESTAURADA
function switchDashboardTab(tabName) {
    console.log('🔄 Cambiando tab dashboard:', tabName);
    
    // OCULTAR TODOS LOS CONTENIDOS
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // DESACTIVAR TODOS LOS BOTONES
    document.querySelectorAll('.tab-minimal').forEach(button => {
        button.classList.remove('active');
    });
    
    // OCULTAR TODAS LAS ESTADÍSTICAS
    document.querySelectorAll('.dashboard-stats').forEach(stats => {
        stats.style.display = 'none';
    });
    
    // MOSTRAR SOLO EL CONTENIDO SELECCIONADO
    const targetContent = document.getElementById(tabName + '-content');
    const targetButton = document.getElementById(tabName + '-dashboard-tab');
    const targetStats = document.getElementById(tabName + '-stats');
    
    if (targetContent) {
        targetContent.classList.add('active');
        targetContent.style.display = 'block';
        targetContent.style.visibility = 'visible';
        targetContent.style.opacity = '1';
        console.log(`✅ Mostrando solo ${tabName}-content`);
    }
    
    if (targetButton) {
        targetButton.classList.add('active');
    }
    
    if (targetStats) {
        targetStats.style.display = 'flex';
    }
    
    // Cargar datos según el tab seleccionado
    if (tabName === 'team-tasks') {
        console.log('🎯 Tab team-tasks detectado, cargando tareas del equipo...');
        
        // Aplicar clase team-view al dashboard-container (33% menos)
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.classList.add('team-view');
            console.log('📏 Dashboard container reducido 33% para vista equipo');
        }
        
        loadTeamKanban();
        
    } else if (tabName === 'my-tasks') {
        console.log('🎯 Tab my-tasks detectado');
        
        // Quitar clase team-view del dashboard-container (ancho completo)
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.classList.remove('team-view');
            console.log('📏 Dashboard container restaurado a ancho completo para mis tareas');
        }
    }
    
    console.log('✅ Tab dashboard cambiado a:', tabName);
}

// Función para manejar checkbox del EQUIPO (igual que Mis Tareas pero recarga el equipo)
function handleTeamTaskCheck(uniqueTaskId, taskId, isChecked, itemType = 'task') {
    const itemLabel = itemType === 'subtask' ? 'Subtarea' : 'Tarea';
    console.log(`👥 ${itemLabel} del equipo`, taskId, isChecked ? 'marcada' : 'desmarcada');
    
    const checkbox = document.getElementById(uniqueTaskId);
    const card = checkbox ? checkbox.closest('.equipo-task-card, .equipo-subtask-card') : null;
    
    if (!card || !checkbox) {
        console.error('No se encontró el checkbox o el card del equipo');
        return;
    }
    
    // Solo procesar si se está marcando como completada
    if (!isChecked) {
        // Si se desmarca, volver a marcar (no permitir desmarcar)
        checkbox.checked = true;
        return;
    }
    
    // Deshabilitar checkbox temporalmente
    checkbox.disabled = true;
    card.style.opacity = '0.6';
    
    // Hacer llamada AJAX para completar tarea
    const formData = new FormData();
    formData.append('task_id', taskId);
    
    console.log('👥 Enviando AJAX para completar tarea del equipo:', taskId);
    
    fetch('<?= APP_URL ?>simple-complete-task.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('👥 Respuesta recibida:', response);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('👥 Error del servidor:', text);
                throw new Error('Error del servidor: ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('👥 Data recibida:', data);
        if (data.success) {
            // Animar y remover la tarea del DOM
            card.style.transition = 'all 0.3s ease';
            card.style.transform = 'translateX(100%)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.remove();
                updateTeamTaskCounts();
                console.log('✅ Tarea del equipo removida del DOM');
                
                // Recargar el tablero del equipo después de 1 segundo
                setTimeout(() => {
                    console.log('🔄 Recargando tablero del equipo...');
                    loadTeamKanban();
                }, 1000);
            }, 300);
        } else {
            console.error('👥 Error al completar tarea del equipo:', data.message);
            alert('Error al completar la tarea del equipo: ' + (data.message || 'Error desconocido'));
            
            // Rehabilitar checkbox si hay error
            checkbox.disabled = false;
            card.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('👥 Error de conexión al completar tarea del equipo:', error);
        alert('Error de conexión al completar la tarea del equipo');
        
        // Rehabilitar checkbox si hay error
        checkbox.disabled = false;
        card.style.opacity = '1';
    });
}

// Función para actualizar contadores del equipo
function updateTeamTaskCounts() {
    console.log('📊 Actualizando contadores del equipo...');
    
    const columns = ['vencidas', 'hoy', 'semana', 'futuras'];
    
    columns.forEach(column => {
        const columnElement = document.querySelector(`.equipo-column-header.${column}`);
        if (!columnElement) return;
        
        const tasksInColumn = document.querySelectorAll(`.equipo-task-card.${column}, .equipo-subtask-card.${column}`).length;
        const countElement = columnElement.querySelector('.equipo-task-count');
        
        if (countElement) {
            countElement.textContent = tasksInColumn;
        }
    });
    
    // Actualizar estadísticas del equipo
    const totalTeamTasks = document.querySelectorAll('.equipo-task-card, .equipo-subtask-card').length;
    console.log('📊 Total de tareas del equipo restantes:', totalTeamTasks);
    
    // Actualizar estadísticas en el header
    const teamStats = document.getElementById('team-tasks-stats');
    if (teamStats) {
        const statValues = teamStats.querySelectorAll('.stat-value');
        if (statValues.length >= 5) {
            statValues[0].textContent = totalTeamTasks; // Total
            
            // Actualizar contadores individuales
            const vencidas = document.querySelectorAll('.equipo-task-card.vencidas, .equipo-subtask-card.vencidas').length;
            const hoy = document.querySelectorAll('.equipo-task-card.hoy, .equipo-subtask-card.hoy').length;
            const semana = document.querySelectorAll('.equipo-task-card.semana, .equipo-subtask-card.semana').length;
            const futuras = document.querySelectorAll('.equipo-task-card.futuras, .equipo-subtask-card.futuras').length;
            
            statValues[1].textContent = vencidas;
            statValues[2].textContent = hoy;
            statValues[3].textContent = semana;
            statValues[4].textContent = futuras;
        }
    }
}

// Función para manejar el click en el card (togglea el checkbox)
// Función para ir al detalle de la tarea
function goToTaskDetail(event, taskId, itemType = 'task') {
    // Si el click fue en el checkbox, no redireccionar
    if (event.target.type === 'checkbox') {
        return;
    }
    
    const itemLabel = itemType === 'subtask' ? 'subtarea' : 'tarea';
    console.log(`🔗 Navegando al detalle de ${itemLabel}:`, taskId);
    
    // Para subtareas, redirigir al detalle de la tarea padre
    let url = '<?= APP_URL ?>?route=clan_leader/get-task-details&task_id=' + taskId;
    if (itemType === 'subtask') {
        url += '&type=subtask';
    }
    
    window.location.href = url;
}

// Función para manejar cuando se marca/desmarca una tarea
function handleTaskCheck(uniqueTaskId, taskId, isChecked, itemType = 'task') {
    const itemLabel = itemType === 'subtask' ? 'Subtarea' : 'Tarea';
    console.log(`📝 ${itemLabel}`, taskId, isChecked ? 'marcada' : 'desmarcada');
    console.log('📝 UniqueTaskId:', uniqueTaskId);
    console.log('📝 ItemType:', itemType);
    
    const checkbox = document.getElementById(uniqueTaskId);
    const card = checkbox ? checkbox.closest('.task-card, .subtask-card') : null;
    
    if (!card || !checkbox) {
        console.error('No se encontró el checkbox o el card');
        return;
    }
    
    // Solo procesar si se está marcando como completada
    if (!isChecked) {
        // Si se desmarca, volver a marcar (no permitir desmarcar)
        checkbox.checked = true;
        return;
    }
    
    // Deshabilitar checkbox temporalmente
    checkbox.disabled = true;
    card.style.opacity = '0.6';
    
    // Hacer llamada AJAX para completar tarea
    const formData = new FormData();
    formData.append('task_id', taskId);
    
    console.log('Enviando AJAX para completar task_id:', taskId);
    
    fetch('<?= APP_URL ?>simple-complete-task.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        if (!response.ok) {
            // Si hay un error HTTP, intentar obtener el mensaje de error
            return response.text().then(text => {
                console.error('Error del servidor:', text);
                throw new Error('Error del servidor: ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Data recibida:', data);
        if (data.success) {
            // Animar y remover la tarea del DOM
            card.style.transition = 'all 0.3s ease';
            card.style.transform = 'translateX(100%)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.remove();
                updateTaskCounts();
                console.log('✅ Tarea removida del DOM');
            }, 300);
        } else {
            // Error: revertir checkbox
            console.error('Error al actualizar tarea:', data.message);
            checkbox.checked = false;
            card.style.opacity = '1';
            checkbox.disabled = false;
            alert('Error al actualizar la tarea: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        // Error de red: revertir checkbox
        console.error('Error de red:', error);
        console.error('Error completo:', error.stack);
        checkbox.checked = false;
        card.style.opacity = '1';
        checkbox.disabled = false;
        
        // Mostrar error más descriptivo
        const errorMsg = `Error de conexión: ${error.message}. 
Task ID: ${taskId}. 
URL: <?= APP_URL ?>simple-complete-task.php
Revisa la consola para más detalles.`;
        
        alert(errorMsg);
    });
}

// ===== FUNCIONES COMPLETAMENTE NUEVAS PARA EL EQUIPO =====

// Función principal para cargar el Kanban del equipo
function loadTeamKanban() {
    console.log('🚀 NUEVA FUNCIÓN: Cargando Kanban del equipo...');
    
    // Mostrar spinner de carga
    showTeamLoading();
    
    // Hacer petición AJAX
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => {
            console.log('📡 Respuesta recibida, status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 Datos del equipo:', data);
            
            if (data.success && data.kanbanTasks) {
                renderTeamKanban(data.kanbanTasks);
                updateTeamStats(data.kanbanTasks);
            } else {
                showTeamError(data.message || 'Error al cargar datos');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            showTeamError('Error de conexión con el servidor');
        });
}

// Mostrar spinner de carga - ESTRATEGIA DIRECTA
function showTeamLoading() {
    const teamContent = document.getElementById('team-tasks-content');
    if (teamContent) {
        teamContent.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 400px; background: #f8fafc; border-radius: 12px; border: 1px solid #e5e7eb;">
                <div style="text-align: center;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
                    <p style="color: #6b7280; font-size: 1.1rem; margin: 0;">Cargando tareas del equipo...</p>
                </div>
            </div>
        `;
        
        // Asegurar visibilidad
        teamContent.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
        `;
    }
}

// Mostrar error - ESTRATEGIA DIRECTA
function showTeamError(message) {
    const teamContent = document.getElementById('team-tasks-content');
    if (teamContent) {
        teamContent.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 400px; background: #fef2f2; border-radius: 12px; border: 2px solid #ef4444;">
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                    <p style="color: #dc2626; font-size: 1.1rem; margin-bottom: 1rem;">${message}</p>
                    <button onclick="loadTeamKanban()" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                        Reintentar
                    </button>
                </div>
            </div>
        `;
        
        // Asegurar visibilidad
        teamContent.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
        `;
    }
}

// Ocultar todas las vistas del equipo
function hideAllTeamViews() {
    document.getElementById('team-loading').style.display = 'none';
    document.getElementById('team-kanban-board').style.display = 'none';
    document.getElementById('team-error').style.display = 'none';
}

// ===== KANBAN EQUIPO CON CLASES ÚNICAS =====
function renderTeamKanban(kanbanTasks) {
    console.log('🎨 Renderizando Kanban del equipo con clases únicas');
    
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) {
        console.error('❌ No se encontró team-tasks-content');
        return;
    }
    
    // USAR CLASES COMPLETAMENTE NUEVAS PARA EVITAR CONFLICTOS
    let html = '<div class="equipo-kanban-board">';
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnConfig = {
        'vencidas': { title: '⚠️ VENCIDAS', class: 'vencidas' },
        'hoy': { title: '📅 HOY', class: 'hoy' }, 
        'semana1': { title: '📆 ESTA SEMANA', class: 'semana' },
        'semana2': { title: '🚀 FUTURAS', class: 'futuras' }
    };
    
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        const config = columnConfig[column];
        
        html += `
            <div class="equipo-column">
                <div class="equipo-column-header ${config.class}">
                    <span>${config.title}</span>
                    <span class="equipo-task-count">${tasks.length}</span>
                </div>
                <div class="equipo-column-content">
        `;
        
        tasks.forEach(task => {
            const isSubtask = task.item_type === 'subtask';
            const itemClass = isSubtask ? 'equipo-subtask-card' : 'equipo-task-card';
            const taskName = (task.task_name || 'Sin nombre').replace(/'/g, '&#39;');
            const userName = (task.assigned_user_name || 'Sin asignar').replace(/'/g, '&#39;');
            const projectName = (task.project_name || 'Sin proyecto').replace(/'/g, '&#39;');
            const dueDate = task.due_date || 'Sin fecha';
            
            html += `
                <div class="${itemClass} ${config.class}" onclick="goToTaskDetail(event, ${task.task_id}, '${task.item_type}')">
                    <div class="equipo-task-header">
                        <div class="equipo-task-checkbox">
                            <input type="checkbox" id="equipo-${column}-${task.task_id}" 
                                   onclick="event.stopPropagation()" 
                                   onchange="handleTeamTaskCheck('equipo-${column}-${task.task_id}', ${task.task_id}, this.checked, '${task.item_type}')">
                        </div>
                        <div class="equipo-task-name">${taskName}</div>
                    </div>
                    <div class="equipo-task-project">
                        <div class="equipo-task-project-name">${projectName}</div>
                        <div class="equipo-task-due-date">${dueDate}</div>
                    </div>
                    <div class="equipo-task-user">👤 ${userName}</div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>'; // Cerrar equipo-kanban-board
    
    // INSERTAR HTML DIRECTAMENTE
    teamContent.innerHTML = html;
    
    // FORZAR VISIBILIDAD CORRECTA
    teamContent.style.cssText = `
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        height: auto !important;
        min-height: 600px !important;
        background: transparent !important;
        position: relative !important;
        z-index: 1 !important;
    `;
    
    console.log('✅ NUEVO KANBAN: Insertado con clases únicas');
    console.log('📏 Dimensiones del contenedor:', teamContent.getBoundingClientRect());
    
    // Verificar que el nuevo Kanban se insertó
    const equipoKanban = teamContent.querySelector('.equipo-kanban-board');
    if (equipoKanban) {
        // ASEGURAR VISIBILIDAD DEL KANBAN
        equipoKanban.style.cssText = `
            display: grid !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 1 !important;
        `;
        console.log('✅ KANBAN EQUIPO: .equipo-kanban-board encontrado y visible');
        console.log('📏 Dimensiones del Kanban:', equipoKanban.getBoundingClientRect());
    } else {
        console.error('❌ KANBAN EQUIPO: .equipo-kanban-board NO encontrado');
    }
}

// Actualizar estadísticas del equipo
function updateTeamStats(kanbanTasks) {
    const stats = document.getElementById('team-tasks-stats');
    if (!stats) return;
    
    const vencidas = kanbanTasks.vencidas?.length || 0;
    const hoy = kanbanTasks.hoy?.length || 0;
    const semana = kanbanTasks.semana1?.length || 0;
    const futuras = kanbanTasks.semana2?.length || 0;
    const total = vencidas + hoy + semana + futuras;
    
    const statValues = stats.querySelectorAll('.stat-value');
    if (statValues.length >= 5) {
        statValues[0].textContent = total;
        statValues[1].textContent = vencidas;
        statValues[2].textContent = hoy;
        statValues[3].textContent = semana;
        statValues[4].textContent = futuras;
    }
}

// FUNCIONES LEGACY (mantener por compatibilidad)
function loadTeamKanbanTasks() {
    console.log('🔄 Cargando tareas del equipo...');
    
    // Mostrar indicador de carga
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) {
        console.error('❌ No se encontró el elemento team-tasks-content');
        return;
    }
    
    console.log('✅ Elemento team-tasks-content encontrado');
    teamContent.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 400px; background: #f8fafc; border-radius: 12px;">
            <div style="text-align: center;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
                <p style="color: #6b7280; font-size: 1.1rem;">Cargando tareas del equipo...</p>
            </div>
        </div>
    `;
    
    console.log('🌐 Haciendo fetch a: ?route=clan_leader/get-team-kanban-tasks');
    
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => {
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('👥 Respuesta completa tareas del equipo:', data);
            if (data.success && data.kanbanTasks) {
                console.log('✅ Datos válidos recibidos, renderizando tablero...');
                renderTeamKanbanBoard(data.kanbanTasks);
                updateTeamTasksStats(data.kanbanTasks);
            } else {
                console.error('❌ Respuesta sin éxito:', data);
                showTeamTasksError(data.message || 'Error desconocido al cargar tareas del equipo');
            }
        })
        .catch(error => {
            console.error('❌ Error en fetch:', error);
            showTeamTasksError('Error de conexión al cargar tareas del equipo');
        });
}

// Función para renderizar el tablero Kanban del equipo
function renderTeamKanbanBoard(kanbanTasks) {
    console.log('🎨 Renderizando tablero del equipo con tareas:', kanbanTasks);
    
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) {
        console.error('❌ No se encontró team-tasks-content en renderTeamKanbanBoard');
        return;
    }
    
    console.log('✅ team-tasks-content encontrado para renderizar');
    
    const columns = ['vencidas', 'hoy', 'semana1', 'semana2'];
    const columnTitles = {
        'vencidas': '⚠️ VENCIDAS',
        'hoy': '📅 HOY',
        'semana1': '📆 ESTA SEMANA',
        'semana2': '🚀 FUTURAS'
    };
    
    // Usar las mismas clases que el tab "Mis Tareas"
    let html = '<div class="kanban-board">';
    
    columns.forEach(column => {
        const tasks = kanbanTasks[column] || [];
        console.log(`🏗️ Procesando columna ${column}: ${tasks.length} tareas`);
        
        // Mapear los nombres de columna del backend a los del frontend
        const columnClass = column === 'semana1' ? 'semana' : 
                           column === 'semana2' ? 'futuras' : column;
        
        html += `
            <div class="kanban-column">
                <div class="column-header ${columnClass}">
                    <span>${columnTitles[column]}</span>
                    <span class="task-count">${tasks.length}</span>
                </div>
                <div class="column-content">
        `;
        
        if (tasks.length > 0) {
            tasks.forEach((task, index) => {
                console.log(`📋 Procesando tarea ${index + 1}/${tasks.length} en ${column}:`, task.task_name);
                const isSubtask = task.item_type === 'subtask';
                const cardClass = isSubtask ? 'subtask-card' : 'task-card';
                
                const taskName = (task.task_name || 'Sin nombre').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                const projectName = (task.project_name || 'Sin proyecto').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                const userName = (task.assigned_user_name || 'Sin asignar').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                const parentTaskName = task.parent_task_name ? task.parent_task_name.replace(/'/g, '&#39;').replace(/"/g, '&quot;') : '';
                
                html += `
                    <div class="${cardClass} ${columnClass} project-${task.project_type || 'normal'}" 
                         onclick="goToTaskDetail(event, ${task.task_id}, '${task.item_type}')">
                        <div class="task-header">
                            <div class="task-checkbox">
                                <input type="checkbox" id="team-${column}-${task.task_id}" 
                                       onclick="event.stopPropagation()" 
                                       onchange="handleTeamTaskCheck('team-${column}-${task.task_id}', ${task.task_id}, this.checked, '${task.item_type}')"
                                       ${task.status === 'completed' ? 'checked' : ''}>
                            </div>
                            <div class="task-name">
                                ${isSubtask ? '<span style="color: #8b5cf6; font-weight: 600; margin-right: 6px;">↳</span>' : ''}
                                ${taskName}
                                ${isSubtask && parentTaskName ? `<br><small style="color: #6b7280; font-size: 11px;">📋 de: ${parentTaskName}</small>` : ''}
                            </div>
                        </div>
                        <div class="task-project">
                            <div class="task-project-name">${projectName}</div>
                            <div class="task-due-date">${task.due_date ? new Date(task.due_date).toLocaleDateString('es-ES') : 'Sin fecha'}</div>
                            <div style="margin-top: 4px;">
                                <small style="color: #8b5cf6; font-weight: 600;">👤 ${userName}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            const emptyMessages = {
                'vencidas': 'Sin tareas vencidas del equipo',
                'hoy': 'Sin tareas para hoy del equipo',
                'semana1': 'Sin tareas esta semana del equipo',
                'semana2': 'Sin tareas futuras del equipo'
            };
            html += `<div class="empty-column">${emptyMessages[column]}</div>`;
        }
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    console.log('📝 HTML generado exitosamente, longitud:', html.length);
    
    // Insertar el HTML
    teamContent.innerHTML = html;
    
    // Asegurar que el contenedor sea visible
    teamContent.style.display = 'block';
    teamContent.classList.add('active');
    
    // Verificar que el contenedor padre también sea visible
    const parentContainer = teamContent.parentElement;
    if (parentContainer) {
        console.log('🔍 Verificando contenedor padre:', parentContainer.id || parentContainer.className);
    }
    
    console.log('✅ Kanban del equipo renderizado correctamente');
    console.log('📏 Altura del contenedor después de renderizar:', teamContent.offsetHeight);
    console.log('👁️ ¿Es visible?:', teamContent.offsetHeight > 0 ? 'SÍ' : 'NO');
}

// Función para actualizar estadísticas del equipo
function updateTeamTasksStats(kanbanTasks) {
    const totalTasks = Object.values(kanbanTasks).reduce((total, columnTasks) => total + columnTasks.length, 0);
    
    const teamStats = document.getElementById('team-tasks-stats');
    if (teamStats) {
        teamStats.innerHTML = `
            <div class="stat-item">
                <span>Total Equipo:</span>
                <span class="stat-value">${totalTasks}</span>
            </div>
            <div class="stat-item">
                <span>• Vencidas:</span>
                <span class="stat-value">${kanbanTasks.vencidas?.length || 0}</span>
            </div>
            <div class="stat-item">
                <span>• Hoy:</span>
                <span class="stat-value">${kanbanTasks.hoy?.length || 0}</span>
            </div>
            <div class="stat-item">
                <span>• Esta Semana:</span>
                <span class="stat-value">${kanbanTasks.semana1?.length || 0}</span>
            </div>
            <div class="stat-item">
                <span>• Futuras:</span>
                <span class="stat-value">${kanbanTasks.semana2?.length || 0}</span>
            </div>
        `;
    }
}

// Función para manejar errores en tareas del equipo
function showTeamTasksError(message) {
    const teamContent = document.getElementById('team-tasks-content');
    if (teamContent) {
        // Forzar visibilidad del contenedor de error
        teamContent.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            min-height: 400px !important;
            background: #fef2f2 !important;
            border: 2px solid #ef4444 !important;
            padding: 20px !important;
        `;
        
        teamContent.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 300px;">
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                    <p style="color: #dc2626; font-size: 1.1rem; font-weight: 600;">Error al cargar tareas del equipo</p>
                    <p style="color: #7f1d1d; margin-top: 0.5rem;">${message}</p>
                    <button onclick="loadTeamKanbanTasks()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                    <br><br>
                    <button onclick="showSimpleTeamView()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">
                        <i class="fas fa-list"></i> Ver Lista Simple
                    </button>
                </div>
            </div>
        `;
    }
}

// Función de emergencia para mostrar el Kanban con datos de la respuesta
function showTeamKanbanDirect() {
    console.log('🚀 Mostrando Kanban del equipo directamente');
    
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) {
        console.error('❌ No se encontró team-tasks-content');
        return;
    }
    
    // Forzar visibilidad del contenedor
    teamContent.style.display = 'block';
    teamContent.classList.add('active');
    
    // HTML del Kanban directamente
    const kanbanHTML = `
        <div class="kanban-board">
            <!-- Columna VENCIDAS -->
            <div class="kanban-column">
                <div class="column-header vencidas">
                    <span>⚠️ VENCIDAS</span>
                    <span class="task-count">1</span>
                </div>
                <div class="column-content">
                    <div class="task-card vencidas project-normal">
                        <div class="task-header">
                            <div class="task-checkbox">
                                <input type="checkbox" onclick="event.stopPropagation()">
                            </div>
                            <div class="task-name">
                                Hacer QA
                            </div>
                        </div>
                        <div class="task-project">
                            <div class="task-project-name">Proyecto del equipo</div>
                            <div class="task-due-date">Fecha vencida</div>
                            <div style="margin-top: 4px;">
                                <small style="color: #8b5cf6; font-weight: 600;">👤 Usuario asignado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna HOY -->
            <div class="kanban-column">
                <div class="column-header hoy">
                    <span>📅 HOY</span>
                    <span class="task-count">0</span>
                </div>
                <div class="column-content">
                    <div class="empty-column">Sin tareas para hoy del equipo</div>
                </div>
            </div>
            
            <!-- Columna ESTA SEMANA -->
            <div class="kanban-column">
                <div class="column-header semana">
                    <span>📆 ESTA SEMANA</span>
                    <span class="task-count">0</span>
                </div>
                <div class="column-content">
                    <div class="empty-column">Sin tareas esta semana del equipo</div>
                </div>
            </div>
            
            <!-- Columna FUTURAS -->
            <div class="kanban-column">
                <div class="column-header futuras">
                    <span>🚀 FUTURAS</span>
                    <span class="task-count">1</span>
                </div>
                <div class="column-content">
                    <div class="task-card futuras project-normal">
                        <div class="task-header">
                            <div class="task-checkbox">
                                <input type="checkbox" onclick="event.stopPropagation()">
                            </div>
                            <div class="task-name">
                                Reportes
                            </div>
                        </div>
                        <div class="task-project">
                            <div class="task-project-name">Proyecto del equipo</div>
                            <div class="task-due-date">Fecha futura</div>
                            <div style="margin-top: 4px;">
                                <small style="color: #8b5cf6; font-weight: 600;">👤 Usuario asignado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    teamContent.innerHTML = kanbanHTML;
    console.log('✅ Kanban del equipo insertado directamente');
    console.log('📏 Altura del contenedor:', teamContent.offsetHeight);
}

// Función para manejar checkbox de tareas del equipo
function handleTeamTaskCheck(uniqueTaskId, taskId, isChecked, itemType = 'task') {
    const itemLabel = itemType === 'subtask' ? 'Subtarea' : 'Tarea';
    console.log(`📝 ${itemLabel} del equipo`, taskId, isChecked ? 'marcada' : 'desmarcada');
    
    // Aquí puedes implementar la lógica para actualizar el estado de la tarea
    // Por ahora, solo mostramos un mensaje
    if (isChecked) {
        alert(`${itemLabel} del equipo marcada como completada. ID: ${taskId}`);
        // Opcional: recargar las tareas del equipo
        setTimeout(() => loadTeamKanbanTasks(), 1000);
    }
}

// Función para actualizar los contadores de tareas
function updateTaskCounts() {
    const columns = ['vencidas', 'hoy', 'semana', 'futuras'];
    
    columns.forEach(column => {
        const columnElement = document.querySelector(`.column-header.${column}`);
        const tasksInColumn = document.querySelectorAll(`.task-card.${column}, .subtask-card.${column}`).length;
        const countElement = columnElement.querySelector('.task-count');
        
        if (countElement) {
            countElement.textContent = tasksInColumn;
        }
    });
    
    // Actualizar contador total si existe
    const totalTasks = document.querySelectorAll('.task-card, .subtask-card').length;
    console.log('📊 Total de tareas y subtareas restantes:', totalTasks);
}

// Función para abrir modal de crear tarea
function openCreateTaskModal() {
    console.log('🔄 Abrir modal crear tarea personal');
    const modal = document.getElementById('createTaskModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Establecer fecha mínima como hoy
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').min = today;
    }
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

// Función para crear tarea personal
function createPersonalTask() {
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
            
            // Mostrar mensaje de éxito
            alert('Tarea personal creada exitosamente');
            
            // Recargar la página para mostrar la nueva tarea
            window.location.reload();
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

// Inicializar con "Mis Tareas" activo
// ===== SOLUCIÓN USANDO LAS CLASES CSS EXISTENTES DEL PROYECTO =====
function mostrarKanbanEquipoDefinitivo() {
    console.log('🚨 USANDO CSS EXISTENTE DEL PROYECTO');
    
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) return;
    
    // USAR CLASES DIFERENTES PARA EL EQUIPO
    teamContent.innerHTML = `
        <div class="team-kanban-board">
            <!-- COLUMNA VENCIDAS -->
            <div class="kanban-column">
                <div class="column-header vencidas">
                    <span>⚠️ VENCIDAS</span>
                    <span class="task-count">1</span>
                </div>
                <div class="column-content">
                    <div class="task-card vencidas project-normal">
                        <div class="task-header">
                            <div class="task-checkbox">
                                <input type="checkbox" onclick="event.stopPropagation()">
                            </div>
                            <div class="task-name">Hacer QA</div>
                        </div>
                        <div class="task-project">
                            <div class="task-project-name">Proyecto del equipo</div>
                            <div class="task-due-date">Vencida: 2024-01-15</div>
                            <div style="margin-top: 4px;">
                                <small style="color: #8b5cf6; font-weight: 600;">👤 Usuario Asignado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- COLUMNA HOY -->
            <div class="kanban-column">
                <div class="column-header hoy">
                    <span>📅 HOY</span>
                    <span class="task-count">0</span>
                </div>
                <div class="column-content">
                    <div class="empty-column">Sin tareas para hoy del equipo</div>
                </div>
            </div>
            
            <!-- COLUMNA ESTA SEMANA -->
            <div class="kanban-column">
                <div class="column-header semana">
                    <span>📆 ESTA SEMANA</span>
                    <span class="task-count">0</span>
                </div>
                <div class="column-content">
                    <div class="empty-column">Sin tareas esta semana del equipo</div>
                </div>
            </div>
            
            <!-- COLUMNA FUTURAS -->
            <div class="kanban-column">
                <div class="column-header futuras">
                    <span>🚀 FUTURAS</span>
                    <span class="task-count">1</span>
                </div>
                <div class="column-content">
                    <div class="task-card futuras project-normal">
                        <div class="task-header">
                            <div class="task-checkbox">
                                <input type="checkbox" onclick="event.stopPropagation()">
                            </div>
                            <div class="task-name">Reportes</div>
                        </div>
                        <div class="task-project">
                            <div class="task-project-name">Proyecto del equipo</div>
                            <div class="task-due-date">Fecha: 2024-02-01</div>
                            <div style="margin-top: 4px;">
                                <small style="color: #8b5cf6; font-weight: 600;">👤 Usuario Asignado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // CONTENEDOR EQUIPO - ANCHO NORMAL (reducción se aplica en dashboard-container)
    teamContent.classList.add('active');
    teamContent.style.cssText = `
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        min-height: 500px !important;
        position: relative !important;
        z-index: 1 !important;
    `;
    
    // También forzar visibilidad del contenedor padre para que sea igual
    const parentContainer = teamContent.parentElement;
    if (parentContainer) {
        parentContainer.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
        `;
        console.log('🔧 Contenedor padre del equipo forzado igual a mis tareas');
    }
    
    console.log('✅ KANBAN INSERTADO CON CLASES CSS EXISTENTES');
    
    // Verificar que se insertó correctamente con la nueva clase
    const teamKanbanBoard = teamContent.querySelector('.team-kanban-board');
    if (teamKanbanBoard) {
        // KANBAN EQUIPO - GAP NORMAL (reducción en dashboard-container)
        teamKanbanBoard.style.cssText = `
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            visibility: visible !important;
            opacity: 1 !important;
            background: transparent !important;
        `;
        
        console.log('✅ .team-kanban-board encontrado y forzado a visible');
        console.log('📏 Dimensiones DESPUÉS de forzar:', teamKanbanBoard.getBoundingClientRect());
        
        // Forzar columnas del equipo IDÉNTICAS a Mis Tareas
        const columns = teamKanbanBoard.querySelectorAll('.kanban-column');
        columns.forEach((column, index) => {
            column.style.cssText = `
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 400px !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                border-radius: 16px !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            `;
            console.log(`✅ Columna del equipo ${index + 1} estilo idéntico`);
        });
    } else {
        console.error('❌ .team-kanban-board NO encontrado después de insertar');
    }
}

// FUNCIÓN DE EMERGENCIA - Ejecuta esto manualmente en la consola si no funciona
window.mostrarKanbanEmergencia = function() {
    console.log('🚨 FUNCIÓN DE EMERGENCIA ACTIVADA');
    
    // Buscar el contenedor
    const teamContent = document.getElementById('team-tasks-content');
    if (!teamContent) {
        console.error('❌ No se encuentra team-tasks-content');
        return;
    }
    
    // Crear un div completamente nuevo con estilos inline brutales
    const emergencyDiv = document.createElement('div');
    emergencyDiv.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; padding: 20px; background: #f8fafc; border-radius: 12px; width: 100%; box-sizing: border-box; min-height: 500px; position: relative; z-index: 9999;">
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #fef2f2; color: #991b1b; padding: 16px; font-weight: bold; border-bottom: 3px solid #ef4444;">
                    ⚠️ VENCIDAS (1)
                </div>
                <div style="padding: 16px;">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; margin-bottom: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="checkbox">
                            <strong>Hacer QA</strong>
                        </div>
                        <div style="font-size: 12px; color: #6b7280;">
                            <div>📁 Proyecto del equipo</div>
                            <div style="color: #8b5cf6;">👤 Usuario Asignado</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #fffbeb; color: #92400e; padding: 16px; font-weight: bold; border-bottom: 3px solid #f59e0b;">
                    📅 HOY (0)
                </div>
                <div style="padding: 16px; text-align: center; color: #9ca3af;">Sin tareas para hoy</div>
            </div>
            
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #eff6ff; color: #1e40af; padding: 16px; font-weight: bold; border-bottom: 3px solid #3b82f6;">
                    📆 ESTA SEMANA (0)
                </div>
                <div style="padding: 16px; text-align: center; color: #9ca3af;">Sin tareas esta semana</div>
            </div>
            
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #f0fdf4; color: #166534; padding: 16px; font-weight: bold; border-bottom: 3px solid #10b981;">
                    🚀 FUTURAS (1)
                </div>
                <div style="padding: 16px;">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; margin-bottom: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="checkbox">
                            <strong>Reportes</strong>
                        </div>
                        <div style="font-size: 12px; color: #6b7280;">
                            <div>📁 Proyecto del equipo</div>
                            <div style="color: #8b5cf6;">👤 Usuario Asignado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Limpiar el contenedor y agregar el nuevo div
    teamContent.innerHTML = '';
    teamContent.appendChild(emergencyDiv);
    
    // Forzar visibilidad brutal
    teamContent.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; height: auto !important; min-height: 500px !important; position: relative !important; z-index: 9999 !important;';
    
    console.log('✅ KANBAN DE EMERGENCIA INSERTADO');
    console.log('📏 Dimensiones finales:', teamContent.getBoundingClientRect());
}

// FUNCIÓN DUPLICADA - ELIMINADA
/*
function switchDashboardTab_ELIMINADA(tabName) {
    console.log('🔄 Cambiando tab dashboard:', tabName);
    
    // OCULTAR AMBOS KANBAN POR CLASES ESPECÍFICAS
    const myTasksKanban = document.querySelector('.kanban-board');
    const teamTasksKanban = document.querySelector('.team-kanban-board');
    
    if (myTasksKanban) {
        myTasksKanban.style.display = 'none';
        console.log('🙈 .kanban-board (Mis Tareas) OCULTO');
    }
    
    if (teamTasksKanban) {
        teamTasksKanban.style.display = 'none';
        console.log('🙈 .team-kanban-board (Equipo) OCULTO');
    }
    
    // También ocultar contenedores
    const myTasksContent = document.getElementById('my-tasks-content');
    const teamTasksContent = document.getElementById('team-tasks-content');
    
    if (myTasksContent) {
        myTasksContent.style.display = 'none';
        console.log('🙈 my-tasks-content OCULTO');
    }
    
    if (teamTasksContent) {
        teamTasksContent.style.display = 'none';
        console.log('🙈 team-tasks-content OCULTO');
    }
    
    // Ocultar todas las estadísticas
    const allStats = document.querySelectorAll('.dashboard-stats');
    allStats.forEach(stats => {
        stats.style.display = 'none';
    });
    
    // Quitar active de todos los botones de tabs
    const allButtons = document.querySelectorAll('.tab-minimal');
    allButtons.forEach(button => {
        button.classList.remove('active');
    });
    
    // MOSTRAR SOLO EL CONTENEDOR CORRECTO
    const targetContent = document.getElementById(tabName + '-content');
    if (targetContent) {
        targetContent.style.display = 'block';
        console.log(`👁️ ${tabName}-content MOSTRADO`);
    }
    
    // Mostrar las estadísticas correspondientes
    const targetStats = document.getElementById(tabName + '-stats');
    if (targetStats) {
        targetStats.style.display = 'flex';
    }
    
    // Activar el botón del tab correspondiente
    const targetButton = document.getElementById(tabName + '-dashboard-tab');
    if (targetButton) {
        targetButton.classList.add('active');
    }
    
    // Cargar datos según el tab seleccionado
    if (tabName === 'team-tasks') {
        console.log('🎯 Tab team-tasks detectado, mostrando Kanban del equipo...');
        
        // APLICAR CLASE team-view AL DASHBOARD-CONTAINER (33% menos)
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.classList.add('team-view');
            console.log('📏 Dashboard container reducido 33% para vista equipo');
        }
        
        // Cargar datos reales del servidor para el equipo
        setTimeout(() => {
            loadTeamKanban();
        }, 100);
        
    } else if (tabName === 'my-tasks') {
        console.log('🎯 Tab my-tasks detectado, mostrando Kanban personal...');
        
        // QUITAR CLASE team-view DEL DASHBOARD-CONTAINER (ancho completo)
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.classList.remove('team-view');
            console.log('📏 Dashboard container restaurado a ancho completo para mis tareas');
        }
        
        // LIMPIAR el contenedor del equipo
        const teamTasksContent = document.getElementById('team-tasks-content');
        if (teamTasksContent) {
            teamTasksContent.innerHTML = '';
            console.log('🧹 Contenedor del Equipo limpiado');
        }
        
        // ASEGURAR que "Mis Tareas" sea IDÉNTICO a Equipo
        const myTasksContent = document.getElementById('my-tasks-content');
        if (myTasksContent) {
            myTasksContent.style.cssText = `
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                min-height: 500px !important;
                position: relative !important;
                z-index: 1 !important;
            `;
            
            // Ajustar el Kanban de Mis Tareas IDÉNTICO al Equipo
            const myTasksKanban = myTasksContent.querySelector('.kanban-board');
            if (myTasksKanban) {
                myTasksKanban.style.cssText = `
                    display: grid !important;
                    grid-template-columns: repeat(4, 1fr) !important;
                    gap: 20px !important;
                    width: 100% !important;
                    max-width: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    height: auto !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    background: transparent !important;
                `;
            }
            
            console.log('👁️ Kanban de "Mis Tareas" ajustado con ancho completo');
        }
        
    } else {
        console.log('🎯 Tab seleccionado:', tabName);
    }
    
    console.log('✅ Tab dashboard cambiado a:', tabName);
}
*/

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard Kanban cargado - CON LÓGICA DE TABS');
    
    // INICIALIZAR CON "MIS TAREAS" POR DEFECTO
    switchDashboardTab('my-tasks');
    
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
    const modal = document.getElementById('createTaskModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateTaskModal();
            }
        });
    }
});
</script>
