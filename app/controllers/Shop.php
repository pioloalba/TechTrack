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
        
        // Get featured testimonials from real customer reviews
        $data['testimonials'] = $this->getFeaturedTestimonials();

        $this->call->view('shop/home', $data);
    }

    public function product($id)
    {
        // Load QR code helper
        $this->call->helper('qrcode');
        
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
        
        // Load reviews with customer data
        $data['reviews'] = $this->getProductReviews($id);
        
        // Check if current user can review (has purchased)
        $ids = $this->getIdentifiers();
        $data['can_review'] = false;
        if ($ids['customer_id']) {
            $this->call->model('ReviewModel');
            $data['can_review'] = $this->ReviewModel->isVerifiedPurchase($id, $ids['customer_id']);
        }
        
        // Generate QR code for product
        $data['qr_code_url'] = product_qr_code($id, 150);
        
        $this->call->view('shop/product', $data);
    }
    
    /**
     * Get product reviews with customer information
     * Only shows approved reviews from verified purchases
     */
    private function getProductReviews($product_id)
    {
        $sql = "
            SELECT 
                pr.id,
                pr.product_id,
                pr.customer_id,
                pr.customer_name,
                pr.review_title,
                pr.review_text,
                pr.verified_purchase,
                pr.helpful_count,
                pr.created_at,
                prat.rating,
                c.name as customer_db_name,
                c.email as customer_email,
                c.phone as customer_phone
            FROM product_reviews pr
            INNER JOIN product_ratings prat ON pr.rating_id = prat.id
            LEFT JOIN customers c ON pr.customer_id = c.id
            WHERE pr.product_id = ?
                AND pr.status = 'approved'
            ORDER BY pr.created_at DESC
            LIMIT 50
        ";
        
        $reviews = $this->db->raw($sql, [$product_id])->fetchAll(PDO::FETCH_ASSOC);
        
        // Format reviews
        foreach ($reviews as &$review) {
            // Use stored name or fallback to database name
            if (empty($review['customer_name']) && !empty($review['customer_db_name'])) {
                $review['customer_name'] = $review['customer_db_name'];
            }
            if (empty($review['customer_name'])) {
                $review['customer_name'] = 'Anonymous';
            }
            
            // Format date
            $review['formatted_date'] = date('F j, Y', strtotime($review['created_at']));
            
            // Generate star HTML
            $rating = (int)$review['rating'];
            $stars = '';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    $stars .= '<span style="color: #FBBF24; font-size: 16px;">★</span>';
                } else {
                    $stars .= '<span style="color: #D1D5DB; font-size: 16px;">★</span>';
                }
            }
            $review['stars_html'] = $stars;
            
            // Extract location (city or first part of address)
            if (!empty($review['customer_location'])) {
                $location_parts = explode(',', $review['customer_location']);
                $review['display_location'] = trim($location_parts[0]);
            } else {
                $review['display_location'] = '';
            }
        }
        
        return $reviews;
    }
    
    /**
     * Get featured testimonials from customer reviews
     */
    private function getFeaturedTestimonials($limit = 6)
    {
        $sql = "
            SELECT 
                pr.id,
                pr.customer_name,
                pr.review_text,
                pr.created_at,
                prat.rating,
                c.name as customer_db_name,
                p.name as product_name
            FROM product_reviews pr
            INNER JOIN product_ratings prat ON pr.rating_id = prat.id
            LEFT JOIN customers c ON pr.customer_id = c.id
            LEFT JOIN products p ON pr.product_id = p.id
            WHERE pr.status = 'approved'
                AND prat.rating >= 4
                AND pr.review_text IS NOT NULL
                AND CHAR_LENGTH(pr.review_text) >= 10
            ORDER BY prat.rating DESC, pr.created_at DESC
            LIMIT ?
        ";
        
        $testimonials = $this->db->raw($sql, [$limit])->fetchAll(PDO::FETCH_ASSOC);
        
        // Format testimonials
        foreach ($testimonials as &$testimonial) {
            // Use stored name or fallback
            if (empty($testimonial['customer_name']) && !empty($testimonial['customer_db_name'])) {
                $testimonial['customer_name'] = $testimonial['customer_db_name'];
            }
            if (empty($testimonial['customer_name'])) {
                $testimonial['customer_name'] = 'Anonymous Customer';
            }
            
            // Generate star HTML
            $rating = (int)$testimonial['rating'];
            $stars = str_repeat('⭐', $rating);
            $testimonial['stars'] = $stars;
            
            // Calculate time ago
            $testimonial['time_ago'] = $this->timeAgo($testimonial['created_at']);
        }
        
        return $testimonials;
    }
    
    /**
     * Convert timestamp to human-readable time ago
     */
    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
        } else {
            $years = floor($diff / 31536000);
            return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
        }
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
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0);
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1);
        
        if ($product_id <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                exit;
            } else {
                $this->session->set_flashdata(['error' => 'Invalid product ID']);
                redirect('shop');
                return;
            }
        }
        
        if ($quantity <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
                exit;
            } else {
                $this->session->set_flashdata(['error' => 'Invalid quantity']);
                redirect('shop');
                return;
            }
        }
        
        // Get product details
        $product = $this->ProductModel->find($product_id);
        if (!$product) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            } else {
                $this->session->set_flashdata(['error' => 'Product not found']);
                redirect('shop');
                return;
            }
        }
        
        // Check stock
        if ($product['stock'] < $quantity) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit;
            } else {
                $this->session->set_flashdata(['error' => 'Insufficient stock for ' . $product['name']]);
                redirect('shop');
                return;
            }
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
        
        // Return JSON for AJAX or redirect for direct access
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Product added to cart',
                'cart_count' => $cart_count,
                'cart_total' => $cart_total
            ]);
            exit;
        } else {
            $this->session->set_flashdata(['success' => $product['name'] . ' added to cart']);
            redirect('checkout');
        }
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
