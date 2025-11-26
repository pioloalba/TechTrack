<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class RatingModel extends Model
{
    protected $table = 'product_ratings';
    protected $primary_key = 'id';
    
    /**
     * Submit or update a rating
     * Prevents duplicate ratings from same user/session
     * 
     * @param int $product_id
     * @param int $rating (1-5)
     * @param int|null $customer_id
     * @param string|null $session_id
     * @return array ['success' => bool, 'message' => string, 'rating_id' => int]
     */
    public function submitRating($product_id, $rating, $customer_id = null, $session_id = null)
    {
        // Validation
        if (empty($product_id) || !is_numeric($product_id)) {
            return ['success' => false, 'message' => 'Invalid product ID'];
        }
        
        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5 stars'];
        }
        
        if (empty($customer_id) && empty($session_id)) {
            return ['success' => false, 'message' => 'Unable to identify user'];
        }
        
        // Check if product exists
        $product = $this->db->table('products')->where('id', $product_id)->get();
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check for existing rating
        $existing = $this->getCustomerRating($product_id, $customer_id, $session_id);
        
        try {
            if ($existing) {
                // Update existing rating
                $this->db->table($this->table)
                    ->where('id', $existing['id'])
                    ->update([
                        'rating' => (int)$rating,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                $rating_id = $existing['id'];
                $message = 'Rating updated successfully';
            } else {
                // Insert new rating
                $rating_id = $this->db->table($this->table)->insert([
                    'product_id' => (int)$product_id,
                    'customer_id' => $customer_id ? (int)$customer_id : null,
                    'session_id' => $session_id,
                    'rating' => (int)$rating,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                $message = 'Rating submitted successfully';
            }
            
            // Update product rating summary
            $this->updateProductRatingSummary($product_id);
            
            return [
                'success' => true,
                'message' => $message,
                'rating_id' => $rating_id,
                'is_update' => (bool)$existing
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save rating: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get customer's existing rating for a product
     * 
     * @param int $product_id
     * @param int|null $customer_id
     * @param string|null $session_id
     * @return array|null
     */
    public function getCustomerRating($product_id, $customer_id = null, $session_id = null)
    {
        $query = $this->db->table($this->table)->where('product_id', $product_id);
        
        if ($customer_id) {
            $query->where('customer_id', $customer_id);
        } else if ($session_id) {
            $query->where('session_id', $session_id);
            $query->where('customer_id IS NULL', null, false);
        } else {
            return null;
        }
        
        return $query->get();
    }
    
    /**
     * Calculate and update product rating summary
     * Updates products.rating and products.review_count
     * 
     * @param int $product_id
     * @return bool
     */
    public function updateProductRatingSummary($product_id)
    {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_ratings,
                    AVG(rating) as avg_rating
                FROM product_ratings
                WHERE product_id = ?
            ";
            
            $result = $this->db->raw($sql, [$product_id])->fetch(PDO::FETCH_ASSOC);
            
            $totalRatings = (int)($result['total_ratings'] ?? 0);
            $avgRating = $totalRatings > 0 ? round((float)$result['avg_rating'], 2) : 0.00;
            
            // Update products table
            $this->db->table('products')
                ->where('id', $product_id)
                ->update([
                    'rating' => $avgRating,
                    'review_count' => $totalRatings
                ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to update product rating summary: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product rating statistics
     * 
     * @param int $product_id
     * @return array
     */
    public function getProductRatingStats($product_id)
    {
        // Overall stats
        $sql = "
            SELECT 
                COUNT(*) as total_ratings,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            FROM product_ratings
            WHERE product_id = ?
        ";
        
        $stats = $this->db->raw($sql, [$product_id])->fetch(PDO::FETCH_ASSOC);
        
        $total = (int)($stats['total_ratings'] ?? 0);
        
        return [
            'total_ratings' => $total,
            'average_rating' => $total > 0 ? round((float)$stats['avg_rating'], 2) : 0.00,
            'distribution' => [
                5 => (int)($stats['five_star'] ?? 0),
                4 => (int)($stats['four_star'] ?? 0),
                3 => (int)($stats['three_star'] ?? 0),
                2 => (int)($stats['two_star'] ?? 0),
                1 => (int)($stats['one_star'] ?? 0)
            ],
            'percentages' => [
                5 => $total > 0 ? round(((int)($stats['five_star'] ?? 0) / $total) * 100, 1) : 0,
                4 => $total > 0 ? round(((int)($stats['four_star'] ?? 0) / $total) * 100, 1) : 0,
                3 => $total > 0 ? round(((int)($stats['three_star'] ?? 0) / $total) * 100, 1) : 0,
                2 => $total > 0 ? round(((int)($stats['two_star'] ?? 0) / $total) * 100, 1) : 0,
                1 => $total > 0 ? round(((int)($stats['one_star'] ?? 0) / $total) * 100, 1) : 0
            ]
        ];
    }
    
    /**
     * Delete a rating
     * 
     * @param int $rating_id
     * @param int|null $customer_id (for security check)
     * @return bool
     */
    public function deleteRating($rating_id, $customer_id = null)
    {
        try {
            // Get rating info first
            $rating = $this->db->table($this->table)->where('id', $rating_id)->get();
            
            if (!$rating) {
                return false;
            }
            
            // Security check: only owner can delete
            if ($customer_id && (int)$rating['customer_id'] !== (int)$customer_id) {
                return false;
            }
            
            $product_id = $rating['product_id'];
            
            // Delete rating
            $this->db->table($this->table)->where('id', $rating_id)->delete();
            
            // Update product summary
            $this->updateProductRatingSummary($product_id);
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to delete rating: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all ratings for a product with customer details
     * 
     * @param int $product_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getProductRatings($product_id, $limit = 50, $offset = 0)
    {
        $sql = "
            SELECT 
                pr.*,
                c.name as customer_name,
                c.email as customer_email
            FROM product_ratings pr
            LEFT JOIN customers c ON pr.customer_id = c.id
            WHERE pr.product_id = ?
            ORDER BY pr.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        return $this->db->raw($sql, [$product_id, $limit, $offset])->fetchAll(PDO::FETCH_ASSOC);
    }
}
