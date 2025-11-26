<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class GoogleAuth extends Controller
{
    private $client;
    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->call->library(['session']);
        $this->call->model('CustomerModel');
        
        // Load Google OAuth config
        require_once APP_DIR . 'config/google_oauth.php';
        $this->config = $config['google_oauth'];
        
        // Initialize Google Client
        $this->initializeClient();
    }

    /**
     * Initialize Google OAuth Client
     */
    private function initializeClient()
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->config['client_id']);
        $this->client->setClientSecret($this->config['client_secret']);
        $this->client->setRedirectUri($this->config['redirect_uri']);
        $this->client->addScope($this->config['scopes']);
        
        // For localhost development only - disable SSL verification
        // TODO: Remove this in production and configure proper SSL certificates
        if (strpos($this->config['redirect_uri'], 'localhost') !== false) {
            $httpClient = new GuzzleHttp\Client([
                'verify' => false
            ]);
            $this->client->setHttpClient($httpClient);
            error_log('WARNING: SSL verification disabled for localhost development');
        }
    }

    /**
     * Initiate Google OAuth login
     * Redirects user to Google's consent screen
     */
    public function login()
    {
        // Generate and store state token for CSRF protection
        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('google_oauth_state', $state);
        $this->client->setState($state);
        
        // Get Google OAuth URL and redirect
        $authUrl = $this->client->createAuthUrl();
        redirect($authUrl);
    }

    /**
     * Handle OAuth callback from Google
     * This is called after user authorizes the app
     */
    public function callback()
    {
        // Verify state token to prevent CSRF
        $state = $this->io->get('state');
        $sessionState = $this->session->userdata('google_oauth_state');
        
        if (!$state || $state !== $sessionState) {
            $this->session->set_flashdata('cust_error', 'Invalid authentication state. Please try again.');
            redirect('shop/login');
            return;
        }
        
        // Remove state from session
        $this->session->unset_userdata('google_oauth_state');
        
        // Check for error from Google
        $error = isset($_GET['error']) ? $_GET['error'] : null;
        if ($error) {
            $this->session->set_flashdata('cust_error', 'Google authentication was cancelled or failed.');
            redirect('shop/login');
            return;
        }
        
        // Get authorization code
        $code = isset($_GET['code']) ? $_GET['code'] : null;
        if (!$code) {
            $this->session->set_flashdata('cust_error', 'No authorization code received from Google.');
            redirect('shop/login');
            return;
        }
        
        try {
            // Exchange authorization code for access token
            error_log('Attempting to fetch access token with code: ' . substr($code, 0, 20) . '...');
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                error_log('Token error: ' . print_r($token, true));
                throw new Exception('Error fetching access token: ' . $token['error']);
            }
            
            error_log('Access token received successfully');
            $this->client->setAccessToken($token);
            
            // Get user info from Google
            $google_oauth = new Google_Service_Oauth2($this->client);
            $google_account_info = $google_oauth->userinfo->get();
            
            // Extract user data
            $googleId = $google_account_info->id;
            $email = $google_account_info->email;
            $firstName = $google_account_info->givenName ?? '';
            $lastName = $google_account_info->familyName ?? '';
            $picture = $google_account_info->picture ?? '';
            
            error_log('Google user info: ' . $email . ' (ID: ' . $googleId . ')');
            
            // Process OAuth login (create or login customer)
            $this->processOAuthLogin($googleId, $email, $firstName, $lastName, $picture);
            
        } catch (Exception $e) {
            error_log('Google OAuth Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $this->session->set_flashdata('cust_error', 'Failed to authenticate with Google: ' . $e->getMessage());
            redirect('shop/login');
        }
    }

    /**
     * Process OAuth login - create account or login existing customer
     */
    private function processOAuthLogin($googleId, $email, $firstName, $lastName, $picture)
    {
        // Check if customer exists with this Google ID (if column exists)
        $customer = null;
        try {
            $stmt = $this->db->raw('SELECT * FROM customers WHERE google_id = ? LIMIT 1', [$googleId]);
            $customer = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $e) {
            // google_id column might not exist yet
            error_log('google_id column not found: ' . $e->getMessage());
        }
        
        if (!$customer) {
            // Check if email already exists (regular account)
            $stmt = $this->db->raw('SELECT * FROM customers WHERE email = ? LIMIT 1', [$email]);
            $customer = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            
            if ($customer) {
                // Link existing account with Google ID (if column exists)
                try {
                    $this->db->raw(
                        'UPDATE customers SET google_id = ? WHERE id = ?',
                        [$googleId, $customer['id']]
                    );
                } catch (Exception $e) {
                    // google_id column might not exist, continue anyway
                    error_log('Could not update google_id: ' . $e->getMessage());
                }
            } else {
                // Create new customer account
                $fullName = trim($firstName . ' ' . $lastName);
                
                try {
                    // Insert with google_id
                    $this->db->raw(
                        'INSERT INTO customers (name, email, phone, google_id, total_orders, total_spent, is_vip) 
                         VALUES (?, ?, NULL, ?, 0, 0.00, 0)',
                        [$fullName, $email, $googleId]
                    );
                } catch (Exception $e) {
                    // If google_id column doesn't exist, insert without it
                    error_log('Could not insert with google_id, trying without: ' . $e->getMessage());
                    $this->db->raw(
                        'INSERT INTO customers (name, email, phone, total_orders, total_spent, is_vip) 
                         VALUES (?, ?, NULL, 0, 0.00, 0)',
                        [$fullName, $email]
                    );
                }
                
                $customerId = (int)$this->db->last_id();
                
                $customer = [
                    'id' => $customerId,
                    'name' => $fullName,
                    'email' => $email
                ];
            }
        }
        
        // Migrate guest cart/wishlist if exists
        $this->migrateGuestData($customer['id']);
        
        // Set session data
        $this->session->set_userdata('customer', [
            'id' => $customer['id'],
            'email' => $customer['email'],
            'name' => isset($customer['name']) ? $customer['name'] : trim($firstName . ' ' . $lastName),
            'oauth_provider' => 'google'
        ]);
        
        // Success message
        $this->session->set_flashdata('cust_success', 'Welcome back, ' . $firstName . '!');
        redirect('shop');
    }

    /**
     * Migrate guest cart and wishlist to logged-in customer
     */
    private function migrateGuestData($customerId)
    {
        // Migrate cart
        $this->call->model('CartModel');
        $sessionId = $this->session->userdata('session_id');
        if ($sessionId) {
            $this->CartModel->migrateGuestCart($sessionId, $customerId);
        }
        
        // Migrate wishlist
        $this->call->model('WishlistModel');
        if ($sessionId) {
            $this->WishlistModel->migrateGuestWishlist($sessionId, $customerId);
        }
    }

    /**
     * Disconnect Google account from customer
     */
    public function disconnect()
    {
        $customer = $this->session->userdata('customer');
        
        if (!$customer || !isset($customer['id'])) {
            $this->session->set_flashdata('cust_error', 'You must be logged in.');
            redirect('shop/login');
            return;
        }
        
        // Remove Google ID from customer record
        try {
            $this->db->raw(
                'UPDATE customers SET google_id = NULL WHERE id = ?',
                [$customer['id']]
            );
        } catch (Exception $e) {
            error_log('Could not remove google_id: ' . $e->getMessage());
        }
        
        // Update session
        $customerData = $this->session->userdata('customer');
        unset($customerData['oauth_provider']);
        $this->session->set_userdata('customer', $customerData);
        
        $this->session->set_flashdata('cust_success', 'Google account has been disconnected.');
        redirect('shop/profile');
    }
}
