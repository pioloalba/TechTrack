# Hostinger Deployment - Quick Fix Guide

## 🔥 Common Issues & Instant Solutions

### Issue 1: White Screen (500 Error)

**Quick Fix:**
```php
// Add temporarily to index.php (line 2)
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**Check:**
- runtime/logs/ for error details
- PHP version = 8.0+ in Hostinger panel
- All PHP extensions enabled

---

### Issue 2: Database Connection Failed

**Quick Fix:**
Edit `app/config/database.php`:
```php
'hostname' => 'localhost',  // NOT an IP address
'username' => 'u123_yourdbuser',
'password' => 'your_actual_password',
'database' => 'u123_yourdbname',
```

**Test in phpMyAdmin:**
- Can you log in with same credentials?
- Does database exist?
- Check for typos in credentials

---

### Issue 3: 404 on All Pages (Homepage works)

**Problem**: mod_rewrite not working

**Quick Fix 1** - Update `.htaccess`:
```apache
RewriteBase /
# If in subfolder: RewriteBase /techtrack/
```

**Quick Fix 2** - Add to `.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

---

### Issue 4: Images Not Loading

**Check these paths:**
```
❌ Wrong: /techtrack1.3/public/uploads/products/image.jpg
✅ Right: public/uploads/products/image.jpg
```

**Quick Fix:**
```sql
-- In phpMyAdmin:
UPDATE product_images 
SET image_url = REPLACE(image_url, '/techtrack1.3/', '');
```

---

### Issue 5: QR Codes Not Showing

**Test:** Visit `https://yourdomain.com/qrcode_generate.php?data=test&size=150`

**Expected:** A QR code image appears

**If fails:**
1. Check if file exists: `qrcode_generate.php` in root
2. PHP setting: `allow_url_fopen = On`
3. Test external API: `https://api.qrserver.com/v1/create-qr-code/?data=test&size=150`

---

### Issue 6: Composer Dependencies Missing

**Error:** "Class 'Google\Client' not found"

**Quick Fix (SSH):**
```bash
cd public_html
php composer.phar install --no-dev
```

**Alternative (No SSH):**
1. Run locally: `composer install --no-dev`
2. ZIP the vendor/ folder
3. Upload and extract on server

---

### Issue 7: Permission Denied Errors

**Quick Fix:**
```bash
# Via SSH
chmod 755 runtime runtime/logs runtime/cache
chmod 755 public/uploads

# Via File Manager: Right-click → Permissions → 755
```

**Never use 777 in production!**

---

### Issue 8: SSL Certificate Not Working

**Quick Fix:**
1. Hostinger Panel → SSL
2. Install Free SSL (Let's Encrypt)
3. Wait 10-15 minutes
4. Force HTTPS in `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

### Issue 9: Google OAuth Fails

**Error:** "redirect_uri_mismatch"

**Quick Fix:**
1. Update `app/config/google_oauth.php`:
   ```php
   'redirect_uri' => 'https://yourdomain.com/googleauth/callback',
   ```

2. Google Console (console.cloud.google.com):
   - Credentials → Edit OAuth Client
   - Add exact URI: `https://yourdomain.com/googleauth/callback`
   - No trailing slash!

---

### Issue 10: PayMongo Errors

**Error:** "Invalid API key"

**Quick Fix:**
Check `app/config/paymongo.php`:
```php
'test_mode' => false,  // Must be false for live keys
'live_public_key' => 'pk_live_...',  // Starts with pk_live_
'live_secret_key' => 'sk_live_...',  // Starts with sk_live_
```

**Verify:** Keys from PayMongo dashboard match exactly

---

## 🔍 Diagnostic Commands

### Check PHP Version
```php
<?php phpinfo(); ?>
// Save as phpinfo.php, upload, visit
// DELETE after checking!
```

### Check Database Connection
```php
<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=yourdb', 'youruser', 'yourpass');
    echo "✓ Connected!";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
```

### Check File Permissions
```bash
# Via SSH
ls -la runtime/
ls -la public/uploads/
```

### Check Error Logs
```bash
# Via SSH
tail -n 50 runtime/logs/log-2025-11-28.php

# Via File Manager
# Open runtime/logs/ → Download latest log file
```

---

## 🆘 Emergency Rollback

**If everything breaks:**

1. **Restore Database:**
   - phpMyAdmin → Import → `sql/techtrack_db_complete.sql`

2. **Restore Files:**
   - Delete current files
   - Re-upload backup

3. **Reset Config:**
   ```php
   // app/config/database.php - use old credentials
   // app/config/config.php - use old base_url
   ```

---

## 📞 When to Contact Hostinger Support

Contact support for:
- ✅ Enabling PHP extensions
- ✅ Increasing PHP limits (memory, upload size)
- ✅ mod_rewrite issues
- ✅ SSL certificate problems
- ✅ Server-level errors (502, 503)
- ✅ Cron job setup
- ✅ Email configuration

**Don't contact for:**
- ❌ Application code errors (check your logs)
- ❌ Database query issues (fix in code)
- ❌ Third-party API problems (Google, PayMongo)

---

## ⚡ Performance Quick Wins

### 1. Enable Caching
```php
// app/config/config.php
$config['cache_enabled'] = true;
```

### 2. Optimize Database
```sql
-- Run in phpMyAdmin
OPTIMIZE TABLE users, customers, products, orders;
```

### 3. Compress Images
- Before upload, resize to 1200px max
- Use tools: TinyPNG, ImageOptim
- Convert to WebP format

### 4. Enable OPcache
- Ask Hostinger support
- Usually enabled by default
- Improves PHP speed 2-3x

---

## 🔐 Security Quick Wins

### 1. Change Default Passwords
```sql
UPDATE users 
SET password = '$2y$10$NEW_HASH_HERE'
WHERE email = 'admin@techtrack.com';
```

### 2. Delete Test Files
```
rm test_*.php
rm setup_checker.php
rm phpinfo.php
```

### 3. Update .htaccess
```apache
# Block sensitive directories
RewriteRule ^(app|scheme|runtime|vendor)/ - [F,L]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
```

---

## 📊 Health Check Script

Save as `health_check.php` (delete after use):

```php
<?php
$checks = [];

// PHP Version
$checks['PHP Version'] = phpversion() >= '8.0' ? '✓ ' . phpversion() : '✗ Too old';

// Extensions
$required = ['mysqli', 'curl', 'gd', 'json', 'mbstring'];
foreach ($required as $ext) {
    $checks[$ext] = extension_loaded($ext) ? '✓ Loaded' : '✗ Missing';
}

// Database
try {
    require 'app/config/database.php';
    $db = $database['main'];
    new PDO("mysql:host={$db['hostname']};dbname={$db['database']}", 
            $db['username'], $db['password']);
    $checks['Database'] = '✓ Connected';
} catch (Exception $e) {
    $checks['Database'] = '✗ ' . $e->getMessage();
}

// Permissions
$checks['runtime/ writable'] = is_writable('runtime') ? '✓ Yes' : '✗ No';
$checks['uploads/ writable'] = is_writable('public/uploads') ? '✓ Yes' : '✗ No';

// Display
echo '<pre>';
foreach ($checks as $name => $status) {
    echo str_pad($name, 25) . ' : ' . $status . "\n";
}
echo '</pre>';
?>
```

---

**Need more help? Check HOSTINGER_DEPLOYMENT_GUIDE.md for detailed instructions.**
