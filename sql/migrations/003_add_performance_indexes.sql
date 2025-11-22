-- TechTrack Performance Indexes Migration
-- Run this SQL script in phpMyAdmin or MySQL client
-- Database: techtrack_db

USE techtrack_db;

-- Products table indexes
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_category (category);
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_stock (stock);
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_sku (sku);

-- Orders table indexes
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_payment_status (payment_status);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_created_at (created_at);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_customer_email (customer_email);

-- Order items table indexes
ALTER TABLE order_items ADD INDEX IF NOT EXISTS idx_product_id (product_id);

-- Inventory transactions table indexes
ALTER TABLE inventory_transactions ADD INDEX IF NOT EXISTS idx_product_id (product_id);
ALTER TABLE inventory_transactions ADD INDEX IF NOT EXISTS idx_transaction_type (transaction_type);
ALTER TABLE inventory_transactions ADD INDEX IF NOT EXISTS idx_timestamp (timestamp);

-- Alerts table indexes
ALTER TABLE alerts ADD INDEX IF NOT EXISTS idx_type (type);
ALTER TABLE alerts ADD INDEX IF NOT EXISTS idx_severity (severity);
ALTER TABLE alerts ADD INDEX IF NOT EXISTS idx_is_read (is_read);
ALTER TABLE alerts ADD INDEX IF NOT EXISTS idx_product_id (product_id);

-- Verify indexes were created
SHOW INDEX FROM products;
SHOW INDEX FROM orders;
SHOW INDEX FROM order_items;
SHOW INDEX FROM inventory_transactions;
SHOW INDEX FROM alerts;

SELECT 'Performance indexes added successfully!' AS Status;
