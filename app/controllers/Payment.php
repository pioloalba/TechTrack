<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * PayMongo Payment Controller
 * Handles payment intent creation, payment processing, and webhooks
 */
class Payment extends Controller
{
    private $config;
    private $secret_key;
    private $public_key;

    public function __construct()
    {
        parent::__construct();
        $this->call->library(['session']);
        $this->call->model(['OrderModel', 'ProductModel']);
        
        // Load PayMongo config
        require_once APP_DIR . 'config/paymongo.php';
        $this->config = $config['paymongo'];
        
        // Set API keys based on test/live mode
        if ($this->config['test_mode']) {
            $this->secret_key = $this->config['test_secret_key'];
            $this->public_key = $this->config['test_public_key'];
        } else {
            $this->secret_key = $this->config['live_secret_key'];
            $this->public_key = $this->config['live_public_key'];
        }
    }

    /**
     * Create Payment Intent for card payments
     */
    public function create_intent()
    {
        // Get payment data from session
        $payment_data = $this->session->userdata('paymongo_payment_data');
        
        if (!$payment_data || !isset($payment_data['order_id'])) {
            $this->session->set_flashdata('error', 'Payment session expired');
            redirect('checkout');
            return;
        }
        
        $order_id = $payment_data['order_id'];
        $amount = $payment_data['amount'];
        $payment_method = $payment_data['payment_method'] ?? 'card';
        
        // Get order details
        $order = $this->OrderModel->find($order_id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found');
            redirect('checkout');
            return;
        }
        
        try {
            // Create Payment Intent
            $data = [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'payment_method_allowed' => ['card'],
                        'payment_method_options' => [
                            'card' => ['request_three_d_secure' => 'any']
                        ],
                        'currency' => 'PHP',
                        'description' => 'Order #' . $order_id . ' - TechTrack',
                        'statement_descriptor' => 'TechTrack Order',
                        'metadata' => [
                            'order_id' => (string)$order_id
                        ]
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', '/payment_intents', $data);
            
            if (isset($response['data']['id'])) {
                // Save payment intent ID to order
                $this->db->table('orders')->where('id', $order_id)->update([
                    'payment_intent_id' => $response['data']['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Show card payment form
                $data = [
                    'order' => $order,
                    'client_key' => $response['data']['attributes']['client_key'],
                    'payment_intent_id' => $response['data']['id'],
                    'public_key' => $this->public_key,
                    'amount' => $amount / 100, // Convert back to PHP
                ];
                
                $this->call->view('payment/card_form', $data);
                return;
            }
            
            $this->session->set_flashdata('error', 'Failed to create payment intent');
            redirect('checkout');
            
        } catch (Exception $e) {
            error_log('PayMongo create_intent Error: ' . $e->getMessage());
            error_log('PayMongo Stack Trace: ' . $e->getTraceAsString());
            $this->session->set_flashdata('error', 'Payment error: ' . $e->getMessage());
            redirect('checkout');
        }
    }

    /**
     * Create Payment Method (for card tokenization)
     */
    public function create_payment_method()
    {
        $card_number = $this->io->post('card_number');
        $exp_month = $this->io->post('exp_month');
        $exp_year = $this->io->post('exp_year');
        $cvc = $this->io->post('cvc');
        
        try {
            $data = [
                'data' => [
                    'attributes' => [
                        'type' => 'card',
                        'details' => [
                            'card_number' => $card_number,
                            'exp_month' => (int)$exp_month,
                            'exp_year' => (int)$exp_year,
                            'cvc' => $cvc
                        ]
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', '/payment_methods', $data);
            
            if (isset($response['data']['id'])) {
                return $this->json_response([
                    'success' => true,
                    'payment_method_id' => $response['data']['id']
                ]);
            }
            
            return $this->json_response(['error' => 'Failed to create payment method'], 500);
            
        } catch (Exception $e) {
            return $this->json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Attach Payment Method to Intent
     */
    public function attach_intent()
    {
        $intent_id = $this->io->post('intent_id');
        $payment_method_id = $this->io->post('payment_method_id');
        $return_url = site_url('payment/success');
        
        try {
            $data = [
                'data' => [
                    'attributes' => [
                        'payment_method' => $payment_method_id,
                        'return_url' => $return_url
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', "/payment_intents/{$intent_id}/attach", $data);
            
            if (isset($response['data']['attributes']['status'])) {
                $status = $response['data']['attributes']['status'];
                $next_action = $response['data']['attributes']['next_action'] ?? null;
                
                return $this->json_response([
                    'success' => true,
                    'status' => $status,
                    'next_action' => $next_action
                ]);
            }
            
            return $this->json_response(['error' => 'Failed to attach payment method'], 500);
            
        } catch (Exception $e) {
            return $this->json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create GCash/GrabPay Source
     */
    public function create_source()
    {
        // Get payment data from session
        $payment_data = $this->session->userdata('paymongo_payment_data');
        
        if (!$payment_data || !isset($payment_data['order_id'])) {
            $this->session->set_flashdata('error', 'Payment session expired');
            redirect('checkout');
            return;
        }
        
        $order_id = $payment_data['order_id'];
        $amount = $payment_data['amount'];
        $type = $payment_data['payment_method']; // 'gcash' or 'grab_pay'
        
        $order = $this->OrderModel->find($order_id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found');
            redirect('checkout');
            return;
        }
        
        try {
            $data = [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'redirect' => [
                            'success' => site_url('payment/success?order_id=' . $order_id),
                            'failed' => site_url('payment/cancel?order_id=' . $order_id)
                        ],
                        'type' => $type,
                        'currency' => 'PHP',
                        'metadata' => [
                            'order_id' => (string)$order_id
                        ]
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', '/sources', $data);
            
            if (isset($response['data']['id'])) {
                // Save source ID to order
                $this->db->table('orders')->where('id', $order_id)->update([
                    'payment_source_id' => $response['data']['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Redirect to GCash/GrabPay checkout page
                $checkout_url = $response['data']['attributes']['redirect']['checkout_url'];
                redirect($checkout_url);
                return;
            }
            
            $this->session->set_flashdata('error', 'Failed to create payment source');
            redirect('checkout');
            
        } catch (Exception $e) {
            error_log('PayMongo create_source Error: ' . $e->getMessage());
            error_log('PayMongo Stack Trace: ' . $e->getTraceAsString());
            $this->session->set_flashdata('error', 'Payment error: ' . $e->getMessage());
            redirect('checkout');
        }
    }

    /**
     * Bank Transfer Payment Form
     */
    public function bank_transfer()
    {
        // Get payment data from session
        $payment_data = $this->session->userdata('paymongo_payment_data');
        
        if (!$payment_data || !isset($payment_data['order_id'])) {
            $this->session->set_flashdata('error', 'Payment session expired');
            redirect('checkout');
            return;
        }
        
        $order_id = $payment_data['order_id'];
        $amount = $payment_data['amount'] / 100; // Convert back to PHP
        
        // Get order details
        $order = $this->OrderModel->find($order_id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found');
            redirect('checkout');
            return;
        }
        
        // Show bank transfer form
        $data = [
            'order' => $order,
            'amount' => $amount,
        ];
        
        $this->call->view('payment/bank_transfer', $data);
    }

    /**
     * Confirm Bank Transfer (upload proof)
     */
    public function confirm_bank_transfer()
    {
        $order_id = $this->io->post('order_id');
        $reference_number = $this->io->post('reference_number');
        
        if (!$order_id) {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('checkout');
            return;
        }
        
        // Handle file upload
        if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === 0) {
            $upload_dir = ROOT_DIR . 'public/uploads/payment_proofs/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['proof_of_payment']['name'], PATHINFO_EXTENSION);
            $new_filename = 'payment_' . $order_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $upload_path)) {
                // Update order with payment proof
                $this->db->table('orders')->where('id', $order_id)->update([
                    'payment_status' => 'pending_verification',
                    'payment_proof' => 'uploads/payment_proofs/' . $new_filename,
                    'bank_reference' => $reference_number,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $this->session->set_flashdata('success', 'Payment proof uploaded! We will verify your payment within 24 hours.');
                redirect('track/' . $order_id);
                return;
            }
        }
        
        $this->session->set_flashdata('error', 'Failed to upload payment proof');
        redirect('payment/bank-transfer');
    }

    /**
     * Payment Success Page
     */
    public function success()
    {
        $order_id = $this->io->get('order_id');
        
        if ($order_id) {
            // Update order status
            $this->db->table('orders')->where('id', $order_id)->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        $this->session->set_flashdata('success', 'Payment successful! Your order is being processed.');
        redirect('track/' . $order_id);
    }

    /**
     * Payment Cancel Page
     */
    public function cancel()
    {
        $order_id = $this->io->get('order_id');
        
        if ($order_id) {
            $this->db->table('orders')->where('id', $order_id)->update([
                'payment_status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        $this->session->set_flashdata('error', 'Payment was cancelled. Please try again.');
        redirect('checkout');
    }

    /**
     * Webhook Handler for PayMongo Events
     */
    public function webhook()
    {
        $payload = file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
        
        // Verify webhook signature
        if (!$this->verify_webhook($payload, $sig_header)) {
            http_response_code(400);
            exit('Invalid signature');
        }
        
        $event = json_decode($payload, true);
        
        if (isset($event['data']['attributes']['type'])) {
            $type = $event['data']['attributes']['type'];
            
            switch ($type) {
                case 'payment.paid':
                    $this->handle_payment_paid($event);
                    break;
                case 'payment.failed':
                    $this->handle_payment_failed($event);
                    break;
                case 'source.chargeable':
                    $this->handle_source_chargeable($event);
                    break;
            }
        }
        
        http_response_code(200);
        echo json_encode(['success' => true]);
    }

    /**
     * Handle payment.paid event
     */
    private function handle_payment_paid($event)
    {
        $order_id = $event['data']['attributes']['data']['attributes']['metadata']['order_id'] ?? null;
        
        if ($order_id) {
            $this->db->table('orders')->where('id', $order_id)->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Handle payment.failed event
     */
    private function handle_payment_failed($event)
    {
        $order_id = $event['data']['attributes']['data']['attributes']['metadata']['order_id'] ?? null;
        
        if ($order_id) {
            $this->db->table('orders')->where('id', $order_id)->update([
                'payment_status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Handle source.chargeable event (for GCash/GrabPay)
     */
    private function handle_source_chargeable($event)
    {
        $source_id = $event['data']['id'] ?? null;
        $order_id = $event['data']['attributes']['metadata']['order_id'] ?? null;
        
        if ($source_id && $order_id) {
            // Create payment from source
            $order = $this->OrderModel->find($order_id);
            $amount = (int)($order['total'] * 100);
            
            try {
                $data = [
                    'data' => [
                        'attributes' => [
                            'amount' => $amount,
                            'source' => ['id' => $source_id, 'type' => 'source'],
                            'currency' => 'PHP',
                            'description' => 'Order #' . $order_id
                        ]
                    ]
                ];
                
                $this->paymongo_request('POST', '/payments', $data);
            } catch (Exception $e) {
                error_log('PayMongo Payment Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Make PayMongo API Request
     */
    private function paymongo_request($method, $endpoint, $data = null)
    {
        $url = 'https://api.paymongo.com/v1' . $endpoint;
        
        // Log the request
        error_log('PayMongo Request: ' . $method . ' ' . $url);
        if ($data) {
            error_log('PayMongo Request Data: ' . json_encode($data));
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->secret_key . ':')
        ]);
        
        // Disable SSL verification for local development (REMOVE IN PRODUCTION)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Log the response
        error_log('PayMongo Response Code: ' . $http_code);
        error_log('PayMongo Response: ' . $response);
        
        if ($curl_error) {
            error_log('PayMongo CURL Error: ' . $curl_error);
            throw new Exception('Connection error: ' . $curl_error);
        }
        
        if ($http_code >= 400) {
            $error = json_decode($response, true);
            $error_msg = $error['errors'][0]['detail'] ?? 'PayMongo API Error';
            error_log('PayMongo API Error: ' . $error_msg);
            error_log('Full Error Response: ' . json_encode($error));
            throw new Exception($error_msg);
        }
        
        return json_decode($response, true);
    }

    /**
     * Verify webhook signature
     */
    private function verify_webhook($payload, $signature)
    {
        $secret = $this->config['webhook_secret'];
        $computed = hash_hmac('sha256', $payload, $secret);
        return hash_equals($computed, $signature);
    }

    /**
     * JSON Response Helper
     */
    private function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
