<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desktop Computers - TechTrack</title>
    <?php
        $fav = null;
        foreach (['favicon.ico','favicon.png'] as $f) {
            if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
        }
        if (!$fav) {
            foreach (['logo.png','logo.jpg','logo.jpeg','logo.svg'] as $f) {
                if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
            }
        }
    ?>
    <?php if ($fav): ?>
        <link rel="icon" href="<?= $fav ?>">
    <?php endif; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F9FAFB;
            color: #111827;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            color: #fff;
            padding: 16px 0;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .logo { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; display:flex; align-items:center; gap:10px; }
        .logo img { height: 32px; width: auto; display: block; }

        .search-bar {
            flex: 1;
            max-width: 600px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 48px 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
        }

        .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            color: #3B82F6;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 24px;
            color: #fff;
            font-size: 14px;
        }

        .header-actions a,
        .header-actions button {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        /* Navigation */
        .nav {
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            gap: 32px;
        }

        .nav-link {
            padding: 16px 0;
            color: #4B5563;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: #2563EB;
            border-bottom-color: #2563EB;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: #fff;
            padding: 60px 24px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .hero-section p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Content */
        .main-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6B7280;
            font-size: 16px;
        }

        .products-count {
            color: #3B82F6;
            font-weight: 600;
            margin-top: 16px;
            font-size: 14px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
            border: 1px solid #3B82F6;
        }

        .product-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #F3F4F6;
        }

        .product-info {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
            line-height: 1.4;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-description {
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 12px;
            line-height: 1.5;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: #2563EB;
            margin-bottom: 12px;
        }

        .product-stock {
            font-size: 13px;
            margin-bottom: 12px;
        }

        .stock-available { color: #10B981; font-weight: 500; }
        .stock-low { color: #F59E0B; font-weight: 500; }
        .stock-out { color: #EF4444; font-weight: 500; }

        .btn {
            width: 100%;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3B82F6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #374151;
            margin-top: 8px;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 24px;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #6B7280;
            font-size: 16px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 48px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #4B5563;
            background: #fff;
            border: 1px solid #E5E7EB;
            transition: all 0.2s;
        }

        .pagination a:hover {
            background: #F3F4F6;
            color: #2563EB;
        }

        .pagination .active {
            background: #3B82F6;
            color: #fff;
            border-color: #3B82F6;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Cart Badge */
        .cart-badge {
            background: #EF4444;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 700;
            position: absolute;
            top: -6px;
            right: -6px;
        }

        /* Footer */
        .footer {
            background: #1F2937;
            color: #D1D5DB;
            padding: 48px 24px 24px;
            margin-top: 80px;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            text-align: center;
        }

        .footer p {
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-wrap: wrap;
            }

            .search-bar {
                order: 3;
                max-width: 100%;
                width: 100%;
            }

            .nav-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .hero-section h1 {
                font-size: 32px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <?php
                $logo = null;
                foreach (['logo.png','logo.jpg','logo.jpeg','logo.svg'] as $f) {
                    if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $logo = base_url() . 'public/assets/img/' . $f; break; }
                }
            ?>
            <a href="<?= site_url('/') ?>" class="logo">
                <?php if ($logo): ?>
                    <img src="<?= $logo ?>" alt="TechTrack">
                <?php else: ?>
                    TechTrack
                <?php endif; ?>
            </a>
            
            <div class="search-bar">
                <form method="GET" action="<?= site_url('shop/desktops') ?>">
                    <input type="text" name="search" class="search-input" placeholder="Search desktop computers..." value="<?= htmlspecialchars($search_query ?? '') ?>">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>

            <div class="header-actions">
                <a href="<?= site_url('/') ?>">← Back to Home</a>
                <button onclick="viewCart()" style="position:relative;">
                    🛒 Cart
                    <span class="cart-badge" id="cart-count">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-container">
            <a href="<?= site_url('/') ?>" class="nav-link">Home</a>
            <a href="<?= site_url('shop') ?>" class="nav-link">All Products</a>
            <a href="<?= site_url('shop/desktops') ?>" class="nav-link active">Desktops</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <h1>🖥️ Desktop Computers</h1>
        <p>Powerful desktop PCs for gaming, productivity, and creative work</p>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h2>Pre-Built Desktop Systems</h2>
            <p>Browse our selection of ready-to-use desktop computers</p>
            <?php if (!empty($products)): ?>
                <p class="products-count">Showing <?= count($products) ?> of <?= $pagination['total'] ?? count($products) ?> desktops</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php 
                            $imageUrl = $product_images[$product['id']] ?? null;
                            if (!$imageUrl) {
                                $imageUrl = base_url() . 'public/assets/img/no-image.png';
                            } elseif (!preg_match('/^https?:\/\//', $imageUrl)) {
                                // Only add base_url for local images (not external URLs)
                                $imageUrl = base_url() . $imageUrl;
                            }
                        ?>
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image" onerror="this.src='<?= base_url() ?>public/assets/img/no-image.png'">
                        
                        <div class="product-info">
                            <div class="product-category">Desktop PC</div>
                            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-description"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 100)) ?><?= strlen($product['description'] ?? '') > 100 ? '...' : '' ?></div>
                            <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                            
                            <?php
                                $stock = $product['stock'] ?? 0;
                                if ($stock > 10) {
                                    echo '<div class="product-stock stock-available">✓ In Stock (' . $stock . ' available)</div>';
                                } elseif ($stock > 0) {
                                    echo '<div class="product-stock stock-low">⚠️ Low Stock (' . $stock . ' left)</div>';
                                } else {
                                    echo '<div class="product-stock stock-out">✗ Out of Stock</div>';
                                }
                            ?>

                            <?php if ($stock > 0): ?>
                                <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($product['name'])) ?>', <?= $product['price'] ?>)">
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" disabled>
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>">← Previous</a>
                    <?php else: ?>
                        <span class="disabled">← Previous</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $pagination['current_page']): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>">Next →</a>
                    <?php else: ?>
                        <span class="disabled">Next →</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🖥️</div>
                <h3>No Desktops Found</h3>
                <p>We couldn't find any desktop computers<?= !empty($search_query) ? ' matching "' . htmlspecialchars($search_query) . '"' : '' ?>.</p>
                <?php if (!empty($search_query)): ?>
                    <a href="<?= site_url('shop/desktops') ?>" class="btn btn-primary" style="margin-top:24px;max-width:200px;margin-left:auto;margin-right:auto;">Clear Search</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> TechTrack. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Update cart count on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        function updateCartCount() {
            fetch('<?= site_url("shop/get-cart") ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-count').textContent = data.cart_count || 0;
                    }
                })
                .catch(err => console.error('Error updating cart count:', err));
        }

        function addToCart(productId, productName, productPrice) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('<?= site_url("shop/add-to-cart") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + productName + ' added to cart!');
                    updateCartCount();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Error adding to cart:', err);
                alert('Failed to add item to cart');
            });
        }

        function viewCart() {
            window.location.href = '<?= site_url("checkout") ?>';
        }
    </script>
</body>
</html>
