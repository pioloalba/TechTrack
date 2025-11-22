<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
        <a href="<?= site_url('admin/customers') ?>" style="color:#6B7280;text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-size:14px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Customers
        </a>
    </div>
    <h2 style="margin:0;font-size:24px;font-weight:600;color:#111827;">Edit Customer</h2>
    <p style="margin:4px 0 0;color:#6B7280;font-size:14px;">Update customer information</p>
</div>

<!-- Error Messages -->
<?php if(isset($error) && $error): ?>
    <div class="alert alert-danger" style="background:#FEE2E2;color:#DC2626;padding:12px 16px;border-radius:8px;margin-bottom:20px;border-left:4px solid #DC2626;">
        <strong>Error:</strong> <?= $error ?>
    </div>
<?php endif; ?>

<!-- Edit Customer Form Card -->
<div class="card" style="background:#fff;max-width:800px;">
    <form method="POST" action="<?= site_url('admin/customers/update/' . $customer['id']) ?>" id="editCustomerForm">
        <div style="display:grid;gap:20px;">
            
            <!-- Customer ID (Read-only) -->
            <div>
                <label style="display:block;font-weight:500;color:#374151;margin-bottom:6px;font-size:14px;">
                    Customer ID
                </label>
                <input 
                    type="text" 
                    value="C<?= str_pad($customer['id'], 3, '0', STR_PAD_LEFT) ?>"
                    disabled
                    style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;background:#F9FAFB;color:#6B7280;"
                >
            </div>

            <!-- Customer Name -->
            <div>
                <label style="display:block;font-weight:500;color:#374151;margin-bottom:6px;font-size:14px;">
                    Customer Name <span style="color:#EF4444;">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="<?= html_escape($customer['name'] ?? '') ?>"
                    required
                    placeholder="Enter customer full name"
                    style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;"
                >
            </div>

            <!-- Email -->
            <div>
                <label style="display:block;font-weight:500;color:#374151;margin-bottom:6px;font-size:14px;">
                    Email Address
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    value="<?= html_escape($customer['email'] ?? '') ?>"
                    placeholder="customer@example.com"
                    style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;"
                >
                <small style="color:#6B7280;font-size:13px;margin-top:4px;display:block;">Optional - Used for order notifications</small>
            </div>

            <!-- Phone -->
            <div>
                <label style="display:block;font-weight:500;color:#374151;margin-bottom:6px;font-size:14px;">
                    Phone Number
                </label>
                <input 
                    type="tel" 
                    name="phone" 
                    id="phone"
                    value="<?= html_escape($customer['phone'] ?? '') ?>"
                    placeholder="+63 912 345 6789"
                    style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;"
                >
                <small style="color:#6B7280;font-size:13px;margin-top:4px;display:block;">Optional - Contact number</small>
            </div>

            <!-- Member Since (Read-only) -->
            <div>
                <label style="display:block;font-weight:500;color:#374151;margin-bottom:6px;font-size:14px;">
                    Member Since
                </label>
                <input 
                    type="text" 
                    value="<?= date('F d, Y', strtotime($customer['created_at'] ?? 'now')) ?>"
                    disabled
                    style="width:100%;padding:10px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:14px;background:#F9FAFB;color:#6B7280;"
                >
            </div>

        </div>

        <!-- Action Buttons -->
        <div style="display:flex;gap:12px;margin-top:32px;padding-top:24px;border-top:1px solid #E5E7EB;">
            <button type="submit" class="btn-primary" id="submitBtn">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;margin-right:6px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Customer
            </button>
            <a href="<?= site_url('admin/customers') ?>" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
.card {
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn-primary {
    padding: 10px 20px;
    background: #3B82F6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
}

.btn-primary:hover {
    background: #2563EB;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-primary:disabled {
    background: #9CA3AF;
    cursor: not-allowed;
}

.btn-secondary {
    padding: 10px 20px;
    background: #F3F4F6;
    color: #374151;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-secondary:hover {
    background: #E5E7EB;
}

input:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

input[required]:invalid {
    border-color: #FCA5A5;
}
</style>

<script>
document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter customer name');
        return;
    }
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="display:inline-block;vertical-align:middle;margin-right:6px;animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-width="4" stroke-dasharray="60" stroke-dashoffset="20"/></svg>Updating...';
});
</script>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

</body>
</html>
