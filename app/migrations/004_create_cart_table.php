<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Migration: Create Cart Table
 */
class Migration_004_create_cart_table extends Migration
{
    public function up()
    {
        // Create cart table using raw SQL
        $this->db->raw("
            CREATE TABLE IF NOT EXISTS cart (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(100) NULL DEFAULT NULL,
                customer_id INT(11) UNSIGNED NULL DEFAULT NULL,
                product_id INT(11) UNSIGNED NOT NULL,
                quantity INT(11) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_session_id (session_id),
                INDEX idx_customer_id (customer_id),
                INDEX idx_product_id (product_id),
                UNIQUE KEY unique_cart_item (session_id, customer_id, product_id),
                CONSTRAINT fk_cart_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        echo "Cart table created successfully.\n";
    }

    public function down()
    {
        $this->db->raw("DROP TABLE IF EXISTS cart");
        echo "Cart table dropped.\n";
    }
}
