<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<style>
    .form-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .form-header {
        margin-bottom: 24px;
    }

    .form-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .form-header p {
        font-size: 14px;
        color: #6B7280;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 24px;
    }

    .form-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 14px;
        color: #111827;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-start;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        border: none;
    }

    .btn-primary {
        background: #3B82F6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563EB;
    }

    .btn-secondary {
        background: #F3F4F6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #E5E7EB;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h1><?= isset($product) ? 'Edit Product' : 'Add New Product' ?></h1>
            <p><?= isset($product) ? 'Update product information below' : 'Fill in the details to create a new product' ?></p>
        </div>

        <div class="form-card">
            <form method="post" action="<?= isset($product) ? site_url('admin/products/update/' . ($product['id'] ?? '')) : site_url('admin/products/store') ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" value="<?= html_escape($product['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" id="sku" name="sku" value="<?= html_escape($product['sku'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">Select Category</option>
                            <option value="Laptop" <?= (isset($product['category']) && $product['category'] === 'Laptop') ? 'selected' : '' ?>>Laptop</option>
                            <option value="Desktop" <?= (isset($product['category']) && $product['category'] === 'Desktop') ? 'selected' : '' ?>>Desktop</option>
                            <option value="Monitors" <?= (isset($product['category']) && $product['category'] === 'Monitors') ? 'selected' : '' ?>>Monitors</option>
                            <option value="Accessories" <?= (isset($product['category']) && $product['category'] === 'Accessories') ? 'selected' : '' ?>>Accessories</option>
                            <option value="Audio" <?= (isset($product['category']) && $product['category'] === 'Audio') ? 'selected' : '' ?>>Audio</option>
                            <option value="Peripherals" <?= (isset($product['category']) && $product['category'] === 'Peripherals') ? 'selected' : '' ?>>Peripherals</option>
                            <option value="Furniture" <?= (isset($product['category']) && $product['category'] === 'Furniture') ? 'selected' : '' ?>>Furniture</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input type="text" id="brand" name="brand" value="<?= html_escape($product['brand'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" step="0.01" id="price" name="price" value="<?= html_escape($product['price'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sale_price">Sale Price</label>
                        <input type="number" step="0.01" id="sale_price" name="sale_price" value="<?= html_escape($product['sale_price'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="discount">Discount (%)</label>
                        <input type="number" step="0.01" id="discount" name="discount" value="<?= html_escape($product['discount'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" value="<?= html_escape($product['stock'] ?? 0) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="low_stock_threshold">Low Stock Threshold</label>
                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" value="<?= html_escape($product['low_stock_threshold'] ?? 5) ?>">
                    </div>

                    <div class="form-group">
                        <label for="reorder_point">Reorder Point</label>
                        <input type="number" id="reorder_point" name="reorder_point" value="<?= html_escape($product['reorder_point'] ?? 10) ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5"><?= html_escape($product['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($product) ? 'Update Product' : 'Create Product' ?>
                    </button>
                    <a href="<?= site_url('admin/products') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
</body>
</html>