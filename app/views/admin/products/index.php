<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Flash Messages -->
<?php if (!empty($flash_success)): ?>
<div class="alert alert-success" style="margin-bottom:16px;padding:12px;background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;border-radius:8px;">
    <?= html_escape($flash_success) ?>
</div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-error" style="margin-bottom:16px;padding:12px;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;border-radius:8px;">
    <?= html_escape($flash_error) ?>
</div>
<?php endif; ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Products</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Manage your product catalog, inventory, and pricing</p>
</div>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;">
    <div class="stat-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Total Products</div>
                <div style="font-size:28px;font-weight:700;color:#111827;"><?= (int)($total_products ?? 0) ?></div>
            </div>
            <div class="stat-icon" style="background:#3B82F6;">
                <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Low Stock Items</div>
                <div style="font-size:28px;font-weight:700;color:#F59E0B;"><?= (int)($low_stock_count ?? 0) ?></div>
            </div>
            <div class="stat-icon" style="background:#FEF3C7;">
                <svg width="24" height="24" fill="none" stroke="#F59E0B" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Out of Stock</div>
                <div style="font-size:28px;font-weight:700;color:#EF4444;"><?= (int)($out_of_stock_count ?? 0) ?></div>
            </div>
            <div class="stat-icon" style="background:#FEE2E2;">
                <svg width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div style="font-size:14px;color:#6B7280;margin-bottom:8px;">Total Value</div>
                <div style="font-size:28px;font-weight:700;color:#10B981;">₱<?= number_format((float)($total_value ?? 0), 2) ?></div>
            </div>
            <div class="stat-icon" style="background:#10B981;">
                <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                    <text x="6" y="18" font-size="20" fill="#fff">₱</text>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div style="display:flex;gap:12px;align-items:center;margin-bottom:24px;">
    <div style="flex:1;position:relative;">
        <svg width="20" height="20" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="searchProducts" placeholder="Search products..." style="width:100%;padding:10px 12px 10px 40px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;">
    </div>
    <select id="filterCategory" style="padding:10px 16px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;color:#374151;min-width:180px;">
        <option value="">All Categories</option>
        <option value="PC Parts">PC Parts</option>
        <option value="Laptop">Laptop</option>
        <option value="Desktop">Desktop</option>
        <option value="Monitors">Monitors</option>
        <option value="Accessories">Accessories</option>
        <option value="Audio">Audio</option>
        <option value="Peripherals">Peripherals</option>
        <option value="Furniture">Furniture</option>
    </select>
    <select id="filterStock" style="padding:10px 16px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;color:#374151;min-width:150px;">
        <option value="">All Stock</option>
        <option value="in_stock">In Stock</option>
        <option value="low_stock">Low Stock</option>
        <option value="out_of_stock">Out of Stock</option>
    </select>
    <button class="btn-icon-text" id="toggleView" title="Toggle Grid/List View">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
    </button>
    <button class="btn-icon-text" id="toggleViewList" title="List View">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <button class="btn primary" id="addProductBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Product
    </button>
</div>

<!-- Products Grid -->
<div id="productsGrid" class="products-grid">
    <?php foreach (($products ?? []) as $p): 
        $stock = (int)($p['stock'] ?? 0);
        $threshold = (int)($p['low_stock_threshold'] ?? 5);
        $status = $stock <= 0 ? 'out_of_stock' : ($stock <= $threshold ? 'low_stock' : 'in_stock');
        $statusLabel = $stock <= 0 ? 'Out of Stock' : ($stock <= $threshold ? 'Low Stock' : 'In Stock');
        $statusClass = $stock <= 0 ? 'status-red' : ($stock <= $threshold ? 'status-amber' : 'status-blue');
        $img = $product_images[$p['id']] ?? null;
        // Fix image URL: only add base_url for local images, not external URLs
        if ($img && !preg_match('/^https?:\/\//', $img)) {
            $img = base_url() . $img;
        }
    ?>
        <div class="product-card" data-product-id="<?= (int)($p['id'] ?? 0) ?>" data-status="<?= $status ?>" data-category="<?= html_escape($p['category'] ?? '') ?>">
            <div class="product-image-container">
                <?php if ($img): ?>
                    <img src="<?= html_escape($img) ?>" alt="<?= html_escape($p['name'] ?? '') ?>" class="product-card-image"/>
                <?php else: ?>
                    <div class="product-card-placeholder">
                        <svg width="48" height="48" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="product-card-content">
                <div class="product-category"><?= html_escape($p['category'] ?? 'Uncategorized') ?></div>
                <h3 class="product-card-title"><?= html_escape($p['name'] ?? '') ?></h3>
                <div class="product-sku">SKU: <?= html_escape($p['sku'] ?? 'N/A') ?></div>
                
                <div class="product-meta">
                    <div class="product-price">
                        <span style="font-size:12px;color:#6B7280;">Price:</span>
                        <span style="font-weight:600;color:#3B82F6;">₱<?= number_format((float)($p['price'] ?? 0), 2) ?></span>
                    </div>
                    <div class="product-stock">
                        <span style="font-size:12px;color:#6B7280;">Stock:</span>
                        <span style="font-weight:600;color:#111827;"><?= $stock ?> units</span>
                    </div>
                </div>
                
                <div class="product-status <?= $statusClass ?>"><?= $statusLabel ?></div>
                
                <div class="product-actions">
                    <button class="action-btn-sm view-btn" data-id="<?= (int)($p['id'] ?? 0) ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View
                    </button>
                    <button class="action-btn-sm edit-btn edit-product" data-id="<?= (int)($p['id'] ?? 0) ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button class="action-btn-sm delete-btn delete-product" data-id="<?= (int)($p['id'] ?? 0) ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span style="color:#EF4444;">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/css/pagination.css">
    <div class="pagination-wrapper" style="margin-top:24px;">
        <div class="pagination-info">
            Showing <?= $pagination['start_item'] ?> to <?= $pagination['end_item'] ?> of <?= $pagination['total_items'] ?> products
        </div>
        <div class="pagination">
            <?php if ($pagination['has_prev']): ?>
                <a href="<?= site_url('admin/products?page=' . $pagination['prev_page']) ?>" class="page-link">« Previous</a>
            <?php else: ?>
                <span class="page-link disabled">« Previous</span>
            <?php endif; ?>
            
            <?php foreach ($pagination['pages'] as $page): ?>
                <?php if ($page === '...'): ?>
                    <span class="page-link disabled">...</span>
                <?php elseif ($page == $pagination['current_page']): ?>
                    <span class="page-link active"><?= $page ?></span>
                <?php else: ?>
                    <a href="<?= site_url('admin/products?page=' . $page) ?>" class="page-link"><?= $page ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if ($pagination['has_next']): ?>
                <a href="<?= site_url('admin/products?page=' . $pagination['next_page']) ?>" class="page-link">Next »</a>
            <?php else: ?>
                <span class="page-link disabled">Next »</span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Product</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="productForm" action="javascript:void(0);" method="post" data-base-url="<?= base_url() ?>admin/products">
            <input type="hidden" id="productId" name="id">

            <div class="form-row">
                <div class="form-group">
                    <label for="productName">Product Name *</label>
                    <input type="text" id="productName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="productSKU">SKU *</label>
                    <input type="text" id="productSKU" name="sku" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="productCategory">Category</label>
                    <input type="text" id="productCategory" name="category" placeholder="e.g., Peripherals">
                </div>
                <div class="form-group">
                    <label for="productBrand">Brand</label>
                    <input type="text" id="productBrand" name="brand">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="productPrice">Price (₱) *</label>
                    <input type="number" id="productPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="productSalePrice">Sale Price (₱)</label>
                    <input type="number" id="productSalePrice" name="sale_price" step="0.01">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="productStock">Stock *</label>
                    <input type="number" id="productStock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="productLowStockThreshold">Low Stock Threshold</label>
                    <input type="number" id="productLowStockThreshold" name="low_stock_threshold" value="5">
                </div>
            </div>

            <div class="form-group">
                <label for="productDescription">Description</label>
                <textarea id="productDescription" name="description" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="productImages">Product Images</label>
                <div style="border:2px dashed #D1D5DB;border-radius:8px;padding:20px;text-align:center;background:#F9FAFB;">
                    <input type="file" id="productImages" name="images[]" accept="image/*" multiple style="display:none;">
                    <label for="productImages" style="cursor:pointer;display:block;">
                        <svg width="48" height="48" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="margin:0 auto 10px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div style="color:#6B7280;font-size:14px;">Click to upload images or drag and drop</div>
                        <div style="color:#9CA3AF;font-size:12px;margin-top:4px;">PNG, JPG, GIF up to 5MB each</div>
                    </label>
                </div>
                <div id="imagePreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:15px;"></div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" id="productFeatured" name="featured" value="1">
                    Mark as Featured Product
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn primary" id="submitBtn">Save Product</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Stat Cards */
.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.product-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    overflow: hidden;
    transition: all 0.3s;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.product-image-container {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #F9FAFB;
    position: relative;
}

.product-card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-card-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
}

.product-card-content {
    padding: 16px;
}

.product-category {
    font-size: 12px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.product-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px 0;
    line-height: 1.4;
}

.product-sku {
    font-size: 12px;
    color: #9CA3AF;
    margin-bottom: 12px;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 12px;
    background: #F9FAFB;
    border-radius: 8px;
}

.product-meta > div {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.product-status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 12px;
}

.status-blue {
    background: #DBEAFE;
    color: #1E40AF;
}

.status-amber {
    background: #FEF3C7;
    color: #92400E;
}

.status-red {
    background: #FEE2E2;
    color: #991B1B;
}

.product-actions {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #E5E7EB;
}

.action-btn-sm {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    background: #fff;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn-sm:hover {
    background: #F9FAFB;
}

.delete-btn:hover {
    background: #FEE2E2;
    border-color: #FCA5A5;
}

.btn-icon-text {
    padding: 10px 12px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    background: #fff;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon-text:hover {
    background: #F9FAFB;
    border-color: #D1D5DB;
}

/* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6B7280;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .modal-close:hover {
        background: #F3F4F6;
    }

    .modal form {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        font-size: 14px;
        color: #374151;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* List View */
    .list-view {
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
    }

    .list-view .product-card {
        display: flex !important;
        flex-direction: row !important;
        max-width: 100% !important;
    }

    .list-view .product-image-container {
        width: 180px !important;
        height: auto !important;
        min-height: 180px !important;
        flex-shrink: 0 !important;
    }

    .list-view .product-card-content {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .list-view .product-actions {
        margin-top: auto;
        justify-content: flex-start;
    }

    .list-view .product-meta {
        display: flex;
        gap: 24px;
        background: transparent;
        padding: 0;
    }

    /* Button Active State */
    .btn-icon-text.active {
        background: #DBEAFE;
        color: #1E40AF;
        border-color: #93C5FD;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
        
        .list-view .product-card {
            flex-direction: column !important;
        }
        
        .list-view .product-image-container {
            width: 100% !important;
            height: 200px !important;
        }
    }
</style>

<script src="<?= base_url() ?>public/assets/js/products.js"></script>

</div>

<!-- View Product Modal -->
<div id="viewProductModal" class="modal">
    <div class="modal-content" style="max-width:900px;">
        <div class="modal-header">
            <h3>Product Details</h3>
            <button class="modal-close" id="viewModalCloseBtn">&times;</button>
        </div>
        <div id="viewLoading" style="padding:24px; color:#6B7280;">Loading product...</div>
        <div id="viewContent" style="display:none; padding: 16px 24px 24px;">
            <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:20px; align-items:start;">
                <div>
                    <div id="viewImages" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px,1fr)); gap:10px;"></div>
                </div>
                <div>
                    <div style="margin-bottom:10px;">
                        <div style="font-size:12px;color:#6B7280;">Product Name</div>
                        <div id="viewName" style="font-size:18px;font-weight:600;color:#111827;">&nbsp;</div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <div style="font-size:12px;color:#6B7280;">SKU</div>
                            <div id="viewSKU" style="font-weight:500;">&nbsp;</div>
                        </div>
                        <div>
                            <div style="font-size:12px;color:#6B7280;">Category</div>
                            <div id="viewCategory" style="font-weight:500;">&nbsp;</div>
                        </div>
                        <div>
                            <div style="font-size:12px;color:#6B7280;">Brand</div>
                            <div id="viewBrand" style="font-weight:500;">&nbsp;</div>
                        </div>
                        <div>
                            <div style="font-size:12px;color:#6B7280;">Stock</div>
                            <div id="viewStock" style="font-weight:500;">&nbsp;</div>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <div style="font-size:12px;color:#6B7280;">Price</div>
                        <div id="viewPrice" style="font-size:18px;color:#3B82F6;font-weight:700;">&nbsp;</div>
                    </div>
                    <div style="margin-top:16px;">
                        <div style="font-size:12px;color:#6B7280;">Description</div>
                        <div id="viewDescription" style="white-space:pre-wrap;color:#111827;">&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</body>

</html>