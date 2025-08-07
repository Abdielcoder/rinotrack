<?php
// Capturar el contenido de la vista
ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Badges - Gamificación Mitológica</title>
    
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
                <li class="nav-item">
                    <a href="?route=gamification" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                    🏆 Gestión de Badges Mitológicos
                </h1>
                <p class="welcome-subtitle">
                    Crea y administra insignias para recompensar a los héroes del Olimpo.
                </p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openCreateBadgeModal()">
                    <i class="fas fa-plus"></i>
                    Crear Badge
                </button>
            </div>
        </header>

        <!-- Filtros -->
        <section class="filters-section animate-fade-in">
            <div class="filters-container glass">
                <div class="filter-group">
                    <label for="categoryFilter" class="filter-label">Categoría:</label>
                    <select id="categoryFilter" class="filter-select" onchange="filterBadges()">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $key => $name): ?>
                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">Estado:</label>
                    <select id="statusFilter" class="filter-select" onchange="filterBadges()">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="searchFilter" class="filter-label">Buscar:</label>
                    <input type="text" id="searchFilter" class="filter-input" placeholder="Nombre del badge..." onkeyup="filterBadges()">
                </div>
            </div>
        </section>

        <!-- Lista de badges -->
        <section class="badges-section animate-fade-in">
            <div class="badges-grid" id="badgesGrid">
                <?php foreach ($badges as $badge): ?>
                    <div class="badge-card glass" data-category="<?php echo $badge['badge_category']; ?>" data-status="<?php echo $badge['is_active']; ?>" data-name="<?php echo strtolower($badge['badge_name']); ?>">
                        <div class="badge-header">
                            <div class="badge-icon" style="background-color: <?php echo $badge['badge_color']; ?>">
                                <?php echo $badge['badge_icon']; ?>
                            </div>
                            <div class="badge-status <?php echo $badge['is_active'] ? 'active' : 'inactive'; ?>">
                                <span class="status-dot"></span>
                                <?php echo $badge['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </div>
                        </div>
                        
                        <div class="badge-content">
                            <h3 class="badge-name"><?php echo Utils::escape($badge['badge_name']); ?></h3>
                            <p class="badge-description"><?php echo Utils::escape($badge['badge_description']); ?></p>
                            
                            <div class="badge-details">
                                <div class="badge-category">
                                    <i class="fas fa-tag"></i>
                                    <?php echo $categories[$badge['badge_category']] ?? $badge['badge_category']; ?>
                                </div>
                                <div class="badge-points">
                                    <i class="fas fa-star"></i>
                                    <?php echo number_format($badge['points_reward']); ?> puntos
                                </div>
                            </div>
                        </div>
                        
                        <div class="badge-actions">
                            <button class="btn btn-sm btn-outline" onclick="editBadge(<?php echo $badge['badge_id']; ?>)">
                                <i class="fas fa-edit"></i>
                                Editar
                            </button>
                            <button class="btn btn-sm btn-outline" onclick="awardBadge(<?php echo $badge['badge_id']; ?>)">
                                <i class="fas fa-gift"></i>
                                Otorgar
                            </button>
                            <?php if ($badge['is_active']): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteBadge(<?php echo $badge['badge_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                    Eliminar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>

<!-- Modal para crear/editar badge -->
<div id="badgeModal" class="modal">
    <div class="modal-content glass">
        <div class="modal-header">
            <h2 id="modalTitle">Crear Nuevo Badge</h2>
            <span class="close" onclick="closeBadgeModal()">&times;</span>
        </div>
        
        <form id="badgeForm" onsubmit="saveBadge(event)">
            <input type="hidden" id="badgeId" name="badge_id" value="">
            
            <div class="form-group">
                <label for="badgeName">Nombre del Badge *</label>
                <input type="text" id="badgeName" name="badge_name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="badgeDescription">Descripción *</label>
                <textarea id="badgeDescription" name="badge_description" required class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="badgeIcon">Icono</label>
                    <div class="icon-selector">
                        <div class="icon-grid" id="iconGrid">
                            <div class="icon-option" data-icon="🏆" onclick="selectIcon('🏆')">🏆</div>
                            <div class="icon-option" data-icon="⭐" onclick="selectIcon('⭐')">⭐</div>
                            <div class="icon-option" data-icon="🔥" onclick="selectIcon('🔥')">🔥</div>
                            <div class="icon-option" data-icon="⚡" onclick="selectIcon('⚡')">⚡</div>
                            <div class="icon-option" data-icon="💎" onclick="selectIcon('💎')">💎</div>
                            <div class="icon-option" data-icon="👑" onclick="selectIcon('👑')">👑</div>
                            <div class="icon-option" data-icon="🎯" onclick="selectIcon('🎯')">🎯</div>
                            <div class="icon-option" data-icon="🚀" onclick="selectIcon('🚀')">🚀</div>
                            <div class="icon-option" data-icon="🏅" onclick="selectIcon('🏅')">🏅</div>
                            <div class="icon-option" data-icon="💪" onclick="selectIcon('💪')">💪</div>
                            <div class="icon-option" data-icon="🧠" onclick="selectIcon('🧠')">🧠</div>
                            <div class="icon-option" data-icon="🎨" onclick="selectIcon('🎨')">🎨</div>
                            <div class="icon-option" data-icon="⚔️" onclick="selectIcon('⚔️')">⚔️</div>
                            <div class="icon-option" data-icon="🛡️" onclick="selectIcon('🛡️')">🛡️</div>
                            <div class="icon-option" data-icon="🏛️" onclick="selectIcon('🏛️')">🏛️</div>
                            <div class="icon-option" data-icon="🌟" onclick="selectIcon('🌟')">🌟</div>
                        </div>
                        <input type="hidden" id="badgeIcon" name="badge_icon" value="🏆">
                        <div class="selected-icon" id="selectedIcon">🏆</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="badgeColor">Color</label>
                    <div class="color-selector">
                        <div class="color-grid" id="colorGrid">
                            <div class="color-option" data-color="#3B82F6" onclick="selectColor('#3B82F6')" style="background: #3B82F6;"></div>
                            <div class="color-option" data-color="#10B981" onclick="selectColor('#10B981')" style="background: #10B981;"></div>
                            <div class="color-option" data-color="#F59E0B" onclick="selectColor('#F59E0B')" style="background: #F59E0B;"></div>
                            <div class="color-option" data-color="#EF4444" onclick="selectColor('#EF4444')" style="background: #EF4444;"></div>
                            <div class="color-option" data-color="#8B5CF6" onclick="selectColor('#8B5CF6')" style="background: #8B5CF6;"></div>
                            <div class="color-option" data-color="#EC4899" onclick="selectColor('#EC4899')" style="background: #EC4899;"></div>
                            <div class="color-option" data-color="#06B6D4" onclick="selectColor('#06B6D4')" style="background: #06B6D4;"></div>
                            <div class="color-option" data-color="#84CC16" onclick="selectColor('#84CC16')" style="background: #84CC16;"></div>
                        </div>
                        <input type="hidden" id="badgeColor" name="badge_color" value="#3B82F6">
                        <div class="selected-color" id="selectedColor" style="background: #3B82F6;"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="badgeCategory">Categoría *</label>
                    <select id="badgeCategory" name="badge_category" required class="form-control">
                        <?php foreach ($categories as $key => $name): ?>
                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pointsReward">Puntos de Recompensa</label>
                    <input type="number" id="pointsReward" name="points_reward" value="0" min="0" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label>Criterios Requeridos</label>
                <div class="criteria-container">
                    <div class="criteria-row">
                        <div class="form-group">
                            <label for="criteriaType">Tipo de Criterio</label>
                            <select id="criteriaType" name="criteria_type" class="form-control" onchange="updateCriteriaFields()">
                                <option value="tasks_completed">Tareas Completadas</option>
                                <option value="projects_completed">Proyectos Completados</option>
                                <option value="days_active">Días Activo</option>
                                <option value="collaborations">Colaboraciones</option>
                                <option value="quality_score">Puntuación de Calidad</option>
                                <option value="speed_bonus">Bonus de Velocidad</option>
                                <option value="leadership_days">Días de Liderazgo</option>
                                <option value="innovations">Innovaciones</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="criteriaValue">Cantidad Requerida</label>
                            <input type="number" id="criteriaValue" name="criteria_value" value="10" min="1" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="criteriaTimeframe">Período de Tiempo</label>
                            <select id="criteriaTimeframe" name="criteria_timeframe" class="form-control">
                                <option value="total">Total (Sin límite)</option>
                                <option value="day">Por Día</option>
                                <option value="week">Por Semana</option>
                                <option value="month">Por Mes</option>
                                <option value="quarter">Por Trimestre</option>
                                <option value="year">Por Año</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="criteria-row" id="additionalCriteria" style="display: none;">
                        <div class="form-group">
                            <label for="criteriaCondition">Condición Adicional</label>
                            <select id="criteriaCondition" name="criteria_condition" class="form-control">
                                <option value="">Sin condición adicional</option>
                                <option value="consecutive">Consecutivo</option>
                                <option value="perfect_score">Puntuación Perfecta</option>
                                <option value="team_effort">Esfuerzo de Equipo</option>
                                <option value="first_time">Primera Vez</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="criteriaThreshold">Umbral Mínimo</label>
                            <input type="number" id="criteriaThreshold" name="criteria_threshold" value="0" min="0" step="0.1" class="form-control">
                        </div>
                    </div>
                    
                    <input type="hidden" id="requiredCriteria" name="required_criteria" value="">
                </div>
            </div>
            
            <div class="form-group" id="isActiveGroup" style="display: none;">
                <label class="checkbox-label">
                    <input type="checkbox" id="isActive" name="is_active" value="1" checked>
                    <span class="checkmark"></span>
                    Badge activo
                </label>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeBadgeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Badge</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para otorgar badge -->
<div id="awardModal" class="modal">
    <div class="modal-content glass">
        <div class="modal-header">
            <h2>Otorgar Badge</h2>
            <span class="close" onclick="closeAwardModal()">&times;</span>
        </div>
        
        <form id="awardForm" onsubmit="submitAwardBadge(event)">
            <input type="hidden" id="awardBadgeId" name="badge_id" value="">
            
            <div class="form-group">
                <label for="awardUserId">Seleccionar Usuario *</label>
                <select id="awardUserId" name="user_id" required class="form-control">
                    <option value="">Selecciona un usuario...</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeAwardModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Otorgar Badge</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos específicos para badges */
.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.badge-card {
    padding: 20px;
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.badge-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.badge-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.badge-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.badge-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
}

.badge-status.active {
    color: #10B981;
}

.badge-status.inactive {
    color: #EF4444;
}

.badge-status .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.badge-status.active .status-dot {
    background-color: #10B981;
}

.badge-status.inactive .status-dot {
    background-color: #EF4444;
}

.badge-name {
    font-size: 18px;
    font-weight: 600;
    color: #fff;
    margin: 0 0 8px 0;
}

.badge-description {
    color: #9CA3AF;
    font-size: 14px;
    line-height: 1.4;
    margin: 0 0 16px 0;
}

.badge-details {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    font-size: 12px;
}

.badge-category, .badge-points {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6B7280;
}

.badge-points {
    color: #F59E0B;
    font-weight: 600;
}

.badge-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filters-section {
    margin-bottom: 20px;
}

.filters-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    border-radius: 12px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 150px;
}

.filter-label {
    font-size: 12px;
    color: #9CA3AF;
    font-weight: 500;
    text-transform: uppercase;
}

.filter-select, .filter-input {
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 14px;
}

.filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: rgba(17, 24, 39, 0.95);
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header h2 {
    margin: 0;
    color: #fff;
    font-size: 20px;
}

.close {
    color: #9CA3AF;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #fff;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #fff;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    color: #fff;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 12px;
    font-weight: bold;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .badges-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-container {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .badge-actions {
        flex-direction: column;
    }
    
    .badge-actions .btn {
        width: 100%;
    }
    
    .criteria-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .icon-grid, .color-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Icon and Color Selectors */
.icon-selector, .color-selector {
    position: relative;
}

.icon-grid, .color-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 8px;
    margin-bottom: 10px;
    max-height: 120px;
    overflow-y: auto;
    padding: 10px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.icon-option {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid transparent;
}

.icon-option:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: #3B82F6;
    transform: scale(1.1);
}

.icon-option.selected {
    background: rgba(59, 130, 246, 0.3);
    border-color: #3B82F6;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

.color-option {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
}

.color-option:hover {
    transform: scale(1.1);
    border-color: #fff;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

.color-option.selected {
    border-color: #fff;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
}

.color-option.selected::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.selected-icon, .selected-color {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    font-size: 24px;
    margin-top: 10px;
}

.selected-color {
    font-size: 0;
}

/* Criteria Container */
.criteria-container {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.criteria-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.criteria-row:last-child {
    margin-bottom: 0;
}
</style>

<script>
// Funciones para filtrado
function filterBadges() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    const badges = document.querySelectorAll('.badge-card');
    
    badges.forEach(badge => {
        const category = badge.dataset.category;
        const status = badge.dataset.status;
        const name = badge.dataset.name;
        
        let show = true;
        
        if (categoryFilter && category !== categoryFilter) show = false;
        if (statusFilter && status !== statusFilter) show = false;
        if (searchFilter && !name.includes(searchFilter)) show = false;
        
        badge.style.display = show ? 'block' : 'none';
    });
}

// Funciones para modal de badge
function openCreateBadgeModal() {
    document.getElementById('modalTitle').textContent = 'Crear Nuevo Badge';
    document.getElementById('badgeForm').reset();
    document.getElementById('badgeId').value = '';
    document.getElementById('isActiveGroup').style.display = 'none';
    document.getElementById('badgeModal').style.display = 'block';
    
    // Inicializar selectores
    selectIcon('🏆');
    selectColor('#3B82F6');
    updateCriteriaFields();
}

function editBadge(badgeId) {
    // Aquí cargarías los datos del badge para editar
    document.getElementById('modalTitle').textContent = 'Editar Badge';
    document.getElementById('badgeId').value = badgeId;
    document.getElementById('isActiveGroup').style.display = 'block';
    document.getElementById('badgeModal').style.display = 'block';
    
    // Cargar datos del badge (implementar AJAX)
    loadBadgeData(badgeId);
}

function closeBadgeModal() {
    document.getElementById('badgeModal').style.display = 'none';
}

function saveBadge(event) {
    event.preventDefault();
    
    // Construir JSON de criterios antes de enviar
    if (!buildCriteriaJSON()) {
        showNotification('Error al construir criterios', 'error');
        return;
    }
    
    const formData = new FormData(event.target);
    const badgeId = formData.get('badge_id');
    const url = badgeId ? '?route=gamification/updateBadge' : '?route=gamification/createBadge';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeBadgeModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al guardar el badge', 'error');
    });
}

// Funciones para otorgar badge
function awardBadge(badgeId) {
    document.getElementById('awardBadgeId').value = badgeId;
    document.getElementById('awardModal').style.display = 'block';
    
    // Cargar lista de usuarios (implementar AJAX)
    loadUsersList();
}

function closeAwardModal() {
    document.getElementById('awardModal').style.display = 'none';
}

function submitAwardBadge(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('?route=gamification/awardBadge', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAwardModal();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al otorgar el badge', 'error');
    });
}

function deleteBadge(badgeId) {
    if (confirm('¿Estás seguro de que quieres eliminar este badge? Esta acción no se puede deshacer.')) {
        const formData = new FormData();
        formData.append('badge_id', badgeId);
        
        fetch('?route=gamification/deleteBadge', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar el badge', 'error');
        });
    }
}

// Funciones auxiliares
function loadBadgeData(badgeId) {
    // Implementar carga de datos del badge via AJAX
    console.log('Cargando datos del badge:', badgeId);
}

function loadUsersList() {
    // Implementar carga de lista de usuarios via AJAX
    console.log('Cargando lista de usuarios');
}

function showNotification(message, type) {
    // Implementar sistema de notificaciones
    alert(message);
}

// Funciones para selectores de iconos y colores
function selectIcon(icon) {
    document.getElementById('badgeIcon').value = icon;
    document.getElementById('selectedIcon').textContent = icon;
    
    // Actualizar selección visual
    document.querySelectorAll('.icon-option').forEach(option => {
        option.classList.remove('selected');
    });
    document.querySelector(`[data-icon="${icon}"]`).classList.add('selected');
}

function selectColor(color) {
    document.getElementById('badgeColor').value = color;
    document.getElementById('selectedColor').style.background = color;
    
    // Actualizar selección visual
    document.querySelectorAll('.color-option').forEach(option => {
        option.classList.remove('selected');
    });
    document.querySelector(`[data-color="${color}"]`).classList.add('selected');
}

// Función para actualizar campos de criterios según el tipo
function updateCriteriaFields() {
    const criteriaType = document.getElementById('criteriaType').value;
    const additionalCriteria = document.getElementById('additionalCriteria');
    const criteriaValue = document.getElementById('criteriaValue');
    const criteriaThreshold = document.getElementById('criteriaThreshold');
    
    // Mostrar/ocultar criterios adicionales según el tipo
    if (criteriaType === 'quality_score' || criteriaType === 'speed_bonus') {
        additionalCriteria.style.display = 'grid';
        criteriaThreshold.style.display = 'block';
    } else {
        additionalCriteria.style.display = 'none';
    }
    
    // Ajustar valores por defecto según el tipo
    switch(criteriaType) {
        case 'tasks_completed':
            criteriaValue.value = '10';
            break;
        case 'projects_completed':
            criteriaValue.value = '5';
            break;
        case 'days_active':
            criteriaValue.value = '30';
            break;
        case 'collaborations':
            criteriaValue.value = '20';
            break;
        case 'quality_score':
            criteriaValue.value = '5';
            criteriaThreshold.value = '4.5';
            break;
        case 'speed_bonus':
            criteriaValue.value = '3';
            criteriaThreshold.value = '0.8';
            break;
        case 'leadership_days':
            criteriaValue.value = '30';
            break;
        case 'innovations':
            criteriaValue.value = '3';
            break;
    }
}

// Función para convertir criterios a JSON antes de enviar
function buildCriteriaJSON() {
    const criteriaType = document.getElementById('criteriaType').value;
    const criteriaValue = document.getElementById('criteriaValue').value;
    const criteriaTimeframe = document.getElementById('criteriaTimeframe').value;
    const criteriaCondition = document.getElementById('criteriaCondition').value;
    const criteriaThreshold = document.getElementById('criteriaThreshold').value;
    
    const criteria = {
        type: criteriaType,
        value: parseInt(criteriaValue),
        timeframe: criteriaTimeframe
    };
    
    if (criteriaCondition) {
        criteria.condition = criteriaCondition;
    }
    
    if (criteriaThreshold && criteriaThreshold > 0) {
        criteria.threshold = parseFloat(criteriaThreshold);
    }
    
    document.getElementById('requiredCriteria').value = JSON.stringify(criteria);
    return true;
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    const badgeModal = document.getElementById('badgeModal');
    const awardModal = document.getElementById('awardModal');
    
    if (event.target === badgeModal) {
        closeBadgeModal();
    }
    if (event.target === awardModal) {
        closeAwardModal();
    }
}
</script>

    </div>
</main>
</div>

<!-- JavaScript con rutas absolutas -->
<script src="<?php echo APP_URL; ?>assets/js/script.js"></script>
<script src="<?php echo APP_URL; ?>assets/js/gamification.js"></script>

</body>
</html> 