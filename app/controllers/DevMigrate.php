<?php
defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed');

class DevMigrate extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('migration');
    }

    public function migrate()
    {
        $this->migration->migrate();
        echo 'Migrations executed.';
    }

    public function seed()
    {
        // Ensure DB is loaded
        $this->call->library('database');

        // Idempotency: seed only if users table is empty
        $hasUsers = (int) $this->db->table('users')->count();
        if ($hasUsers > 0) {
            $this->io->set_status_code(200);
            return $this->io->send_json(['status' => 'ok', 'message' => 'Database already has data. Skipping seeding.']);
        }

        $now = date('Y-m-d H:i:s');

        // Settings
        if ((int) $this->db->table('settings')->count() === 0) {
            $this->db->table('settings')->insert([
                'store_name' => 'TechTrack Store',
                'tax_rate' => 12.00,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Users (normalized enums + status)
        $adminId = $this->db->table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => $now,
        ]);

        // Customers
        $customers = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'phone' => '09171234567', 'total_orders' => 0, 'total_spent' => 0.00, 'is_vip' => false, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'phone' => '09182345678', 'total_orders' => 0, 'total_spent' => 0.00, 'is_vip' => false, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Carlos Reyes', 'email' => 'carlos.reyes@example.com', 'phone' => '09991234567', 'total_orders' => 0, 'total_spent' => 0.00, 'is_vip' => false, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('customers')->bulk_insert($customers);

        // Fetch inserted customers (simple way: query first 3 by id asc)
        $custRows = $this->db->table('customers')->select('*')->order_by('id ASC')->limit(3)->get_all();
        $cust1 = $custRows[0]['id'] ?? null;
        $cust2 = $custRows[1]['id'] ?? null;

        // Customer addresses
        $custAddrs = [];
        if ($cust1) {
            $custAddrs[] = [
                'customer_id' => $cust1,
                'address_type' => 'shipping',
                'line1' => '123 Mabuhay St',
                'line2' => 'Brgy. Maligaya',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'postal_code' => '1100',
                'country' => 'Philippines',
                'is_default' => true,
            ];
        }
        if ($cust2) {
            $custAddrs[] = [
                'customer_id' => $cust2,
                'address_type' => 'shipping',
                'line1' => '456 Bayanihan Ave',
                'line2' => null,
                'city' => 'Makati',
                'province' => 'Metro Manila',
                'postal_code' => '1200',
                'country' => 'Philippines',
                'is_default' => true,
            ];
        }
        if (!empty($custAddrs)) {
            $this->db->table('customer_addresses')->bulk_insert($custAddrs);
        }

        // Products
        $products = [
            ['name' => 'Wireless Mouse', 'sku' => 'WM-1001', 'price' => 599.00, 'sale_price' => 549.00, 'discount' => 8.35, 'stock' => 50, 'category' => 'Peripherals', 'brand' => 'LogiTech', 'description' => 'Ergonomic wireless mouse with long battery life', 'low_stock_threshold' => 5, 'reorder_point' => 10, 'rating' => 4.5, 'review_count' => 24, 'featured' => true, 'is_new' => false, 'tags' => 'mouse,wireless,peripherals', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mechanical Keyboard', 'sku' => 'MK-2002', 'price' => 2499.00, 'sale_price' => 2299.00, 'discount' => 8.00, 'stock' => 30, 'category' => 'Peripherals', 'brand' => 'KeyMaster', 'description' => 'RGB backlit mechanical keyboard', 'low_stock_threshold' => 5, 'reorder_point' => 10, 'rating' => 4.7, 'review_count' => 31, 'featured' => true, 'is_new' => true, 'tags' => 'keyboard,mechanical,rgb', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '24" LED Monitor', 'sku' => 'MN-2401', 'price' => 6999.00, 'sale_price' => 6599.00, 'discount' => 5.72, 'stock' => 20, 'category' => 'Displays', 'brand' => 'ViewPro', 'description' => '24-inch Full HD monitor', 'low_stock_threshold' => 3, 'reorder_point' => 6, 'rating' => 4.3, 'review_count' => 12, 'featured' => false, 'is_new' => false, 'tags' => 'monitor,display,1080p', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'USB-C Cable', 'sku' => 'CB-UC01', 'price' => 199.00, 'sale_price' => 159.00, 'discount' => 20.10, 'stock' => 100, 'category' => 'Accessories', 'brand' => 'CableX', 'description' => 'Durable USB-C to USB-A cable', 'low_stock_threshold' => 10, 'reorder_point' => 20, 'rating' => 4.2, 'review_count' => 44, 'featured' => false, 'is_new' => false, 'tags' => 'cable,usb-c,accessory', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Portable SSD 1TB', 'sku' => 'SSD-1TB', 'price' => 5999.00, 'sale_price' => 5699.00, 'discount' => 5.00, 'stock' => 15, 'category' => 'Storage', 'brand' => 'FastDisk', 'description' => 'High-speed portable SSD', 'low_stock_threshold' => 2, 'reorder_point' => 4, 'rating' => 4.8, 'review_count' => 18, 'featured' => true, 'is_new' => false, 'tags' => 'ssd,storage,portable', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('products')->bulk_insert($products);

        // Get products back to map IDs
        $prodRows = $this->db->table('products')->select('*')->order_by('id ASC')->limit(5)->get_all();
        $productIds = array_column($prodRows, 'id');

        // Product images (sample URLs)
        $images = [];
        foreach ($prodRows as $i => $p) {
            $images[] = ['product_id' => $p['id'], 'image_url' => '/public/images/sample_' . ($i+1) . '.jpg', 'is_main' => true];
        }
        $this->db->table('product_images')->bulk_insert($images);

        // Product specs
        $specs = [];
        foreach ($prodRows as $p) {
            if (stripos($p['name'], 'Keyboard') !== false) {
                $specs[] = ['product_id' => $p['id'], 'spec_name' => 'Switch Type', 'spec_value' => 'Blue'];
            } elseif (stripos($p['name'], 'Monitor') !== false) {
                $specs[] = ['product_id' => $p['id'], 'spec_name' => 'Resolution', 'spec_value' => '1920x1080'];
                $specs[] = ['product_id' => $p['id'], 'spec_name' => 'Panel', 'spec_value' => 'IPS'];
            } elseif (stripos($p['name'], 'SSD') !== false) {
                $specs[] = ['product_id' => $p['id'], 'spec_name' => 'Capacity', 'spec_value' => '1TB'];
                $specs[] = ['product_id' => $p['id'], 'spec_name' => 'Interface', 'spec_value' => 'USB 3.2'];
            }
        }
        if (!empty($specs)) $this->db->table('product_specs')->bulk_insert($specs);

        // Inventory Transactions (initial stock)
        $inv = [];
        foreach ($prodRows as $p) {
            $inv[] = [
                'product_id' => $p['id'],
                'type' => 'add',
                'transaction_type' => 'add',
                'quantity' => (int) $p['stock'],
                'reason' => 'restock',
                'notes' => 'Initial stock seeding',
                'performed_by' => $adminId,
                'timestamp' => $now,
                'created_at' => $now,
            ];
        }
        if (!empty($inv)) {
            $this->db->table('inventory_transactions')->bulk_insert($inv);
        }

        // Orders and Order Items
        // Order 1 for customer 1
        $order1_items = [
            ['product' => $prodRows[0], 'qty' => 2], // Wireless Mouse
            ['product' => $prodRows[3], 'qty' => 3], // USB-C Cable
        ];
        $subtotal1 = 0;
        foreach ($order1_items as $it) {
            $subtotal1 += $it['product']['price'] * $it['qty'];
        }
        $tax1 = round($subtotal1 * 0.12, 2);
        $total1 = $subtotal1 + $tax1;
        $order1Id = $this->db->table('orders')->insert([
            'customer_id' => $cust1,
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
            'customer_phone' => '09171234567',
            'order_code' => 'ORD-1001',
            'status' => 'processing',
            'subtotal' => $subtotal1,
            'tax' => $tax1,
            'shipping' => 100.00,
            'discount' => 0,
            'total' => $total1,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'tracking_number' => 'TRK-1001',
            'courier' => 'LBC',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $items1 = [];
        foreach ($order1_items as $it) {
            $items1[] = [
                'order_id' => $order1Id,
                'product_id' => $it['product']['id'],
                'product_name' => $it['product']['name'],
                'price' => $it['product']['price'],
                'subtotal' => $it['product']['price'] * $it['qty'],
                'quantity' => $it['qty'],
            ];
        }
        $this->db->table('order_items')->bulk_insert($items1);

        // Shipping address for order1
        $this->db->table('shipping_addresses')->insert([
            'order_id' => $order1Id,
            'line1' => '123 Mabuhay St',
            'line2' => 'Brgy. Maligaya',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'country' => 'Philippines',
        ]);

        // Order 2 for customer 2
        $order2_items = [
            ['product' => $prodRows[1], 'qty' => 1], // Mechanical Keyboard
            ['product' => $prodRows[2], 'qty' => 1], // 24" LED Monitor
        ];
        $subtotal2 = 0;
        foreach ($order2_items as $it) {
            $subtotal2 += $it['product']['price'] * $it['qty'];
        }
        $tax2 = round($subtotal2 * 0.12, 2);
        $total2 = $subtotal2 + $tax2;
        $order2Id = $this->db->table('orders')->insert([
            'customer_id' => $cust2,
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane.smith@example.com',
            'customer_phone' => '09182345678',
            'order_code' => 'ORD-1002',
            'status' => 'pending',
            'subtotal' => $subtotal2,
            'tax' => $tax2,
            'shipping' => 150.00,
            'discount' => 0,
            'total' => $total2,
            'payment_method' => 'gcash',
            'payment_status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $items2 = [];
        foreach ($order2_items as $it) {
            $items2[] = [
                'order_id' => $order2Id,
                'product_id' => $it['product']['id'],
                'product_name' => $it['product']['name'],
                'price' => $it['product']['price'],
                'subtotal' => $it['product']['price'] * $it['qty'],
                'quantity' => $it['qty'],
            ];
        }
        $this->db->table('order_items')->bulk_insert($items2);

        // Shipping address for order2
        $this->db->table('shipping_addresses')->insert([
            'order_id' => $order2Id,
            'line1' => '456 Bayanihan Ave',
            'line2' => null,
            'city' => 'Makati',
            'province' => 'Metro Manila',
            'postal_code' => '1200',
            'country' => 'Philippines',
        ]);

        // Alerts
        $alerts = [
            ['title' => 'System initialized', 'message' => 'Initial data seeded.', 'type' => 'system', 'alert_type' => 'system', 'severity' => 'info', 'product_id' => null, 'order_id' => null, 'is_read' => false, 'is_dismissed' => false, 'created_at' => $now],
            ['title' => 'New order received', 'message' => 'Order ORD-1001 has been placed.', 'type' => 'order', 'alert_type' => 'new_order', 'severity' => 'info', 'product_id' => null, 'order_id' => $order1Id, 'is_read' => false, 'is_dismissed' => false, 'created_at' => $now],
            ['title' => 'Low stock warning', 'message' => 'USB-C Cable approaching low stock threshold.', 'type' => 'low_stock', 'alert_type' => 'low_stock', 'severity' => 'warning', 'product_id' => $prodRows[3]['id'] ?? null, 'order_id' => null, 'is_read' => false, 'is_dismissed' => false, 'created_at' => $now],
        ];
        $this->db->table('alerts')->bulk_insert($alerts);

        $this->io->set_status_code(201);
        return $this->io->send_json(['status' => 'ok', 'message' => 'Sample data inserted successfully.', 'admin_login' => ['email' => 'admin@example.com', 'password' => 'admin123']]);
    }
}

?>