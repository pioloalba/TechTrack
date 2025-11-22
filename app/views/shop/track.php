<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - TechTrack</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 48px 32px;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .success-icon svg {
            width: 48px;
            height: 48px;
            color: #fff;
        }

        h1 {
            font-size: 32px;
            color: #111827;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .subtitle {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 32px;
        }

        .order-details {
            background: #F9FAFB;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .order-row label {
            color: #6B7280;
            font-weight: 500;
        }

        .order-row span {
            color: #111827;
            font-weight: 600;
        }

        .order-id {
            font-size: 24px;
            color: #3B82F6;
            font-weight: 700;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
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
            color: #3730A3;
        }

        .status-delivered {
            background: #D1FAE5;
            color: #065F46;
        }

        .items-section {
            margin: 24px 0;
            text-align: left;
        }

        .items-section h3 {
            font-size: 18px;
            margin-bottom: 16px;
            color: #374151;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #111827;
        }

        .item-quantity {
            color: #6B7280;
            font-size: 14px;
        }

        .item-price {
            font-weight: 600;
            color: #3B82F6;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #374151;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 8px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        .info-text {
            color: #6B7280;
            font-size: 14px;
            margin-top: 24px;
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            .success-container {
                padding: 32px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .btn-primary, .btn-secondary {
                display: block;
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <?php if (!empty($order)): ?>
            <div class="success-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1>Order Placed Successfully!</h1>
            <p class="subtitle">Thank you for your order. We'll send you a confirmation email shortly.</p>

            <div class="order-details">
                <div class="order-row">
                    <label>Order Number</label>
                    <span class="order-id">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="order-row">
                    <label>Status</label>
                    <span class="status-badge status-<?= html_escape($order['status']) ?>">
                        <?= html_escape(ucfirst($order['status'])) ?>
                    </span>
                </div>
                <div class="order-row">
                    <label>Customer Name</label>
                    <span><?= html_escape($order['customer_name']) ?></span>
                </div>
                <?php if (!empty($order['customer_email'])): ?>
                <div class="order-row">
                    <label>Email</label>
                    <span><?= html_escape($order['customer_email']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($order['customer_phone'])): ?>
                <div class="order-row">
                    <label>Phone</label>
                    <span><?= html_escape($order['customer_phone']) ?></span>
                </div>
                <?php endif; ?>
                <div class="order-row">
                    <label>Payment Method</label>
                    <span><?= html_escape(strtoupper($order['payment_method'])) ?></span>
                </div>
                <div class="order-row">
                    <label>Order Total</label>
                    <span style="font-size: 20px; color: #10B981;">₱<?= number_format($order['total'], 2) ?></span>
                </div>
            </div>

            <?php if (!empty($items)): ?>
            <div class="items-section">
                <h3>Order Items</h3>
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="item-info">
                            <div class="item-name"><?= html_escape($item['product_name']) ?></div>
                            <div class="item-quantity">Qty: <?= html_escape($item['quantity']) ?> × ₱<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div class="item-price">₱<?= number_format($item['subtotal'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div style="margin-top: 32px;">
                <a href="<?= site_url('shop') ?>" class="btn-primary">Continue Shopping</a>
                <a href="<?= site_url('shop') ?>" class="btn-secondary">View My Orders</a>
            </div>

            <p class="info-text">
                Your order is being processed. You will receive an email confirmation shortly.<br>
                For any questions, please contact our support team.
            </p>

        <?php else: ?>
            <div class="success-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1>Order Not Found</h1>
            <p class="subtitle">The order you're looking for doesn't exist or has been removed.</p>

            <div style="margin-top: 32px;">
                <a href="<?= site_url('shop') ?>" class="btn-primary">Back to Shop</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>