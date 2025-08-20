<?php
// Archivo de prueba para verificar la creación de subtareas
header('Content-Type: application/json');

// Simular datos de subtareas
$subtasks = [
    [
        'title' => 'Subtarea de prueba 1',
        'percentage' => 25,
        'description' => 'Descripción de prueba 1'
    ],
    [
        'title' => 'Subtarea de prueba 2',
        'percentage' => 50,
        'description' => 'Descripción de prueba 2'
    ]
];

// Simular datos de tarea
$taskData = [
    'task_title' => 'Tarea de prueba',
    'task_due_date' => '2024-12-31',
    'task_project' => '29',
    'task_description' => 'Descripción de prueba',
    'priority' => 'medium',
    'assigned_members' => ['1', '2'],
    'subtasks' => json_encode($subtasks)
];

echo json_encode([
    'success' => true,
    'message' => 'Datos de prueba generados',
    'task_data' => $taskData,
    'subtasks_raw' => $subtasks,
    'subtasks_json' => json_encode($subtasks),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
