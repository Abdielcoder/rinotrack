<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <!-- Selector de Temas -->
    <div class="theme-selector" style="display: none;">
        <button class="theme-btn" data-theme="default" title="Tema Predeterminado">
            <div class="theme-preview">
                <div class="color-dot" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);"></div>
                <div class="color-dot" style="background: #10b981;"></div>
            </div>
        </button>
        <button class="theme-btn" data-theme="dark" title="Tema Oscuro">
            <div class="theme-preview">
                <div class="color-dot" style="background: #0f172a;"></div>
                <div class="color-dot" style="background: #6366f1;"></div>
            </div>
        </button>
        <button class="theme-btn" data-theme="tech-blue" title="Tech Blue">
            <div class="theme-preview">
                <div class="color-dot" style="background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);"></div>
                <div class="color-dot" style="background: #06b6d4;"></div>
            </div>
        </button>
        <button class="theme-btn" data-theme="forest" title="Forest Green">
            <div class="theme-preview">
                <div class="color-dot" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);"></div>
                <div class="color-dot" style="background: #0d9488;"></div>
            </div>
        </button>
        <button class="theme-btn" data-theme="sunset" title="Sunset">
            <div class="theme-preview">
                <div class="color-dot" style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);"></div>
                <div class="color-dot" style="background: #f97316;"></div>
            </div>
        </button>
    </div>

    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-rhino"></i>
                </div>
                <span class="brand-text">RinoTrack</span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($currentPage ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
                    <a href="?route=dashboard" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($currentPage ?? '') === 'admin' ? 'active' : ''; ?>">
                    <a href="?route=admin" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($currentPage ?? '') === 'badges' ? 'active' : ''; ?>">
                    <a href="?route=badges" class="nav-link">
                        <i class="fas fa-award"></i>
                        <span>Badges</span>
                    </a>
                </li>
                <li class="nav-item dropdown <?php echo (strpos($currentPage ?? '', 'kpi') === 0) ? 'active' : ''; ?>">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chart-line"></i>
                        <span>KPIs</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="?route=kpi/dashboard" class="dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard KPI</span>
                        </a></li>
                        <li><a href="?route=kpi/quarters" class="dropdown-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Trimestres</span>
                        </a></li>
                        <li><a href="?route=kpi/projects" class="dropdown-link">
                            <i class="fas fa-project-diagram"></i>
                            <span>Asignación</span>
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item <?php echo ($currentPage ?? '') === 'perfil' ? 'active' : ''; ?>">
                    <a href="?route=perfil" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Perfil</span>
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
                    <span class="user-role">Administrator</span>
                </div>
                <div class="user-actions">
                    <button class="action-btn" title="Configuración">
                        <i class="fas fa-cog"></i>
                    </button>
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
                    ¡Hola, <?php echo Utils::escape($user['full_name'] ?: $user['username']); ?>! 👋
                </h1>
                <p class="welcome-subtitle">
                    Bienvenido de vuelta a tu dashboard. Aquí tienes un resumen de tu actividad reciente.
                </p>
            </div>
            <div class="welcome-stats">
                <div class="quick-stat">
                    <div class="stat-icon success">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-text">
                        <span class="stat-value"><?php echo Utils::escape($sessionInfo['login_time']); ?></span>
                        <span class="stat-label">Último acceso</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Grid de estadísticas principales -->
        <section class="stats-section animate-fade-in">
            <div class="stats-grid">
                <div class="stat-card gradient-bg">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Proyectos</h3>
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['projects']; ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% este mes</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Tareas</h3>
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['tasks']; ?></div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+8% esta semana</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>Completado</h3>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['completed']; ?></div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Excelente progreso</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-header">
                            <h3>En Progreso</h3>
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-clock"></i>
                            <span>Tareas activas</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido adicional -->
        <section class="content-section animate-fade-in">
            <div class="content-grid">
                <!-- Actividad Reciente -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-clock icon-gradient"></i>
                            Actividad Reciente
                        </h3>
                        <button class="btn-secondary btn-sm">Ver todo</button>
                    </div>
                    <div class="activity-timeline">
                        <?php foreach ($recentActivity as $index => $activity): ?>
                        <div class="timeline-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                            <div class="timeline-icon">
                                <i class="<?php echo $activity['icon']; ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <h4><?php echo Utils::escape($activity['title']); ?></h4>
                                <span class="timeline-time"><?php echo Utils::escape($activity['time']); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Información del Sistema -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-info-circle icon-gradient"></i>
                            Información de Sesión
                        </h3>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">IP de Conexión</span>
                                <span class="info-value"><?php echo Utils::escape($sessionInfo['ip_address']); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-browser"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Navegador</span>
                                <span class="info-value"><?php echo Utils::escape(substr($sessionInfo['user_agent'], 0, 50)) . '...'; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="info-details">
                                <span class="info-label">Miembro desde</span>
                                <span class="info-value"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
/* ============================================
   ESTILOS ESPECÍFICOS DEL DASHBOARD MODERNO
   ============================================ */

.modern-dashboard {
    min-height: 100vh;
    background: var(--bg-secondary);
    padding: 0;
    position: relative;
}

/* === SELECTOR DE TEMAS === */
.theme-selector {
    position: fixed;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    display: flex;
    gap: var(--spacing-xs);
    z-index: 1000;
    background: var(--bg-glass);
    backdrop-filter: var(--glass-backdrop);
    padding: var(--spacing-sm);
    border-radius: var(--radius-full);
    border: 1px solid var(--glass-border);
    box-shadow: var(--shadow-lg);
}

.theme-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-primary);
    box-shadow: var(--shadow-sm);
}

.theme-btn:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-md);
}

.theme-btn.active {
    transform: scale(1.1);
    box-shadow: var(--shadow-glow);
}

.theme-preview {
    display: flex;
    gap: 2px;
}

.color-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

/* === NAVEGACIÓN MODERNA === */
.modern-nav {
    background: var(--bg-glass);
    backdrop-filter: var(--glass-backdrop);
    border-bottom: 1px solid var(--glass-border);
    padding: var(--spacing-md) 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-xl);
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.brand-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-white);
    font-size: 1.2rem;
}

.brand-text {
    font-size: 1.5rem;
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: var(--spacing-sm);
}

.nav-item .nav-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: var(--font-weight-medium);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.nav-item .nav-link:hover {
    color: var(--primary-color);
    background: var(--bg-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.nav-item.active .nav-link {
    background: var(--primary-gradient);
    color: var(--text-white);
    box-shadow: var(--shadow-glow);
}

.nav-item.active .nav-link:hover {
    transform: translateY(-2px);
}

/* === MENÚ DE USUARIO === */
.user-menu {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.modern-avatar {
    position: relative;
    width: 45px;
    height: 45px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-white);
    font-weight: var(--font-weight-semibold);
    box-shadow: var(--shadow-md);
}

.status-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: var(--success);
    border: 2px solid var(--bg-primary);
    border-radius: var(--radius-full);
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-name {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    font-size: 0.95rem;
}

.user-role {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.user-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.action-btn {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: var(--radius-md);
    background: var(--bg-primary);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
}

.action-btn:hover {
    color: var(--primary-color);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.action-btn.logout:hover {
    color: var(--error);
}

/* === CONTENIDO PRINCIPAL === */
.main-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: var(--spacing-xl) var(--spacing-lg);
}

/* === HEADER DE BIENVENIDA === */
.welcome-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-2xl);
    padding: var(--spacing-xl);
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.welcome-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

.quick-stat {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    background: var(--bg-tertiary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--bg-accent);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-white);
    font-size: 1.2rem;
}

.stat-icon.success {
    background: var(--success);
}

.stat-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.stat-value {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-muted);
}

/* === ESTADÍSTICAS === */
.stats-section {
    margin-bottom: var(--spacing-2xl);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-xl);
}

.stat-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.stat-card.gradient-bg {
    background: var(--primary-gradient);
    color: var(--text-white);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
}

.stat-header h3 {
    font-size: 1.1rem;
    font-weight: var(--font-weight-semibold);
    opacity: 0.9;
}

.stat-header i {
    font-size: 1.5rem;
    opacity: 0.7;
}

.stat-number {
    font-size: 3rem;
    font-weight: var(--font-weight-bold);
    margin-bottom: var(--spacing-md);
    line-height: 1;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 0.9rem;
    opacity: 0.8;
}

.stat-trend.positive {
    color: var(--success);
}

/* === CONTENIDO ADICIONAL === */
.content-section {
    margin-bottom: var(--spacing-2xl);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: var(--spacing-xl);
}

.content-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
    transition: all var(--transition-normal);
}

.content-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--bg-tertiary);
}

.card-header h3 {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    font-size: 1.3rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.icon-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.btn-sm {
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: 0.85rem;
}

/* === TIMELINE === */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    transition: all var(--transition-normal);
    animation: slideIn var(--transition-slow) ease-out;
}

.timeline-item:hover {
    background: var(--bg-tertiary);
    transform: translateX(5px);
}

.timeline-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-white);
    font-size: 0.9rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.timeline-content h4 {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
    margin-bottom: 2px;
}

.timeline-time {
    font-size: 0.85rem;
    color: var(--text-muted);
}

/* === INFORMACIÓN === */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.info-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    border: 1px solid var(--bg-accent);
    transition: all var(--transition-normal);
}

.info-item:hover {
    transform: translateX(3px);
    box-shadow: var(--shadow-sm);
}

.info-icon {
    width: 35px;
    height: 35px;
    background: var(--bg-primary);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 0.9rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.info-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.info-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: var(--font-weight-medium);
}

.info-value {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
    word-break: break-word;
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    .nav-container {
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }
    
    .user-menu {
        order: -1;
        width: 100%;
        justify-content: space-between;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .theme-selector {
        top: var(--spacing-md);
        right: var(--spacing-md);
        flex-wrap: wrap;
    }
    
    .welcome-header {
        flex-direction: column;
        text-align: center;
        gap: var(--spacing-lg);
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
    }
    
    .nav-menu {
        display: none;
    }
    
    .main-content {
        padding: var(--spacing-lg) var(--spacing-md);
    }
}

@media (max-width: 480px) {
    .user-menu {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .timeline-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Configurar variables para el layout
$title = 'Dashboard - ' . APP_NAME;

// Incluir el layout principal
include __DIR__ . '/layout.php';
?>