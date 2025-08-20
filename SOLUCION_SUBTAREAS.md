# 🔧 Solución para Error de Subtareas

## ❌ Error Encontrado
```
Error interno: createAdvanced falló: createSubtaskAdvanced falló: 
SQLSTATE[HY000]: General error: 1364 Field 'subtask_id' doesn't have a default value
```

## 🔍 Causa del Problema
La tabla `Subtasks` no tiene configurado el campo `subtask_id` como PRIMARY KEY y AUTO_INCREMENT.

## ✅ Solución

### Opción 1: Ejecutar Script Automático
Ejecuta el archivo `fix_subtasks_table.sql` en tu base de datos:

```sql
-- En phpMyAdmin o tu cliente MySQL favorito
SOURCE /Applications/MAMP/htdocs/RinoTrack/fix_subtasks_table.sql;
```

### Opción 2: Ejecutar Comandos Manuales
Ejecuta estos comandos SQL en tu base de datos `rinotrack`:

```sql
USE rinotrack;

-- Añadir PRIMARY KEY y AUTO_INCREMENT
ALTER TABLE Subtasks 
ADD PRIMARY KEY (subtask_id),
MODIFY subtask_id int(11) NOT NULL AUTO_INCREMENT;

-- Añadir índices para mejor rendimiento
ALTER TABLE Subtasks
ADD KEY idx_task_id (task_id),
ADD KEY idx_assigned_to (assigned_to_user_id),
ADD KEY idx_created_by (created_by_user_id),
ADD KEY idx_status (status),
ADD KEY idx_due_date (due_date);
```

### Opción 3: Recrear desde SQL actualizado
Si prefieres recrear la base de datos completa:
1. Haz backup de tus datos actuales
2. Usa el archivo `rinotrack.sql` actualizado (ya corregido)

## 🧪 Verificar la Solución

Después de aplicar la corrección, ejecuta:

```sql
DESCRIBE Subtasks;
SHOW CREATE TABLE Subtasks;
```

Deberías ver que `subtask_id` aparece como PRIMARY KEY AUTO_INCREMENT.

## 🎯 Resultado Esperado

Una vez corregido, podrás:
- ✅ Agregar subtareas desde el modal
- ✅ Las subtareas se crearán automáticamente en la BD
- ✅ El progreso de la tarea padre se actualizará automáticamente
- ✅ Todo funcionará sin errores

## 📝 Archivos Modificados

1. `rinotrack.sql` - Corregido con índices y AUTO_INCREMENT
2. `fix_subtasks_table.sql` - Script de corrección rápida
3. `app/views/admin/projects.php` - Modal con interfaz de subtareas
4. `app/controllers/AdminController.php` - Procesamiento de subtareas

## 🚀 Después de la Corrección

Recarga la página de administración de proyectos y prueba:
1. Crear una nueva tarea
2. Agregar algunas subtareas
3. Guardar la tarea
4. Verificar que se creó correctamente

¡La funcionalidad de subtareas estará completamente operativa! 🎉
