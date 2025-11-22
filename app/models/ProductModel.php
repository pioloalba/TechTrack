<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primary_key = 'id';
    
    /**
     * Get paginated products with images (optimized - no N+1 query)
     */
    public function getPaginatedWithImages($limit, $offset, $search = '', $category = '')
    {
        $params = [];
        $sql = "SELECT p.*, 
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id LIMIT 1) as main_image
                FROM products p
                WHERE 1=1";
        
        if (!empty($search)) {
            $sql .= " AND (LOWER(p.name) LIKE ? OR LOWER(p.description) LIKE ? OR LOWER(p.category) LIKE ?)";
            $searchTerm = '%' . strtolower($search) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($category)) {
            $sql .= " AND LOWER(p.category) = ?";
            $params[] = strtolower($category);
        }
        
        $sql .= " ORDER BY p.name ASC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        
        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count products for pagination
     */
    public function countProducts($search = '', $category = '')
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
        
        if (!empty($search)) {
            $sql .= " AND (LOWER(name) LIKE ? OR LOWER(description) LIKE ? OR LOWER(category) LIKE ?)";
            $searchTerm = '%' . strtolower($search) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($category)) {
            $sql .= " AND LOWER(category) = ?";
            $params[] = strtolower($category);
        }
        
        $result = $this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Get all products with images in single query (optimized)
     */
    public function getAllWithImages()
    {
        $sql = "SELECT p.*, 
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id LIMIT 1) as main_image
                FROM products p
                ORDER BY p.name ASC";
        
        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts()
    {
        $sql = "SELECT * FROM products WHERE stock <= low_stock_threshold ORDER BY stock ASC";
        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts()
    {
        $sql = "SELECT * FROM products WHERE stock = 0 ORDER BY name ASC";
        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get products by category with images for PC builder
     */
    public function getByCategory($category)
    {
        $sql = "SELECT p.*, 
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id LIMIT 1) as image_url
                FROM products p
                WHERE LOWER(p.category) = ?
                ORDER BY p.name ASC";
        
        return $this->db->raw($sql, [strtolower($category)])->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
