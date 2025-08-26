<?php
// Test file for debugging admin user creation
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Test - Admin User Creation</h1>";

try {
    echo "<p>1. Testing basic PHP execution... ✓</p>";
    
    // Include bootstrap
    echo "<p>2. Loading bootstrap...</p>";
    require_once '../app/bootstrap.php';
    echo "<p>   Bootstrap loaded ✓</p>";
    
    // Test database connection
    echo "<p>3. Testing database connection...</p>";
    $db = Database::getConnection();
    echo "<p>   Database connected ✓</p>";
    
    // Test User model
    echo "<p>4. Testing User model...</p>";
    $userModel = new User();
    echo "<p>   User model created ✓</p>";
    
    // Test Role model
    echo "<p>5. Testing Role model...</p>";
    $roleModel = new Role();
    echo "<p>   Role model created ✓</p>";
    
    // Test Auth model
    echo "<p>6. Testing Auth model...</p>";
    $auth = new Auth();
    echo "<p>   Auth model created ✓</p>";
    
    // Test AdminController
    echo "<p>7. Testing AdminController...</p>";
    $adminController = new AdminController();
    echo "<p>   AdminController created ✓</p>";
    
    // Test exists method
    echo "<p>8. Testing User::exists method...</p>";
    $exists = $userModel->exists('test_nonexistent_user', 'test@nonexistent.com');
    echo "<p>   User::exists method works: " . ($exists ? "true" : "false") . " ✓</p>";
    
    // Test database table structure
    echo "<p>9. Testing Users table structure...</p>";
    $stmt = $db->prepare("DESCRIBE Users");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    echo "<p>   Users table columns:</p><ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} - {$column['Type']} - " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>All tests passed! ✓</h2>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error found: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
