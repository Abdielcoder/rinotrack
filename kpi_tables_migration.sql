-- =============================================
-- MIGRACIÓN: Sistema KPI - ACTUALIZACIÓN COMPATIBLE
-- =============================================
-- Este script actualiza el sistema KPI existente sin perder datos
-- ✅ Compatible con estructura existente en rinotrack.sql

-- =============================================
-- PASO 1: VERIFICAR ESTADO ACTUAL Y CREAR TABLAS SI NO EXISTEN
-- =============================================

SELECT '🔍 VERIFICANDO SISTEMA KPI EXISTENTE...' AS estado;

-- Mostrar tablas KPI existentes
SELECT 'Tablas encontradas que contienen KPI:' AS info;
SELECT TABLE_NAME, CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND (TABLE_NAME LIKE '%KPI%' OR TABLE_NAME LIKE '%kpi%')
ORDER BY TABLE_NAME;

-- Verificar si KPI_Quarters existe
SET @kpi_quarters_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'KPI_Quarters'
);

SELECT CASE 
    WHEN @kpi_quarters_exists > 0 THEN '✅ KPI_Quarters existe'
    ELSE '❌ KPI_Quarters NO existe - se creará'
END AS estado_kpi_quarters;

-- Crear tabla KPI_Quarters si no existe
SET @sql_create_quarters = IF(@kpi_quarters_exists = 0, 
    "CREATE TABLE `KPI_Quarters` (
      `kpi_quarter_id` int NOT NULL AUTO_INCREMENT,
      `quarter` enum('Q1','Q2','Q3','Q4') NOT NULL,
      `year` int NOT NULL,
      `total_points` int NOT NULL DEFAULT '1000',
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `status` varchar(50) DEFAULT 'planning',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`kpi_quarter_id`),
      UNIQUE KEY `unique_quarter_year` (`quarter`,`year`),
      KEY `idx_is_active` (`is_active`),
      KEY `idx_year_quarter` (`year`,`quarter`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    "SELECT '✅ KPI_Quarters ya existe' AS resultado"
);

PREPARE stmt_create_quarters FROM @sql_create_quarters;
EXECUTE stmt_create_quarters;
DEALLOCATE PREPARE stmt_create_quarters;

-- Insertar trimestre actual si la tabla estaba vacía o se acaba de crear
INSERT IGNORE INTO KPI_Quarters (quarter, year, total_points, is_active, status, created_at) 
VALUES (
    CONCAT('Q', CEIL(MONTH(NOW()) / 3)), 
    YEAR(NOW()), 
    3000, 
    1, 
    'active', 
    NOW()
);

-- Mostrar trimestres KPI después de la creación
SELECT 'Trimestres KPI disponibles:' AS info;
SELECT 
    kq.kpi_quarter_id,
    kq.quarter,
    kq.year,
    kq.total_points,
    kq.is_active,
    kq.status,
    IFNULL(COUNT(p.project_id), 0) as proyectos_asignados
FROM KPI_Quarters kq
LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
GROUP BY kq.kpi_quarter_id, kq.quarter, kq.year, kq.total_points, kq.is_active, kq.status
ORDER BY kq.year DESC, kq.quarter DESC;

-- =============================================
-- PASO 2: CREAR/ACTUALIZAR TABLA CLAN_KPIS
-- =============================================

-- Verificar si Clan_KPIs existe
SET @clan_kpis_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Clan_KPIs'
);

SELECT CASE 
    WHEN @clan_kpis_exists > 0 THEN '✅ Clan_KPIs existe'
    ELSE '❌ Clan_KPIs NO existe - se creará'
END AS estado_clan_kpis;

-- Crear tabla Clan_KPIs si no existe
SET @sql_create_clan_kpis = IF(@clan_kpis_exists = 0, 
    "CREATE TABLE `Clan_KPIs` (
      `kpi_id` int NOT NULL AUTO_INCREMENT,
      `clan_id` int NOT NULL,
      `kpi_quarter_id` int NOT NULL,
      `year` int NOT NULL,
      `quarter` varchar(10) NOT NULL,
      `total_points` int DEFAULT '1000',
      `assigned_points` int DEFAULT '0',
      `status` enum('planning','active','completed','closed') DEFAULT 'planning',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`kpi_id`),
      UNIQUE KEY `unique_clan_quarter` (`clan_id`,`kpi_quarter_id`),
      KEY `idx_clan_id` (`clan_id`),
      KEY `idx_kpi_quarter_id` (`kpi_quarter_id`),
      KEY `idx_year_quarter` (`year`,`quarter`),
      CONSTRAINT `clan_kpis_clan_fk` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
      CONSTRAINT `clan_kpis_quarter_fk` FOREIGN KEY (`kpi_quarter_id`) REFERENCES `KPI_Quarters` (`kpi_quarter_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
    "SELECT '✅ Clan_KPIs ya existe' AS resultado"
);

PREPARE stmt_create_clan_kpis FROM @sql_create_clan_kpis;
EXECUTE stmt_create_clan_kpis;
DEALLOCATE PREPARE stmt_create_clan_kpis;

-- Si Clan_KPIs existía pero no tenía kpi_quarter_id, agregarlo
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Clan_KPIs' 
      AND COLUMN_NAME = 'kpi_quarter_id'
);

SET @sql_add_col = IF(@col_exists = 0 AND @clan_kpis_exists > 0, 
    'ALTER TABLE Clan_KPIs ADD COLUMN kpi_quarter_id int NOT NULL AFTER clan_id, ADD KEY idx_kpi_quarter_id (kpi_quarter_id), ADD CONSTRAINT clan_kpis_quarter_fk FOREIGN KEY (kpi_quarter_id) REFERENCES KPI_Quarters(kpi_quarter_id) ON DELETE CASCADE',
    'SELECT "✅ Clan_KPIs ya tiene columna kpi_quarter_id" AS resultado'
);

PREPARE stmt_add_col FROM @sql_add_col;
EXECUTE stmt_add_col;
DEALLOCATE PREPARE stmt_add_col;

-- =============================================
-- PASO 3: AGREGAR COLUMNAS KPI A PROJECTS SI NO EXISTEN
-- =============================================

SELECT '✅ Verificando/actualizando tabla Projects...' AS info;

-- Verificar si Projects tiene las columnas KPI
SET @has_kpi_quarter_id = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Projects' 
      AND COLUMN_NAME = 'kpi_quarter_id'
);

SET @has_kpi_points = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Projects' 
      AND COLUMN_NAME = 'kpi_points'
);

SET @has_distribution_mode = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Projects' 
      AND COLUMN_NAME = 'task_distribution_mode'
);

-- Agregar kpi_quarter_id si no existe
SET @sql_kpi_quarter = IF(@has_kpi_quarter_id = 0, 
    'ALTER TABLE Projects ADD COLUMN kpi_quarter_id int NULL COMMENT "ID del trimestre KPI asignado"',
    'SELECT "✅ Projects ya tiene columna kpi_quarter_id" AS resultado'
);

PREPARE stmt_kpi_quarter FROM @sql_kpi_quarter;
EXECUTE stmt_kpi_quarter;
DEALLOCATE PREPARE stmt_kpi_quarter;

-- Agregar kpi_points si no existe
SET @sql_kpi_points = IF(@has_kpi_points = 0, 
    'ALTER TABLE Projects ADD COLUMN kpi_points int NOT NULL DEFAULT 0 COMMENT "Puntos KPI asignados al proyecto"',
    'SELECT "✅ Projects ya tiene columna kpi_points" AS resultado'
);

PREPARE stmt_kpi_points FROM @sql_kpi_points;
EXECUTE stmt_kpi_points;
DEALLOCATE PREPARE stmt_kpi_points;

-- Agregar task_distribution_mode si no existe
SET @sql_distribution = IF(@has_distribution_mode = 0, 
    'ALTER TABLE Projects ADD COLUMN task_distribution_mode enum("automatic","percentage") DEFAULT "automatic" COMMENT "Modalidad de distribución de puntos"',
    'SELECT "✅ Projects ya tiene columna task_distribution_mode" AS resultado'
);

PREPARE stmt_distribution FROM @sql_distribution;
EXECUTE stmt_distribution;
DEALLOCATE PREPARE stmt_distribution;

-- Agregar índice para kpi_quarter_id si no existe
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Projects' 
      AND INDEX_NAME = 'idx_kpi_quarter'
);

SET @sql_idx = IF(@idx_exists = 0, 
    'ALTER TABLE Projects ADD KEY idx_kpi_quarter (kpi_quarter_id)',
    'SELECT "✅ Índice idx_kpi_quarter ya existe en Projects" AS resultado'
);

PREPARE stmt_idx FROM @sql_idx;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;

-- Agregar restricción de clave foránea si no existe
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'Projects' 
      AND CONSTRAINT_NAME = 'projects_kpi_quarter_fk'
);

SET @sql_fk = IF(@fk_exists = 0, 
    'ALTER TABLE Projects ADD CONSTRAINT projects_kpi_quarter_fk FOREIGN KEY (kpi_quarter_id) REFERENCES KPI_Quarters(kpi_quarter_id) ON DELETE SET NULL',
    'SELECT "✅ Relación Projects -> KPI_Quarters ya existe" AS resultado'
);

PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

-- Mostrar columnas KPI en Projects
SELECT 'Columnas KPI en Projects:' AS info;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'Projects' 
  AND COLUMN_NAME IN ('kpi_quarter_id', 'kpi_points', 'task_distribution_mode')
ORDER BY ORDINAL_POSITION;

-- =============================================
-- PASO 4: SINCRONIZAR CLAN_KPIS CON TRIMESTRES EXISTENTES
-- =============================================

SELECT '🔄 Sincronizando Clan_KPIs con trimestres existentes...' AS info;

-- Obtener el trimestre activo actual
SET @active_quarter_id = (
    SELECT kpi_quarter_id 
    FROM KPI_Quarters 
    WHERE is_active = 1 
    LIMIT 1
);

SELECT CONCAT('📅 Trimestre activo ID: ', IFNULL(@active_quarter_id, 'NINGUNO')) AS trimestre_activo;

-- Crear registros en Clan_KPIs para el trimestre activo si no existen
INSERT IGNORE INTO Clan_KPIs (clan_id, kpi_quarter_id, year, quarter, total_points, status, created_at)
SELECT 
    c.clan_id,
    kq.kpi_quarter_id,
    kq.year,
    kq.quarter,
    1000,
    'active',
    NOW()
FROM Clans c
CROSS JOIN KPI_Quarters kq
WHERE kq.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM Clan_KPIs ck 
      WHERE ck.clan_id = c.clan_id 
        AND ck.kpi_quarter_id = kq.kpi_quarter_id
  );

-- Si no hay trimestre activo, activar el más reciente
-- SOLUCIÓN para error #1093: Usar variables para evitar UPDATE con subconsultas de la misma tabla
-- Primero verificar si ya hay un trimestre activo
SET @active_count = (SELECT COUNT(*) FROM KPI_Quarters WHERE is_active = 1);

-- Si no hay trimestre activo, obtener el ID del más reciente
SET @latest_quarter_id = NULL;
SELECT kpi_quarter_id INTO @latest_quarter_id 
FROM KPI_Quarters 
ORDER BY year DESC, quarter DESC 
LIMIT 1;

-- Activar el trimestre más reciente solo si no hay ninguno activo
UPDATE KPI_Quarters 
SET is_active = 1, status = 'active' 
WHERE kpi_quarter_id = @latest_quarter_id 
  AND @active_count = 0 
  AND @latest_quarter_id IS NOT NULL;

-- Mostrar resultado de la activación
SELECT 
    CASE 
        WHEN @active_count > 0 THEN CONCAT('✅ Ya había ', @active_count, ' trimestre(s) activo(s)')
        WHEN @latest_quarter_id IS NULL THEN '⚠️ No hay trimestres KPI en la base de datos'
        WHEN @active_count = 0 AND @latest_quarter_id IS NOT NULL THEN CONCAT('✅ Activado trimestre ID: ', @latest_quarter_id)
        ELSE 'ℹ️ Estado desconocido'
    END AS resultado_activacion;

-- =============================================
-- PASO 5: MOSTRAR RESUMEN FINAL
-- =============================================

SELECT '🎉 ACTUALIZACIÓN KPI COMPLETADA EXITOSAMENTE' AS status;

-- Estado del trimestre activo
SELECT 
    CONCAT('📅 Trimestre activo: ', quarter, ' ', year) AS periodo_actual,
    CONCAT('📊 Puntos totales: ', FORMAT(total_points, 0)) AS puntos_totales,
    CONCAT('🎯 Estado: ', UPPER(status)) AS estado,
    created_at as fecha_creacion
FROM KPI_Quarters 
WHERE is_active = 1;

-- Clanes configurados
SELECT 
    CONCAT('🏰 Total clanes: ', COUNT(DISTINCT c.clan_id)) AS total_clanes,
    CONCAT('🔗 Clanes con KPI activo: ', COUNT(DISTINCT ck.clan_id)) AS clanes_con_kpi
FROM Clans c
LEFT JOIN Clan_KPIs ck ON c.clan_id = ck.clan_id 
    AND ck.kpi_quarter_id = (SELECT kpi_quarter_id FROM KPI_Quarters WHERE is_active = 1);

-- Estado de proyectos
SELECT 
    COUNT(*) AS total_proyectos,
    SUM(CASE WHEN kpi_quarter_id IS NOT NULL THEN 1 ELSE 0 END) AS proyectos_con_kpi,
    ROUND(SUM(CASE WHEN kpi_quarter_id IS NOT NULL THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) AS porcentaje_con_kpi
FROM Projects;

-- Puntos KPI por clan (si existen)
SELECT 
    c.clan_name,
    IFNULL(ck.total_points, 0) as puntos_asignados,
    IFNULL(ck.assigned_points, 0) as puntos_usados,
    IFNULL(ck.total_points - ck.assigned_points, 0) as puntos_disponibles,
    ck.status as estado_kpi
FROM Clans c
LEFT JOIN Clan_KPIs ck ON c.clan_id = ck.clan_id 
    AND ck.kpi_quarter_id = (SELECT kpi_quarter_id FROM KPI_Quarters WHERE is_active = 1)
ORDER BY c.clan_name;

-- Verificar integridad de datos
SELECT '🔍 VERIFICACIONES FINALES' AS verificaciones;

SELECT 
    'Relaciones Projects -> KPI_Quarters' AS relacion,
    COUNT(*) AS registros_relacionados
FROM Projects p
INNER JOIN KPI_Quarters kq ON p.kpi_quarter_id = kq.kpi_quarter_id;

SELECT 
    'Relaciones Clan_KPIs -> KPI_Quarters' AS relacion,
    COUNT(*) AS registros_relacionados
FROM Clan_KPIs ck
INNER JOIN KPI_Quarters kq ON ck.kpi_quarter_id = kq.kpi_quarter_id;

SELECT '✅ Sistema KPI listo para usar!' AS resultado_final;