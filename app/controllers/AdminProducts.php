<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminProducts extends AdminBase
{
    private $cache;
    
    public function __construct()
    {
        parent::__construct();
        $this->call->library('session');
        $this->call->model('ProductModel');
        
        // Load cache helper
        $this->call->helper(['cache']);
        $this->cache = new SimpleCache();
    }

    public function index()
    {
        // Load paginator helper
        $this->call->helper(['paginator']);
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20; // 20 products per page for admin
        
        // Create cache key
        $cacheKey = 'admin_products_page_' . $page;
        
        // Try to get from cache (2 minutes TTL for admin - shorter because data changes frequently)
        $cachedData = $this->cache->get($cacheKey);
        
        if ($cachedData !== null) {
            $data = $cachedData;
        } else {
            // Count total products
            $totalProducts = $this->ProductModel->countProducts();
            
            // Create paginator
            $paginator = new Paginator($totalProducts, $perPage, $page);
            
            // Get paginated products with images (optimized - no N+1 query)
            $data['products'] = $this->ProductModel->getPaginatedWithImages(
                $paginator->getLimit(),
                $paginator->getOffset()
            );
            
            // Pagination data
            $data['pagination'] = $paginator->getPaginationData();
            
            // Cache for 2 minutes
            $this->cache->set($cacheKey, $data, 120);
        }
        
        // Extract product images from optimized query
        $productImages = [];
        foreach ($data['products'] as $p) {
            $productImages[$p['id']] = $p['main_image'] ?? null;
        }
        $data['product_images'] = $productImages;
        
        // Calculate summary stats
        $totalProducts = count($data['products']);
        $lowStockCount = 0;
        $outOfStockCount = 0;
        $totalValue = 0;
        
        foreach ($data['products'] as $p) {
            $stock = (int)($p['stock'] ?? 0);
            $threshold = (int)($p['low_stock_threshold'] ?? 5);
            if ($stock <= 0) $outOfStockCount++;
            elseif ($stock <= $threshold) $lowStockCount++;
            $totalValue += $stock * (float)($p['price'] ?? 0);
        }
        
        $data['total_products'] = $totalProducts;
        $data['low_stock_count'] = $lowStockCount;
        $data['out_of_stock_count'] = $outOfStockCount;
        $data['total_value'] = $totalValue;
        
        // Pass flash messages to view
        $data['flash_success'] = $this->session->flashdata('success');
        $data['flash_error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/products/index', $data);
    }

    public function create()
    {
        $this->call->view('admin/layouts/header');
        $this->call->view('admin/layouts/sidebar');
        $this->call->view('admin/products/form');
    }

    public function store()
    {
        // Check if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                // Validate required fields
                if (empty($_POST['name'])) {
                    throw new Exception('Product name is required.');
                }
                if (empty($_POST['price'])) {
                    throw new Exception('Product price is required.');
                }
                if (!isset($_POST['stock'])) {
                    throw new Exception('Product stock is required.');
                }
                
                $payload = $_POST;
                $id = $this->ProductModel->insert($payload);
                
                if (!$id) {
                    throw new Exception('Failed to create product in database.');
                }
                
                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $this->handleImageUploads($id, $_FILES['images']);
                }
                
                // Clear product cache
                $this->clearProductCache();
                
                echo json_encode(['success' => true, 'message' => 'Product created successfully.', 'id' => $id]);
            } catch (Exception $e) {
                error_log('AdminProducts store error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Traditional form submission
        $payload = $_POST;
        $id = $this->ProductModel->insert($payload);
        
        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $this->handleImageUploads($id, $_FILES['images']);
        }
        
        // Clear product cache
        $this->clearProductCache();
        
        $this->session->set_flashdata('success', 'Product created successfully.');
        redirect('admin/products');
    }

    public function edit($id)
    {
        $data['product'] = $this->ProductModel->find($id);
        
        if (!$data['product']) {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('admin/products');
            return;
        }
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/products/form', $data);
    }

    public function get($id)
    {
        // Get product data via AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $product = $this->ProductModel->find($id);
                if ($product) {
                    // Get product images
                    $productId = (int)$id;
                    $imagesStmt = $this->db->raw("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC", array($productId));
                    if ($imagesStmt) {
                        $images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $images = array();
                    }
                    $product['images'] = $images;
                    echo json_encode(array('success' => true, 'product' => $product));
                } else {
                    echo json_encode(array('success' => false, 'message' => 'Product not found.'));
                }
            } catch (Exception $e) {
                echo json_encode(array('success' => false, 'message' => $e->getMessage()));
            }
            exit;
        }
        // Return 404 if not AJAX
        show_404();
    }

    public function update($id)
    {
        // Check if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $this->ProductModel->update($id, $_POST);
                
                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $this->handleImageUploads($id, $_FILES['images']);
                }
                
                echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Traditional form submission
        $this->ProductModel->update($id, $_POST);
        
        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $this->handleImageUploads($id, $_FILES['images']);
        }
        
        // Clear product cache
        $this->clearProductCache();
        
        $this->session->set_flashdata('success', 'Product updated successfully.');
        redirect('admin/products');
    }

    // Media management: images
    public function images($id)
    {
        $data['product'] = $this->ProductModel->find($id);
        $data['images'] = $this->db->table('product_images')->where('product_id', $id)->order_by('is_main DESC')->get_all();
        // flash messages
        $data['flash_success'] = $this->session->flashdata('success');
        $data['flash_error'] = $this->session->flashdata('error');
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/products/images', $data);
    }

    public function add_image($id)
    {
        $url = $_POST['image_url'] ?? '';
        $is_main = !empty($_POST['is_main']) ? 1 : 0;
        if ($is_main) {
            // unset other mains
            $this->db->table('product_images')->where('product_id', $id)->update(['is_main' => 0]);
        }
        $this->db->table('product_images')->insert(['product_id' => $id, 'image_url' => $url, 'is_main' => $is_main]);
        $this->session->set_flashdata('success', 'Image URL added successfully.');
        redirect('admin/products/images/' . $id);
    }

    public function delete_image($image_id)
    {
        // Check if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $img = $this->db->table('product_images')->where('id', (int)$image_id)->get();
                if ($img) {
                    // Delete physical file
                    $url = $img['image_url'] ?? '';
                    $baseUrl = base_url();
                    if (strpos($url, $baseUrl) === 0) {
                        $relativePath = str_replace($baseUrl, '', $url);
                        $fullPath = realpath(__DIR__ . '/../../' . $relativePath);
                        if ($fullPath && file_exists($fullPath) && is_file($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                    
                    $this->db->table('product_images')->where('id', (int)$image_id)->delete();
                    echo json_encode(['success' => true, 'message' => 'Image deleted successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Image not found.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Traditional link deletion
        $img = $this->db->table('product_images')->where('id', (int)$image_id)->get();
        if ($img) {
            // attempt to delete physical file if under /public/uploads/products
            $url = $img['image_url'] ?? '';
            if (is_string($url) && strpos($url, '/public/uploads/products/') === 0) {
                $root = realpath(__DIR__ . '/../../');
                if ($root) {
                    $fullPath = $root . str_replace('/', DIRECTORY_SEPARATOR, $url);
                    if (file_exists($fullPath) && is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }
            $this->db->table('product_images')->where('id', (int)$image_id)->delete();
            $this->session->set_flashdata('success', 'Image deleted.');
            redirect('admin/products/images/' . $img['product_id']);
        }
        $this->session->set_flashdata('error', 'Image not found.');
        redirect('admin/products');
    }

    public function upload_image($id)
    {
        // Accepts file input named 'image_file' and optional checkbox 'is_main'
        if (empty($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'No file selected or upload error.');
            redirect('admin/products/images/' . (int)$id);
        }

        $this->call->library('upload');
        $file = $_FILES['image_file'];
        $uploader = new Upload($file);
        // Allow common image types including webp
        $uploader->allowed_extensions(['gif','jpg','jpeg','png','webp'])
                 ->allowed_mimes(['image/gif','image/jpg','image/jpeg','image/png','image/webp'])
                 ->set_dir('public/uploads/products/' . (int)$id)
                 ->max_size(5) // 5 MB
                 ->is_image()
                 ->encrypt_name();

        if ($uploader->do_upload() !== TRUE) {
            $errors = $uploader->get_errors();
            $msg = !empty($errors) ? implode("\n", (array)$errors) : 'Upload failed.';
            $this->session->set_flashdata('error', $msg);
            redirect('admin/products/images/' . (int)$id);
        }

    $urlPath = '/public/uploads/products/' . (int)$id . '/' . $uploader->get_filename();

        $is_main = !empty($_POST['is_main']) ? 1 : 0;
        if ($is_main) {
            $this->db->table('product_images')->where('product_id', (int)$id)->update(['is_main' => 0]);
        }
        $this->db->table('product_images')->insert([
            'product_id' => (int)$id,
            'image_url'  => $urlPath,
            'is_main'    => $is_main,
        ]);
        $this->session->set_flashdata('success', 'Image uploaded successfully.');
        redirect('admin/products/images/' . (int)$id);
    }

    // Specs management
    public function specs($id)
    {
        $data['product'] = $this->ProductModel->find($id);
        $data['specs'] = $this->db->table('product_specs')->where('product_id', $id)->get_all();
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/products/specs', $data);
    }

    public function add_spec($id)
    {
        $name = $_POST['spec_name'] ?? '';
        $value = $_POST['spec_value'] ?? '';
        $this->db->table('product_specs')->insert(['product_id' => $id, 'spec_name' => $name, 'spec_value' => $value]);
        redirect('admin/products/specs/' . $id);
    }

    public function delete_spec($spec_id)
    {
        $spec = $this->db->table('product_specs')->where('id', (int)$spec_id)->get();
        if ($spec) {
            $this->db->table('product_specs')->where('id', (int)$spec_id)->delete();
            redirect('admin/products/specs/' . $spec['product_id']);
        }
        redirect('admin/products');
    }

    public function delete($id)
    {
        // Check if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $this->ProductModel->delete($id);
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Traditional link deletion
        $this->ProductModel->delete($id);
        
        // Clear product cache
        $this->clearProductCache();
        
        $this->session->set_flashdata('success', 'Product deleted successfully.');
        redirect('admin/products');
    }

    /**
     * Handle multiple image uploads for a product
     */
    private function handleImageUploads($productId, $files)
    {
        $uploadDir = 'public/uploads/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Check if this is the first image for the product
        $productIdInt = (int)$productId;
        $existingImages = $this->db->raw("SELECT COUNT(*) as count FROM product_images WHERE product_id = ?", array($productIdInt))->fetch(PDO::FETCH_ASSOC);
        $isFirstImage = ($existingImages['count'] == 0);

        // Process each uploaded file
        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $fileName = $files['name'][$i];
                $fileSize = $files['size'][$i];
                $fileType = $files['type'][$i];

                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($fileType, $allowedTypes)) {
                    continue;
                }

                // Validate file size (5MB max)
                if ($fileSize > 5 * 1024 * 1024) {
                    continue;
                }

                // Generate unique filename
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'product_' . $productId . '_' . time() . '_' . $i . '.' . $extension;
                $destination = $uploadDir . $newFileName;

                // Move uploaded file
                if (move_uploaded_file($tmpName, $destination)) {
                    // Save to database - keep 'public/' in the path for proper web access
                    $imageUrl = base_url() . $destination;
                    $isMain = ($isFirstImage && $i === 0) ? 1 : 0;

                    $this->db->raw(
                        "INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, ?)",
                        array($productIdInt, $imageUrl, $isMain)
                    );
                }
            }
        }
    }
    
    /**
     * Clear all product-related cache entries
     */
    private function clearProductCache()
    {
        // Clear all cache files - simpler and more reliable
        $cacheDir = ROOT_DIR . 'runtime/cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*.cache');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        
        // Also clear SimpleCache if it's being used
        if ($this->cache) {
            // The SimpleCache helper doesn't have a clear all method, 
            // but we've already cleared the files above
        }
    }
}

?>
