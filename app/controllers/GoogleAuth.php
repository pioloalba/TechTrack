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
        if ($this->io->get('error')) {
            $this->session->set_flashdata('cust_error', 'Google authentication was cancelled or failed.');
            redirect('shop/login');
            return;
        }
        
        // Get authorization code
        $code = $this->io->get('code');
        if (!$code) {
            $this->session->set_flashdata('cust_error', 'No authorization code received from Google.');
            redirect('shop/login');
            return;
        }
        
        try {
            // Exchange authorization code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new Exception('Error fetching access token: ' . $token['error']);
            }
            
            $this->client->setAccessToken($token);
            
            // Get user info from Google
            $google_oauth = new Google_Service_Oauth2($this->client);
            $google_account_info = $google_oauth->userinfo->get();
            
            // Extract user data
            $googleId = $google_account_info->id;
            $email = $google_account_info->email;
            $firstName = $google_account_info->givenName;
            $lastName = $google_account_info->familyName;
            $picture = $google_account_info->picture;
            
            // Process OAuth login (create or login customer)
            $this->processOAuthLogin($googleId, $email, $firstName, $lastName, $picture);
            
        } catch (Exception $e) {
            error_log('Google OAuth Error: ' . $e->getMessage());
            $this->session->set_flashdata('cust_error', 'Failed to authenticate with Google. Please try again.');
            redirect('shop/login');
        }
    }

    /**
     * Process OAuth login - create account or login existing customer
     */
    private function processOAuthLogin($googleId, $email, $firstName, $lastName, $picture)
    {
        // Check if customer exists with this Google ID
        $customer = $this->CustomerModel->where('google_id', $googleId)->get();
        
        if (!$customer) {
            // Check if email already exists (regular account)
            $customer = $this->CustomerModel->where('email', $email)->get();
            
            if ($customer) {
                // Link existing account with Google ID
                $this->CustomerModel->where('id', $customer['id'])->update([
                    'google_id' => $googleId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Create new customer account
                $customerId = $this->CustomerModel->insert([
                    'google_id' => $googleId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => '',
                    'address' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $customer = [
                    'id' => $customerId,
                    'google_id' => $googleId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
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
            'first_name' => $customer['first_name'],
            'last_name' => $customer['last_name'],
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
        $this->CustomerModel->where('id', $customer['id'])->update([
            'google_id' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Update session
        $customerData = $this->session->userdata('customer');
        unset($customerData['oauth_provider']);
        $this->session->set_userdata('customer', $customerData);
        
        $this->session->set_flashdata('cust_success', 'Google account has been disconnected.');
        redirect('shop/profile');
    }
}
