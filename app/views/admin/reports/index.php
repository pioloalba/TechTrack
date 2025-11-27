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

<!-- Tab Content -->
<div id="salesTab" class="tab-content active">
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
</div>

<!-- Inventory Tab -->
<div id="inventoryTab" class="tab-content" style="display:none;">
    <div class="card" style="background:#fff;">
        <h3 style="margin:0 0 24px 0;font-size:18px;font-weight:600;color:#111827;">📦 Low Stock Inventory</h3>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#F9FAFB;border-bottom:2px solid #E5E7EB;">
                        <th style="padding:12px;text-align:left;font-weight:600;color:#374151;">Product Name</th>
                        <th style="padding:12px;text-align:left;font-weight:600;color:#374151;">Category</th>
                        <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Current Stock</th>
                        <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Threshold</th>
                        <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Price</th>
                        <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Stock Value</th>
                        <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($low_stock) && count($low_stock) > 0): ?>
                        <?php foreach ($low_stock as $item): ?>
                            <tr style="border-bottom:1px solid #E5E7EB;">
                                <td style="padding:12px;color:#111827;font-weight:500;"><?= html_escape($item['name']) ?></td>
                                <td style="padding:12px;color:#6B7280;"><?= html_escape($item['category'] ?? 'N/A') ?></td>
                                <td style="padding:12px;text-align:center;">
                                    <span style="font-weight:700;color:<?= $item['stock'] == 0 ? '#DC2626' : ($item['stock'] <= 5 ? '#F59E0B' : '#111827') ?>;">
                                        <?= $item['stock'] ?>
                                    </span>
                                </td>
                                <td style="padding:12px;text-align:center;color:#6B7280;"><?= $item['low_stock_threshold'] ?></td>
                                <td style="padding:12px;text-align:right;color:#111827;">₱<?= number_format($item['price'], 2) ?></td>
                                <td style="padding:12px;text-align:right;font-weight:600;color:#111827;">₱<?= number_format($item['stock_value'], 2) ?></td>
                                <td style="padding:12px;text-align:center;">
                                    <?php if ($item['stock'] == 0): ?>
                                        <span style="background:#FEE2E2;color:#DC2626;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Out of Stock</span>
                                    <?php elseif ($item['stock'] <= 5): ?>
                                        <span style="background:#FEF3C7;color:#F59E0B;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Critical</span>
                                    <?php else: ?>
                                        <span style="background:#DBEAFE;color:#3B82F6;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Low Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding:40px;text-align:center;color:#6B7280;">
                                ✅ All products are well stocked!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Profit Analysis Tab -->
<div id="profitTab" class="tab-content" style="display:none;">
    <div class="card" style="background:#fff;">
        <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:600;color:#111827;">💰 Profit Analysis (Last 6 Months)</h3>
        <div style="background:#FEF3C7;border-left:4px solid #F59E0B;padding:12px 16px;margin-bottom:20px;border-radius:4px;">
            <p style="margin:0;color:#92400E;font-size:13px;">
                <strong>Note:</strong> Profit estimates use 30% margin calculation (Revenue × 0.30) since product cost prices are not tracked in the database.
            </p>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#F9FAFB;border-bottom:2px solid #E5E7EB;">
                        <th style="padding:12px;text-align:left;font-weight:600;color:#374151;">Month</th>
                        <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Revenue</th>
                        <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Cost</th>
                        <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Profit</th>
                        <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Margin %</th>
                        <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($profit_data) && count($profit_data) > 0): ?>
                        <?php foreach ($profit_data as $profit): ?>
                            <?php 
                                $margin = $profit['revenue'] > 0 ? ($profit['profit'] / $profit['revenue']) * 100 : 0;
                            ?>
                            <tr style="border-bottom:1px solid #E5E7EB;">
                                <td style="padding:12px;color:#111827;font-weight:500;">
                                    <?= date('F Y', strtotime($profit['month'] . '-01')) ?>
                                </td>
                                <td style="padding:12px;text-align:right;color:#111827;font-weight:600;">
                                    ₱<?= number_format($profit['revenue'], 2) ?>
                                </td>
                                <td style="padding:12px;text-align:right;color:#DC2626;">
                                    ₱<?= number_format($profit['cost'], 2) ?>
                                </td>
                                <td style="padding:12px;text-align:right;font-weight:700;color:<?= $profit['profit'] > 0 ? '#10B981' : '#DC2626' ?>;">
                                    ₱<?= number_format($profit['profit'], 2) ?>
                                </td>
                                <td style="padding:12px;text-align:center;">
                                    <span style="background:<?= $margin > 20 ? '#D1FAE5' : ($margin > 10 ? '#FEF3C7' : '#FEE2E2') ?>;color:<?= $margin > 20 ? '#059669' : ($margin > 10 ? '#F59E0B' : '#DC2626') ?>;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">
                                        <?= number_format($margin, 1) ?>%
                                    </span>
                                </td>
                                <td style="padding:12px;text-align:center;color:#6B7280;font-weight:500;">
                                    <?= number_format($profit['order_count']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="padding:40px;text-align:center;color:#6B7280;">
                                📊 No profit data available yet
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

.tab-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
            // Remove active class from all tabs
            document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected tab content
            const tabName = this.getAttribute('data-tab');
            if (tabName === 'sales') {
                document.getElementById('salesTab').style.display = 'block';
            } else if (tabName === 'inventory') {
                document.getElementById('inventoryTab').style.display = 'block';
            } else if (tabName === 'profit') {
                document.getElementById('profitTab').style.display = 'block';
            }
        });
    });
    
    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        generatePDF();
    });
    
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Get current data
        const totalRevenue = '<?= number_format($total_revenue ?? 0, 2) ?>';
        const totalOrders = '<?= number_format($total_orders ?? 0) ?>';
        const avgOrderValue = '<?= number_format($average_order_value ?? 0, 2) ?>';
        
        // Professional Header with Blue Background
        doc.setFillColor(41, 98, 255);
        doc.rect(0, 0, 210, 35, 'F');
        
        // Title Section
        let yPos = 18;
        doc.setFontSize(24);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(255, 255, 255);
        doc.text('TechTrack Admin Report', 105, yPos, { align: 'center' });
        
        // Subtitle
        yPos += 9;
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text('Generated on <?= date('F d, Y, g:i A') ?>', 105, yPos, { align: 'center' });
        
        // Reset to black text
        doc.setTextColor(0, 0, 0);
        
        // Key Performance Metrics Section with Cards
        yPos = 50;
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(41, 98, 255);
        doc.text('Key Performance Metrics', 20, yPos);
        
        yPos += 10;
        
        // Metrics Cards
        const cardWidth = 56;
        const cardHeight = 28;
        const cardGap = 5;
        const startX = 20;
        
        // Card 1: Total Revenue
        doc.setFillColor(248, 250, 252);
        doc.roundedRect(startX, yPos, cardWidth, cardHeight, 2, 2, 'F');
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.5);
        doc.roundedRect(startX, yPos, cardWidth, cardHeight, 2, 2, 'S');
        
        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('TOTAL REVENUE', startX + 3, yPos + 6);
        
        doc.setFontSize(14);
        doc.setTextColor(15, 23, 42);
        doc.setFont('helvetica', 'bold');
        doc.text('PHP ' + totalRevenue, startX + 3, yPos + 16);
        
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('All time earnings', startX + 3, yPos + 23);
        
        // Card 2: Total Orders
        const card2X = startX + cardWidth + cardGap;
        doc.setFillColor(248, 250, 252);
        doc.roundedRect(card2X, yPos, cardWidth, cardHeight, 2, 2, 'F');
        doc.setDrawColor(226, 232, 240);
        doc.roundedRect(card2X, yPos, cardWidth, cardHeight, 2, 2, 'S');
        
        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('TOTAL ORDERS', card2X + 3, yPos + 6);
        
        doc.setFontSize(14);
        doc.setTextColor(15, 23, 42);
        doc.setFont('helvetica', 'bold');
        doc.text(totalOrders, card2X + 3, yPos + 16);
        
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('Total transactions', card2X + 3, yPos + 23);
        
        // Card 3: Average Order Value
        const card3X = card2X + cardWidth + cardGap;
        doc.setFillColor(248, 250, 252);
        doc.roundedRect(card3X, yPos, cardWidth, cardHeight, 2, 2, 'F');
        doc.setDrawColor(226, 232, 240);
        doc.roundedRect(card3X, yPos, cardWidth, cardHeight, 2, 2, 'S');
        
        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('AVG ORDER VALUE', card3X + 3, yPos + 6);
        
        doc.setFontSize(14);
        doc.setTextColor(15, 23, 42);
        doc.setFont('helvetica', 'bold');
        doc.text('PHP ' + avgOrderValue, card3X + 3, yPos + 16);
        
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.setFont('helvetica', 'normal');
        doc.text('Per transaction', card3X + 3, yPos + 23);
        
        // Weekly Sales Performance Section
        yPos += 40;
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(41, 98, 255);
        doc.text('Weekly Sales Performance', 20, yPos);
        
        yPos += 8;
        
        // Table with professional styling
        const tableStartY = yPos;
        const rowHeight = 8;
        
        // Table header
        doc.setFillColor(41, 98, 255);
        doc.rect(20, yPos, 170, 10, 'F');
        
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(255, 255, 255);
        doc.text('DAY', 30, yPos + 7);
        doc.text('SALES AMOUNT (PHP)', 180, yPos + 7, { align: 'right' });
        
        yPos += 10;
        
        // Table rows with alternating colors
        const weeklyData = <?= json_encode($weekly_sales ?? []) ?>;
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        
        doc.setFontSize(10);
        
        days.forEach((day, index) => {
            const amount = weeklyData[day] || 0;
            
            // Alternating row colors
            if (index % 2 === 0) {
                doc.setFillColor(248, 250, 252);
                doc.rect(20, yPos, 170, rowHeight, 'F');
            }
            
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(51, 65, 85);
            doc.text(day, 30, yPos + 5.5);
            
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(71, 85, 105);
            doc.text(Number(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}), 180, yPos + 5.5, { align: 'right' });
            
            yPos += rowHeight;
        });
        
        // Table border
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.5);
        doc.rect(20, tableStartY, 170, yPos - tableStartY, 'S');
        
        // Sales Distribution by Category Section
        yPos += 12;
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(41, 98, 255);
        doc.text('Sales Distribution by Category', 20, yPos);
        
        yPos += 8;
        
        // Table with professional styling
        const catTableStartY = yPos;
        
        // Table header
        doc.setFillColor(41, 98, 255);
        doc.rect(20, yPos, 170, 10, 'F');
        
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(255, 255, 255);
        doc.text('CATEGORY', 30, yPos + 7);
        doc.text('AMOUNT (PHP)', 130, yPos + 7, { align: 'right' });
        doc.text('PERCENTAGE', 180, yPos + 7, { align: 'right' });
        
        yPos += 10;
        
        // Table rows with alternating colors
        const categoryData = <?= json_encode($category_sales ?? []) ?>;
        
        doc.setFontSize(10);
        
        categoryData.forEach((cat, index) => {
            // Alternating row colors
            if (index % 2 === 0) {
                doc.setFillColor(248, 250, 252);
                doc.rect(20, yPos, 170, rowHeight, 'F');
            }
            
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(51, 65, 85);
            doc.text(cat.name, 30, yPos + 5.5);
            
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(71, 85, 105);
            doc.text(Number(cat.total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}), 130, yPos + 5.5, { align: 'right' });
            
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(41, 98, 255);
            doc.text(cat.percentage + '%', 180, yPos + 5.5, { align: 'right' });
            
            yPos += rowHeight;
        });
        
        // Table border
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.5);
        doc.rect(20, catTableStartY, 170, yPos - catTableStartY, 'S');
        
        // Professional Footer with QR Code
        yPos += 15;
        
        // Add QR Code to link back to admin dashboard
        const qrCodeData = '<?= admin_dashboard_qr_code(100) ?>';
        doc.addImage(qrCodeData, 'PNG', 20, yPos, 25, 25);
        
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(51, 65, 85);
        doc.text('Scan to Access Dashboard', 50, yPos + 8);
        
        doc.setFontSize(7);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(100, 116, 139);
        doc.text('Scan this QR code to quickly access', 50, yPos + 14);
        doc.text('the TechTrack Admin Dashboard', 50, yPos + 18);
        
        // Footer bar
        doc.setFillColor(248, 250, 252);
        doc.rect(0, 282, 210, 15, 'F');
        
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(100, 116, 139);
        doc.text('© <?= date('Y') ?> TechTrack. All rights reserved.', 105, 289, { align: 'center' });
        doc.text('This is a confidential business report generated by TechTrack Admin System', 105, 293, { align: 'center' });
        
        // Save PDF with simple filename
        doc.save('TechTrack_Report.pdf');
    }
});
</script>

</div>
</div>
</body>
</html>