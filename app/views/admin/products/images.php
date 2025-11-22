<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>
<div class="card" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
    <strong>Images - <?= html_escape($product['name'] ?? '') ?></strong>
    <a class="btn" href="<?= site_url('admin/products') ?>">Back to Products</a>
</div>
<?php if (!empty($flash_success)): ?>
<div class="card" style="margin-bottom:16px;background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;">
    <?= nl2br(html_escape(is_array($flash_success)? implode("\n", $flash_success) : $flash_success)) ?>
    </div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
<div class="card" style="margin-bottom:16px;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;">
    <?= nl2br(html_escape(is_array($flash_error)? implode("\n", $flash_error) : $flash_error)) ?>
    </div>
<?php endif; ?>
<div class="card" style="margin-bottom:16px;">
    <form method="post" action="<?= site_url('admin/products/add-image/' . ($product['id'] ?? '')) ?>" style="display:flex;gap:8px;align-items:flex-end;">
        <div style="flex:1;">
            <label>Image URL</label>
            <input name="image_url" placeholder="/public/images/file.jpg" style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:8px">
        </div>
        <label style="display:flex;align-items:center;gap:6px;">
            <input type="checkbox" name="is_main" value="1"> Main
        </label>
        <button class="btn primary" type="submit">Add</button>
    </form>
</div>
<div class="card" style="margin-bottom:16px;">
    <form method="post" enctype="multipart/form-data" action="<?= site_url('admin/products/upload-image/' . ($product['id'] ?? '')) ?>" style="display:flex;gap:8px;align-items:flex-end;">
        <div style="flex:1;">
            <label>Upload Image</label>
            <input type="file" name="image_file" accept="image/*" style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:8px;background:#fff">
        </div>
        <label style="display:flex;align-items:center;gap:6px;">
            <input type="checkbox" name="is_main" value="1"> Main
        </label>
        <button class="btn primary" type="submit">Upload</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Preview</th>
                <th>URL</th>
                <th>Main</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (($images ?? []) as $img): ?>
            <tr>
                <td><?php if (!empty($img['image_url'])): ?><img src="<?= html_escape($img['image_url']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px;"/><?php endif; ?></td>
                <td><?= html_escape($img['image_url'] ?? '') ?></td>
                <td><?= !empty($img['is_main']) ? 'Yes' : 'No' ?></td>
                <td>
                    <a class="btn" style="color:#991B1B" href="<?= site_url('admin/products/delete-image/' . ($img['id'] ?? '')) ?>">Delete</a>
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
