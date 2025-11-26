# ✅ TechTrack Render Deployment Checklist

## Before You Deploy

### 1. Code Changes Required ✏️
- [ ] Change `sess_driver` to `'database'` in `app/config/config.php` (line 227)
- [ ] Update `base_url` to use `getenv('BASE_URL')` in `app/config/config.php`
- [ ] Update database config to use environment variables in `app/config/database.php`

### 2. Database Setup 🗄️
- [ ] Choose external MySQL provider (Railway/PlanetScale/AWS RDS)
- [ ] Create database instance
- [ ] Import your SQL: `sql/techtrack_db.sql`
- [ ] Import sessions table: `sql/create_sessions_table.sql`
- [ ] Save connection credentials (host, user, password, database, port)

### 3. File Storage Solution 📤
- [ ] Sign up for Cloudinary (recommended) or AWS S3
- [ ] Get API credentials
- [ ] Plan to update upload code in `AdminProducts.php` (future task)

### 4. GitHub Repository 🐙
- [ ] Push all changes to GitHub
- [ ] Verify `.gitignore` is working (no vendor/ or credentials committed)
- [ ] Make sure repository is public or grant Render access

## During Deployment

### 5. Render Setup 🚀
- [ ] Sign up at https://render.com
- [ ] Create new Web Service
- [ ] Connect GitHub repository
- [ ] Set Build Command: `./build.sh` or `composer install`
- [ ] Set Start Command: `php -S 0.0.0.0:$PORT -t .`

### 6. Environment Variables 🔐
Add these in Render Dashboard → Environment:

- [ ] `BASE_URL` = `https://your-app-name.onrender.com/`
- [ ] `DB_HOST` = Your database host
- [ ] `DB_USER` = Your database username
- [ ] `DB_PASS` = Your database password
- [ ] `DB_NAME` = Your database name
- [ ] `DB_PORT` = `3306` (or your database port)
- [ ] `ENVIRONMENT` = `production`
- [ ] `GOOGLE_CLIENT_ID` = Your Google OAuth client ID
- [ ] `GOOGLE_CLIENT_SECRET` = Your Google OAuth secret
- [ ] `PAYMONGO_PUBLIC_KEY` = Your PayMongo public key
- [ ] `PAYMONGO_SECRET_KEY` = Your PayMongo secret key

### 7. Deploy! 🎉
- [ ] Click "Create Web Service"
- [ ] Wait for build to complete (5-10 minutes)
- [ ] Check logs for any errors

## After Deployment

### 8. Testing ✅
- [ ] Visit your Render URL
- [ ] Test admin login (sessions should work!)
- [ ] Test customer registration/login
- [ ] Add product to cart as guest (should persist!)
- [ ] Test Google OAuth login
- [ ] Check if product images load
- [ ] Test checkout flow

### 9. Monitor & Verify 📊
- [ ] Check Render logs for errors
- [ ] Verify sessions table has data: `SELECT COUNT(*) FROM sessions;`
- [ ] Test on different browsers
- [ ] Test on mobile devices

### 10. Known Limitations (Free Tier) ⚠️
- [ ] Understand: Service sleeps after 15 min inactivity (first load will be slow)
- [ ] Understand: 750 hours/month free (enough for small projects)
- [ ] Understand: No persistent file storage (images need Cloudinary/S3)

## Troubleshooting

### If login doesn't work:
1. Check sessions table exists
2. Verify `sess_driver = 'database'` in config
3. Check database connection in Render logs

### If database connection fails:
1. Verify all DB_* environment variables are correct
2. Test connection from another tool (MySQL Workbench)
3. Check database firewall allows Render IPs

### If images don't load:
1. Implement Cloudinary/S3 storage
2. Or upload images directly to database (temporary solution)

### If site is slow:
1. Upgrade from free tier
2. Use Cloudflare CDN
3. Optimize database queries

## Quick Links

- Render Dashboard: https://dashboard.render.com
- Render Docs: https://render.com/docs
- Railway (MySQL): https://railway.app
- PlanetScale (MySQL): https://planetscale.com
- Cloudinary: https://cloudinary.com
- AWS S3: https://aws.amazon.com/s3/

---

**Last Updated:** November 26, 2025
**Deployment Status:** Ready for deployment ✅

---

## Need Help?

Refer to `RENDER_DEPLOYMENT_GUIDE.md` for detailed step-by-step instructions.
