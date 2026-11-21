-- =====================================================================
-- Taguay - Cultivo y Propietario en Estados por Lote (21/11/2026)
-- Equivalente SQL de la migración:
--   database/migrations/2026_11_21_140000_add_cultivo_y_propietario_to_lote_estados_table.php
-- Para ejecutar en phpMyAdmin (pestaña SQL), UNA sola vez.
-- Si da "Duplicate column name 'cultivo_id'", ya estaba aplicado: no hace falta repetirlo.
-- =====================================================================

-- 1) Columnas nuevas
--    cultivo_id: cultivo del movimiento (ABM de Cultivos)
--    propietario: 1 = propio (Taguay), 0 = de socio. Por defecto 1.
ALTER TABLE `lote_estados`
    ADD COLUMN `cultivo_id` BIGINT UNSIGNED NULL AFTER `estado_cultivo_id`,
    ADD COLUMN `propietario` TINYINT(1) NOT NULL DEFAULT 1 AFTER `pertenece_empresa`,
    ADD CONSTRAINT `lote_estados_cultivo_id_foreign`
        FOREIGN KEY (`cultivo_id`) REFERENCES `cultivos` (`id`) ON DELETE SET NULL;

-- 2) Registros anteriores: se completa el cultivo con el que tiene asignado el lote
UPDATE `lote_estados` le
JOIN `lotes` l ON l.id = le.lote_id
SET le.cultivo_id = l.cultivo_id
WHERE le.cultivo_id IS NULL AND l.cultivo_id IS NOT NULL;

-- 3) Registrar la migración como ejecutada
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_21_140000_add_cultivo_y_propietario_to_lote_estados_table', b.siguiente
FROM (SELECT COALESCE(MAX(`batch`), 0) + 1 AS siguiente FROM `migrations`) b
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_21_140000_add_cultivo_y_propietario_to_lote_estados_table'
);
