# 🔐 Quick Start Guide - Login System

## Getting Started

When you first load TechTrack, you'll see the **Login Page** with three role options.

## 🎯 Demo Accounts (One-Click Access)

Click "Use Demo Account" button on any tab, or manually enter:

### 👨‍💼 Admin Access
```
Email: admin@techtrack.com
Password: admin123
```
**Access Level:** Full system control
**Landing Page:** Dashboard
**Can Access:** Everything

---

### 💳 Cashier/POS Access
```
Email: cashier@techtrack.com
Password: cashier123
```
**Access Level:** Sales focused
**Landing Page:** POS System
**Can Access:** POS, Orders, Inventory (view), Products (view)

---

### 🛍️ Customer Access
```
Email: customer@email.com
Password: customer123
```
**Access Level:** Shopping only
**Landing Page:** Home/Shop
**Can Access:** Shop, Cart, Checkout, Order Tracking

---

## 📋 What Each Role Can Do

| Feature | Admin | Cashier | Customer |
|---------|-------|---------|----------|
| Dashboard Analytics | ✅ | ❌ | ❌ |
| Product Management | ✅ (CRUD) | ❌ | ❌ |
| Inventory Management | ✅ | 👁️ View Only | ❌ |
| POS System | ✅ | ✅ | ❌ |
| Orders Management | ✅ | ✅ | 👁️ Own Only |
| Customer Database | ✅ | ❌ | ❌ |
| Reports & Analytics | ✅ | ❌ | ❌ |
| System Settings | ✅ | ❌ | ❌ |
| Shopping Cart | ❌ | ❌ | ✅ |
| Product Browsing | ✅ | ✅ | ✅ |
| Checkout | ❌ | ❌ | ✅ |

## 🎨 Visual Indicators

Each role has unique color coding:

- **Admin**: 🔵 Blue (`#2563eb`)
- **Cashier**: 🟢 Green 
- **Customer**: 🟣 Purple

## 🔄 How to Switch Accounts

1. Click your **avatar/name** in the header (Customer view) or sidebar (Admin/Cashier view)
2. Click **"Logout"**
3. You'll return to the login page
4. Select a different role and login

## 💡 Tips

- **Forgot credentials?** They're displayed on the left side of the login page
- **Quick test?** Use the "Use Demo Account" button
- **Password hidden?** Click the 👁️ icon to show/hide
- **Wrong role?** The system validates role-specific credentials

## 🚀 Typical Workflows

### Morning Shift (Cashier)
1. Login as **Cashier** → Goes to POS
2. Process sales throughout the day
3. Check inventory when needed
4. Logout at end of shift

### Store Manager (Admin)
1. Login as **Admin** → Goes to Dashboard
2. Review analytics and reports
3. Manage inventory and products
4. Handle customer issues
5. Monitor all orders

### Customer
1. Login as **Customer** → Browse shop
2. Add items to cart
3. Checkout with payment
4. Track order status
5. Logout when done

## 🔧 Technical Details

**Authentication Type:** Client-side state management (Demo mode)
**Session Persistence:** No (resets on page refresh)
**Password Security:** Plain text (Demo only - NOT production ready)
**API Backend:** None (Mock data only)

## ⚠️ Important Notes

- This is a **DEMO/PROTOTYPE** system
- Credentials are **hardcoded** for testing
- **No real security** - do not use in production
- **No data persistence** - resets on refresh
- All payments are **simulated**

## 🆘 Troubleshooting

**Can't login?**
- Double-check the email/password match exactly
- Make sure you selected the correct role tab
- Try using the "Demo Account" button instead

**Not seeing expected pages?**
- Verify you logged in with the correct role
- Cashiers don't see Settings/Reports
- Customers only see shop interface

**Page refresh loses login?**
- This is expected behavior (no session persistence)
- Just login again

## 📞 Test Scenarios

Try these to explore the system:

1. **Admin Flow**: Login → View Dashboard → Manage Products → Check Reports
2. **Cashier Flow**: Login → Use POS → Process Sale → View Inventory
3. **Customer Flow**: Login → Browse Products → Add to Cart → Checkout → Track Order
4. **Cross-Role**: Login as each role to see different interfaces

---

**Need Help?** Check `/LOGIN_FEATURE_DOCUMENTATION.md` for detailed technical documentation.
