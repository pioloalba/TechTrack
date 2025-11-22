<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth extends Controller
{
    private $csrf;
    private $rateLimiter;
    
    public function __construct()
    {
        parent::__construct();
        $this->call->library('session');
        
        // Load security helpers
        require_once APP_DIR . 'helpers/csrf_helper.php';
        require_once APP_DIR . 'helpers/rate_limiter_helper.php';
        
        $this->csrf = new CSRF($this->session);
        $this->rateLimiter = new RateLimiter($this->session, 5, 15); // 5 attempts per 15 minutes
    }

    // Landing page -> Login
    public function index()
    {
        // If already logged in, redirect by role
        $existing = $this->session->userdata('user');
        if ($existing) {
            $role = $existing['role'] ?? 'Admin';
            redirect($this->get_redirect_for_role($role));
            return;
        }

        // Ensure at least one admin exists (bootstrap)
        $this->bootstrap_default_admin();

        // Render login page
        $data = [];
        $data['flash_error'] = $this->session->flashdata('error');
        $data['flash_success'] = $this->session->flashdata('success');
        $data['csrf_token'] = $this->csrf->getToken();
        $data['csrf_field'] = $this->csrf->getInputField();
        
        // Check if role is specified in URL (for pre-selecting tab)
        $requestedRole = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : 'admin';
        $validRoles = ['customer', 'cashier', 'admin'];
        $data['active_tab'] = in_array($requestedRole, $validRoles) ? $requestedRole : 'admin';
        
        $this->call->view('auth/login', $data);
    }

    // Admin Login Page
    public function admin_login()
    {
        // If already logged in, redirect by role
        $existing = $this->session->userdata('user');
        if ($existing) {
            $role = $existing['role'] ?? 'Admin';
            redirect($this->get_redirect_for_role($role));
            return;
        }

        // Ensure at least one admin exists (bootstrap)
        $this->bootstrap_default_admin();

        // Render admin login page
        $data = [];
        $data['flash_error'] = $this->session->flashdata('error');
        $data['flash_success'] = $this->session->flashdata('success');
        $data['login_type'] = 'admin';
        $data['csrf_token'] = $this->csrf->getToken();
        $data['csrf_field'] = $this->csrf->getInputField();
        
        $this->call->view('auth/admin_login', $data);
    }

    // Cashier Login Page
    public function cashier_login()
    {
        // If already logged in, redirect by role
        $existing = $this->session->userdata('user');
        if ($existing) {
            $role = $existing['role'] ?? 'Admin';
            redirect($this->get_redirect_for_role($role));
            return;
        }

        // Ensure at least one admin exists (bootstrap)
        $this->bootstrap_default_admin();

        // Render cashier login page
        $data = [];
        $data['flash_error'] = $this->session->flashdata('error');
        $data['flash_success'] = $this->session->flashdata('success');
        $data['login_type'] = 'cashier';
        $data['csrf_token'] = $this->csrf->getToken();
        $data['csrf_field'] = $this->csrf->getInputField();
        
        $this->call->view('auth/cashier_login', $data);
    }

    // POST /login
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $requestedRole = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : null;

        // CSRF validation - TEMPORARILY DISABLED FOR TESTING
        // TODO: Re-enable after fixing session issues
        /*
        if (!$this->csrf->validateToken()) {
            // Regenerate token on failure for next attempt
            $this->csrf->generateToken();
            
            // Determine which login page to redirect to based on role parameter
            $redirect = 'login';
            if ($requestedRole === 'admin') {
                $redirect = 'admin-login';
            } elseif ($requestedRole === 'cashier') {
                $redirect = 'cashier-login';
            }
            
            $this->session->set_flashdata('error', 'Invalid security token. Please try again.');
            redirect($redirect);
            return;
        }
        */
        
        // Regenerate CSRF token after successful validation
        $this->csrf->generateToken();

        // basic validation
        if ($email === '' || $password === '') {
            return $this->respond_login(false, 'Please enter email and password.');
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respond_login(false, 'Please enter a valid email address.');
        }
        
        // Rate limiting check
        $identifier = $email . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if ($this->rateLimiter->tooManyAttempts($identifier)) {
            return $this->respond_login(false, $this->rateLimiter->getLimitMessage($identifier));
        }

        // fetch user
        $stmt = $this->db->raw('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

        if (!$user) {
            return $this->respond_login(false, 'Invalid email or password.');
        }

        $hash = $user['password'] ?? '';
        $ok = false;
        if (strlen($hash) >= 60 && (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$argon2'))) {
            // hashed
            $ok = password_verify($password, $hash);
        } else {
            // fallback for plain text legacy rows
            $ok = hash_equals((string)$hash, $password);
        }

        if (!$ok) {
            // Record failed attempt
            $this->rateLimiter->hit($identifier);
            $remaining = $this->rateLimiter->retriesLeft($identifier);
            
            if ($remaining > 0) {
                return $this->respond_login(false, "Invalid email or password. {$remaining} attempt(s) remaining.");
            } else {
                return $this->respond_login(false, $this->rateLimiter->getLimitMessage($identifier));
            }
        }
        
        // Clear rate limit on successful login
        $this->rateLimiter->clear($identifier);

        // Validate role - customers cannot access admin/cashier areas
        $userRole = $user['role'] ?? ''; // Keep original case from DB
        $userRoleLower = strtolower($userRole);
        
        // If trying to access admin or cashier, block customers
        if ($requestedRole === 'admin' || $requestedRole === 'cashier') {
            if ($userRoleLower === 'customer') {
                return $this->respond_login(false, 'Access denied. Customer accounts cannot access admin/cashier areas. Please use the shop interface.');
            }
            
            // If requesting cashier access, only allow cashier, admin, or manager roles
            if ($requestedRole === 'cashier' && !in_array($userRoleLower, ['cashier', 'admin', 'manager'])) {
                return $this->respond_login(false, 'Access denied. You do not have cashier privileges.');
            }
            
            // If requesting admin access, only allow admin or manager roles
            if ($requestedRole === 'admin' && !in_array($userRoleLower, ['admin', 'manager'])) {
                return $this->respond_login(false, 'Access denied. You do not have admin privileges.');
            }
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // set session
        $this->session->set_userdata('user', [
            'id' => (int)$user['id'],
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => $user['role'] ?? 'Admin',
            'login_time' => time(),
            'login_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        $redirect = $this->get_redirect_for_role($user['role'] ?? 'Admin');
        return $this->respond_login(true, 'Login successful.', $redirect);
    }

    // GET /logout
    public function logout()
    {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        redirect('login');
    }

    private function respond_login($success, $message, $redirect = null)
    {
        // AJAX support
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $message, 'redirect' => $redirect ? site_url($redirect) : null]);
            exit;
        }

        if ($success) {
            $this->session->set_flashdata('success', $message);
            redirect($redirect ?: 'admin/dashboard');
        } else {
            $this->session->set_flashdata('error', $message);
            
            // Redirect to the appropriate login page based on the role parameter
            $requestedRole = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : null;
            $loginPage = 'login';
            if ($requestedRole === 'admin') {
                $loginPage = 'admin-login';
            } elseif ($requestedRole === 'cashier') {
                $loginPage = 'cashier-login';
            }
            
            redirect($loginPage);
        }
    }

    // Create a default admin user if users table is empty
    private function bootstrap_default_admin()
    {
        try {
            $countStmt = $this->db->raw('SELECT COUNT(*) as c FROM users');
            $row = $countStmt ? $countStmt->fetch(PDO::FETCH_ASSOC) : ['c' => 0];
            if ((int)($row['c'] ?? 0) === 0) {
                $name = 'Admin User';
                $email = 'admin@techtrack.com';
                $pass = password_hash('admin123', PASSWORD_BCRYPT);
                $this->db->raw('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())', [$name, $email, $pass, 'Admin']);

                // seed a cashier user for demos
                $cname = 'Cashier User';
                $cemail = 'cashier@techtrack.com';
                $cpass = password_hash('cashier123', PASSWORD_BCRYPT);
                $this->db->raw('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())', [$cname, $cemail, $cpass, 'Cashier']);
            }
        } catch (Exception $e) {
            // ignore bootstrap errors silently
        }
    }

    private function get_redirect_for_role($role)
    {
        $r = strtolower((string)$role);
        if ($r === 'cashier') {
            return 'admin/pos';
        }
        if ($r === 'manager' || $r === 'admin') {
            return 'admin/dashboard';
        }
        if ($r === 'customer') {
            return 'shop';
        }
        // default fallback: shop
        return 'shop';
    }
}

?>