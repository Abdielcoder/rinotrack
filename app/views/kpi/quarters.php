<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="kpi-quarters minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Trimestres KPI</h1>
                <span class="subtitle">Gestión de períodos de evaluación</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=admin" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Admin
                </a>
                <a href="?route=kpi/dashboard" class="btn-minimal">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <button class="btn-minimal primary" onclick="openCreateQuarterModal()">
                    <i class="fas fa-plus"></i>
                    Nuevo Trimestre
                </button>
            </div>
        </div>
        
        <!-- Período Actual Minimalista -->
        <?php if ($currentKPI): ?>
            <div class="period-minimal">
                <div class="period-info">
                    <span class="period-name">
                        ✅ <?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?> (Activo)
                    </span>
                    <?php 
                    $usage = $currentKPI['total_points'] > 0 ? 
                        round((($currentKPI['assigned_points'] ?? 0) / $currentKPI['total_points']) * 100, 1) : 0;
                    ?>
                    <div class="period-progress">
                        <span class="progress-label"><?php echo $usage; ?>% utilizado</span>
                        <div class="progress-minimal">
                            <div class="progress-fill" style="width: <?php echo $usage; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-minimal">
                <span>⚠️ No hay trimestre KPI activo</span>
                <button class="link-minimal" onclick="openCreateQuarterModal()">Crear trimestre</button>
            </div>
        <?php endif; ?>
    </header>

    <!-- Lista de Trimestres Minimalista -->
    <main class="content-minimal">
        <section class="quarters-minimal">
            <div class="section-header-minimal">
                <h3>Todos los Trimestres</h3>
                <select class="filter-minimal" onchange="filterByYear(this.value)">
                    <option value="">Todos los años</option>
                    <?php 
                    $years = array_unique(array_column($quarters, 'year'));
                    rsort($years);
                    foreach ($years as $year): 
                    ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (!empty($quarters)): ?>
                <div class="quarters-list">
                    <?php foreach ($quarters as $index => $quarter): ?>
                        <div class="quarter-item <?php echo $quarter['is_active'] ? 'active' : ''; ?>" 
                             data-year="<?php echo $quarter['year']; ?>"
                             data-quarter="<?php echo $quarter['quarter']; ?>">
                            
                            <!-- Quarter Header Minimalista -->
                            <div class="quarter-header-minimal">
                                <div class="quarter-name">
                                    <?php if ($quarter['is_active']): ?>
                                        <span class="status-indicator">🟢</span>
                                    <?php else: ?>
                                        <span class="status-indicator">⚪</span>
                                    <?php endif; ?>
                                    <span class="quarter-title"><?php echo htmlspecialchars($quarter['quarter'] . ' ' . $quarter['year']); ?></span>
                                    <?php if ($quarter['is_active']): ?>
                                        <span class="active-badge">Activo</span>
                                    <?php endif; ?>
                                </div>
                                <div class="quarter-date">
                                    <?php echo date('d/m/Y', strtotime($quarter['created_at'])); ?>
                                </div>
                            </div>
                            
                            <!-- Stats Minimalistas -->
                            <div class="quarter-stats-minimal">
                                <div class="stat-small">
                                    <span class="stat-value"><?php echo number_format($quarter['total_points']); ?></span>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-small">
                                    <span class="stat-value"><?php echo number_format($quarter['assigned_points'] ?? 0); ?></span>
                                    <span class="stat-label">Asignados</span>
                                </div>
                                <div class="stat-small">
                                    <span class="stat-value"><?php echo number_format(($quarter['total_points'] - ($quarter['assigned_points'] ?? 0))); ?></span>
                                    <span class="stat-label">Disponibles</span>
                                </div>
                            </div>
                            
                            <!-- Progress Minimalista -->
                            <div class="quarter-progress-minimal">
                                <?php 
                                $progress = $quarter['total_points'] > 0 ? 
                                    round((($quarter['assigned_points'] ?? 0) / $quarter['total_points']) * 100, 1) : 0;
                                ?>
                                <div class="progress-bar-small">
                                    <div class="progress-fill-small" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <span class="progress-text"><?php echo $progress; ?>%</span>
                            </div>
                            
                            <!-- Actions Minimalistas -->
                            <div class="quarter-actions-minimal">
                                <?php if (!$quarter['is_active']): ?>
                                    <button class="btn-action activate" 
                                            onclick="activateQuarter(<?php echo $quarter['kpi_quarter_id']; ?>)"
                                            title="Activar">
                                        ▶️
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn-action" 
                                        onclick="viewQuarterDetails(<?php echo $quarter['kpi_quarter_id']; ?>)"
                                        title="Ver detalles">
                                    👁️
                                </button>
                                
                                <button class="btn-action" 
                                        onclick="editQuarter(<?php echo $quarter['kpi_quarter_id']; ?>)"
                                        title="Editar">
                                    ✏️
                                </button>
                                
                                <?php if (!$quarter['is_active'] && $quarter['projects_count'] == 0): ?>
                                <button class="btn-action delete" 
                                        onclick="deleteQuarter(<?php echo $quarter['kpi_quarter_id']; ?>)"
                                        title="Eliminar">
                                    🗑️
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-minimal">
                    <span>📅 No hay trimestres creados</span>
                    <button class="btn-minimal primary" onclick="openCreateQuarterModal()">
                        <i class="fas fa-plus"></i>
                        Crear Primer Trimestre
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Modal para crear/editar trimestre -->
<div id="quarterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="quarterModalTitle">Crear Nuevo Trimestre</h3>
            <button class="modal-close" onclick="closeQuarterModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="quarterForm" class="modal-form">
            <input type="hidden" id="quarterId" name="quarterId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="quarterYear">
                        <i class="fas fa-calendar"></i>
                        Año
                    </label>
                    <select id="quarterYear" name="year" required>
                        <?php 
                        $currentYear = date('Y');
                        for ($year = $currentYear - 1; $year <= $currentYear + 2; $year++): 
                        ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == $currentYear ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quarterPeriod">
                        <i class="fas fa-calendar-check"></i>
                        Trimestre
                    </label>
                    <select id="quarterPeriod" name="quarter" required>
                        <option value="Q1">Q1 - Primer Trimestre</option>
                        <option value="Q2">Q2 - Segundo Trimestre</option>
                        <option value="Q3">Q3 - Tercer Trimestre</option>
                        <option value="Q4">Q4 - Cuarto Trimestre</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="totalPoints">
                    <i class="fas fa-bullseye"></i>
                    Puntos Totales
                </label>
                <input type="number" id="totalPoints" name="total_points" 
                       value="1000" min="100" max="10000" step="100" required 
                       placeholder="Ej: 1000">
                <small class="form-help">Cantidad total de puntos disponibles para asignar en este trimestre</small>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="activateImmediately" name="activate_immediately">
                    <span class="checkmark"></span>
                    Activar inmediatamente después de crear
                </label>
                <small class="form-help">Si está marcado, este trimestre se activará automáticamente</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeQuarterModal()">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="quarterSubmitBtn">
                    <span id="quarterSubmitText">Crear Trimestre</span>
                    <span id="quarterSubmitLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para detalles del trimestre -->
<div id="quarterDetailsModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 id="detailsModalTitle">Detalles del Trimestre</h3>
            <button class="modal-close" onclick="closeQuarterDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="quarter-details-content">
            <!-- Se llenará dinámicamente -->
        </div>
    </div>
</div>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para KPIs
$additionalCSS = [
    APP_URL . 'assets/css/kpi.css'
];

// JavaScript adicional para KPIs  
$additionalJS = [
    APP_URL . 'assets/js/kpi-quarters.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?>