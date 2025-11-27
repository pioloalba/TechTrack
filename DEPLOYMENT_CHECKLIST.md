# TechTrack 1.3 - Deployment Checklist

## 🚀 Step-by-Step Deployment to Hostinger

**Prerequisites**: Complete PRE_DEPLOYMENT_CHECKLIST.md first

---

## PHASE 1: Hostinger Setup

### Step 1.1: Access Hostinger Control Panel
- [ ] Log in to Hostinger: https://hpanel.hostinger.com
- [ ] Select your hosting plan
- [ ] Navigate to **MySQL Databases** section

### Step 1.2: Create Database
- [ ] Click **"Create New Database"**
- [ ] Enter database name: `________________`
- [ ] Enter username: `________________` (or auto-generate)
- [ ] Generate strong password
- [ ] **SAVE CREDENTIALS**: 
  ```
  Database: ________________
  Username: ________________
  Password: ________________
  Hostname: localhost
  ```
- [ ] Click **Create**
- [ ] Verify database appears in list

### Step 1.3: Import Database
- [ ] Click **"Manage"** next to your database (opens phpMyAdmin)
- [ ] Select database from left sidebar
- [ ] Click **"Import"** tab
- [ ] Click **"Choose File"**
- [ ] Upload `sql/techtrack_db_complete.sql`
- [ ] Scroll down and click **"Go"**
- [ ] Wait for success message
- [ ] Verify table count (should see 15-20 tables)

### Step 1.4: Verify Database Import
Check these critical tables exist:
- [ ] `users` - Has admin accounts
- [ ] `customers` - Customer data
- [ ] `products` - Product catalog
- [ ] `orders` - Order records
- [ ] `cart` - Shopping cart
- [ ] `customer_auth` - Login credentials

---

## PHASE 2: File Upload

### Step 2.1: Prepare Files Locally
- [ ] Delete `vendor/` folder from local project
- [ ] Create ZIP: `techtrack_deployment.zip`
- [ ] Verify ZIP size (manageable for upload)

### Step 2.2: Upload to Hostinger

**Using File Manager** (Recommended):
- [ ] Hostinger Panel → **File Manager**
- [ ] Navigate to `public_html/`
- [ ] If subdirectory: Create folder (e.g., `techtrack/`)
- [ ] Click **Upload** button
- [ ] Select `techtrack_deployment.zip`
- [ ] Wait for upload completion (5-10 minutes)
- [ ] Right-click ZIP → **Extract**
- [ ] Verify files extracted correctly
- [ ] Delete ZIP file

**OR Using FTP**:
- [ ] Open FileZilla (or FTP client)
- [ ] Connect using Hostinger FTP credentials
- [ ] Navigate to `public_html/` (or subdirectory)
- [ ] Upload all project files
- [ ] Wait for transfer completion

---

## PHASE 3: Configuration

### Step 3.1: Configure Database Connection
- [ ] File Manager → `app/config/database.php`
- [ ] Click **Edit**
- [ ] Update these values:
  ```php
  'hostname' => 'localhost',
  'username' => 'YOUR_DB_USER',
  'password' => 'YOUR_DB_PASSWORD',
  'database' => 'YOUR_DB_NAME',
  ```
- [ ] Click **Save**
- [ ] Close file

### Step 3.2: Configure Base URL
- [ ] File Manager → `app/config/config.php`
- [ ] Click **Edit**
- [ ] Find and update (~line 85):
  ```php
  $config['base_url'] = 'https://yourdomain.com/';
  ```
- [ ] Update environment (~line 70):
  ```php
  $config['ENVIRONMENT'] = 'production';
  ```
- [ ] Update log threshold (~line 90):
  ```php
  $config['log_threshold'] = 1;
  ```
- [ ] Click **Save**

### Step 3.3: Configure Google OAuth (If Using)
- [ ] File Manager → `app/config/google_oauth.php`
- [ ] Click **Edit**
- [ ] Update redirect URI:
  ```php
  'redirect_uri' => 'https://yourdomain.com/googleauth/callback',
  ```
- [ ] Click **Save**
- [ ] Go to Google Cloud Console
- [ ] Navigate to your project → Credentials
- [ ] Edit OAuth 2.0 Client ID
- [ ] Add Authorized redirect URI: `https://yourdomain.com/googleauth/callback`
- [ ] Save in Google Console

### Step 3.4: Configure PayMongo (If Using)
- [ ] File Manager → `app/config/paymongo.php`
- [ ] Click **Edit**
- [ ] Set test_mode to false:
  ```php
  'test_mode' => false,
  ```
- [ ] Update LIVE keys:
  ```php
  'live_public_key' => 'pk_live_YOUR_KEY',
  'live_secret_key' => 'sk_live_YOUR_KEY',
  ```
- [ ] Click **Save**

---

## PHASE 4: Composer Dependencies

### Method A: SSH (Recommended)
- [ ] Hostinger Panel → **SSH Access** → Enable
- [ ] Note SSH credentials
- [ ] Connect via SSH:
  ```bash
  ssh u123456789@yourdomain.com
  ```
- [ ] Navigate to project:
  ```bash
  cd public_html/techtrack
  ```
- [ ] Install Composer (if needed):
  ```bash
  curl -sS https://getcomposer.org/installer | php
  ```
- [ ] Install dependencies:
  ```bash
  php composer.phar install --no-dev --optimize-autoloader
  ```
- [ ] Wait for completion
- [ ] Verify `vendor/` folder created

### Method B: Manual Upload (If No SSH)
- [ ] Local machine: Run `composer install --no-dev`
- [ ] ZIP the `vendor/` folder
- [ ] Upload ZIP to server via File Manager
- [ ] Extract in project root
- [ ] Delete ZIP file

---

## PHASE 5: Permissions Setup

### Set Directory Permissions (755)
- [ ] Right-click `runtime/` → Permissions → 755
- [ ] Right-click `runtime/logs/` → Permissions → 755
- [ ] Right-click `runtime/cache/` → Permissions → 755
- [ ] Right-click `public/uploads/` → Permissions → 755
- [ ] Right-click `public/uploads/products/` → Permissions → 755
- [ ] Right-click `vendor/` → Permissions → 755

**Security Note**: NEVER use 777 permissions in production!

---

## PHASE 6: .htaccess Configuration

### Step 6.1: Verify Root .htaccess
- [ ] File Manager → Check `.htaccess` exists in root
- [ ] Click **Edit**
- [ ] Verify RewriteBase matches your setup:
  ```apache
  RewriteBase /              # For root domain
  # OR
  RewriteBase /techtrack/    # For subdirectory
  ```
- [ ] Verify mod_rewrite rules present
- [ ] Click **Save**

### Step 6.2: Verify Protected Directories
- [ ] Check `app/.htaccess` exists (Deny all)
- [ ] Check `scheme/.htaccess` exists (Deny all)
- [ ] Check `runtime/.htaccess` exists (Deny all)

---

## PHASE 7: SSL Certificate

### Step 7.1: Enable SSL
- [ ] Hostinger Panel → **SSL**
- [ ] Select your domain
- [ ] Click **"Install SSL"** (Free Let's Encrypt)
- [ ] Wait 10-15 minutes for activation
- [ ] Refresh page to verify status: "Active"

### Step 7.2: Force HTTPS
- [ ] File Manager → Edit root `.htaccess`
- [ ] Add at the top (if not present):
  ```apache
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```
- [ ] Click **Save**

---

## PHASE 8: Post-Deployment Verification

### Test 1: Database Connection
- [ ] Visit: `https://yourdomain.com/`
- [ ] Expected: Homepage loads without errors
- [ ] If error: Check database credentials in `app/config/database.php`

### Test 2: Admin Login
- [ ] Visit: `https://yourdomain.com/auth`
- [ ] Enter: `admin@techtrack.com` / `admin123`
- [ ] Expected: Successfully logs in to admin panel
- [ ] If fails: Check `users` table in database

### Test 3: Customer Login
- [ ] Visit: `https://yourdomain.com/login`
- [ ] Test with existing customer credentials
- [ ] Expected: Successfully logs in
- [ ] Test registration flow

### Test 4: Product Display
- [ ] Navigate to shop/products page
- [ ] Expected: Products display with images
- [ ] Click on a product
- [ ] Expected: Product details page loads
- [ ] If images broken: Check paths in database and file uploads

### Test 5: QR Code Generation
- [ ] Go to any product page
- [ ] Scroll to "Share this Product" section
- [ ] Expected: QR code displays
- [ ] Scan with phone (must have internet)
- [ ] If not showing: Test `https://yourdomain.com/qrcode_generate.php?data=test&size=150`

### Test 6: Cart and Checkout
- [ ] Add product to cart
- [ ] View cart
- [ ] Proceed to checkout
- [ ] Expected: All steps work smoothly

### Test 7: File Uploads
- [ ] Admin Panel → Products → Add New
- [ ] Upload product image
- [ ] Expected: Image saves to `public/uploads/products/`
- [ ] Verify image displays on product page

### Test 8: PDF Reports
- [ ] Admin Panel → Reports
- [ ] Generate any report
- [ ] Click "Download PDF"
- [ ] Expected: PDF downloads successfully

### Test 9: Google OAuth (If Enabled)
- [ ] Visit login page
- [ ] Click "Login with Google"
- [ ] Expected: Redirects to Google consent screen
- [ ] Complete authentication
- [ ] Expected: Redirects back and logs in

### Test 10: PayMongo (If Enabled)
- [ ] Add product to cart
- [ ] Proceed to checkout
- [ ] Test payment flow
- [ ] Expected: Payment processes (use test card if testing)

---

## PHASE 9: Security Hardening

### Step 9.1: Change Default Passwords
- [ ] Generate new admin password hash locally:
  ```php
  echo password_hash('NewSecurePassword123!', PASSWORD_BCRYPT);
  ```
- [ ] Copy hash
- [ ] phpMyAdmin → `users` table
- [ ] Update admin password field with new hash
- [ ] Test new login credentials

### Step 9.2: Remove Test Files
Verify these are deleted:
- [ ] No `test_*.php` files exist
- [ ] No `phpinfo.php` or `info.php`
- [ ] No `setup_checker.php`
- [ ] No debug/migration files

### Step 9.3: Verify File Protection
Test these URLs (should show 403 Forbidden):
- [ ] `https://yourdomain.com/app/config/database.php`
- [ ] `https://yourdomain.com/scheme/`
- [ ] `https://yourdomain.com/runtime/`
- [ ] `https://yourdomain.com/.env` (if exists)

### Step 9.4: Check Error Logs
- [ ] File Manager → `runtime/logs/`
- [ ] Open latest log file
- [ ] Review for any critical errors
- [ ] Address any issues found

---

## PHASE 10: Production Optimizations

### Step 10.1: Enable Caching
- [ ] File Manager → `app/config/config.php`
- [ ] Edit and set:
  ```php
  $config['cache_enabled'] = true;
  $config['cache_lifetime'] = 3600;
  ```
- [ ] Save

### Step 10.2: Optimize Database
- [ ] phpMyAdmin → Select database
- [ ] Check all tables
- [ ] From dropdown: **Optimize table**
- [ ] Click **Go**

### Step 10.3: Enable OPcache
- [ ] Hostinger Panel → **PHP Configuration**
- [ ] Find OPcache section
- [ ] Enable if not already enabled
- [ ] Save changes

---

## PHASE 11: Final Verification

### Complete Functionality Test
- [ ] Browse as guest user
- [ ] Register new customer account
- [ ] Browse products
- [ ] Add to cart and wishlist
- [ ] Complete checkout process
- [ ] Log in as admin
- [ ] Add/edit product
- [ ] View orders
- [ ] Generate reports
- [ ] Test on mobile device
- [ ] Test on different browsers

### Performance Check
- [ ] Page loads in under 3 seconds
- [ ] Images load properly
- [ ] No console errors (F12 dev tools)
- [ ] Mobile responsive design works

### Security Check
- [ ] SSL certificate active (padlock icon)
- [ ] HTTPS redirect works
- [ ] Protected directories return 403
- [ ] Login forms use CSRF protection

---

## PHASE 12: Monitoring Setup

### Week 1 Actions
- [ ] Check error logs daily (`runtime/logs/`)
- [ ] Monitor site performance
- [ ] Watch for user-reported issues
- [ ] Test payment processing (if applicable)
- [ ] Verify email notifications work

### Documentation
- [ ] Document final configuration
- [ ] Save all credentials securely
- [ ] Note any customizations made
- [ ] Create backup schedule

---

## 🎯 Deployment Success Criteria

Your deployment is complete when ALL of these are true:

- [x] Homepage loads without errors
- [x] Admin login works
- [x] Customer login/registration works
- [x] Products display with images
- [x] Cart and wishlist function
- [x] Checkout process completes
- [x] QR codes generate and scan
- [x] PDF reports download
- [x] Google OAuth works (if enabled)
- [x] PayMongo payments process (if enabled)
- [x] SSL certificate is active
- [x] No critical errors in logs
- [x] All security checks pass
- [x] Site tested on mobile
- [x] Performance is acceptable

---

## 🚨 If Something Goes Wrong

### Emergency Rollback
1. **Database**: phpMyAdmin → Import backup SQL
2. **Files**: Delete current files, upload backup
3. **Quick Fixes**:
   - Wrong base_url → Edit `app/config/config.php`
   - Database error → Edit `app/config/database.php`
   - 500 error → Check `runtime/logs/` for details

### Get Help
- [ ] Check `runtime/logs/` for error details
- [ ] Review **Troubleshooting** section in HOSTINGER_DEPLOYMENT_GUIDE.md
- [ ] Contact Hostinger 24/7 support
- [ ] Test locally to isolate server-specific issues

---

## 📊 Deployment Timeline

**Actual Time Taken**: _____ hours _____ minutes

- Database setup: _____ min
- File upload: _____ min
- Configuration: _____ min
- Composer install: _____ min
- Testing: _____ min
- Security: _____ min
- Final verification: _____ min

---

## 📝 Post-Deployment Notes

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Domain**: _______________  
**Hosting Plan**: _______________

**Issues Encountered**:
- 
- 
- 

**Solutions Applied**:
- 
- 
- 

**Next Steps**:
- [ ] Monitor for 1 week
- [ ] Set up automated backups
- [ ] Configure monitoring tools
- [ ] Update documentation
- [ ] Train users/staff

---

**Checklist Version**: 1.0  
**Last Updated**: November 28, 2025  
**Status**: [ ] In Progress [ ] Complete [ ] Issues Found

---

## 🎉 Congratulations!

If you've completed all steps above, your TechTrack 1.3 application is now live on Hostinger!

**Next**: Review HOSTINGER_DEPLOYMENT_GUIDE.md for maintenance and troubleshooting information.
