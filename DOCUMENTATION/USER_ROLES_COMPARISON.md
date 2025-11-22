# 👥 TechTrack User Roles - Complete Comparison

## 🎭 Role Overview

| Aspect | 🔵 Admin | 🟢 Cashier | 🟣 Customer |
|--------|----------|-----------|-------------|
| **Primary Purpose** | System Management | Point of Sale | Shopping |
| **Interface** | Admin Dashboard | Admin Dashboard (Limited) | Customer Store |
| **Default Page** | Dashboard | POS System | Home/Shop |
| **Icon** | Shield 🛡️ | Credit Card 💳 | Shopping Bag 🛍️ |
| **Theme Color** | Blue (#2563eb) | Green | Purple |
| **Access Level** | Full Control | Limited | Restricted |

---

## 🔑 Login Credentials

### Admin Accounts

| Name | Email | Password | Avatar |
|------|-------|----------|--------|
| Admin User | admin@techtrack.com | admin123 | ✅ |

### Cashier Accounts

| Name | Email | Password | Avatar |
|------|-------|----------|--------|
| John Cashier | cashier@techtrack.com | cashier123 | ✅ |
| Maria Santos | maria@techtrack.com | cashier123 | ✅ |

### Customer Accounts

| Name | Email | Password | Avatar |
|------|-------|----------|--------|
| John Smith | customer@email.com | customer123 | ✅ |
| Emma Wilson | emma@email.com | customer123 | ✅ |

---

## 📊 Feature Access Matrix

### Admin Panel Pages

| Page | Admin | Cashier | Customer |
|------|:-----:|:-------:|:--------:|
| **Dashboard** | ✅ Full | ❌ No | ❌ No |
| **Products** | ✅ CRUD | 👁️ View Only | ❌ No |
| **Inventory** | ✅ Manage | 👁️ View Only | ❌ No |
| **POS System** | ✅ Full | ✅ Full | ❌ No |
| **Orders** | ✅ All Orders | ✅ All Orders | ❌ No |
| **Customers** | ✅ Full | ❌ No | ❌ No |
| **Reports** | ✅ Full | ❌ No | ❌ No |
| **Alerts** | ✅ Full | ❌ No | ❌ No |
| **Settings** | ✅ Full | ❌ No | ❌ No |

### Customer Store Features

| Feature | Admin | Cashier | Customer |
|---------|:-----:|:-------:|:--------:|
| **Browse Products** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Shopping Cart** | ❌ No | ❌ No | ✅ Yes |
| **Checkout** | ❌ No | ❌ No | ✅ Yes |
| **Order Tracking** | ✅ All | ✅ All | ✅ Own Only |
| **Wishlist** | ❌ No | ❌ No | ✅ Yes |
| **User Profile** | ✅ Yes | ✅ Yes | ✅ Yes |

---

## 🎯 Permissions Breakdown

### 📦 Product Management

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| View Products | ✅ | ✅ | ✅ |
| Search Products | ✅ | ✅ | ✅ |
| Add New Product | ✅ | ❌ | ❌ |
| Edit Product | ✅ | ❌ | ❌ |
| Delete Product | ✅ | ❌ | ❌ |
| View Specs | ✅ | ✅ | ✅ |
| Manage Categories | ✅ | ❌ | ❌ |
| Set Prices | ✅ | ❌ | ❌ |
| Update Images | ✅ | ❌ | ❌ |

### 📊 Inventory Management

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| View Stock Levels | ✅ | ✅ | ❌ |
| Update Stock | ✅ | ❌ | ❌ |
| Low Stock Alerts | ✅ | ❌ | ❌ |
| Reorder Products | ✅ | ❌ | ❌ |
| View Suppliers | ✅ | ❌ | ❌ |
| Inventory Reports | ✅ | ❌ | ❌ |
| Stock Adjustments | ✅ | ❌ | ❌ |

### 💰 Sales & Orders

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| Process POS Sale | ✅ | ✅ | ❌ |
| View All Orders | ✅ | ✅ | ❌ |
| View Own Orders | ✅ | ✅ | ✅ |
| Update Order Status | ✅ | ✅ | ❌ |
| Cancel Order | ✅ | ✅ | Own Only |
| Refund Order | ✅ | ❌ | ❌ |
| Place Order (Shop) | ❌ | ❌ | ✅ |
| Track Order | ✅ | ✅ | ✅ |

### 👥 Customer Management

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| View Customer List | ✅ | ❌ | ❌ |
| View Customer Details | ✅ | ❌ | ❌ |
| Edit Customer Info | ✅ | ❌ | Own Only |
| View Purchase History | ✅ | ❌ | Own Only |
| Customer Analytics | ✅ | ❌ | ❌ |

### 📈 Reports & Analytics

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| Sales Reports | ✅ | ❌ | ❌ |
| Revenue Charts | ✅ | ❌ | ❌ |
| Product Analytics | ✅ | ❌ | ❌ |
| Customer Insights | ✅ | ❌ | ❌ |
| Export PDF Reports | ✅ | ❌ | ❌ |
| View Dashboard Stats | ✅ | ❌ | ❌ |

### ⚙️ System Settings

| Action | Admin | Cashier | Customer |
|--------|:-----:|:-------:|:--------:|
| System Configuration | ✅ | ❌ | ❌ |
| User Management | ✅ | ❌ | ❌ |
| Payment Settings | ✅ | ❌ | ❌ |
| Email Notifications | ✅ | ❌ | ❌ |
| Tax Settings | ✅ | ❌ | ❌ |
| Store Information | ✅ | ❌ | ❌ |

---

## 🎨 UI/UX Differences

### Login Experience

| Element | Admin | Cashier | Customer |
|---------|-------|---------|----------|
| **Tab Color** | Blue | Green | Purple |
| **Icon** | Shield | Credit Card | Shopping Bag |
| **Title** | "Admin Login" | "Cashier Login" | "Customer Login" |
| **Description** | "Access the admin dashboard to manage your store" | "Access the POS system for sales transactions" | "Sign in to track orders and access exclusive deals" |

### Main Interface

| Element | Admin | Cashier | Customer |
|---------|-------|---------|----------|
| **Navigation** | Sidebar (left) | Sidebar (left) | Header (top) |
| **Menu Items** | 9 items | 4 items | 8 items |
| **Logo Position** | Top of sidebar | Top of sidebar | Top left header |
| **User Info** | Sidebar footer | Sidebar footer | Header dropdown |
| **Logout** | Sidebar button | Sidebar button | Dropdown menu |

### Landing Page

| Role | Default Page | Purpose |
|------|--------------|---------|
| **Admin** | Dashboard | View analytics, reports, and system overview |
| **Cashier** | POS System | Ready to process sales immediately |
| **Customer** | Home/Shop | Browse products and start shopping |

---

## 🔄 Workflow Examples

### 🔵 Admin Daily Workflow

```
1. Login → Dashboard
2. Review daily sales analytics
3. Check low stock alerts
4. Add/update products
5. Process any pending orders
6. Generate end-of-day reports
7. Update system settings if needed
8. Logout
```

### 🟢 Cashier Daily Workflow

```
1. Login → POS System
2. Process customer purchases
3. Scan products / enter manually
4. Accept payments (Cash/GCash/PayPal)
5. Print receipts
6. Check inventory when needed
7. View order history
8. Logout at end of shift
```

### 🟣 Customer Shopping Workflow

```
1. Login → Home/Shop
2. Browse product categories
3. Search for specific items
4. Add items to cart
5. Review cart
6. Proceed to checkout
7. Enter shipping details
8. Select payment method
9. Place order
10. Track order status
11. Logout when done
```

---

## 📱 Responsive Behavior

| Feature | Desktop | Tablet | Mobile |
|---------|---------|--------|--------|
| **Admin Sidebar** | Fixed left | Fixed left | Hamburger menu |
| **Customer Header** | Full nav | Condensed | Icon menu |
| **Login Page** | Split (left/right) | Stacked | Single column |
| **Product Grid** | 4 columns | 2-3 columns | 1-2 columns |
| **User Avatar** | Full with name | Icon only | Icon only |

---

## 🔐 Security Comparison

| Security Feature | Implementation | Production Ready? |
|------------------|----------------|-------------------|
| **Password Storage** | Plain text | ❌ No |
| **Authentication** | Client-side | ❌ No |
| **Session Management** | React state | ❌ No |
| **Token Management** | None | ❌ No |
| **API Security** | N/A (no API) | ❌ No |
| **HTTPS** | Not enforced | ❌ No |
| **Rate Limiting** | None | ❌ No |
| **Role Validation** | Frontend only | ❌ No |

⚠️ **This is a DEMO system** - Not suitable for production use!

---

## 🎯 Use Case Scenarios

### Scenario 1: New Product Launch

| Role | Responsibility |
|------|----------------|
| **Admin** | Add new product to system with images, specs, pricing |
| **Cashier** | View new product in POS, ready to sell |
| **Customer** | See new product in shop, can purchase |

### Scenario 2: Low Stock Alert

| Role | Action |
|------|--------|
| **Admin** | Receives alert, places reorder, updates inventory |
| **Cashier** | Cannot sell out-of-stock items |
| **Customer** | Sees "Out of Stock" badge on product |

### Scenario 3: Customer Order

| Role | Involvement |
|------|-------------|
| **Customer** | Places order through checkout |
| **Cashier** | N/A (online order) |
| **Admin** | Sees order in Orders page, updates status |

### Scenario 4: In-Store Purchase

| Role | Involvement |
|------|-------------|
| **Customer** | N/A (in-store) |
| **Cashier** | Processes sale via POS |
| **Admin** | Sees transaction in reports |

---

## 📊 Stats & Numbers

| Metric | Count |
|--------|-------|
| **Total User Roles** | 3 |
| **Admin Accounts** | 1 |
| **Cashier Accounts** | 2 |
| **Customer Accounts** | 2 |
| **Admin Pages** | 9 |
| **Cashier Pages** | 4 |
| **Customer Pages** | 8 |
| **Total Features** | 50+ |

---

## 🚀 Quick Reference

### When to Use Each Role

**Use Admin when you need to:**
- Manage products and inventory
- View analytics and reports
- Configure system settings
- Manage customer database
- Generate reports

**Use Cashier when you need to:**
- Process in-store sales
- Use POS system
- Quick inventory lookup
- View order history

**Use Customer when you need to:**
- Shop for products
- Place online orders
- Track deliveries
- Manage wishlist

---

**Last Updated:** October 31, 2025  
**Version:** 1.0.0  
**Status:** ✅ Complete
