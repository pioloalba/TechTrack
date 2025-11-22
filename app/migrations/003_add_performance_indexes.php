<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Migration: Add Performance Indexes
 * 
 * Adds database indexes to improve query performance for frequently accessed columns
 */
class Migration_003_add_performance_indexes extends Migration
{
    public function up()
    {
        // Products table indexes
        $this->db->raw("ALTER TABLE products ADD INDEX idx_category (category);");
        $this->db->raw("ALTER TABLE products ADD INDEX idx_stock (stock);");
        $this->db->raw("ALTER TABLE products ADD INDEX idx_sku (sku);");

        // Orders table indexes
        $this->db->raw("ALTER TABLE orders ADD INDEX idx_status (status);");
        $this->db->raw("ALTER TABLE orders ADD INDEX idx_payment_status (payment_status);");
        $this->db->raw("ALTER TABLE orders ADD INDEX idx_created_at (created_at);");
        $this->db->raw("ALTER TABLE orders ADD INDEX idx_customer_email (customer_email);");

        // Order items table indexes
        $this->db->raw("ALTER TABLE order_items ADD INDEX idx_product_id (product_id);");

        // Inventory transactions table indexes
        $this->db->raw("ALTER TABLE inventory_transactions ADD INDEX idx_product_id (product_id);");
        $this->db->raw("ALTER TABLE inventory_transactions ADD INDEX idx_transaction_type (transaction_type);");
        $this->db->raw("ALTER TABLE inventory_transactions ADD INDEX idx_timestamp (timestamp);");

        // Alerts table indexes
        $this->db->raw("ALTER TABLE alerts ADD INDEX idx_type (type);");
        $this->db->raw("ALTER TABLE alerts ADD INDEX idx_severity (severity);");
        $this->db->raw("ALTER TABLE alerts ADD INDEX idx_is_read (is_read);");
        $this->db->raw("ALTER TABLE alerts ADD INDEX idx_product_id (product_id);");

        echo "Performance indexes added successfully.\n";
    }

    public function down()
    {
        // Drop indexes in reverse order
        $this->db->raw("ALTER TABLE alerts DROP INDEX idx_product_id;");
        $this->db->raw("ALTER TABLE alerts DROP INDEX idx_is_read;");
        $this->db->raw("ALTER TABLE alerts DROP INDEX idx_severity;");
        $this->db->raw("ALTER TABLE alerts DROP INDEX idx_type;");

        $this->db->raw("ALTER TABLE inventory_transactions DROP INDEX idx_timestamp;");
        $this->db->raw("ALTER TABLE inventory_transactions DROP INDEX idx_transaction_type;");
        $this->db->raw("ALTER TABLE inventory_transactions DROP INDEX idx_product_id;");

        $this->db->raw("ALTER TABLE order_items DROP INDEX idx_product_id;");

        $this->db->raw("ALTER TABLE orders DROP INDEX idx_customer_email;");
        $this->db->raw("ALTER TABLE orders DROP INDEX idx_created_at;");
        $this->db->raw("ALTER TABLE orders DROP INDEX idx_payment_status;");
        $this->db->raw("ALTER TABLE orders DROP INDEX idx_status;");

        $this->db->raw("ALTER TABLE products DROP INDEX idx_sku;");
        $this->db->raw("ALTER TABLE products DROP INDEX idx_stock;");
        $this->db->raw("ALTER TABLE products DROP INDEX idx_category;");

        echo "Performance indexes removed successfully.\n";
    }
}
