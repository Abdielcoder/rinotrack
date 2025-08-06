<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="collaborator-availability-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large"><?php echo $this->getClanIcon($clan['clan_name']); ?></div>
                <h1>Diagrama de Gantt</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?> - <?php echo htmlspecialchars($clan['clan_departamento']); ?></span>
            </div>
            
            <div class="actions-minimal">
                <div class="view-toggle">
                    <a href="?route=clan_leader/collaborator-availability&view=calendar" class="btn-minimal <?= ($view === 'calendar') ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i>
                        Calendario
                    </a>
                    <a href="?route=clan_leader/collaborator-availability&view=gantt" class="btn-minimal <?= ($view === 'gantt') ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        Diagrama Gantt
                    </a>
                </div>
                <a href="?route=clan_leader/dashboard" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido del Diagrama de Gantt -->
    <div class="content-minimal">
        <section class="gantt-view">
            <h3>Diagrama de Gantt - Proyectos y Tareas</h3>
            <div class="gantt-container">
                <div class="gantt-header">
                    <div class="gantt-controls">
                        <button class="btn-gantt" onclick="zoomIn()">
                            <i class="fas fa-search-plus"></i>
                            Zoom +
                        </button>
                        <button class="btn-gantt" onclick="zoomOut()">
                            <i class="fas fa-search-minus"></i>
                            Zoom -
                        </button>
                        <button class="btn-gantt" onclick="resetZoom()">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Reset
                        </button>
                    </div>
                    <div class="gantt-timeline" id="ganttTimeline">
                        <!-- Timeline se generará con JavaScript -->
                    </div>
                </div>
                
                <div class="gantt-body">
                    <div class="gantt-sidebar">
                        <div class="gantt-sidebar-header">
                            <h4>Proyectos y Tareas</h4>
                        </div>
                        <div class="gantt-sidebar-content" id="ganttSidebar">
                            <!-- Contenido del sidebar se generará con JavaScript -->
                        </div>
                    </div>
                    
                    <div class="gantt-chart" id="ganttChart">
                        <!-- El diagrama de Gantt se generará con JavaScript -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
/* Estilos para cambio de vista */
.view-toggle {
    display: flex;
    gap: 0.5rem;
    margin-right: 1rem;
}

.btn-minimal.active {
    background: #3b82f6;
    color: white;
}

.btn-minimal.active:hover {
    background: #2563eb;
}

/* Estilos para el diagrama de Gantt */
.gantt-view {
    margin: 2rem 0;
}

.gantt-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.gantt-header {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.gantt-controls {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.btn-gantt {
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6b7280;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-gantt:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
}

.gantt-timeline {
    display: flex;
    overflow-x: auto;
    padding: 0.5rem 0;
    border-top: 1px solid #e5e7eb;
}

.timeline-day {
    min-width: 60px;
    text-align: center;
    font-size: 0.75rem;
    color: #6b7280;
    padding: 0.25rem;
    border-right: 1px solid #f3f4f6;
}

.timeline-day.today {
    background: #eff6ff;
    color: #1e40af;
    font-weight: 600;
}

.gantt-body {
    display: flex;
    min-height: 400px;
}

.gantt-sidebar {
    width: 300px;
    border-right: 1px solid #e5e7eb;
    background: #f9fafb;
}

.gantt-sidebar-header {
    padding: 1rem;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
}

.gantt-sidebar-header h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.gantt-sidebar-content {
    padding: 0.5rem;
}

.gantt-item {
    padding: 0.75rem;
    margin-bottom: 0.25rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gantt-item:hover {
    background: #f3f4f6;
}

.gantt-item.project {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    font-weight: 600;
    color: #1e40af;
}

.gantt-item.task {
    background: white;
    border-left: 4px solid #10b981;
    margin-left: 1rem;
    font-size: 0.875rem;
}

.gantt-item.task.overdue {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.gantt-item.task.completed {
    border-left-color: #10b981;
    background: #f0fdf4;
}

.gantt-chart {
    flex: 1;
    overflow: auto;
    position: relative;
    background: white;
}

.gantt-grid {
    position: relative;
    min-height: 100%;
}

.gantt-row {
    display: flex;
    align-items: center;
    height: 50px;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
}

.gantt-row:hover {
    background: #f9fafb;
}

.gantt-bar {
    position: absolute;
    height: 30px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gantt-bar:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.gantt-bar.pending {
    background: #f59e0b;
}

.gantt-bar.in_progress {
    background: #3b82f6;
}

.gantt-bar.completed {
    background: #10b981;
}

.gantt-bar.overdue {
    background: #ef4444;
}

.gantt-bar.project {
    background: #6366f1;
    height: 40px;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .view-toggle {
        flex-direction: column;
        gap: 0.25rem;
        margin-right: 0.5rem;
    }
    
    .gantt-sidebar {
        width: 200px;
    }
    
    .gantt-controls {
        flex-wrap: wrap;
    }
    
    .btn-gantt {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script>
// Datos para el diagrama de Gantt
const ganttData = <?= json_encode($all_tasks) ?>;
const projects = <?= json_encode($projects) ?>;
let zoomLevel = 1;

// Organizar datos por proyecto
function organizeGanttData() {
    const organizedData = {};
    
    projects.forEach(project => {
        organizedData[project.project_id] = {
            project: project,
            tasks: []
        };
    });
    
    ganttData.forEach(taskData => {
        const projectId = taskData.task.project_id;
        if (organizedData[projectId]) {
            organizedData[projectId].tasks.push(taskData);
        }
    });
    
    return organizedData;
}

// Generar timeline
function generateTimeline() {
    const timeline = document.getElementById('ganttTimeline');
    timeline.innerHTML = '';
    
    const today = new Date();
    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() - 7); // 7 días atrás
    
    const endDate = new Date(today);
    endDate.setDate(endDate.getDate() + 30); // 30 días adelante
    
    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
        const dayElement = document.createElement('div');
        dayElement.className = 'timeline-day';
        
        if (d.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        dayElement.textContent = d.getDate();
        dayElement.title = d.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        timeline.appendChild(dayElement);
    }
}

// Generar sidebar
function generateSidebar() {
    const sidebar = document.getElementById('ganttSidebar');
    sidebar.innerHTML = '';
    
    const organizedData = organizeGanttData();
    
    Object.values(organizedData).forEach(projectData => {
        // Proyecto
        const projectElement = document.createElement('div');
        projectElement.className = 'gantt-item project';
        projectElement.textContent = projectData.project.project_name;
        projectElement.onclick = () => toggleProject(projectData.project.project_id);
        sidebar.appendChild(projectElement);
        
        // Tareas del proyecto
        projectData.tasks.forEach(taskData => {
            const taskElement = document.createElement('div');
            taskElement.className = `gantt-item task ${taskData.task.status}`;
            
            const isOverdue = new Date(taskData.task.due_date) < new Date() && taskData.task.status !== 'completed';
            if (isOverdue) {
                taskElement.classList.add('overdue');
            }
            
            taskElement.textContent = taskData.task.task_name;
            taskElement.title = `${taskData.task.task_name} - ${taskData.task.status}`;
            sidebar.appendChild(taskElement);
        });
    });
}

// Generar diagrama de Gantt
function generateGanttChart() {
    const chart = document.getElementById('ganttChart');
    chart.innerHTML = '';
    
    const grid = document.createElement('div');
    grid.className = 'gantt-grid';
    
    const organizedData = organizeGanttData();
    let rowIndex = 0;
    
    Object.values(organizedData).forEach(projectData => {
        // Fila del proyecto
        const projectRow = document.createElement('div');
        projectRow.className = 'gantt-row';
        projectRow.style.top = `${rowIndex * 50}px`;
        
        // Barra del proyecto
        const projectBar = createGanttBar(projectData.project, 'project', rowIndex);
        if (projectBar) {
            projectRow.appendChild(projectBar);
        }
        
        grid.appendChild(projectRow);
        rowIndex++;
        
        // Filas de tareas
        projectData.tasks.forEach(taskData => {
            const taskRow = document.createElement('div');
            taskRow.className = 'gantt-row';
            taskRow.style.top = `${rowIndex * 50}px`;
            
            // Barra de la tarea
            const taskBar = createGanttBar(taskData.task, 'task', rowIndex);
            if (taskBar) {
                taskRow.appendChild(taskBar);
            }
            
            grid.appendChild(taskRow);
            rowIndex++;
        });
    });
    
    chart.appendChild(grid);
}

// Crear barra de Gantt
function createGanttBar(item, type, rowIndex) {
    if (!item.due_date) return null;
    
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 7);
    
    const dueDate = new Date(item.due_date);
    const daysFromStart = Math.floor((dueDate - startDate) / (1000 * 60 * 60 * 24));
    
    if (daysFromStart < 0 || daysFromStart > 37) return null; // Solo mostrar si está en el rango visible
    
    const bar = document.createElement('div');
    bar.className = `gantt-bar ${type} ${item.status}`;
    
    const isOverdue = new Date(item.due_date) < new Date() && item.status !== 'completed';
    if (isOverdue) {
        bar.classList.add('overdue');
    }
    
    bar.style.left = `${daysFromStart * 60 * zoomLevel}px`;
    bar.style.width = `${Math.max(60 * zoomLevel, 80)}px`;
    bar.style.top = `${rowIndex * 50 + 10}px`;
    
    bar.textContent = type === 'project' ? item.project_name : item.task_name;
    bar.title = `${item.task_name || item.project_name} - Vence: ${new Date(item.due_date).toLocaleDateString('es-ES')}`;
    
    return bar;
}

// Funciones de zoom
function zoomIn() {
    zoomLevel = Math.min(zoomLevel * 1.2, 3);
    generateGanttChart();
}

function zoomOut() {
    zoomLevel = Math.max(zoomLevel / 1.2, 0.5);
    generateGanttChart();
}

function resetZoom() {
    zoomLevel = 1;
    generateGanttChart();
}

// Toggle proyecto (expandir/contraer)
function toggleProject(projectId) {
    // Implementar lógica para expandir/contraer proyectos
    console.log('Toggle project:', projectId);
}

// Inicializar diagrama de Gantt
document.addEventListener('DOMContentLoaded', () => {
    generateTimeline();
    generateSidebar();
    generateGanttChart();
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