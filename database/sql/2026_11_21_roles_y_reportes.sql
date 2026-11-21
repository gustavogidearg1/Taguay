-- =====================================================================
-- Taguay - Reorganización de roles y reportes (21/11/2026)
-- Equivalente SQL de la migración:
--   database/migrations/2026_11_21_130000_reorganizar_roles_y_reportes.php
-- Para ejecutar en phpMyAdmin (pestaña SQL) cuando no hay acceso a terminal.
-- Se puede ejecutar más de una vez sin duplicar datos.
-- =====================================================================

START TRANSACTION;

-- 1) Permisos (áreas + reportes del Home)
INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT p.name, 'web', NOW(), NOW()
FROM (
    SELECT 'ver_agricola' AS name UNION ALL SELECT 'ver_ganadero' UNION ALL SELECT 'ver_comercial'
    UNION ALL SELECT 'reporte_agricola' UNION ALL SELECT 'reporte_ganadero' UNION ALL SELECT 'reporte_flujo_fondo'
) p
WHERE NOT EXISTS (SELECT 1 FROM `permissions` x WHERE x.name = p.name AND x.guard_name = 'web');

-- 2) Roles
INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT r.name, 'web', NOW(), NOW()
FROM (
    SELECT 'admin' AS name UNION ALL SELECT 'agricola' UNION ALL SELECT 'ganadero' UNION ALL SELECT 'comercial'
) r
WHERE NOT EXISTS (SELECT 1 FROM `roles` x WHERE x.name = r.name AND x.guard_name = 'web');

-- 3) Permisos de cada rol: admin = todos; agricola/ganadero/comercial = su área
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.guard_name = 'web' AND r.name = 'admin'
WHERE p.guard_name = 'web'
  AND p.name IN ('ver_agricola','ver_ganadero','ver_comercial','reporte_agricola','reporte_ganadero','reporte_flujo_fondo');

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.guard_name = 'web'
 AND ( (r.name = 'agricola'  AND p.name = 'ver_agricola')
    OR (r.name = 'ganadero'  AND p.name = 'ver_ganadero')
    OR (r.name = 'comercial' AND p.name = 'ver_comercial') )
WHERE p.guard_name = 'web';

-- Usuarios administradores (para excluirlos de la migración de usuarios)
DROP TEMPORARY TABLE IF EXISTS tmp_admins;
CREATE TEMPORARY TABLE tmp_admins AS
SELECT mhr.model_id AS user_id
FROM `model_has_roles` mhr
JOIN `roles` r ON r.id = mhr.role_id AND r.name = 'admin'
WHERE mhr.model_type = 'App\\Models\\User';

-- 4) Áreas tildadas (permisos directos ver_*) -> roles
INSERT IGNORE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
SELECT r.id, 'App\\Models\\User', mhp.model_id
FROM `model_has_permissions` mhp
JOIN `permissions` p ON p.id = mhp.permission_id
JOIN `roles` r ON r.guard_name = 'web' AND r.name = CASE p.name
        WHEN 'ver_agricola'  THEN 'agricola'
        WHEN 'ver_ganadero'  THEN 'ganadero'
        WHEN 'ver_comercial' THEN 'comercial'
     END
WHERE mhp.model_type = 'App\\Models\\User'
  AND p.name IN ('ver_agricola','ver_ganadero','ver_comercial')
  AND mhp.model_id NOT IN (SELECT user_id FROM tmp_admins);

-- 5) Reportes que ya veían (para que nadie pierda acceso):
--    Margen Bruto/Cosecha y Flujo de Fondo -> todos los no-admin
INSERT IGNORE INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`)
SELECT p.id, 'App\\Models\\User', u.id
FROM `users` u
JOIN `permissions` p ON p.guard_name = 'web' AND p.name IN ('reporte_agricola','reporte_flujo_fondo')
WHERE u.id NOT IN (SELECT user_id FROM tmp_admins);

--    Ganadería -> quienes tienen el rol ganadero (incluye los recién migrados)
INSERT IGNORE INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`)
SELECT p.id, 'App\\Models\\User', mhr.model_id
FROM `model_has_roles` mhr
JOIN `roles` r ON r.id = mhr.role_id AND r.name = 'ganadero'
JOIN `permissions` p ON p.guard_name = 'web' AND p.name = 'reporte_ganadero'
WHERE mhr.model_type = 'App\\Models\\User'
  AND mhr.model_id NOT IN (SELECT user_id FROM tmp_admins);

-- 6) Quitar permisos directos de área (ahora los dan los roles)
DELETE mhp FROM `model_has_permissions` mhp
JOIN `permissions` p ON p.id = mhp.permission_id
WHERE mhp.model_type = 'App\\Models\\User'
  AND p.name IN ('ver_agricola','ver_ganadero','ver_comercial');

DROP TEMPORARY TABLE IF EXISTS tmp_admins;

-- 7) Registrar la migración como ejecutada
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_21_130000_reorganizar_roles_y_reportes', b.siguiente
FROM (SELECT COALESCE(MAX(`batch`), 0) + 1 AS siguiente FROM `migrations`) b
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_21_130000_reorganizar_roles_y_reportes');

-- 8) Limpiar la caché de permisos de Spatie (CACHE_STORE=database)
DELETE FROM `cache` WHERE `key` LIKE '%spatie.permission.cache%';

COMMIT;
