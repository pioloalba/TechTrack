<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primary_key = 'id';
    
    /**
     * Get wishlist items for a customer or session
     */
    public function getWishlist($customer_id = null, $session_id = null)
    {
        $sql = "SELECT w.*, p.name as product_name, p.price, p.stock, p.sku,
                (SELECT image_url FROM product_images WHERE product_id = w.product_id LIMIT 1) as image
                FROM wishlist w
                INNER JOIN products p ON w.product_id = p.id
                WHERE ";
        
        $params = [];
        
        if ($customer_id) {
            $sql .= "w.customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= "w.session_id = ? AND w.customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $sql .= " ORDER BY w.created_at DESC";
        
        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get wishlist product IDs only
     */
    public function getWishlistProductIds($customer_id = null, $session_id = null)
    {
        $sql = "SELECT product_id FROM wishlist WHERE ";
        $params = [];
        
        if ($customer_id) {
            $sql .= "customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= "session_id = ? AND customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $results = $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
        return array_column($results, 'product_id');
    }
    
    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($product_id, $customer_id = null, $session_id = null)
    {
        $sql = "SELECT COUNT(*) as count FROM wishlist WHERE product_id = ?";
        $params = [$product_id];
        
        if ($customer_id) {
            $sql .= " AND customer_id = ?";
            $params[] = $customer_id;
        } else {
            $sql .= " AND session_id = ? AND customer_id IS NULL";
            $params[] = $session_id;
        }
        
        $result = $this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }
    
    /**
     * Add item to wishlist
     */
    public function addItem($product_id, $customer_id = null, $session_id = null)
    {
        // Check if already exists
        if ($this->isInWishlist($product_id, $customer_id, $session_id)) {
            return false; // Already in wishlist
        }
        
        return $this->db->table('wishlist')->insert([
            'customer_id' => $customer_id,
            'session_id' => $session_id,
            'product_id' => $product_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Remove item from wishlist
     */
    public function removeItem($product_id, $customer_id = null, $session_id = null)
    {
        $query = $this->db->table('wishlist')->where('product_id', $product_id);
        
        if ($customer_id) {
            $query->where('customer_id', $customer_id);
        } else {
            $query->where('session_id', $session_id)->where('customer_id', null);
        }
        
        return $query->delete();
    }
    
    /**
     * Clear all wishlist items
     */
    public function clearWishlist($customer_id = null, $session_id = null)
    {
        if ($customer_id) {
            return $this->db->table('wishlist')->where('customer_id', $customer_id)->delete();
        } else {
            return $this->db->table('wishlist')
                ->where('session_id', $session_id)
                ->where('customer_id', null)
                ->delete();
        }
    }
    
    /**
     * Get wishlist count
     */
    public function getWishlistCount($customer_id = null, $session_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM wishlist WHERE ";
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
     * Migrate guest wishlist to customer wishlist on login
     */
    public function migrateGuestWishlist($session_id, $customer_id)
    {
        // Get guest wishlist items
        $guestItems = $this->db->table('wishlist')
            ->where('session_id', $session_id)
            ->where('customer_id', null)
            ->get_all();
        
        foreach ($guestItems as $item) {
            // Check if customer already has this product
            $existing = $this->db->table('wishlist')
                ->where('customer_id', $customer_id)
                ->where('product_id', $item['product_id'])
                ->get();
            
            if (!$existing) {
                // Transfer to customer
                $this->db->table('wishlist')
                    ->where('id', $item['id'])
                    ->update([
                        'customer_id' => $customer_id,
                        'session_id' => null
                    ]);
            }
        }
        
        // Delete any remaining guest items (duplicates)
        $this->clearWishlist(null, $session_id);
    }
    
    /**
     * Clean old guest wishlists (older than 30 days)
     */
    public function cleanOldGuestWishlists()
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        return $this->db->table('wishlist')
            ->where('customer_id', null)
            ->where('created_at <', $thirtyDaysAgo)
            ->delete();
    }
}
