<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiRatings extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load models - use string format for single models
        $this->call->model('RatingModel');
        $this->call->model('ReviewModel');
        
        $this->call->library('session');
    }
    
    /**
     * Get customer/session identifiers
     */
    private function getIdentifiers()
    {
        $customer = $this->session->userdata('customer');
        $customer_id = ($customer && isset($customer['id'])) ? (int)$customer['id'] : null;
        $session_id = $this->session->session_id ?? session_id();
        
        return [
            'customer_id' => $customer_id,
            'session_id' => $session_id,
            'customer_name' => ($customer && isset($customer['name'])) ? $customer['name'] : null
        ];
    }
    
    /**
     * Submit or update a product rating
     * POST /api/ratings/submit
     * Body: { product_id, rating }
     */
    public function submit()
    {
        header('Content-Type: application/json');
        
        try {
            
            // Get input
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            
            $product_id = $input['product_id'] ?? null;
            $rating = $input['rating'] ?? null;
            
            // Validate input
            if (empty($product_id) || empty($rating)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product ID and rating are required'
                ]);
                return;
            }
            
            // Get user identifiers
            $ids = $this->getIdentifiers();
            
            // Submit rating using properties array
            $result = $this->properties['RatingModel']->submitRating(
                $product_id,
                $rating,
                $ids['customer_id'],
                $ids['session_id']
            );
            
            // If successful, get updated stats
            if ($result['success']) {
                $result['stats'] = $this->properties['RatingModel']->getProductRatingStats($product_id);
            }
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log('Rating submission error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get product rating statistics
     * GET /api/ratings/stats/{product_id}
     */
    public function stats($product_id)
    {
        header('Content-Type: application/json');
        
        if (empty($product_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }
        
        $stats = $this->properties['RatingModel']->getProductRatingStats($product_id);
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get customer's rating for a product
     * GET /api/ratings/my-rating/{product_id}
     */
    public function my_rating($product_id)
    {
        header('Content-Type: application/json');
        
        if (empty($product_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }
        
        $ids = $this->getIdentifiers();
        
        $rating = $this->properties['RatingModel']->getCustomerRating(
            $product_id,
            $ids['customer_id'],
            $ids['session_id']
        );
        
        echo json_encode([
            'success' => true,
            'data' => $rating,
            'has_rating' => !empty($rating)
        ]);
    }
    
    /**
     * Submit a text review
     * POST /api/ratings/review
     * Body: { product_id, rating_id, review_title, review_text }
     */
    public function review()
    {
        header('Content-Type: application/json');
        
        // Get input
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $ids = $this->getIdentifiers();
        
        // Prepare review data
        $reviewData = [
            'product_id' => $input['product_id'] ?? null,
            'rating_id' => $input['rating_id'] ?? null,
            'customer_id' => $ids['customer_id'],
            'customer_name' => $input['customer_name'] ?? $ids['customer_name'] ?? 'Anonymous',
            'review_title' => $input['review_title'] ?? null,
            'review_text' => $input['review_text'] ?? null,
        ];
        
        // Check if verified purchase
        if ($ids['customer_id']) {
            $reviewData['verified_purchase'] = $this->properties['ReviewModel']->isVerifiedPurchase(
                $reviewData['product_id'],
                $ids['customer_id']
            );
        }
        
        $result = $this->properties['ReviewModel']->submitReview($reviewData);
        
        echo json_encode($result);
    }
    
    /**
     * Get all reviews for a product
     * GET /api/ratings/reviews/{product_id}?sort=recent&limit=20&offset=0
     */
    public function reviews($product_id)
    {
        header('Content-Type: application/json');
        
        if (empty($product_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }
        
        $sort = $_GET['sort'] ?? 'recent';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $reviews = $this->properties['ReviewModel']->getProductReviews($product_id, $sort, $limit, $offset);
        
        echo json_encode([
            'success' => true,
            'data' => $reviews,
            'count' => count($reviews)
        ]);
    }
    
    /**
     * Mark a review as helpful
     * POST /api/ratings/helpful/{review_id}
     */
    public function helpful($review_id)
    {
        header('Content-Type: application/json');
        
        if (empty($review_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Review ID is required'
            ]);
            return;
        }
        
        $result = $this->properties['ReviewModel']->markHelpful($review_id);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Marked as helpful' : 'Failed to mark as helpful'
        ]);
    }
    
    /**
     * Delete a rating
     * DELETE /api/ratings/delete/{rating_id}
     */
    public function delete($rating_id)
    {
        header('Content-Type: application/json');
        
        if (empty($rating_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Rating ID is required'
            ]);
            return;
        }
        
        $ids = $this->getIdentifiers();
        
        $result = $this->properties['RatingModel']->deleteRating($rating_id, $ids['customer_id']);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Rating deleted successfully' : 'Failed to delete rating'
        ]);
    }
}
