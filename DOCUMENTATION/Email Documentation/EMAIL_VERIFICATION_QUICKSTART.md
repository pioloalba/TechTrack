# 🚀 Quick Start - Email Verification

## Mabilis na Setup (5 minutes)

### 1. Database Setup
```sql
-- Run sa phpMyAdmin o MySQL terminal
USE techtrack_db;

CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `verification_code` VARCHAR(6) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `is_used` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_code` (`verification_code`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. Gmail App Password
1. https://myaccount.google.com/security → Enable 2-Step Verification
2. https://myaccount.google.com/apppasswords → Generate App Password
3. Copy ang 16-character password

### 3. Update .env
```env
EMAIL_SERVICE=gmail
EMAIL_USER=your-email@gmail.com
EMAIL_PASS=xxxx-xxxx-xxxx-xxxx
```

### 4. Restart Backend
```bash
cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"
npm start
```

### 5. Test!
- Open registration page
- Use REAL email
- Enter code from email
- Success! 🎉

---

## What Changed?

### ✅ Added Files:
- `DOCUMENTATION/EMAIL_VERIFICATION_SETUP.md` (full guide)
- `DOCUMENTATION/EMAIL_VERIFICATION_QUICKSTART.md` (this file)

### ✅ Modified Files:
- `backend/routes/auth.js` (added 2 new endpoints)
- `backend/.env` (added email config)
- `frontend/src/pages/auth/LoginPage.vue` (added verification modal)
- `frontend/src/services/api.ts` (added 2 new API methods)

### ✅ No Changes To:
- Login functionality
- Role system
- Demo accounts
- Other features

---

## Flow Diagram

```
User Registers
     ↓
Fills Form + Click "Create Account"
     ↓
Backend sends 6-digit code to email
     ↓
Verification modal appears
     ↓
User enters code from email
     ↓
Backend validates code
     ↓
Registration completes
     ↓
Auto-login + Redirect
```

---

## Email Template

```
Subject: Email Verification Code - TechTrack

Hello [Name],

Your Verification Code: 123456

⏰ Expires in 10 minutes
🔒 Do not share this code
```

---

**Basahin ang complete guide:** `EMAIL_VERIFICATION_SETUP.md`
