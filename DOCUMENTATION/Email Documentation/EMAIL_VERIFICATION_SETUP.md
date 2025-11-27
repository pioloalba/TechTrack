# 📧 Email Verification Feature - Setup Guide

## Overview
Naidagdag na ang **Email Verification** feature sa registration process. Ang users ay kailangan mag-verify ng kanilang email address gamit ang 6-digit code na ipapadala sa kanilang email bago makumpleto ang registration.

---

## 🎯 Features Implemented

### Backend (`backend/routes/auth.js`)
- ✅ **POST /api/auth/send-verification-code** - Sends 6-digit code to email
- ✅ **POST /api/auth/verify-code** - Validates the verification code
- ✅ Code expiration (10 minutes)
- ✅ One-time use codes
- ✅ Email sending via nodemailer

### Frontend (`frontend/src/pages/auth/LoginPage.vue`)
- ✅ Email verification modal
- ✅ 6-digit code input with validation
- ✅ 10-minute countdown timer
- ✅ Resend code functionality
- ✅ Beautiful UI with animations

### API Services (`frontend/src/services/api.ts`)
- ✅ `sendVerificationCode()` - API call to send code
- ✅ `verifyCode()` - API call to verify code

---

## 🔧 Setup Instructions

### Step 1: Database Migration
Run ang SQL migration para gumawa ng `email_verifications` table:

```bash
# Navigate to backend folder
cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"

# Import the migration sa MySQL
mysql -u root -p techtrack_db < migrations/create_email_verifications_table.sql
```

O manually run sa phpMyAdmin:
1. Open phpMyAdmin
2. Select `techtrack_db` database
3. Click SQL tab
4. Copy-paste ang content ng `create_email_verifications_table.sql`
5. Click "Go"

---

### Step 2: Email Configuration

#### For Gmail Users:
1. **Enable 2-Step Verification**
   - Go to https://myaccount.google.com/security
   - Enable "2-Step Verification"

2. **Generate App Password**
   - Go to https://myaccount.google.com/apppasswords
   - Select "Mail" as app
   - Select "Windows Computer" as device
   - Click "Generate"
   - Copy ang 16-character password

3. **Update .env file** (`backend/.env`):
```env
EMAIL_SERVICE=gmail
EMAIL_USER=your-email@gmail.com
EMAIL_PASS=xxxx-xxxx-xxxx-xxxx  # Your 16-character app password
```

#### For Other Email Providers:
```env
# Outlook/Hotmail
EMAIL_SERVICE=hotmail
EMAIL_USER=your-email@outlook.com
EMAIL_PASS=your-password

# Yahoo
EMAIL_SERVICE=yahoo
EMAIL_USER=your-email@yahoo.com
EMAIL_PASS=your-password

# Custom SMTP
EMAIL_HOST=smtp.example.com
EMAIL_PORT=587
EMAIL_USER=your-email@example.com
EMAIL_PASS=your-password
```

---

### Step 3: Install Dependencies (if needed)
```bash
cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"
npm install nodemailer
```

---

### Step 4: Restart Backend Server
```bash
cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"
npm start
```

---

## 🎬 How It Works

### Registration Flow:
1. **User fills registration form** → Clicks "Create Account"
2. **System sends verification code** → 6-digit code sent to email
3. **Verification modal appears** → User enters the code
4. **System validates code** → If valid, completes registration
5. **Auto-login** → Redirects to appropriate dashboard

### Security Features:
- ✅ Codes expire after 10 minutes
- ✅ One-time use only (marked as used after verification)
- ✅ Email must be real and accessible
- ✅ Timer countdown displayed to user
- ✅ Resend code option available

---

## 🧪 Testing the Feature

### Test Registration with Email Verification:

1. **Start Backend Server**
   ```bash
   cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"
   npm start
   ```

2. **Start Frontend Server**
   ```bash
   cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\frontend"
   npm run dev
   ```

3. **Open Browser**
   - Go to login page
   - Click "Register" tab
   - Fill in registration form with REAL email address
   - Click "Create Account"

4. **Check Email**
   - Open your email inbox
   - Look for "Email Verification Code - TechTrack"
   - Copy the 6-digit code

5. **Enter Verification Code**
   - Modal will appear
   - Enter the 6-digit code
   - Click "Verify & Create Account"

6. **Success!**
   - Account created
   - Auto-logged in
   - Redirected to dashboard

---

## 🐛 Troubleshooting

### Email Not Sending?

**Check 1: Email Configuration**
```bash
# Verify .env has correct settings
cd "c:\wamp64\www\TECHTRACK_EVENT\VUE FINAL PROJECT\backend"
cat .env | Select-String EMAIL
```

**Check 2: Backend Logs**
- Look for email sending errors in terminal
- Common errors:
  - "Invalid login" → Wrong app password
  - "Connection refused" → Gmail 2FA not enabled
  - "Email service not configured" → Missing .env variables

**Check 3: Gmail App Password**
- Make sure you used App Password, NOT your regular password
- App password should be 16 characters (4 groups of 4)

### Verification Code Expired?
- Code valid for 10 minutes only
- Click "Resend Code" to get new code
- New code will be sent immediately

### Modal Not Showing?
- Check browser console for errors
- Make sure frontend is connected to backend
- Verify backend is running on port 3000

---

## 📁 Files Modified

### Backend:
- ✅ `backend/routes/auth.js` - Added verification endpoints
- ✅ `backend/services/emailService.js` - Already existed
- ✅ `backend/.env` - Added email configuration
- ✅ `backend/migrations/create_email_verifications_table.sql` - Already existed

### Frontend:
- ✅ `frontend/src/pages/auth/LoginPage.vue` - Added verification UI & logic
- ✅ `frontend/src/services/api.ts` - Added verification API calls

---

## 🔒 Security Notes

1. **Environment Variables**
   - Never commit `.env` file to git
   - Keep email password secure
   - Use app-specific passwords

2. **Code Validation**
   - Codes expire after 10 minutes
   - One-time use only
   - Properly validated on backend

3. **Email Verification**
   - Prevents fake registrations
   - Ensures valid email addresses
   - Reduces spam accounts

---

## ✅ What's Protected

Ang existing features ay **HINDI naaapektuhan**:
- ✅ Login functionality (customer/cashier/admin)
- ✅ Role-based access control
- ✅ Demo accounts
- ✅ Password validation
- ✅ Session management
- ✅ All other features (orders, products, wishlist, etc.)

Ang bagong verification ay **ONLY sa registration** - hindi kasama sa login!

---

## 🎨 UI Features

- Beautiful verification modal with gradient design
- Real-time countdown timer (10 minutes)
- 6-digit code input with auto-formatting
- Loading states for better UX
- Error messages with helpful instructions
- Resend code functionality
- Responsive design
- Smooth animations

---

## 📞 Support

Kung may problema:
1. Check ang .env email configuration
2. Verify database migration completed
3. Check backend terminal for errors
4. Test with real email address
5. Check spam/junk folder for verification email

---

**Created by:** GitHub Copilot 🤖
**Date:** November 27, 2025
**Feature:** Email Verification for Registration
