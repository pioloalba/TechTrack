# TechTrack 1.3 - Hostinger Deployment Guide

## 📋 Table of Contents
1. [Project Analysis](#project-analysis)
2. [Technical Requirements](#technical-requirements)
3. [Pre-Deployment Checklist](#pre-deployment-checklist)
4. [Step-by-Step Deployment](#step-by-step-deployment)
5. [Post-Deployment Configuration](#post-deployment-configuration)
6. [Production Optimizations](#production-optimizations)
7. [Troubleshooting](#troubleshooting)

---

## 🔍 Project Analysis

### Framework & Structure
- **Framework**: LavaLust 4.2.3 (PHP MVC Framework)
- **Pattern**: Model-View-Controller (MVC)
- **Database**: MySQL with InnoDB engine
- **Composer Dependencies**: Google OAuth, PayMongo SDK
- **Custom Features**: QR Code generation, PDF reports, Rating system

### Key Components
```
techtrack1.3/
├── app/                    # Application code
│   ├── controllers/        # Business logic
│   ├── models/            # Database interactions
│   ├── views/             # HTML templates
│   ├── config/            # Configuration files
│   ├── helpers/           # Helper functions
│   └── migrations/        # Database migrations
├── public/                # Public assets
│   ├── assets/           # CSS, JS, images
│   └── uploads/          # User uploaded files
├── vendor/               # Composer dependencies
├── scheme/               # Framework core
├── sql/                  # Database dumps
├── .htaccess            # Apache rewrite rules
└── index.php            # Application entry point
```

---

## ⚙️ Technical Requirements

### Minimum Hostinger Requirements

#### PHP Requirements
- **PHP Version**: 8.0 or higher (8.2 recommended)
- **Required Extensions**:
  - ✅ `mysqli` or `pdo_mysql` - Database connectivity
  - ✅ `curl` - External API calls (Google OAuth, PayMongo, QR API)
  - ✅ `gd` - Image manipulation (QR placeholders)
  - ✅ `json` - JSON parsing
  - ✅ `mbstring` - String handling
  - ✅ `openssl` - Secure connections
  - ✅ `zip` - File compression
  - ✅ `fileinfo` - File type detection

#### PHP Configuration (`php.ini`)
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
allow_url_fopen = On
file_uploads = On
```

#### MySQL Requirements
- **Version**: MySQL 5.7+ or MariaDB 10.3+
- **Storage Engine**: InnoDB
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

#### Apache Modules
- ✅ `mod_rewrite` - URL rewriting (REQUIRED)
- ✅ `mod_headers` - Security headers
- ✅ `mod_expires` - Browser caching

---

## 📝 Pre-Deployment Checklist

### 1. Backup Your Local Database
```bash
# Export complete database
mysqldump -u root -p techtrack_db > techtrack_db_backup.sql

# Or use the included SQL file
# sql/techtrack_db_complete.sql
```

### 2. Review and Clean Files

#### Files to DELETE before upload:
```
✗ test_*.php (test files)
✗ setup_checker.php
✗ verify_*.php
✗ export_database.bat
✗ update_passwords.bat
✗ generate_password_hash.php
✗ migrate_customers.php
✗ run_*.php (migration runners)
✗ *.md (except README.md if needed)
✗ composer.lock (will regenerate)
✗ .git/ folder (if exists)
✗ NEW_SECURE_CREDENTIALS.txt
```

#### Files to KEEP:
```
✓ index.php
✓ .htaccess
✓ composer.json
✓ app/ folder
✓ public/ folder
✓ scheme/ folder
✓ vendor/ folder
✓ sql/techtrack_db_complete.sql (for reference)
✓ qrcode_generate.php (QR endpoint)
```

### 3. Prepare Configuration Changes

Create a file `deployment_config.txt` with your Hostinger details:
```
Database Host: localhost (usually)
Database Name: [your_hostinger_db_name]
Database User: [your_hostinger_db_user]
Database Pass: [your_hostinger_db_password]
Domain: https://yourdomain.com
Base URL: https://yourdomain.com/
```

---

## 🚀 Step-by-Step Deployment

### PHASE 1: Hostinger Setup

#### Step 1.1: Access Hostinger Control Panel
1. Log in to Hostinger: https://hpanel.hostinger.com
2. Select your hosting plan
3. Go to **MySQL Databases**

#### Step 1.2: Create Database
1. Click **"Create New Database"**
2. Database name: `u123456789_techtrack` (use your format)
3. Username: Same as database or create new
4. Password: Generate strong password (SAVE THIS!)
5. Click **Create**
6. Note down:
   - Database name
   - Username
   - Password
   - Host (usually `localhost`)

#### Step 1.3: Import Database
1. Click **"Manage"** next to your database
2. Opens phpMyAdmin
3. Select your database from left sidebar
4. Click **"Import"** tab
5. Click **"Choose File"**
6. Upload `sql/techtrack_db_complete.sql`
7. Click **"Go"** at bottom
8. Wait for success message

**⚠️ IMPORTANT**: After import, verify these tables exist:
- users, customers, customer_auth
- products, product_images, product_specs
- orders, order_items
- cart, wishlist
- customer_addresses
- alerts, inventory_transactions
- settings, migrations

---

### PHASE 2: File Upload

#### Step 2.1: Prepare Files Locally

1. **Clean vendor folder** (will reinstall on server):
```bash
# Delete vendor folder
rm -rf vendor/
```

2. **Create deployment archive**:
   - Select ALL files except those in DELETE list above
   - Right-click → Send to → Compressed (zipped) folder
   - Name: `techtrack_deployment.zip`

#### Step 2.2: Upload to Hostinger

**Option A: File Manager (Recommended for first deployment)**
1. Hostinger Panel → **File Manager**
2. Navigate to `public_html/`
3. Create subfolder if deploying to subdirectory (e.g., `public_html/techtrack/`)
4. Click **Upload** → Select `techtrack_deployment.zip`
5. Wait for upload (may take 5-10 minutes)
6. Right-click ZIP → **Extract**
7. Delete the ZIP file after extraction

**Option B: FTP (For large projects)**
1. Get FTP credentials from Hostinger Panel → **FTP Accounts**
2. Use FileZilla:
   - Host: `ftp.yourdomain.com`
   - Username: Your FTP user
   - Password: Your FTP password
   - Port: 21
3. Upload all files to `public_html/` or subfolder

---

### PHASE 3: Configuration

#### Step 3.1: Configure Database Connection

1. File Manager → Navigate to `app/config/database.php`
2. Click **Edit**
3. Update these lines:

```php
$database['main'] = array(
    'driver'	=> 'mysql',
    'hostname'	=> 'localhost',  // Usually localhost on Hostinger
    'port'		=> '3306',
    'username'	=> 'u123456789_techtrack',  // YOUR database username
    'password'	=> 'YOUR_STRONG_PASSWORD',   // YOUR database password
    'database'	=> 'u123456789_techtrack',  // YOUR database name
    'charset'	=> 'utf8mb4',
    'dbprefix'	=> '',
    'path'      => ''
);
```

4. Click **Save**

#### Step 3.2: Configure Base URL

1. Navigate to `app/config/config.php`
2. Click **Edit**
3. Find line ~85 and update:

```php
// OLD (local):
$config['base_url'] = 'http://localhost:8080/techtrack1.3/';

// NEW (production) - Option 1: Root domain
$config['base_url'] = 'https://yourdomain.com/';

// NEW (production) - Option 2: Subdirectory
$config['base_url'] = 'https://yourdomain.com/techtrack/';
```

4. Update environment to production:
```php
$config['ENVIRONMENT'] = 'production';  // Line ~70
```

5. Update log threshold for production:
```php
$config['log_threshold'] = 1;  // Only log errors and exceptions
```

6. **Save the file**

#### Step 3.3: Configure Google OAuth (If using)

1. Navigate to `app/config/google_oauth.php`
2. Update redirect URI:

```php
$config['google_oauth'] = [
    'client_id'     => 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
    'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
    'redirect_uri'  => 'https://yourdomain.com/googleauth/callback',  // UPDATE THIS
    'scopes'        => ['email', 'profile'],
];
```

3. **Update Google Console**:
   - Go to https://console.cloud.google.com
   - Navigate to your project
   - Credentials → OAuth 2.0 Client IDs
   - Edit your client
   - Add Authorized redirect URI: `https://yourdomain.com/googleauth/callback`
   - Save

#### Step 3.4: Configure PayMongo (If using)

1. Navigate to `app/config/paymongo.php`
2. For production, set:

```php
$config['paymongo'] = [
    'test_mode' => false,  // Change to false for live mode
    
    // Use LIVE keys in production
    'live_public_key' => 'pk_live_YOUR_ACTUAL_LIVE_KEY',
    'live_secret_key' => 'sk_live_YOUR_ACTUAL_LIVE_KEY',
    
    // Keep test keys for reference (get from PayMongo dashboard)
    'test_public_key' => 'pk_test_YOUR_TEST_KEY',
    'test_secret_key' => 'sk_test_YOUR_TEST_KEY',
];
```

---

### PHASE 4: Composer Dependencies

#### Step 4.1: SSH Access Method (Recommended)

1. **Enable SSH** in Hostinger Panel:
   - Advanced → SSH Access → Enable
   - Note your SSH credentials

2. **Connect via SSH**:
```bash
ssh u123456789@yourdomain.com
# Enter password when prompted
```

3. **Navigate to project**:
```bash
cd public_html/techtrack  # or just cd public_html if root
```

4. **Install Composer** (if not available):
```bash
curl -sS https://getcomposer.org/installer | php
```

5. **Install dependencies**:
```bash
php composer.phar install --no-dev --optimize-autoloader
# OR if composer is globally available:
composer install --no-dev --optimize-autoloader
```

#### Step 4.2: Manual Upload Method (If no SSH)

If Hostinger doesn't allow SSH:

1. **Local machine**: Run composer locally
```bash
composer install --no-dev --optimize-autoloader
```

2. **Upload vendor folder** via FTP/File Manager
   - ZIP the `vendor/` folder first
   - Upload to server
   - Extract in project root

---

### PHASE 5: Permissions Setup

#### Step 5.1: Set Directory Permissions

Via File Manager:
1. Right-click folder → **Permissions** (or **Change Permissions**)
2. Set these permissions:

```
📁 runtime/                  → 755 (rwxr-xr-x)
  📁 runtime/logs/           → 755
  📁 runtime/cache/          → 755
  
📁 public/uploads/           → 755
  📁 public/uploads/products/ → 755
  
📁 vendor/                   → 755
```

Via SSH:
```bash
chmod 755 runtime runtime/logs runtime/cache
chmod 755 public/uploads public/uploads/products
chmod 755 vendor
```

**⚠️ Security Note**: NEVER set 777 permissions in production!

---

### PHASE 6: .htaccess Configuration

#### Step 6.1: Root .htaccess

Check `public_html/.htaccess` (or your project root):

```apache
AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # If in subdirectory, update this:
    # RewriteBase /techtrack/
    
    # Allow direct access to public folder assets
    RewriteCond %{REQUEST_URI} ^/public/
    RewriteRule ^ - [L]
    
    # Block access to sensitive files
    RewriteCond %{REQUEST_URI} ^/(app|scheme|runtime|vendor|sql)/
    RewriteRule ^ - [F,L]
    
    # Route everything else through index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php/$1 [QSA,L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Disable directory browsing
Options -Indexes

# Hide sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

#### Step 6.2: Verify Protected Directories

These files should already exist:
- `app/.htaccess` → Deny all
- `scheme/.htaccess` → Deny all
- `runtime/.htaccess` → Deny all
- `public/uploads/.htaccess` → Allow images, block PHP

---

### PHASE 7: SSL Certificate

#### Step 7.1: Enable SSL (Free with Hostinger)

1. Hostinger Panel → **SSL**
2. Select your domain
3. Click **"Install SSL"** (Free Let's Encrypt)
4. Wait 10-15 minutes for activation

#### Step 7.2: Force HTTPS

Add to top of `.htaccess`:

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## ✅ Post-Deployment Verification

### Step 1: Test Database Connection

Visit: `https://yourdomain.com/`

**Expected**: Homepage loads without errors

**If error "Database connection failed"**:
- Recheck `app/config/database.php` credentials
- Verify database exists in Hostinger phpMyAdmin
- Check database user has privileges

### Step 2: Test Login Systems

#### Admin Login:
- URL: `https://yourdomain.com/auth`
- Default: `admin@techtrack.com` / `admin123`

#### Customer Login:
- URL: `https://yourdomain.com/login`
- Test with existing customer

### Step 3: Test QR Codes

1. Visit any product page
2. Scroll to "Share this Product" section
3. QR code should display
4. Scan with phone (must be on internet)

**If QR not showing**:
- Check `qrcode_generate.php` exists in root
- Verify `allow_url_fopen = On` in PHP settings
- Test URL directly: `https://yourdomain.com/qrcode_generate.php?data=test&size=150`

### Step 4: Test File Uploads

1. Admin Panel → Products → Add New
2. Upload product image
3. Should save to `public/uploads/products/`

### Step 5: Test PayMongo (if enabled)

1. Shop → Add product to cart
2. Proceed to checkout
3. Test payment flow

---

## 🔧 Production Optimizations

### 1. Enable Caching

Edit `app/config/config.php`:

```php
// Enable caching
$config['cache_enabled'] = true;
$config['cache_lifetime'] = 3600; // 1 hour
```

### 2. Optimize Images

Before uploading product images:
- Resize to max 1200px width
- Compress using TinyPNG or similar
- Use WebP format when possible

### 3. Enable OPcache

Ask Hostinger support to enable OPcache for PHP:
- Improves PHP performance by 2-3x
- Free and should be enabled by default

### 4. Database Optimization

Run these in phpMyAdmin:

```sql
-- Optimize all tables
OPTIMIZE TABLE users, customers, customer_auth, products, 
  product_images, orders, order_items, cart, wishlist, 
  customer_addresses, alerts, inventory_transactions;

-- Add indexes if missing
ALTER TABLE orders ADD INDEX idx_status (status);
ALTER TABLE orders ADD INDEX idx_created (created_at);
ALTER TABLE products ADD INDEX idx_category (category);
```

### 5. Monitor Error Logs

Check regularly:
- File Manager → `runtime/logs/`
- Review `log-YYYY-MM-DD.php` files
- Fix any recurring errors

---

## 🔒 Security Checklist

### Critical Security Steps

#### 1. Change Default Passwords

**Admin Users**:
```sql
-- In phpMyAdmin, run:
UPDATE users 
SET password = '$2y$10$YOUR_NEW_BCRYPT_HASH_HERE'
WHERE email = 'admin@techtrack.com';
```

Generate hash locally:
```php
<?php
echo password_hash('YourNewSecurePassword123!', PASSWORD_BCRYPT);
?>
```

#### 2. Remove Debug/Test Files

Delete these if they exist:
```
test_*.php
setup_checker.php
phpinfo.php
info.php
```

#### 3. Protect Config Files

Ensure these are NOT web-accessible:
- `app/config/database.php`
- `app/config/google_oauth.php`
- `app/config/paymongo.php`

Test: `https://yourdomain.com/app/config/database.php` should show **403 Forbidden**

#### 4. Update CSRF Protection

Already enabled in config:
```php
$config['csrf_protection'] = true;
```

#### 5. Secure API Keys

**Google OAuth**:
- Use production credentials
- Restrict API keys to your domain
- Enable domain verification in Google Console

**PayMongo**:
- Use LIVE keys only
- Never expose secret keys in JavaScript
- Implement webhook signature verification

---

## ⚠️ Troubleshooting

### Issue 1: White Screen / 500 Error

**Cause**: PHP error, usually configuration

**Solutions**:
1. Check error logs: `runtime/logs/`
2. Enable error display temporarily:
   ```php
   // Add to top of index.php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. Check PHP version: Hostinger Panel → PHP Configuration
4. Verify all required extensions are enabled

### Issue 2: "Database Connection Failed"

**Solutions**:
1. Verify credentials in `app/config/database.php`
2. Test connection in phpMyAdmin
3. Check if database user has correct privileges
4. Confirm hostname is `localhost` (not IP)

### Issue 3: 404 on All Pages (Except Homepage)

**Cause**: mod_rewrite not working

**Solutions**:
1. Check `.htaccess` exists in root
2. Verify `RewriteBase` matches your setup
3. Contact Hostinger to enable mod_rewrite
4. Check if in subdirectory, update paths

### Issue 4: Images Not Loading

**Solutions**:
1. Check paths in database:
   ```sql
   SELECT image_url FROM products LIMIT 5;
   ```
2. Should be: `public/uploads/products/filename.jpg`
3. Verify files exist in that directory
4. Check permissions: 755 for folders, 644 for files

### Issue 5: QR Codes Not Generating

**Solutions**:
1. Verify `qrcode_generate.php` exists
2. Test directly: `/qrcode_generate.php?data=test&size=150`
3. Check `allow_url_fopen` is On in PHP settings
4. Verify api.qrserver.com is accessible from server

### Issue 6: PayMongo Errors

**Solutions**:
1. Verify using correct API keys (test vs live)
2. Check `test_mode` setting matches keys
3. Confirm PayMongo account is verified
4. Enable CURL extension in PHP
5. Check error logs for API responses

### Issue 7: Google OAuth Fails

**Solutions**:
1. Update redirect URI in Google Console
2. Verify redirect URI matches exactly: `https://yourdomain.com/googleauth/callback`
3. Check OAuth consent screen is published
4. Confirm domain ownership in Google Console

---

## 📊 Post-Launch Monitoring

### Week 1 Checklist

- [ ] Check error logs daily
- [ ] Test all user flows (browse, cart, checkout)
- [ ] Verify email notifications work
- [ ] Monitor page load times
- [ ] Test on mobile devices
- [ ] Verify SSL certificate is active
- [ ] Check payment gateway transactions
- [ ] Test admin panel functions
- [ ] Review database backups

### Monthly Maintenance

- [ ] Backup database
- [ ] Download error logs and review
- [ ] Check for PHP/framework updates
- [ ] Review security logs
- [ ] Optimize database tables
- [ ] Clear old cache files
- [ ] Check disk space usage
- [ ] Test backup restoration

---

## 📧 Support Resources

### Hostinger Support
- 24/7 Live Chat: https://hpanel.hostinger.com
- Knowledge Base: https://support.hostinger.com
- Email: support@hostinger.com

### Framework Documentation
- LavaLust: https://lavalust.netlify.app
- GitHub: https://github.com/ronmarasigan/LavaLust

### External APIs
- Google OAuth: https://console.cloud.google.com
- PayMongo: https://dashboard.paymongo.com
- QR API: https://goqr.me/api/

---

## 📝 Environment Variables (Advanced)

For better security, use environment variables:

Create `.env` file in root:
```env
DB_HOST=localhost
DB_NAME=u123456789_techtrack
DB_USER=u123456789_techtrack
DB_PASS=your_password
BASE_URL=https://yourdomain.com/
ENVIRONMENT=production

GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret

PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
```

Update `app/config/database.php`:
```php
$database['main'] = array(
    'hostname'	=> getenv('DB_HOST') ?: 'localhost',
    'username'	=> getenv('DB_USER') ?: 'root',
    'password'	=> getenv('DB_PASS') ?: '',
    'database'	=> getenv('DB_NAME') ?: 'techtrack_db',
);
```

---

## 🎯 Success Criteria

Your deployment is successful when:

✅ Homepage loads without errors  
✅ Admin login works  
✅ Customer login/registration works  
✅ Products display correctly with images  
✅ Cart and wishlist function  
✅ Checkout process completes  
✅ QR codes generate and scan correctly  
✅ PDF reports download  
✅ Google OAuth login works (if enabled)  
✅ PayMongo payments process (if enabled)  
✅ SSL certificate is active  
✅ No errors in logs  

---

## 📅 Quick Deployment Timeline

**Total Time: 2-4 hours**

- Database setup: 20 minutes
- File upload: 30 minutes
- Configuration: 30 minutes
- Composer dependencies: 15 minutes
- Testing: 30 minutes
- SSL setup: 15 minutes
- Security hardening: 20 minutes
- Final verification: 20 minutes

---

## 🚨 Emergency Rollback

If deployment fails critically:

1. **Restore database**:
   - phpMyAdmin → Import → Upload backup SQL
   
2. **Restore files**:
   - FTP/File Manager → Delete current files
   - Upload previous backup
   
3. **Quick fix** for common issues:
   - Wrong base_url → Edit `app/config/config.php`
   - Database error → Edit `app/config/database.php`
   - 500 error → Check `runtime/logs/` for details

---

**Document Version**: 1.0  
**Last Updated**: November 28, 2025  
**Prepared For**: TechTrack 1.3 Production Deployment  
**Target Platform**: Hostinger Shared Hosting  

---

## 📞 Need Help?

If you encounter issues not covered in this guide:

1. Check error logs in `runtime/logs/`
2. Review Hostinger documentation
3. Test locally to isolate server-specific issues
4. Contact Hostinger support for server configuration issues

**Good luck with your deployment! 🚀**
