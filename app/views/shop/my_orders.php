<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - TechTrack</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F9FAFB;
            color: #111827;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            color: #fff;
            padding: 20px 0;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo { 
            font-size: 24px; 
            font-weight: 700; 
            letter-spacing: -0.5px;
            color: #fff;
            text-decoration: none;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 32px;
        }

        /* Orders List */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }

        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #3B82F6;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }

        .order-id {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .order-date {
            font-size: 14px;
            color: #6B7280;
        }

        .order-body {
            padding: 24px;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .order-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .order-info-label {
            font-size: 13px;
            color: #6B7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .order-info-value {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .order-total {
            font-size: 20px;
            font-weight: 700;
            color: #3B82F6;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-processing {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-shipped {
            background: #E0E7FF;
            color: #4338CA;
        }

        .status-delivered {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-cancelled {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* Order Actions */
        .order-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3B82F6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #374151;
            border: 1px solid #E5E7EB;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #111827;
        }

        .empty-text {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 24px;
        }

        /* Login Required */
        .login-required {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
        }

        .login-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-info {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="<?= site_url('shop') ?>" class="logo">TechTrack</a>
            <a href="<?= site_url('shop') ?>" class="back-btn">← Back to Shop</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">🚚 My Orders</h1>
        <p class="page-subtitle">Track and manage your orders</p>

        <?php if (!empty($customer) || !empty($orders)): ?>
            <?php if (!empty($orders)): ?>
                <!-- Filter Tabs -->
                <div style="display: flex; gap: 12px; margin-bottom: 24px; padding: 8px; background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;">
                    <button class="filter-btn active" data-filter="all" style="flex: 1; padding: 12px 20px; border: none; background: #3B82F6; color: #fff; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        All Orders (<?= count($orders) ?>)
                    </button>
                    <button class="filter-btn" data-filter="pending" style="flex: 1; padding: 12px 20px; border: none; background: #F3F4F6; color: #374151; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Pending (<?= count(array_filter($orders, function($o) { return $o['status'] === 'pending'; })) ?>)
                    </button>
                    <button class="filter-btn" data-filter="processing" style="flex: 1; padding: 12px 20px; border: none; background: #F3F4F6; color: #374151; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Processing (<?= count(array_filter($orders, function($o) { return $o['status'] === 'processing'; })) ?>)
                    </button>
                    <button class="filter-btn" data-filter="delivered" style="flex: 1; padding: 12px 20px; border: none; background: #F3F4F6; color: #374151; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        ✅ Delivered (<?= count(array_filter($orders, function($o) { return $o['status'] === 'delivered'; })) ?>)
                    </button>
                </div>

                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" data-status="<?= strtolower($order['status'] ?? 'pending') ?>">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #<?= $order['id'] ?></div>
                                    <div class="order-date">
                                        <?= date('F j, Y g:i A', strtotime($order['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                                <span class="status-badge status-<?= strtolower($order['status'] ?? 'pending') ?>">
                                    <?= ucfirst($order['status'] ?? 'Pending') ?>
                                </span>
                            </div>

                            <div class="order-body">
                                <div class="order-info">
                                    <div class="order-info-item">
                                        <div class="order-info-label">Customer</div>
                                        <div class="order-info-value"><?= html_escape($order['customer_name'] ?? 'Guest') ?></div>
                                    </div>

                                    <div class="order-info-item">
                                        <div class="order-info-label">Items</div>
                                        <div class="order-info-value"><?= (int)($order['total_items'] ?? $order['item_count'] ?? 0) ?> item(s)</div>
                                    </div>

                                    <div class="order-info-item">
                                        <div class="order-info-label">Payment</div>
                                        <div class="order-info-value"><?= html_escape($order['payment_method'] ?? 'N/A') ?></div>
                                    </div>

                                    <div class="order-info-item">
                                        <div class="order-info-label">Total</div>
                                        <div class="order-total">₱<?= number_format($order['total'] ?? 0, 2) ?></div>
                                    </div>
                                </div>

                                <div class="order-actions">
                                    <a href="<?= site_url('track/' . $order['id']) ?>" class="btn btn-primary">
                                        📦 Track Order
                                    </a>
                                    <a href="<?= site_url('shop') ?>" class="btn btn-secondary">
                                        🛒 Shop Again
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h2 class="empty-title">No Orders Yet</h2>
                    <p class="empty-text">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="<?= site_url('shop') ?>" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="login-required">
                <div class="login-icon">🔒</div>
                <h2 class="empty-title">Login Required</h2>
                <p class="empty-text">Please login to view your orders</p>
                <a href="<?= site_url('shop/login') ?>" class="btn btn-primary">Login / Register</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const orderCards = document.querySelectorAll('.order-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.dataset.filter;
                
                // Update active state
                filterButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.style.background = '#F3F4F6';
                    btn.style.color = '#374151';
                });
                button.classList.add('active');
                button.style.background = '#3B82F6';
                button.style.color = '#fff';
                
                // Filter orders
                orderCards.forEach(card => {
                    if (filter === 'all' || card.dataset.status === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
