<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="kpi-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>KPI Dashboard</h1>
                <span class="subtitle">Indicadores de rendimiento</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=admin" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Admin
                </a>
                <a href="?route=kpi/quarters" class="btn-minimal">
                    <i class="fas fa-calendar-alt"></i>
                    Trimestres
                </a>
                <a href="?route=kpi/projects" class="btn-minimal primary">
                    <i class="fas fa-bullseye"></i>
                    Asignar
                </a>
            </div>
        </div>
        
        <!-- Período Actual Minimalista -->
        <?php if ($currentKPI): ?>
            <div class="period-minimal">
                <div class="period-info">
                    <span class="period-name"><?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?></span>
                    <?php 
                    $percentage = $currentKPI['total_points'] > 0 ? 
                        round((($currentKPI['assigned_points'] ?? 0) / $currentKPI['total_points']) * 100, 1) : 0;
                    ?>
                    <div class="period-progress">
                        <span class="progress-label"><?php echo $percentage; ?>% asignado</span>
                        <div class="progress-minimal">
                            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-minimal">
                <span>⚠️ No hay trimestre KPI activo</span>
                <a href="?route=kpi/quarters" class="link-minimal">Crear trimestre</a>
            </div>
        <?php endif; ?>
    </header>

    <?php if ($currentKPI): ?>
        <!-- Camino Tipo Serpiente -->
        <section class="snake-path-section">
            <div class="snake-path-container">
                <div class="snake-path-header">
                    <h3>Progreso del Trimestre</h3>
                    <div class="quarter-info">
                        <span class="quarter-label"><?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?></span>
                        <span class="quarter-period">
                            <?php
                            $quarterMonths = [
                                'Q1' => ['ENE', 'FEB', 'MAR'],
                                'Q2' => ['ABR', 'MAY', 'JUN'],
                                'Q3' => ['JUL', 'AGO', 'SEP'],
                                'Q4' => ['OCT', 'NOV', 'DIC']
                            ];
                            $months = $quarterMonths[$currentKPI['quarter']] ?? ['MES1', 'MES2', 'MES3'];
                            echo implode(' - ', $months);
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="snake-path-board">
                    <div class="path-grid" id="snakePathGrid">
                        <!-- El camino se generará dinámicamente con JavaScript -->
                    </div>
                    
                    <div class="clan-markers" id="clanMarkers">
                        <!-- Los marcadores de clanes se generarán dinámicamente -->
                    </div>
                </div>
                
                <div class="snake-path-legend">
                    <div class="legend-title">Clanes Participantes</div>
                    <div class="legend-items" id="clanLegend">
                        <!-- La leyenda se generará dinámicamente -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Estadísticas Minimalistas -->
        <div class="content-minimal">
            <section class="stats-minimal">
                <div class="stats-row">
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['total_points']); ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['assigned_points']); ?></div>
                        <div class="stat-label">Asignados</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['available_points']); ?></div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                    
                    <div class="stat-minimal">
                        <div class="stat-value"><?php echo number_format($stats['completed_points'] ?? 0); ?></div>
                        <div class="stat-label">Completados</div>
                    </div>
                </div>
            </section>
            
            <!-- Ranking Minimalista -->
            <section class="ranking-minimal">
                <h3>Ranking de Clanes</h3>
                
                <?php if (!empty($clanRanking)): ?>
                    <div class="ranking-list">
                        <?php foreach (array_slice($clanRanking, 0, 5) as $index => $clan): ?>
                            <div class="rank-item">
                                <div class="rank-position">
                                    <span class="rank-number"><?php echo $index + 1; ?></span>
                                    <?php if ($index < 3): ?>
                                        <span class="medal">
                                            <?php echo $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="clan-info">
                                    <div class="clan-name"><?php echo Utils::escape($clan['clan_name']); ?></div>
                                    <?php if (!empty($clan['clan_departamento'])): ?>
                                        <div class="clan-dept"><?php echo Utils::escape($clan['clan_departamento']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="clan-score">
                                    <span class="points"><?php echo number_format($clan['earned_points'] ?? 0); ?></span>
                                    <span class="label">pts</span>
                                </div>
                                
                                <div class="clan-progress">
                                    <?php 
                                    $efficiency = $clan['total_points'] > 0 ? 
                                        round((($clan['earned_points'] ?? 0) / $clan['total_points']) * 100, 1) : 0;
                                    ?>
                                    <div class="progress-bar-small">
                                        <div class="progress-fill-small" style="width: <?php echo min($efficiency, 100); ?>%"></div>
                                    </div>
                                    <span class="progress-percent"><?php echo $efficiency; ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-minimal">
                        <span>📊 No hay datos disponibles</span>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
</div>

<!-- Script para pasar datos del camino tipo serpiente al JavaScript -->
<script>
window.snakePathData = <?= json_encode($snakePathData ?? []) ?>;
console.log('Datos pasados desde PHP:', window.snakePathData);
console.log('Clanes disponibles:', window.snakePathData?.clans_data);
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para KPIs
$additionalCSS = [
    APP_URL . 'assets/css/kpi.css'
];

// JavaScript adicional para KPIs  
$additionalJS = [
    APP_URL . 'assets/js/kpi-dashboard.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?>