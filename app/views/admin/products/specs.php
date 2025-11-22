<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>
<div class="card" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
    <strong>Specs - <?= html_escape($product['name'] ?? '') ?></strong>
    <a class="btn" href="<?= site_url('admin/products') ?>">Back to Products</a>
</div>
<div class="card" style="margin-bottom:16px;">
    <form method="post" action="<?= site_url('admin/products/add-spec/' . ($product['id'] ?? '')) ?>" style="display:flex;gap:8px;align-items:flex-end;">
        <div style="flex:1;">
            <label>Name</label>
            <input name="spec_name" style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:8px">
        </div>
        <div style="flex:1;">
            <label>Value</label>
            <input name="spec_value" style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:8px">
        </div>
        <button class="btn primary" type="submit">Add</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (($specs ?? []) as $sp): ?>
            <tr>
                <td><?= html_escape($sp['spec_name'] ?? '') ?></td>
                <td><?= html_escape($sp['spec_value'] ?? '') ?></td>
                <td>
                    <a class="btn" style="color:#991B1B" href="<?= site_url('admin/products/delete-spec/' . ($sp['id'] ?? '')) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</div>
</body>
</html>
