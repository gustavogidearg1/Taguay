-- =====================================================================
-- Taguay - Carga de Avance de Campo por WhatsApp (27/11/2026)
-- Equivalente SQL de las migraciones:
--   database/migrations/2026_11_27_120000_add_telefono_whatsapp_to_users_table.php
--   database/migrations/2026_11_27_120100_create_whatsapp_sesiones_table.php
-- Para ejecutar en phpMyAdmin (pestaña SQL), UNA sola vez.
-- Si da "Duplicate column name 'telefono_whatsapp'", la parte 1 ya estaba aplicada:
-- en ese caso ejecutá solo desde la parte 2.
-- =====================================================================

-- 1) Teléfono de WhatsApp en los usuarios (solo dígitos con código de país, ej. 5493534191741)
ALTER TABLE `users`
    ADD COLUMN `telefono_whatsapp` VARCHAR(20) NULL DEFAULT NULL AFTER `email`,
    ADD UNIQUE KEY `users_telefono_whatsapp_unique` (`telefono_whatsapp`);

-- 2) Conversaciones de carga en curso (en qué paso está cada teléfono)
CREATE TABLE IF NOT EXISTS `whatsapp_sesiones` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `telefono` VARCHAR(20) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `paso` VARCHAR(40) NULL DEFAULT NULL,
    `datos` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `whatsapp_sesiones_telefono_unique` (`telefono`),
    KEY `whatsapp_sesiones_user_id_foreign` (`user_id`),
    CONSTRAINT `whatsapp_sesiones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Registrar las migraciones como ejecutadas
INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.nombre, b.siguiente
FROM (
    SELECT '2026_11_27_120000_add_telefono_whatsapp_to_users_table' AS nombre
    UNION ALL SELECT '2026_11_27_120100_create_whatsapp_sesiones_table'
) m
CROSS JOIN (SELECT COALESCE(MAX(`batch`), 0) + 1 AS siguiente FROM `migrations`) b
WHERE NOT EXISTS (SELECT 1 FROM `migrations` x WHERE x.`migration` = m.nombre);
