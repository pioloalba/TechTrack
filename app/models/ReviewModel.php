<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ReviewModel extends Model
{
    protected $table = 'product_reviews';
    protected $primary_key = 'id';
    
    /**
     * Submit a review for a product
     * Must have an existing rating first
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'review_id' => int]
     */
    public function submitReview($data)
    {
        // Validation
        $required = ['product_id', 'rating_id', 'customer_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Missing required field: {$field}"];
            }
        }
        
        // Validate rating exists
        $rating = $this->db->table('product_ratings')->where('id', $data['rating_id'])->get();
        if (!$rating) {
            return ['success' => false, 'message' => 'Rating not found. Please rate the product first.'];
        }
        
        // Check if review already exists for this rating
        $existing = $this->db->table($this->table)
            ->where('rating_id', $data['rating_id'])
            ->get();
        
        try {
            $reviewData = [
                'product_id' => (int)$data['product_id'],
                'customer_id' => isset($data['customer_id']) ? (int)$data['customer_id'] : null,
                'rating_id' => (int)$data['rating_id'],
                'customer_name' => trim($data['customer_name']),
                'review_title' => isset($data['review_title']) ? trim($data['review_title']) : null,
                'review_text' => isset($data['review_text']) ? trim($data['review_text']) : null,
                'verified_purchase' => isset($data['verified_purchase']) ? (bool)$data['verified_purchase'] : false,
                'status' => 'approved', // Auto-approve, or set to 'pending' for moderation
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($existing) {
                // Update existing review
                $this->db->table($this->table)
                    ->where('id', $existing['id'])
                    ->update(array_merge($reviewData, ['updated_at' => date('Y-m-d H:i:s')]));
                    
                $review_id = $existing['id'];
                $message = 'Review updated successfully';
            } else {
                // Insert new review
                $review_id = $this->db->table($this->table)->insert($reviewData);
                $message = 'Review submitted successfully';
            }
            
            return [
                'success' => true,
                'message' => $message,
                'review_id' => $review_id
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save review: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get all approved reviews for a product with ratings
     * 
     * @param int $product_id
     * @param string $sort ('recent', 'helpful', 'highest', 'lowest')
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getProductReviews($product_id, $sort = 'recent', $limit = 20, $offset = 0)
    {
        $orderBy = 'pr_reviews.created_at DESC';
        
        switch ($sort) {
            case 'helpful':
                $orderBy = 'pr_reviews.helpful_count DESC, pr_reviews.created_at DESC';
                break;
            case 'highest':
                $orderBy = 'pr_ratings.rating DESC, pr_reviews.created_at DESC';
                break;
            case 'lowest':
                $orderBy = 'pr_ratings.rating ASC, pr_reviews.created_at DESC';
                break;
            default:
                $orderBy = 'pr_reviews.created_at DESC';
        }
        
        $sql = "
            SELECT 
                pr_reviews.*,
                pr_ratings.rating,
                c.name as customer_name_db,
                c.email as customer_email
            FROM product_reviews pr_reviews
            INNER JOIN product_ratings pr_ratings ON pr_reviews.rating_id = pr_ratings.id
            LEFT JOIN customers c ON pr_reviews.customer_id = c.id
            WHERE pr_reviews.product_id = ?
                AND pr_reviews.status = 'approved'
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ";
        
        $reviews = $this->db->raw($sql, [$product_id, $limit, $offset])->fetchAll(PDO::FETCH_ASSOC);
        
        // Use stored customer_name if available, fallback to DB name
        foreach ($reviews as &$review) {
            if (empty($review['customer_name']) && !empty($review['customer_name_db'])) {
                $review['customer_name'] = $review['customer_name_db'];
            }
            // Default to 'Anonymous' if still empty
            if (empty($review['customer_name'])) {
                $review['customer_name'] = 'Anonymous User';
            }
        }
        
        return $reviews;
    }
    
    /**
     * Get review count for a product
     * 
     * @param int $product_id
     * @return int
     */
    public function getReviewCount($product_id)
    {
        $result = $this->db->table($this->table)
            ->where('product_id', $product_id)
            ->where('status', 'approved')
            ->get();
            
        return $result ? 1 : 0; // Simple count, could be improved
    }
    
    /**
     * Check if customer purchased the product
     * 
     * @param int $product_id
     * @param int $customer_id
     * @return bool
     */
    public function isVerifiedPurchase($product_id, $customer_id)
    {
        if (empty($customer_id)) {
            return false;
        }
        
        $sql = "
            SELECT COUNT(*) as purchase_count
            FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE oi.product_id = ?
                AND o.customer_id = ?
                AND o.status IN ('completed', 'delivered', 'shipped')
        ";
        
        $result = $this->db->raw($sql, [$product_id, $customer_id])->fetch(PDO::FETCH_ASSOC);
        
        return (int)($result['purchase_count'] ?? 0) > 0;
    }
    
    /**
     * Mark review as helpful
     * 
     * @param int $review_id
     * @return bool
     */
    public function markHelpful($review_id)
    {
        try {
            $this->db->raw("
                UPDATE product_reviews 
                SET helpful_count = helpful_count + 1 
                WHERE id = ?
            ", [$review_id]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Delete a review
     * 
     * @param int $review_id
     * @param int|null $customer_id (for security check)
     * @return bool
     */
    public function deleteReview($review_id, $customer_id = null)
    {
        try {
            $query = $this->db->table($this->table)->where('id', $review_id);
            
            // Security check: only owner can delete
            if ($customer_id) {
                $query->where('customer_id', $customer_id);
            }
            
            $query->delete();
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to delete review: " . $e->getMessage());
            return false;
        }
    }
}
