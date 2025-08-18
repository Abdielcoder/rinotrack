<?php
// Script de prueba para verificar el filtrado de proyectos personales
require_once 'app/bootstrap.php';

// Simular datos de proyectos (como los que vemos en la imagen)
$testProjects = [
    [
        'project_id' => 36,
        'project_name' => 'Proyecto de prueba',
        'created_by_user_id' => 2,
        'is_personal' => 0,
        'clan_id' => 5
    ],
    [
        'project_id' => 38,
        'project_name' => 'Proyecto Personal - Usuario 4',
        'created_by_user_id' => 4,
        'is_personal' => 1,
        'clan_id' => 5
    ],
    [
        'project_id' => 39,
        'project_name' => 'Proyecto Personal - Usuario 9',
        'created_by_user_id' => 9,
        'is_personal' => 1,
        'clan_id' => 5
    ],
    [
        'project_id' => 37,
        'project_name' => 'Proyecto Personal de Prueba',
        'created_by_user_id' => 4,
        'is_personal' => 1,
        'clan_id' => 5
    ],
    [
        'project_id' => 40,
        'project_name' => 'Tareas Personales',
        'created_by_user_id' => 4,
        'is_personal' => 1,
        'clan_id' => 5
    ]
];

// Simular que el usuario actual es el usuario 4 (líder del clan)
$currentUserId = 4;

echo "=== PRUEBA DE FILTRADO ===\n";
echo "Usuario actual: $currentUserId\n";
echo "Total de proyectos: " . count($testProjects) . "\n\n";

// Aplicar el mismo filtro que en el controlador
$filteredProjects = array_filter($testProjects, function($project) use ($currentUserId) {
    $projectId = $project['project_id'];
    $projectName = $project['project_name'];
    $isPersonal = $project['is_personal'] == 1;
    $createdBy = $project['created_by_user_id'];
    
    echo "--- Analizando proyecto: ID=$projectId, Nombre='$projectName' ---\n";
    echo "  is_personal: $isPersonal\n";
    echo "  created_by_user_id: $createdBy\n";
    echo "  current_user_id: $currentUserId\n";
    
    // Si es un proyecto personal (is_personal = 1)
    if ($isPersonal) {
        // Solo mostrar si fue creado por el líder actual
        $isOwnPersonal = $createdBy == $currentUserId;
        echo "  RESULTADO: Proyecto PERSONAL - Es propio: " . ($isOwnPersonal ? 'SÍ' : 'NO') . "\n";
        return $isOwnPersonal;
    }
    // Mostrar todos los proyectos no personales
    echo "  RESULTADO: Proyecto NORMAL - Se muestra\n";
    return true;
});

echo "\n=== RESULTADO DEL FILTRADO ===\n";
echo "Total de proyectos después del filtro: " . count($filteredProjects) . "\n";
echo "Proyectos que se mostrarán:\n";

foreach ($filteredProjects as $project) {
    echo "- {$project['project_name']} (ID: {$project['project_id']})\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";
?>
