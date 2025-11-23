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
        $order_id = $this->io->post('order_id');
        $payment_method = $this->io->post('payment_method');
        
        if (!$order_id) {
            return $this->json_response(['error' => 'Order ID required'], 400);
        }
        
        // Get order details
        $order = $this->OrderModel->find($order_id);
        if (!$order) {
            return $this->json_response(['error' => 'Order not found'], 404);
        }
        
        // Convert to centavos (PayMongo requires amount in smallest currency unit)
        $amount = (int)($order['total'] * 100);
        
        try {
            // Create Payment Intent
            $data = [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'payment_method_allowed' => [$payment_method],
                        'payment_method_options' => [
                            'card' => ['request_three_d_secure' => 'any']
                        ],
                        'currency' => 'PHP',
                        'description' => 'Order #' . $order_id . ' - TechTrack',
                        'statement_descriptor' => 'TechTrack Order',
                        'metadata' => [
                            'order_id' => $order_id
                        ]
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', '/payment_intents', $data);
            
            if (isset($response['data']['id'])) {
                // Save payment intent ID to order
                $this->OrderModel->where('id', $order_id)->update([
                    'payment_intent_id' => $response['data']['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                return $this->json_response([
                    'success' => true,
                    'client_key' => $response['data']['attributes']['client_key'],
                    'payment_intent_id' => $response['data']['id']
                ]);
            }
            
            return $this->json_response(['error' => 'Failed to create payment intent'], 500);
            
        } catch (Exception $e) {
            error_log('PayMongo Error: ' . $e->getMessage());
            return $this->json_response(['error' => $e->getMessage()], 500);
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
        $order_id = $this->io->post('order_id');
        $type = $this->io->post('type'); // 'gcash' or 'grab_pay'
        
        if (!$order_id || !$type) {
            return $this->json_response(['error' => 'Order ID and type required'], 400);
        }
        
        $order = $this->OrderModel->find($order_id);
        if (!$order) {
            return $this->json_response(['error' => 'Order not found'], 404);
        }
        
        $amount = (int)($order['total'] * 100);
        
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
                            'order_id' => $order_id
                        ]
                    ]
                ]
            ];
            
            $response = $this->paymongo_request('POST', '/sources', $data);
            
            if (isset($response['data']['id'])) {
                // Save source ID to order
                $this->OrderModel->where('id', $order_id)->update([
                    'payment_source_id' => $response['data']['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                return $this->json_response([
                    'success' => true,
                    'checkout_url' => $response['data']['attributes']['redirect']['checkout_url']
                ]);
            }
            
            return $this->json_response(['error' => 'Failed to create payment source'], 500);
            
        } catch (Exception $e) {
            return $this->json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Payment Success Page
     */
    public function success()
    {
        $order_id = $this->io->get('order_id');
        
        if ($order_id) {
            // Update order status
            $this->OrderModel->where('id', $order_id)->update([
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
            $this->OrderModel->where('id', $order_id)->update([
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
            $this->OrderModel->where('id', $order_id)->update([
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
            $this->OrderModel->where('id', $order_id)->update([
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
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->secret_key . ':')
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code >= 400) {
            $error = json_decode($response, true);
            throw new Exception($error['errors'][0]['detail'] ?? 'PayMongo API Error');
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
