<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CartModel extends Model
{
    protected $table = 'cart';
    protected $primary_key = 'id';
    
    /**
     * Get cart items for a customer or session
     */
    public function getCart($customer_id = null, $session_id = null)
    {
        $sql = "SELECT c.*, p.name as product_name, p.price, p.stock, p.sku,
                (SELECT image_url FROM product_images WHERE product_id = c.product_id LIMIT 1) as image
                FROM cart c
                INNER JOIN products p ON c.product_id = p.id
                WHERE ";
        
        $params = [];
        
        if ($customer_id) {
            $sql .= "c.customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= "c.session_id = ? AND c.customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add or update cart item
     */
    public function addOrUpdate($product_id, $quantity, $customer_id = null, $session_id = null)
    {
        // Check if item already exists
        $sql = "SELECT * FROM cart WHERE product_id = ?";
        $params = [$product_id];
        
        if ($customer_id) {
            $sql .= " AND customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= " AND session_id = ? AND customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $existing = $this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC);
        
        $now = date('Y-m-d H:i:s');
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            $this->db->table('cart')
                ->where('id', $existing['id'])
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => $now
                ]);
            return $existing['id'];
        } else {
            // Insert new item
            return $this->db->table('cart')->insert([
                'customer_id' => $customer_id,
                'session_id' => $session_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function updateQuantity($cart_id, $quantity)
    {
        return $this->db->table('cart')
            ->where('id', $cart_id)
            ->update([
                'quantity' => $quantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
    
    /**
     * Remove cart item
     */
    public function removeItem($cart_id, $customer_id = null, $session_id = null)
    {
        $query = $this->db->table('cart')->where('id', $cart_id);
        
        if ($customer_id) {
            $query->where('customer_id', $customer_id);
        } else {
            $query->where('session_id', $session_id)->where('customer_id', null);
        }
        
        return $query->delete();
    }
    
    /**
     * Clear all cart items for a customer or session
     */
    public function clearCart($customer_id = null, $session_id = null)
    {
        if ($customer_id) {
            return $this->db->table('cart')->where('customer_id', $customer_id)->delete();
        } else {
            return $this->db->table('cart')
                ->where('session_id', $session_id)
                ->where('customer_id', null)
                ->delete();
        }
    }
    
    /**
     * Get cart count
     */
    public function getCartCount($customer_id = null, $session_id = null)
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE ";
        $params = [];
        
        if ($customer_id) {
            $sql .= "customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= "session_id = ? AND customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $result = $this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Migrate guest cart to customer cart on login
     */
    public function migrateGuestCart($session_id, $customer_id)
    {
        // Get guest cart items
        $guestItems = $this->db->table('cart')
            ->where('session_id', $session_id)
            ->where('customer_id', null)
            ->get_all();
        
        foreach ($guestItems as $item) {
            // Check if customer already has this product
            $existing = $this->db->table('cart')
                ->where('customer_id', $customer_id)
                ->where('product_id', $item['product_id'])
                ->get();
            
            if ($existing) {
                // Merge quantities
                $this->db->table('cart')
                    ->where('id', $existing['id'])
                    ->update([
                        'quantity' => $existing['quantity'] + $item['quantity'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Transfer to customer
                $this->db->table('cart')
                    ->where('id', $item['id'])
                    ->update([
                        'customer_id' => $customer_id,
                        'session_id' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
        }
        
        // Delete any remaining guest items
        $this->clearCart(null, $session_id);
    }
    
    /**
     * Clean old guest carts (older than 30 days)
     */
    public function cleanOldGuestCarts()
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        return $this->db->table('cart')
            ->where('customer_id', null)
            ->where('created_at <', $thirtyDaysAgo)
            ->delete();
    }
}
