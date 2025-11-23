# Google OAuth Setup Guide for TechTrack

## Overview
This guide will help you set up Google OAuth 2.0 authentication for the TechTrack e-commerce platform, allowing customers to sign in using their Google accounts.

## Prerequisites
- Google account
- TechTrack project running locally (http://localhost:8080/techtrack1.3)

---

## Step 1: Create a Google Cloud Project

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create New Project**
   - Click the project dropdown at the top
   - Click "New Project"
   - Enter project name: `TechTrack` (or your preferred name)
   - Click "Create"
   - Wait for project creation to complete

3. **Select Your Project**
   - Click the project dropdown again
   - Select your newly created project

---

## Step 2: Enable Google+ API

1. **Navigate to APIs & Services**
   - Click the hamburger menu (☰) in the top-left
   - Select "APIs & Services" → "Library"

2. **Enable Google+ API**
   - Search for "Google+ API" in the search bar
   - Click on "Google+ API"
   - Click "Enable" button
   - Wait for activation to complete

---

## Step 3: Configure OAuth Consent Screen

1. **Go to OAuth Consent Screen**
   - In the left sidebar, click "OAuth consent screen"

2. **Choose User Type**
   - Select "External" (for testing with any Google account)
   - Click "Create"

3. **Fill App Information**
   - **App name**: TechTrack
   - **User support email**: Your email address
   - **App logo**: (Optional) Upload your logo
   - **Application home page**: http://localhost:8080/techtrack1.3
   - **Developer contact email**: Your email address
   - Click "Save and Continue"

4. **Scopes**
   - Click "Add or Remove Scopes"
   - Select these scopes:
     - `.../auth/userinfo.email`
     - `.../auth/userinfo.profile`
   - Click "Update"
   - Click "Save and Continue"

5. **Test Users** (For development)
   - Click "Add Users"
   - Add your test Gmail accounts
   - Click "Save and Continue"

6. **Summary**
   - Review your settings
   - Click "Back to Dashboard"

---

## Step 4: Create OAuth 2.0 Credentials

1. **Go to Credentials**
   - In the left sidebar, click "Credentials"

2. **Create OAuth Client ID**
   - Click "Create Credentials" → "OAuth client ID"
   - **Application type**: Select "Web application"
   - **Name**: TechTrack Web Client

3. **Configure Authorized Redirect URIs**
   - Under "Authorized redirect URIs", click "Add URI"
   - Enter: `http://localhost:8080/techtrack1.3/googleauth/callback`
   - Click "Create"

4. **Save Your Credentials**
   - A dialog will appear with your credentials
   - **Copy the Client ID** (looks like: `123456789-abc123.apps.googleusercontent.com`)
   - **Copy the Client Secret** (looks like: `GOCSPX-abc123xyz789`)
   - Click "OK"

   > **Important**: Keep these credentials secure! Do not commit them to Git.

---

## Step 5: Configure TechTrack

1. **Edit Google OAuth Config File**
   - Open: `c:\wamp64\www\techtrack1.3\app\config\google_oauth.php`

2. **Update Credentials**
   ```php
   $config['google_oauth'] = [
       'client_id'     => 'YOUR_CLIENT_ID_HERE',      // Paste your Client ID
       'client_secret' => 'YOUR_CLIENT_SECRET_HERE',   // Paste your Client Secret
       'redirect_uri'  => 'http://localhost:8080/techtrack1.3/googleauth/callback',
       'scopes'        => [
           'email',
           'profile'
       ],
   ];
   ```

3. **Save the file**

---

## Step 6: Test Google OAuth Login

1. **Start your local server**
   - Make sure WAMP is running
   - Visit: http://localhost:8080/techtrack1.3

2. **Test Login Flow**
   - Go to: http://localhost:8080/techtrack1.3/shop/login
   - Click "Sign in with Google" button
   - You should be redirected to Google's consent screen
   - Select your Google account
   - Grant permissions
   - You should be redirected back and logged in

3. **Verify Customer Record**
   - Check the `customers` table in phpMyAdmin
   - New OAuth users should have a `google_id` value

---

## Step 7: Production Setup (When Deploying)

When you're ready to deploy to production:

1. **Update Redirect URI in Google Cloud Console**
   - Go back to your OAuth Client ID settings
   - Add production redirect URI: `https://yourdomain.com/techtrack/googleauth/callback`
   - Keep localhost URI for local testing

2. **Update TechTrack Config**
   - In `app/config/google_oauth.php`, change:
   ```php
   'redirect_uri' => 'https://yourdomain.com/techtrack/googleauth/callback',
   ```

3. **Verify OAuth Consent Screen**
   - If using "External" user type, you need Google verification for public use
   - Or switch to "Internal" if using Google Workspace

---

## Troubleshooting

### Error: "redirect_uri_mismatch"
- **Cause**: The redirect URI in your config doesn't match Google Console
- **Solution**: 
  - Check exact URL in `app/config/google_oauth.php`
  - Make sure it matches exactly in Google Console (including trailing slashes)
  - Wait 5 minutes after updating for changes to propagate

### Error: "invalid_client"
- **Cause**: Wrong Client ID or Client Secret
- **Solution**: Double-check credentials in `google_oauth.php`

### Error: "access_denied"
- **Cause**: User cancelled authentication or app not verified
- **Solution**: 
  - Make sure you added yourself as a test user
  - Check OAuth consent screen settings

### Error: "This app isn't verified"
- **Cause**: App in development mode with external users
- **Solution**: Click "Advanced" → "Go to TechTrack (unsafe)" for testing

### Error: Class 'Google_Client' not found
- **Cause**: Composer dependencies not installed
- **Solution**: 
  ```bash
  cd c:\wamp64\www\techtrack1.3
  composer install
  ```

### Database column missing
- **Cause**: `google_id` column not added to customers table
- **Solution**: 
  ```bash
  php add_google_id_column.php
  ```

---

## Features Implemented

✅ Google OAuth 2.0 authentication
✅ Automatic customer account creation
✅ Link existing accounts with Google ID
✅ Guest cart/wishlist migration on OAuth login
✅ Secure state token validation (CSRF protection)
✅ Error handling and user feedback

---

## Security Notes

- Never commit `google_oauth.php` with real credentials to Git
- Use environment variables for production credentials
- Keep Client Secret secure
- Rotate credentials if compromised
- Use HTTPS in production

---

## Database Schema

The `customers` table includes a `google_id` column:
```sql
google_id VARCHAR(255) NULL UNIQUE
```

This stores the Google user ID and allows:
- Linking Google accounts to customer profiles
- Preventing duplicate accounts
- OAuth-only accounts (no password required)

---

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Google OAuth 2.0 documentation: https://developers.google.com/identity/protocols/oauth2
3. Check browser console for JavaScript errors
4. Review PHP error logs in `runtime/logs/`

---

## Next Steps

- [ ] Implement Facebook OAuth (similar process)
- [ ] Add "Disconnect Google Account" feature in profile settings
- [ ] Add OAuth provider badge in customer profile
- [ ] Consider adding more providers (Microsoft, Apple, etc.)
