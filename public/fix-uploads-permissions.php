<?php
/**
 * Script para intentar arreglar permisos del directorio uploads
 */

header('Content-Type: application/json');

$uploadsDir = __DIR__ . '/uploads/';
$result = [
    'uploads_dir' => $uploadsDir,
    'initial_exists' => file_exists($uploadsDir),
    'initial_writable' => is_writable($uploadsDir),
    'initial_permissions' => file_exists($uploadsDir) ? substr(sprintf('%o', fileperms($uploadsDir)), -4) : 'N/A',
    'actions_taken' => [],
    'final_writable' => false,
    'success' => false
];

try {
    // Paso 1: Crear directorio si no existe
    if (!file_exists($uploadsDir)) {
        if (mkdir($uploadsDir, 0777, true)) {
            $result['actions_taken'][] = 'Directorio creado con permisos 0777';
        } else {
            $result['actions_taken'][] = 'ERROR: No se pudo crear el directorio';
        }
    }

    // Paso 2: Intentar cambiar permisos
    if (file_exists($uploadsDir)) {
        if (chmod($uploadsDir, 0777)) {
            $result['actions_taken'][] = 'Permisos cambiados a 0777';
        } else {
            $result['actions_taken'][] = 'ERROR: No se pudieron cambiar permisos a 0777';
            
            // Intentar con 0755
            if (chmod($uploadsDir, 0755)) {
                $result['actions_taken'][] = 'Permisos cambiados a 0755';
            } else {
                $result['actions_taken'][] = 'ERROR: No se pudieron cambiar permisos a 0755';
            }
        }
    }

    // Paso 3: Crear archivo de prueba
    $testFile = $uploadsDir . 'test_write_' . time() . '.txt';
    if (file_put_contents($testFile, 'test')) {
        $result['actions_taken'][] = 'Archivo de prueba creado exitosamente';
        unlink($testFile);
        $result['actions_taken'][] = 'Archivo de prueba eliminado';
        $result['success'] = true;
    } else {
        $result['actions_taken'][] = 'ERROR: No se pudo crear archivo de prueba';
    }

    // Verificar estado final
    $result['final_exists'] = file_exists($uploadsDir);
    $result['final_writable'] = is_writable($uploadsDir);
    $result['final_permissions'] = file_exists($uploadsDir) ? substr(sprintf('%o', fileperms($uploadsDir)), -4) : 'N/A';

    // Información del servidor
    $result['server_info'] = [
        'user' => get_current_user(),
        'uid' => function_exists('posix_getuid') ? posix_getuid() : 'N/A',
        'gid' => function_exists('posix_getgid') ? posix_getgid() : 'N/A',
        'php_user' => function_exists('posix_getpwuid') && function_exists('posix_getuid') ? 
                      posix_getpwuid(posix_getuid())['name'] : 'N/A'
    ];

} catch (Exception $e) {
    $result['actions_taken'][] = 'EXCEPCIÓN: ' . $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
