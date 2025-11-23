# PayMongo Payment Integration - Setup Guide

## Overview
TechTrack now supports PayMongo payment gateway for accepting:
- **Credit/Debit Cards** (Visa, Mastercard, etc.)
- **GCash**
- **GrabPay**
- **PayMaya**

---

## Step 1: Create PayMongo Account

1. **Go to**: https://dashboard.paymongo.com/signup
2. **Sign up** with your business email
3. **Verify your email** address
4. **Complete business verification** (for live payments)

---

## Step 2: Get API Keys

1. **Log in** to PayMongo Dashboard
2. **Go to**: Developers → API Keys
3. You'll see two sets of keys:

### Test Keys (for development):
- **Public Key**: `pk_test_xxxxxxxxxx`
- **Secret Key**: `sk_test_xxxxxxxxxx`

### Live Keys (for production):
- **Public Key**: `pk_live_xxxxxxxxxx` 
- **Secret Key**: `sk_live_xxxxxxxxxx`

4. **Copy both test keys** for now

---

## Step 3: Configure TechTrack

1. **Open**: `c:\wamp64\www\techtrack1.3\app\config\paymongo.php`

2. **Update the configuration**:
```php
$config['paymongo'] = [
    'test_mode' => true,  // Set to false for production
    
    // Paste your test keys here
    'test_public_key' => 'pk_test_YOUR_KEY_HERE',
    'test_secret_key' => 'sk_test_YOUR_KEY_HERE',
    
    // Later, add live keys for production
    'live_public_key' => 'pk_live_YOUR_KEY_HERE',
    'live_secret_key' => 'sk_live_YOUR_KEY_HERE',
    
    // ...rest of config
];
```

3. **Save the file**

---

## Step 4: Set Up Webhooks (Important!)

Webhooks notify your site when payments are completed.

1. **Go to**: PayMongo Dashboard → Developers → Webhooks
2. **Click**: "Create Webhook"
3. **Webhook URL**: `http://localhost:8080/techtrack1.3/payment/webhook`
   - For production, use: `https://yourdomain.com/techtrack/payment/webhook`
4. **Events to listen**:
   - ✅ `payment.paid`
   - ✅ `payment.failed`
   - ✅ `source.chargeable`
5. **Click**: "Create"
6. **Copy the Webhook Secret** (looks like: `whsec_xxxxx`)
7. **Add to config**:
```php
'webhook_secret' => 'whsec_YOUR_WEBHOOK_SECRET_HERE',
```

---

## Step 5: Test Payment Integration

### Test Cards (Use these for testing):

#### Successful Payment:
- **Card Number**: `4343434343434345`
- **Expiry**: Any future date (e.g., `12/25`)
- **CVC**: Any 3 digits (e.g., `123`)

#### Failed Payment:
- **Card Number**: `4571736000000075`
- **Expiry**: Any future date
- **CVC**: Any 3 digits

### Test GCash:
1. Select GCash as payment method
2. Click checkout
3. You'll be redirected to PayMongo's test page
4. Click "Authorize Test Payment"

---

## Step 6: Testing Flow

1. **Go to**: http://localhost:8080/techtrack1.3/shop
2. **Add products** to cart
3. **Go to checkout**
4. **Select payment method**:
   - **For Cards**: Enter test card details
   - **For GCash/GrabPay**: You'll be redirected
5. **Complete payment**
6. **Verify**:
   - Order status updated to "processing"
   - Payment status shows "paid"
   - Check PayMongo Dashboard → Payments

---

## Payment Methods

### 1. Credit/Debit Card
- **Flow**: Customer enters card → 3D Secure → Payment complete
- **Fees**: 3.5% + ₱15 per transaction

### 2. GCash
- **Flow**: Redirect to GCash → Customer authorizes → Redirect back
- **Fees**: 2.5% per transaction
- **Min**: ₱100, **Max**: ₱50,000 per transaction

### 3. GrabPay
- **Flow**: Redirect to GrabPay → Customer authorizes → Redirect back
- **Fees**: 2.5% per transaction

### 4. PayMaya
- **Flow**: Redirect to PayMaya → Customer authorizes → Redirect back
- **Fees**: 2.5% per transaction

---

## Production Deployment

When ready to accept real payments:

1. **Complete Business Verification** in PayMongo Dashboard
2. **Update config** to use live keys:
```php
'test_mode' => false,
```

3. **Update webhook URL** to production domain:
```
https://yourdomain.com/techtrack/payment/webhook
```

4. **Update success/cancel URLs** in config:
```php
'success_url' => 'https://yourdomain.com/techtrack/payment/success',
'cancel_url' => 'https://yourdomain.com/techtrack/payment/cancel',
```

5. **Test thoroughly** with real small amounts first

---

## Database Schema

Added columns to `orders` table:
```sql
payment_intent_id VARCHAR(255) NULL
payment_source_id VARCHAR(255) NULL
```

Run: `php add_paymongo_columns.php` (already executed)

---

## Security Notes

- ✅ Never commit API keys to Git
- ✅ Use environment variables in production
- ✅ Always verify webhook signatures
- ✅ Use HTTPS in production
- ✅ Keep `test_mode = true` until verified

---

## Troubleshooting

### Error: "Invalid API Key"
- Check if you copied the full key
- Verify you're using test keys in test mode
- Check for extra spaces

### Webhook not working
- Verify webhook URL is publicly accessible
- For localhost, use ngrok: `ngrok http 8080`
- Check webhook secret matches

### Payment not completing
- Check browser console for errors
- Verify order exists in database
- Check PayMongo Dashboard → Logs

### GCash redirect not working
- Verify return URLs in config
- Check if amount is within limits (₱100 - ₱50,000)

---

## Support

- **PayMongo Docs**: https://developers.paymongo.com/docs
- **PayMongo Support**: support@paymongo.com
- **Test Mode**: No real money charged

---

## Features Implemented

✅ Credit/Debit card payments with 3D Secure
✅ GCash integration
✅ GrabPay integration
✅ PayMaya integration
✅ Webhook handling for payment notifications
✅ Automatic order status updates
✅ Payment tracking in database
✅ Test mode for safe development
✅ Secure API communication

---

## Next Steps

1. Get your PayMongo API keys
2. Update `app/config/paymongo.php`
3. Test with test cards
4. Set up webhooks
5. Go live when ready!
