<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME . ' - Admin'; ?></title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230965f7'/%3E%3Ctext x='50%' y='50%' dy='.35em' text-anchor='middle' font-family='Inter, Arial, sans-serif' font-size='32' fill='white'%3ER%3C/text%3E%3C/svg%3E">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS con rutas absolutas -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/css/admin.css">
    
    <!-- Additional CSS files -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <!-- Including CSS: <?php echo htmlspecialchars($css); ?> -->
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        const APP_URL = '<?php echo APP_URL; ?>';
    </script>
    
    <!-- JavaScript con rutas absolutas -->
    <script src="<?php echo APP_URL; ?>assets/js/script.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <?php if (strpos($js, '<script>') === 0): ?>
                <!-- Including inline JS -->
                <?php echo $js; ?>
            <?php else: ?>
                <!-- Including external JS: <?php echo htmlspecialchars($js); ?> -->
                <script src="<?php echo $js; ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Asegurar que las funciones estén disponibles después de cargar todos los scripts
        if (typeof openCreateProjectModal === 'undefined') {
            window.openCreateProjectModal = function() {
                var modal = document.getElementById('projectModal');
                if (modal) { modal.style.display = 'block'; }
            };
        }
    </script>
</head>
<body>
    <?php echo $content ?? ''; ?>
</body>
</html>