# QR Code Integration Documentation
## TechTrack System

### Overview
The QR code system has been successfully integrated into TechTrack using Google Charts API. No external library dependencies required - the system uses a simple, reliable API-based approach.

---

## Features Implemented

### 1. **Product Pages** 
- **Location:** `app/views/shop/product.php`
- **Functionality:** Each product detail page displays a QR code
- **Links to:** Product detail URL (`/product/{id}`)
- **Display:** Rendered as an HTML `<img>` tag with styling
- **Use Case:** Customers can scan to quickly share or save product links

### 2. **Order Invoices/Tracking**
- **Location:** `app/views/admin/orders/view.php`
- **Functionality:** Order detail pages show QR codes for tracking
- **Links to:** Order tracking URL (`/track-order/{order_id}`)
- **Display:** Card-style display in the admin order view sidebar
- **Use Case:** Staff can print/share QR codes with customers for order tracking

### 3. **Admin Reports**
- **Location:** `app/views/admin/reports/index.php`
- **Functionality:** PDF reports include QR codes at the bottom
- **Links to:** Admin dashboard (`/admin/dashboard`)
- **Display:** Embedded in PDF with descriptive text
- **Use Case:** Quick access back to dashboard from printed reports

---

## QR Code Helper Functions

### File: `app/helpers/qrcode_helper.php`

#### Core Functions:

```php
// Generate QR code URL
generate_qr_code($data, $size = 200)
// Returns: URL string to QR code image

// Generate base64 encoded QR code (for PDFs)
generate_qr_code_base64($data, $size = 200)
// Returns: Base64 data URI string

// Save QR code as PNG file
save_qr_code($data, $filepath, $size = 200)
// Returns: boolean (success status)

// Generate HTML img tag
qr_code_img_tag($data, $size = 200, $alt = 'QR Code', $attributes = [])
// Returns: HTML string
```

#### Specialized Functions:

```php
// Product QR code
product_qr_code($productId, $size = 200)

// Order tracking QR code
order_tracking_qr_code($orderId, $size = 200)

// Admin dashboard QR code
admin_dashboard_qr_code($size = 200)
```

---

## Usage Examples

### Example 1: Product Page QR Code

```php
// In controller
$this->call->helper('qrcode');
$data['qr_code_url'] = product_qr_code($product_id, 150);

// In view
<img src="<?= html_escape($qr_code_url) ?>" alt="Product QR Code">
```

### Example 2: Custom QR Code

```php
$this->call->helper('qrcode');

// Generate QR for any URL
$qr_url = generate_qr_code('https://example.com', 200);

// Or use img tag helper
echo qr_code_img_tag('https://example.com', 200, 'My QR', ['class' => 'my-qr']);
```

### Example 3: Save QR Code as File

```php
$this->call->helper('qrcode');

$url = site_url('product/123');
$filepath = PUBLIC_DIR . '/qrcodes/product_123.png';

if (save_qr_code($url, $filepath, 300)) {
    echo "QR code saved successfully!";
}
```

### Example 4: PDF Embedding

```php
$this->call->helper('qrcode');

// Get base64 encoded QR code
$qr_base64 = generate_qr_code_base64(site_url('admin/dashboard'), 100);

// In jsPDF
doc.addImage('<?= $qr_base64 ?>', 'PNG', x, y, width, height);
```

---

## Integration Points

### Controllers Updated:
1. **Shop.php** - `product()` method loads QR helper and generates product QR
2. **AdminOrders.php** - `view()` method generates order tracking QR
3. **AdminReports.php** - Constructor loads QR helper, `index()` generates admin dashboard QR

### Views Updated:
1. **app/views/shop/product.php** - Added QR code section below add-to-cart form
2. **app/views/admin/orders/view.php** - Added QR code in status update sidebar
3. **app/views/admin/reports/index.php** - Added QR code to PDF footer generation

---

## Technical Details

### API Provider
- **Service:** Google Charts API
- **Endpoint:** `https://chart.googleapis.com/chart`
- **Format:** PNG image
- **Parameters:**
  - `chs`: Size (e.g., `200x200`)
  - `cht`: Chart type (`qr` for QR codes)
  - `chl`: Data to encode (URL encoded)
  - `choe`: Character encoding (`UTF-8`)

### Benefits of This Approach:
✅ **No Dependencies:** No Composer packages needed
✅ **Simple:** Easy to understand and maintain
✅ **Reliable:** Uses Google's stable API
✅ **Fast:** No server-side image generation
✅ **Flexible:** Works with any data/URL
✅ **Compatible:** Returns standard PNG images

### Limitations:
⚠️ Requires internet connection (API call)
⚠️ Dependent on Google Charts API availability
⚠️ Limited customization (no color/logo support)

---

## Testing

Access the test page to verify QR code functionality:
```
http://localhost:8080/techtrack1.3/test_qrcode.php
```

The test page demonstrates:
- Product QR codes
- Order tracking QR codes  
- Admin dashboard QR codes
- Custom URL QR codes

---

## Maintenance Notes

### Future Enhancements:
- Add QR code customization (colors, logos)
- Implement offline QR generation library
- Add QR code analytics/tracking
- Create QR code management dashboard
- Batch QR code generation for products

### Security Considerations:
- QR codes link to public-facing URLs
- No sensitive data should be encoded directly
- Use order IDs (not customer data) for tracking
- Validate/sanitize all URLs before encoding

---

## Support

For issues or questions:
1. Check helper file: `app/helpers/qrcode_helper.php`
2. Review test page: `test_qrcode.php`
3. Verify Google Charts API is accessible
4. Check PHP `file_get_contents()` is enabled for base64 encoding

---

**Version:** 1.0  
**Last Updated:** November 28, 2025  
**Author:** TechTrack Development Team
