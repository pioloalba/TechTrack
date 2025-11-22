# 🧪 TechTrack Button Testing Guide

**Quick reference for testing all button functionality**

---

## 🚀 Quick Start Testing

### 1. Open Your Browser
Navigate to: `http://localhost:8080`

---

## 📝 Test Sequence

### A. **Admin Products Page** (5 minutes)

**Access:** Login as admin → Navigate to Products

**Test Checklist:**
```
✓ Click "Add Product" button
  → Modal should open with empty form
  
✓ Fill in product details (name, price, stock, etc.)
  → Click "Save Product"
  → Success notification should appear
  → Page should reload showing new product
  
✓ Click "Edit" button on any product card
  → Modal should open with pre-filled data
  → Change product name
  → Click "Update Product"
  → Success notification + reload
  
✓ Click "View" button on any product
  → View modal should open
  → All product details should display
  → Images should show
  
✓ Click "Delete" button on any product
  → Confirmation dialog should appear
  → Click OK
  → Product card should fade out and disappear
  
✓ Click Grid View icon (squares)
  → Products should display in grid layout
  
✓ Click List View icon (lines)
  → Products should display in horizontal cards
  → Image on left, content on right
  
✓ Refresh page
  → Last selected view (grid/list) should persist
  
✓ Type in "Search products" input
  → Products should filter in real-time
  → Try: processor, laptop, etc.
  
✓ Select category from dropdown
  → Products should filter by category
  
✓ Select "Low Stock" from stock filter
  → Only low stock items should show
```

---

### B. **Shop Homepage** (3 minutes)

**Access:** Navigate to Shop page (main customer view)

**Test Checklist:**
```
✓ Scroll to "Shop by Category" section
  
✓ Click "View All ›" button
  → Should smooth scroll to products section
  
✓ Click any category card (e.g., "Processor")
  → Should navigate to shop with category filter
  → URL should include: ?search=Processor
  
✓ Test all 8 category cards:
  → Processor
  → Motherboard
  → Graphics Card
  → Memory
  → SSD
  → Power Supply
  → PC Case
  → Laptop
  
✓ Hover over category cards
  → Card should lift up (translateY effect)
  → Shadow should increase
  
✓ Scroll to "Top Sellers" section
  
✓ Click "View All Products ›"
  → Should scroll to products section
  
✓ Click "Pre-Order Now" in hero section
  → Should navigate to Gaming products
```

---

### C. **Admin Customers** (2 minutes)

**Access:** Admin → Customers

**Test Checklist:**
```
✓ Click "Add Customer" button
  → Should navigate to customer form
  
✓ Go back to customers list
  
✓ Type in "Search customers" input
  → Table should filter in real-time
  
✓ Click "View" (eye icon) on any customer
  → Should navigate to customer detail page
  
✓ Click "Edit" (pencil icon) on any customer
  → Should navigate to edit form
  
✓ Click "Delete" (trash icon) on any customer
  → Confirmation dialog should appear
```

---

### D. **Admin Orders** (2 minutes)

**Access:** Admin → Orders

**Test Checklist:**
```
✓ Type in "Search orders" input
  → Orders should filter in real-time
  
✓ Select status from "Filter by status" dropdown
  → All Statuses
  → Pending
  → Processing
  → Completed
  → Cancelled
  → Orders should filter by selected status
  
✓ Click "View" button on any order
  → Should navigate to order detail page
```

---

### E. **Admin Reports** (1 minute)

**Access:** Admin → Reports

**Test Checklist:**
```
✓ Click "Export as PDF" button
  → New window/tab should open
  → PDF should be generated or download initiated
```

---

## 🎯 Common Issues & Solutions

### Issue: Modal doesn't open
**Solution:** 
- Check browser console for errors
- Verify `public/assets/js/products.js` is loaded
- Check if jQuery is loaded first

### Issue: Filters don't work
**Solution:**
- Ensure JavaScript is enabled
- Check console for errors
- Verify product cards have correct `data-` attributes

### Issue: View toggle doesn't persist
**Solution:**
- Check if localStorage is enabled in browser
- Clear browser cache
- Check browser privacy settings

### Issue: AJAX operations fail
**Solution:**
- Check network tab in browser DevTools
- Verify controller methods exist
- Check server error logs

### Issue: Buttons have no styling
**Solution:**
- Clear browser cache (Ctrl+F5)
- Check if CSS files are loaded
- Verify no CSS conflicts

---

## 🔧 Browser Console Commands

Open console with `F12` or `Ctrl+Shift+I`

### Check if jQuery is loaded:
```javascript
typeof jQuery
// Should output: "function"
```

### Check if products.js is loaded:
```javascript
console.log('Products.js:', $('#addProductBtn').length)
// Should output: Products.js: 1
```

### Check localStorage:
```javascript
localStorage.getItem('productView')
// Should output: "grid" or "list"
```

### Clear view preference:
```javascript
localStorage.removeItem('productView')
// Then refresh page
```

---

## 📊 Expected Results Summary

| Feature | Expected Behavior | Time to Test |
|---------|------------------|--------------|
| Add Product | Modal opens, form submits, success notification | 30 sec |
| Edit Product | Modal opens with data, updates save | 30 sec |
| View Product | Details modal opens with all info | 15 sec |
| Delete Product | Confirmation, card removes with animation | 20 sec |
| Grid/List Toggle | View switches, preference saves | 20 sec |
| Search Products | Real-time filtering, <300ms delay | 30 sec |
| Category Filter | Dropdown filters products instantly | 20 sec |
| Stock Filter | Filters by status instantly | 20 sec |
| View All Button | Smooth scroll to products | 10 sec |
| Category Cards | Navigate with filter, hover effects | 60 sec |
| Customer Search | Real-time table filtering | 20 sec |
| Order Search | Real-time table filtering | 20 sec |
| Order Status Filter | Dropdown filters orders | 20 sec |
| Export PDF | Opens in new window | 10 sec |

**Total Testing Time: ~12 minutes**

---

## ✅ Success Criteria

**ALL tests pass when:**
- ✓ No JavaScript errors in console
- ✓ All modals open/close properly
- ✓ All AJAX operations return success
- ✓ Filters work in real-time
- ✓ View toggle persists after refresh
- ✓ Notifications appear and auto-dismiss
- ✓ Confirmation dialogs show before delete
- ✓ Animations are smooth
- ✓ No broken links or buttons
- ✓ Mobile responsive works

---

## 📱 Mobile Testing

### Responsive Breakpoints:
- Desktop: 1024px+
- Tablet: 768px - 1023px
- Mobile: < 768px

### Test on mobile:
```
✓ Products switch to single column
✓ List view converts to card stacking
✓ Filters remain accessible
✓ Modals fit screen
✓ Touch targets are large enough
✓ Horizontal scroll doesn't occur
```

---

## 🐛 Bug Reporting Template

If you find an issue:

```
**Button Name:** [e.g., Add Product]
**Page:** [e.g., Admin Products]
**Browser:** [e.g., Chrome 120]
**Steps:**
1. [What you did]
2. [What happened]

**Expected:** [What should happen]
**Actual:** [What actually happened]

**Console Errors:** [Copy any red errors]
**Screenshot:** [If applicable]
```

---

## 🎓 Pro Tips

1. **Use Ctrl+Shift+R** to hard refresh (bypass cache)
2. **Open DevTools Network tab** to see AJAX requests
3. **Check Console tab** for JavaScript errors
4. **Use Elements tab** to inspect button HTML
5. **Test in incognito mode** to rule out extensions

---

## 📞 Need Help?

**Check these first:**
1. Browser console for errors
2. Network tab for failed requests
3. Button has correct ID or class
4. JavaScript files are loaded
5. Page has no PHP errors

**Documentation References:**
- Main Report: `BUTTON_FUNCTIONALITY_AUDIT.md`
- Products JS: `public/assets/js/products.js`
- Shop JS: `public/assets/js/shop.js`

---

**Happy Testing! 🚀**

Every button should work perfectly. If something doesn't work, it's likely:
- Browser cache issue (hard refresh)
- JavaScript not loaded (check console)
- Controller endpoint missing (check network tab)
