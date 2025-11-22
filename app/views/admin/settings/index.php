<?php defined('PREVENT_DIRECT_ACCESS') or exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <h2 style="margin:0;font-size:28px;font-weight:600;color:#111827;">Settings</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Manage your store settings and preferences</p>
</div>

<form method="post" action="<?= site_url('admin/settings/update') ?>" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:24px;">
    <!-- Branding -->
    <div class="settings-card">
        <h3 class="settings-section-title">Branding</h3>
        <div class="settings-grid">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Current Logo</label>
                <?php 
                    $logoCandidates = ['logo.svg','logo.png','logo.jpg','logo.jpeg'];
                    $logoUrl = null;
                    foreach ($logoCandidates as $cand) {
                        $p = ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $cand;
                        if (is_file($p)) { $logoUrl = base_url() . 'public/assets/img/' . $cand; break; }
                    }
                ?>
                <?php if ($logoUrl): ?>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="<?= $logoUrl ?>" alt="Logo" style="height:48px;max-width:240px;object-fit:contain;background:#fff;border:1px solid #E5E7EB;border-radius:8px;padding:6px;">
                        <span class="form-hint">Displayed in the sidebar and header</span>
                    </div>
                <?php else: ?>
                    <p class="form-hint">No logo uploaded yet. You can upload PNG, JPG, or SVG.</p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="form-label">Upload Logo</label>
                <input type="file" name="logo_file" accept="image/png,image/jpeg,image/svg+xml" class="form-input" style="padding:8px;">
            </div>
            <div class="form-group">
                <label class="form-label">Upload Favicon</label>
                <input type="file" name="favicon_file" accept="image/x-icon,image/vnd.microsoft.icon,image/png" class="form-input" style="padding:8px;">
                <p class="form-hint">Recommended: 32×32 ICO or PNG</p>
            </div>
        </div>
    </div>
    
    <!-- Store Information -->
    <div class="settings-card">
        <h3 class="settings-section-title">Store Information</h3>
        
        <div class="settings-grid">
            <div class="form-group">
                <label class="form-label">Store Name</label>
                <input type="text" name="store_name" value="<?= html_escape($settings['store_name'] ?? '') ?>" class="form-input" placeholder="Enter store name">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= html_escape($settings['email'] ?? '') ?>" class="form-input" placeholder="store@email.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="<?= html_escape($settings['phone'] ?? '') ?>" class="form-input" placeholder="+1 (555) 123-4567">
            </div>
            
            <div class="form-group">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" value="<?= html_escape($settings['currency'] ?? 'USD') ?>" class="form-input" placeholder="USD">
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="<?= html_escape($settings['address'] ?? '') ?>" class="form-input" placeholder="123 Street, City, State ZIP">
            </div>
        </div>
    </div>

    <!-- Store Location -->
    <div class="settings-card">
        <h3 class="settings-section-title">Store Location</h3>
        <div class="settings-grid">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Store Address (public)</label>
                <input type="text" name="store_address" value="<?= html_escape($settings['store_address'] ?? ($settings['address'] ?? '')) ?>" class="form-input" placeholder="Enter the store address your customers will see">
                <p class="form-hint">Shown on the shop and used for pickup/delivery estimates</p>
            </div>
            <div class="form-group">
                <label class="form-label">Latitude</label>
                <input type="text" name="store_lat" id="store_lat" value="<?= isset($settings['store_lat']) ? html_escape($settings['store_lat']) : '' ?>" class="form-input" placeholder="e.g. 14.5995">
            </div>
            <div class="form-group">
                <label class="form-label">Longitude</label>
                <input type="text" name="store_lng" id="store_lng" value="<?= isset($settings['store_lng']) ? html_escape($settings['store_lng']) : '' ?>" class="form-input" placeholder="e.g. 120.9842">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <div style="font-size:14px;color:#6B7280;">Preview</div>
                    <button type="button" class="btn-secondary" onclick="detectStoreLocation()">Use my current location</button>
                </div>
                <iframe id="store_map" style="width:100%;height:280px;border:1px solid #E5E7EB;border-radius:8px;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <!-- Inventory Settings -->
    <div class="settings-card">
        <h3 class="settings-section-title">Inventory Settings</h3>
        
        <div class="settings-grid">
            <div class="form-group">
                <label class="form-label">Low Stock Threshold</label>
                <input type="number" name="low_stock_threshold" value="<?= html_escape($settings['low_stock_threshold'] ?? 5) ?>" class="form-input" placeholder="5">
                <p class="form-hint">Alert when stock falls below this number</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Critical Stock Threshold</label>
                <input type="number" name="critical_stock_threshold" value="<?= html_escape($settings['critical_stock_threshold'] ?? 2) ?>" class="form-input" placeholder="2">
                <p class="form-hint">Critical alert threshold</p>
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <div class="toggle-wrapper">
                    <label class="toggle-label">
                        <span>
                            <div style="font-weight:500;color:#111827;">Track Serial Numbers</div>
                            <div style="font-size:13px;color:#6B7280;margin-top:2px;">Enable serial number tracking for products</div>
                        </span>
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="track_serial" <?= !empty($settings['track_serial']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax & Pricing -->
    <div class="settings-card">
        <h3 class="settings-section-title">Tax & Pricing</h3>
        
        <div class="settings-grid">
            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="<?= html_escape($settings['tax_rate'] ?? 8.00) ?>" class="form-input" placeholder="8.00">
            </div>
            
            <div class="form-group">
                <label class="form-label">Default Profit Margin (%)</label>
                <input type="number" step="0.01" name="profit_margin" value="<?= html_escape($settings['profit_margin'] ?? 30.00) ?>" class="form-input" placeholder="30.00">
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <div class="toggle-wrapper">
                    <label class="toggle-label">
                        <span>
                            <div style="font-weight:500;color:#111827;">Include Tax in Price</div>
                            <div style="font-size:13px;color:#6B7280;margin-top:2px;">Display prices with tax included</div>
                        </span>
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="include_tax" <?= !empty($settings['include_tax']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Settings -->
    <div class="settings-card">
        <h3 class="settings-section-title">Receipt Settings</h3>
        
        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="form-group">
                <label class="form-label">Receipt Header</label>
                <input type="text" name="receipt_header" value="<?= html_escape($settings['receipt_header'] ?? '') ?>" class="form-input" placeholder="Thank you for shopping with us!">
            </div>
            
            <div class="form-group">
                <label class="form-label">Receipt Footer</label>
                <input type="text" name="receipt_footer" value="<?= html_escape($settings['receipt_footer'] ?? '') ?>" class="form-input" placeholder="Visit us again at techtrack.com">
            </div>
            
            <div class="toggle-wrapper">
                <label class="toggle-label">
                    <span>
                        <div style="font-weight:500;color:#111827;">Auto-print Receipt</div>
                        <div style="font-size:13px;color:#6B7280;margin-top:2px;">Automatically print receipt after checkout</div>
                    </span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="auto_print_receipt" <?= !empty($settings['auto_print_receipt']) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div style="display:flex;justify-content:flex-end;gap:12px;">
        <button type="button" onclick="window.location.reload()" class="btn-secondary">
            Cancel
        </button>
        <button type="submit" class="btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:8px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Save Changes
        </button>
    </div>
</form>

<style>
.settings-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 24px;
}

.settings-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 20px 0;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-input {
    padding: 10px 14px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 14px;
    color: #111827;
    background: #F9FAFB;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3B82F6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-hint {
    font-size: 13px;
    color: #9CA3AF;
    margin-top: 6px;
    margin-bottom: 0;
}

.toggle-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: #F9FAFB;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
}

.toggle-label {
    flex: 1;
    cursor: pointer;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 24px;
    cursor: pointer;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #D1D5DB;
    transition: 0.3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #3B82F6;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    background: #3B82F6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #2563EB;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    padding: 12px 24px;
    background: #fff;
    color: #374151;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #F9FAFB;
    border-color: #9CA3AF;
}

/* Responsive */
@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group[style*="grid-column: span 2"] {
        grid-column: span 1 !important;
    }
    
    .toggle-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>

<script>
// Show success/error messages
<?php if (!empty($success)): ?>
    showNotification('success', '<?= html_escape($success) ?>');
<?php endif; ?>

<?php if (!empty($error)): ?>
    showNotification('error', '<?= html_escape($error) ?>');
<?php endif; ?>

function showNotification(type, message) {
    const bgColor = type === 'success' ? '#ECFDF5' : '#FEE2E2';
    const textColor = type === 'success' ? '#065F46' : '#991B1B';
    const borderColor = type === 'success' ? '#A7F3D0' : '#FECACA';
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        background: ${bgColor};
        color: ${textColor};
        border: 1px solid ${borderColor};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Store map helpers
function updateStoreMap() {
    const lat = document.getElementById('store_lat')?.value.trim();
    const lng = document.getElementById('store_lng')?.value.trim();
    const map = document.getElementById('store_map');
    if (!map) return;
    let url;
    if (lat && lng && !isNaN(Number(lat)) && !isNaN(Number(lng))) {
        const latNum = Number(lat);
        const lngNum = Number(lng);
        const bbox = [lngNum - 0.02, latNum - 0.02, lngNum + 0.02, latNum + 0.02].join(',');
        url = `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${latNum},${lngNum}`;
    } else {
        url = 'https://www.openstreetmap.org/export/embed.html?bbox=120.96,14.56,121.02,14.64&layer=mapnik&marker=14.5995,120.9842';
    }
    map.src = url;
}

function detectStoreLocation() {
    if (!('geolocation' in navigator)) {
        showNotification('error', 'Geolocation not supported in this browser');
        return;
    }
    navigator.geolocation.getCurrentPosition((pos) => {
        const { latitude, longitude } = pos.coords;
        const latEl = document.getElementById('store_lat');
        const lngEl = document.getElementById('store_lng');
        if (latEl && lngEl) {
            latEl.value = latitude.toFixed(6);
            lngEl.value = longitude.toFixed(6);
            updateStoreMap();
        }
    }, (err) => {
        showNotification('error', 'Failed to get your location: ' + (err?.message || 'Unknown error'));
    });
}

document.addEventListener('DOMContentLoaded', function(){
    const latEl = document.getElementById('store_lat');
    const lngEl = document.getElementById('store_lng');
    if (latEl) latEl.addEventListener('input', updateStoreMap);
    if (lngEl) lngEl.addEventListener('input', updateStoreMap);
    updateStoreMap();
});
</script>

</div>
</div>
</body>
</html>