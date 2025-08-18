<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Checkboxes</title>
    <style>
        /* Estilos para el checkbox de tareas */
        .task-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            margin: 0;
            margin-right: 8px;
            accent-color: #10b981;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .task-checkbox:checked {
            background: #10b981;
            border-color: #10b981;
        }

        .task-checkbox:hover {
            border-color: #10b981;
            transform: scale(1.05);
        }

        /* Forzar visibilidad de checkboxes */
        input[type="checkbox"].task-checkbox {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 10 !important;
            /* Debug: hacer muy visible */
            border: 3px solid #ff0000 !important;
            background: #ffff00 !important;
            width: 24px !important;
            height: 24px !important;
        }

        /* Asegurar que el header de la tarea tenga el layout correcto */
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }

        .task-card {
            border: 1px solid #ddd;
            padding: 16px;
            margin: 8px;
            border-radius: 8px;
            background: white;
        }
    </style>
</head>
<body>
    <h1>Test de Checkboxes</h1>
    
    <div class="task-card">
        <div class="task-header">
            <input type="checkbox" class="task-checkbox" onchange="console.log('Checkbox cambiado:', this.checked)">
            <div class="task-priority-badge">MEDIA</div>
        </div>
        <div class="task-content">
            <h5 class="task-title">Tarea de prueba</h5>
            <p>Esta es una tarea de prueba para verificar que los checkboxes se muestren correctamente.</p>
        </div>
    </div>

    <div class="task-card">
        <div class="task-header">
            <input type="checkbox" class="task-checkbox" checked onchange="console.log('Checkbox cambiado:', this.checked)">
            <div class="task-priority-badge">ALTA</div>
        </div>
        <div class="task-content">
            <h5 class="task-title">Tarea completada</h5>
            <p>Esta tarea ya está marcada como completada.</p>
        </div>
    </div>

    <script>
        console.log('Página cargada. Verificando checkboxes...');
        const checkboxes = document.querySelectorAll('.task-checkbox');
        console.log('Checkboxes encontrados:', checkboxes.length);
        checkboxes.forEach((cb, index) => {
            console.log(`Checkbox ${index}:`, cb.checked, cb.visible, cb.style.display);
        });
    </script>
</body>
</html>
