<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME . ' - Admin'; ?></title>
    
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
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
    
    <!-- JavaScript con rutas absolutas -->
    <script src="<?php echo APP_URL; ?>assets/js/script.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</body>
</html>