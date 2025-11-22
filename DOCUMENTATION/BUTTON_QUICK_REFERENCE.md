# 🚀 TechTrack Button Quick Reference

**One-page reference for all button functionality**

---

## 📍 Quick Access URLs

| Page | URL |
|------|-----|
| Admin Products | `http://localhost:8080/index.php/admin/products` |
| Admin Customers | `http://localhost:8080/index.php/admin/customers` |
| Admin Orders | `http://localhost:8080/index.php/admin/orders` |
| Admin Reports | `http://localhost:8080/index.php/admin/reports` |
| Admin Settings | `http://localhost:8080/index.php/admin/settings` |
| Admin Alerts | `http://localhost:8080/index.php/admin/alerts` |
| Shop Homepage | `http://localhost:8080/index.php/shop` |
| Admin Login | `http://localhost:8080/index.php/auth/admin_login` |

---

## 🎯 Most Important Buttons

### Admin Products Page
```javascript
// Add Product
Button ID: #addProductBtn
Action: Opens modal for new product
JS File: public/assets/js/products.js (Line 36)

// Grid/List Toggle  
Button ID: #toggleView, #toggleViewList
Action: Switch between grid and list layout
Persistence: localStorage
JS File: public/assets/js/products.js (Line 131-148)

// Search & Filter
Input ID: #searchProducts
Dropdown ID: #filterCategory, #filterStock
Action: Real-time filtering with 300ms debounce
JS File: public/assets/js/products.js (Line 108-125)

// Edit Product
Class: .edit-product
Data Attribute: data-id="productId"
Action: Opens modal with product data
JS File: public/assets/js/products.js (Line 52)

// Delete Product
Class: .delete-product
Data Attribute: data-id="productId"
Action: Confirmation dialog → Delete
JS File: public/assets/js/products.js (Line 64)
```

### Shop Homepage
```javascript
// Category Cards (8 total)
Class: .category-card
Action: Navigate to shop with category filter
Hover: Lift effect with shadow
JS File: public/assets/js/shop.js (Line 48)

// View All Buttons
Class: .view-all
Action: Smooth scroll to products section
JS File: public/assets/js/shop.js (Line 34)

// Pre-Order Button
Class: .hero-btn
Action: Navigate to Gaming products
JS File: public/assets/js/shop.js (Line 68)
```

---

## 🔧 Common JavaScript Functions

### products.js
```javascript
// Open Add/Edit Modal
openModal('add')           // Add new product
openModal('edit', id)      // Edit existing product

// Filter Functions
filterGrid(search, category, stock)  // Filter product cards
filterTable(search, category)        // Filter table rows

// Show Notification
showNotification('success', 'Message')  // Success toast
showNotification('error', 'Message')    // Error toast

// View Toggle
$('#toggleView').click()      // Switch to grid
$('#toggleViewList').click()  // Switch to list
```

### shop.js
```javascript
// Navigation
$('.view-all').click()        // Scroll to products
$('.category-card').click()   // Navigate with filter
$('.hero-btn').click()        // Navigate to Gaming

// Show Notification
showNotification('success', 'Message')
showNotification('error', 'Message')
```

---

## 🎨 CSS Classes

### Button States
```css
.btn                      /* Base button style */
.btn.primary              /* Blue primary button */
.btn-icon-text            /* Icon + text button */
.btn-icon-text.active     /* Active state (blue bg) */
.action-btn-sm            /* Small action button */
.delete-btn:hover         /* Red background on hover */
```

### View Layouts
```css
.products-grid            /* Default grid layout */
.list-view                /* List/horizontal layout */
.product-card             /* Individual product card */
.product-card:hover       /* Lift effect on hover */
```

### Status Badges
```css
.status-blue              /* In Stock */
.status-amber             /* Low Stock */
.status-red               /* Out of Stock */
```

---

## 💾 LocalStorage Keys

```javascript
// View Preference
localStorage.getItem('productView')
// Values: 'grid' or 'list'

// Set Grid View
localStorage.setItem('productView', 'grid')

// Set List View
localStorage.setItem('productView', 'list')

// Clear Preference
localStorage.removeItem('productView')
```

---

## 🔍 Finding Buttons in Code

### By ID
```javascript
$('#addProductBtn')       // Add Product button
$('#toggleView')          // Grid view button
$('#toggleViewList')      // List view button
$('#searchProducts')      // Search input
$('#filterCategory')      // Category dropdown
$('#filterStock')         // Stock status dropdown
$('#exportPDF')          // Export PDF button
```

### By Class
```javascript
$('.edit-product')        // All edit buttons
$('.delete-product')      // All delete buttons
$('.view-btn')            // All view buttons
$('.category-card')       // All category cards
$('.view-all')            // All "View All" links
$('.add-to-cart-btn')     // All add to cart buttons
```

### By Data Attribute
```javascript
$('[data-id="123"]')              // Specific product ID
$('[data-category="CPU"]')        // Specific category
$('[data-status="low_stock"]')    // Specific stock status
```

---

## 🐛 Debugging Commands

### Open Browser Console (F12)

```javascript
// Check if jQuery loaded
typeof jQuery
// Should return: "function"

// Check if button exists
$('#addProductBtn').length
// Should return: 1

// Check button click handler
$._data($('#addProductBtn')[0], 'events')
// Should show: {click: Array(1)}

// Get current view preference
localStorage.getItem('productView')
// Returns: "grid" or "list" or null

// Manually trigger button click
$('#addProductBtn').click()

// Check all product cards
$('.product-card').length
// Returns: number of products

// Filter test
filterGrid('processor', '', '')

// Clear all filters
$('#searchProducts').val('')
$('#filterCategory').val('')
$('#filterStock').val('')
$('.product-card').show()
```

---

## 📱 Mobile Testing

### Responsive Breakpoints
```css
Desktop:  > 1024px   (Grid: 3-4 columns)
Tablet:   768-1023px (Grid: 2 columns)
Mobile:   < 768px    (Grid: 1 column)
```

### Test Checklist
```
[ ] Products display single column
[ ] List view converts to stacked cards
[ ] Buttons stack vertically
[ ] Filters become dropdowns
[ ] Modals fit screen
[ ] Touch targets > 44px
[ ] No horizontal scroll
```

---

## ⚡ Performance Tips

### Search Optimization
- 300ms debounce prevents excessive filtering
- Only visible elements are checked

### View Toggle
- CSS transitions for smooth switching
- LocalStorage prevents re-render on reload

### AJAX Operations
- Loading states prevent double-submit
- Error handling shows user feedback
- Success notifications confirm actions

---

## 🎯 Testing Shortcuts

### Quick Button Test (2 min)
1. Click "Add Product" → Modal opens ✓
2. Click "Grid/List" → View toggles ✓
3. Type in search → Products filter ✓
4. Select category → Products filter ✓
5. Refresh page → View persists ✓

### Quick Shop Test (1 min)
1. Click category card → Navigates ✓
2. Click "View All" → Scrolls ✓
3. Hover category → Lifts up ✓

---

## 📊 Button Count by Page

| Page | Buttons |
|------|---------|
| Admin Products | 15+ |
| Shop Homepage | 12+ |
| Admin Customers | 8+ |
| Admin Orders | 5+ |
| Admin Reports | 3 |
| Admin Settings | 4 |
| Authentication | 10+ |
| **TOTAL** | **57+** |

---

## 🚨 Common Issues

### Modal Won't Open
- Check: `$('#productModal').length` should be 1
- Check: JavaScript console for errors
- Solution: Hard refresh (Ctrl+Shift+R)

### Filters Not Working
- Check: Product cards have `data-` attributes
- Check: filterGrid function exists
- Solution: Clear cache and reload

### View Won't Toggle
- Check: localStorage is enabled
- Check: CSS classes exist (.list-view)
- Solution: Inspect element for classes

### AJAX Fails
- Check: Network tab for 404/500 errors
- Check: Controller methods exist
- Solution: Check server error logs

---

## 📞 Quick Support

**Problem → Solution**

| Problem | Solution |
|---------|----------|
| Button doesn't respond | Check console for JS errors |
| Filter doesn't work | Check data attributes on cards |
| Modal won't close | Click outside modal or × button |
| View doesn't persist | Check localStorage in DevTools |
| AJAX returns error | Check network tab, verify endpoint |
| Styles not loading | Hard refresh (Ctrl+Shift+R) |

---

## 🎓 Key Takeaways

✅ All 57+ buttons are functional  
✅ JavaScript enhances existing functionality  
✅ LocalStorage saves user preferences  
✅ Debounced search improves performance  
✅ AJAX prevents page reloads  
✅ Mobile responsive design  
✅ Professional UI/UX  
✅ Comprehensive documentation  

---

**🎉 Everything Works! Enjoy Your Functional Buttons! 🎉**

---

**Quick Links:**
- Full Audit: `BUTTON_FUNCTIONALITY_AUDIT.md`
- Testing Guide: `BUTTON_TESTING_GUIDE.md`
- Location Map: `BUTTON_LOCATION_MAP.md`
- Completion Report: `BUTTON_FUNCTIONALITY_COMPLETE.md`
