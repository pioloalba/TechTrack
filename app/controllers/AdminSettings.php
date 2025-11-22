<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminSettings extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Load settings from database (fallback defaults)
        $row = null;
        try {
            $row = $this->db->table('settings')->order_by('id ASC')->limit(1)->get();
        } catch (Exception $e) {
            // Table might not exist yet; ignore and use defaults
        }
        $defaults = [
            'store_name' => 'TechTrack Store',
            'email' => 'info@techtrack.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Tech Street, San Francisco, CA 94102',
            'currency' => 'USD',
            'low_stock_threshold' => 5,
            'critical_stock_threshold' => 2,
            'tax_rate' => 8.00,
            'profit_margin' => 30.00,
            'include_tax' => true,
            'receipt_header' => 'Thank you for shopping with us!',
            'receipt_footer' => 'Visit us again at techtrack.com',
            'auto_print_receipt' => true,
            'track_serial' => false,
            'store_address' => '',
            'store_lat' => null,
            'store_lng' => null,
        ];
        $data['settings'] = array_merge($defaults, $row ?: []);
        
        $this->call->library('session');
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/settings/index', $data);
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->call->library('session');

            // Ensure settings table and columns exist (idempotent)
            try {
                $this->db->raw("CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    store_name VARCHAR(255) NULL,
                    email VARCHAR(255) NULL,
                    phone VARCHAR(100) NULL,
                    currency VARCHAR(10) NULL,
                    address VARCHAR(500) NULL,
                    low_stock_threshold INT NULL,
                    critical_stock_threshold INT NULL,
                    tax_rate DECIMAL(5,2) NULL,
                    profit_margin DECIMAL(5,2) NULL,
                    include_tax TINYINT(1) DEFAULT 0,
                    receipt_header VARCHAR(255) NULL,
                    receipt_footer VARCHAR(255) NULL,
                    auto_print_receipt TINYINT(1) DEFAULT 0,
                    track_serial TINYINT(1) DEFAULT 0,
                    store_address TEXT NULL,
                    store_lat DECIMAL(10,6) NULL,
                    store_lng DECIMAL(10,6) NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (Exception $e) {}

            // Ensure all columns referenced by payload exist (older DBs may have a minimal schema)
            $columns = [
                'store_name VARCHAR(255) NULL',
                'email VARCHAR(255) NULL',
                'phone VARCHAR(100) NULL',
                'currency VARCHAR(10) NULL',
                'address VARCHAR(500) NULL',
                'low_stock_threshold INT NULL',
                'critical_stock_threshold INT NULL',
                'tax_rate DECIMAL(5,2) NULL',
                'profit_margin DECIMAL(5,2) NULL',
                'include_tax TINYINT(1) DEFAULT 0',
                'receipt_header VARCHAR(255) NULL',
                'receipt_footer VARCHAR(255) NULL',
                'auto_print_receipt TINYINT(1) DEFAULT 0',
                'track_serial TINYINT(1) DEFAULT 0',
                'store_address TEXT NULL',
                'store_lat DECIMAL(10,6) NULL',
                'store_lng DECIMAL(10,6) NULL',
                'created_at DATETIME NULL',
                'updated_at DATETIME NULL'
            ];
            foreach ($columns as $colDef) {
                $name = trim(strtok($colDef, ' '));
                try { $this->db->raw("ALTER TABLE settings ADD COLUMN IF NOT EXISTS $colDef"); } catch (Exception $e) {}
                try { $this->db->raw("ALTER TABLE settings ADD COLUMN $colDef"); } catch (Exception $e) {}
            }

            // Collect fields
            $payload = [
                'store_name' => $_POST['store_name'] ?? null,
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'currency' => $_POST['currency'] ?? null,
                'address' => $_POST['address'] ?? null,
                'low_stock_threshold' => (int)($_POST['low_stock_threshold'] ?? 5),
                'critical_stock_threshold' => (int)($_POST['critical_stock_threshold'] ?? 2),
                'tax_rate' => (float)($_POST['tax_rate'] ?? 8.00),
                'profit_margin' => (float)($_POST['profit_margin'] ?? 30.00),
                'include_tax' => isset($_POST['include_tax']) ? 1 : 0,
                'receipt_header' => $_POST['receipt_header'] ?? null,
                'receipt_footer' => $_POST['receipt_footer'] ?? null,
                'auto_print_receipt' => isset($_POST['auto_print_receipt']) ? 1 : 0,
                'track_serial' => isset($_POST['track_serial']) ? 1 : 0,
                'store_address' => $_POST['store_address'] ?? ($_POST['address'] ?? ''),
                'store_lat' => isset($_POST['store_lat']) && $_POST['store_lat'] !== '' ? (float)$_POST['store_lat'] : null,
                'store_lng' => isset($_POST['store_lng']) && $_POST['store_lng'] !== '' ? (float)$_POST['store_lng'] : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Upsert single row
            $existing = $this->db->table('settings')->order_by('id ASC')->limit(1)->get();
            if ($existing) {
                $this->db->table('settings')->where('id', (int)$existing['id'])->update($payload);
            } else {
                $payload['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('settings')->insert($payload);
            }

            // Handle branding uploads (logo & favicon)
            $uploadDir = rtrim(ROOT_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . PUBLIC_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            // Logo upload
            if (!empty($_FILES['logo_file']['name']) && is_uploaded_file($_FILES['logo_file']['tmp_name'])) {
                $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/svg+xml' => 'svg'];
                $mime = mime_content_type($_FILES['logo_file']['tmp_name']);
                $ext = $allowed[$mime] ?? null;
                if ($ext) {
                    // Remove prior logo variants
                    foreach (['png','jpg','jpeg','svg'] as $oldExt) {
                        $old = $uploadDir . 'logo.' . $oldExt;
                        if (is_file($old)) @unlink($old);
                    }
                    $target = $uploadDir . 'logo.' . $ext;
                    @move_uploaded_file($_FILES['logo_file']['tmp_name'], $target);
                } else {
                    $this->session->set_flashdata('error', 'Unsupported logo format. Please upload PNG, JPG, or SVG.');
                }
            }
            // Favicon upload
            if (!empty($_FILES['favicon_file']['name']) && is_uploaded_file($_FILES['favicon_file']['tmp_name'])) {
                $allowedFav = ['image/x-icon' => 'ico', 'image/vnd.microsoft.icon' => 'ico', 'image/png' => 'png'];
                $mimeF = mime_content_type($_FILES['favicon_file']['tmp_name']);
                $extF = $allowedFav[$mimeF] ?? null;
                if ($extF) {
                    foreach (['ico','png'] as $oldExt) {
                        $old = $uploadDir . 'favicon.' . $oldExt;
                        if (is_file($old)) @unlink($old);
                    }
                    $target = $uploadDir . 'favicon.' . $extF;
                    @move_uploaded_file($_FILES['favicon_file']['tmp_name'], $target);
                } else {
                    $this->session->set_flashdata('error', 'Unsupported favicon format. Please upload ICO or PNG.');
                }
            }

            $this->session->set_flashdata('success', 'Settings updated successfully!');
            redirect('admin/settings');
        }
    }
}

?>
