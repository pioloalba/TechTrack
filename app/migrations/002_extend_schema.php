<?php
class extend_schema
{
    private $_lava;
    protected $dbforge;

    public function __construct()
    {
        $this->_lava =& lava_instance();
        $this->_lava->call->dbforge();
        $this->dbforge = $this->_lava->dbforge;
    }

    public function up()
    {
        // Ensure InnoDB and utf8mb4 by raw alters where applicable

        // USERS: adjust role enum (lowercase), add status, adjust created_at
        $this->_lava->db->raw("ALTER TABLE users MODIFY role ENUM('admin','manager','cashier') DEFAULT 'admin'");
        $this->_lava->db->raw("ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') DEFAULT 'active' AFTER role");
        $this->_lava->db->raw("ALTER TABLE users MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");

        // CUSTOMERS: add extended analytics/meta columns
        $this->_lava->db->raw("ALTER TABLE customers ADD COLUMN IF NOT EXISTS total_orders INT DEFAULT 0");
        $this->_lava->db->raw("ALTER TABLE customers ADD COLUMN IF NOT EXISTS total_spent DECIMAL(12,2) DEFAULT 0.00");
        $this->_lava->db->raw("ALTER TABLE customers ADD COLUMN IF NOT EXISTS last_order_date DATETIME NULL");
        $this->_lava->db->raw("ALTER TABLE customers ADD COLUMN IF NOT EXISTS is_vip BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE customers ADD COLUMN IF NOT EXISTS notes TEXT");
        $this->_lava->db->raw("ALTER TABLE customers MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        // email unique (if column exists)
        try {
            $this->_lava->db->raw("ALTER TABLE customers ADD UNIQUE KEY IF NOT EXISTS unique_email (email)");
        } catch (Exception $e) { /* ignore if not supported */
        }

        // CUSTOMER_ADDRESSES
        $this->_lava->db->raw("CREATE TABLE IF NOT EXISTS customer_addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            address_type ENUM('shipping','billing') DEFAULT 'shipping',
            line1 VARCHAR(255) NOT NULL,
            line2 VARCHAR(255),
            city VARCHAR(100),
            province VARCHAR(100),
            postal_code VARCHAR(20),
            country VARCHAR(100) DEFAULT 'Philippines',
            is_default BOOLEAN DEFAULT FALSE,
            CONSTRAINT fk_custaddr_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // PRODUCTS: widen and add fields
        $this->_lava->db->raw("ALTER TABLE products MODIFY sku VARCHAR(50) NOT NULL");
        try {
            $this->_lava->db->raw("ALTER TABLE products ADD UNIQUE KEY IF NOT EXISTS unique_sku (sku)");
        } catch (Exception $e) { /* ignore */
        }
        $this->_lava->db->raw("ALTER TABLE products MODIFY price DECIMAL(12,2) NOT NULL");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS sale_price DECIMAL(12,2) NULL AFTER price");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS discount DECIMAL(5,2) NULL AFTER sale_price");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS description TEXT AFTER brand");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS low_stock_threshold INT DEFAULT 5");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS reorder_point INT DEFAULT 10");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.0");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS featured BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS is_new BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE products ADD COLUMN IF NOT EXISTS tags TEXT");
        $this->_lava->db->raw("ALTER TABLE products MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        $this->_lava->db->raw("ALTER TABLE products MODIFY updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

        // PRODUCT_IMAGES
        $this->_lava->db->raw("CREATE TABLE IF NOT EXISTS product_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            is_main BOOLEAN DEFAULT FALSE,
            CONSTRAINT fk_prodimg_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // PRODUCT_SPECS
        $this->_lava->db->raw("CREATE TABLE IF NOT EXISTS product_specs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            spec_name VARCHAR(100),
            spec_value VARCHAR(255),
            CONSTRAINT fk_prodspec_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ORDERS: add expanded fields
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_code VARCHAR(20) UNIQUE");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_email VARCHAR(100) NULL AFTER customer_name");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) NULL AFTER customer_email");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY subtotal DECIMAL(12,2) DEFAULT 0.00");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY tax DECIMAL(12,2) DEFAULT 0.00");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping DECIMAL(12,2) DEFAULT 0.00 AFTER tax");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY discount DECIMAL(12,2) DEFAULT 0.00");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY total DECIMAL(12,2) DEFAULT 0.00");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY payment_method ENUM('cash','gcash','paypal','card') DEFAULT 'cash'");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status ENUM('pending','paid','failed') DEFAULT 'pending' AFTER payment_method");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS tracking_number VARCHAR(100) NULL");
        $this->_lava->db->raw("ALTER TABLE orders ADD COLUMN IF NOT EXISTS courier VARCHAR(100) NULL");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        $this->_lava->db->raw("ALTER TABLE orders MODIFY updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

        // ORDER_ITEMS: add subtotal
        $this->_lava->db->raw("ALTER TABLE order_items MODIFY product_name VARCHAR(150)");
        $this->_lava->db->raw("ALTER TABLE order_items ADD COLUMN IF NOT EXISTS subtotal DECIMAL(12,2) NOT NULL AFTER price");

        // SHIPPING_ADDRESSES
        $this->_lava->db->raw("CREATE TABLE IF NOT EXISTS shipping_addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            line1 VARCHAR(255) NOT NULL,
            line2 VARCHAR(255),
            city VARCHAR(100),
            province VARCHAR(100),
            postal_code VARCHAR(20),
            country VARCHAR(100) DEFAULT 'Philippines',
            CONSTRAINT fk_shipaddr_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // INVENTORY_TRANSACTIONS: add transaction_type, reason enum, performed_by, timestamp
        $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD COLUMN IF NOT EXISTS transaction_type ENUM('add','remove') AFTER product_id");
        $this->_lava->db->raw("UPDATE inventory_transactions SET transaction_type = IFNULL(transaction_type, type)");
        $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD COLUMN IF NOT EXISTS reason ENUM('restock','sale','damage','return','adjustment') DEFAULT 'adjustment'");
        $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD COLUMN IF NOT EXISTS performed_by INT NULL");
        $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD COLUMN IF NOT EXISTS timestamp TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        // FK to users
        try {
            $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inv_user FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL");
        } catch (Exception $e) { /* maybe exists */
        }

        // ALERTS: expanded structure
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS alert_type ENUM('low_stock','out_of_stock','new_order','system') DEFAULT 'system'");
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS severity ENUM('info','warning','critical') DEFAULT 'info'");
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS product_id INT NULL");
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS order_id INT NULL");
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS is_read BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE alerts ADD COLUMN IF NOT EXISTS is_dismissed BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE alerts MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        try {
            $this->_lava->db->raw("ALTER TABLE alerts ADD CONSTRAINT fk_alert_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL");
        } catch (Exception $e) {
        }
        try {
            $this->_lava->db->raw("ALTER TABLE alerts ADD CONSTRAINT fk_alert_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL");
        } catch (Exception $e) {
        }

        // SETTINGS: expanded fields
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS store_address TEXT");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS contact_info VARCHAR(100)");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS business_hours VARCHAR(100)");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS currency VARCHAR(10) DEFAULT 'PHP'");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS theme ENUM('light','dark') DEFAULT 'light'");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS primary_color VARCHAR(20) DEFAULT '#3B82F6'");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS logo_url VARCHAR(255)");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS favicon_url VARCHAR(255)");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS auto_reorder BOOLEAN DEFAULT FALSE");
        $this->_lava->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS default_low_stock_threshold INT DEFAULT 5");
        $this->_lava->db->raw("ALTER TABLE settings MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
    }

    public function down()
    {
        // Non-destructive: only drop newly added tables; we won't revert column changes to avoid data loss
        $this->_lava->db->raw("DROP TABLE IF EXISTS product_images");
        $this->_lava->db->raw("DROP TABLE IF EXISTS product_specs");
        $this->_lava->db->raw("DROP TABLE IF EXISTS customer_addresses");
        $this->_lava->db->raw("DROP TABLE IF EXISTS shipping_addresses");
    }
}
