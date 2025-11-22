<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>
<div class="dashboard-greeting">
    <h1 style="font-size:28px;font-weight:600;margin:0;">Dashboard</h1>
    <p style="color:#6B7280;margin-top:4px;">Welcome back! Here's what's happening today.</p>
</div>

<!-- Summary Cards -->
<div class="grid cols-4" style="margin-top:24px;">
    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Total Sales</div>
                <div class="stat-value">₱<?= number_format($total_sales ?? 0, 0) ?></div>
                <div class="stat-trend positive">+12.5%</div>
            </div>
            <div class="stat-icon" style="background:#3B82F6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><text x="6" y="18" font-size="20">₱</text></svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value"><?= (int)($low_stock_count ?? 0) ?></div>
                <div class="stat-sublabel">Need attention</div>
            </div>
            <div class="stat-icon" style="background:#F97316;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Total Products</div>
                <div class="stat-value"><?= (int)($total_products ?? 0) ?></div>
                <div class="stat-sublabel">+3 this week</div>
            </div>
            <div class="stat-icon" style="background:#A855F7;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Orders Today</div>
                <div class="stat-value"><?= (int)($ordersToday ?? 0) ?></div>
                <div class="stat-trend positive">+8.2%</div>
            </div>
            <div class="stat-icon" style="background:#10B981;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-top:24px;">
    <!-- Sales Overview Bar Chart -->
    <div class="card">
        <h3 style="font-size:18px;font-weight:600;margin:0 0 16px 0;">Sales Overview</h3>
        <canvas id="salesOverviewChart" style="max-height:320px;"></canvas>
    </div>

    <!-- Sales by Category Pie Chart -->
    <div class="card">
        <h3 style="font-size:18px;font-weight:600;margin:0 0 16px 0;">Sales by Category</h3>
        <canvas id="salesByCategoryChart" style="max-height:320px;"></canvas>
    </div>
</div>

<!-- Recent Orders and Low Stock Alerts -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">
    <!-- Recent Orders -->
    <div class="card">
        <h3 style="font-size:18px;font-weight:600;margin:0 0 16px 0;">Recent Orders</h3>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <?php if (!empty($recent_orders)): ?>
                <?php foreach ($recent_orders as $order): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;border-radius:8px;border:1px solid #e5e7eb;">
                        <div>
                            <div style="font-weight:600;color:#111827;">ORD<?= str_pad((int)($order['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?></div>
                            <div style="font-size:14px;color:#6B7280;margin-top:2px;"><?= html_escape($order['customer_name'] ?? 'Guest') ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:600;color:#111827;">₱<?= number_format((float)($order['total'] ?? 0), 2) ?></div>
                            <div style="font-size:12px;margin-top:2px;">
                                <?php 
                                $status = strtolower($order['status'] ?? 'pending');
                                $statusColors = [
                                    'completed' => 'color:#065F46;',
                                    'processing' => 'color:#1E40AF;',
                                    'shipped' => 'color:#7C3AED;',
                                    'pending' => 'color:#92400E;',
                                ];
                                $statusColor = $statusColors[$status] ?? 'color:#6B7280;';
                                ?>
                                <span style="<?= $statusColor ?>"><?= ucfirst($status) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center;color:#6B7280;padding:24px;">No recent orders</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="card">
        <h3 style="font-size:18px;font-weight:600;margin:0 0 16px 0;">Low Stock Alerts</h3>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <?php if (!empty($low_stock_alerts)): ?>
                <?php foreach ($low_stock_alerts as $product): ?>
                    <?php 
                    $stock = (int)($product['stock'] ?? 0);
                    $threshold = (int)($product['low_stock_threshold'] ?? 5);
                    ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;border-radius:8px;border:1px solid #FED7AA;background:#FFFBEB;">
                        <div style="flex:1;">
                            <div style="font-weight:600;color:#111827;"><?= html_escape($product['name'] ?? 'Unknown') ?></div>
                            <div style="font-size:14px;color:#6B7280;margin-top:2px;">Threshold: <?= $threshold ?></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <svg width="20" height="20" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span style="font-weight:600;color:#F97316;font-size:16px;"><?= $stock ?> left</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center;color:#6B7280;padding:24px;">All products are well stocked</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Switch to Customer View Button -->
<div style="position:fixed;bottom:24px;right:24px;">
    <a href="<?= site_url('shop') ?>" class="btn primary" style="box-shadow:0 4px 12px rgba(59,130,246,0.4);font-weight:600;">
        Switch to Customer View
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Sales Overview Bar Chart
const salesCtx = document.getElementById('salesOverviewChart');
if (salesCtx) {
    const weeklySales = <?= json_encode(array_values($weekly_sales ?? [])) ?>;
    const weeklyLabels = <?= json_encode(array_keys($weekly_sales ?? [])) ?>;
    
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Sales (₱)',
                data: weeklySales,
                backgroundColor: '#3B82F6',
                borderRadius: 8,
            }, {
                label: 'Orders',
                data: [1200, 1100, 1400, 1300, 1800, 2100, 1600],
                backgroundColor: '#A855F7',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Sales by Category Pie Chart
const categoryCtx = document.getElementById('salesByCategoryChart');
if (categoryCtx) {
    const categories = <?= json_encode(array_keys($category_sales ?? [])) ?>;
    const categoryValues = <?= json_encode(array_values($category_sales ?? [])) ?>;
    
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: categories.length ? categories : ['Laptops', 'Smartphones', 'Accessories', 'Audio', 'Others'],
            datasets: [{
                data: categoryValues.length ? categoryValues : [35, 25, 20, 12, 8],
                backgroundColor: ['#3B82F6', '#A855F7', '#14B8A6', '#F59E0B', '#EF4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'right' }
            }
        }
    });
}
</script>

</div>
</div>
</body>
</html>