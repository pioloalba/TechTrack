<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminReports extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['OrderModel','ProductModel','CustomerModel']);
        $this->call->helper('qrcode');
    }

    public function index()
    {
        $data = [];
        
        // Get total revenue (all orders - matching dashboard)
        $revenueQuery = $this->db->raw("SELECT SUM(total) as total FROM orders");
        $revenueResult = $revenueQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_revenue'] = (float)($revenueResult['total'] ?? 0);
        
        // Get total orders
        $ordersQuery = $this->db->raw("SELECT COUNT(*) as count FROM orders");
        $ordersResult = $ordersQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_orders'] = (int)($ordersResult['count'] ?? 0);
        
        // Get average order value
        $data['average_order_value'] = $data['total_orders'] > 0 ? $data['total_revenue'] / $data['total_orders'] : 0;
        
        // Get last month revenue for comparison
        $lastMonthQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
        $lastMonthResult = $lastMonthQuery->fetch(PDO::FETCH_ASSOC);
        $lastMonthRevenue = (float)($lastMonthResult['total'] ?? 0);
        $data['revenue_growth'] = $lastMonthRevenue > 0 ? (($data['total_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        
        // Get weekly sales data for chart (matching dashboard)
        $weeklySalesQuery = $this->db->raw("
            SELECT 
                DAYNAME(created_at) as day_name,
                DAYOFWEEK(created_at) as day_num,
                SUM(total) as total
            FROM orders 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY day_name, day_num
            ORDER BY day_num
        ");
        $weeklySales = $weeklySalesQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $data['weekly_sales'] = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0
        ];
        foreach ($weeklySales as $sale) {
            $dayName = substr($sale['day_name'], 0, 3);
            $data['weekly_sales'][$dayName] = (float)$sale['total'];
        }
        
        // Get category sales distribution (matching dashboard)
        $categorySalesQuery = $this->db->raw("
            SELECT 
                p.category,
                SUM(oi.subtotal) as total,
                COUNT(DISTINCT oi.order_id) as order_count
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            WHERE p.category IS NOT NULL AND p.category != ''
            GROUP BY p.category
            ORDER BY total DESC
            LIMIT 5
        ");
        $categorySales = $categorySalesQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $data['category_sales'] = [];
        $totalCategorySales = array_sum(array_column($categorySales, 'total'));
        
        foreach ($categorySales as $cat) {
            $data['category_sales'][] = [
                'name' => $cat['category'] ?? 'Others',
                'total' => (float)$cat['total'],
                'percentage' => $totalCategorySales > 0 ? round(((float)$cat['total'] / $totalCategorySales) * 100) : 0
            ];
        }
        
        // Get inventory data
        $data['low_stock'] = $this->getInventoryData();
        
        // Get profit analysis data
        $data['profit_data'] = $this->getProfitAnalysis();
        
        // Generate QR code for admin dashboard
        $data['admin_qr_code'] = generate_qr_code_base64(site_url('admin/dashboard'), 120);
        
        $this->call->library('session');
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/reports/index', $data);
    }
    
    public function export_pdf()
    {
        // Get the same data as index
        $data = [];
        
        // Get total revenue (matching dashboard - all orders)
        $revenueQuery = $this->db->raw("SELECT SUM(total) as total FROM orders");
        $revenueResult = $revenueQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_revenue'] = (float)($revenueResult['total'] ?? 0);
        
        // Get total orders
        $ordersQuery = $this->db->raw("SELECT COUNT(*) as count FROM orders");
        $ordersResult = $ordersQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_orders'] = (int)($ordersResult['count'] ?? 0);
        
        // Get average order value
        $data['average_order_value'] = $data['total_orders'] > 0 ? $data['total_revenue'] / $data['total_orders'] : 0;
        
        // Get last month revenue for comparison
        $lastMonthQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
        $lastMonthResult = $lastMonthQuery->fetch(PDO::FETCH_ASSOC);
        $lastMonthRevenue = (float)($lastMonthResult['total'] ?? 0);
        $data['revenue_growth'] = $lastMonthRevenue > 0 ? (($data['total_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        
        // Get weekly sales data
        $weeklySalesQuery = $this->db->raw("
            SELECT 
                DAYNAME(created_at) as day_name,
                DAYOFWEEK(created_at) as day_num,
                SUM(total) as total
            FROM orders 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY day_name, day_num
            ORDER BY day_num
        ");
        $weeklySales = $weeklySalesQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $data['weekly_sales'] = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0
        ];
        foreach ($weeklySales as $sale) {
            $dayName = substr($sale['day_name'], 0, 3);
            $data['weekly_sales'][$dayName] = (float)$sale['total'];
        }
        
        // Get category sales distribution (matching dashboard)
        $categorySalesQuery = $this->db->raw("
            SELECT 
                p.category,
                SUM(oi.subtotal) as total,
                COUNT(DISTINCT oi.order_id) as order_count
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            WHERE p.category IS NOT NULL AND p.category != ''
            GROUP BY p.category
            ORDER BY total DESC
            LIMIT 5
        ");
        $categorySales = $categorySalesQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $data['category_sales'] = [];
        $totalCategorySales = array_sum(array_column($categorySales, 'total'));
        
        foreach ($categorySales as $cat) {
            $data['category_sales'][] = [
                'name' => $cat['category'] ?? 'Others',
                'total' => (float)$cat['total'],
                'percentage' => $totalCategorySales > 0 ? round(((float)$cat['total'] / $totalCategorySales) * 100) : 0
            ];
        }
        
        // Generate HTML report with auto-print
        $html = $this->generateReportHTML($data);
        
        // Output HTML directly (browser print dialog will handle PDF conversion)
        echo $html;
        exit;
    }
    
    private function getInventoryData()
    {
        $inventoryQuery = $this->db->raw("
            SELECT 
                id,
                name,
                category,
                stock,
                low_stock_threshold,
                price,
                (stock * price) as stock_value
            FROM products
            WHERE stock <= low_stock_threshold OR stock < 10
            ORDER BY stock ASC
            LIMIT 20
        ");
        return $inventoryQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getProfitAnalysis()
    {
        // Get monthly profit data
        // Note: Using estimated 70% cost ratio since cost_price column doesn't exist
        // Formula: Estimated Cost = Revenue * 0.70, Profit = Revenue * 0.30
        $profitQuery = $this->db->raw("
            SELECT 
                DATE_FORMAT(o.created_at, '%Y-%m') as month,
                SUM(oi.subtotal) as revenue,
                ROUND(SUM(oi.subtotal) * 0.70, 2) as cost,
                ROUND(SUM(oi.subtotal) * 0.30, 2) as profit,
                COUNT(DISTINCT o.id) as order_count
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        return $profitQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function generateReportHTML($data)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TechTrack Report - ' . date('Y-m-d') . '</title>
    <style>
        @page { margin: 20mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 { 
            color: #3B82F6; 
            font-size: 24pt;
            margin-bottom: 10px;
            text-align: center;
        }
        h2 { 
            color: #666; 
            font-size: 18pt;
            margin-top: 0;
            text-align: center;
            font-weight: normal;
        }
        h3 { 
            color: #3B82F6; 
            border-bottom: 2px solid #3B82F6; 
            padding-bottom: 5px;
            margin-top: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3B82F6;
        }
        .date {
            color: #666;
            font-size: 10pt;
            margin-top: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: #f9fafb;
        }
        .stat-label {
            color: #666;
            font-size: 10pt;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 5px;
        }
        .stat-growth {
            color: #10B981;
            font-size: 9pt;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #3B82F6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 9pt;
        }
        .section {
            margin: 40px 0;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛒 TechTrack Admin</h1>
        <h2>Reports & Analytics</h2>
        <div class="date">Generated on ' . date('F d, Y h:i A') . '</div>
    </div>
    
    <div class="section">
        <h3>📊 Key Performance Metrics</h3>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱' . number_format($data['total_revenue'], 2) . '</div>
                <div class="stat-growth">+' . number_format(abs($data['revenue_growth']), 1) . '% from last month</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">' . number_format($data['total_orders']) . '</div>
                <div class="stat-growth">All time</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Average Order Value</div>
                <div class="stat-value">₱' . number_format($data['average_order_value'], 2) . '</div>
                <div class="stat-growth">Per transaction</div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h3>📈 Weekly Sales Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th style="text-align: right;">Sales Amount</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data['weekly_sales'] as $day => $amount) {
            $html .= '
                <tr>
                    <td><strong>' . $day . '</strong></td>
                    <td style="text-align: right;">₱' . number_format($amount, 2) . '</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h3>🏷️ Sales Distribution by Category</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: right;">Total Sales</th>
                    <th style="text-align: center;">Percentage</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data['category_sales'] as $cat) {
            $html .= '
                <tr>
                    <td><strong>' . htmlspecialchars($cat['name']) . '</strong></td>
                    <td style="text-align: right;">₱' . number_format($cat['total'], 2) . '</td>
                    <td style="text-align: center;">' . $cat['percentage'] . '%</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        <p><strong>This report was automatically generated by TechTrack Admin System</strong></p>
        <p>© ' . date('Y') . ' TechTrack. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 8pt;">
            Confidential - For internal use only
        </p>
    </div>
    
    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            window.print();
            // Close window after printing (optional)
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html>';
        
        return $html;
    }
}

?>
