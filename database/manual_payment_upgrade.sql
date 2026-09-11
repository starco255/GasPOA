-- GasPOA payment upgrade (safe to run manually on MySQL/MariaDB).
-- It only ADDS new columns/tables; it does not drop or alter existing data.

DELIMITER $$
CREATE PROCEDURE gaspoa_add_column_if_missing(IN table_name_in VARCHAR(64), IN column_name_in VARCHAR(64), IN definition_in TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = table_name_in AND column_name = column_name_in
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', table_name_in, '` ADD COLUMN `', column_name_in, '` ', definition_in);
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
  END IF;
END$$
DELIMITER ;

CALL gaspoa_add_column_if_missing('business_profiles', 'accept_mpesa', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL gaspoa_add_column_if_missing('business_profiles', 'accept_tigopesa', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL gaspoa_add_column_if_missing('business_profiles', 'accept_airtelmoney', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL gaspoa_add_column_if_missing('business_profiles', 'accept_halopesa', 'TINYINT(1) NOT NULL DEFAULT 0');

CALL gaspoa_add_column_if_missing('retail_orders', 'payment_method_provider', 'VARCHAR(30) NULL');
CALL gaspoa_add_column_if_missing('wholesale_orders', 'payment_method', 'VARCHAR(30) NULL');
CALL gaspoa_add_column_if_missing('wholesale_orders', 'transaction_reference', 'VARCHAR(100) NULL');

CREATE TABLE IF NOT EXISTS `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_type` ENUM('retail','wholesale') NOT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `payment_method` ENUM('mpesa','tigopesa','airtelmoney','halopesa','bank') NOT NULL,
  `provider` VARCHAR(30) NOT NULL DEFAULT 'manual',
  `provider_reference` VARCHAR(100) NOT NULL,
  `provider_payment_reference` VARCHAR(100) NULL,
  `checkout_url` TEXT NULL,
  `status` ENUM('pending','success','failed','reversed') NOT NULL DEFAULT 'pending',
  `amount` DECIMAL(10,2) NOT NULL,
  `raw_callback_payload` JSON NULL,
  `paid_at` TIMESTAMP NULL,
  `failed_at` TIMESTAMP NULL,
  `failure_reason` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_provider_reference_unique` (`provider_reference`),
  KEY `payments_order_type_order_id_index` (`order_type`,`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `payouts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id` BIGINT UNSIGNED NOT NULL,
  `business_profile_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `commission_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `provider_reference` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payouts_payment_id_unique` (`payment_id`),
  KEY `payouts_business_profile_id_index` (`business_profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Additive security audit table. It keeps a durable history without modifying
-- any pre-existing business, user or payment table.
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
  KEY `security_audit_logs_event_type_occurred_at_index` (`event_type`,`occurred_at`),
  KEY `security_audit_logs_subject_user_id_occurred_at_index` (`subject_user_id`,`occurred_at`),
  KEY `security_audit_logs_actor_user_id_occurred_at_index` (`actor_user_id`,`occurred_at`),
  KEY `security_audit_logs_severity_occurred_at_index` (`severity`,`occurred_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Also upgrades an existing payments table created by an earlier version.
CALL gaspoa_add_column_if_missing('payments', 'provider', 'VARCHAR(30) NOT NULL DEFAULT ''manual''');
CALL gaspoa_add_column_if_missing('payments', 'provider_payment_reference', 'VARCHAR(100) NULL');
CALL gaspoa_add_column_if_missing('payments', 'checkout_url', 'TEXT NULL');
CALL gaspoa_add_column_if_missing('payments', 'paid_at', 'TIMESTAMP NULL');
CALL gaspoa_add_column_if_missing('payments', 'failed_at', 'TIMESTAMP NULL');
CALL gaspoa_add_column_if_missing('payments', 'failure_reason', 'VARCHAR(255) NULL');

DROP PROCEDURE gaspoa_add_column_if_missing;

-- Optional data cleanup: enable a provider only after its Lipa Namba is filled.
UPDATE business_profiles SET accept_mpesa = 1 WHERE mpesa_number IS NOT NULL AND mpesa_number <> '';
UPDATE business_profiles SET accept_tigopesa = 1 WHERE mixx_number IS NOT NULL AND mixx_number <> '';
UPDATE business_profiles SET accept_airtelmoney = 1 WHERE airtel_number IS NOT NULL AND airtel_number <> '';
UPDATE business_profiles SET accept_halopesa = 1 WHERE halopesa_number IS NOT NULL AND halopesa_number <> '';
