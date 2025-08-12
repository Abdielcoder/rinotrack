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
                <a href="?route=clan_leader/kpi-dashboard" class="btn-minimal primary">
                    <i class="fas fa-chart-line"></i>
                    KPIs del Clan
                </a>
                <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Progreso General del Equipo -->
    <div class="content-minimal">
        <section class="team-progress-section">
            <h3>Progreso General del Equipo</h3>
            <div class="progress-card">
                <div class="progress-info">
                    <div class="progress-left">
                        <span class="progress-label">Tareas Completadas Total</span>
                        <span class="progress-value"><?php echo number_format($taskStats['completed_tasks']); ?> tareas</span>
                    </div>
                    <div class="progress-right">
                        <div class="completion-box">
                            <span class="completion-percentage"><?php echo $taskStats['completion_percentage']; ?>%</span>
                            <span class="completion-label">Completado</span>
                        </div>
                    </div>
                </div>
                
                <div class="progress-bar-container">
                    <div class="progress-bar-main">
                        <?php 
                        $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                        $totalMembers = count($memberContributions);
                        $colorIndex = 0;
                        
                        foreach ($memberContributions as $member) {
                            if ($member['completed_tasks'] > 0) {
                                $width = ($member['completed_tasks'] / max($taskStats['completed_tasks'], 1)) * 100;
                                echo '<div class="progress-segment" style="width: ' . $width . '%; background-color: ' . $colors[$colorIndex % count($colors)] . ';" title="' . htmlspecialchars($member['full_name']) . ' - ' . $member['completed_tasks'] . ' tareas"></div>';
                                $colorIndex++;
                            }
                        }
                        ?>
                    </div>
                    <span class="remaining-text"><?php echo (100 - $taskStats['completion_percentage']); ?>% restante</span>
                </div>
            </div>
        </section>
        
        <!-- Contribuciones por Colaborador -->
        <section class="contributions-section">
            <h3>Contribuciones por Colaborador</h3>
            <div class="contributions-grid">
                <?php 
                $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                $colorIndex = 0;
                
                if (empty($memberContributions)): ?>
                    <div style="text-align: center; padding: 20px; color: #666;">
                        <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>No hay miembros en el clan o no se pudieron cargar las contribuciones.</p>
                    </div>
                <?php else:
                foreach ($memberContributions as $member): 
                    $memberColor = $colors[$colorIndex % count($colors)];
                    $colorIndex++;
                ?>
                    <div class="contribution-card clickable" data-user-id="<?php echo $member['user_id']; ?>" onclick="showUserStats(<?php echo $member['user_id']; ?>)">
                        <div class="member-avatar">
                            <?php if ($member['profile_picture']): ?>
                                <img src="<?php echo htmlspecialchars($member['profile_picture']); ?>" alt="<?php echo htmlspecialchars($member['full_name']); ?>">
                            <?php else: ?>
                                <div class="avatar-initial" style="background-color: <?php echo $memberColor; ?>">
                                    <?php echo $member['initial']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                            <div class="member-tasks">
                                <?php if ($member['total_tasks'] > 0): ?>
                                    <span class="task-count"><?php echo $member['completed_tasks']; ?> tareas (<?php echo $member['contribution_percentage']; ?>%)</span>
                                    <span class="contribution-dot" style="background-color: <?php echo $memberColor; ?>"></span>
                                <?php else: ?>
                                    <span class="task-count">Sin tareas asignadas</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; 
                endif; ?>
            </div>
        </section>
        
        <!-- Acciones Rápidas -->
        <section class="quick-actions-minimal">
            <h3>Acciones Rápidas</h3>
            <div class="actions-grid">
                <a href="?route=clan_leader/members" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Miembros</h4>
                        <p>Agregar o remover miembros del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/projects" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Proyectos</h4>
                        <p>Crear y administrar proyectos del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/tasks" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Tareas</h4>
                        <p>Asignar y supervisar tareas</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/kpi-dashboard" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h4>Dashboard KPI</h4>
                        <p>Ver métricas y rendimiento del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/collaborator-availability" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Disponibilidad</h4>
                        <p>Ver disponibilidad de colaboradores</p>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- Resumen de Actividad -->
        <section class="activity-summary-minimal">
            <h3>Resumen de Actividad</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Miembros Activos</h4>
                        <span class="summary-value"><?php echo $userStats['active_members']; ?> / <?php echo $userStats['total_members']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $userStats['total_members'] > 0 ? ($userStats['active_members'] / $userStats['total_members']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Proyectos Activos</h4>
                        <span class="summary-value"><?php echo $projectStats['active_projects']; ?> / <?php echo $projectStats['total_projects']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $projectStats['total_projects'] > 0 ? ($projectStats['active_projects'] / $projectStats['total_projects']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Proyectos con KPI</h4>
                        <span class="summary-value"><?php echo $projectStats['kpi_projects']; ?> / <?php echo $projectStats['total_projects']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $projectStats['total_projects'] > 0 ? ($projectStats['kpi_projects'] / $projectStats['total_projects']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal de Estadísticas de Usuario -->
<div id="userStatsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="userStatsTitle">Estadísticas del Usuario</h2>
            <span class="close" onclick="closeUserStatsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="userStatsContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>

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