<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Shop extends Controller
{
    private $cache;
    
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['ProductModel','OrderModel','CustomerModel','CartModel','WishlistModel']);
        $this->call->library(['session']);
        
        // Load cache helper
        $this->call->helper(['cache']);
        $this->cache = new SimpleCache();
    }
    
    /**
     * Get customer ID and session ID for cart/wishlist
     */
    private function getIdentifiers()
    {
        $customer = $this->session->userdata('customer');
        $customer_id = ($customer && isset($customer['id'])) ? (int)$customer['id'] : null;
        $session_id = $this->session->session_id ?? session_id();
        
        return ['customer_id' => $customer_id, 'session_id' => $session_id];
    }

    public function index()
    {
        // Load paginator helper
        $this->call->helper(['paginator']);
        
        // Get search query and pagination parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12; // 12 products per page
        
        // If no search/category filter, load all products for home page sections
        if (empty($search) && empty($category)) {
            // Load all products for home page display
            $data['products'] = $this->ProductModel->getAllWithImages();
            $data['pagination'] = null;
        } else {
            // Create cache key
            $cacheKey = 'products_page_' . $page . '_search_' . md5($search) . '_cat_' . md5($category);
            
            // Try to get from cache (5 minutes TTL)
            $cachedData = $this->cache->get($cacheKey);
            
            if ($cachedData !== null) {
                $data = $cachedData;
            } else {
                // Count total products
                $totalProducts = $this->ProductModel->countProducts($search, $category);
                
                // Create paginator
                $paginator = new Paginator($totalProducts, $perPage, $page);
                
                // Get paginated products with images (optimized query - no N+1)
                $data['products'] = $this->ProductModel->getPaginatedWithImages(
                    $paginator->getLimit(),
                    $paginator->getOffset(),
                    $search,
                    $category
                );
                
                // Pagination data
                $data['pagination'] = $paginator->getPaginationData();
                
                // Cache for 5 minutes
                $this->cache->set($cacheKey, $data, 300);
            }
        }
        
        $data['search_query'] = $search;
        $data['category'] = $category;
        
        // Extract product images from the optimized query result
        $productImages = [];
        foreach ($data['products'] as $p) {
            $productImages[$p['id']] = $p['main_image'] ?? null;
        }
        $data['product_images'] = $productImages;
        
        // Saved customer location (if any)
        $data['saved_location'] = $this->session->userdata('customer_location');

        // Store settings (for default map center)
        $settings = null;
        try {
            $settings = $this->db->table('settings')->order_by('id ASC')->limit(1)->get();
        } catch (Exception $e) {
            $settings = null;
        }
        if ($settings) {
            $data['store_settings'] = [
                'store_address' => $settings['store_address'] ?? ($settings['address'] ?? ''),
                'store_lat' => isset($settings['store_lat']) ? (float)$settings['store_lat'] : null,
                'store_lng' => isset($settings['store_lng']) ? (float)$settings['store_lng'] : null,
            ];
        } else {
            $data['store_settings'] = null;
        }

        $this->call->view('shop/home', $data);
    }

    public function product($id)
    {
        $data['product'] = $this->ProductModel->find($id);
        // Attach gallery images and specs if available
        $data['images'] = $this->db->table('product_images')
            ->where('product_id', (int)$id)
            ->order_by('is_main DESC, id ASC')
            ->get_all();
        $data['specs'] = $this->db->table('product_specs')
            ->where('product_id', (int)$id)
            ->order_by('id ASC')
            ->get_all();
        $this->call->view('shop/product', $data);
    }

    public function checkout()
    {
        // Get cart from database
        $ids = $this->getIdentifiers();
        $cartItems = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        
        // Format cart items
        $cart = [];
        foreach ($cartItems as $item) {
            $cart[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'price' => (float)$item['price'],
                'quantity' => (int)$item['quantity'],
                'subtotal' => (float)$item['price'] * (int)$item['quantity'],
                'image' => $item['image']
            ];
        }
        
        // Calculate totals
        $subtotal = 0;
        $cart_count = 0;
        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
            $cart_count += $item['quantity'];
        }
        
        $tax_rate = 0.12; // 12% tax
        $tax = round($subtotal * $tax_rate, 2);
        $shipping = 0; // Free shipping for now
        $total = $subtotal + $tax + $shipping;
        
        $data['cart'] = $cart;
        $data['subtotal'] = $subtotal;
        $data['tax'] = $tax;
        $data['shipping'] = $shipping;
        $data['total'] = $total;
        $data['cart_count'] = $cart_count;
        
        $this->call->view('shop/checkout', $data);
    }

    public function place_order()
    {
        // Basic order placement: support single item (product_id, quantity) or items JSON
        $payload = $_POST;
        $items = [];
        if (!empty($payload['items'])) {
            $items = is_string($payload['items']) ? (json_decode($payload['items'], true) ?: []) : $payload['items'];
        } elseif (!empty($payload['product_id'])) {
            $items[] = ['product_id' => (int)$payload['product_id'], 'quantity' => (int)($payload['quantity'] ?? 1)];
        }

        // Compute totals
        $subtotal = 0.0; $tax_rate = 0.12; $enriched = [];
        foreach ($items as $it) {
            $prod = $this->db->table('products')->where('id', (int)$it['product_id'])->get();
            if (!$prod) continue;
            $qty = max(1, (int)($it['quantity'] ?? 1));
            $price = (float)($prod['price'] ?? 0);
            $line = [
                'product_id' => (int)$it['product_id'],
                'product_name' => $prod['name'] ?? '',
                'price' => $price,
                'quantity' => $qty,
                'subtotal' => $price * $qty,
            ];
            $enriched[] = $line;
            $subtotal += $line['subtotal'];
        }
        $tax = round($subtotal * $tax_rate, 2);
        $total = $subtotal + $tax;

        // Get customer info from session if available
        $customer = $this->session->userdata('customer');
        $customer_id = ($customer && isset($customer['id'])) ? $customer['id'] : null;
        
        // Save guest email/phone to session for order tracking
        if (!empty($payload['customer_email'])) {
            $this->session->set_userdata('guest_email', $payload['customer_email']);
        }
        if (!empty($payload['customer_phone'])) {
            $this->session->set_userdata('guest_phone', $payload['customer_phone']);
        }
        
        // Insert order
        $payment_method = $payload['payment_method'] ?? 'cash';
        
        // Debug: log the payment method
        error_log("Payment method received: " . $payment_method);
        
        $order = [
            'customer_id' => $customer_id,
            'customer_name' => $payload['customer_name'] ?? 'Guest',
            'customer_email' => $payload['customer_email'] ?? null,
            'customer_phone' => $payload['customer_phone'] ?? null,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'total' => $total,
            'payment_method' => $payment_method,
            'payment_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $orderId = $this->OrderModel->insert($order);

        // Insert items and reduce stock, generate low-stock alerts
        foreach ($enriched as $it) {
            $this->db->table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $it['product_id'],
                'product_name' => $it['product_name'],
                'price' => $it['price'],
                'subtotal' => $it['subtotal'],
                'quantity' => $it['quantity'],
            ]);
            $prod = $this->db->table('products')->where('id', $it['product_id'])->get();
            if ($prod) {
                $newStock = max(0, ((int)$prod['stock']) - (int)$it['quantity']);
                $this->db->table('products')->where('id', $it['product_id'])->update(['stock' => $newStock]);
                $threshold = (int)($prod['low_stock_threshold'] ?? 5);
                if ($newStock <= $threshold) {
                    $this->db->table('alerts')->insert([
                        'title' => 'Low stock: ' . ($prod['name'] ?? ''),
                        'message' => 'Stock is now ' . $newStock . ' for SKU ' . ($prod['sku'] ?? ''),
                        'type' => 'low_stock',
                        'alert_type' => 'low_stock',
                        'severity' => 'warning',
                        'product_id' => $it['product_id'],
                        'order_id' => $orderId,
                        'is_read' => 0,
                        'is_dismissed' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->db->table('inventory_transactions')->insert([
                    'product_id' => $it['product_id'],
                    'transaction_type' => 'remove',
                    'quantity' => (int)$it['quantity'],
                    'reason' => 'sale',
                    'notes' => 'Order #' . $orderId,
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Clear cart after successful order from database
        $ids = $this->getIdentifiers();
        $this->CartModel->clearCart($ids['customer_id'], $ids['session_id']);

        // If payment method requires online payment (anything except COD), redirect to payment
        if ($payment_method !== 'cash') {
            error_log("Redirecting to payment page for method: " . $payment_method);
            error_log("Order ID: " . $orderId);
            error_log("Total: " . $total);
            
            // Store order ID in session for payment callback
            $this->session->set_userdata('pending_order_id', $orderId);
            
            // Prepare items for PayMongo
            $paymongo_items = [];
            foreach ($enriched as $it) {
                $paymongo_items[] = [
                    'name' => $it['product_name'],
                    'quantity' => $it['quantity'],
                    'amount' => (int)($it['price'] * 100), // Convert to centavos
                ];
            }
            
            // Store payment data in session
            $this->session->set_userdata('paymongo_payment_data', [
                'order_id' => $orderId,
                'amount' => (int)($total * 100), // Convert to centavos
                'description' => 'Order #' . $orderId,
                'items' => $paymongo_items,
                'payment_method' => $payment_method,
            ]);
            
            // Redirect based on payment method
            if ($payment_method === 'card') {
                redirect('payment/create-intent');
                exit();
            } elseif ($payment_method === 'gcash') {
                redirect('payment/create-source');
                exit();
            } elseif ($payment_method === 'bank') {
                redirect('payment/bank-transfer');
                exit();
            } else {
                redirect('payment/create-source');
                exit();
            }
        }

        redirect('track/' . $orderId);
    }

    public function track_order($order_id)
    {
        $data['order'] = $this->OrderModel->find($order_id);
        $data['items'] = $this->db->table('order_items')->where('order_id', $order_id)->get_all();
        $this->call->view('shop/track', $data);
    }

    public function my_orders()
    {
        // Get customer from session
        $customer = $this->session->userdata('customer');
        
        $data['orders'] = [];
        $data['customer'] = $customer;
        
        // Try multiple approaches to get orders
        if ($customer && isset($customer['id'])) {
            // Get all orders for logged-in customer by customer_id
            $data['orders'] = $this->db->raw("
                SELECT o.*, 
                       COUNT(oi.id) as item_count,
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.customer_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ", [$customer['id']])->fetchAll(PDO::FETCH_ASSOC);
            
            // Also get orders by email if customer_id didn't match
            if (empty($data['orders']) && !empty($customer['email'])) {
                $data['orders'] = $this->db->raw("
                    SELECT o.*, 
                           COUNT(oi.id) as item_count,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE o.customer_email = ?
                    GROUP BY o.id
                    ORDER BY o.created_at DESC
                ", [$customer['email']])->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Try to get orders by email/phone from session (guest checkout)
            $guest_email = $this->session->userdata('guest_email');
            $guest_phone = $this->session->userdata('guest_phone');
            
            if ($guest_email || $guest_phone) {
                $conditions = [];
                $params = [];
                
                if ($guest_email) {
                    $conditions[] = "o.customer_email = ?";
                    $params[] = $guest_email;
                }
                if ($guest_phone) {
                    $conditions[] = "o.customer_phone = ?";
                    $params[] = $guest_phone;
                }
                
                $whereClause = implode(' OR ', $conditions);
                
                $data['orders'] = $this->db->raw("
                    SELECT o.*, 
                           COUNT(oi.id) as item_count,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE $whereClause
                    GROUP BY o.id
                    ORDER BY o.created_at DESC
                ", $params)->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        $this->call->view('shop/my_orders', $data);
    }

    public function wishlist()
    {
        // Get wishlist from database
        $ids = $this->getIdentifiers();
        $wishlistItems = $this->WishlistModel->getWishlist($ids['customer_id'], $ids['session_id']);
        
        // Format products
        $data['products'] = [];
        foreach ($wishlistItems as $item) {
            $data['products'][] = [
                'id' => $item['product_id'],
                'name' => $item['product_name'],
                'price' => $item['price'],
                'stock' => $item['stock'],
                'sku' => $item['sku'],
                'main_image' => $item['image']
            ];
        }
        
        $this->call->view('shop/wishlist', $data);
    }

    public function add_to_wishlist()
    {
        header('Content-Type: application/json');
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            return;
        }
        
        // Add to database
        $ids = $this->getIdentifiers();
        $result = $this->WishlistModel->addItem($productId, $ids['customer_id'], $ids['session_id']);
        
        if ($result) {
            $count = $this->WishlistModel->getWishlistCount($ids['customer_id'], $ids['session_id']);
            echo json_encode([
                'success' => true,
                'message' => 'Added to wishlist',
                'wishlist_count' => $count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Already in wishlist'
            ]);
        }
    }

    public function remove_from_wishlist()
    {
        header('Content-Type: application/json');
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            return;
        }
        
        // Remove from database
        $ids = $this->getIdentifiers();
        $this->WishlistModel->removeItem($productId, $ids['customer_id'], $ids['session_id']);
        
        $count = $this->WishlistModel->getWishlistCount($ids['customer_id'], $ids['session_id']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Removed from wishlist',
            'wishlist_count' => $count
        ]);
    }

    public function get_wishlist()
    {
        header('Content-Type: application/json');
        
        $ids = $this->getIdentifiers();
        $productIds = $this->WishlistModel->getWishlistProductIds($ids['customer_id'], $ids['session_id']);
        $count = count($productIds);
        
        echo json_encode([
            'success' => true,
            'wishlist' => $productIds,
            'wishlist_count' => $count
        ]);
    }

    // POST /shop/save-location
    public function save_location()
    {
        $lat = isset($_POST['lat']) ? (float)$_POST['lat'] : null;
        $lng = isset($_POST['lng']) ? (float)$_POST['lng'] : null;
        $address = trim($_POST['address'] ?? '');

        if ($lat === null || $lng === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Latitude and longitude are required.']);
            exit;
        }

        $loc = [
            'lat' => $lat,
            'lng' => $lng,
            'address' => $address,
            'saved_at' => date('Y-m-d H:i:s')
        ];
        $this->session->set_userdata('customer_location', $loc);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'location' => $loc]);
        exit;
    }

    // Add to cart
    public function add_to_cart()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        if ($quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
            exit;
        }
        
        // Get product details
        $product = $this->ProductModel->find($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Check stock
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            exit;
        }
        
        // Add to database
        $ids = $this->getIdentifiers();
        $this->CartModel->addOrUpdate($product_id, $quantity, $ids['customer_id'], $ids['session_id']);
        
        // Get updated cart totals
        $cart = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        $cart_count = 0;
        $cart_total = 0;
        foreach ($cart as $item) {
            $cart_count += (int)$item['quantity'];
            $cart_total += (float)$item['price'] * (int)$item['quantity'];
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart',
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
        exit;
    }
    
    // Get cart
    public function get_cart()
    {
        header('Content-Type: application/json');
        
        $ids = $this->getIdentifiers();
        $cartItems = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        
        $cart = [];
        $cart_count = 0;
        $cart_total = 0;
        foreach ($cartItems as $item) {
            $subtotal = (float)$item['price'] * (int)$item['quantity'];
            $cart[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'price' => (float)$item['price'],
                'quantity' => (int)$item['quantity'],
                'subtotal' => $subtotal,
                'image' => $item['image']
            ];
            $cart_count += (int)$item['quantity'];
            $cart_total += $subtotal;
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $cart,
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
        exit;
    }
    
    // Update cart item
    public function update_cart()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        $ids = $this->getIdentifiers();
        
        // If quantity is 0, remove item
        if ($quantity <= 0) {
            // Find cart item by product_id
            $cartItems = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
            foreach ($cartItems as $item) {
                if ($item['product_id'] == $product_id) {
                    $this->CartModel->removeItem($item['id'], $ids['customer_id'], $ids['session_id']);
                    break;
                }
            }
        } else {
            // Update quantity
            $product = $this->ProductModel->find($product_id);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            if ($quantity > $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit;
            }
            
            // Find and update cart item
            $cartItems = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
            foreach ($cartItems as $item) {
                if ($item['product_id'] == $product_id) {
                    $this->CartModel->updateQuantity($item['id'], $quantity);
                    break;
                }
            }
        }
        
        // Get updated cart
        $cart = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        $cart_count = 0;
        $cart_total = 0;
        foreach ($cart as $item) {
            $cart_count += (int)$item['quantity'];
            $cart_total += (float)$item['price'] * (int)$item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated',
            'cart' => $cart,
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
        exit;
    }
    
    // Remove from cart
    public function remove_from_cart()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        $ids = $this->getIdentifiers();
        
        // Find and remove cart item
        $cartItems = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        foreach ($cartItems as $item) {
            if ($item['product_id'] == $product_id) {
                $this->CartModel->removeItem($item['id'], $ids['customer_id'], $ids['session_id']);
                break;
            }
        }
        
        // Get updated cart
        $cart = $this->CartModel->getCart($ids['customer_id'], $ids['session_id']);
        $cart_count = 0;
        $cart_total = 0;
        foreach ($cart as $item) {
            $cart_count += (int)$item['quantity'];
            $cart_total += (float)$item['price'] * (int)$item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart' => $cart,
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
        exit;
    }
    
    // Clear cart
    public function clear_cart()
    {
        header('Content-Type: application/json');
        
        $ids = $this->getIdentifiers();
        $this->CartModel->clearCart($ids['customer_id'], $ids['session_id']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared',
            'cart' => [],
            'cart_count' => 0,
            'cart_total' => 0
        ]);
        exit;
    }
    
    // Helper function to get product image
    private function get_product_image($product_id)
    {
        $imageStmt = $this->db->raw("SELECT image_url FROM product_images WHERE product_id = ? LIMIT 1", [(int)$product_id]);
        $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
        return $image['image_url'] ?? null;
    }

    // Debug cart - remove in production
    public function debug_cart()
    {
        header('Content-Type: application/json');
        $cart = $this->session->userdata('shopping_cart') ?? [];
        echo json_encode(['cart' => $cart], JSON_PRETTY_PRINT);
        exit;
    }

    // Desktops dedicated page
    public function desktops()
    {
        // Load paginator helper
        $this->call->helper(['paginator']);
        
        // Desktop category filter
        $category = 'Desktop';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        
        // Create cache key
        $cacheKey = 'desktops_page_' . $page . '_search_' . md5($search);
        
        // Try to get from cache (5 minutes TTL)
        $cachedData = $this->cache->get($cacheKey);
        
        if ($cachedData !== null) {
            $data = $cachedData;
        } else {
            // Count total desktop products
            $totalProducts = $this->ProductModel->countProducts($search, $category);
            
            // Create paginator
            $paginator = new Paginator($totalProducts, $perPage, $page);
            
            // Get paginated desktop products with images
            $data['products'] = $this->ProductModel->getPaginatedWithImages(
                $paginator->getLimit(),
                $paginator->getOffset(),
                $search,
                $category
            );
            
            // Pagination data
            $data['pagination'] = $paginator->getPaginationData();
            
            // Cache for 5 minutes
            $this->cache->set($cacheKey, $data, 300);
        }
        
        $data['search_query'] = $search;
        $data['category'] = $category;
        
        // Extract product images
        $productImages = [];
        foreach ($data['products'] as $p) {
            $productImages[$p['id']] = $p['main_image'] ?? null;
        }
        $data['product_images'] = $productImages;

        $this->call->view('shop/desktops', $data);
    }

    // Laptops dedicated page
    public function laptops()
    {
        // Load paginator helper
        $this->call->helper(['paginator']);
        
        // Laptop category filter
        $category = 'Laptop';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        
        // Create cache key
        $cacheKey = 'laptops_page_' . $page . '_search_' . md5($search);
        
        // Try to get from cache (5 minutes TTL)
        $cachedData = $this->cache->get($cacheKey);
        
        if ($cachedData !== null) {
            $data = $cachedData;
        } else {
            // Count total laptop products
            $totalProducts = $this->ProductModel->countProducts($search, $category);
            
            // Create paginator
            $paginator = new Paginator($totalProducts, $perPage, $page);
            
            // Get paginated laptop products with images
            $data['products'] = $this->ProductModel->getPaginatedWithImages(
                $paginator->getLimit(),
                $paginator->getOffset(),
                $search,
                $category
            );
            
            // Pagination data
            $data['pagination'] = $paginator->getPaginationData();
            
            // Cache for 5 minutes
            $this->cache->set($cacheKey, $data, 300);
        }
        
        $data['search_query'] = $search;
        $data['category'] = $category;
        
        // Extract product images
        $productImages = [];
        foreach ($data['products'] as $p) {
            $productImages[$p['id']] = $p['main_image'] ?? null;
        }
        $data['product_images'] = $productImages;

        $this->call->view('shop/laptops', $data);
    }

    // Rewards/Loyalty Program page
    public function rewards()
    {
        // Get user info if logged in
        $data['user'] = null;
        $data['points'] = 0;
        $data['tier'] = 'Bronze';
        
        // Check if customer is logged in
        if ($this->session->userdata('customer_id')) {
            $customerId = $this->session->userdata('customer_id');
            // Get customer points from database (you can extend this)
            $data['user'] = [
                'name' => $this->session->userdata('customer_name'),
                'email' => $this->session->userdata('customer_email')
            ];
            // Mock data for now - you can fetch real points from database
            $data['points'] = 1250;
            $data['tier'] = $this->calculateTier($data['points']);
        }
        
        $this->call->view('shop/rewards', $data);
    }
    
    private function calculateTier($points)
    {
        if ($points >= 5000) return 'Platinum';
        if ($points >= 2500) return 'Gold';
        if ($points >= 1000) return 'Silver';
        return 'Bronze';
    }

    // PC Builder page
    public function build_pc()
    {
        // Get all PC Parts products
        $allPCParts = $this->ProductModel->getByCategory('PC Parts');
        
        // Component categories for PC building
        $categories = [
            'CPU' => 'Processor',
            'Motherboard' => 'Motherboard',
            'GPU' => 'Graphics Card',
            'RAM' => 'Memory',
            'Storage' => 'Storage',
            'PSU' => 'Power Supply',
            'Case' => 'Case',
            'Cooling' => 'Cooling'
        ];
        
        $data['categories'] = $categories;
        $data['products_by_category'] = [];
        
        // Group PC Parts products by their subcategory/name keywords
        foreach ($categories as $categoryKey => $categoryName) {
            $data['products_by_category'][$categoryKey] = [];
            
            // Filter products by keyword matching in name or description
            foreach ($allPCParts as $product) {
                $name = strtolower($product['name']);
                $description = strtolower($product['description'] ?? '');
                $searchText = $name . ' ' . $description;
                
                $keywordMatch = false;
                switch ($categoryKey) {
                    case 'CPU':
                        $keywordMatch = (strpos($searchText, 'processor') !== false || 
                                       strpos($searchText, 'cpu') !== false ||
                                       strpos($searchText, 'intel') !== false ||
                                       strpos($searchText, 'amd') !== false ||
                                       strpos($searchText, 'ryzen') !== false);
                        break;
                    case 'Motherboard':
                        $keywordMatch = (strpos($searchText, 'motherboard') !== false || 
                                       strpos($searchText, 'mobo') !== false ||
                                       strpos($searchText, 'msi') !== false ||
                                       strpos($searchText, 'asus') !== false ||
                                       strpos($searchText, 'gigabyte') !== false ||
                                       strpos($searchText, 'asrock') !== false ||
                                       preg_match('/[bzx]\d{3,4}/i', $searchText)); // Matches chipsets like Z590, B550, X570
                        break;
                    case 'GPU':
                        $keywordMatch = (strpos($searchText, 'graphics') !== false || 
                                       strpos($searchText, 'gpu') !== false ||
                                       strpos($searchText, 'video card') !== false ||
                                       strpos($searchText, 'nvidia') !== false ||
                                       strpos($searchText, 'rtx') !== false ||
                                       strpos($searchText, 'gtx') !== false);
                        break;
                    case 'RAM':
                        $keywordMatch = (strpos($searchText, 'ram') !== false || 
                                       strpos($searchText, 'memory') !== false ||
                                       strpos($searchText, 'ddr') !== false);
                        break;
                    case 'Storage':
                        $keywordMatch = (strpos($searchText, 'storage') !== false || 
                                       strpos($searchText, 'ssd') !== false ||
                                       strpos($searchText, 'hdd') !== false ||
                                       strpos($searchText, 'hard drive') !== false ||
                                       strpos($searchText, 'nvme') !== false);
                        break;
                    case 'PSU':
                        $keywordMatch = (strpos($searchText, 'power supply') !== false || 
                                       strpos($searchText, 'psu') !== false);
                        break;
                    case 'Case':
                        $keywordMatch = (strpos($searchText, 'case') !== false || 
                                       strpos($searchText, 'chassis') !== false ||
                                       strpos($searchText, 'tower') !== false);
                        break;
                    case 'Cooling':
                        $keywordMatch = (strpos($searchText, 'cooling') !== false || 
                                       strpos($searchText, 'cooler') !== false ||
                                       strpos($searchText, 'fan') !== false ||
                                       strpos($searchText, 'radiator') !== false ||
                                       strpos($searchText, 'liquid') !== false);
                        break;
                }
                
                if ($keywordMatch) {
                    $data['products_by_category'][$categoryKey][] = $product;
                }
            }
        }
        
        $this->call->view('shop/build_pc', $data);
    }
}

?>
