<?php
// VISTA COMPLETAMENTE LIMPIA - NO DEBUG
error_reporting(0);
@ini_set('display_errors', 0);

// Verificar datos
$projects = isset($projects) && is_array($projects) ? $projects : [];
$clan = isset($clan) && is_array($clan) ? $clan : ['clan_name' => 'Sin clan'];
$search = isset($search) ? $search : '';

// Iniciar captura de salida
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Proyectos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #333; }
        .subtitle { color: #666; font-size: 14px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .search-box { margin-bottom: 20px; }
        .search-box input { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px; }
        .project-list { display: grid; gap: 20px; }
        .project-item { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; }
        .project-header { display: flex; justify-content: space-between; align-items: center; }
        .project-title { font-size: 18px; font-weight: bold; color: #333; }
        .project-desc { color: #666; margin: 10px 0; }
        .project-actions { display: flex; gap: 10px; }
        .btn-small { padding: 5px 15px; font-size: 14px; }
        .empty { text-align: center; padding: 50px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Gestionar Proyectos</h1>
                <div class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?></div>
            </div>
            <a href="#" class="btn" onclick="alert('Crear proyecto'); return false;">
                <i class="fas fa-plus"></i> Crear Proyecto
            </a>
        </div>

        <div class="search-box">
            <form method="GET" action="?route=clan_leader/projects">
                <input type="hidden" name="route" value="clan_leader/projects">
                <input type="text" name="search" placeholder="Buscar proyectos..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-small">Buscar</button>
                <?php if ($search): ?>
                    <a href="?route=clan_leader/projects" class="btn btn-small">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="project-list">
            <?php if (empty($projects)): ?>
                <div class="empty">
                    <p><i class="fas fa-folder-open fa-3x" style="color: #ddd;"></i></p>
                    <p>No hay proyectos en el clan</p>
                    <a href="#" class="btn" onclick="alert('Crear proyecto'); return false;">Crear primer proyecto</a>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <?php if (!is_array($project)) continue; ?>
                    <div class="project-item">
                        <div class="project-header">
                            <div>
                                <div class="project-title">
                                    <i class="fas fa-project-diagram"></i>
                                    <?php echo htmlspecialchars($project['project_name'] ?? 'Sin nombre'); ?>
                                </div>
                                <div class="project-desc">
                                    <?php echo htmlspecialchars($project['description'] ?? 'Sin descripción'); ?>
                                </div>
                            </div>
                            <div class="project-actions">
                                <a href="#" class="btn btn-small" onclick="alert('Editar'); return false;">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?route=clan_leader/tasks&project_id=<?php echo intval($project['project_id'] ?? 0); ?>" class="btn btn-small">
                                    <i class="fas fa-tasks"></i> Tareas
                                </a>
                                <a href="#" class="btn btn-small" style="background: #dc3545;" onclick="if(confirm('¿Eliminar proyecto?')) alert('Eliminado'); return false;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
// Obtener y limpiar el contenido
$output = ob_get_clean();

// Asegurarse de que no hay nada de debug antes de la salida
$output = preg_replace('/DEBUG.*?Total de proyectos.*?<\/div>/s', '', $output);
$output = preg_replace('/ID:\s*\d+.*?Creado por:\s*\d+<br>/s', '', $output);

// Mostrar solo el contenido limpio
echo $output;
exit(); // Terminar aquí para evitar que se cargue cualquier layout
?>