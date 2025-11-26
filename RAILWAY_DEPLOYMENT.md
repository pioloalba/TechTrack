# 🚂 Railway Deployment Guide for TechTrack

## Quick Start

### 1. Deploy to Railway

**Option A: Using Railway CLI (Fastest)**

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login to Railway
railway login

# Initialize project
railway init

# Add MySQL database
railway add

# Deploy
railway up
```

**Option B: Using Railway Dashboard (Easiest)**

1. Go to https://railway.app
2. Sign up with GitHub
3. Click "New Project"
4. Select "Deploy from GitHub repo"
5. Choose your TechTrack repository
6. Railway will auto-detect PHP and deploy!

---

### 2. Add MySQL Database

After your app is deployed:

1. In Railway dashboard, click **"+ New"**
2. Select **"Database" → "MySQL"**
3. Wait for database to provision (~30 seconds)
4. Railway automatically connects your app to MySQL!

**Environment variables are auto-injected:**
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

---

### 3. Import Your Database

**Option A: Using Railway CLI**

```bash
# Connect to MySQL
railway connect mysql

# In MySQL prompt:
SOURCE /path/to/your/sql/techtrack_db.sql;
exit;
```

**Option B: Using MySQL Client**

1. Get connection details from Railway dashboard
2. Use MySQL Workbench or phpMyAdmin
3. Import `sql/techtrack_db.sql`

**Option C: Using Railway Data Tab**

1. Go to your MySQL service
2. Click "Data" tab
3. Click "Import"
4. Upload `techtrack_db.sql`

---

### 4. Configure Environment Variables (Optional)

Railway auto-configures most things, but you can add custom variables:

1. Go to your service → **Variables** tab
2. Add these (optional):

```
ENVIRONMENT=production
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
PAYMONGO_PUBLIC_KEY=your-paymongo-public-key
PAYMONGO_SECRET_KEY=your-paymongo-secret-key
```

---

### 5. Get Your Public URL

1. Go to your service → **Settings** tab
2. Scroll to **Domains**
3. Click **Generate Domain**
4. Your app will be available at: `https://your-app.up.railway.app`

---

## 🎉 That's It!

Your TechTrack system is now live on Railway!

**What Railway provides:**
- ✅ PHP 8.2 environment
- ✅ MySQL database (500MB free)
- ✅ Persistent file storage
- ✅ Automatic HTTPS
- ✅ Auto-deployment on git push
- ✅ $5/month free credit
- ✅ No sleep/cold starts

---

## 📊 Free Tier Limits

- **$5 credit per month** (usually covers everything)
- **500 execution hours** (~20 days of 24/7 uptime)
- **100GB outbound bandwidth**
- **500MB database storage**

**Perfect for:**
- Development
- School projects
- MVPs
- Small businesses
- Demos/portfolios

---

## 🔄 Auto-Deploy on Push

Once connected to GitHub:

```bash
# Make changes locally
git add .
git commit -m "Update feature"
git push origin main

# Railway automatically deploys! 🚀
```

---

## 🐛 Troubleshooting

### Build fails?
Check `nixpacks.toml` is committed to repo

### Database connection error?
Railway auto-injects variables, just redeploy

### Files not uploading?
Railway has persistent storage, unlike Render!

### Need logs?
Click your service → "Deployments" → View logs

---

## 📞 Support

- Railway Docs: https://docs.railway.app
- Discord: https://discord.gg/railway
- Twitter: @Railway

---

**Cost:** FREE for development (includes MySQL!)
**Setup Time:** 10 minutes
**Difficulty:** Easy ⭐⭐⭐⭐⭐
