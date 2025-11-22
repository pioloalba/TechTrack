<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
    <div>
        <h2 style="margin:0;font-size:28px;font-weight:600;color:#111827;">Reports & Analytics</h2>
        <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Comprehensive business insights and analytics</p>
    </div>
    <button class="btn primary" id="exportPDF" style="display:flex;align-items:center;gap:8px;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export as PDF
    </button>
</div>

<!-- Tabs -->
<div class="reports-tabs">
    <button class="report-tab active" data-tab="sales">Sales</button>
    <button class="report-tab" data-tab="inventory">Inventory</button>
    <button class="report-tab" data-tab="profit">Profit Analysis</button>
</div>

<!-- Stats Cards -->
<div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:24px;margin-bottom:32px;">
    <div class="card" style="background:#fff;">
        <div style="color:#6B7280;font-size:14px;margin-bottom:8px;">Total Revenue</div>
        <div style="font-size:32px;font-weight:700;color:#111827;margin-bottom:8px;">
            ₱<?= number_format($total_revenue ?? 0, 0) ?>
        </div>
        <div style="color:#10B981;font-size:14px;font-weight:500;">
            +<?= number_format(abs($revenue_growth ?? 0), 1) ?>% from last month
        </div>
    </div>
    
    <div class="card" style="background:#fff;">
        <div style="color:#6B7280;font-size:14px;margin-bottom:8px;">Total Orders</div>
        <div style="font-size:32px;font-weight:700;color:#111827;margin-bottom:8px;">
            <?= number_format($total_orders ?? 0) ?>
        </div>
        <div style="color:#10B981;font-size:14px;font-weight:500;">
            +8.7% from last month
        </div>
    </div>
    
    <div class="card" style="background:#fff;">
        <div style="color:#6B7280;font-size:14px;margin-bottom:8px;">Average Order Value</div>
        <div style="font-size:32px;font-weight:700;color:#111827;margin-bottom:8px;">
            ₱<?= number_format($average_order_value ?? 0, 2) ?>
        </div>
        <div style="color:#10B981;font-size:14px;font-weight:500;">
            +3.2% from last month
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display:grid;grid-template-columns:1fr;gap:24px;">
    <!-- Weekly Sales Chart -->
    <div class="card" style="background:#fff;">
        <h3 style="margin:0 0 24px 0;font-size:18px;font-weight:600;color:#111827;">Weekly Sales Performance</h3>
        <canvas id="weeklySalesChart" height="80"></canvas>
    </div>
    
    <!-- Category Distribution -->
    <div class="card" style="background:#fff;">
        <h3 style="margin:0 0 24px 0;font-size:18px;font-weight:600;color:#111827;">Sales Distribution by Category</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:center;">
            <div style="max-width:400px;margin:0 auto;">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="category-legend">
                <?php 
                $colors = ['#3B82F6', '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B'];
                $colorIndex = 0;
                foreach (($category_sales ?? []) as $cat): 
                ?>
                    <div class="legend-item">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:16px;height:16px;border-radius:4px;background:<?= $colors[$colorIndex % count($colors)] ?>;"></div>
                            <span style="color:#374151;font-weight:500;"><?= html_escape($cat['name']) ?></span>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:700;color:#111827;">₱<?= number_format($cat['total'], 0) ?></div>
                            <div style="font-size:13px;color:#9CA3AF;"><?= $cat['percentage'] ?>%</div>
                        </div>
                    </div>
                <?php 
                    $colorIndex++;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.reports-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 32px;
    border-bottom: 1px solid #E5E7EB;
}

.report-tab {
    padding: 12px 24px;
    background: none;
    border: none;
    color: #6B7280;
    font-weight: 500;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.report-tab:hover {
    color: #3B82F6;
}

.report-tab.active {
    color: #3B82F6;
    border-bottom-color: #3B82F6;
}

.category-legend {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.legend-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #F9FAFB;
    border-radius: 8px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Sales Chart
    const weeklySalesData = <?= json_encode(array_values($weekly_sales ?? [])) ?>;
    const weeklySalesLabels = <?= json_encode(array_keys($weekly_sales ?? [])) ?>;
    
    new Chart(document.getElementById('weeklySalesChart'), {
        type: 'line',
        data: {
            labels: weeklySalesLabels,
            datasets: [{
                label: 'Sales',
                data: weeklySalesData,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: '#F3F4F6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Category Distribution Chart
    const categoryData = <?= json_encode(array_column($category_sales ?? [], 'total')) ?>;
    const categoryLabels = <?= json_encode(array_column($category_sales ?? [], 'name')) ?>;
    const categoryColors = ['#3B82F6', '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B'];
    
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: categoryColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ₱' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Tab switching
    document.querySelectorAll('.report-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // In a real app, you would load different data here
        });
    });
    
    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        // Open PDF export in new window
        window.open('<?= site_url('admin/reports/export_pdf') ?>', '_blank');
    });
});
</script>

</div>
</div>
</body>
</html>