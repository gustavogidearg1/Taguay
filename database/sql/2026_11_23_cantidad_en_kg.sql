-- =====================================================================
-- Taguay - Producción en kilogramos (23/11/2026)
-- Equivalente SQL de la migración:
--   database/migrations/2026_11_23_120000_rename_produccion_tn_to_cantidad_on_lote_estados_table.php
-- Para ejecutar en phpMyAdmin (pestaña SQL), UNA sola vez.
-- Si da "Unknown column 'produccion_tn'", ya estaba aplicado: no hace falta repetirlo.
-- =====================================================================

-- 1) La columna produccion_tn (toneladas) pasa a llamarse cantidad (kilogramos).
--    Mismo tipo: DECIMAL(12,2), admite nulos. No se convierten valores.
ALTER TABLE `lote_estados`
    CHANGE `produccion_tn` `cantidad` DECIMAL(12,2) NULL DEFAULT NULL;

-- 2) Registrar la migración como ejecutada
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_23_120000_rename_produccion_tn_to_cantidad_on_lote_estados_table', b.siguiente
FROM (SELECT COALESCE(MAX(`batch`), 0) + 1 AS siguiente FROM `migrations`) b
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_23_120000_rename_produccion_tn_to_cantidad_on_lote_estados_table'
);
