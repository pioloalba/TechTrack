# 🔄 TechTrack Login System - Flow Diagrams

## Authentication Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    USER OPENS APP                           │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
            ┌─────────────────┐
            │  Is Logged In?  │
            └────┬──────┬─────┘
                 │ NO   │ YES
                 │      │
                 │      └──────────────────────┐
                 │                             │
                 ▼                             ▼
        ┌─────────────────┐         ┌──────────────────┐
        │  LOGIN PAGE     │         │  Check User Role │
        │  3 Role Tabs    │         └────┬──────┬──────┘
        └────┬────────────┘              │      │
             │                           │      │
             ▼                           │      │
    ┌────────────────┐                  │      │
    │ Select Role:   │                  │      │
    │ • Customer     │                  │      │
    │ • Cashier      │                  │      │
    │ • Admin        │                  │      │
    └────┬───────────┘                  │      │
         │                              │      │
         ▼                              │      │
    ┌─────────────────┐                │      │
    │ Enter Creds OR  │                │      │
    │ Use Demo Button │                │      │
    └────┬────────────┘                │      │
         │                             │      │
         ▼                             │      │
    ┌──────────────┐                  │      │
    │ Validate     │                  │      │
    │ Credentials  │                  │      │
    └────┬─────┬───┘                  │      │
         │     │                      │      │
    VALID│     │INVALID               │      │
         │     │                      │      │
         │     └────────┐             │      │
         │              ▼             │      │
         │    ┌──────────────────┐   │      │
         │    │  Show Error      │   │      │
         │    │  "Invalid login" │   │      │
         │    └──────────────────┘   │      │
         │                            │      │
         ▼                            │      │
    ┌─────────────┐                  │      │
    │ Set User    │                  │      │
    │ in State    │                  │      │
    └────┬────────┘                  │      │
         │                            │      │
         └────────────────────────────┘      │
                                             │
         ┌───────────────────────────────────┘
         │
         ▼
    ┌─────────────────────────────┐
    │     Route Based on Role     │
    └─┬──────────┬────────────┬───┘
      │          │            │
ADMIN │     CASHIER      CUSTOMER
      │          │            │
      ▼          ▼            ▼
┌──────────┐ ┌────────┐ ┌──────────┐
│Dashboard │ │  POS   │ │   Shop   │
│  Page    │ │  Page  │ │   Page   │
└──────────┘ └────────┘ └──────────┘
```

---

## Role Decision Tree

```
                    ┌─────────────┐
                    │  LOGIN AS:  │
                    └──────┬──────┘
                           │
          ┌────────────────┼────────────────┐
          │                │                │
          ▼                ▼                ▼
    ┌──────────┐    ┌──────────┐    ┌──────────┐
    │  ADMIN   │    │ CASHIER  │    │ CUSTOMER │
    └────┬─────┘    └────┬─────┘    └────┬─────┘
         │               │                │
         ▼               ▼                ▼
    Full Access    Limited Access   Shop Only
         │               │                │
         ▼               ▼                ▼
    ┌─────────┐    ┌─────────┐    ┌─────────┐
    │Dashboard│    │   POS   │    │  Home   │
    │Products │    │Products*│    │  Shop   │
    │Inventory│    │Inventory*│    │  Cart   │
    │   POS   │    │ Orders  │    │Checkout │
    │ Orders  │    │         │    │Tracking │
    │Customers│    │         │    └─────────┘
    │ Reports │    └─────────┘
    │ Alerts  │    *View Only
    │Settings │
    └─────────┘
```

---

## Component Interaction Diagram

```
┌────────────────────────────────────────────────────┐
│                    App.tsx                         │
│  ┌──────────────────────────────────────────────┐ │
│  │  State: currentUser: User | null             │ │
│  │  Functions:                                  │ │
│  │    - handleLogin(user)                       │ │
│  │    - handleLogout()                          │ │
│  └──────────┬──────────────────┬────────────────┘ │
└─────────────┼──────────────────┼──────────────────┘
              │                  │
    No User   │                  │  Has User
              ▼                  ▼
     ┌─────────────────┐  ┌──────────────┐
     │  LoginPage.tsx  │  │ Role Router  │
     │                 │  └──────┬───────┘
     │  Props:         │         │
     │   onLogin()     │         │
     │                 │         ├─Admin/Cashier──┐
     │  Functions:     │         │                │
     │   handleLogin() │         └─Customer──┐    │
     │   validateCreds │                     │    │
     │                 │                     ▼    ▼
     └─────────────────┘         ┌──────────────────────┐
                                 │  EasyPCHeader.tsx    │
                                 │                      │
                                 │  Props:              │
                                 │   user: User         │
                                 │   onLogout()         │
                                 │                      │
                                 │  Shows:              │
                                 │   - User avatar      │
                                 │   - Dropdown menu    │
                                 │   - Logout button    │
                                 └──────────────────────┘

                                 ┌──────────────────────┐
                                 │  AdminSidebar.tsx    │
                                 │                      │
                                 │  Props:              │
                                 │   user: User         │
                                 │   onLogout()         │
                                 │   currentPage        │
                                 │                      │
                                 │  Shows:              │
                                 │   - User info        │
                                 │   - Avatar           │
                                 │   - Logout button    │
                                 └──────────────────────┘
```

---

## Data Flow Diagram

```
┌──────────────────────────────────────────────────────────┐
│                   lib/mockData.ts                        │
│  ┌────────────────────────────────────────────────────┐ │
│  │  mockUsers: User[]                                 │ │
│  │    - U001: admin@techtrack.com (Admin)            │ │
│  │    - U002: cashier@techtrack.com (Cashier)        │ │
│  │    - U003: maria@techtrack.com (Cashier)          │ │
│  │    - U004: customer@email.com (Customer)          │ │
│  │    - U005: emma@email.com (Customer)              │ │
│  └────────────────────────────────────────────────────┘ │
└────────────────┬─────────────────────────────────────────┘
                 │
                 ▼ Import
      ┌────────────────────┐
      │  LoginPage.tsx     │
      │  - Receives input  │
      │  - Validates       │
      │  - Finds user      │
      └─────────┬──────────┘
                │
                │ onLogin(user)
                ▼
        ┌──────────────┐
        │   App.tsx    │
        │ setCurrentUser│
        └──────┬────────┘
               │
               │ Pass as props
               ├──────────────┬───────────────┐
               │              │               │
               ▼              ▼               ▼
        ┌───────────┐  ┌────────────┐  ┌──────────┐
        │AdminSidebar│  │EasyPCHeader│  │Pages     │
        │user={user} │  │user={user} │  │Access user│
        └───────────┘  └────────────┘  └──────────┘
```

---

## User Journey Maps

### 🔵 Admin User Journey

```
1. OPEN APP
   └─> See Login Page

2. SELECT "ADMIN" TAB
   └─> Blue theme activates
   └─> Shield icon displayed

3. CLICK "USE DEMO ACCOUNT"
   └─> Fields auto-fill
   └─> Instant login

4. REDIRECTED TO DASHBOARD
   └─> See analytics
   └─> View reports
   └─> Access all features

5. MANAGE SYSTEM
   ├─> Add/Edit Products
   ├─> Check Inventory
   ├─> View Orders
   ├─> Generate Reports
   └─> Update Settings

6. LOGOUT
   └─> Click Logout in sidebar
   └─> Return to Login Page
```

### 🟢 Cashier User Journey

```
1. OPEN APP
   └─> See Login Page

2. SELECT "CASHIER" TAB
   └─> Green theme activates
   └─> CreditCard icon displayed

3. ENTER CREDENTIALS
   └─> cashier@techtrack.com
   └─> cashier123

4. REDIRECTED TO POS SYSTEM
   └─> Ready to process sales

5. DAILY TASKS
   ├─> Scan items
   ├─> Process payments
   ├─> Check inventory (view)
   └─> Print receipts

6. END SHIFT
   └─> Click Logout in sidebar
   └─> Close register
```

### 🟣 Customer User Journey

```
1. OPEN APP
   └─> See Login Page

2. SELECT "CUSTOMER" TAB
   └─> Purple theme activates
   └─> ShoppingBag icon displayed

3. USE DEMO ACCOUNT
   └─> Quick access to shop

4. BROWSE PRODUCTS
   └─> See product catalog
   └─> View deals and promotions

5. SHOPPING
   ├─> Add items to cart
   ├─> View cart
   ├─> Proceed to checkout
   └─> Enter shipping info

6. TRACK ORDER
   └─> View order status
   └─> See delivery updates

7. ACCOUNT MANAGEMENT
   └─> Click avatar in header
   ├─> View My Orders
   ├─> Manage Wishlist
   ├─> Update Settings
   └─> Logout when done
```

---

## State Management Flow

```
┌─────────────────────────────────────────────────┐
│         App Component State                    │
├─────────────────────────────────────────────────┤
│  currentUser: User | null                      │
│    ├─ null → Show LoginPage                    │
│    └─ User → Show App Interface                │
│                                                 │
│  adminPage: AdminPage                          │
│    └─ 'dashboard' | 'products' | 'pos' | ...   │
│                                                 │
│  customerPage: CustomerPage                    │
│    └─ 'home' | 'shop' | 'checkout' | ...       │
│                                                 │
│  cart: CartItem[]                              │
│    └─ Shopping cart items                      │
│                                                 │
│  selectedProduct: Product | null               │
│    └─ Product detail view                      │
└─────────────────────────────────────────────────┘

           ┌─────────────────┐
           │  User Actions   │
           └────────┬────────┘
                    │
        ┌───────────┼────────────┐
        │           │            │
        ▼           ▼            ▼
    ┌──────┐   ┌───────┐   ┌────────┐
    │ Login│   │Navigate│   │ Logout │
    └──┬───┘   └───┬───┘   └───┬────┘
       │           │            │
       │           │            │
       ▼           ▼            ▼
    Set User   Change Page   Clear User
```

---

## Security Flow (Demo Mode)

```
┌──────────────────────────────────────────────────┐
│           AUTHENTICATION PROCESS                 │
└──────────────────┬───────────────────────────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │ Input: email, pass  │
        └──────────┬──────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │ Find in mockUsers   │
        └──────┬───────┬──────┘
               │       │
          FOUND│       │NOT FOUND
               │       │
               ▼       ▼
        ┌──────────┐  ┌──────────┐
        │ Check    │  │  Error   │
        │ Password │  │ Message  │
        └────┬─────┘  └──────────┘
             │
        MATCH│
             │
             ▼
        ┌──────────┐
        │ Check    │
        │ Role     │
        └────┬─────┘
             │
        MATCH│
             │
             ▼
        ┌──────────┐
        │ SUCCESS  │
        │ Set User │
        └──────────┘

⚠️  NOTE: This is CLIENT-SIDE only
    No encryption, no tokens, no backend
    Demo purposes only - NOT SECURE
```

---

## File Structure Map

```
TechTrack/
│
├── 📄 App.tsx ........................... Main app with auth logic
│
├── 📂 components/
│   ├── 🔐 LoginPage.tsx ................ Login interface
│   ├── 👤 AdminSidebar.tsx ............. Admin nav with logout
│   ├── 🛍️ EasyPCHeader.tsx ............. Customer header with user menu
│   └── 📂 ui/
│       ├── tabs.tsx .................... Role selection tabs
│       ├── dropdown-menu.tsx ........... User dropdown
│       └── ... (other UI components)
│
├── 📂 lib/
│   └── 📊 mockData.ts .................. User data & credentials
│
└── 📚 Documentation/
    ├── LOGIN_FEATURE_DOCUMENTATION.md .. Technical docs
    ├── QUICK_START_LOGIN.md ............ User guide
    ├── COPILOT_PROMPT.md ............... AI prompt
    ├── LOGIN_SYSTEM_SUMMARY.md ......... Implementation summary
    └── LOGIN_FLOW_DIAGRAM.md ........... This file
```

---

## Quick Reference Commands

```bash
# Test Admin Login
email: admin@techtrack.com
password: admin123
→ Redirects to: Dashboard

# Test Cashier Login  
email: cashier@techtrack.com
password: cashier123
→ Redirects to: POS System

# Test Customer Login
email: customer@email.com
password: customer123
→ Redirects to: Home/Shop
```

---

**Visual Guide Complete!** 🎉

Use these diagrams to understand the authentication flow at a glance.
