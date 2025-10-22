<?php
// Guardar el contenido de la vista
ob_start();
?>

<?php
    $projectsCount = is_array($projects ?? null) ? count($projects) : 0;
    $totalTasks = (int)($userTaskStats['total_tasks'] ?? 0);
    $completedTasks = (int)($userTaskStats['completed_tasks'] ?? 0);
    $inProgress = max(0, $totalTasks - $completedTasks);
    $progressPct = (float)($userTaskStats['completion_percentage'] ?? 0);
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-star"></i>
                </div>
                <span class="brand-text" style="color: #1e3a8a; !important">Polaris</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item active"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <!-- <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li> -->
                <li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Agenda</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/profile" class="nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
            </ul>
            
            <!-- Botón hamburguesa para móvil -->
            <button class="hamburger-menu" onclick="toggleMobileMenu()" style="display: flex !important; flex-direction: column; justify-content: space-around; width: 30px; height: 25px; background: transparent; border: none; cursor: pointer; padding: 0; z-index: 1000; position: relative; margin-left: auto; margin-right: 15px;">
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
            </button>
            
            <!-- Menú móvil -->
            <div class="mobile-menu" id="mobileMenu" style="display: block !important; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0, 0, 0, 0.8); z-index: 999; opacity: 0; visibility: hidden; transition: all 0.3s ease;">
                <div class="mobile-menu-content" style="position: absolute; top: 0; right: 0; width: 280px; height: 100%; background: #ffffff; transform: translateX(100%); transition: transform 0.3s ease; box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);">
                    <div class="mobile-menu-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <span class="mobile-menu-title" style="font-size: 1.2rem; font-weight: 600; color: #1e3a8a;">Menú</span>
                        <button class="mobile-menu-close" onclick="toggleMobileMenu()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s ease;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="mobile-nav-menu" style="list-style: none; padding: 0; margin: 0;">
                        <li class="mobile-nav-item" style="border-bottom: 1px solid #e5e7eb;">
                            <a href="?route=clan_member" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease; background: #f3f4f6; color: #1e3a8a;">
                                <i class="fas fa-home" style="width: 20px; text-align: center; font-size: 1.1rem;"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="mobile-nav-item" style="border-bottom: 1px solid #e5e7eb;">
                            <a href="?route=clan_member/tasks" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease;">
                                <i class="fas fa-tasks" style="width: 20px; text-align: center; font-size: 1.1rem;"></i>
                                <span>Tareas</span>
                            </a>
                        </li>
                        <!-- <li class="mobile-nav-item" style="border-bottom: 1px solid #e5e7eb;">
                            <a href="?route=clan_member/kpi-dashboard" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease;">
                                <i class="fas fa-chart-line" style="width: 20px; text-align: center; font-size: 1.1rem;"></i>
                                <span>KPI</span>
                            </a>
                        </li> -->
                        <li class="mobile-nav-item" style="border-bottom: 1px solid #e5e7eb;">
                            <a href="?route=clan_member/availability" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease;">
                                <i class="fas fa-user-clock" style="width: 20px; text-align: center; font-size: 1.1rem;"></i>
                                <span>Agenda</span>
                            </a>
                        </li>
                        <li class="mobile-nav-item" style="border-bottom: 1px solid #e5e7eb;">
                            <a href="?route=clan_member/profile" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease;">
                                <i class="fas fa-user" style="width: 20px; text-align: center; font-size: 1.1rem;"></i>
                                <span>Perfil</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <?php if (!empty($user['avatar_path'])): ?>
                        <img src="<?php echo Utils::asset($user['avatar_path']); ?>" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:999px"/>
                    <?php else: ?>
                        <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
                    <?php endif; ?>
                    <div class="status-dot"></div>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span>
                    <span class="user-role">Miembro de Clan</span>
                </div>
                <div class="user-actions">
                    <a href="?route=logout" class="action-btn logout" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <header class="welcome-header animate-fade-in">
            <div class="welcome-content">
                <h1 class="welcome-title">¡Hola, <?php echo Utils::escape($user['full_name'] ?: $user['username']); ?>! 👋</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? 'Sin clan'); ?></p>
                <div class="motivation" style="display:flex;align-items:center;gap:10px;margin-top:10px">
                    <div class="motivation-icon" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:var(--primary-gradient);color:#fff">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="motivation-text">
                        <div id="motQuote" style="font-weight:600;color:#1e3a8a">Cargando frase motivacional...</div>
                        <div id="motAuthor" style="font-size:.9rem;color:#6b7280"></div>
                    </div>
                </div>
            </div>
            <div class="welcome-stats">
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Proyectos</h3><i class="fas fa-folder-open"></i></div>
                            <div class="stat-number"><?php echo $projectsCount; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Tareas</h3><i class="fas fa-tasks"></i></div>
                            <div class="stat-number"><?php echo $totalTasks; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><h3>Completadas</h3><i class="fas fa-check-circle"></i></div>
                        <div class="stat-number"><?php echo $completedTasks; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><h3>En Progreso</h3><i class="fas fa-spinner"></i></div>
                        <div class="stat-number"><?php echo $inProgress; ?></div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Tablero Kanban de Tareas -->
        <section class="kanban-section animate-fade-in">
            <div class="kanban-header">
                <div class="kanban-title">
                    <h2><i class="fas fa-tasks icon-gradient"></i> Tareas</h2>
                </div>
                <div class="kanban-actions">
                    <button class="btn-add-task" onclick="openAddTaskModal()">
                        <i class="fas fa-plus"></i>
                        Agregar Tarea
                    </button>
                </div>
            </div>
            <div class="kanban-board">
                <!-- Columna: Vencidas -->
                <div class="kanban-column">
                    <div class="column-header overdue">
                        <h3>⚠️ VENCIDAS</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['vencidas'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['vencidas'] as $task): ?>
                            <?php 
                            // Debug: Log de la tarea para verificar el project_name
                            error_log("DEBUG TAREA VENCIDA - ID: {$task['task_id']}, Nombre: {$task['task_name']}, Proyecto: {$task['project_name']}");
                            ?>
                            <div class="task-card overdue <?php echo ($task['item_type'] ?? 'task') === 'subtask' ? 'subtask-card' : ''; ?>" data-task-id="<?php echo $task['task_id']; ?>" data-priority="<?php echo $task['priority']; ?>">
                                <div class="task-header">
                                    <?php if (($task['item_type'] ?? 'task') === 'subtask'): ?>
                                        <div class="subtask-indicator">
                                            <i class="fas fa-list-ul"></i>
                                            <span>Subtarea</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="checkbox" id="vencidas-<?php echo $task['task_id']; ?>" class="task-checkbox" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onclick="event.stopPropagation()" onchange="handleTaskCheck('vencidas-<?php echo $task['task_id']; ?>', <?php echo $task['task_id']; ?>, this.checked, '<?php echo $task['item_type'] ?? 'task'; ?>')">
                                    <?php 
                                    $linkTaskId = ($task['item_type'] ?? 'task') === 'subtask' ? $task['parent_task_id'] : $task['task_id'];
                                    ?>
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $linkTaskId; ?>" class="btn-edit" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-content">
                                    <h4 class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></h4>

                                    <div class="task-project-info">
                                        <?php if (in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales', 'Personal'])): ?>
                                            <!-- Solo mostrar etiqueta para tareas especiales y personales -->
                                            <span class="task-type-badge <?php echo strtolower(str_replace(' ', '-', $task['project_name'])); ?>">
                                                <?php if ($task['project_name'] === 'Tareas Recurrentes'): ?>
                                                    <i class="fas fa-redo"></i> Recurrente
                                                <?php elseif ($task['project_name'] === 'Tareas Eventuales'): ?>
                                                    <i class="fas fa-calendar-alt"></i> Eventual
                                                <?php elseif ($task['project_name'] === 'Tareas Personales' || $task['project_name'] === 'Personal'): ?>
                                                    <i class="fas fa-user"></i> Personal
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <!-- Mostrar project-name para tareas normales -->
                                            <span class="project-name"><?php echo htmlspecialchars($task['project_name']); ?></span>
                                            <?php if (isset($task['clan_name']) && $task['clan_name'] !== ($clan['clan_name'] ?? '') && !str_contains($task['clan_name'], 'Dirección')): ?>
                                                <span class="external-clan-badge" title="Proyecto de otro clan: <?php echo htmlspecialchars($task['clan_name']); ?>">
                                                    <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($task['clan_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Hoy -->
                <div class="kanban-column">
                    <div class="column-header today">
                        <h3>📅 HOY</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['hoy'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['hoy'] ?? [] as $task): ?>
                            <?php 
                            // Debug: Log de la tarea para verificar el project_name y priority
                            error_log("DEBUG TAREA HOY - ID: {$task['task_id']}, Nombre: {$task['task_name']}, Proyecto: {$task['project_name']}, Prioridad: {$task['priority']}");
                            ?>
                            <div class="task-card today <?php echo ($task['item_type'] ?? 'task') === 'subtask' ? 'subtask-card' : ''; ?>" data-task-id="<?php echo $task['task_id']; ?>" data-priority="<?php echo $task['priority']; ?>">
                                <div class="task-header">
                                    <?php if (($task['item_type'] ?? 'task') === 'subtask'): ?>
                                        <div class="subtask-indicator">
                                            <i class="fas fa-list-ul"></i>
                                            <span>Subtarea</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="checkbox" id="hoy-<?php echo $task['task_id']; ?>" class="task-checkbox" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onclick="event.stopPropagation()" onchange="handleTaskCheck('hoy-<?php echo $task['task_id']; ?>', <?php echo $task['task_id']; ?>, this.checked, '<?php echo $task['item_type'] ?? 'task'; ?>')">
                                    <?php 
                                    $linkTaskId = ($task['item_type'] ?? 'task') === 'subtask' ? $task['parent_task_id'] : $task['task_id'];
                                    ?>
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $linkTaskId; ?>" class="btn-edit" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-content">
                                    <h4 class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></h4>

                                    <div class="task-project-info">
                                        <?php if (in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales', 'Personal'])): ?>
                                            <!-- Solo mostrar etiqueta para tareas especiales y personales -->
                                            <span class="task-type-badge <?php echo strtolower(str_replace(' ', '-', $task['project_name'])); ?>">
                                                <?php if ($task['project_name'] === 'Tareas Recurrentes'): ?>
                                                    <i class="fas fa-redo"></i> Recurrente
                                                <?php elseif ($task['project_name'] === 'Tareas Eventuales'): ?>
                                                    <i class="fas fa-calendar-alt"></i> Eventual
                                                <?php elseif ($task['project_name'] === 'Tareas Personales' || $task['project_name'] === 'Personal'): ?>
                                                    <i class="fas fa-user"></i> Personal
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <!-- Mostrar project-name para tareas normales -->
                                            <span class="project-name"><?php echo htmlspecialchars($task['project_name']); ?></span>
                                            <?php if (isset($task['clan_name']) && $task['clan_name'] !== ($clan['clan_name'] ?? '') && !str_contains($task['clan_name'], 'Dirección')): ?>
                                                <span class="external-clan-badge" title="Proyecto de otro clan: <?php echo htmlspecialchars($task['clan_name']); ?>">
                                                    <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($task['clan_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: 1 Semana -->
                <div class="kanban-column">
                    <div class="column-header week1">
                        <h3>📆 1 SEMANA</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['1_semana'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['1_semana'] ?? [] as $task): ?>
                            <div class="task-card week1 <?php echo ($task['item_type'] ?? 'task') === 'subtask' ? 'subtask-card' : ''; ?>" data-task-id="<?php echo $task['task_id']; ?>" data-priority="<?php echo $task['priority']; ?>">
                                <div class="task-header">
                                    <?php if (($task['item_type'] ?? 'task') === 'subtask'): ?>
                                        <div class="subtask-indicator">
                                            <i class="fas fa-list-ul"></i>
                                            <span>Subtarea</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="checkbox" id="semana1-<?php echo $task['task_id']; ?>" class="task-checkbox" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onclick="event.stopPropagation()" onchange="handleTaskCheck('semana1-<?php echo $task['task_id']; ?>', <?php echo $task['task_id']; ?>, this.checked, '<?php echo $task['item_type'] ?? 'task'; ?>')">
                                    <?php 
                                    $linkTaskId = ($task['item_type'] ?? 'task') === 'subtask' ? $task['parent_task_id'] : $task['task_id'];
                                    ?>
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $linkTaskId; ?>" class="btn-edit" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-content">
                                    <h4 class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></h4>

                                    <div class="task-project-info">
                                        <?php if (in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales', 'Personal'])): ?>
                                            <!-- Solo mostrar etiqueta para tareas especiales y personales -->
                                            <span class="task-type-badge <?php echo strtolower(str_replace(' ', '-', $task['project_name'])); ?>">
                                                <?php if ($task['project_name'] === 'Tareas Recurrentes'): ?>
                                                    <i class="fas fa-redo"></i> Recurrente
                                                <?php elseif ($task['project_name'] === 'Tareas Eventuales'): ?>
                                                    <i class="fas fa-calendar-alt"></i> Eventual
                                                <?php elseif ($task['project_name'] === 'Tareas Personales' || $task['project_name'] === 'Personal'): ?>
                                                    <i class="fas fa-user"></i> Personal
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <!-- Mostrar project-name para tareas normales -->
                                            <span class="project-name"><?php echo htmlspecialchars($task['project_name']); ?></span>
                                            <?php if (isset($task['clan_name']) && $task['clan_name'] !== ($clan['clan_name'] ?? '') && !str_contains($task['clan_name'], 'Dirección')): ?>
                                                <span class="external-clan-badge" title="Proyecto de otro clan: <?php echo htmlspecialchars($task['clan_name']); ?>">
                                                    <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($task['clan_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: 2 Semanas -->
                <div class="kanban-column">
                    <div class="column-header week2">
                        <h3>🚀 2 SEMANAS</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['2_semanas'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['2_semanas'] as $task): ?>
                            <div class="task-card week2 <?php echo ($task['item_type'] ?? 'task') === 'subtask' ? 'subtask-card' : ''; ?>" data-task-id="<?php echo $task['task_id']; ?>" data-priority="<?php echo $task['priority']; ?>">
                                <div class="task-header">
                                    <?php if (($task['item_type'] ?? 'task') === 'subtask'): ?>
                                        <div class="subtask-indicator">
                                            <i class="fas fa-list-ul"></i>
                                            <span>Subtarea</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="checkbox" id="semana2-<?php echo $task['task_id']; ?>" class="task-checkbox" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onclick="event.stopPropagation()" onchange="handleTaskCheck('semana2-<?php echo $task['task_id']; ?>', <?php echo $task['task_id']; ?>, this.checked, '<?php echo $task['item_type'] ?? 'task'; ?>')">
                                    <?php 
                                    $linkTaskId = ($task['item_type'] ?? 'task') === 'subtask' ? $task['parent_task_id'] : $task['task_id'];
                                    ?>
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $linkTaskId; ?>" class="btn-edit" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-content">
                                    <h4 class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></h4>

                                    <div class="task-project-info">
                                        <?php if (in_array($task['project_name'], ['Tareas Recurrentes', 'Tareas Eventuales', 'Tareas Personales', 'Personal'])): ?>
                                            <!-- Solo mostrar etiqueta para tareas especiales y personales -->
                                            <span class="task-type-badge <?php echo strtolower(str_replace(' ', '-', $task['project_name'])); ?>">
                                                <?php if ($task['project_name'] === 'Tareas Recurrentes'): ?>
                                                    <i class="fas fa-redo"></i> Recurrente
                                                <?php elseif ($task['project_name'] === 'Tareas Eventuales'): ?>
                                                    <i class="fas fa-calendar-alt"></i> Eventual
                                                <?php elseif ($task['project_name'] === 'Tareas Personales' || $task['project_name'] === 'Personal'): ?>
                                                    <i class="fas fa-user"></i> Personal
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <!-- Mostrar project-name para tareas normales -->
                                            <span class="project-name"><?php echo htmlspecialchars($task['project_name']); ?></span>
                                            <?php if (isset($task['clan_name']) && $task['clan_name'] !== ($clan['clan_name'] ?? '') && !str_contains($task['clan_name'], 'Dirección')): ?>
                                                <span class="external-clan-badge" title="Proyecto de otro clan: <?php echo htmlspecialchars($task['clan_name']); ?>">
                                                    <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($task['clan_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Animación de estrellas de fondo -->
        <div class="stars-container">
            <div class="star star-1"></div>
            <div class="star star-2"></div>
            <div class="star star-3"></div>
            <div class="star star-4"></div>
            <div class="star star-5"></div>
            <div class="star star-6"></div>
            <div class="star star-7"></div>
            <div class="star star-8"></div>
            <div class="star star-9"></div>
            <div class="star star-10"></div>
        </div>




    </main>
</div>

<!-- Modal para Agregar/Editar Tarea -->
<div id="addTaskModal" class="modal-overlay">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>
                <i class="fas fa-<?php echo isset($editTaskId) && $editTaskId > 0 ? 'edit' : 'plus-circle'; ?>"></i> 
                <?php echo isset($editTaskId) && $editTaskId > 0 ? 'Editar Tarea' : 'Agregar Nueva Tarea'; ?>
            </h3>
            <button class="modal-close" onclick="closeAddTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addTaskForm" class="modal-form">
            <div class="form-group">
                <label for="taskName">Nombre de la Tarea *</label>
                <input type="text" id="taskName" name="task_name" required 
                       placeholder="Escribe el nombre de la tarea"
                       value="<?php echo isset($taskToEdit) ? htmlspecialchars($taskToEdit['task_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="taskDescription">Descripción</label>
                <textarea id="taskDescription" name="description" rows="3" 
                          placeholder="Describe la tarea (opcional)"><?php echo isset($taskToEdit) ? htmlspecialchars($taskToEdit['description']) : ''; ?></textarea>
            </div>
            
            <!-- Configuración de recurrencia -->
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="isRecurrent" name="is_recurrent" value="1" onchange="toggleRecurrenceFields()">
                    <span class="checkmark"></span>
                    Tarea Recurrente
                </label>
            </div>
            
            <div id="recurrenceFields" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="recurrenceType">Tipo de Recurrencia</label>
                        <select name="recurrence_type" id="recurrenceType">
                            <option value="">Seleccionar...</option>
                            <option value="daily">Diaria</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recurrenceStart">Fecha de Inicio</label>
                        <input type="date" id="recurrenceStart" name="recurrence_start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="recurrenceEnd">Fecha de Vigencia (Opcional)</label>
                    <input type="date" id="recurrenceEnd" name="recurrence_end_date">
                    <small style="color: #6b7280; font-size: 0.8rem;">Si no se especifica, la recurrencia será indefinida</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" id="dueDateGroup">
                    <label for="taskDueDate">Fecha de Vencimiento</label>
                    <input type="date" id="taskDueDate" name="due_date" required
                           value="<?php echo isset($taskToEdit) && $taskToEdit['due_date'] ? $taskToEdit['due_date'] : ''; ?>">
                    <small style="color: #6b7280; font-size: 0.8rem; display: none;" id="dueDateHelp">Para tareas recurrentes, se usará la fecha de inicio de recurrencia</small>
                </div>
                
                <!-- Campo oculto para prioridad con valor fijo -->
                <input type="hidden" name="priority" value="medium">
            </div>
            
            <div class="form-group">
                <label for="taskStatus">Estado</label>
                <select name="status" id="taskStatus" required>
                    <option value="pending" <?php echo (isset($taskToEdit) && $taskToEdit['status'] === 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="in_progress" <?php echo (isset($taskToEdit) && $taskToEdit['status'] === 'in_progress') ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="completed" <?php echo (isset($taskToEdit) && $taskToEdit['status'] === 'completed') ? 'selected' : ''; ?>>Completada</option>
                    <option value="cancelled" <?php echo (isset($taskToEdit) && $taskToEdit['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            
            <!-- Campo oculto para el ID de la tarea cuando se esté editando -->
            <?php if (isset($editTaskId) && $editTaskId > 0): ?>
                <input type="hidden" name="task_id" value="<?php echo $editTaskId; ?>">
            <?php endif; ?>
            
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeAddTaskModal()">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas="<?php echo isset($editTaskId) && $editTaskId > 0 ? 'save' : 'plus'; ?>"></i>
                    <?php echo isset($editTaskId) && $editTaskId > 0 ? 'Guardar Cambios' : 'Crear Tarea'; ?>
                </button>
            </div>
        </form>
    </div>
</div>



<style>
.modern-dashboard{min-height:100vh;background:var(--bg-secondary)}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-brand{display:flex;align-items:center;gap:var(--spacing-md)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:1.2rem;background:var(--primary-gradient)}
.brand-text{font-size:1.5rem;font-weight:var(--font-weight-bold);color:#1e3a8a}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.nav-item .nav-link{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md) var(--spacing-lg);border-radius:var(--radius-md);text-decoration:none;color:#1e3a8a;font-weight:var(--font-weight-medium);transition:all var(--transition-normal);position:relative;overflow:hidden}
.nav-item .nav-link:hover{color:#ffffff;background:var(--primary-gradient);transform:translateY(-2px);box-shadow:var(--shadow-md)}
.nav-item.active .nav-link{background:var(--primary-gradient);color:#ffffff;box-shadow:var(--shadow-glow)}
.user-menu{display:flex;align-items:center;gap:var(--spacing-md)}
.modern-avatar{position:relative;width:45px;height:45px;background:var(--bg-tertiary);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;color:#ffffff;font-weight:var(--font-weight-semibold);box-shadow:var(--shadow-md)}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid var(--bg-primary);border-radius:999px}
.user-info{display:flex;flex-direction:column;gap:2px}
.user-name{font-weight:var(--font-weight-semibold);color:#1e3a8a;font-size:.95rem}
.user-role{font-size:.8rem;color:#6b7280}
.action-btn{width:35px;height:35px;border:none;border-radius:var(--radius-md);background:#ffffff;color:#1e3a8a;cursor:pointer;transition:all var(--transition-normal);display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:var(--shadow-sm);border:1px solid #e5e7eb}
.action-btn.logout:hover{color:var(--error)}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.welcome-title{font-size:2.2rem;font-weight:var(--font-weight-bold);color:#1e3a8a;margin-bottom:var(--spacing-sm)}
.welcome-subtitle{font-size:1.05rem;color:#6b7280}
.welcome-stats{display:flex;flex-direction:column;align-items:stretch;min-width:fit-content}
.quick-stat{display:flex;align-items:center;gap:var(--spacing-md);padding:var(--spacing-lg);background:#f3f4f6;border-radius:var(--radius-lg);border:1px solid #e5e7eb}
.stat-icon{width:50px;height:50px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:1.2rem}
.stat-icon.success{background:var(--success)}
.stats-section{margin-bottom:var(--spacing-2xl)}
.stats-row{display:flex;align-items:center;gap:var(--spacing-md);flex-wrap:wrap;justify-content:flex-start}
.stat-card{background:#ffffff;border-radius:var(--radius-lg);padding:var(--spacing-md);box-shadow:var(--shadow-md);border:1px solid #e5e7eb;transition:all var(--transition-normal);min-width:140px;flex-shrink:0;position:relative;overflow:hidden}
.stat-card.gradient-bg{background:var(--primary-gradient);color:#ffffff}
.stat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-sm)}
.stat-header h3{font-size:0.9rem;margin:0;font-weight:600;color:#1e3a8a}
.stat-header i{font-size:1rem;color:#1e3a8a}
.stat-number{font-size:1.5rem;font-weight:var(--font-weight-bold);margin-bottom:0;line-height:1;color:#1e3a8a;display:block;text-align:center;min-height:2rem;line-height:2rem;text-shadow:none;background:transparent;padding:4px 0;font-family:inherit}
.btn-stats{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md);background:#ffffff;border:1px solid #e5e7eb;border-radius:var(--radius-lg);text-decoration:none;color:#1e3a8a;font-weight:600;font-size:0.9rem;min-width:140px;justify-content:center;box-shadow:var(--shadow-md);transition:all var(--transition-normal);flex-shrink:0}
.btn-stats:hover{background:#f3f4f6;color:#1e3a8a;transform:translateY(-2px);box-shadow:var(--shadow-lg)}

/* === ANIMACIÓN DE ESTRELLAS === */
.stars-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.star {
    position: absolute;
    width: 3px;
    height: 3px;
    background: rgba(30, 58, 138, 0.6);
    border-radius: 50%;
    animation: twinkle 3s infinite ease-in-out;
}

.star-1 { top: 15%; left: 10%; animation-delay: 0s; }
.star-2 { top: 25%; left: 25%; animation-delay: 0.3s; }
.star-3 { top: 35%; left: 40%; animation-delay: 0.6s; }
.star-4 { top: 45%; left: 55%; animation-delay: 0.9s; }
.star-5 { top: 55%; left: 70%; animation-delay: 1.2s; }
.star-6 { top: 65%; left: 85%; animation-delay: 1.5s; }
.star-7 { top: 75%; left: 15%; animation-delay: 1.8s; }
.star-8 { top: 85%; left: 30%; animation-delay: 2.1s; }
.star-9 { top: 20%; left: 80%; animation-delay: 2.4s; }
.star-10 { top: 60%; left: 5%; animation-delay: 2.7s; }

@keyframes twinkle {
    0%, 100% { 
        opacity: 0.3; 
        transform: scale(1);
    }
    50% { 
        opacity: 1; 
        transform: scale(1.2);
    }
}

/* === MENÚ HAMBURGUESA RESPONSIVE === */
.hamburger-menu {
    display: flex !important;
    flex-direction: column;
    justify-content: space-around;
    width: 30px;
    height: 25px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 1000;
    position: relative;
    margin-left: auto;
    margin-right: 15px;
}

.hamburger-line {
    width: 100%;
    height: 3px;
    background: #1e3a8a !important;
    border-radius: 2px;
    transition: all 0.3s ease;
    display: block !important;
    margin: 2px 0;
}

.hamburger-menu.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.hamburger-menu.active .hamburger-line:nth-child(2) {
    opacity: 0;
}

.hamburger-menu.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

.mobile-menu {
    display: block !important;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-menu.active {
    opacity: 1 !important;
    visibility: visible !important;
}

.mobile-menu-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 280px;
    height: 100%;
    background: #ffffff;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
}

.mobile-menu.active .mobile-menu-content {
    transform: translateX(0) !important;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.mobile-menu-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e3a8a;
}

.mobile-menu-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.mobile-menu-close:hover {
    background: #e5e7eb;
    color: #1e3a8a;
}

.mobile-nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-nav-item {
    border-bottom: 1px solid #e5e7eb;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.2s ease;
}

.mobile-nav-link:hover,
.mobile-nav-item.active .mobile-nav-link {
    background: #f3f4f6;
    color: #1e3a8a;
}

.mobile-nav-link i {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

/* === TABLERO KANBAN === */
.kanban-section {
    margin: var(--spacing-2xl) 0;
}

.kanban-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.kanban-header h2 {
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    color: #1e3a8a;
    margin: 0;
}

.kanban-board {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin: 0 auto;
    max-width: 100%;
}

.kanban-column {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-height: 300px;
    max-width: 100%;
    flex: 1;
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

.column-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.column-header.overdue {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.column-header.today {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.column-header.week1 {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.column-header.week2 {
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
    color: white;
    min-width: 24px;
    text-align: center;
    display: inline-block;
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

.task-card.overdue {
    border-left: 4px solid #ef4444;
}

.task-card.today {
    border-left: 4px solid #f59e0b;
}

.task-card.week1 {
    border-left: 4px solid #3b82f6;
}

.task-card.week2 {
    border-left: 4px solid #10b981;
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
}

.task-priority {
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    font-size: 0.7rem;
    font-weight: var(--font-weight-medium);
}

.task-priority.urgent { background: #fef2f2; color: #dc2626; }
.task-priority.high { background: #fffbeb; color: #d97706; }
.task-priority.medium { background: #eff6ff; color: #2563eb; }
.task-priority.low { background: #f0fdf4; color: #059669; }

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

.task-actions {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    padding: 2px 0;
    margin-top: 4px;
    min-height: 32px;
    position: relative;
    z-index: 5;
}

.btn-edit {
    background: #1e3a8a;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 6px 8px;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    flex-shrink: 0;
}

.btn-edit:hover {
    background: #1e40af;
    text-decoration: none;
    color: #ffffff;
    opacity: 0.9;
    box-shadow: 0 2px 4px rgba(30, 58, 138, 0.3);
}

.btn-edit i {
    font-size: 0.85rem;
    color: #ffffff;
}

/* Evitar que el hover de la tarjeta afecte el botón */
.task-card:hover .btn-edit {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Responsive para el Kanban */
@media (max-width: 1200px) {
    .kanban-board {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .kanban-column {
        min-height: 250px;
        max-height: 400px;
    }
    
    .column-content {
        max-height: 300px;
        overflow-y: auto;
    }
}
.content-section{margin-bottom:var(--spacing-2xl)}
.content-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:var(--spacing-xl)}
.content-card{background:#ffffff;border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid #e5e7eb;transition:all var(--transition-normal)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:#f3f4f6;padding:var(--spacing-lg);text-align:left;font-weight:600;color:#1e3a8a;border-bottom:1px solid #e5e7eb}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid #e5e7eb;color:#6b7280}
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.btn-sm{padding:6px 10px;font-size:.85rem}
.progress-bar.large{width:100%;height:14px;background:#f3f4f6;border-radius:9999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
@media (max-width:1024px){.nav-container{flex-wrap:wrap;gap:var(--spacing-md)}.user-menu{order:-1;width:100%;justify-content:space-between}.content-grid{grid-template-columns:1fr}}
@media (max-width:768px){.welcome-header{flex-direction:column;text-align:center;gap:var(--spacing-lg);align-items:center}.welcome-stats{width:100%}.stats-row{gap:var(--spacing-sm);justify-content:center}.stat-card{min-width:120px}.btn-stats{min-width:120px}.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}

/* Header del Kanban */
.kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-md) 0;
}

.kanban-title h2 {
    margin: 0;
    color: var(--text-primary);
}

.kanban-actions {
    display: flex;
    gap: var(--spacing-md);
}

/* Botón Agregar Tarea */
.btn-add-task {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-lg);
    background: #1e3a8a;
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-add-task:hover {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
}

.modal-content {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease;
}

.modal-large {
    max-width: 600px;
}



@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
    margin: 0;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: var(--spacing-sm);
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: var(--bg-accent);
    color: var(--text-primary);
}

/* Formulario del modal */
.modal-form {
    padding: var(--spacing-lg);
}

.modal-form .form-group {
    margin-bottom: var(--spacing-lg);
}

.modal-form label {
    display: block;
    margin-bottom: var(--spacing-sm);
    font-weight: 600;
    color: var(--text-primary);
}

.modal-form input,
.modal-form textarea,
.modal-form select {
    width: 100%;
    padding: var(--spacing-md);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: var(--bg-primary);
    box-sizing: border-box;
}

.modal-form input:focus,
.modal-form textarea:focus,
.modal-form select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.modal-form textarea {
    resize: vertical;
    min-height: 80px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-md);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border-color);
}

/* Responsive para el modal */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: var(--spacing-md);
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .kanban-header {
        flex-direction: column;
        gap: var(--spacing-md);
        align-items: stretch;
    }
    
    .btn-add-task {
        justify-content: center;
    }
}

/* Panel de Debug */
.debug-panel {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.debug-panel h4 {
    margin: 0 0 var(--spacing-sm) 0;
    color: var(--text-primary);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.debug-log {
    background: #1f2937;
    color: #f9fafb;
    border-radius: var(--radius-sm);
    padding: var(--spacing-sm);
    max-height: 200px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    margin-bottom: var(--spacing-sm);
}

.debug-entry {
    margin-bottom: 4px;
    padding: 2px 0;
    border-bottom: 1px solid #374151;
}

.debug-entry:last-child {
    border-bottom: none;
}

.debug-time {
    color: #9ca3af;
    margin-right: var(--spacing-sm);
}

.debug-info {
    color: #60a5fa;
}

.debug-success {
    color: #34d399;
}

.debug-error {
    color: #f87171;
}

.debug-warning {
    color: #fbbf24;
}

/* === BADGES DE TIPO DE TAREA === */
.task-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: 8px;
}

.task-type-badge.tareas-recurrentes {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
}

.task-type-badge.tareas-eventuales {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.task-type-badge.tarea-personal {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
}

.task-type-badge i {
    font-size: 0.8rem;
}

/* === MEJORAS EN LOS CARDS === */
.task-project-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 3px 0;
    flex-wrap: wrap;
    gap: 8px;
}

.project-name {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 150px;
}

/* === BADGE PARA CLAN EXTERNO === */
.external-clan-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    margin-left: 8px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(239, 68, 68, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.external-clan-badge i {
    font-size: 0.6rem;
}

.task-description {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 3px 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.task-status {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    margin-top: 3px;
}

.task-status.overdue {
    color: #dc2626;
}

.task-status.today {
    color: #ea580c;
}

.task-status.week1 {
    color: #2563eb;
}

.task-status.week2 {
    color: #059669;
}

.task-status i {
    font-size: 0.7rem;
}

/* Estilos para subtareas */
.subtask-card {
    background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
    border: 1px solid #e9d5ff;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 3px rgba(139, 92, 246, 0.1);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    border-left: 4px solid #a855f7;
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
.subtask-card.overdue {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.subtask-card.today {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.subtask-card.week1 {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.subtask-card.week2 {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.subtask-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    color: #6b7280;
    margin-bottom: 4px;
}

.subtask-indicator i {
    font-size: 0.65rem;
}

.subtask-card .task-name {
    font-size: 0.85rem !important;
    color: #4b5563 !important;
}
</style>

<script>
// Frase motivacional en dashboard (una por sesión)
(function(){
  const qEl = document.getElementById('motQuote');
  const aEl = document.getElementById('motAuthor');
  if (!qEl || !aEl) return;
  const apis=[
    {url:'https://api.quotable.io/random', map:d=>({text:d.content, author:d.author})},
    {url:'https://zenquotes.io/api/random', map:d=>{const x=(Array.isArray(d)?d[0]:{})||{}; return {text:x.q, author:x.a};}},
    {url:'https://type.fit/api/quotes', map:d=>{const arr=Array.isArray(d)?d:[]; const r=arr[Math.floor(Math.random()*arr.length)]||{}; return {text:r.text, author:r.author||'Anónimo'};}}
  ];
  const localFallback=[
    {text:'La excelencia no es un acto, es un hábito.', author:'Aristóteles'},
    {text:'La disciplina es el puente entre metas y logros.', author:'Jim Rohn'},
    {text:'Haz hoy lo que otros no harán y mañana vivirás como otros no pueden.', author:'Jerry Rice'}
  ];
  function applyQuote(q){ if(!q||!q.text) q=localFallback[Math.floor(Math.random()*localFallback.length)]; qEl.textContent='"'+(q.text||'')+'"'; aEl.textContent=q.author?('— '+q.author):''; }
  (async function(){
    for(const api of apis){
      try { const r=await fetch(api.url,{credentials:'omit'}); if(!r.ok) continue; const d=await r.json(); const q=api.map(d); if(q&&q.text){ applyQuote(q); return; } } catch(_){ }
    }
    applyQuote(null);
  })();
})();



// Función para cambiar el estado de las tareas (igual que clan_leader)
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
    
    // Hacer llamada AJAX para completar tarea o subtarea
    const formData = new FormData();
    formData.append('task_id', taskId);
    
    console.log(`Enviando AJAX para completar ${itemType}:`, taskId);
    
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

// Función para actualizar contadores de tareas
function updateTaskCounts() {
    const columns = ['overdue', 'today', 'week1', 'week2'];
    
    columns.forEach(column => {
        const columnElement = document.querySelector(`.column-header.${column}`);
        const tasksInColumn = document.querySelectorAll(`.task-card.${column}, .subtask-card.${column}`).length;
        const countElement = columnElement ? columnElement.querySelector('.task-count') : null;
        
        if (countElement) {
            countElement.textContent = tasksInColumn;
        }
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear contenedor de notificaciones si no existe
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(notificationContainer);
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 500;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 100%;
        word-wrap: break-word;
    `;
    
    // Icono según el tipo
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    else if (type === 'error') icon = 'exclamation-circle';
    else if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}" style="font-size: 18px; flex-shrink: 0;"></i>
        <span style="flex: 1;">${message}</span>
        <button onclick="this.parentElement.remove()" style="
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            font-size: 16px;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        " onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Agregar al contenedor
    notificationContainer.appendChild(notification);
    
    // Mostrar con animación
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Ocultar automáticamente después de 5 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
    
    // Limpiar contenedor si está vacío
    setTimeout(() => {
        if (notificationContainer.children.length === 0) {
            notificationContainer.remove();
        }
    }, 5300);
}

// Sistema de Debug Visual
function addDebugLog(message, type = 'info') {
    const debugLog = document.getElementById('debugLog');
    if (!debugLog) return;
    
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = document.createElement('div');
    logEntry.className = `debug-entry debug-${type}`;
    logEntry.innerHTML = `
        <span class="debug-time">[${timestamp}]</span>
        <span class="debug-message">${message}</span>
    `;
    
    debugLog.appendChild(logEntry);
    debugLog.scrollTop = debugLog.scrollHeight;
    
    // También mostrar en consola
    console.log(`[DEBUG ${type.toUpperCase()}] ${message}`);
}

function clearDebugLog() {
    const debugLog = document.getElementById('debugLog');
    if (debugLog) {
        debugLog.innerHTML = '';
        addDebugLog('Log de debug limpiado', 'info');
    }
}

// Funciones para el modal de agregar tarea
function openAddTaskModal() {
    const modal = document.getElementById('addTaskModal');
    modal.style.display = 'flex';
    
    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('taskDueDate').min = today;
    
    // Limpiar formulario
    document.getElementById('addTaskForm').reset();
    
    // Inicializar sistema de debug
    clearDebugLog();
    addDebugLog('Modal de agregar tarea abierto', 'info');
    addDebugLog(`Usuario ID: <?php echo $user['user_id'] ?? 0; ?>`, 'info');
    addDebugLog(`Fecha actual: ${today}`, 'info');
    
    // Enfocar en el primer campo
    setTimeout(() => {
        document.getElementById('taskName').focus();
    }, 100);
}

function closeAddTaskModal() {
    const modal = document.getElementById('addTaskModal');
    modal.style.display = 'none';
}

// Función para mostrar/ocultar campos de recurrencia
function toggleRecurrenceFields() {
    const checkbox = document.getElementById('isRecurrent');
    const fields = document.getElementById('recurrenceFields');
    const dueDateHelp = document.getElementById('dueDateHelp');
    const dueDateField = document.getElementById('taskDueDate');
    const dueDateGroup = document.getElementById('dueDateGroup');
    
    console.log('Toggle recurrence - checked:', checkbox.checked);
    
    if (checkbox.checked) {
        // Mostrar campos de recurrencia
        fields.style.display = 'block';
        
        // Ocultar completamente el grupo de fecha límite
        if (dueDateGroup) {
            dueDateGroup.style.display = 'none';
        }
        
        // IMPORTANTE: Quitar required y limpiar valor de fecha límite
        if (dueDateField) {
            dueDateField.required = false;
            dueDateField.removeAttribute('required'); // Asegurar que se quite el atributo
            dueDateField.value = ''; // Limpiar el valor para evitar validación
            console.log('Fecha límite required:', dueDateField.required);
        }
        
        // Hacer requeridos los campos de recurrencia
        document.getElementById('recurrenceType').required = true;
        document.getElementById('recurrenceStart').required = true;
        
        // Mostrar ayuda
        if (dueDateHelp) {
            dueDateHelp.style.display = 'block';
            dueDateHelp.innerHTML = 'ℹ️ Para tareas recurrentes, se usará la fecha de inicio como fecha límite';
        }
    } else {
        // Ocultar campos de recurrencia
        fields.style.display = 'none';
        
        // Mostrar campo de fecha límite normal
        if (dueDateGroup) {
            dueDateGroup.style.display = 'block';
        }
        
        // IMPORTANTE: Restaurar required para fecha límite
        if (dueDateField) {
            dueDateField.required = true;
            dueDateField.setAttribute('required', 'required');
            console.log('Fecha límite required:', dueDateField.required);
        }
        
        // Ocultar ayuda
        if (dueDateHelp) {
            dueDateHelp.style.display = 'none';
        }
        
        // Quitar requerimiento de campos de recurrencia
        document.getElementById('recurrenceType').required = false;
        document.getElementById('recurrenceStart').required = false;
        
        // Limpiar valores de recurrencia
        document.getElementById('recurrenceType').value = '';
        document.getElementById('recurrenceStart').value = '';
        document.getElementById('recurrenceEnd').value = '';
    }
}

// Cerrar modal al hacer click fuera de él
document.addEventListener('DOMContentLoaded', function() {
    const addTaskModal = document.getElementById('addTaskModal');
    
    addTaskModal.addEventListener('click', function(e) {
        if (e.target === addTaskModal) {
            closeAddTaskModal();
        }
    });
    
    // Manejar envío del formulario
    document.getElementById('addTaskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createPersonalTask();
    });
    
    // Función para el menú hamburguesa (global)
    window.toggleMobileMenu = function() {
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburger = document.querySelector('.hamburger-menu');
        
        if (mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            hamburger.classList.remove('active');
        } else {
            mobileMenu.classList.add('active');
            hamburger.classList.add('active');
        }
    };
    
    // Cerrar menú móvil al hacer click en un enlace
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            toggleMobileMenu();
        });
    });
    
    // Cerrar menú móvil al hacer click fuera
    const mobileMenuContainer = document.getElementById('mobileMenu');
    mobileMenuContainer.addEventListener('click', function(e) {
        if (e.target === mobileMenuContainer) {
            toggleMobileMenu();
        }
    });

});

// Función para crear tarea personal
function createPersonalTask() {
    const form = document.getElementById('addTaskForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales para tarea personal
    formData.append('route', 'clan_member/create-personal-task');
    formData.append('user_id', '<?php echo $user['user_id'] ?? 0; ?>');
    
    // Mostrar estado de carga
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    submitBtn.disabled = true;
    
    // Log para debugging
    const debugData = {
        task_name: formData.get('task_name'),
        description: formData.get('description'),
        priority: formData.get('priority'),
        due_date: formData.get('due_date'),
        status: formData.get('status'),
        user_id: formData.get('user_id')
    };
    
    // Log del valor original de priority
    console.log('Priority value from form:', debugData.priority);
    console.log('Priority type:', typeof debugData.priority);
    
    // La prioridad ahora es fija en 'medium', no requiere validación
    if (!debugData.priority) {
        debugData.priority = 'medium';
        formData.set('priority', 'medium');
    }
    
    addDebugLog('Iniciando creación de tarea personal...', 'info');
    addDebugLog(`Datos a enviar: ${JSON.stringify(debugData)}`, 'info');
    
    fetch('?route=clan_member/create-personal-task', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        addDebugLog(`Respuesta del servidor: ${response.status} ${response.statusText}`, 'info');
        return response.json();
    })
    .then(data => {
        addDebugLog(`Datos de respuesta: ${JSON.stringify(data)}`, 'info');
        if (data.success) {
            addDebugLog('Tarea creada exitosamente!', 'success');
            showNotification('Tarea creada exitosamente', 'success');
            closeAddTaskModal();
            
            // Recargar la página para mostrar la nueva tarea
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            addDebugLog(`Error al crear tarea: ${data.message}`, 'error');
            showNotification(data.message || 'Error al crear la tarea', 'error');
        }
    })
    .catch(error => {
        addDebugLog(`Error de conexión: ${error.message}`, 'error');
        console.error('Error en fetch:', error);
        showNotification('Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función para probar la creación de tarea con datos mínimos
function testCreateTask() {
    addDebugLog('Iniciando test de creación de tarea...', 'info');
    showNotification('Probando creación de tarea con datos mínimos...', 'info');
    
    const formData = new FormData();
    formData.append('route', 'clan_member/create-personal-task');
    formData.append('user_id', '<?php echo $user['user_id'] ?? 0; ?>');
    formData.append('task_name', 'Tarea de Prueba');
    formData.append('description', 'Descripción de prueba');
    formData.append('priority', 'medium');
    formData.append('due_date', new Date().toISOString().split('T')[0]);
    formData.append('status', 'pending');

    addDebugLog(`Datos de prueba enviados: ${JSON.stringify({
        task_name: 'Tarea de Prueba',
        description: 'Descripción de prueba',
        priority: 'medium',
        due_date: new Date().toISOString().split('T')[0],
        status: 'pending',
        user_id: '<?php echo $user['user_id'] ?? 0; ?>'
    })}`, 'info');

    const submitBtn = document.querySelector('#addTaskForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    submitBtn.disabled = true;

    fetch('?route=clan_member/create-personal-task', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        addDebugLog(`Respuesta del servidor: ${response.status} ${response.statusText}`, 'info');
        return response.json();
    })
    .then(data => {
        addDebugLog(`Datos de respuesta: ${JSON.stringify(data)}`, 'info');
        if (data.success) {
            addDebugLog('Tarea creada exitosamente con datos mínimos!', 'success');
            showNotification('Tarea creada exitosamente con datos mínimos!', 'success');
            closeAddTaskModal();
            // No recargar la página, solo mostrar notificación
        } else {
            addDebugLog(`Error al crear tarea: ${data.message}`, 'error');
            showNotification(data.message || 'Error al crear la tarea con datos mínimos', 'error');
        }
    })
    .catch(error => {
        addDebugLog(`Error de conexión: ${error.message}`, 'error');
        showNotification('Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función para recargar el dashboard
function reloadDashboard() {
    addDebugLog('Recargando dashboard...', 'info');
    showNotification('Recargando dashboard...', 'info');
    location.reload();
}

// Función para obtener el color y texto de prioridad
function getPriorityBadge(priority) {
    const priorityConfig = {
        'low': { color: '#6b7280', text: 'Baja', bgColor: '#f3f4f6' },
        'medium': { color: '#3b82f6', text: 'Media', bgColor: '#dbeafe' },
        'high': { color: '#f59e0b', text: 'Alta', bgColor: '#fef3c7' },
        'critical': { color: '#dc2626', text: 'Crítica', bgColor: '#fee2e2' }
    };
    
    const config = priorityConfig[priority] || priorityConfig['medium'];
    
    return `<span class="priority-badge" style="
        background: ${config.bgColor};
        color: ${config.color};
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid ${config.color}20;
    ">${config.text}</span>`;
}

// Función para obtener el score de prioridad para ordenamiento
function getPriorityScore(priority) {
    const scores = { 'low': 1, 'medium': 2, 'high': 3, 'critical': 4 };
    return scores[priority] || 2;
}

// Función para ordenar tareas por prioridad y fecha
function sortTasksByPriorityAndDate(tasks) {
    return tasks.sort((a, b) => {
        // Primero por prioridad (crítica primero)
        const priorityDiff = getPriorityScore(b.priority) - getPriorityScore(a.priority);
        if (priorityDiff !== 0) return priorityDiff;
        
        // Luego por fecha de vencimiento (más cercana primero)
        const aDate = a.due_date ? new Date(a.due_date) : new Date('9999-12-31');
        const bDate = b.due_date ? new Date(b.due_date) : new Date('9999-12-31');
        return aDate - bDate;
    });
}

// Función para insertar etiquetas de prioridad en todas las tareas
function insertPriorityBadges() {
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach(card => {
        const taskId = card.dataset.taskId;
        const priority = card.dataset.priority || 'medium';
        const priorityBadge = card.querySelector('.task-priority-badge');
        
        if (priorityBadge && taskId) {
            // Insertar la etiqueta de prioridad usando el atributo data-priority
            priorityBadge.innerHTML = getPriorityBadge(priority);
        }
    });
}

// Ejecutar cuando el DOM esté listo
// Función preventiva para eliminar etiquetas "Dirección" del dashboard
function removeDireccionLabels() {
    // Buscar todos los badges de clan externo
    const externalBadges = document.querySelectorAll('.external-clan-badge');
    externalBadges.forEach(badge => {
        if (badge.textContent && badge.textContent.includes('Dirección')) {
            console.warn('Removiendo badge de clan incorrecto:', badge.textContent);
            badge.remove();
        }
    });
    
    // Buscar cualquier elemento que contenga "Dirección Rinorisk"
    const allElements = document.querySelectorAll('span, .project-name, .external-clan-badge');
    allElements.forEach(element => {
        if (element.textContent && element.textContent.includes('Dirección')) {
            console.warn('Removiendo elemento con Dirección:', element.textContent);
            element.remove();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Insertar etiquetas de prioridad
    insertPriorityBadges();
    
    // Ejecutar función preventiva para eliminar etiquetas incorrectas
    removeDireccionLabels();
    
    // Ejecutar la función preventiva cada 2 segundos como medida adicional
    setInterval(removeDireccionLabels, 2000);
    
    // Verificar si se debe abrir el modal para editar una tarea
    <?php if (isset($editTaskId) && $editTaskId > 0): ?>
    // Abrir modal automáticamente para editar tarea
    setTimeout(() => {
        openAddTaskModal();
        addDebugLog('Modal abierto automáticamente para editar tarea ID: <?php echo $editTaskId; ?>', 'info');
    }, 500);
    <?php endif; ?>
    
    // También ejecutar después de recargar el dashboard
    if (typeof window.addEventListener === 'function') {
        window.addEventListener('load', function() {
            insertPriorityBadges();
            removeDireccionLabels(); // También ejecutar después de load
        });
    }
});


</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [];
require_once __DIR__ . '/../layout.php';
?>


