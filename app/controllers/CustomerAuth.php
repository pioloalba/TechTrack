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

        // Use users table for customers
        $stmt = $this->db->raw('SELECT * FROM users WHERE email = ? AND role = ? LIMIT 1', [$email, 'Customer']);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!$user) {
            return $this->respond(false, 'Account not found. Please sign up.', 'shop/login');
        }

        $hash = $user['password'] ?? '';
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

        $this->session->set_userdata('customer', [
            'id' => (int)$user['id'],
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
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

        // Check existing user
        $existsStmt = $this->db->raw('SELECT 1 FROM users WHERE email = ? LIMIT 1', [$email]);
        $exists = $existsStmt ? $existsStmt->fetch(PDO::FETCH_ASSOC) : null;
        if ($exists) {
            return $this->respond(false, 'Email already in use. Try logging in.', 'shop/register');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->raw('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())', [$name, $email, $hash, 'Customer']);

        // Auto-login after registration
        $this->session->set_userdata('customer', [
            'id' => (int)$this->db->last_id(),
            'name' => $name,
            'email' => $email,
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
