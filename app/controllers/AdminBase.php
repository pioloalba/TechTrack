<?php
defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed');

/**
 * AdminBase
 *
 * Shared base controller for all Admin pages. Enforces authentication
 * and role-based access control (RBAC).
 *
 * Rules:
 * - All users must be authenticated (session 'user').
 * - Role 'Cashier' may only access AdminPOS and AdminOrders pages.
 *   Any attempt to access other admin pages will return a 403 Access Denied page (or JSON for AJAX).
 * - Roles 'Admin' and 'Manager' have full access.
 */
class AdminBase extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // session library available
        $this->call->library(['session']);

        // login require or auth
        $user = $this->session->userdata('user');
        if (!$user) {
            // Redirect to admin login page instead of generic login
            redirect('admin-login');
            exit;
        }

        // RBAC: restrict Cashier
        $role = strtolower($user['role'] ?? '');
        if ($role === 'cashier') {
            // only allow POS and orders in admin area
            $allowed = ['AdminPOS', 'AdminOrders'];
            $current = get_class($this);
            if (!in_array($current, $allowed, true)) {
                // access denied for cashier to other admin pages
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                http_response_code(403);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Access denied',
                        'message' => 'You do not have permission to access this page.',
                    ]);
                    exit;
                } else {
                    $data = [
                        'heading' => 'Access Denied',
                        'message' => 'You do not have permission to access this page. Please use POS or Orders.',
                    ];
                    // Render minimal 403 page
                    $this->call->view('errors/error_403', $data);
                    exit;
                }
            }
        }
    }
}

?>