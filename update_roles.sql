-- Actualizar tabla de roles según la nueva jerarquía
-- Jerarquía: Super admin > admin > lider_clan > usuario normal

-- Limpiar roles existentes
DELETE FROM User_Roles;
DELETE FROM Roles;

-- Insertar nuevos roles con la jerarquía correcta
INSERT INTO Roles (role_id, role_name) VALUES
(1, 'super_admin'),
(2, 'admin'), 
(3, 'lider_clan'),
(4, 'usuario_normal');

-- Asignar roles a usuarios existentes
-- Usuario 'super' será super_admin
INSERT INTO User_Roles (user_id, role_id) VALUES (1, 1);

-- Usuario 'admin' será admin  
INSERT INTO User_Roles (user_id, role_id) VALUES (2, 2);

-- Usuario 'usuario1' será usuario normal
INSERT INTO User_Roles (user_id, role_id) VALUES (3, 4);

-- Agregar algunos clanes de ejemplo
INSERT INTO Clans (clan_name, created_at) VALUES
('Desarrollo', NOW()),
('Marketing', NOW()),
('Diseño', NOW()),
('QA Testing', NOW());