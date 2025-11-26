<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <h2 style="margin:0;font-size:28px;font-weight:600;color:#111827;">Alerts & Notifications</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Monitor inventory alerts and configure notifications</p>
</div>

<!-- Alert Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:24px;margin-bottom:32px;">
    <!-- Critical Alerts -->
    <div class="alert-summary-card" style="background:linear-gradient(135deg, #FEE2E2 0%, #FEF2F2 100%);border:1px solid #FECACA;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="alert-icon" style="background:#EF4444;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px;color:#991B1B;font-weight:500;margin-bottom:4px;">Critical Alerts</div>
                <div style="font-size:28px;font-weight:700;color:#DC2626;"><?= $critical_count ?> Items</div>
            </div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="alert-summary-card" style="background:linear-gradient(135deg, #FEF3C7 0%, #FEF9E7 100%);border:1px solid #FDE68A;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="alert-icon" style="background:#F59E0B;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px;color:#92400E;font-weight:500;margin-bottom:4px;">Low Stock</div>
                <div style="font-size:28px;font-weight:700;color:#D97706;"><?= $low_stock_count ?> Items</div>
            </div>
        </div>
    </div>

    <!-- Total Alerts -->
    <div class="alert-summary-card" style="background:linear-gradient(135deg, #DBEAFE 0%, #EFF6FF 100%);border:1px solid #BFDBFE;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div class="alert-icon" style="background:#3B82F6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px;color:#1E40AF;font-weight:500;margin-bottom:4px;">Total Alerts</div>
                <div style="font-size:28px;font-weight:700;color:#2563EB;"><?= $total_alerts ?> Items</div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alerts Table -->
<div class="card" style="background:#fff;">
    <h3 style="margin:0 0 20px 0;font-size:20px;font-weight:600;color:#111827;">Low Stock Alerts</h3>
    
    <?php if (empty($low_stock_items)): ?>
        <div style="text-align:center;padding:60px 20px;color:#9CA3AF;">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="margin:0;font-size:18px;font-weight:500;color:#6B7280;">All Good!</p>
            <p style="margin:8px 0 0;color:#9CA3AF;">No low stock alerts at the moment</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <?php foreach ($low_stock_items as $product): 
                $stock = (int)$product['stock'];
                $threshold = $stock <= 5 ? 5 : 10;
                $isCritical = $stock <= 5;
                $priority = $isCritical ? 'Critical' : ($stock <= 8 ? 'High' : 'Medium');
                $priorityColors = [
                    'Critical' => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'border' => '#EF4444'],
                    'High' => ['bg' => '#FED7AA', 'text' => '#9A3412', 'border' => '#F97316'],
                    'Medium' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'border' => '#F59E0B']
                ];
                $colors = $priorityColors[$priority];
            ?>
                <div class="alert-item" style="border-left:4px solid <?= $colors['border'] ?>;">
                    <div style="display:flex;align-items:center;gap:16px;flex:1;">
                        <div class="alert-warning-icon" style="color:<?= $colors['border'] ?>;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600;color:#111827;font-size:16px;margin-bottom:4px;">
                                <?= html_escape($product['name'] ?? 'Unknown Product') ?>
                            </div>
                            <div style="font-size:14px;color:#6B7280;">
                                Current stock: <span style="font-weight:600;color:#374151;"><?= $stock ?></span> | 
                                Threshold: <span style="font-weight:600;color:#374151;"><?= $threshold ?></span>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span class="priority-badge" style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
                            <?= $priority ?>
                        </span>
                        <button class="dismiss-alert-btn" data-product-id="<?= $product['id'] ?>" title="Dismiss alert">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.alert-summary-card {
    padding: 24px;
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.alert-summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.alert-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}

.alert-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: #F9FAFB;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    transition: all 0.2s;
}

.alert-item:hover {
    background: #F3F4F6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.alert-warning-icon {
    flex-shrink: 0;
}

.priority-badge {
    display: inline-block;
    white-space: nowrap;
}

/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns:repeat(3, 1fr)"] {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .alert-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert-summary-card {
        padding: 16px;
    }
    
    .alert-icon {
        width: 48px;
        height: 48px;
    }
}

.dismiss-alert-btn {
    background: #FEE2E2;
    border: 1px solid #FECACA;
    color: #DC2626;
    padding: 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dismiss-alert-btn:hover {
    background: #FEF2F2;
    transform: scale(1.05);
}

.dismiss-alert-btn:active {
    transform: scale(0.95);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle dismiss alert button clicks
    document.querySelectorAll('.dismiss-alert-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const alertItem = this.closest('.alert-item');
            
            if (confirm('Are you sure you want to dismiss this alert?\n\nThis will remove the notification until stock drops below threshold again.')) {
                dismissAlert(productId, alertItem);
            }
        });
    });
});

function dismissAlert(productId, alertItem) {
    // Add loading state
    alertItem.style.opacity = '0.5';
    alertItem.style.pointerEvents = 'none';
    
    // Send AJAX request to dismiss alert
    fetch('<?= base_url() ?>admin/alerts/dismiss/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fade out and remove the alert item
            alertItem.style.transition = 'opacity 0.3s, transform 0.3s';
            alertItem.style.opacity = '0';
            alertItem.style.transform = 'translateX(20px)';
            
            setTimeout(function() {
                alertItem.remove();
                
                // Check if there are no more alerts
                const remainingAlerts = document.querySelectorAll('.alert-item');
                if (remainingAlerts.length === 0) {
                    location.reload(); // Reload to show "All Good" message
                }
            }, 300);
            
            // Show success message (optional)
            showNotification('Alert dismissed successfully', 'success');
        } else {
            alertItem.style.opacity = '1';
            alertItem.style.pointerEvents = 'auto';
            showNotification(data.message || 'Failed to dismiss alert', 'error');
        }
    })
    .catch(error => {
        alertItem.style.opacity = '1';
        alertItem.style.pointerEvents = 'auto';
        showNotification('Error dismissing alert', 'error');
        console.error('Error:', error);
    });
}

function showNotification(message, type) {
    // Simple notification (you can replace with your existing notification system)
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 16px 24px;
        background: ${type === 'success' ? '#10B981' : '#EF4444'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

</div>
</div>
</body>
</html>