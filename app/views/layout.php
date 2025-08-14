<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo Utils::asset('assets/css/theme.css'); ?>">
    <link rel="stylesheet" href="<?php echo Utils::asset('assets/css/styles.css'); ?>">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- JavaScript crítico en el head -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script de emergencia para deleteClan -->
    <script>
    // Función deleteClan de emergencia - disponible globalmente
    window.deleteClan = function(clanId) {
        if (confirm("¿Estás seguro de que quieres eliminar este clan? Esta acción no se puede deshacer.")) {
            const formData = new FormData();
            formData.append("clanId", clanId);
            
            fetch("?route=admin/delete-clan", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Clan eliminado exitosamente");
                    location.reload();
                } else {
                    alert(data.message || "Error al eliminar el clan");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error de conexión al eliminar el clan");
            });
        }
    };
    
    // console.log("✅ Función deleteClan cargada desde layout global");
    </script>
</head>
<body>
    <?php echo $content ?? ''; ?>
    
    <script src="<?php echo Utils::asset('assets/js/script.js'); ?>"></script>
</body>
</html>