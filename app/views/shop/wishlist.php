<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - TechTrack</title>
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
            padding: 20px 0;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo { 
            font-size: 24px; 
            font-weight: 700; 
            letter-spacing: -0.5px;
            color: #fff;
            text-decoration: none;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6B7280;
        }

        .wishlist-count {
            display: inline-block;
            background: #EF4444;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
            border-color: #3B82F6;
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #F9FAFB;
        }

        .product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 16px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #111827;
            line-height: 1.4;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 22px;
            font-weight: 700;
            color: #3B82F6;
            margin-bottom: 4px;
        }

        .product-stock {
            font-size: 13px;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .stock-available {
            color: #059669;
        }

        .stock-low {
            color: #F59E0B;
        }

        .stock-out {
            color: #DC2626;
        }

        .product-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 14px;
            flex: 1;
        }

        .btn-primary {
            background: #3B82F6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-danger {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FCA5A5;
        }

        .btn-danger:hover {
            background: #DC2626;
            color: #fff;
        }

        /* Remove Button */
        .remove-wishlist-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .remove-wishlist-btn:hover {
            background: #FEE2E2;
            transform: scale(1.1);
        }

        .remove-wishlist-btn svg {
            width: 20px;
            height: 20px;
            stroke: #DC2626;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #111827;
        }

        .empty-text {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }

            .product-image {
                height: 160px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="<?= site_url('shop') ?>" class="logo">TechTrack</a>
            <a href="<?= site_url('shop') ?>" class="back-btn">← Back to Shop</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                ❤️ My Wishlist
                <?php if (!empty($products)): ?>
                    <span class="wishlist-count"><?= count($products) ?></span>
                <?php endif; ?>
            </h1>
            <p class="page-subtitle">Products you love and want to buy later</p>
        </div>

        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): 
                    $stock = (int)($product['stock'] ?? 0);
                    $stockClass = $stock > 10 ? 'stock-available' : ($stock > 0 ? 'stock-low' : 'stock-out');
                    $stockText = $stock > 10 ? 'In Stock' : ($stock > 0 ? 'Only ' . $stock . ' left' : 'Out of Stock');
                    
                    $imageUrl = !empty($product['main_image']) 
                        ? $product['main_image'] 
                        : 'https://via.placeholder.com/300x200?text=No+Image';
                ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <button class="remove-wishlist-btn" onclick="removeFromWishlist(<?= $product['id'] ?>, '<?= html_escape($product['name']) ?>')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <img src="<?= html_escape($imageUrl) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                        
                        <div class="product-info">
                            <h3 class="product-name"><?= html_escape($product['name']) ?></h3>
                            <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                            <div class="product-stock <?= $stockClass ?>"><?= $stockText ?></div>
                            
                            <div class="product-actions">
                                <?php if ($stock > 0): ?>
                                    <button class="btn btn-primary add-to-cart-btn" 
                                            data-product-id="<?= $product['id'] ?>"
                                            data-product-name="<?= html_escape($product['name']) ?>">
                                        🛒 Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">💔</div>
                <h2 class="empty-title">Your Wishlist is Empty</h2>
                <p class="empty-text">Start adding products you love to your wishlist!</p>
                <a href="<?= site_url('shop') ?>" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Remove from wishlist
        function removeFromWishlist(productId, productName) {
            if (!confirm('Remove "' + productName + '" from wishlist?')) {
                return;
            }

            fetch('<?= site_url('shop/remove-from-wishlist') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove card from view
                    const card = document.querySelector(`.product-card[data-product-id="${productId}"]`);
                    if (card) {
                        card.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if wishlist is empty
                            const grid = document.querySelector('.products-grid');
                            if (grid && grid.children.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                    
                    showNotification('Removed from wishlist', 'success');
                } else {
                    alert(data.message || 'Failed to remove from wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const originalText = this.innerHTML;
                
                // Disable button and show loading
                this.disabled = true;
                this.innerHTML = 'Adding...';
                
                fetch('<?= site_url('shop/add-to-cart') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        this.innerHTML = '✓ Added';
                        this.style.background = '#10B981';
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.background = '';
                            this.disabled = false;
                        }, 2000);
                        
                        showNotification('Added to cart: ' + productName, 'success');
                    } else {
                        alert(data.message || 'Failed to add to cart');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: ${type === 'success' ? '#10B981' : '#3B82F6'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-size: 14px;
                font-weight: 500;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
            @keyframes fadeOut {
                from { opacity: 1; transform: scale(1); }
                to { opacity: 0; transform: scale(0.8); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
