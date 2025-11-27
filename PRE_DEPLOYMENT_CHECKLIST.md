# TechTrack 1.3 - Pre-Deployment Checklist

## 📋 Complete This Before Deploying to Hostinger

---

## ✅ 1. Backup Your Local Database

- [ ] Export complete database
  ```bash
  mysqldump -u root -p techtrack_db > techtrack_db_backup.sql
  ```
- [ ] Verify backup file exists and has content
- [ ] Save backup to safe location (cloud storage recommended)
- [ ] Alternative: Use `sql/techtrack_db_complete.sql`

---

## ✅ 2. Review and Clean Files

### Files to DELETE before upload:
- [ ] `test_*.php` (all test files)
- [ ] `setup_checker.php`
- [ ] `verify_*.php`
- [ ] `export_database.bat`
- [ ] `update_passwords.bat`
- [ ] `generate_password_hash.php`
- [ ] `migrate_customers.php`
- [ ] `run_*.php` (migration runners)
- [ ] `*.md` files (except README.md if needed)
- [ ] `composer.lock` (will regenerate on server)
- [ ] `.git/` folder (if exists)
- [ ] `NEW_SECURE_CREDENTIALS.txt`
- [ ] Any personal notes or test data files

### Files to KEEP:
- [ ] `index.php`
- [ ] `.htaccess`
- [ ] `composer.json`
- [ ] `app/` folder (entire directory)
- [ ] `public/` folder (entire directory)
- [ ] `scheme/` folder (entire directory)
- [ ] `vendor/` folder (will reinstall on server)
- [ ] `sql/techtrack_db_complete.sql` (for reference)
- [ ] `qrcode_generate.php` (QR endpoint)

---

## ✅ 3. Verify Local Functionality

### Core Features:
- [ ] Admin login works (`admin@techtrack.com`)
- [ ] Customer login/registration works
- [ ] Products display correctly with images
- [ ] Cart functionality works
- [ ] Checkout process completes
- [ ] Order management works
- [ ] Inventory tracking updates

### Additional Features:
- [ ] QR code generation works
- [ ] PDF reports download correctly
- [ ] Google OAuth login (if enabled)
- [ ] PayMongo payments process (if enabled)
- [ ] Rating/review system works
- [ ] Email notifications send

---

## ✅ 4. Prepare Configuration Details

Create a file `deployment_config.txt` with your Hostinger details:

- [ ] Database Host: `____________` (usually `localhost`)
- [ ] Database Name: `____________` (e.g., `u123456789_techtrack`)
- [ ] Database User: `____________`
- [ ] Database Password: `____________`
- [ ] Domain: `https://____________`
- [ ] Base URL: `https://____________/` or `https://____________/techtrack/`
- [ ] Deployment Type: [ ] Root domain [ ] Subdirectory

---

## ✅ 5. Security Preparations

### Password Changes:
- [ ] Generate new admin password hash
  ```php
  echo password_hash('YourNewPassword123!', PASSWORD_BCRYPT);
  ```
- [ ] Document new password securely
- [ ] Plan to update after deployment

### API Keys Review:
- [ ] Google OAuth credentials ready
  - [ ] Client ID: `____________`
  - [ ] Client Secret: `____________`
  - [ ] Redirect URI will be updated post-deployment
- [ ] PayMongo keys ready (LIVE keys for production)
  - [ ] Live Public Key: `pk_live_____________`
  - [ ] Live Secret Key: `sk_live_____________`

---

## ✅ 6. Hostinger Account Verification

- [ ] Hostinger account active
- [ ] Hosting plan supports PHP 8.0+
- [ ] Domain pointed to Hostinger nameservers
- [ ] Access to hPanel: https://hpanel.hostinger.com
- [ ] FTP/SSH credentials available (if needed)

---

## ✅ 7. Required PHP Extensions Check

Verify your Hostinger plan includes:
- [ ] `mysqli` or `pdo_mysql` - Database connectivity
- [ ] `curl` - External API calls
- [ ] `gd` - Image manipulation
- [ ] `json` - JSON parsing
- [ ] `mbstring` - String handling
- [ ] `openssl` - Secure connections
- [ ] `zip` - File compression
- [ ] `fileinfo` - File type detection

*Check in: Hostinger Panel → PHP Configuration*

---

## ✅ 8. Test Database Structure

Verify these tables exist locally:
- [ ] `users`
- [ ] `customers`
- [ ] `customer_auth`
- [ ] `products`
- [ ] `product_images`
- [ ] `product_specs`
- [ ] `orders`
- [ ] `order_items`
- [ ] `cart`
- [ ] `wishlist`
- [ ] `customer_addresses`
- [ ] `alerts`
- [ ] `inventory_transactions`
- [ ] `ratings` (if using rating system)
- [ ] `settings`
- [ ] `migrations`

---

## ✅ 9. Prepare Deployment Package

### Option A: Full Upload (Recommended)
- [ ] Delete `vendor/` folder (will reinstall on server)
- [ ] Create ZIP archive: `techtrack_deployment.zip`
- [ ] Verify ZIP size (should be under 100MB without vendor)

### Option B: FTP Upload
- [ ] FileZilla or FTP client installed
- [ ] Hostinger FTP credentials ready
- [ ] Files organized and ready to upload

---

## ✅ 10. External Services Configuration

### Google Cloud Console:
- [ ] Project created
- [ ] OAuth consent screen configured
- [ ] OAuth 2.0 credentials created
- [ ] Test redirect URI noted (will update after deployment)

### PayMongo Dashboard:
- [ ] Account verified
- [ ] Business information complete
- [ ] Live API keys generated
- [ ] Test mode vs Live mode understood

---

## ✅ 11. Documentation Review

- [ ] Read HOSTINGER_DEPLOYMENT_GUIDE.md fully
- [ ] Understand each deployment phase
- [ ] Know where to find troubleshooting section
- [ ] Have Hostinger support info handy

---

## ✅ 12. Backup Current Live Site (If Updating)

- [ ] Backup current production database
- [ ] Download current production files
- [ ] Document current configuration
- [ ] Plan rollback procedure if needed

---

## ✅ 13. Time Allocation

Estimated deployment time: **2-4 hours**

- [ ] Schedule uninterrupted time block
- [ ] Avoid peak business hours (if updating live site)
- [ ] Have contingency time for troubleshooting
- [ ] Inform stakeholders of deployment window

---

## ✅ 14. Emergency Contacts

- [ ] Hostinger support: 24/7 Live Chat
- [ ] Domain registrar support info
- [ ] Database administrator contact (if applicable)
- [ ] Developer/technical support contact

---

## ✅ 15. Final Checks

- [ ] All dependencies installed locally (`composer install`)
- [ ] No error messages in local environment
- [ ] All migrations run successfully
- [ ] `.htaccess` file exists and configured
- [ ] `public/uploads/` directory has content
- [ ] Error logs are clear
- [ ] Browser cache cleared for fresh testing

---

## 🎯 Ready to Deploy?

If all items above are checked, proceed to **DEPLOYMENT_CHECKLIST.md**

---

**Checklist Version**: 1.0  
**Last Updated**: November 28, 2025  
**Next Step**: DEPLOYMENT_CHECKLIST.md
