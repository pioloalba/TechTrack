<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Order #<?= html_escape($order['id'] ?? '') ?></h2>
        <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">
            Order Date: <?= isset($order['created_at']) ? date('F d, Y g:i A', strtotime($order['created_at'])) : 'N/A' ?>
        </p>
    </div>
    <a href="<?= site_url('admin/orders') ?>" style="padding:10px 20px;background:#f3f4f6;color:#374151;border-radius:8px;text-decoration:none;font-weight:500;">
        ← Back to Orders
    </a>
</div>

<!-- Order Details Grid -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px;">
    <!-- Order Information Card -->
    <div class="card" style="background:#fff;padding:24px;">
        <h3 style="margin:0 0 20px;font-size:18px;font-weight:600;color:#111827;border-bottom:2px solid #f3f4f6;padding-bottom:12px;">
            Order Information
        </h3>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Customer Name</div>
                <div style="font-weight:600;color:#111827;"><?= html_escape($order['customer_name'] ?? 'N/A') ?></div>
            </div>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Order Status</div>
                <div>
                    <?php 
                    $statusColors = [
                        'pending' => 'background:#FEF3C7;color:#92400E',
                        'processing' => 'background:#DBEAFE;color:#1E40AF',
                        'shipped' => 'background:#E0E7FF;color:#4338CA',
                        'delivered' => 'background:#D1FAE5;color:#065F46',
                        'cancelled' => 'background:#FEE2E2;color:#991B1B'
                    ];
                    $status = $order['status'] ?? 'pending';
                    $statusStyle = $statusColors[$status] ?? $statusColors['pending'];
                    ?>
                    <span style="<?= $statusStyle ?>;padding:4px 12px;border-radius:12px;font-size:13px;font-weight:600;">
                        <?= ucfirst($status) ?>
                    </span>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <?php if (!empty($order['customer_email'])): ?>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Email</div>
                <div style="color:#111827;"><?= html_escape($order['customer_email']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($order['customer_phone'])): ?>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Phone</div>
                <div style="color:#111827;"><?= html_escape($order['customer_phone']) ?></div>
            </div>
            <?php endif; ?>
            
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Payment Method</div>
                <div style="color:#111827;font-weight:600;">
                    <?php 
                    $paymentMethods = [
                        'cash' => 'Cash on Delivery',
                        'card' => 'Credit/Debit Card',
                        'gcash' => 'GCash',
                        'bank' => 'Bank Transfer',
                        'grab_pay' => 'GrabPay'
                    ];
                    $payment = $order['payment_method'] ?? 'cash';
                    echo html_escape($paymentMethods[$payment] ?? ucfirst($payment));
                    ?>
                </div>
            </div>
            
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Payment Status</div>
                <div>
                    <?php 
                    $paymentStatusColors = [
                        'pending' => 'background:#FEF3C7;color:#92400E',
                        'paid' => 'background:#D1FAE5;color:#065F46',
                        'failed' => 'background:#FEE2E2;color:#991B1B',
                        'pending_verification' => 'background:#DBEAFE;color:#1E40AF'
                    ];
                    $paymentStatus = $order['payment_status'] ?? 'pending';
                    $paymentStyle = $paymentStatusColors[$paymentStatus] ?? $paymentStatusColors['pending'];
                    ?>
                    <span style="<?= $paymentStyle ?>;padding:4px 12px;border-radius:12px;font-size:13px;font-weight:600;">
                        <?= ucfirst(str_replace('_', ' ', $paymentStatus)) ?>
                    </span>
                </div>
            </div>
            
            <?php if (!empty($order['tracking_number'])): ?>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Tracking Number</div>
                <div style="color:#111827;font-family:monospace;"><?= html_escape($order['tracking_number']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($order['courier'])): ?>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Courier</div>
                <div style="color:#111827;"><?= html_escape($order['courier']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($order['bank_reference'])): ?>
            <div>
                <div style="color:#6B7280;font-size:13px;margin-bottom:4px;">Bank Reference Number</div>
                <div style="color:#111827;font-family:monospace;font-weight:600;"><?= html_escape($order['bank_reference']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($order['payment_proof'])): ?>
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #f3f4f6;">
            <h4 style="margin:0 0 12px;font-size:16px;font-weight:600;color:#111827;">Payment Proof</h4>
            <a href="<?= site_url($order['payment_proof']) ?>" target="_blank" style="display:inline-block;padding:10px 20px;background:#4F46E5;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">
                View Proof of Payment
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($shipping)): ?>
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #f3f4f6;">
            <h4 style="margin:0 0 12px;font-size:16px;font-weight:600;color:#111827;">Shipping Address</h4>
            <div style="color:#374151;line-height:1.6;">
                <div><?= html_escape($shipping['line1'] ?? '') ?></div>
                <?php if (!empty($shipping['line2'])): ?>
                <div><?= html_escape($shipping['line2']) ?></div>
                <?php endif; ?>
                <div><?= html_escape(($shipping['city'] ?? '') . ', ' . ($shipping['province'] ?? '')) ?></div>
                <div><?= html_escape(($shipping['postal_code'] ?? '') . ' ' . ($shipping['country'] ?? '')) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Update Status Card -->
    <div class="card" style="background:#fff;padding:24px;height:fit-content;">
        <h3 style="margin:0 0 16px;font-size:18px;font-weight:600;color:#111827;">Update Status</h3>
        <form method="post" action="<?= site_url('admin/orders/update-status/' . ($order['id'] ?? '')) ?>">
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-weight:500;color:#374151;font-size:14px;">Order Status</label>
                <select name="status" style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;color:#111827;background:#fff;">
                    <option value="pending" <?= ($order['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= ($order['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= ($order['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= ($order['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= ($order['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" style="width:100%;padding:10px;background:#4F46E5;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;">
                Update Status
            </button>
        </form>
        
        <!-- Order Tracking QR Code -->
        <?php if (isset($order_qr_code)): ?>
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #E5E7EB;text-align:center;">
            <h4 style="margin:0 0 12px;font-size:14px;font-weight:600;color:#111827;">📦 Order Tracking QR</h4>
            <img src="<?= html_escape($order_qr_code) ?>" alt="Order Tracking QR Code" style="max-width:150px;height:auto;border:2px solid #E5E7EB;border-radius:8px;padding:8px;background:#fff;margin-bottom:8px;">
            <p style="color:#6B7280;font-size:11px;margin:0;">Share this QR code with the customer for order tracking</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Items Card -->
<div class="card" style="background:#fff;padding:24px;">
    <h3 style="margin:0 0 20px;font-size:18px;font-weight:600;color:#111827;border-bottom:2px solid #f3f4f6;padding-bottom:12px;">
        Order Items
    </h3>
    
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#F9FAFB;border-bottom:2px solid #E5E7EB;">
                <th style="padding:12px;text-align:left;font-weight:600;color:#374151;font-size:13px;">Product</th>
                <th style="padding:12px;text-align:center;font-weight:600;color:#374151;font-size:13px;">Quantity</th>
                <th style="padding:12px;text-align:right;font-weight:600;color:#374151;font-size:13px;">Price</th>
                <th style="padding:12px;text-align:right;font-weight:600;color:#374151;font-size:13px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            foreach (($items ?? []) as $it): 
                $subtotal = (float) ($it['subtotal'] ?? (((float) ($it['price'] ?? 0)) * ((int) ($it['quantity'] ?? 0))));
                $total += $subtotal;
            ?>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:16px;color:#111827;"><?= html_escape($it['product_name'] ?? '') ?></td>
                    <td style="padding:16px;text-align:center;color:#6B7280;"><?= (int) ($it['quantity'] ?? 0) ?></td>
                    <td style="padding:16px;text-align:right;color:#6B7280;">₱<?= number_format((float) ($it['price'] ?? 0), 2) ?></td>
                    <td style="padding:16px;text-align:right;color:#111827;font-weight:600;">₱<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background:#F9FAFB;border-top:2px solid #E5E7EB;">
                <td colspan="3" style="padding:16px;text-align:right;font-weight:600;color:#111827;font-size:16px;">Total:</td>
                <td style="padding:16px;text-align:right;font-weight:700;color:#4F46E5;font-size:18px;">₱<?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>
</div>