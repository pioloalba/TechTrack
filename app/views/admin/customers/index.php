<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Customers Management</h2>
        <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">View and manage customer information</p>
    </div>
    <a href="<?= site_url('admin/customers/create') ?>" class="btn-add-customer">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Customer
    </a>
</div>

<!-- Success/Error Messages -->
<?php if(isset($success) && $success): ?>
    <div class="alert alert-success" style="background:#D1FAE5;color:#047857;padding:12px 16px;border-radius:8px;margin-bottom:20px;border-left:4px solid #10B981;">
        <strong>Success:</strong> <?= $success ?>
    </div>
<?php endif; ?>

<?php if(isset($error) && $error): ?>
    <div class="alert alert-danger" style="background:#FEE2E2;color:#DC2626;padding:12px 16px;border-radius:8px;margin-bottom:20px;border-left:4px solid #DC2626;">
        <strong>Error:</strong> <?= $error ?>
    </div>
<?php endif; ?>

<!-- Customers Table Card -->
<div class="card" style="background:#fff;">
    <!-- Search Bar -->
    <div style="display:flex;gap:12px;margin-bottom:20px;">
        <div style="flex:1;position:relative;">
            <svg width="20" height="20" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="searchCustomers" placeholder="Search customers by name or email..." style="width:100%;padding:10px 12px 10px 40px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;">
        </div>
    </div>

    <!-- Customers Table -->
    <div style="overflow-x:auto;">
        <table class="customers-table">
            <thead>
                <tr>
                    <th style="width:80px;">ID</th>
                    <th style="width:220px;">Customer</th>
                    <th style="width:220px;">Email</th>
                    <th style="width:120px;">Total Orders</th>
                    <th style="width:120px;">Total Spent</th>
                    <th style="width:180px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody id="customersTableBody">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p style="margin:0;">No customers found</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr class="customer-row" data-name="<?= html_escape(strtolower($customer['name'] ?? '')) ?>" data-email="<?= html_escape(strtolower($customer['email'] ?? '')) ?>">
                            <td>
                                <span style="font-weight:600;color:#6B7280;">C<?= str_pad($customer['id'], 3, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div class="customer-avatar">
                                        <?= strtoupper(substr($customer['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:500;color:#111827;"><?= html_escape($customer['name'] ?? 'Unknown') ?></div>
                                        <div style="font-size:13px;color:#6B7280;">
                                            Member since <?= date('M Y', strtotime($customer['created_at'] ?? 'now')) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="color:#374151;display:flex;align-items:center;gap:6px;">
                                    <svg width="16" height="16" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <?= html_escape($customer['email'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <svg width="16" height="16" fill="none" stroke="#3B82F6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span style="font-weight:600;color:#111827;"><?= (int)($customer['total_orders'] ?? 0) ?></span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:600;color:#10B981;font-size:15px;">
                                    ₱<?= number_format((float)($customer['total_spent'] ?? 0), 2) ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex;gap:8px;">
                                    <a href="<?= site_url('admin/customers/view/' . $customer['id']) ?>" class="btn-action btn-view" title="View Details">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="<?= site_url('admin/customers/edit/' . $customer['id']) ?>" class="btn-action btn-edit" title="Edit Customer">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete(<?= $customer['id'] ?>, '<?= html_escape($customer['name'] ?? '') ?>')" class="btn-action btn-delete" title="Delete Customer">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.customers-table {
    width: 100%;
    border-collapse: collapse;
}

.customers-table thead {
    background: #F9FAFB;
    border-bottom: 2px solid #E5E7EB;
}

.customers-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.customers-table tbody tr {
    border-bottom: 1px solid #F3F4F6;
    transition: background-color 0.2s;
}

.customers-table tbody tr:hover {
    background: #F9FAFB;
}

.customers-table td {
    padding: 16px;
    vertical-align: middle;
}

.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 16px;
}

.btn-add-customer {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #3B82F6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-add-customer:hover {
    background: #2563EB;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view {
    background: #DBEAFE;
    color: #3B82F6;
}

.btn-view:hover {
    background: #3B82F6;
    color: #fff;
}

.btn-edit {
    background: #FEF3C7;
    color: #F59E0B;
}

.btn-edit:hover {
    background: #F59E0B;
    color: #fff;
}

.btn-delete {
    background: #FEE2E2;
    color: #EF4444;
}

.btn-delete:hover {
    background: #EF4444;
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .customers-table {
        font-size: 13px;
    }
    
    .customers-table th,
    .customers-table td {
        padding: 10px;
    }
    
    .customer-avatar {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
}
</style>

<script>
// Confirm delete function
function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to delete customer "${name}"?\n\nThis action cannot be undone.`)) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= site_url('admin/customers/delete/') ?>' + id;
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCustomers');
    const tableBody = document.getElementById('customersTableBody');
    const rows = tableBody.querySelectorAll('.customer-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();

        rows.forEach(row => {
            const name = row.dataset.name;
            const email = row.dataset.email;

            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

</div>
</div>
</body>
</html>