# 🚀 TechTrack - How to Access Your Project

## ⚠️ IMPORTANT: Folder Name Has Spaces

Your project folder is: `TECH TRACK LAVALUST 1.2` (with spaces)

This causes URL issues. Here are your options:

---

## ✅ METHOD 1: Use Correct URL (Recommended)

### Access URLs:

**Main Page:**
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/
```

**Shop Homepage:**
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop
```

**Admin Login:**
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/auth/admin_login
```

**Note:** `%20` represents spaces in URLs

---

## ✅ METHOD 2: Use WAMP Projects List (Easiest)

1. Go to: `http://localhost`
2. Find **"Your Projects"** section
3. Click on **"TECH TRACK LAVALUST 1.2"**
4. WAMP will automatically handle the URL

---

## ✅ METHOD 3: Rename Folder (Best Long-term)

Rename your project folder to remove spaces:

### Steps:

1. **Stop WAMP Server:**
   - Right-click WAMP icon → Stop All Services

2. **Rename Folder:**
   ```
   From: C:\wamp64\www\TECH TRACK LAVALUST 1.2
   To:   C:\wamp64\www\techtrack
   ```

3. **Start WAMP Server:**
   - Right-click WAMP icon → Start All Services

4. **New URLs (much cleaner):**
   ```
   http://localhost/techtrack/
   http://localhost/techtrack/index.php/shop
   http://localhost/techtrack/index.php/auth/admin_login
   ```

---

## ✅ METHOD 4: Create Virtual Host (Professional)

This gives you a custom domain like: `http://techtrack.local`

### Steps:

1. **Open WAMP Menu:**
   - WAMP Icon → Tools → Add a Virtual Host

2. **Fill Form:**
   - Name: `techtrack.local`
   - Path: `C:\wamp64\www\TECH TRACK LAVALUST 1.2`

3. **Access:**
   ```
   http://techtrack.local/
   http://techtrack.local/index.php/shop
   http://techtrack.local/index.php/auth/admin_login
   ```

---

## 🎯 Quick Test:

### Try This Now:

**Copy and paste this URL in your browser:**
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop
```

**Or click this from WAMP dashboard:**
- Go to: `http://localhost`
- Click: "TECH TRACK LAVALUST 1.2" under "Your Projects"

---

## 📋 All Your URLs:

| Page | URL |
|------|-----|
| Home | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/` |
| Shop | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop` |
| Admin Login | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/auth/admin_login` |
| Cashier Login | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/auth/cashier_login` |
| Admin Dashboard | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/admin/dashboard` |
| Admin Products | `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/admin/products` |

---

## 💡 Why Port 8080 Didn't Work:

You were trying:
```
http://localhost:8080/index.php/shop
```

This went to WAMP's default page on port 8080, not your project.

**Correct format:**
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop
```

Or just use port 80 (default):
```
http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop
```

---

## 🔥 My Recommendation:

**Rename the folder to remove spaces!**

This will make your life much easier:

```powershell
# Stop WAMP first, then in PowerShell:
cd C:\wamp64\www
Rename-Item "TECH TRACK LAVALUST 1.2" -NewName "techtrack"
# Start WAMP again
```

**Then use clean URLs:**
```
http://localhost/techtrack/index.php/shop
```

Much better! 🎉

---

## ✅ Quick Access Bookmarks:

Save these in your browser:

1. **Shop:** `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/shop`
2. **Admin:** `http://localhost/TECH%20TRACK%20LAVALUST%201.2/index.php/auth/admin_login`
3. **WAMP:** `http://localhost`

---

**Try the URLs above and let me know which method works for you!** 🚀
