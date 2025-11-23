<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Migration: Create Customer Addresses Table
 */
class Migration_006_create_customer_addresses_table extends Migration
{
    public function up()
    {
        // Create customer_addresses table using raw SQL
        $this->db->raw("
            ALTER TABLE customer_addresses
            ADD CONSTRAINT fk_address_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        ");
        
        echo "Customer addresses table created successfully.\n";
    }

    public function down()
    {
        $this->db->raw("DROP TABLE IF EXISTS customer_addresses");
        echo "Customer addresses table dropped.\n";
    }
}
