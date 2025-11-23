# TechTrack Setup Guide (For Team Members)

## Quick Setup Steps

### 1. Clone the Repository
```bash
git clone https://github.com/pioloalba/TechTrack.git
cd TechTrack
git checkout dev-v4
```

### 2. Database Setup

#### Option A: Import Complete Database (RECOMMENDED)
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE techtrack_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import SQL file
mysql -u root -p techtrack_db < sql/techtrack_db.sql

# Run additional migrations for new tables
# Open in browser: http://localhost:8080/techtrack1.3/index.php
# Then navigate to: http://localhost:8080/techtrack1.3/DevMigrate/migrate
```

#### Option B: Run Migration Scripts (If SQL file not available)
```bash
# In the project directory, run these PHP scripts:
php create_customer_auth_table.php
php run_db_migration.php
php migrate_customers.php
php set_default_passwords.php
```

### 3. Update Database Configuration
Edit `app/config/database.php`:
```php
'hostname' => 'localhost',
'username' => 'root',        // Your MySQL username
'password' => '',            // Your MySQL password
'database' => 'techtrack_db',
'port'     => 3306,
```

### 4. Verify Setup
```bash
php setup_checker.php
```

### 5. Test Login Credentials

**Admin Login:**
- URL: `http://localhost:8080/techtrack1.3/index.php`
- Email: `admin@techtrack.com`
- Password: `admin123`

**Customer Login:**
- URL: `http://localhost:8080/techtrack1.3/shop`
- Email: `Borenik12345@gmail.com`
- Password: (ask team for password)

## Common Issues & Solutions

### Issue 1: "Buttons not working" / "Functions not responding"

**Cause:** Missing database tables (cart, wishlist, customer_auth)

**Solution:**
```bash
# Run these migration scripts in order:
php create_customer_auth_table.php
php run_db_migration.php
```

Or visit: `http://localhost/techtrack1.3/DevMigrate/migrate`

### Issue 2: "Class not found" errors

**Cause:** Missing model files

**Solution:**
- Ensure `CartModel.php` and `WishlistModel.php` exist in `app/models/`
- Pull latest changes: `git pull origin dev-v4`

### Issue 3: Customer login fails

**Cause:** customer_auth table doesn't exist or missing passwords

**Solution:**
```bash
php create_customer_auth_table.php
php migrate_customers.php
php set_default_passwords.php
```

### Issue 4: Products not showing

**Cause:** Empty products table

**Solution:**
```sql
-- Run in MySQL/phpMyAdmin
mysql -u root techtrack_db < sql/techtrack_db.sql
```

Or access: `http://localhost/techtrack1.3/DevMigrate/seed`

### Issue 5: "Cannot add to cart/wishlist"

**Cause:** Missing cart/wishlist tables

**Solution:**
```bash
php run_db_migration.php
```

This creates:
- `cart` table
- `wishlist` table  
- `customer_addresses` table

## Required Database Tables

Your database should have these tables:
- ✓ users
- ✓ customers
- ✓ customer_auth (NEW - for customer passwords)
- ✓ products
- ✓ product_images
- ✓ product_specs
- ✓ orders
- ✓ order_items
- ✓ cart (NEW - replaces session cart)
- ✓ wishlist (NEW - replaces session wishlist)
- ✓ customer_addresses (NEW)
- ✓ alerts
- ✓ inventory_transactions
- ✓ settings
- ✓ shipping_addresses

## File Structure Check

Ensure these files exist:
```
app/
├── models/
│   ├── CartModel.php (NEW)
│   ├── WishlistModel.php (NEW)
│   ├── CustomerModel.php
│   └── ProductModel.php
├── migrations/
│   ├── 004_create_cart_table.php (NEW)
│   ├── 005_create_wishlist_table.php (NEW)
│   └── 006_create_customer_addresses_table.php (NEW)
├── views/
│   └── shop/
│       ├── wishlist.php (NEW)
│       └── my_orders.php (NEW)
```

## Quick Troubleshooting Commands

```bash
# Check PHP version (needs 7.4+)
php -v

# Check MySQL is running
mysql -u root -p -e "SHOW DATABASES;"

# List all tables in techtrack_db
mysql -u root -p techtrack_db -e "SHOW TABLES;"

# Check if cart table exists
mysql -u root -p techtrack_db -e "DESCRIBE cart;"

# Check if customer_auth table exists
mysql -u root -p techtrack_db -e "DESCRIBE customer_auth;"

# Verify setup
php setup_checker.php
```

## Getting Help

If issues persist:
1. Run `php setup_checker.php` and share the output
2. Check browser console for JavaScript errors (F12)
3. Check PHP error logs in `runtime/logs/`
4. Verify you're on `dev-v4` branch: `git branch`

## Important Notes

- **New Authentication System**: Customers now use separate `customers` + `customer_auth` tables (NOT users table)
- **Database Persistence**: Cart and wishlist are now stored in database (not PHP sessions)
- **Migration Required**: Run migration scripts to create new tables
- **MySQL Engine**: customers table converted to InnoDB (was MyISAM)

## Contact

If setup fails, contact team lead for:
- Complete SQL dump file
- Environment configuration details
- Latest migration scripts
