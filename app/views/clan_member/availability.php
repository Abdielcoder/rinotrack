<?php
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg"><i class="fas fa-star"></i></div>
                <span class="brand-text">Polaris</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <!-- <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li> -->
                <li class="nav-item active"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Agenda</span></a></li>
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
                            <a href="?route=clan_member" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease;">
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
                            <a href="?route=clan_member/availability" class="mobile-nav-link" style="display: flex; align-items: center; gap: 16px; padding: 20px; text-decoration: none; color: #6b7280; font-weight: 500; transition: all 0.2s ease; background: #f3f4f6; color: #1e3a8a;">
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
                <h1 class="welcome-title">Mi disponibilidad</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? ''); ?></p>
            </div>
            <div class="welcome-stats">
                <div class="quick-stat">
                    <div class="stat-icon success"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-text">
                        <span class="stat-value"><?php echo (int)($summary['task_count'] ?? 0); ?></span>
                        <span class="stat-label">Tareas próximas (15 días)</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Calendario de tareas personales (integrado como en líder) -->
        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                    <h3><i class="fas fa-calendar-alt icon-gradient"></i> Calendario de entregables</h3>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn btn-secondary" onclick="previousMonth()"><i class="fas fa-chevron-left"></i></button>
                        <strong id="currentMonth">Mes Año</strong>
                        <button class="btn btn-secondary" onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div class="weekday">Dom</div>
                        <div class="weekday">Lun</div>
                        <div class="weekday">Mar</div>
                        <div class="weekday">Mié</div>
                        <div class="weekday">Jue</div>
                        <div class="weekday">Vie</div>
                        <div class="weekday">Sáb</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>
            </div>
        </section>

        <!-- Modal tareas del día -->
        <div class="task-modal" id="taskModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Tareas del día</h3>
                    <button class="modal-close" onclick="closeTaskModal()">&times;</button>
                </div>
                <div class="task-list" id="modalTaskList"></div>
            </div>
        </div>

        <!-- Modal para crear tarea personal -->
        <div id="addPersonalTaskModal" class="modal-overlay">
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>
                        <i class="fas fa-plus-circle"></i> 
                        Agregar Tarea Personal
                    </h3>
                    <button class="modal-close" onclick="closeAddPersonalTaskModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="addPersonalTaskForm" class="modal-form">
                    <div class="form-group">
                        <label for="personalTaskName">Nombre de la Tarea *</label>
                        <input type="text" id="personalTaskName" name="task_name" required 
                               placeholder="Escribe el nombre de la tarea">
                    </div>
                    
                    <div class="form-group">
                        <label for="personalTaskDescription">Descripción</label>
                        <textarea id="personalTaskDescription" name="description" rows="3" 
                                  placeholder="Describe la tarea (opcional)"></textarea>
                    </div>
                    
                    <!-- Configuración de recurrencia -->
                    <div class="form-group">
                        <div class="checkbox-container">
                            <input type="checkbox" id="personalTaskIsRecurrent" name="is_recurrent" value="1" onchange="togglePersonalTaskRecurrenceFields()">
                            <label for="personalTaskIsRecurrent" class="checkbox-label">
                                <i class="fas fa-redo"></i>
                                Tarea Recurrente
                            </label>
                        </div>
                        <small class="field-help">Las tareas recurrentes se crearán automáticamente en el rango de fechas especificado</small>
                    </div>
                    
                    <div id="personalTaskRecurrenceFields" class="recurrence-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="personalTaskRecurrenceType">Tipo de Recurrencia *</label>
                                <div class="select-wrapper">
                                    <select id="personalTaskRecurrenceType" name="recurrence_type">
                                        <option value="">Seleccionar...</option>
                                        <option value="daily">Diaria</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly">Mensual</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="personalTaskRecurrenceStartDate">Fecha de Inicio *</label>
                                <div class="date-input-wrapper">
                                    <input type="date" id="personalTaskRecurrenceStartDate" name="recurrence_start_date">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="personalTaskRecurrenceEndDate">Fecha de Vigencia (Opcional)</label>
                                <div class="date-input-wrapper">
                                    <input type="date" id="personalTaskRecurrenceEndDate" name="recurrence_end_date">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <small class="field-help">Si no se especifica, la recurrencia será indefinida. Las tareas se crearán desde la fecha de inicio hasta esta fecha.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" id="personalTaskDueDateGroup">
                            <label for="personalTaskDueDate">Fecha de Vencimiento *</label>
                            <div class="date-input-wrapper">
                                <input type="date" id="personalTaskDueDate" name="due_date" required>
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        
                        <!-- Campo oculto para prioridad con valor fijo -->
                        <input type="hidden" name="priority" value="medium">
                    </div>
                    
                    <div class="form-group">
                        <label for="personalTaskStatus">Estado</label>
                        <select name="status" id="personalTaskStatus" required>
                            <option value="pending" selected>Pendiente</option>
                            <option value="in_progress">En Progreso</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeAddPersonalTaskModal()">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
.modern-dashboard{min-height:100vh;background:var(--bg-secondary)}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#fff;background:var(--primary-gradient)}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.user-menu{display:flex;align-items:center;gap:var(--spacing-md)}
.modern-avatar{position:relative;width:45px;height:45px;background:var(--primary-gradient);border-radius:999px;display:flex;align-items:center;justify-content:center;color:#fff}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid var(--bg-primary);border-radius:999px}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.content-card{background:var(--bg-primary);border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:var(--bg-tertiary);padding:var(--spacing-lg);text-align:left;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--bg-accent)}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid var(--bg-accent);color:var(--text-secondary)}
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.empty{padding:12px;color:#64748b}
/* Calendario inspirado en perfil líder */
.calendar-grid{display:grid;gap:12px}
.calendar-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.weekday{font-weight:700;color:var(--text-secondary);text-align:center}
.calendar-days{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.calendar-day{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:10px;min-height:90px;padding:8px;cursor:pointer;transition:.2s}
.calendar-day:hover{box-shadow:var(--shadow-md);transform:translateY(-1px)}
.calendar-day.other-month{opacity:.45}
.calendar-day.today{border-color: var(--primary-color)}
.day-number{font-weight:800;color:var(--text-primary)}
.day-tasks{font-size:.8rem;color:var(--text-secondary)}
.task-indicator{display:inline-block;width:8px;height:8px;border-radius:999px;margin-right:4px}
.task-indicator.completed{background:#10b981}
.task-indicator.pending{background:#f59e0b}
.task-indicator.in_progress{background:#3b82f6}
.task-indicator.overdue{background:#ef4444}
.task-modal{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:1000}
.task-modal.show{display:flex}
.task-modal .modal-content{background:#fff;border-radius:12px;max-width:640px;width:92%;box-shadow:var(--shadow-lg)}
.task-modal .modal-header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid var(--bg-accent)}
.task-modal .task-list{padding:14px 16px;max-height:60vh;overflow:auto}
.task-item{border:1px solid var(--bg-accent);border-radius:10px;padding:10px;margin-bottom:8px}
.task-status{padding:2px 8px;border-radius:999px;font-size:.75rem}
.task-status.completed{background:rgba(16,185,129,.15);color:#065f46}
.task-status.pending{background:rgba(245,158,11,.15);color:#7c2d12}
.task-status.in_progress{background:rgba(59,130,246,.15);color:#1e3a8a}
.task-status.overdue{background:rgba(239,68,68,.15);color:#7f1d1d}
/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .nav-menu {
        display: none !important;
    }
    
    .hamburger-menu {
        display: flex !important;
    }
    
    .nav-container {
        padding: 0 var(--spacing-md);
    }
    
    .main-content {
        padding: var(--spacing-lg) var(--spacing-md);
    }
    
    .welcome-header {
        flex-direction: column;
        gap: var(--spacing-lg);
        text-align: center;
        padding: var(--spacing-lg);
    }
    
    .welcome-title {
        font-size: 1.8rem;
    }
    
    .welcome-subtitle {
        font-size: 1rem;
    }
    
    .welcome-stats {
        width: 100%;
        justify-content: center;
    }
    
    .quick-stat {
        flex-direction: column;
        text-align: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-lg);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-text {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.9rem;
    }
    
    .content-card {
        padding: var(--spacing-lg);
    }
    
    .card-header {
        flex-direction: column;
        gap: var(--spacing-md);
        text-align: center;
    }
    
    .card-header h3 {
        font-size: 1.3rem;
    }
    
    .calendar-grid {
        gap: 8px;
    }
    
    .calendar-weekdays {
        gap: 4px;
    }
    
    .weekday {
        font-size: 0.8rem;
        padding: 8px 4px;
    }
    
    .calendar-days {
        gap: 4px;
    }
    
    .calendar-day {
        min-height: 70px;
        padding: 6px;
        font-size: 0.9rem;
    }
    
    .task-modal .modal-content {
        width: 95%;
        max-width: 500px;
        margin: 20px auto;
    }
    
    .modal-header h3 {
        font-size: 1.2rem;
    }
    
    .task-list {
        max-height: 60vh;
    }
}

@media (max-width: 480px) {
    .nav-container {
        padding: 0 var(--spacing-sm);
    }
    
    .main-content {
        padding: var(--spacing-md) var(--spacing-sm);
    }
    
    .welcome-header {
        padding: var(--spacing-md);
    }
    
    .welcome-title {
        font-size: 1.6rem;
    }
    
    .quick-stat {
        padding: var(--spacing-md);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .stat-value {
        font-size: 1.3rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .content-card {
        padding: var(--spacing-md);
    }
    
    .card-header h3 {
        font-size: 1.1rem;
    }
    
    .calendar-grid {
        gap: 6px;
    }
    
    .weekday {
        font-size: 0.7rem;
        padding: 6px 2px;
    }
    
    .calendar-day {
        min-height: 60px;
        padding: 4px;
        font-size: 0.8rem;
    }
    
    .btn {
        padding: var(--spacing-sm);
        font-size: 0.9rem;
    }
    
    .task-modal .modal-content {
        width: 98%;
        margin: 10px auto;
    }
    
    .modal-header h3 {
        font-size: 1.1rem;
    }
    
    .task-list {
        max-height: 70vh;
    }
}

/* === MENÚ HAMBURGUESA RESPONSIVE === */
.hamburger-menu {
    display: none;
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
}

.hamburger-line {
    width: 100%;
    height: 3px;
    background: #1e3a8a;
    border-radius: 2px;
    transition: all 0.3s ease;
    display: block;
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

/* Estilos para el botón de agregar tarea en el modal */
.modal-add-task-section {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
    text-align: center;
}

.btn-add-task-modal {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
}

.btn-add-task-modal:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
}

.btn-add-task-modal i {
    font-size: 1rem;
}

/* Estilos para el modal de crear tarea personal */
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
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease;
}

.modal-large {
    max-width: 600px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    color: #1e3a8a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #1e3a8a;
}

.modal-form {
    padding: 20px;
}

.modal-form .form-group {
    margin-bottom: 20px;
}

.modal-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1e3a8a;
}

.modal-form input,
.modal-form textarea,
.modal-form select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #ffffff;
    box-sizing: border-box;
}

.modal-form input:focus,
.modal-form textarea:focus,
.modal-form select:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.modal-form textarea {
    resize: vertical;
    min-height: 80px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-secondary:hover {
    background: #e5e7eb;
    color: #4b5563;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
}

/* Estilos para campos de recurrencia */
.checkbox-container {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #ffffff;
    transition: all 0.2s ease;
    cursor: pointer;
}

.checkbox-container:hover {
    border-color: #1e3a8a;
    background: #f8fafc;
}

.checkbox-container input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #1e3a8a;
    cursor: pointer;
    margin: 0;
}

.checkbox-label i {
    font-size: 1rem;
    color: #667eea;
}

.recurrence-fields {
    margin-top: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.field-help {
    display: block;
    margin-top: 5px;
    font-size: 0.85rem;
    color: #6b7280;
    font-style: italic;
}

/* Wrappers para selects y fechas */
.select-wrapper,
.date-input-wrapper {
    position: relative;
}

.select-wrapper i,
.date-input-wrapper i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
}

.select-wrapper select {
    padding-right: 40px;
    appearance: none;
}

.date-input-wrapper input {
    padding-right: 40px;
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

/* Responsive para el modal */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Reutilizar motor del calendario del líder
let tasksData = [];
let currentDate = new Date();

function setTasksData(data){ tasksData = data; if (document.getElementById('calendarDays')) generateCalendar(); }

function generateCalendar(){
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  const mEl = document.getElementById('currentMonth'); if (mEl) mEl.textContent = monthNames[month] + ' ' + year;
  const first = new Date(year, month, 1); const start = new Date(first); start.setDate(start.getDate() - first.getDay());
  const daysEl = document.getElementById('calendarDays'); if (!daysEl) return; daysEl.innerHTML = '';
  const today = new Date(); today.setHours(0,0,0,0);
  for (let i=0;i<42;i++){
    const d = new Date(start); d.setDate(start.getDate()+i);
    const day = document.createElement('div'); day.className='calendar-day';
    if (d.getMonth()!==month) day.classList.add('other-month');
    if (d.getTime()===today.getTime()) day.classList.add('today');
    const ds = d.toISOString().split('T')[0];
    const dayTasks = tasksData.filter(t=>t && t.due_date && t.due_date===ds);
    if (dayTasks.length>0) day.classList.add('has-tasks');
    const num = document.createElement('div'); num.className='day-number'; num.textContent=d.getDate();
    const info = document.createElement('div'); info.className='day-tasks'; info.textContent = dayTasks.length+' tarea'+(dayTasks.length!==1?'s':'');
    day.appendChild(num); day.appendChild(info);
    if (dayTasks.length>0){
      const indicators=document.createElement('div');
      const counts={}; dayTasks.forEach(td=>{ const st=td.status; counts[st]=(counts[st]||0)+1; });
      Object.keys(counts).forEach(st=>{ const b=document.createElement('span'); b.className='task-indicator '+st; b.title=counts[st]+' '+st; indicators.appendChild(b); });
      day.appendChild(indicators);
    }
    day.addEventListener('click',()=>showTasksForDate(d, dayTasks));
    daysEl.appendChild(day);
  }
}
function previousMonth(){ currentDate.setMonth(currentDate.getMonth()-1); generateCalendar(); }
function nextMonth(){ currentDate.setMonth(currentDate.getMonth()+1); generateCalendar(); }
function showTasksForDate(date, list){
  const modal=document.getElementById('taskModal'); const title=document.getElementById('modalTitle'); const body=document.getElementById('modalTaskList');
  if(!modal||!title||!body) return; body.innerHTML='';
  title.textContent = 'Tareas del '+ date.toLocaleDateString('es-ES',{weekday:'long', year:'numeric', month:'long', day:'numeric'});
  if(!list||list.length===0){ body.innerHTML='<p>No hay tareas programadas para este día.</p>'; } else {
    list.forEach(t=>{
      const overdue = t.due_date && (new Date(t.due_date) < new Date()) && t.status!=='completed';
      const st = overdue? 'overdue' : (t.status||'');
      const el=document.createElement('div'); el.className='task-item';
      el.innerHTML = `<div class=\"task-header\"><h4 class=\"task-title\">${(t.task_name||'')}</h4><span class=\"task-status ${st}\">${overdue?'Vencida':(t.status||'')}</span></div>
      <div class=\"task-details\"><div class=\"task-project\"><i class=\"fas fa-folder\"></i> ${(t.project_name||'')}</div>${t.description?`<div style='margin-top:.5rem'><i class='fas fa-align-left'></i> ${t.description}</div>`:''}</div>`;
      body.appendChild(el);
    });
  }
  
  // Agregar botón "Agregar tarea" al final del modal
  const addTaskButton = document.createElement('div');
  addTaskButton.className = 'modal-add-task-section';
  addTaskButton.innerHTML = `
      <button class="btn-add-task-modal" onclick="openAddPersonalTaskModal('${date.toISOString().split('T')[0]}')">
          <i class="fas fa-plus"></i>
          Agregar tarea personal
      </button>
  `;
  body.appendChild(addTaskButton);
  modal.classList.add('show');
}
function closeTaskModal(){ document.getElementById('taskModal')?.classList.remove('show'); }

// Función para abrir modal de agregar tarea personal desde el calendario
function openAddPersonalTaskModal(selectedDate) {
    // Cerrar el modal actual de tareas
    closeTaskModal();
    
    // Crear el modal si no existe
    let modal = document.getElementById('addPersonalTaskModal');
    if (!modal) {
        console.error('Modal de agregar tarea personal no encontrado');
        return;
    }
    
    // Establecer la fecha preseleccionada
    const dateInput = document.getElementById('personalTaskDueDate');
    const recurrenceStartInput = document.getElementById('personalTaskRecurrenceStartDate');
    
    if (selectedDate) {
        // Establecer en fecha límite (para tareas normales)
        if (dateInput) {
            dateInput.value = selectedDate;
        }
        
        // También establecer en fecha de inicio de recurrencia (para tareas recurrentes)
        if (recurrenceStartInput) {
            recurrenceStartInput.value = selectedDate;
        }
    }
    
    // Mostrar el modal
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

// Función para cerrar el modal de agregar tarea personal
function closeAddPersonalTaskModal() {
    const modal = document.getElementById('addPersonalTaskModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        
        // Limpiar formulario
        const form = document.getElementById('addPersonalTaskForm');
        if (form) {
            form.reset();
        }
        
        // Ocultar campos de recurrencia
        const recurrenceFields = document.getElementById('personalTaskRecurrenceFields');
        if (recurrenceFields) {
            recurrenceFields.style.display = 'none';
        }
        
        // Mostrar campo de fecha límite
        const dueDateGroup = document.getElementById('personalTaskDueDateGroup');
        if (dueDateGroup) {
            dueDateGroup.style.display = 'block';
        }
    }
}

// Función para mostrar/ocultar campos de recurrencia en el modal de creación
function togglePersonalTaskRecurrenceFields() {
    const checkbox = document.getElementById('personalTaskIsRecurrent');
    const fields = document.getElementById('personalTaskRecurrenceFields');
    const dueDateField = document.getElementById('personalTaskDueDate');
    const recurrenceStartField = document.getElementById('personalTaskRecurrenceStartDate');
    const dueDateGroup = document.getElementById('personalTaskDueDateGroup');
    
    if (checkbox.checked) {
        // Mostrar campos de recurrencia
        fields.style.display = 'block';
        
        // Ocultar campo de fecha límite cuando es recurrente
        if (dueDateGroup) {
            dueDateGroup.style.display = 'none';
        }
        
        // Quitar required y limpiar valor del campo de fecha límite
        if (dueDateField) {
            dueDateField.required = false;
            dueDateField.removeAttribute('required');
            // NO limpiar el valor, mantenerlo para referencia
        }
        
        // Si la fecha de inicio de recurrencia está vacía, copiar la fecha límite
        if (recurrenceStartField && !recurrenceStartField.value && dueDateField && dueDateField.value) {
            recurrenceStartField.value = dueDateField.value;
        }
        
        // Hacer requeridos los campos de recurrencia
        document.getElementById('personalTaskRecurrenceType').required = true;
        document.getElementById('personalTaskRecurrenceStartDate').required = true;
    } else {
        // Ocultar campos de recurrencia
        fields.style.display = 'none';
        
        // Mostrar campo de fecha límite normal
        if (dueDateGroup) {
            dueDateGroup.style.display = 'block';
        }
        
        // Restaurar required
        if (dueDateField) {
            dueDateField.required = true;
            dueDateField.setAttribute('required', 'required');
        }
        
        // Quitar requerimiento de campos de recurrencia
        document.getElementById('personalTaskRecurrenceType').required = false;
        document.getElementById('personalTaskRecurrenceStartDate').required = false;
        
        // Limpiar valores de recurrencia
        document.getElementById('personalTaskRecurrenceType').value = '';
        document.getElementById('personalTaskRecurrenceStartDate').value = '';
        document.getElementById('personalTaskRecurrenceEndDate').value = '';
    }
}

// Función para crear tarea personal
function createPersonalTask() {
    const form = document.getElementById('addPersonalTaskForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales para tarea personal
    formData.append('route', 'clan_member/create-personal-task');
    formData.append('user_id', '<?php echo $user['user_id'] ?? 0; ?>');
    
    // Mostrar estado de carga
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    submitBtn.disabled = true;
    
    fetch('?route=clan_member/create-personal-task', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Tarea personal creada exitosamente', 'success');
            closeAddPersonalTaskModal();
            
            // Recargar la página para mostrar la nueva tarea
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error al crear la tarea', 'error');
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        showNotification('Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
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

// Event listeners para el modal
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer click fuera de él
    const addPersonalTaskModal = document.getElementById('addPersonalTaskModal');
    
    if (addPersonalTaskModal) {
        addPersonalTaskModal.addEventListener('click', function(e) {
            if (e.target === addPersonalTaskModal) {
                closeAddPersonalTaskModal();
            }
        });
        
        // Manejar envío del formulario
        const form = document.getElementById('addPersonalTaskForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                createPersonalTask();
            });
        }
    }
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddPersonalTaskModal();
        }
    });
});

document.addEventListener('DOMContentLoaded',()=>{ window.calendarTasksData = <?= json_encode($tasks ?? []) ?>; setTasksData(calendarTasksData); });
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>

<script>
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
document.addEventListener('DOMContentLoaded', function() {
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            toggleMobileMenu();
        });
    });
    
    // Cerrar menú móvil al hacer click fuera
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.addEventListener('click', function(e) {
        if (e.target === mobileMenu) {
            toggleMobileMenu();
        }
    });
});
</script>


