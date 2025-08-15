<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-star"></i>
                </div>
                <span class="brand-text">Polaris Admin</span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($currentPage ?? 'admin') === 'admin' ? 'active' : ''; ?>">
                    <a href="?route=admin" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/projects" class="nav-link">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyectos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/tasks" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>Tareas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/clans" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Clanes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/notifications" class="nav-link">
                        <i class="fas fa-bell"></i>
                        <span>Notificaciones</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chart-line"></i>
                        <span>KPIs</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="?route=kpi/dashboard" class="dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a></li>
                        <li><a href="?route=kpi/quarters" class="dropdown-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Trimestres</span>
                        </a></li>
                        <li><a href="?route=kpi/projects" class="dropdown-link">
                            <i class="fas fa-project-diagram"></i>
                            <span>Proyectos</span>
                        </a></li>
                    </ul>
                </li>
                <li style="display: none;" class="nav-item">
                    <a href="?route=gamification" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Gamificación</span>
                    </a>
                </li>
                <li style="display: none;" class="nav-item">
                    <a href="?route=dashboard" class="nav-link">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver</span>
                    </a>
                </li>
            </ul>

            <!-- Información del usuario -->
            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
                    <div class="status-dot"></div>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span>
                    <span class="user-role">Administrador</span>
                </div>
                <div class="user-actions">
                    <a href="?route=logout" class="action-btn logout" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Header de bienvenida -->
        <header class="welcome-header animate-fade-in">
            <div class="welcome-content">
                <h1 class="welcome-title">
                    🚀 Panel de Administración
                </h1>
                <p class="welcome-subtitle">
                    Gestiona usuarios, proyectos y clanes desde un solo lugar.
                </p>
            </div>
        </header>

        <!-- Grid de estadísticas principales -->
        <section class="stats-section animate-fade-in">
            <div class="stats-grid">
                <!-- Estadísticas de Usuarios -->
                <div class="stat-card gradient-bg">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Usuarios</h3>
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $userStats['total_users']; ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $userStats['active_users']; ?> activos</span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Proyectos -->
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Proyectos</h3>
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="stat-number"><?php echo $projectStats['total_projects']; ?></div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span><?php echo $projectStats['open_projects']; ?> abiertos</span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Clanes -->
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Clanes</h3>
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="stat-number"><?php echo $clanStats['total_clans']; ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-chart-line"></i>
                            <span>Promedio: <?php echo number_format((float)($clanStats['avg_members_per_clan'] ?? 0), 1); ?> miembros</span>
                        </div>
                    </div>
                </div>

                <!-- Progreso Promedio -->
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Progreso Promedio</h3>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format((float)($projectStats['avg_progress'] ?? 0), 1); ?>%</div>
                        <div class="stat-trend positive">
                            <i class="fas fa-trophy"></i>
                            <span>Rendimiento del sistema</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Acciones rápidas -->
        <section class="content-section animate-fade-in">
            <div class="content-grid">
                <!-- Acciones de Usuario -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-user-plus icon-gradient"></i>
                            Gestión de Usuarios
                        </h3>
                    </div>
                    <div class="action-buttons">
                        <a href="?route=admin/users" class="btn btn-primary">
                            <i class="fas fa-users"></i>
                            Ver Usuarios
                        </a>
                        <a href="?route=admin/users" class="btn btn-secondary">
                            <i class="fas fa-user-plus"></i>
                            Crear Usuario
                        </a>
                    </div>
                </div>

                <!-- Acciones de Proyecto -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-project-diagram icon-gradient"></i>
                            Gestión de Proyectos
                        </h3>
                    </div>
                    <div class="action-buttons">
                        <a href="?route=admin/projects" class="btn btn-primary">
                            <i class="fas fa-list"></i>
                            Ver Proyectos
                        </a>
                        <a href="?route=admin/projects" class="btn btn-secondary">
                            <i class="fas fa-plus"></i>
                            Crear Proyecto
                        </a>
                    </div>
                </div>

                <!-- Acciones de Clan -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-users-cog icon-gradient"></i>
                            Gestión de Clanes
                        </h3>
                    </div>
                    <div class="action-buttons">
                        <a href="?route=admin/clans" class="btn btn-primary">
                            <i class="fas fa-eye"></i>
                            Ver Clanes
                        </a>
                        <a href="?route=admin/clans" class="btn btn-secondary">
                            <i class="fas fa-plus"></i>
                            Crear Clan
                        </a>
                    </div>
                </div>

                <!-- Distribución de Roles -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-chart-pie icon-gradient"></i>
                            Distribución de Roles
                        </h3>
                    </div>
                    <div class="role-stats">
                        <?php foreach ($roleStats as $role): ?>
                        <div class="role-item">
                            <div class="role-info">
                                <span class="role-name"><?php echo Utils::escape($role['role_name']); ?></span>
                                <span class="role-count"><?php echo $role['user_count']; ?> usuarios</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-progress" style="width: <?php echo ($userStats['total_users'] > 0) ? ($role['user_count'] / $userStats['total_users'] * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>



<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Configurar variables para el layout
$title = 'Panel de Administración - ' . APP_NAME;

// Incluir el layout del admin
include __DIR__ . '/layout.php';
?>