<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminCustomers extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['CustomerModel','OrderModel']);
    }

    public function index()
    {
        // get customers with order count
        $customersQuery = $this->db->raw("
            SELECT 
                c.*,
                COUNT(o.id) as total_orders,
                SUM(o.total) as total_spent
            FROM customers c
            LEFT JOIN orders o ON c.id = o.customer_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $data['customers'] = $customersQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $this->call->library('session');
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/customers/index', $data);
    }

    public function view($id)
    {
        $data['customer'] = $this->CustomerModel->find($id);
        $data['orders'] = $this->OrderModel->table('orders')->where('customer_id', $id)->get_all();
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/customers/view', $data);
    }

    public function create()
    {
        $data = [];
        $this->call->library('session');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/customers/create', $data);
    }

    public function store()
    {
        $this->call->library('session');
        
        // Validate input
        $name = trim($this->io->post('name'));
        $email = trim($this->io->post('email'));
        $phone = trim($this->io->post('phone'));
        
        // Basic validation
        if (empty($name)) {
            $this->session->set_flashdata(['error' => 'Customer name is required']);
            redirect('admin/customers/create');
            return;
        }
        
        // Check if email already exists (if provided)
        if (!empty($email)) {
            $existing = $this->db->table('customers')->where('email', $email)->get();
            if ($existing) {
                $this->session->set_flashdata(['error' => 'Email already exists']);
                redirect('admin/customers/create');
                return;
            }
        }
        
        // Prepare data
        $data = [
            'name' => $name,
            'email' => !empty($email) ? $email : null,
            'phone' => !empty($phone) ? $phone : null,
            'total_orders' => 0,
            'total_spent' => 0.00,
            'is_vip' => 0,
            'notes' => null
        ];
        
        try {
            // Insert into database
            $result = $this->CustomerModel->insert($data);
            
            if ($result) {
                $this->session->set_flashdata(['success' => 'Customer added successfully']);
                redirect('admin/customers');
            } else {
                $this->session->set_flashdata(['error' => 'Failed to add customer. Please try again.']);
                redirect('admin/customers/create');
            }
        } catch (Exception $e) {
            // Log the error
            error_log('Customer insert error: ' . $e->getMessage());
            $this->session->set_flashdata(['error' => 'Database error: ' . $e->getMessage()]);
            redirect('admin/customers/create');
        }
    }

    public function edit($id)
    {
        $this->call->library('session');
        $data['customer'] = $this->CustomerModel->find($id);
        
        if (!$data['customer']) {
            $this->session->set_flashdata(['error' => 'Customer not found']);
            redirect('admin/customers');
            return;
        }
        
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/customers/edit', $data);
    }

    public function update($id)
    {
        $this->call->library('session');
        
        // Check if customer exists
        $customer = $this->CustomerModel->find($id);
        if (!$customer) {
            $this->session->set_flashdata(['error' => 'Customer not found']);
            redirect('admin/customers');
            return;
        }
        
        // Validate input
        $name = trim($this->io->post('name'));
        $email = trim($this->io->post('email'));
        $phone = trim($this->io->post('phone'));
        
        if (empty($name)) {
            $this->session->set_flashdata(['error' => 'Customer name is required']);
            redirect('admin/customers/edit/' . $id);
            return;
        }
        
        // Check if email already exists for another customer
        if (!empty($email)) {
            $existing = $this->db->table('customers')
                ->where('email', $email)
                ->where('id !=', $id)
                ->get();
            if ($existing) {
                $this->session->set_flashdata(['error' => 'Email already exists for another customer']);
                redirect('admin/customers/edit/' . $id);
                return;
            }
        }
        
        // Prepare data
        $data = [
            'name' => $name,
            'email' => !empty($email) ? $email : null,
            'phone' => !empty($phone) ? $phone : null
        ];
        
        // Update database
        $result = $this->CustomerModel->where('id', $id)->update($data);
        
        if ($result !== false) {
            $this->session->set_flashdata(['success' => 'Customer updated successfully']);
        } else {
            $this->session->set_flashdata(['error' => 'Failed to update customer']);
        }
        
        redirect('admin/customers');
    }

    public function delete($id)
    {
        $this->call->library('session');
        
        // Check if customer exists
        $customer = $this->CustomerModel->find($id);
        if (!$customer) {
            $this->session->set_flashdata(['error' => 'Customer not found']);
            redirect('admin/customers');
            return;
        }
        
        // Check if customer has orders
        $orders = $this->OrderModel->table('orders')->where('customer_id', $id)->get();
        if ($orders) {
            $this->session->set_flashdata(['error' => 'Cannot delete customer with existing orders']);
            redirect('admin/customers');
            return;
        }
        
        // Delete customer
        $result = $this->CustomerModel->where('id', $id)->delete();
        
        if ($result) {
            $this->session->set_flashdata(['success' => 'Customer deleted successfully']);
        } else {
            $this->session->set_flashdata(['error' => 'Failed to delete customer']);
        }
        
        redirect('admin/customers');
    }
}

?>
