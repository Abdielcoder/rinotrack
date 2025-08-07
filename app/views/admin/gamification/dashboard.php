<?php
// Capturar el contenido de la vista
ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamificación Mitológica - RinoTrack</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS con rutas absolutas -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/css/admin.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/css/gamification.css">
    
    <script>
        const APP_URL = '<?php echo APP_URL; ?>';
    </script>
</head>
<body>
<div class="modern-dashboard" data-theme="default">
    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-trophy"></i>
                </div>
                <span class="brand-text">Gamificación Mitológica</span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($currentPage ?? 'gamification') === 'gamification' ? 'active' : ''; ?>">
                    <a href="?route=gamification" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=gamification/badges" class="nav-link">
                        <i class="fas fa-medal"></i>
                        <span>Badges</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=gamification/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=gamification/clan-achievements" class="nav-link">
                        <i class="fas fa-crown"></i>
                        <span>Logros de Clan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=gamification/events" class="nav-link">
                        <i class="fas fa-calendar-star"></i>
                        <span>Eventos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=gamification/leaderboard" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Leaderboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin" class="nav-link">
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
                    🏛️ Sistema de Gamificación Mitológica
                </h1>
                <p class="welcome-subtitle">
                    Gestiona badges, logros y eventos para motivar a los héroes del Olimpo.
                </p>
            </div>
        </header>

        <!-- Estadísticas principales -->
        <section class="stats-section animate-fade-in">
            <div class="stats-grid">
                <div class="stat-card glass">
                    <div class="stat-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo number_format($stats['total_badges_awarded'] ?? 0); ?></h3>
                        <p class="stat-label">Badges Otorgados</p>
                    </div>
                </div>
                
                <div class="stat-card glass">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo number_format($stats['users_with_badges'] ?? 0); ?></h3>
                        <p class="stat-label">Usuarios con Badges</p>
                    </div>
                </div>
                
                <div class="stat-card glass">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo number_format($stats['total_points_awarded'] ?? 0); ?></h3>
                        <p class="stat-label">Puntos Otorgados</p>
                    </div>
                </div>
                
                <div class="stat-card glass">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number"><?php echo number_format($stats['active_events'] ?? 0); ?></h3>
                        <p class="stat-label">Eventos Activos</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido principal -->
        <div class="content-grid">
            <!-- Leaderboard -->
            <section class="content-section glass animate-fade-in">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-trophy"></i>
                        Top 10 Héroes del Olimpo
                    </h2>
                    <a href="?route=gamification/leaderboard" class="btn btn-primary btn-sm">
                        Ver Completo
                    </a>
                </div>
                
                <div class="leaderboard-list">
                    <?php if (!empty($leaderboard)): ?>
                        <?php foreach ($leaderboard as $index => $player): ?>
                            <div class="leaderboard-item">
                                <div class="rank-position">
                                    <?php if ($index < 3): ?>
                                        <span class="rank-medal rank-<?php echo $index + 1; ?>">
                                            <?php echo $index + 1; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="rank-number"><?php echo $index + 1; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="player-info">
                                    <div class="player-name"><?php echo Utils::escape($player['full_name'] ?: $player['username']); ?></div>
                                    <div class="player-clan"><?php echo Utils::escape($player['clan_name'] ?? 'Sin Clan'); ?></div>
                                </div>
                                <div class="player-stats">
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo number_format($player['total_points']); ?></span>
                                        <span class="stat-label">Puntos</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $player['badges_earned']; ?></span>
                                        <span class="stat-label">Badges</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-trophy empty-icon"></i>
                            <p class="empty-text">Aún no hay héroes en el ranking</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Eventos Activos -->
            <section class="content-section glass animate-fade-in">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-star"></i>
                        Eventos Activos
                    </h2>
                    <a href="?route=gamification/events" class="btn btn-primary btn-sm">
                        Gestionar
                    </a>
                </div>
                
                <div class="events-list">
                    <?php if (!empty($activeEvents)): ?>
                        <?php foreach ($activeEvents as $event): ?>
                            <div class="event-card">
                                <div class="event-header">
                                    <div class="event-icon">🎉</div>
                                    <div class="event-info">
                                        <h4 class="event-name"><?php echo Utils::escape($event['event_name']); ?></h4>
                                        <p class="event-description"><?php echo Utils::escape($event['event_description']); ?></p>
                                    </div>
                                </div>
                                <div class="event-details">
                                    <div class="event-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($event['start_date'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($event['end_date'])); ?>
                                    </div>
                                    <div class="event-bonus">
                                        <i class="fas fa-star"></i>
                                        x<?php echo $event['bonus_multiplier']; ?> Puntos
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar empty-icon"></i>
                            <p class="empty-text">No hay eventos activos</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Acciones rápidas -->
        <section class="quick-actions animate-fade-in">
            <div class="actions-grid">
                <a href="?route=gamification/badges" class="action-card glass">
                    <div class="action-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="action-title">Gestionar Badges</h3>
                    <p class="action-description">Crear y administrar insignias mitológicas</p>
                </a>
                
                <a href="?route=gamification/users" class="action-card glass">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="action-title">Usuarios</h3>
                    <p class="action-description">Ver perfiles y asignar badges</p>
                </a>
                
                <a href="?route=gamification/clan-achievements" class="action-card glass">
                    <div class="action-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="action-title">Logros de Clan</h3>
                    <p class="action-description">Configurar logros para clanes</p>
                </a>
                
                <a href="?route=gamification/events" class="action-card glass">
                    <div class="action-icon">
                        <i class="fas fa-calendar-star"></i>
                    </div>
                    <h3 class="action-title">Eventos</h3>
                    <p class="action-description">Crear eventos especiales</p>
                </a>
            </div>
        </section>
    </main>
</div>

<style>
/* Estilos específicos para gamificación */
.leaderboard-list {
    max-height: 400px;
    overflow-y: auto;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

.leaderboard-item:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.leaderboard-item:last-child {
    border-bottom: none;
}

.rank-position {
    width: 40px;
    text-align: center;
    margin-right: 16px;
}

.rank-medal {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.rank-1 {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000;
}

.rank-2 {
    background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
    color: #000;
}

.rank-3 {
    background: linear-gradient(135deg, #CD7F32, #B8860B);
    color: #fff;
}

.rank-number {
    color: #6B7280;
    font-weight: bold;
}

.player-info {
    flex: 1;
    margin-right: 16px;
}

.player-name {
    font-weight: 600;
    color: #fff;
    margin-bottom: 4px;
}

.player-clan {
    font-size: 12px;
    color: #9CA3AF;
}

.player-stats {
    display: flex;
    gap: 16px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-weight: bold;
    color: #10B981;
    font-size: 16px;
}

.stat-label {
    font-size: 11px;
    color: #6B7280;
    text-transform: uppercase;
}

.events-list {
    max-height: 300px;
    overflow-y: auto;
}

.event-card {
    padding: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

.event-card:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.event-card:last-child {
    border-bottom: none;
}

.event-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 12px;
}

.event-icon {
    font-size: 24px;
    margin-right: 12px;
    margin-top: 2px;
}

.event-name {
    font-weight: 600;
    color: #fff;
    margin: 0 0 4px 0;
    font-size: 16px;
}

.event-description {
    color: #9CA3AF;
    font-size: 14px;
    margin: 0;
    line-height: 1.4;
}

.event-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #6B7280;
}

.event-date, .event-bonus {
    display: flex;
    align-items: center;
    gap: 6px;
}

.event-bonus {
    color: #F59E0B;
    font-weight: 600;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.action-card {
    padding: 24px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 12px;
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    color: inherit;
    text-decoration: none;
}

.action-icon {
    font-size: 32px;
    margin-bottom: 16px;
    color: #3B82F6;
}

.action-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #fff;
}

.action-description {
    color: #9CA3AF;
    font-size: 14px;
    margin: 0;
    line-height: 1.4;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6B7280;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-text {
    font-size: 16px;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .player-stats {
        flex-direction: column;
        gap: 8px;
    }
    
    .event-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>

    </div>
</main>
</div>

<!-- JavaScript con rutas absolutas -->
<script src="<?php echo APP_URL; ?>assets/js/script.js"></script>
<script src="<?php echo APP_URL; ?>assets/js/gamification.js"></script>

</body>
</html> 