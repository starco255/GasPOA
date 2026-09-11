-- GasPOA Market: safe XAMPP/phpMyAdmin recovery and interface-preference upgrade
-- Backup source: gaspoa_market+BackupProoooo.sql
-- MariaDB 10.4 / MySQL-compatible SQL
--
-- IMPORTANT: Run SECTION 1, then import the backup in phpMyAdmin, then run SECTION 2.
-- Do not run `php artisan migrate` after the import: the ledger in Section 2 records
-- migrations that are already represented by the backup schema.

-- ============================================================================
-- SECTION 1 — Run this in phpMyAdmin SQL first.
-- It RENAMES the currently empty Laravel tables; it does not delete them.
-- ============================================================================
USE `gaspoa_market`;

-- Optional safety check. At the time this file was prepared, all five counts were 0.
SELECT 'users' AS table_name, COUNT(*) AS row_count FROM `users`
UNION ALL SELECT 'migrations', COUNT(*) FROM `migrations`
UNION ALL SELECT 'failed_jobs', COUNT(*) FROM `failed_jobs`
UNION ALL SELECT 'password_reset_tokens', COUNT(*) FROM `password_reset_tokens`
UNION ALL SELECT 'personal_access_tokens', COUNT(*) FROM `personal_access_tokens`;

-- Only run these RENAME statements once. They preserve the current tables as a fallback.
RENAME TABLE
    `users` TO `recovery_20260820_users`,
    `migrations` TO `recovery_20260820_migrations`,
    `failed_jobs` TO `recovery_20260820_failed_jobs`,
    `password_reset_tokens` TO `recovery_20260820_password_reset_tokens`,
    `personal_access_tokens` TO `recovery_20260820_personal_access_tokens`;

-- STOP HERE.
-- In phpMyAdmin select `gaspoa_market` > Import, then select:
-- C:\Users\Administrator\Downloads\gaspoa_market+BackupProoooo.sql
-- Press Import. The backup restores business_profiles, products, inventory,
-- orders, messages, payments and the original users/data.

-- ============================================================================
-- SECTION 2 — Run this in phpMyAdmin SQL AFTER the backup import succeeds.
-- ============================================================================
USE `gaspoa_market`;

-- Recreate Laravel support tables not included in the supplied backup.
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) NOT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `tokenable_type` varchar(255) NOT NULL,
    `tokenable_id` bigint(20) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `token` varchar(64) NOT NULL,
    `abilities` text DEFAULT NULL,
    `last_used_at` timestamp NULL DEFAULT NULL,
    `expires_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The new, non-destructive Language and Light/Dark Mode settings.
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `interface_language` varchar(5) NULL,
    ADD COLUMN IF NOT EXISTS `interface_theme` varchar(10) NULL;

-- Keep Laravel from re-running schema changes that are already present in the backup.
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
    ('2014_10_12_000000_create_users_table', 1),
    ('2014_10_12_100000_create_password_reset_tokens_table', 1),
    ('2019_08_19_000000_create_failed_jobs_table', 1),
    ('2019_12_14_000001_create_personal_access_tokens_table', 1),
    ('2026_08_13_000001_add_provider_payment_settings_to_business_profiles', 1),
    ('2026_08_13_000002_add_payment_method_to_wholesale_orders', 1),
    ('2026_08_13_000003_create_payments_table', 1),
    ('2026_08_13_000004_create_payouts_table', 1),
    ('2026_08_13_000005_add_provider_payment_method_to_retail_orders', 1),
    ('2026_08_14_000006_add_gateway_audit_fields_to_payments', 1),
    ('2026_08_16_000007_create_security_audit_logs_table', 1),
    ('2026_08_20_000008_add_interface_preferences_to_users_table', 2);

-- Final checks: all must exist, including business_profiles and the two new fields.
SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'gaspoa_market'
  AND table_name IN ('users', 'business_profiles', 'products', 'inventory', 'retail_orders', 'wholesale_orders', 'payments')
ORDER BY table_name;

SELECT column_name
FROM information_schema.columns
WHERE table_schema = 'gaspoa_market'
  AND table_name = 'users'
  AND column_name IN ('interface_language', 'interface_theme');

-- Keep the renamed recovery tables until the application has been checked successfully.
-- They can be dropped later manually, but this script intentionally does not delete them.
