-- Sessions table for database-backed sessions
-- Required for cloud deployment (Render, Heroku, etc.)

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(128) NOT NULL PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` TEXT NOT NULL,
  INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
