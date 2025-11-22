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
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
    <div>
        <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Inventory Management</h2>
        <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Manage your product inventory and stock levels</p>
    </div>
    <a href="<?= site_url('admin/products') ?>" class="btn primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Product
    </a>
</div>

<!-- Search Bar -->
<div class="card" style="margin-bottom:24px;padding:12px 16px;">
    <input type="text" id="searchInventory" placeholder="Search by name, ID, or category..." style="width:100%;padding:10px 40px 10px 40px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
    <svg width="20" height="20" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="position:absolute;left:28px;top:26px;pointer-events:none;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
</div>

<!-- Inventory Table -->
<div class="card">
    <div class="table-responsive">
        <table class="inventory-table" id="inventoryTable">
            <thead>
                <tr>
                    <th style="width:80px;">ID</th>
                    <th style="min-width:280px;">Product Name</th>
                    <th style="width:100px;text-align:center;">Stock</th>
                    <th style="width:120px;">Price</th>
                    <th style="width:140px;">Category</th>
                    <th style="min-width:180px;">Supplier</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:100px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($products ?? []) as $p): 
                    $stock = (int)($p['stock'] ?? 0);
                    $threshold = (int)($p['low_stock_threshold'] ?? 5);
                    $status = $stock <= 0 ? 'out_of_stock' : ($stock <= $threshold ? 'low_stock' : 'in_stock');
                    $statusLabel = $stock <= 0 ? 'Out of Stock' : ($stock <= $threshold ? 'Low Stock' : 'In Stock');
                    $statusClass = $stock <= 0 ? 'badge-red' : ($stock <= $threshold ? 'badge-amber' : 'badge-green');
                ?>
                    <tr data-product-id="<?= (int)($p['id'] ?? 0) ?>" data-status="<?= $status ?>">
                        <td style="font-weight:600;color:#6B7280;">P<?= str_pad((int)($p['id'] ?? 0), 3, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php $img = $product_images[$p['id']] ?? null; ?>
                                <?php if ($img): ?>
                                    <img src="<?= html_escape($img) ?>" alt="product" class="product-image"/>
                                <?php else: ?>
                                    <div class="product-image-placeholder">
                                        <svg width="24" height="24" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <span class="product-name" style="font-weight:500;color:#111827;"><?= html_escape($p['name'] ?? '') ?></span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($stock <= $threshold && $stock > 0): ?>
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <svg width="18" height="18" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span style="font-weight:600;color:#111827;"><?= $stock ?></span>
                                </div>
                            <?php else: ?>
                                <span style="font-weight:600;color:#111827;"><?= $stock ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:500;color:#111827;">₱<?= number_format((float)($p['price'] ?? 0), 2) ?></td>
                        <td style="color:#6B7280;"><?= html_escape($p['category'] ?? 'Uncategorized') ?></td>
                        <td style="color:#6B7280;"><?= html_escape($p['brand'] ?? 'N/A') ?></td>
                        <td>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <a href="<?= site_url('admin/products/edit/' . ($p['id'] ?? '')) ?>" class="action-btn edit-btn" title="Edit">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button class="action-btn delete-btn" data-id="<?= (int)($p['id'] ?? 0) ?>" title="Delete">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Inventory Table Styles */
.inventory-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.inventory-table thead {
    background: #F9FAFB;
    border-bottom: 2px solid #E5E7EB;
}

.inventory-table th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.inventory-table tbody tr {
    border-bottom: 1px solid #E5E7EB;
    transition: background 0.2s;
}

.inventory-table tbody tr:hover {
    background: #F9FAFB;
}

.inventory-table td {
    padding: 16px;
    vertical-align: middle;
}

/* Product Image */
.product-image {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
}

.product-image-placeholder {
    width: 48px;
    height: 48px;
    background: #F3F4F6;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.badge-green {
    background: #DCFCE7;
    color: #166534;
}

.badge-amber {
    background: #FEF3C7;
    color: #92400E;
}

.badge-red {
    background: #FEE2E2;
    color: #991B1B;
}

/* Action Buttons */
.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: 1px solid #E5E7EB;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.edit-btn {
    color: #3B82F6;
}

.edit-btn:hover {
    background: #EFF6FF;
    border-color: #3B82F6;
}

.delete-btn {
    color: #EF4444;
}

.delete-btn:hover {
    background: #FEE2E2;
    border-color: #EF4444;
}

/* Table Responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Search positioning */
.card {
    position: relative;
}

/* Responsive */
@media (max-width: 1400px) {
    .inventory-table {
        font-size: 13px;
    }
    
    .inventory-table th,
    .inventory-table td {
        padding: 12px;
    }
}
</style>

<script>
// Simple search filter
document.getElementById('searchInventory')?.addEventListener('keyup', function(e) {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll('#inventoryTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
});

// Delete product handler
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.id;
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            window.location.href = `<?= site_url('admin/products/delete/') ?>${productId}`;
        }
    });
});
</script>

</div>
</div>
</body>
</html>