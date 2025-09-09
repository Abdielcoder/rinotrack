<?php
// Script de prueba para verificar rutas
echo "Testing route: " . ($_GET['route'] ?? 'No route') . "\n";
echo "Project ID: " . ($_GET['project_id'] ?? 'No project_id') . "\n";

// Simular la lógica del index.php
$route = $_GET['route'] ?? '';

switch ($route) {
    case 'clan_leader/get-project-data':
        echo "Route matched: clan_leader/get-project-data\n";
        
        // Verificar si la clase existe
        require_once '../app/bootstrap.php';
        
        if (class_exists('ClanLeaderController')) {
            echo "ClanLeaderController class exists\n";
            
            try {
                $controller = new ClanLeaderController();
                if (method_exists($controller, 'getProjectData')) {
                    echo "getProjectData method exists\n";
                    // No ejecutar el método real para evitar problemas de sesión
                } else {
                    echo "getProjectData method does NOT exist\n";
                }
            } catch (Exception $e) {
                echo "Error creating controller: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ClanLeaderController class does NOT exist\n";
        }
        break;
    default:
        echo "Route NOT matched\n";
}
?>
