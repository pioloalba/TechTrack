# 🚀 TechTrack Render Deployment Guide

## 📋 Prerequisites

- GitHub account with your TechTrack repository
- Render account (https://render.com - free tier available)
- External MySQL database (recommended options below)

---

## ⚠️ CRITICAL: Why You Need Changes

**Render uses ephemeral filesystem** - files are reset on every deployment. This means:
- ❌ File-based sessions will be lost → users logged out randomly
- ❌ Uploaded images will disappear → product images gone after restart
- ❌ Cache files will be cleared → performance issues

**Solutions implemented:**
✅ Database sessions (persistent across restarts)
✅ Environment variables for configuration
✅ External storage recommendations

---

## 🗄️ STEP 1: Setup External MySQL Database

### Option A: Railway (Recommended - Easy)
1. Go to https://railway.app
2. Create new project → MySQL
3. Get connection details:
   - Host: `containers-us-west-xxx.railway.app`
   - Port: `6379`
   - Database: `railway`
   - Username: `root`
   - Password: `xxxxx`

### Option B: PlanetScale (Free tier)
1. Go to https://planetscale.com
2. Create database → Get connection string
3. Note: Uses different port (3306)

### Option C: AWS RDS (Production grade)
1. Create RDS MySQL instance
2. Configure security groups
3. Get endpoint URL

---

## 🔧 STEP 2: Prepare Your Code for Deployment

### A. Switch to Database Sessions

Edit `app/config/config.php` line 227:

```php
// Change from:
$config['sess_driver'] = 'file';

// To:
$config['sess_driver'] = 'database';
```

### B. Update Base URL to Use Environment Variable

Edit `app/config/config.php` line 79:

```php
// Change from:
$config['base_url'] = 'http://localhost:8080/techtrack1.3/';

// To:
$config['base_url'] = getenv('BASE_URL') ?: 'http://localhost:8080/techtrack1.3/';
```

### C. Update Database Config to Use Environment Variables

Edit `app/config/database.php`:

```php
$db['hostname'] = getenv('DB_HOST') ?: 'localhost';
$db['username'] = getenv('DB_USER') ?: 'root';
$db['password'] = getenv('DB_PASS') ?: '';
$db['database'] = getenv('DB_NAME') ?: 'techtrack_db';
$db['port']     = getenv('DB_PORT') ?: 3306;
```

### D. Import Sessions Table

Run this SQL on your external database:

```sql
-- Import all your existing tables first
SOURCE sql/techtrack_db.sql;

-- Then add sessions table
SOURCE sql/create_sessions_table.sql;
```

Or manually:
```sql
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(128) NOT NULL PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` TEXT NOT NULL,
  INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🌐 STEP 3: Deploy to Render

### A. Create Web Service

1. Go to https://dashboard.render.com
2. Click **New +** → **Web Service**
3. Connect your GitHub repository
4. Configure:
   - **Name:** `techtrack-app`
   - **Environment:** `PHP`
   - **Build Command:** `composer install`
   - **Start Command:** `php -S 0.0.0.0:$PORT -t .`
   - **Instance Type:** Free (or paid for better performance)

### B. Add Environment Variables

In Render dashboard, go to **Environment** and add:

```
BASE_URL=https://techtrack-app.onrender.com/
DB_HOST=your-railway-or-planetscale-host
DB_USER=your-database-username
DB_PASS=your-database-password
DB_NAME=your-database-name
DB_PORT=3306
ENVIRONMENT=production
```

### C. Deploy

Click **Create Web Service** - Render will:
1. Clone your repository
2. Run `composer install`
3. Start PHP server
4. Assign public URL

---

## 📤 STEP 4: Handle File Uploads (IMPORTANT!)

**Problem:** Product images uploaded will disappear on restart.

### Solution A: Use Cloudinary (Recommended)

1. Sign up at https://cloudinary.com (free tier: 25GB)
2. Get credentials:
   - Cloud name
   - API Key
   - API Secret
3. Install Cloudinary SDK:
   ```bash
   composer require cloudinary/cloudinary_php
   ```
4. Update upload code in `AdminProducts.php` to use Cloudinary API

### Solution B: Use AWS S3

1. Create S3 bucket
2. Install AWS SDK:
   ```bash
   composer require aws/aws-sdk-php
   ```
3. Update upload logic to store in S3

### Solution C: Store in Database (Not recommended)

Convert images to Base64 and store in database (increases DB size significantly)

---

## 🔐 STEP 5: Secure Your Deployment

### A. Update Cookie Settings for HTTPS

Edit `app/config/config.php`:

```php
$config['cookie_secure'] = getenv('ENVIRONMENT') === 'production' ? TRUE : FALSE;
```

### B. Add .gitignore

Create `.gitignore` in root:

```
/vendor/
/runtime/cache/*
/runtime/logs/*
!/runtime/cache/.gitkeep
!/runtime/logs/.gitkeep
.env
.DS_Store
```

### C. Never Commit Sensitive Data

- Database credentials should be in environment variables only
- API keys (Google, PayMongo) should be in Render environment settings

---

## ✅ STEP 6: Test Your Deployment

1. **Visit your Render URL:** `https://techtrack-app.onrender.com/`

2. **Test Authentication:**
   - Admin login should work
   - Customer registration/login should work
   - Sessions should persist across page refreshes

3. **Test Guest Features:**
   - Add to cart without login
   - Cart should persist (database sessions working!)

4. **Test Payment:**
   - Ensure PayMongo keys are in environment variables
   - Test checkout flow

5. **Check Session Table:**
   ```sql
   SELECT COUNT(*) FROM sessions;
   -- Should show active sessions
   ```

---

## 🐛 Common Issues & Fixes

### Issue 1: "Session write failed"
**Fix:** Ensure sessions table exists and database connection works.
```bash
# Check Render logs
tail -f /var/log/php-errors.log
```

### Issue 2: "Base URL incorrect"
**Fix:** Verify `BASE_URL` environment variable in Render dashboard.

### Issue 3: "Database connection error"
**Fix:** Double-check all DB environment variables match your external database.

### Issue 4: "Composer dependencies missing"
**Fix:** Ensure build command is `composer install` (not `composer update`).

### Issue 5: Images not loading
**Fix:** Implement Cloudinary or S3 storage (file uploads won't persist on Render).

### Issue 6: Cold start (free tier)
**Fix:** Render free tier spins down after 15 min inactivity. Upgrade to paid or use uptime monitors.

---

## 📊 Performance Optimization for Production

### 1. Enable Caching
Edit `app/config/config.php`:
```php
$config['ENVIRONMENT'] = 'production'; // Disables debug errors
```

### 2. Use Connection Pooling
Configure database with persistent connections.

### 3. Add CDN for Static Assets
Use Cloudflare or Render's CDN for CSS/JS/images.

### 4. Optimize Database
Add indexes on frequently queried columns:
```sql
CREATE INDEX idx_customer_email ON customers(email);
CREATE INDEX idx_product_sku ON products(sku);
CREATE INDEX idx_order_status ON orders(status);
```

---

## 🎯 Quick Checklist Before Deploy

- [ ] Changed session driver to `database` in config.php
- [ ] Created sessions table in external MySQL
- [ ] Updated config files to use environment variables
- [ ] Set up external MySQL database (Railway/PlanetScale/RDS)
- [ ] Added all environment variables in Render dashboard
- [ ] Tested locally with database sessions first
- [ ] Committed and pushed changes to GitHub
- [ ] Implemented file upload solution (Cloudinary/S3)
- [ ] Updated `.gitignore` to exclude sensitive files
- [ ] Verified all credentials are in environment variables (not hardcoded)

---

## 🆘 Getting Help

If deployment fails:
1. Check Render logs: Dashboard → Logs tab
2. Check database connectivity
3. Verify all environment variables are set correctly
4. Test locally first with `sess_driver = 'database'`

---

## 📞 Support Resources

- Render Docs: https://render.com/docs
- Railway Docs: https://docs.railway.app
- Cloudinary Docs: https://cloudinary.com/documentation

---

**Good luck with your deployment! 🚀**
