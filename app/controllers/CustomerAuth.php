<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CustomerAuth extends Controller
{
    private $csrf;
    private $rateLimiter;
    
    public function __construct()
    {
        parent::__construct();
        $this->call->library(['session']);
        
        // Load security helpers
        require_once APP_DIR . 'helpers/csrf_helper.php';
        require_once APP_DIR . 'helpers/rate_limiter_helper.php';
        
        $this->csrf = new CSRF($this->session);
        $this->rateLimiter = new RateLimiter($this->session, 5, 15);
    }

    // GET /shop/login
    public function login()
    {
        if ($this->session->userdata('customer')) {
            redirect('shop');
            return;
        }
        $data = [];
        $data['flash_error'] = $this->session->flashdata('cust_error');
        $data['flash_success'] = $this->session->flashdata('cust_success');
        $data['csrf_token'] = $this->csrf->getToken();
        $data['csrf_field'] = $this->csrf->getInputField();
        $this->call->view('shop/auth/login', $data);
    }

    // POST /shop/login
    public function do_login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // CSRF validation - TEMPORARILY DISABLED FOR TESTING
        // TODO: Re-enable after fixing session issues
        /*
        if (!$this->csrf->validateToken()) {
            return $this->respond(false, 'Invalid security token. Please refresh and try again.', 'shop/login');
        }
        $this->csrf->generateToken();
        */

        if ($email === '' || $password === '') {
            return $this->respond(false, 'Please enter email and password.', 'shop/login');
        }
        
        // Rate limiting
        $identifier = $email . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if ($this->rateLimiter->tooManyAttempts($identifier, 'customer_login')) {
            return $this->respond(false, $this->rateLimiter->getLimitMessage($identifier, 'customer_login'), 'shop/login');
        }

        // Query customers table with password from customer_auth table
        $stmt = $this->db->raw('
            SELECT c.*, ca.password 
            FROM customers c
            LEFT JOIN customer_auth ca ON c.id = ca.customer_id
            WHERE c.email = ? 
            LIMIT 1
        ', [$email]);
        $customer = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        
        if (!$customer) {
            return $this->respond(false, 'Account not found. Please sign up.', 'shop/login');
        }

        // Check if customer has password set
        if (empty($customer['password'])) {
            return $this->respond(false, 'Account found but no password set. Please contact support.', 'shop/login');
        }

        $hash = $customer['password'];
        $ok = false;
        if (strlen($hash) >= 60 && (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$argon2'))) {
            $ok = password_verify($password, $hash);
        } else {
            $ok = hash_equals((string)$hash, $password);
        }
        if (!$ok) {
            $this->rateLimiter->hit($identifier, 'customer_login');
            $remaining = $this->rateLimiter->retriesLeft($identifier, 'customer_login');
            
            if ($remaining > 0) {
                return $this->respond(false, "Incorrect password. {$remaining} attempt(s) remaining.", 'shop/login');
            } else {
                return $this->respond(false, $this->rateLimiter->getLimitMessage($identifier, 'customer_login'), 'shop/login');
            }
        }
        
        $this->rateLimiter->clear($identifier, 'customer_login');

        $customer_id = (int)$customer['id'];
        $session_id = $this->session->session_id ?? session_id();
        
        // Load models for cart/wishlist migration
        $this->call->model(['CartModel', 'WishlistModel']);
        
        // Migrate guest cart to customer cart
        $this->CartModel->migrateGuestCart($session_id, $customer_id);
        
        // Migrate guest wishlist to customer wishlist
        $this->WishlistModel->migrateGuestWishlist($session_id, $customer_id);

        $this->session->set_userdata('customer', [
            'id' => $customer_id,
            'name' => $customer['name'] ?? '',
            'email' => $customer['email'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'role' => 'Customer',
        ]);

        return $this->respond(true, 'Welcome back!', 'shop');
    }

    // GET /shop/register
    public function register()
    {
        if ($this->session->userdata('customer')) {
            redirect('shop');
            return;
        }
        $data = [];
        $data['flash_error'] = $this->session->flashdata('cust_error');
        $data['flash_success'] = $this->session->flashdata('cust_success');
        $data['csrf_token'] = $this->csrf->getToken();
        $data['csrf_field'] = $this->csrf->getInputField();
        $this->call->view('shop/auth/register', $data);
    }

    // POST /shop/register
    public function do_register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm'] ?? '');

        // CSRF validation - TEMPORARILY DISABLED FOR TESTING
        // TODO: Re-enable after fixing session issues
        /*
        if (!$this->csrf->validateToken()) {
            return $this->respond(false, 'Invalid security token. Please refresh and try again.', 'shop/register');
        }
        $this->csrf->generateToken();
        */

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            return $this->respond(false, 'Please complete all fields.', 'shop/register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respond(false, 'Invalid email address.', 'shop/register');
        }
        if ($password !== $confirm) {
            return $this->respond(false, 'Passwords do not match.', 'shop/register');
        }
        
        // Password complexity validation
        if (strlen($password) < 8) {
            return $this->respond(false, 'Password must be at least 8 characters long.', 'shop/register');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return $this->respond(false, 'Password must contain at least one uppercase letter.', 'shop/register');
        }
        if (!preg_match('/[a-z]/', $password)) {
            return $this->respond(false, 'Password must contain at least one lowercase letter.', 'shop/register');
        }
        if (!preg_match('/[0-9]/', $password)) {
            return $this->respond(false, 'Password must contain at least one number.', 'shop/register');
        }

        // Check existing customer
        $existsStmt = $this->db->raw('SELECT 1 FROM customers WHERE email = ? LIMIT 1', [$email]);
        $exists = $existsStmt ? $existsStmt->fetch(PDO::FETCH_ASSOC) : null;
        if ($exists) {
            return $this->respond(false, 'Email already in use. Try logging in.', 'shop/register');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert into customers table
        $this->db->raw('
            INSERT INTO customers (name, email, phone, total_orders, total_spent, is_vip, created_at) 
            VALUES (?, ?, NULL, 0, 0.00, 0, NOW())
        ', [$name, $email]);
        
        $customer_id = (int)$this->db->last_id();
        
        // Insert password into customer_auth table
        $this->db->raw('
            INSERT INTO customer_auth (customer_id, password, created_at, updated_at) 
            VALUES (?, ?, NOW(), NOW())
        ', [$customer_id, $hash]);

        // Auto-login after registration
        $this->session->set_userdata('customer', [
            'id' => $customer_id,
            'name' => $name,
            'email' => $email,
            'phone' => null,
            'role' => 'Customer',
        ]);

        return $this->respond(true, 'Account created. Welcome!', 'shop');
    }

    // GET /shop/logout
    public function logout()
    {
        $this->session->unset_userdata('customer');
        $this->session->set_flashdata('cust_success', 'You have been logged out.');
        redirect('shop');
    }

    private function respond($success, $message, $redirect)
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $message,
                'redirect' => site_url($redirect)
            ]);
            exit;
        }

        if ($success) {
            $this->session->set_flashdata('cust_success', $message);
        } else {
            $this->session->set_flashdata('cust_error', $message);
        }
        redirect($redirect);
    }
}

?>
