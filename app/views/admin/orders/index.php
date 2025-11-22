<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Orders Management</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">View and manage customer orders</p>
</div>

<!-- Orders Table Card -->
<div class="card" style="background:#fff;">
    <!-- Search and Filter Bar -->
    <div style="display:flex;gap:12px;margin-bottom:20px;">
        <div style="flex:1;position:relative;">
            <svg width="20" height="20" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="searchOrders" placeholder="Search orders by ID, customer name..." style="width:100%;padding:10px 12px 10px 40px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;">
        </div>
        <select id="filterStatus" style="padding:10px 16px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;background:#fff;cursor:pointer;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Orders Table -->
    <div style="overflow-x:auto;">
        <table class="orders-table">
            <thead>
                <tr>
                    <th style="width:100px;">Order ID</th>
                    <th style="width:200px;">Customer</th>
                    <th style="width:180px;">Date & Time</th>
                    <th style="width:140px;">Total</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:100px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p style="margin:0;">No orders found</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        $status = strtolower($order['status'] ?? 'pending');
                        $statusColors = [
                            'pending' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'dot' => '#F59E0B'],
                            'processing' => ['bg' => '#DBEAFE', 'text' => '#1E40AF', 'dot' => '#3B82F6'],
                            'completed' => ['bg' => '#D1FAE5', 'text' => '#065F46', 'dot' => '#10B981'],
                            'cancelled' => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'dot' => '#EF4444']
                        ];
                        $statusColor = $statusColors[$status] ?? $statusColors['pending'];
                        $orderDate = new DateTime($order['created_at'] ?? 'now');
                    ?>
                        <tr class="order-row" data-order-id="<?= html_escape($order['id']) ?>" data-customer="<?= html_escape(strtolower($order['customer_name'] ?? '')) ?>" data-status="<?= html_escape($status) ?>">
                            <td>
                                <span style="font-weight:600;color:#3B82F6;">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight:500;color:#111827;"><?= html_escape($order['customer_name'] ?? 'Unknown') ?></div>
                                    <div style="font-size:13px;color:#6B7280;"><?= html_escape($order['customer_email'] ?? '') ?></div>
                                </div>
                            </td>
                            <td>
                                <div style="color:#374151;"><?= $orderDate->format('M d, Y') ?></div>
                                <div style="font-size:13px;color:#6B7280;"><?= $orderDate->format('h:i A') ?></div>
                            </td>
                            <td>
                                <span style="font-weight:600;color:#111827;font-size:15px;">₱<?= number_format((float)($order['total'] ?? 0), 2) ?></span>
                            </td>
                            <td>
                                <span class="status-badge" style="background:<?= $statusColor['bg'] ?>;color:<?= $statusColor['text'] ?>;padding:6px 12px;border-radius:16px;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
                                    <span style="width:6px;height:6px;border-radius:50%;background:<?= $statusColor['dot'] ?>;"></span>
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <a href="<?= site_url('admin/orders/view/' . $order['id']) ?>" class="btn-view" title="View Details">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table thead {
    background: #F9FAFB;
    border-bottom: 2px solid #E5E7EB;
}

.orders-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.orders-table tbody tr {
    border-bottom: 1px solid #F3F4F6;
    transition: background-color 0.2s;
}

.orders-table tbody tr:hover {
    background: #F9FAFB;
}

.orders-table td {
    padding: 16px;
    vertical-align: middle;
}

.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #3B82F6;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view:hover {
    background: #2563EB;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .orders-table {
        font-size: 13px;
    }
    
    .orders-table th,
    .orders-table td {
        padding: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchOrders');
    const statusFilter = document.getElementById('filterStatus');
    const tableBody = document.getElementById('ordersTableBody');
    const rows = tableBody.querySelectorAll('.order-row');

    function filterOrders() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        rows.forEach(row => {
            const orderId = row.dataset.orderId;
            const customerName = row.dataset.customer;
            const orderStatus = row.dataset.status;

            const matchesSearch = orderId.includes(searchTerm) || customerName.includes(searchTerm);
            const matchesStatus = !statusValue || orderStatus === statusValue;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('keyup', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
});
</script>

</div>
</div>
</body>
</html>