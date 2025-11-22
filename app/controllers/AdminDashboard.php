<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminDashboard extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library(['session','database']);
        $this->call->model(['ProductModel', 'OrderModel', 'CustomerModel']);
    }

    public function index()
    {
        // Total sales (SUM of orders.total)
        $salesResult = $this->db->table('orders')->select('SUM(total) as total_sales')->get();
        $totalSales = $salesResult ? (float)($salesResult['total_sales'] ?? 0) : 0;

            // Minimal auth guard
            $this->call->library('session');
            if (!$this->session->userdata('user')) {
                redirect('login');
                return;
            }
        // Low stock items (COUNT of products WHERE stock < low_stock_threshold)
        $lowStockStmt = $this->db->raw("SELECT COUNT(*) as low_stock_count FROM products WHERE stock < low_stock_threshold");
        $lowStockRow = $lowStockStmt->fetch(PDO::FETCH_ASSOC);
        $lowStockCount = $lowStockRow ? (int)($lowStockRow['low_stock_count'] ?? 0) : 0;

        // Total products (COUNT of products)
        $totalProducts = $this->db->table('products')->count();

        // Orders today (COUNT of orders WHERE DATE(created_at) = CURDATE())
        $ordersTodayStmt = $this->db->raw("SELECT COUNT(*) as orders_today FROM orders WHERE DATE(created_at) = CURDATE()");
        $ordersTodayRow = $ordersTodayStmt->fetch(PDO::FETCH_ASSOC);
        $ordersToday = $ordersTodayRow ? (int)($ordersTodayRow['orders_today'] ?? 0) : 0;

        // Weekly sales data (last 7 days: Mon-Sun)
        $weeklySalesStmt = $this->db->raw("
            SELECT DAYNAME(created_at) as day_name, SUM(total) as day_total
            FROM orders
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DAYNAME(created_at), DAYOFWEEK(created_at)
            ORDER BY DAYOFWEEK(created_at)
        ");
        $weeklySalesRaw = $weeklySalesStmt->fetchAll(PDO::FETCH_ASSOC);
        $weeklySales = ['Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0];
        if ($weeklySalesRaw) {
            foreach ($weeklySalesRaw as $row) {
                $day = substr($row['day_name'], 0, 3);
                if (isset($weeklySales[$day])) {
                    $weeklySales[$day] = (float)$row['day_total'];
                }
            }
        }

        // Sales by category (pie chart data)
        $categorySalesStmt = $this->db->raw("
            SELECT p.category, SUM(oi.subtotal) as category_total
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE p.category IS NOT NULL AND p.category != ''
            GROUP BY p.category
            ORDER BY category_total DESC
            LIMIT 5
        ");
        $categorySalesRaw = $categorySalesStmt->fetchAll(PDO::FETCH_ASSOC);
        $categorySales = [];
        if ($categorySalesRaw) {
            foreach ($categorySalesRaw as $row) {
                $categorySales[$row['category']] = (float)$row['category_total'];
            }
        }

        // Recent orders (last 4 orders)
        $recentOrdersStmt = $this->db->raw("
            SELECT o.id, o.total, o.status, c.name as customer_name
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            ORDER BY o.created_at DESC
            LIMIT 4
        ");
        $recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Low stock alerts (products with stock <= low_stock_threshold)
        $lowStockAlertsStmt = $this->db->raw("
            SELECT id, name, stock, low_stock_threshold
            FROM products
            WHERE stock <= low_stock_threshold
            ORDER BY stock ASC
            LIMIT 4
        ");
        $lowStockAlerts = $lowStockAlertsStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'total_sales' => $totalSales,
            'low_stock_count' => $lowStockCount,
            'total_products' => $totalProducts,
            'orders_today' => $ordersToday,
            'weekly_sales' => $weeklySales,
            'category_sales' => $categorySales,
            'recent_orders' => $recentOrders,
            'low_stock_alerts' => $lowStockAlerts,
        ];

        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/dashboard', $data);
    }
}

?>
