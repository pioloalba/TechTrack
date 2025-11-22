<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechTrack</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F9FAFB;
            color: #111827;
        }

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
            color: #fff;
            text-decoration: none;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 32px;
        }

        .section {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: #111827;
        }

        h3 {
            font-size: 18px;
            margin: 24px 0 16px;
            color: #374151;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 24px;
        }

        .cart-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #FAFAFA;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            background: #E5E7EB;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .cart-item-price {
            color: #3B82F6;
            font-weight: 600;
            font-size: 15px;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #D1D5DB;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #F3F4F6;
        }

        .qty-value {
            font-weight: 500;
            min-width: 30px;
            text-align: center;
        }

        .remove-btn {
            color: #EF4444;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
            margin-top: 4px;
        }

        .remove-btn:hover {
            text-decoration: underline;
        }

        .order-summary {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #E5E7EB;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #6B7280;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            padding-top: 12px;
            border-top: 1px solid #E5E7EB;
            margin-top: 12px;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: #3B82F6;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 24px;
        }

        .btn-primary:hover {
            background: #2563EB;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: #F3F4F6;
            color: #374151;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 12px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
        }

        .empty-cart svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            color: #D1D5DB;
        }

        @media (max-width: 968px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="<?= site_url('shop') ?>" class="logo">TechTrack</a>
            <div style="color: #fff; font-size: 18px; font-weight: 600;">Checkout</div>
        </div>
    </header>

    <div class="checkout-container">
        <!-- Left Column: Checkout Form -->
        <div>
            <?php if (empty($cart)): ?>
                <div class="section">
                    <div class="empty-cart">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h3>Your cart is empty</h3>
                        <p>Add some products to get started!</p>
                        <a href="<?= site_url('shop') ?>" class="btn-primary" style="max-width: 300px; margin: 24px auto 0;">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="section">
                    <h2>Shipping Information</h2>
                    <form method="post" action="<?= site_url('checkout/place-order') ?>" id="checkoutForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="customer_email" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="customer_phone" required>
                        </div>

                        <div class="form-group">
                            <label>Complete Address *</label>
                            <textarea name="address" rows="3" required placeholder="Street, Barangay, City, Province"></textarea>
                        </div>

                        <h3>Payment Method</h3>
                        
                        <div class="form-group">
                            <label>Select Payment Method *</label>
                            <select name="payment_method" required>
                                <option value="cash">Cash on Delivery (COD)</option>
                                <option value="gcash">GCash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Order Notes (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="Any special instructions for your order?"></textarea>
                        </div>

                        <input type="hidden" name="items" id="cartItemsInput">
                        <input type="hidden" name="customer_name" id="customerNameInput">

                        <button type="submit" class="btn-primary">Place Order - ₱<?= number_format($total, 2) ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Order Summary -->
        <?php if (!empty($cart)): ?>
        <div>
            <div class="section">
                <h2>Order Summary</h2>
                
                <div class="cart-items">
                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= html_escape($item['image']) ?>" alt="<?= html_escape($item['product_name']) ?>" class="cart-item-image">
                            <?php else: ?>
                                <div class="cart-item-image"></div>
                            <?php endif; ?>
                            <div class="cart-item-details">
                                <div class="cart-item-name"><?= html_escape($item['product_name']) ?></div>
                                <div class="cart-item-price">₱<?= number_format($item['price'], 2) ?></div>
                                <div class="cart-item-quantity">
                                    <button type="button" class="qty-btn qty-decrease" data-product-id="<?= $item['product_id'] ?>">−</button>
                                    <span class="qty-value"><?= $item['quantity'] ?></span>
                                    <button type="button" class="qty-btn qty-increase" data-product-id="<?= $item['product_id'] ?>">+</button>
                                    <button type="button" class="remove-btn" data-product-id="<?= $item['product_id'] ?>">Remove</button>
                                </div>
                                <div style="margin-top: 8px; color: #6B7280; font-size: 14px;">
                                    Subtotal: ₱<?= number_format($item['subtotal'], 2) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Subtotal (<?= $cart_count ?> items)</span>
                        <span id="subtotalAmount">₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shippingAmount">₱<?= number_format($shipping, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (12%)</span>
                        <span id="taxAmount">₱<?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="totalAmount">₱<?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <a href="<?= site_url('shop') ?>" class="btn-secondary">Continue Shopping</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Update cart quantities
        document.querySelectorAll('.qty-increase, .qty-decrease').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const cartItem = this.closest('.cart-item');
                const qtyValue = cartItem.querySelector('.qty-value');
                let currentQty = parseInt(qtyValue.textContent);
                
                if (this.classList.contains('qty-increase')) {
                    currentQty++;
                    updateCartQuantity(productId, currentQty);
                } else if (this.classList.contains('qty-decrease')) {
                    if (currentQty === 1) {
                        // If quantity is 1, ask to remove item
                        if (confirm('Remove this item from cart?')) {
                            removeFromCart(productId);
                        }
                    } else {
                        currentQty--;
                        updateCartQuantity(productId, currentQty);
                    }
                }
            });
        });

        // Remove items
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Remove this item from cart?')) {
                    const productId = this.getAttribute('data-product-id');
                    removeFromCart(productId);
                }
            });
        });

        function updateCartQuantity(productId, quantity) {
            fetch('<?= site_url('shop/update-cart') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function removeFromCart(productId) {
            fetch('<?= site_url('shop/remove-from-cart') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Prepare cart data for order submission
        document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
            const firstName = document.querySelector('input[name="first_name"]').value;
            const lastName = document.querySelector('input[name="last_name"]').value;
            document.getElementById('customerNameInput').value = firstName + ' ' + lastName;
            
            // Get cart items from PHP
            const cartItems = <?= json_encode(array_map(function($item) {
                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ];
            }, $cart)) ?>;
            
            document.getElementById('cartItemsInput').value = JSON.stringify(cartItems);
        });
    </script>
</body>
</html>