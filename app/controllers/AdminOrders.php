<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminOrders extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['OrderModel','OrderItemModel','CustomerModel']);
    }

    public function index()
    {
        // Get all orders with customer details
        $ordersQuery = $this->db->raw("
            SELECT 
                o.*,
                COALESCE(c.name, o.customer_name) as customer_name,
                COALESCE(c.email, o.customer_email) as customer_email,
                COALESCE(c.phone, o.customer_phone) as customer_phone
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            ORDER BY o.created_at DESC
        ");
        $data['orders'] = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $this->call->library('session');
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/orders/index', $data);
    }

    public function view($id)
    {
        // Load QR code helper
        $this->call->helper('qrcode');
        
        $data['order'] = $this->OrderModel->find($id);
        $data['items'] = $this->db->table('order_items')->where('order_id', $id)->get_all();
        // shipping address (if any)
        $data['shipping'] = $this->db->table('shipping_addresses')->where('order_id', $id)->get();
        
        // Generate QR code for order tracking
        $data['order_qr_code'] = order_tracking_qr_code($id, 150);
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/orders/view', $data);
    }

    public function update_status($id)
    {
        $status = $_POST['status'] ?? 'pending';
        $this->OrderModel->update($id, ['status' => $status]);
        redirect('admin/orders');
    }
}

?>
