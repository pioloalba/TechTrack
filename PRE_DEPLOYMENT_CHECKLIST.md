# Pre-Deployment Checklist for Hostinger

## Before Upload

### ✅ Files Cleaned
- [ ] Deleted all test_*.php files
- [ ] Deleted setup_checker.php
- [ ] Deleted verify_*.php files
- [ ] Deleted *.bat files
- [ ] Deleted NEW_SECURE_CREDENTIALS.txt
- [ ] Deleted generate_password_hash.php
- [ ] Removed .git folder (if present)
- [ ] Kept sql/techtrack_db_complete.sql for reference

### ✅ Credentials Prepared
- [ ] Hostinger database name recorded
- [ ] Hostinger database username recorded
- [ ] Hostinger database password recorded
- [ ] Production domain URL confirmed
- [ ] Google OAuth redirect URI updated (if using)
- [ ] PayMongo LIVE keys obtained (if using)

### ✅ Configurations Ready
- [ ] app/config/database.php - ready to update
- [ ] app/config/config.php - base_url ready
- [ ] app/config/config.php - environment set to 'production'
- [ ] app/config/google_oauth.php - redirect_uri ready (if using)
- [ ] app/config/paymongo.php - live keys ready (if using)

## During Upload

### ✅ Hostinger Setup
- [ ] MySQL database created
- [ ] Database imported via phpMyAdmin
- [ ] All tables verified (16+ tables)
- [ ] Files uploaded to public_html/
- [ ] Composer dependencies installed
- [ ] Directory permissions set (755)

### ✅ Configuration Updates
- [ ] Database credentials updated
- [ ] Base URL updated
- [ ] Environment changed to 'production'
- [ ] Log threshold set to 1
- [ ] OAuth redirect URI updated
- [ ] SSL certificate installed
- [ ] HTTPS force enabled in .htaccess

## After Deployment

### ✅ Verification Tests
- [ ] Homepage loads without errors
- [ ] Admin login works (/auth)
- [ ] Customer login works (/login)
- [ ] Product pages display correctly
- [ ] Images load properly
- [ ] QR codes generate
- [ ] Cart functions work
- [ ] Checkout process completes
- [ ] PDF reports download
- [ ] No errors in runtime/logs/

### ✅ Security Checks
- [ ] Default passwords changed
- [ ] Test files deleted
- [ ] Protected directories return 403
- [ ] SSL certificate active (https://)
- [ ] CSRF protection enabled
- [ ] Error display disabled
- [ ] API keys are production versions

### ✅ Performance
- [ ] OPcache enabled (check with Hostinger)
- [ ] Caching configured
- [ ] Database tables optimized
- [ ] Images compressed
- [ ] Page load time acceptable (<3s)

## Emergency Contacts

**Hostinger Support**: 24/7 Live Chat at hpanel.hostinger.com
**Database Backup**: sql/techtrack_db_complete.sql
**Last Working Config**: Keep backup of config files

## Deployment Date: _____________

## Completed By: _____________

## Notes:
_____________________________________________
_____________________________________________
_____________________________________________
