<?php
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Task Update Debug Test</h1>";

$tests = [
    'Auth' => 'Auth',
    'User' => 'User',
    'Task' => 'Task',
    'Project' => 'Project',
    'Clan' => 'Clan',
    'ClanMemberController' => 'ClanMemberController',
];

$allPassed = true;

foreach ($tests as $name => $class) {
    echo "<h2>Testing $name...</h2>";
    try {
        $instance = new $class();
        echo "<p style='color: green;'>✅ $name loaded successfully!</p>";
        
        // Test specific methods for Task model
        if ($class === 'Task') {
            echo "<h3>Testing Task model methods...</h3>";
            try {
                // Test findById method
                $result = $instance->findById(1);
                echo "<p>findById(1) result: " . ($result ? "SUCCESS" : "NO TASK FOUND") . "</p>";
                
                // Test update method signature
                $reflection = new ReflectionMethod($instance, 'update');
                $params = $reflection->getParameters();
                echo "<p>update method parameters: " . count($params) . "</p>";
                foreach ($params as $param) {
                    echo "<p>  - " . $param->getName() . " (default: " . ($param->isDefaultValueAvailable() ? "YES" : "NO") . ")</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: orange;'>⚠️ Task method test failed: " . $e->getMessage() . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Failed to load $name: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "<h1>All core classes loaded successfully!</h1>";
} else {
    echo "<h1>Some core classes failed to load. Check errors above.</h1>";
}

// Test database connection
echo "<h2>Testing Database Connection...</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test Tasks table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Tasks LIMIT 1");
    $result = $stmt->fetch();
    echo "<p>Tasks table accessible: " . ($result ? "YES" : "NO") . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>
