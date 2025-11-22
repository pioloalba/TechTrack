<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Point of Sale (POS)</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Quick sale interface for in-store transactions</p>
</div>

<!-- POS Layout -->
<div style="display:grid;grid-template-columns:1fr 400px;gap:24px;height:calc(100vh - 200px);">
    <!-- Products Section -->
    <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- Search Bar -->
        <div style="display:flex;gap:12px;">
            <div style="flex:1;position:relative;">
                <svg width="20" height="20" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchPOS" placeholder="Search products by name or ID..." style="width:100%;padding:12px 12px 12px 40px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;">
            </div>
            <button class="btn-scan">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Scan
            </button>
        </div>

        <!-- Products Grid -->
        <div class="pos-products-grid">
            <?php foreach (($products ?? []) as $p): 
                $stock = (int)($p['stock'] ?? 0);
                $img = $product_images[$p['id']] ?? null;
            ?>
                <div class="pos-product-card" data-product-id="<?= (int)($p['id'] ?? 0) ?>" data-name="<?= html_escape($p['name'] ?? '') ?>" data-price="<?= (float)($p['price'] ?? 0) ?>" data-stock="<?= $stock ?>">
                    <div class="pos-product-image">
                        <?php if ($img): ?>
                            <img src="<?= html_escape($img) ?>" alt="<?= html_escape($p['name'] ?? '') ?>"/>
                        <?php else: ?>
                            <div class="pos-product-placeholder">
                                <svg width="32" height="32" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="pos-product-info">
                        <h4 class="pos-product-name"><?= html_escape($p['name'] ?? '') ?></h4>
                        <div class="pos-product-price">₱<?= number_format((float)($p['price'] ?? 0), 2) ?></div>
                        <div class="pos-product-stock">Stock: <?= $stock ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart Summary Section -->
    <div class="pos-cart-container">
        <div class="card" style="height:100%;display:flex;flex-direction:column;">
            <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:600;">Cart Summary</h3>
            
            <!-- Cart Items -->
            <div id="cartItems" class="cart-items">
                <div class="cart-empty">
                    <svg width="48" height="48" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24" style="margin-bottom:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p style="color:#9CA3AF;margin:0;">Cart is empty</p>
                </div>
            </div>

            <!-- Cart Footer -->
            <div class="cart-footer">
                <div class="cart-totals">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <span style="color:#6B7280;">Subtotal</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                        <span style="color:#6B7280;">Tax (8%)</span>
                        <span id="tax">₱0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:2px solid #E5E7EB;">
                        <span style="font-weight:600;font-size:16px;">Total</span>
                        <span id="total" style="font-weight:700;font-size:20px;color:#3B82F6;">₱0.00</span>
                    </div>
                </div>
                <button id="checkoutBtn" class="btn primary" style="width:100%;padding:14px;font-size:16px;margin-top:16px;" disabled>
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* POS Products Grid */
.pos-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
    overflow-y: auto;
    padding: 2px;
}

.pos-product-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
}

.pos-product-card:hover {
    border-color: #3B82F6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.pos-product-image {
    width: 100%;
    height: 160px;
    overflow: hidden;
    background: #F9FAFB;
}

.pos-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pos-product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
}

.pos-product-info {
    padding: 12px;
}

.pos-product-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 6px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.pos-product-price {
    font-size: 18px;
    font-weight: 700;
    color: #3B82F6;
    margin-bottom: 4px;
}

.pos-product-stock {
    font-size: 12px;
    color: #6B7280;
}

/* Scan Button */
.btn-scan {
    padding: 12px 20px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    background: #fff;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: all 0.2s;
}

.btn-scan:hover {
    background: #F9FAFB;
    border-color: #D1D5DB;
}

/* Cart Container */
.pos-cart-container {
    height: 100%;
}

.cart-items {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 16px;
    min-height: 300px;
}

.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 40px 20px;
}

.cart-item {
    padding: 12px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    margin-bottom: 8px;
    background: #F9FAFB;
}

.cart-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 8px;
}

.cart-item-name {
    font-weight: 600;
    color: #111827;
    font-size: 14px;
    flex: 1;
}

.cart-item-remove {
    background: none;
    border: none;
    color: #EF4444;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.cart-item-remove:hover {
    background: #FEE2E2;
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cart-quantity-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.qty-btn {
    width: 28px;
    height: 28px;
    border: 1px solid #E5E7EB;
    border-radius: 6px;
    background: #fff;
    color: #374151;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.qty-btn:hover {
    background: #F9FAFB;
    border-color: #3B82F6;
    color: #3B82F6;
}

.qty-input {
    width: 50px;
    text-align: center;
    border: 1px solid #E5E7EB;
    border-radius: 6px;
    padding: 4px;
    font-weight: 600;
}

.cart-item-price {
    font-weight: 700;
    color: #3B82F6;
    font-size: 16px;
}

.cart-footer {
    border-top: 2px solid #E5E7EB;
    padding-top: 16px;
}

.cart-totals {
    background: #F9FAFB;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 1200px) {
    .pos-products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
</style>

<script>
// POS Cart Management
const cart = [];
const TAX_RATE = 0.08;

document.addEventListener('DOMContentLoaded', function() {
    // Product click to add to cart
    document.querySelectorAll('.pos-product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);
            const productStock = parseInt(this.dataset.stock);
            
            if (productStock <= 0) {
                showNotification('error', 'Product is out of stock');
                return;
            }
            
            addToCart(productId, productName, productPrice, productStock);
        });
    });
    
    // Search functionality
    document.getElementById('searchPOS').addEventListener('keyup', function(e) {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.pos-product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            card.style.display = name.includes(query) ? '' : 'none';
        });
    });
    
    // Checkout button
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        if (cart.length === 0) return;
        
        if (confirm('Complete this sale?')) {
            // Here you would normally send cart data to server
            showNotification('success', 'Sale completed successfully!');
            clearCart();
        }
    });
});

function addToCart(id, name, price, maxStock) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        if (existingItem.quantity >= maxStock) {
            showNotification('error', 'Cannot add more than available stock');
            return;
        }
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1, maxStock });
    }
    
    updateCart();
}

function removeFromCart(id) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
        cart.splice(index, 1);
    }
    updateCart();
}

function updateQuantity(id, quantity) {
    const item = cart.find(item => item.id === id);
    if (!item) return;
    
    if (quantity <= 0) {
        removeFromCart(id);
        return;
    }
    
    if (quantity > item.maxStock) {
        showNotification('error', 'Cannot exceed available stock');
        return;
    }
    
    item.quantity = quantity;
    updateCart();
}

function updateCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="cart-empty">
                <svg width="48" height="48" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24" style="margin-bottom:12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p style="color:#9CA3AF;margin:0;">Cart is empty</p>
            </div>
        `;
        document.getElementById('checkoutBtn').disabled = true;
    } else {
        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-header">
                    <div class="cart-item-name">${item.name}</div>
                    <button class="cart-item-remove" onclick="removeFromCart(${item.id})">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="cart-item-controls">
                    <div class="cart-quantity-controls">
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">−</button>
                        <input type="number" class="qty-input" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="updateQuantity(${item.id}, parseInt(this.value))">
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    </div>
                    <div class="cart-item-price">₱${(item.price * item.quantity).toFixed(2)}</div>
                </div>
            </div>
        `).join('');
        document.getElementById('checkoutBtn').disabled = false;
    }
    
    // Update totals
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * TAX_RATE;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `₱${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `₱${total.toFixed(2)}`;
}

function clearCart() {
    cart.length = 0;
    updateCart();
}

function showNotification(type, message) {
    const bgColor = type === 'success' ? '#ECFDF5' : '#FEE2E2';
    const textColor = type === 'success' ? '#065F46' : '#991B1B';
    const borderColor = type === 'success' ? '#A7F3D0' : '#FECACA';
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        background: ${bgColor};
        color: ${textColor};
        border: 1px solid ${borderColor};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
        font-weight: 500;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

</div>
</div>
</body>
</html>