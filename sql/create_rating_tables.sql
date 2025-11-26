-- Create migrations table if it doesn't exist
CREATE TABLE IF NOT EXISTS `migrations` (
  `version` bigint(20) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_ratings table
CREATE TABLE IF NOT EXISTS `product_ratings` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT(11) NOT NULL,
  `customer_id` INT(11) NULL COMMENT 'NULL for guest ratings',
  `session_id` VARCHAR(128) NULL COMMENT 'Track guest ratings',
  `rating` TINYINT(1) NOT NULL COMMENT '1-5 stars',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_product_ratings_product` (`product_id`),
  INDEX `idx_product_ratings_customer` (`customer_id`),
  INDEX `idx_product_ratings_session` (`session_id`),
  UNIQUE INDEX `idx_product_ratings_unique` (`product_id`, `customer_id`, `session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_reviews table
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT(11) NOT NULL,
  `customer_id` INT(11) NULL,
  `rating_id` INT(11) NOT NULL COMMENT 'Links to product_ratings',
  `customer_name` VARCHAR(150) NULL,
  `review_title` VARCHAR(255) NULL,
  `review_text` TEXT NULL,
  `verified_purchase` BOOLEAN DEFAULT FALSE,
  `helpful_count` INT(11) DEFAULT 0,
  `status` ENUM('pending','approved','rejected') DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_product_reviews_product` (`product_id`),
  INDEX `idx_product_reviews_customer` (`customer_id`),
  INDEX `idx_product_reviews_rating` (`rating_id`),
  INDEX `idx_product_reviews_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Success message
SELECT 'Rating tables created successfully!' AS message;
