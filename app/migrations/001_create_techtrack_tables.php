<?php
class Create_techtrack_tables
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
        // users
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'ENUM("Admin","Manager","Cashier")'],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users', TRUE);

        // customers
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('customers', TRUE);

        // products
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'sku' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'price' => ['type' => 'DECIMAL(10,2)'],
            'stock' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'category' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'brand' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('products', TRUE);

        // orders
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'customer_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE],
            'status' => ['type' => 'ENUM("pending","processing","shipped","delivered","cancelled")', 'default' => 'pending'],
            'subtotal' => ['type' => 'DECIMAL(10,2)', 'default' => 0],
            'tax' => ['type' => 'DECIMAL(10,2)', 'default' => 0],
            'discount' => ['type' => 'DECIMAL(10,2)', 'default' => 0],
            'total' => ['type' => 'DECIMAL(10,2)', 'default' => 0],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('orders', TRUE);

        // order_items
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'order_id' => ['type' => 'INT', 'constraint' => 11],
            'product_id' => ['type' => 'INT', 'constraint' => 11],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'price' => ['type' => 'DECIMAL(10,2)'],
            'quantity' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_items', TRUE);

        // inventory_transactions
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'product_id' => ['type' => 'INT', 'constraint' => 11],
            'type' => ['type' => 'ENUM("add","remove")'],
            'quantity' => ['type' => 'INT', 'constraint' => 11],
            'reason' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'notes' => ['type' => 'TEXT', 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('inventory_transactions', TRUE);

        // alerts
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'message' => ['type' => 'TEXT', 'null' => TRUE],
            'type' => ['type' => 'ENUM("low_stock","order","system","critical")', 'default' => 'system'],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('alerts', TRUE);

        // settings
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'store_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE],
            'tax_rate' => ['type' => 'DECIMAL(5,2)', 'default' => 12.00],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('settings', TRUE);

        // Add foreign keys via raw SQL (DBForge may not support FKs directly)
        $this->_lava->db->raw("ALTER TABLE orders ADD CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL");
        $this->_lava->db->raw("ALTER TABLE order_items ADD CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE");
        $this->_lava->db->raw("ALTER TABLE order_items ADD CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL");
        $this->_lava->db->raw("ALTER TABLE inventory_transactions ADD CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE");
    }

    public function down()
    {
        $this->dbforge->drop_table('inventory_transactions', TRUE);
        $this->dbforge->drop_table('order_items', TRUE);
        $this->dbforge->drop_table('orders', TRUE);
        $this->dbforge->drop_table('products', TRUE);
        $this->dbforge->drop_table('customers', TRUE);
        $this->dbforge->drop_table('alerts', TRUE);
        $this->dbforge->drop_table('settings', TRUE);
        $this->dbforge->drop_table('users', TRUE);
    }
}
