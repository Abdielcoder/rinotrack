<?php
// Script de diagnóstico para verificar problemas con proyectos

// Incluir configuración
require_once '../config/database.php';

// Conexión a la base de datos
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>🔍 Diagnóstico de Proyectos</h2>";
    
    // 1. Verificar proyectos con caracteres problemáticos
    echo "<h3>1. Proyectos con caracteres especiales:</h3>";
    $stmt = $pdo->query("
        SELECT project_id, project_name, description, clan_id 
        FROM Projects 
        WHERE project_name LIKE '%\"%' 
           OR project_name LIKE '%\'%' 
           OR description LIKE '%\"%' 
           OR description LIKE '%\'%'
           OR project_name LIKE '%\\\\%'
           OR description LIKE '%\\\\%'
    ");
    $problematicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($problematicos)) {
        echo "<p style='color: green;'>✅ No hay proyectos con caracteres problemáticos</p>";
    } else {
        echo "<p style='color: red;'>⚠️ Proyectos con caracteres problemáticos:</p>";
        echo "<pre>" . print_r($problematicos, true) . "</pre>";
    }
    
    // 2. Verificar estructura de la tabla
    echo "<h3>2. Estructura de la tabla Projects:</h3>";
    $stmt = $pdo->query("SHOW CREATE TABLE Projects");
    $estructura = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (strpos($estructura['Create Table'], 'AUTO_INCREMENT') !== false) {
        echo "<p style='color: green;'>✅ AUTO_INCREMENT está configurado</p>";
    } else {
        echo "<p style='color: red;'>⚠️ AUTO_INCREMENT NO está configurado</p>";
    }
    
    // 3. Verificar proyectos actuales
    echo "<h3>3. Proyectos actuales:</h3>";
    $stmt = $pdo->query("
        SELECT project_id, project_name, clan_id, status 
        FROM Projects 
        ORDER BY project_id DESC 
        LIMIT 10
    ");
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Clan ID</th><th>Estado</th></tr>";
    foreach ($proyectos as $proyecto) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($proyecto['project_id']) . "</td>";
        echo "<td>" . htmlspecialchars($proyecto['project_name']) . "</td>";
        echo "<td>" . htmlspecialchars($proyecto['clan_id']) . "</td>";
        echo "<td>" . htmlspecialchars($proyecto['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 4. Verificar IDs problemáticos
    echo "<h3>4. Verificación de IDs:</h3>";
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM Projects 
        WHERE project_id IS NULL OR project_id <= 0
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        echo "<p style='color: green;'>✅ Todos los project_id son válidos</p>";
    } else {
        echo "<p style='color: red;'>⚠️ Hay " . $result['count'] . " proyectos con IDs inválidos</p>";
    }
    
    // 5. Verificar clanes
    echo "<h3>5. Clanes disponibles:</h3>";
    $stmt = $pdo->query("SELECT clan_id, clan_name FROM Clans ORDER BY clan_id");
    $clanes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($clanes as $clan) {
        echo "<li>ID: " . htmlspecialchars($clan['clan_id']) . " - " . htmlspecialchars($clan['clan_name']) . "</li>";
    }
    echo "</ul>";
    
    // 6. Test de JavaScript
    echo "<h3>6. Test de JavaScript:</h3>";
    ?>
    
    <button onclick="testFunction()">Test JavaScript</button>
    <div id="test-result"></div>
    
    <script>
        function testFunction() {
            document.getElementById('test-result').innerHTML = '<p style="color: green;">✅ JavaScript funcionando correctamente</p>';
        }
        
        // Verificar si las funciones están disponibles
        window.addEventListener('load', function() {
            var funciones = [
                'openCreateProjectModal',
                'closeProjectModal',
                'editProject',
                'viewProject',
                'deleteProject',
                'toggleProjectMenu',
                'filterProjects'
            ];
            
            var resultHTML = '<h4>Estado de funciones:</h4><ul>';
            funciones.forEach(function(func) {
                if (typeof window[func] === 'function') {
                    resultHTML += '<li style="color: green;">✅ ' + func + ' está definida</li>';
                } else {
                    resultHTML += '<li style="color: red;">❌ ' + func + ' NO está definida</li>';
                }
            });
            resultHTML += '</ul>';
            
            var div = document.createElement('div');
            div.innerHTML = resultHTML;
            document.body.appendChild(div);
        });
    </script>
    
    <?php
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error de conexión a la base de datos:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
