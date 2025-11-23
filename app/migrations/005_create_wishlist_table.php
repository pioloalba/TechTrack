<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Migration: Create Wishlist Table
 */
class Migration_005_create_wishlist_table extends Migration
{
    public function up()
    {
        // Create wishlist table using raw SQL
        $this->db->raw("
            ALTER TABLE wishlist
            ADD CONSTRAINT fk_wishlist_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            ADD CONSTRAINT fk_wishlist_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ");
        
        echo "Wishlist table created successfully.\n";
    }

    public function down()
    {
        $this->db->raw("DROP TABLE IF EXISTS wishlist");
        echo "Wishlist table dropped.\n";
    }
}
