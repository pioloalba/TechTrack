# 🗺️ TechTrack Button Location Map

**Visual guide to locate every button in the system**

---

## 🎯 Admin Section

### **Admin Products Page** (`http://localhost:8080/index.php/admin/products`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - Admin Products                                  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │ Total   │ │ Active  │ │Low Stock│ │Out Stock│          │
│  │ 42 prod │ │ 38 prod │ │ 3 items │ │ 1 item  │          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│                                                              │
│  ┌─────────────────┐  ┌──────────────┐  ┌────────────┐    │
│  │[🔍] Search      │  │Category ▼    │  │Stock ▼     │    │
│  └─────────────────┘  └──────────────┘  └────────────┘    │
│                                                              │
│  [⊞] Grid  [☰] List  [+ Add Product]  ← 3 Buttons         │
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │ Product 1│ │ Product 2│ │ Product 3│ │ Product 4│      │
│  │ Image    │ │ Image    │ │ Image    │ │ Image    │      │
│  │ Title    │ │ Title    │ │ Title    │ │ Title    │      │
│  │ Price    │ │ Price    │ │ Price    │ │ Price    │      │
│  │[👁View]  │ │[👁View]  │ │[👁View]  │ │[👁View]  │      │
│  │[✏Edit]  │ │[✏Edit]  │ │[✏Edit]  │ │[✏Edit]  │      │
│  │[🗑Delete]│ │[🗑Delete]│ │[🗑Delete]│ │[🗑Delete]│      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘      │
│                                                              │
│  ← Pagination: [< Prev] [1] [2] [3] [Next >]               │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Search Products        → Top left area
2. Filter Category        → Next to search
3. Filter Stock Status    → Next to category
4. Grid View Toggle       → Top right area
5. List View Toggle       → Next to grid button
6. Add Product Button     → Top right corner (blue)
7. View Button (×N)       → On each product card
8. Edit Button (×N)       → On each product card
9. Delete Button (×N)     → On each product card
10. Pagination Links      → Bottom of page
```

---

### **Admin Customers Page** (`http://localhost:8080/index.php/admin/customers`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - Admin Customers                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  [+ Add Customer]  [🔍 Search customers...]                 │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Name    │ Email         │ Phone       │ Actions       │ │
│  ├────────────────────────────────────────────────────────┤ │
│  │ John D. │ john@mail.com │ 123-4567    │ [👁] [✏] [🗑]│ │
│  │ Jane S. │ jane@mail.com │ 234-5678    │ [👁] [✏] [🗑]│ │
│  │ Mike T. │ mike@mail.com │ 345-6789    │ [👁] [✏] [🗑]│ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Add Customer          → Top left (blue button)
2. Search Customers      → Top right input field
3. View Customer (×N)    → Actions column (eye icon)
4. Edit Customer (×N)    → Actions column (pencil icon)
5. Delete Customer (×N)  → Actions column (trash icon)
```

---

### **Admin Orders Page** (`http://localhost:8080/index.php/admin/orders`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - Admin Orders                                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  [🔍 Search orders...]  [Filter Status ▼]                   │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Order # │ Customer    │ Total    │ Status  │ Actions  │ │
│  ├────────────────────────────────────────────────────────┤ │
│  │ #1001   │ John Doe    │ ₱45,999  │ Pending │ [View]   │ │
│  │ #1002   │ Jane Smith  │ ₱12,500  │ Done    │ [View]   │ │
│  │ #1003   │ Mike Taylor │ ₱8,750   │ Process │ [View]   │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Search Orders         → Top left input field
2. Filter Status         → Top right dropdown
3. View Order (×N)       → Actions column
```

---

### **Admin Reports Page** (`http://localhost:8080/index.php/admin/reports`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - Admin Reports                                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  [📄 Export as PDF]  ← Main Export Button                   │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │                                                          │ │
│  │  Sales Report                                            │ │
│  │  • Total Revenue: ₱125,450                               │ │
│  │  • Total Orders: 42                                      │ │
│  │  • Average Order: ₱2,987                                 │ │
│  │                                                          │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Export as PDF         → Top right corner (blue button)
```

---

### **Admin Alerts Page** (`http://localhost:8080/index.php/admin/alerts`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - Alerts & Notifications                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                    │
│  │Critical  │ │Low Stock │ │Total     │                    │
│  │5 Items   │ │12 Items  │ │17 Items  │                    │
│  └──────────┘ └──────────┘ └──────────┘                    │
│                                                              │
│  Low Stock Alerts:                                           │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ ⚠ Intel Core i9 - Stock: 3 | Threshold: 5  [Critical]  │ │
│  │ ⚠ RTX 4090 GPU  - Stock: 2 | Threshold: 5  [Critical]  │ │
│  │ ⚠ DDR5 RAM 32GB - Stock: 7 | Threshold: 10 [Medium]    │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
(No action buttons - Display/Information only)
Priority badges shown, but not interactive
```

---

### **Admin Settings Page** (`http://localhost:8080/index.php/admin/settings`)

```
┌─────────────────────────────────────────────────────────────┐
│ TechTrack - System Settings                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Store Information:                                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Store Name:    [TechTrack                            ] │ │
│  │ Email:         [info@techtrack.com                   ] │ │
│  │ Phone:         [+63 123 456 7890                     ] │ │
│  │ Address:       [123 Tech Street, Manila              ] │ │
│  │                                                          │ │
│  │ [🌍 Detect Store Location]  ← Geolocation Button        │ │
│  │                                                          │ │
│  │ [💾 Save Changes]           ← Main Save Button          │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Detect Store Location → Middle of form (blue button)
2. Save Changes          → Bottom of form (blue button)
```

---

## 🛍️ Shop Section (Customer View)

### **Shop Homepage** (`http://localhost:8080/index.php/shop`)

```
┌─────────────────────────────────────────────────────────────┐
│ 🔷 TechTrack  [🔍 Search...]  🛒 Cart  👤 Profile          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ 🎮 Gaming Desktop PCs                                   │ │
│  │ Starting at ₱45,999                                     │ │
│  │ [Pre-Order Now]  ← Hero Button                          │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  Shop by Category              [View All ›]  ← Button 1     │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐                                │
│  │🖥️ │ │⚡ │ │💾 │ │💿 │  ← 8 Category Cards              │
│  │CPU │ │M/B │ │RAM │ │SSD │     (All Clickable)           │
│  └────┘ └────┘ └────┘ └────┘                                │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐                                │
│  │🖥️ │ │🔌 │ │📦 │ │💻 │                                │
│  │GPU │ │PSU │ │Case│ │Lap │                                │
│  └────┘ └────┘ └────┘ └────┘                                │
│                                                              │
│  Latest Products                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │ Product  │ │ Product  │ │ Product  │ │ Product  │      │
│  │ Image    │ │ Image    │ │ Image    │ │ Image    │      │
│  │ ₱12,999  │ │ ₱8,499   │ │ ₱15,999  │ │ ₱25,999  │      │
│  │[Add Cart]│ │[Add Cart]│ │[Add Cart]│ │[Add Cart]│      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘      │
│                                                              │
│  Top Sellers                   [View All Products ›]  ← B2  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │🔥 HOT    │ │🔥 HOT    │ │🔥 HOT    │ │🔥 HOT    │      │
│  │ Product  │ │ Product  │ │ Product  │ │ Product  │      │
│  │ ⭐⭐⭐⭐⭐│ │ ⭐⭐⭐⭐⭐│ │ ⭐⭐⭐⭐⭐│ │ ⭐⭐⭐⭐⭐│      │
│  │ ₱45,999  │ │ ₱12,500  │ │ ₱8,750   │ │ ₱22,990  │      │
│  │[Add Cart]│ │[Add Cart]│ │[Add Cart]│ │[Add Cart]│      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘      │
└─────────────────────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Pre-Order Now         → Hero section (large button)
2. View All              → Shop by Category section (link)
3. Category Cards (×8)   → Grid below "Shop by Category"
   - Processor
   - Motherboard
   - Graphics Card
   - Memory
   - SSD
   - Power Supply
   - PC Case
   - Laptop
4. Add to Cart (×N)      → On each product card
5. View All Products     → Top Sellers section (link)
```

---

## 🔐 Authentication Pages

### **Admin Login** (`http://localhost:8080/index.php/auth/admin_login`)

```
┌─────────────────────────────────────────┐
│         TechTrack Admin Login           │
├─────────────────────────────────────────┤
│                                         │
│  Email:    [_____________________]     │
│                                         │
│  Password: [_____________________] [👁] │
│                                         │
│  [Use Demo Account]  ← Demo Button     │
│                                         │
│  [Sign In]  ← Main Login Button        │
│                                         │
│  Cashier Login  |  Customer Login      │
│                                         │
└─────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Toggle Password       → Right side of password input
2. Use Demo Account      → Below password field
3. Sign In               → Main form submit button
4. Cashier Login         → Bottom link
5. Customer Login        → Bottom link
```

---

## 📋 Modal Locations

### **Product Modal** (Opens on "Add Product" or "Edit")

```
┌───────────────────────────────────────────────┐
│ Add Product                               [×] │ ← Close
├───────────────────────────────────────────────┤
│                                               │
│  Product Name:  [______________________]     │
│  SKU:           [______________________]     │
│  Category:      [________▼]                   │
│  Brand:         [______________________]     │
│  Price:         [______________________]     │
│  Sale Price:    [______________________]     │
│  Stock:         [______________________]     │
│  Threshold:     [______________________]     │
│  Description:   [______________________]     │
│                 [                      ]     │
│  Images:        [Choose Files]               │
│                                               │
│  [Preview images appear here]                │
│                                               │
├───────────────────────────────────────────────┤
│              [Cancel]  [Save Product]         │ ← 2 Buttons
└───────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Close (×)             → Top right corner
2. Cancel                → Bottom left
3. Save Product          → Bottom right (blue)
4. Choose Files          → Image upload section
5. Remove Image (×N)     → On each preview image (red ×)
```

---

### **View Product Modal** (Opens on "View" button)

```
┌───────────────────────────────────────────────┐
│ Product Details                           [×] │ ← Close
├───────────────────────────────────────────────┤
│                                               │
│  ┌──────┐ ┌──────┐                           │
│  │Image │ │Image │  Product Name: RTX 4090   │
│  │  1   │ │  2   │  SKU: GPU-001             │
│  └──────┘ └──────┘  Category: Graphics Card  │
│  ┌──────┐ ┌──────┐  Brand: NVIDIA            │
│  │Image │ │Image │  Stock: 12 units          │
│  │  3   │ │  4   │  Price: ₱125,999          │
│  └──────┘ └──────┘                            │
│                     Description: ...          │
│                                               │
└───────────────────────────────────────────────┘

BUTTON LOCATIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Close (×)             → Top right corner
(View-only modal, no action buttons)
```

---

## 🎨 Button Visual Styles

### Primary Buttons (Blue)
```
[+ Add Product]  [💾 Save Changes]  [Sign In]
```
- Background: #3B82F6 (Blue)
- Text: White
- Used for: Main actions, submit, create

### Secondary Buttons (Gray)
```
[Cancel]  [⊞ Grid]  [☰ List]
```
- Background: White
- Border: Gray
- Used for: Cancel, toggle, view switches

### Action Buttons (Icon + Text)
```
[👁 View]  [✏ Edit]  [🗑 Delete]
```
- Small, icon-based
- View: Blue
- Edit: Gray
- Delete: Red on hover

### Link Buttons
```
[View All ›]  [View All Products ›]
```
- No background
- Blue text
- Arrow indicator (›)

---

## 🔍 Quick Find Reference

**Need to find a specific button?**

| Button Name | Page | Location | ID/Class |
|------------|------|----------|----------|
| Add Product | Products | Top right | `#addProductBtn` |
| Grid View | Products | Top toolbar | `#toggleView` |
| List View | Products | Top toolbar | `#toggleViewList` |
| Search Products | Products | Top left | `#searchProducts` |
| Filter Category | Products | Top toolbar | `#filterCategory` |
| Filter Stock | Products | Top toolbar | `#filterStock` |
| Edit Product | Products | Card actions | `.edit-product` |
| View Product | Products | Card actions | `.view-btn` |
| Delete Product | Products | Card actions | `.delete-product` |
| Add Customer | Customers | Top left | Link |
| Search Customers | Customers | Top right | `#searchCustomers` |
| View Customer | Customers | Table actions | `.view-btn` |
| Edit Customer | Customers | Table actions | `.edit-btn` |
| Delete Customer | Customers | Table actions | `.delete-btn` |
| Search Orders | Orders | Top left | `#searchOrders` |
| Filter Status | Orders | Top right | `#filterStatus` |
| View Order | Orders | Table actions | `.view-btn` |
| Export PDF | Reports | Top right | `#exportPDF` |
| View All | Shop | Category section | `.view-all` |
| Category Cards | Shop | Category grid | `.category-card` |
| Pre-Order Now | Shop | Hero section | `.hero-btn` |
| Add to Cart | Shop | Product cards | `.add-to-cart-btn` |

---

## 📱 Mobile View Adjustments

**On screens < 768px:**
- Buttons stack vertically
- Filters become dropdowns
- Category cards go single column
- Product cards go single column
- List view converts to card stacking

---

**Use this map to quickly locate and test any button in the system! 🎯**
