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
    }

    public function index()
    {
        $data = [];
        
        // Get total revenue
        $revenueQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE status = 'completed'");
        $revenueResult = $revenueQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_revenue'] = (float)($revenueResult['total'] ?? 0);
        
        // Get total orders
        $ordersQuery = $this->db->raw("SELECT COUNT(*) as count FROM orders");
        $ordersResult = $ordersQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_orders'] = (int)($ordersResult['count'] ?? 0);
        
        // Get average order value
        $data['average_order_value'] = $data['total_orders'] > 0 ? $data['total_revenue'] / $data['total_orders'] : 0;
        
        // Get last month revenue for comparison
        $lastMonthQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
        $lastMonthResult = $lastMonthQuery->fetch(PDO::FETCH_ASSOC);
        $lastMonthRevenue = (float)($lastMonthResult['total'] ?? 0);
        $data['revenue_growth'] = $lastMonthRevenue > 0 ? (($data['total_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        
        // Get weekly sales data for chart
        $weeklySalesQuery = $this->db->raw("
            SELECT 
                DAYNAME(created_at) as day_name,
                DAYOFWEEK(created_at) as day_num,
                SUM(total) as total
            FROM orders 
            WHERE status = 'completed' 
            AND WEEK(created_at) = WEEK(NOW())
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
        
        // Get category sales distribution
        $categorySalesQuery = $this->db->raw("
            SELECT 
                p.category,
                SUM(oi.quantity * oi.price) as total,
                COUNT(DISTINCT oi.order_id) as order_count
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed'
            GROUP BY p.category
            ORDER BY total DESC
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
        
        // Get total revenue
        $revenueQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE status = 'completed'");
        $revenueResult = $revenueQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_revenue'] = (float)($revenueResult['total'] ?? 0);
        
        // Get total orders
        $ordersQuery = $this->db->raw("SELECT COUNT(*) as count FROM orders");
        $ordersResult = $ordersQuery->fetch(PDO::FETCH_ASSOC);
        $data['total_orders'] = (int)($ordersResult['count'] ?? 0);
        
        // Get average order value
        $data['average_order_value'] = $data['total_orders'] > 0 ? $data['total_revenue'] / $data['total_orders'] : 0;
        
        // Get last month revenue for comparison
        $lastMonthQuery = $this->db->raw("SELECT SUM(total) as total FROM orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
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
            WHERE status = 'completed' 
            AND WEEK(created_at) = WEEK(NOW())
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
        
        // Get category sales distribution
        $categorySalesQuery = $this->db->raw("
            SELECT 
                p.category,
                SUM(oi.quantity * oi.price) as total,
                COUNT(DISTINCT oi.order_id) as order_count
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed'
            GROUP BY p.category
            ORDER BY total DESC
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
        
        // Generate HTML for PDF
        $html = $this->generateReportHTML($data);
        
        // Load PDF library
        $this->call->library('pdf');
        $pdfContent = $this->pdf->generateFromHTML($html, 'TechTrack_Report_' . date('Y-m-d') . '.pdf');
        
        echo $pdfContent;
    }
    
    private function generateReportHTML($data)
    {
        $html = '
        <div style="text-align: center; margin-bottom: 30px;">
            <h1>TechTrack Admin</h1>
            <h2>Reports & Analytics</h2>
            <p style="color: #666;">Generated on ' . date('F d, Y h:i A') . '</p>
        </div>
        
        <div style="margin: 30px 0;">
            <h3 style="color: #3B82F6; border-bottom: 2px solid #3B82F6; padding-bottom: 5px;">Key Metrics</h3>
            
            <div class="stat-box">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱' . number_format($data['total_revenue'], 2) . '</div>
                <div style="color: #10B981; font-size: 10pt;">+' . number_format(abs($data['revenue_growth']), 1) . '% from last month</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">' . number_format($data['total_orders']) . '</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Average Order Value</div>
                <div class="stat-value">₱' . number_format($data['average_order_value'], 2) . '</div>
            </div>
        </div>
        
        <div style="margin: 30px 0;">
            <h3 style="color: #3B82F6; border-bottom: 2px solid #3B82F6; padding-bottom: 5px;">Weekly Sales Performance</h3>
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Sales Amount</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($data['weekly_sales'] as $day => $amount) {
            $html .= '
                    <tr>
                        <td>' . $day . '</td>
                        <td>₱' . number_format($amount, 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>
        
        <div style="margin: 30px 0;">
            <h3 style="color: #3B82F6; border-bottom: 2px solid #3B82F6; padding-bottom: 5px;">Sales Distribution by Category</h3>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Sales</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($data['category_sales'] as $cat) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($cat['name']) . '</td>
                        <td>₱' . number_format($cat['total'], 2) . '</td>
                        <td>' . $cat['percentage'] . '%</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 10pt;">
            <p>This report was automatically generated by TechTrack Admin System</p>
            <p>© ' . date('Y') . ' TechTrack. All rights reserved.</p>
        </div>';
        
        return $html;
    }
}

?>
