-- GasPOA production payment upgrade for an EXISTING MySQL/MariaDB database.
-- Run this file manually after taking a backup. It only creates tables, columns
-- and indexes when missing; it does not drop tables, columns, or existing rows.
-- Compatible with the attached gaspoa_market++ database structure.

DELIMITER $$
CREATE PROCEDURE gaspoa_add_column_if_missing(
    IN table_name_in VARCHAR(64),
    IN column_name_in VARCHAR(64),
    IN definition_in TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = table_name_in
          AND column_name = column_name_in
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', table_name_in, '` ADD COLUMN `', column_name_in, '` ', definition_in);
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;
    END IF;
END$$

CREATE PROCEDURE gaspoa_add_index_if_missing(
    IN table_name_in VARCHAR(64),
    IN index_name_in VARCHAR(64),
    IN index_columns_in TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = table_name_in
          AND index_name = index_name_in
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', table_name_in, '` ADD INDEX `', index_name_in, '` (', index_columns_in, ')');
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;
    END IF;
END$$
DELIMITER ;

-- These columns complete the existing payments table without changing old fields.
CALL gaspoa_add_column_if_missing('payments', 'provider', 'VARCHAR(30) NOT NULL DEFAULT ''manual''');
CALL gaspoa_add_column_if_missing('payments', 'provider_payment_reference', 'VARCHAR(100) NULL');
CALL gaspoa_add_column_if_missing('payments', 'checkout_url', 'TEXT NULL');
CALL gaspoa_add_column_if_missing('payments', 'paid_at', 'TIMESTAMP NULL');
CALL gaspoa_add_column_if_missing('payments', 'failed_at', 'TIMESTAMP NULL');
CALL gaspoa_add_column_if_missing('payments', 'failure_reason', 'VARCHAR(255) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_event_id', 'VARCHAR(120) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_event', 'VARCHAR(80) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_status', 'VARCHAR(50) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_paid_amount', 'DECIMAL(12,2) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_currency', 'CHAR(3) NULL');
CALL gaspoa_add_column_if_missing('payments', 'gateway_callback_received_at', 'TIMESTAMP NULL');
CALL gaspoa_add_column_if_missing('payments', 'checkout_expires_at', 'TIMESTAMP NULL');

-- A durable, idempotent audit log for callbacks. A repeated ClickPesa webhook
-- has the same event_key and is saved only once.
CREATE TABLE IF NOT EXISTS `payment_webhook_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payment_id` BIGINT UNSIGNED NOT NULL,
    `provider` VARCHAR(30) NOT NULL,
    `event_key` VARCHAR(191) NOT NULL,
    `event_name` VARCHAR(80) NULL,
    `event_status` VARCHAR(50) NULL,
    `payload` JSON NOT NULL,
    `received_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payment_webhook_events_event_key_unique` (`event_key`),
    KEY `payment_webhook_events_payment_id_received_at_index` (`payment_id`, `received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Security audit trail for account recovery, phone verification and login risk.
-- This is additive and deliberately has no foreign keys, so an old audit event
-- remains available even if the related account is later removed.
CREATE TABLE IF NOT EXISTS `security_audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `actor_user_id` BIGINT UNSIGNED NULL,
    `subject_user_id` BIGINT UNSIGNED NULL,
    `event_type` VARCHAR(80) NOT NULL,
    `severity` VARCHAR(20) NOT NULL DEFAULT 'info',
    `description` VARCHAR(500) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `context` JSON NULL,
    `occurred_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    KEY `security_audit_logs_event_type_occurred_at_index` (`event_type`, `occurred_at`),
    KEY `security_audit_logs_subject_user_id_occurred_at_index` (`subject_user_id`, `occurred_at`),
    KEY `security_audit_logs_actor_user_id_occurred_at_index` (`actor_user_id`, `occurred_at`),
    KEY `security_audit_logs_severity_occurred_at_index` (`severity`, `occurred_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CALL gaspoa_add_index_if_missing('payments', 'payments_gateway_event_id_index', '`gateway_event_id`');
CALL gaspoa_add_index_if_missing('payments', 'payments_provider_status_index', '`provider`, `status`');
CALL gaspoa_add_index_if_missing('otp_codes', 'otp_codes_user_purpose_valid_index', '`user_id`, `purpose`, `is_used`, `expires_at`');

DROP PROCEDURE gaspoa_add_column_if_missing;
DROP PROCEDURE gaspoa_add_index_if_missing;

-- Verification query (read-only):
-- SELECT id, order_type, order_id, provider, provider_reference, status, amount, paid_at
-- FROM payments ORDER BY id DESC LIMIT 20;
