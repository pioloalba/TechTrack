<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Your PC - TechTrack</title>
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 15px;
            opacity: 0.9;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 16px;
            opacity: 0.9;
            transition: opacity 0.2s;
        }

        .back-link:hover { opacity: 1; }

        /* Main Container */
        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 24px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
        }

        /* Build Section */
        .build-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .component-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .component-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .component-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #F3F4F6;
        }

        .component-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .component-icon {
            width: 40px;
            height: 40px;
            background: #EFF6FF;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .component-name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .component-status {
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-required {
            background: #FEE2E2;
            color: #DC2626;
        }

        .status-optional {
            background: #E0E7FF;
            color: #4F46E5;
        }

        .status-selected {
            background: #D1FAE5;
            color: #059669;
        }

        .component-body {
            display: none;
        }

        .component-body.active {
            display: block;
        }

        .selected-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            background: #F9FAFB;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .selected-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
        }

        .selected-info {
            flex: 1;
        }

        .selected-info h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .selected-info p {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .selected-price {
            font-size: 18px;
            font-weight: 700;
            color: #3B82F6;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-select {
            background: #3B82F6;
            color: white;
            width: 100%;
        }

        .btn-select:hover {
            background: #2563EB;
        }

        .btn-change {
            background: #F3F4F6;
            color: #374151;
        }

        .btn-change:hover {
            background: #E5E7EB;
        }

        .btn-remove {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-remove:hover {
            background: #FECACA;
        }

        /* Product List Modal */
        .product-list {
            display: none;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 12px;
        }

        .product-list.active {
            display: block;
        }

        .product-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-item:hover {
            border-color: #3B82F6;
            background: #F9FAFB;
        }

        .product-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .product-item-info {
            flex: 1;
        }

        .product-item-info h5 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .product-item-info p {
            font-size: 12px;
            color: #6B7280;
        }

        .product-item-price {
            font-size: 16px;
            font-weight: 700;
            color: #3B82F6;
        }

        /* Summary Sidebar */
        .summary-sidebar {
            position: sticky;
            top: 24px;
            height: fit-content;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .summary-card h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #F3F4F6;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #F3F4F6;
            font-size: 14px;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6B7280;
        }

        .summary-value {
            font-weight: 600;
            color: #111827;
        }

        .summary-total {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-total .label {
            font-size: 18px;
            font-weight: 600;
        }

        .summary-total .value {
            font-size: 28px;
            font-weight: 700;
            color: #3B82F6;
        }

        .checkout-btn {
            width: 100%;
            padding: 16px;
            background: #3B82F6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .checkout-btn:hover {
            background: #2563EB;
            transform: translateY(-1px);
        }

        .checkout-btn:disabled {
            background: #E5E7EB;
            color: #9CA3AF;
            cursor: not-allowed;
            transform: none;
        }

        .compatibility-note {
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            padding: 12px;
            margin-top: 16px;
            font-size: 13px;
            color: #92400E;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9CA3AF;
        }

        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }
            .summary-sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-container">
            <a class="back-link" href="<?= site_url('shop') ?>">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Shop
            </a>
            <h1>🖥️ Build Your Dream PC</h1>
            <p>Select components to build your custom PC. Start with the essential parts.</p>
        </div>
    </div>

    <div class="main-container">
        <div class="build-section">
            <!-- CPU -->
            <div class="component-card" data-category="CPU">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">💻</div>
                        <div>
                            <div class="component-name">Processor (CPU)</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="CPU"></div>
                <button class="btn btn-select" onclick="toggleProductList('CPU')">Select Processor</button>
                <div class="product-list" id="products-CPU"></div>
            </div>

            <!-- Motherboard -->
            <div class="component-card" data-category="Motherboard">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">🔌</div>
                        <div>
                            <div class="component-name">Motherboard</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="Motherboard"></div>
                <button class="btn btn-select" onclick="toggleProductList('Motherboard')">Select Motherboard</button>
                <div class="product-list" id="products-Motherboard"></div>
            </div>

            <!-- GPU -->
            <div class="component-card" data-category="GPU">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">🎮</div>
                        <div>
                            <div class="component-name">Graphics Card (GPU)</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="GPU"></div>
                <button class="btn btn-select" onclick="toggleProductList('GPU')">Select Graphics Card</button>
                <div class="product-list" id="products-GPU"></div>
            </div>

            <!-- RAM -->
            <div class="component-card" data-category="RAM">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">🧠</div>
                        <div>
                            <div class="component-name">Memory (RAM)</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="RAM"></div>
                <button class="btn btn-select" onclick="toggleProductList('RAM')">Select Memory</button>
                <div class="product-list" id="products-RAM"></div>
            </div>

            <!-- Storage -->
            <div class="component-card" data-category="Storage">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">💾</div>
                        <div>
                            <div class="component-name">Storage</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="Storage"></div>
                <button class="btn btn-select" onclick="toggleProductList('Storage')">Select Storage</button>
                <div class="product-list" id="products-Storage"></div>
            </div>

            <!-- PSU -->
            <div class="component-card" data-category="PSU">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">⚡</div>
                        <div>
                            <div class="component-name">Power Supply (PSU)</div>
                        </div>
                    </div>
                    <span class="component-status status-required">Required</span>
                </div>
                <div class="selected-component" data-category="PSU"></div>
                <button class="btn btn-select" onclick="toggleProductList('PSU')">Select Power Supply</button>
                <div class="product-list" id="products-PSU"></div>
            </div>

            <!-- Case -->
            <div class="component-card" data-category="Case">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">🖥️</div>
                        <div>
                            <div class="component-name">PC Case</div>
                        </div>
                    </div>
                    <span class="component-status status-optional">Optional</span>
                </div>
                <div class="selected-component" data-category="Case"></div>
                <button class="btn btn-select" onclick="toggleProductList('Case')">Select Case</button>
                <div class="product-list" id="products-Case"></div>
            </div>

            <!-- Cooling -->
            <div class="component-card" data-category="Cooling">
                <div class="component-header">
                    <div class="component-title">
                        <div class="component-icon">❄️</div>
                        <div>
                            <div class="component-name">Cooling System</div>
                        </div>
                    </div>
                    <span class="component-status status-optional">Optional</span>
                </div>
                <div class="selected-component" data-category="Cooling"></div>
                <button class="btn btn-select" onclick="toggleProductList('Cooling')">Select Cooling</button>
                <div class="product-list" id="products-Cooling"></div>
            </div>
        </div>

        <div class="summary-sidebar">
            <div class="summary-card">
                <h2>Build Summary</h2>
                <div id="summary-list"></div>
                <div class="summary-total">
                    <span class="label">Total:</span>
                    <span class="value" id="total-price">₱0.00</span>
                </div>
                <button class="checkout-btn" id="checkout-btn" disabled onclick="checkout()">
                    Add to Cart
                </button>
                <div class="compatibility-note">
                    💡 Note: Please verify component compatibility before purchase
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const build = {};
        const products = <?= json_encode($products_by_category ?? []) ?>;

        // Initialize product lists
        Object.keys(products).forEach(category => {
            const container = document.getElementById(`products-${category}`);
            if (!container) return;

            if (products[category].length === 0) {
                container.innerHTML = '<div class="empty-state">No products available in this category</div>';
                return;
            }

            products[category].forEach(product => {
                const imageUrl = product.image_url || '<?= base_url() ?>public/assets/img/no-image.png';
                const finalImageUrl = imageUrl.match(/^https?:\/\//) ? imageUrl : '<?= base_url() ?>' + imageUrl;
                
                container.innerHTML += `
                    <div class="product-item" onclick="selectComponent('${category}', ${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price}, '${finalImageUrl}')">
                        <img src="${finalImageUrl}" alt="${product.name}">
                        <div class="product-item-info">
                            <h5>${product.name}</h5>
                            <p>${product.description ? product.description.substring(0, 60) + '...' : 'No description'}</p>
                        </div>
                        <div class="product-item-price">₱${parseFloat(product.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                    </div>
                `;
            });
        });

        function toggleProductList(category) {
            const list = document.getElementById(`products-${category}`);
            const allLists = document.querySelectorAll('.product-list');
            
            allLists.forEach(l => {
                if (l !== list) l.classList.remove('active');
            });
            
            list.classList.toggle('active');
        }

        function selectComponent(category, id, name, price, imageUrl) {
            build[category] = { id, name, price, imageUrl };
            updateUI();
            toggleProductList(category);
        }

        function removeComponent(category) {
            delete build[category];
            updateUI();
        }

        function updateUI() {
            // Update each component card
            Object.keys(products).forEach(category => {
                const container = document.querySelector(`[data-category="${category}"] .selected-component`);
                const button = document.querySelector(`[data-category="${category}"] .btn-select`);
                const status = document.querySelector(`[data-category="${category}"] .component-status`);
                
                if (build[category]) {
                    const item = build[category];
                    container.innerHTML = `
                        <div class="selected-item">
                            <img src="${item.imageUrl}" alt="${item.name}">
                            <div class="selected-info">
                                <h4>${item.name}</h4>
                                <div class="selected-price">₱${parseFloat(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-change" onclick="toggleProductList('${category}')">Change</button>
                            <button class="btn btn-remove" onclick="removeComponent('${category}')">Remove</button>
                        </div>
                    `;
                    button.style.display = 'none';
                    if (status.classList.contains('status-required')) {
                        status.className = 'component-status status-selected';
                        status.textContent = 'Selected';
                    }
                } else {
                    container.innerHTML = '';
                    button.style.display = 'block';
                    if (status.classList.contains('status-selected')) {
                        status.className = 'component-status status-required';
                        status.textContent = 'Required';
                    }
                }
            });

            // Update summary
            updateSummary();
        }

        function updateSummary() {
            const summaryList = document.getElementById('summary-list');
            let total = 0;
            let html = '';

            Object.keys(build).forEach(category => {
                const item = build[category];
                total += parseFloat(item.price);
                html += `
                    <div class="summary-item">
                        <span class="summary-label">${category}</span>
                        <span class="summary-value">₱${parseFloat(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                    </div>
                `;
            });

            if (html === '') {
                html = '<div class="empty-state">No components selected</div>';
            }

            summaryList.innerHTML = html;
            document.getElementById('total-price').textContent = `₱${total.toLocaleString('en-PH', {minimumFractionDigits: 2})}`;

            // Enable checkout if required components are selected
            const requiredCategories = ['CPU', 'Motherboard', 'GPU', 'RAM', 'Storage', 'PSU'];
            const allRequiredSelected = requiredCategories.every(cat => build[cat]);
            document.getElementById('checkout-btn').disabled = !allRequiredSelected;
        }

        function checkout() {
            if (Object.keys(build).length === 0) {
                alert('Please select at least one component');
                return;
            }

            // Send build to cart
            const buildData = Object.values(build).map(item => ({
                product_id: item.id,
                quantity: 1
            }));

            // Add all items to cart via AJAX
            let completed = 0;
            let failed = 0;
            buildData.forEach(item => {
                $.ajax({
                    url: '<?= site_url('shop/add-to-cart') ?>',
                    method: 'POST',
                    data: item,
                    success: function(response) {
                        completed++;
                        if (completed + failed === buildData.length) {
                            if (failed === 0) {
                                alert('PC build added to cart successfully!');
                                window.location.href = '<?= site_url('checkout') ?>';
                            } else {
                                alert('Some items could not be added to cart. Please try again.');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error adding to cart:', error, xhr.responseText);
                        failed++;
                        if (completed + failed === buildData.length) {
                            alert('Error adding items to cart. Please try again.');
                        }
                    }
                });
            });
        }

        // Initialize
        updateUI();
    </script>
</body>
</html>
