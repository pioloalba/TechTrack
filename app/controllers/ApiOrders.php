<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiOrders extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['OrderModel','OrderItemModel']);
    }

    public function index()
    {
        header('Content-Type: application/json');
        echo json_encode($this->OrderModel->all());
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        $order = $this->OrderModel->find($id);
        $order['items'] = $this->OrderItemModel->table('order_items')->where('order_id', $id)->get_all();
        echo json_encode($order);
    }

    public function store()
    {
        // Expecting POST with items: [{product_id, quantity}], and optional customer fields
        $payload = $_POST;
        $items = [];
        if (isset($payload['items'])) {
            // items may be JSON string or array
            if (is_string($payload['items'])) {
                $items = json_decode($payload['items'], true) ?: [];
            } elseif (is_array($payload['items'])) {
                $items = $payload['items'];
            }
        }

        // Compute totals
        $subtotal = 0.0;
        $tax_rate = 0.12;
        foreach ($items as &$it) {
            $prod = $this->db->table('products')->where('id', (int)$it['product_id'])->get();
            if (!$prod) { continue; }
            $price = (float)($prod['price'] ?? 0);
            $qty = (int)($it['quantity'] ?? 1);
            $it['price'] = $price;
            $it['product_name'] = $prod['name'] ?? '';
            $it['subtotal'] = $price * $qty;
            $subtotal += $it['subtotal'];
        }
        $tax = round($subtotal * $tax_rate, 2);
        $shipping = isset($payload['shipping']) ? (float)$payload['shipping'] : 0.0;
        $discount = isset($payload['discount']) ? (float)$payload['discount'] : 0.0;
        $total = max(0, $subtotal + $tax + $shipping - $discount);

        // Insert order
        $order = [
            'customer_id' => $payload['customer_id'] ?? null,
            'customer_name' => $payload['customer_name'] ?? null,
            'customer_email' => $payload['customer_email'] ?? null,
            'customer_phone' => $payload['customer_phone'] ?? null,
            'order_code' => $payload['order_code'] ?? ('ORD-' . time()),
            'status' => $payload['status'] ?? 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $payload['payment_method'] ?? 'cash',
            'payment_status' => $payload['payment_status'] ?? 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $order_id = $this->OrderModel->insert($order);

        // Insert items and update stock, generate low-stock alerts
        foreach ($items as $it) {
            if (empty($it['product_id'])) continue;
            $qty = (int)$it['quantity'];
            $this->db->table('order_items')->insert([
                'order_id' => $order_id,
                'product_id' => (int)$it['product_id'],
                'product_name' => $it['product_name'] ?? '',
                'price' => (float)$it['price'],
                'subtotal' => (float)$it['subtotal'],
                'quantity' => $qty,
            ]);

            // decrement stock
            $prod = $this->db->table('products')->where('id', (int)$it['product_id'])->get();
            if ($prod) {
                $newStock = max(0, ((int)$prod['stock']) - $qty);
                $this->db->table('products')->where('id', (int)$it['product_id'])->update(['stock' => $newStock]);
                // low stock alert
                $threshold = (int)($prod['low_stock_threshold'] ?? 5);
                if ($newStock <= $threshold) {
                    $this->db->table('alerts')->insert([
                        'title' => 'Low stock: ' . ($prod['name'] ?? ''),
                        'message' => 'Stock is now ' . $newStock . ' for SKU ' . ($prod['sku'] ?? ''),
                        'type' => 'low_stock',
                        'alert_type' => 'low_stock',
                        'severity' => 'warning',
                        'product_id' => (int)$it['product_id'],
                        'order_id' => $order_id,
                        'is_read' => 0,
                        'is_dismissed' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                // inventory transaction record
                $this->db->table('inventory_transactions')->insert([
                    'product_id' => (int)$it['product_id'],
                    'type' => 'remove',
                    'transaction_type' => 'remove',
                    'quantity' => $qty,
                    'reason' => 'sale',
                    'notes' => 'Order #' . $order_id,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['id' => $order_id, 'subtotal' => $subtotal, 'tax' => $tax, 'total' => $total]);
    }
}

?>
