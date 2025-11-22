# TechTrack - Complete System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Design System](#design-system)
3. [Admin Interface](#admin-interface)
4. [Customer Interface](#customer-interface)
5. [Component Architecture](#component-architecture)
6. [Data Structure](#data-structure)
7. [Future Integration](#future-integration)

---

## System Overview

**TechTrack** is a comprehensive web-based tech shop and inventory management system designed for electronics retailers. It provides two main interfaces:

- **Admin Dashboard**: Complete inventory management, POS system, order processing, customer management, analytics, and reporting
- **Customer Shop**: EASYPC-inspired e-commerce interface with product browsing, shopping cart, checkout, and order tracking

### Technology Stack
- **Framework**: React with TypeScript
- **Styling**: Tailwind CSS v4.0
- **UI Components**: Shadcn/ui
- **Icons**: Lucide React
- **Charts**: Recharts
- **Carousels**: React Slick
- **Date Handling**: date-fns
- **Notifications**: Sonner (toast)

---

## Design System

### Color Palette

#### Primary Colors
- **Tech Blue**: `#3B82F6` (Blue-500) - Primary brand color
- **Deep Blue**: `#1E40AF` (Blue-800) - Headers, emphasis
- **Light Blue**: `#DBEAFE` (Blue-100) - Backgrounds, highlights

#### Neutral Colors
- **Gray Scale**:
  - Gray-50: `#F9FAFB` - Light backgrounds
  - Gray-100: `#F3F4F6` - Card backgrounds
  - Gray-200: `#E5E7EB` - Borders
  - Gray-600: `#4B5563` - Secondary text
  - Gray-900: `#111827` - Primary text

- **White**: `#FFFFFF` - Main background
- **Black**: `#000000` - Text (when needed)

#### Accent Colors
- **Success Green**: `#10B981` - Success states, stock available
- **Warning Amber**: `#F59E0B` - Warnings, low stock
- **Danger Red**: `#EF4444` - Errors, out of stock, critical alerts
- **Orange**: `#F97316` - Discount badges

### Typography

**Primary Font**: System default (can be customized to Poppins or Inter)

**Font Sizes** (defined in globals.css):
- Headings use default HTML sizing
- Body text uses system defaults
- Do not override with Tailwind classes unless specifically needed

### Design Principles

1. **Clean & Minimal**: Uncluttered interfaces with ample white space
2. **Rounded Corners**: 12px border radius (rounded-xl in Tailwind)
3. **Soft Shadows**: Subtle elevation with shadow-sm, shadow-md
4. **Consistent Spacing**: 4px grid system (Tailwind spacing scale)
5. **Responsive**: Mobile-first approach with desktop optimization

---

## Admin Interface

### 1. Layout Structure

#### Sidebar Navigation (`AdminSidebar.tsx`)
**Width**: 256px (w-64)
**Background**: White with shadow
**Position**: Fixed left side

**Menu Items**:
- Dashboard - Overview and analytics
- Products - Product catalog management
- Inventory - Stock management and tracking
- POS - Point of Sale system
- Orders - Order management and fulfillment
- Customers - Customer database
- Reports - Analytics and exports
- Alerts - Notifications and warnings
- Settings - System configuration

**Design Features**:
- Active state: Blue background (bg-blue-50), blue text (text-blue-600)
- Hover state: Light gray background (hover:bg-gray-100)
- Icons: Lucide React icons (20px)
- Text: Medium weight for labels
- Selected indicator: Left border or background highlight

#### Top Bar (`AdminTopBar.tsx`)
**Height**: 64px (h-16)
**Background**: White with bottom border
**Position**: Fixed top, spans full width minus sidebar

**Components**:
- Search bar (left side)
- User profile dropdown (right side)
- Notifications icon with badge
- Quick actions menu

### 2. Dashboard (`Dashboard.tsx`)

#### Stats Cards (Top Row)
**Layout**: 4 cards in a grid (grid-cols-4)
**Card Design**:
- White background
- Rounded corners (rounded-xl)
- Soft shadow (shadow-sm)
- Padding: p-6

**Metrics Displayed**:
1. **Total Revenue**: ₱XXX,XXX.XX
   - Icon: DollarSign (blue)
   - Trend indicator: +X% from last month
   
2. **Total Orders**: XXX
   - Icon: ShoppingCart (green)
   - Trend indicator: +X orders today
   
3. **Total Products**: XXX
   - Icon: Package (purple)
   - Low stock indicator: X items
   
4. **Total Customers**: XXX
   - Icon: Users (orange)
   - New today: +X

#### Recent Orders Table
**Design**:
- Full-width table
- Alternating row colors (optional)
- Hover effect on rows
- Status badges with color coding:
  - Pending: Yellow/Amber
  - Processing: Blue
  - Shipped: Purple
  - Delivered: Green
  - Cancelled: Red

**Columns**:
- Order ID (e.g., #ORD-1001)
- Customer Name
- Date (formatted: "Dec 15, 2024")
- Total Amount (₱X,XXX.XX)
- Status (Badge)
- Actions (View/Edit buttons)

#### Sales Chart
**Chart Type**: Line or Area chart (Recharts)
**Time Period**: Last 7 days / 30 days (toggleable)
**Colors**: Blue gradient
**Data Points**: Daily revenue
**Tooltips**: Show exact values on hover

#### Low Stock Alerts
**Design**: Warning card with amber accent
**List Items**:
- Product name
- Current stock count
- Reorder threshold
- Quick action button: "Reorder"

### 3. Products Page

#### Product Grid/Table View Toggle
**View Options**:
- Grid view (default): 3-4 columns
- Table view: Detailed list

#### Filters & Search
**Filter Options**:
- Category dropdown
- Price range slider
- Stock status (In Stock / Low Stock / Out of Stock)
- Brand filter
- Search input (real-time)

**Action Buttons**:
- Add Product (primary blue button, top right)
- Import CSV
- Export CSV

#### Product Card (Grid View)
**Design**:
- Aspect ratio: 1:1 for product image
- White background
- Hover effect: Slight elevation
- Border: 1px gray-200

**Content**:
- Product image (top)
- Product name
- Category badge
- Price (₱X,XXX.XX)
- Stock indicator (color-coded dot + text)
- Quick actions: Edit, Delete icons

#### Product Table (Table View)
**Columns**:
- Image (thumbnail, 48x48px)
- Product Name
- SKU
- Category
- Price
- Stock
- Status
- Actions (Edit/Delete)

#### Add Product Modal (`AddProductModal.tsx`)
**Size**: Large modal (max-w-2xl)
**Sections**:

1. **Basic Information**:
   - Product Name (input)
   - SKU (auto-generated or manual)
   - Category (select dropdown)
   - Brand (input)
   - Description (textarea)

2. **Pricing**:
   - Regular Price (₱ input)
   - Sale Price (optional, ₱ input)
   - Discount Percentage (auto-calculated)

3. **Inventory**:
   - Stock Quantity (number input)
   - Low Stock Threshold (number input)
   - Reorder Point (number input)

4. **Images**:
   - Main Image (upload)
   - Gallery Images (multiple upload)
   - Image preview thumbnails

5. **Specifications** (for tech products):
   - Key-value pairs (dynamic fields)
   - Examples: Processor, RAM, Storage, GPU, etc.

6. **Actions**:
   - Save & Add Another (secondary button)
   - Save Product (primary blue button)
   - Cancel (text button)

### 4. Inventory Page (`InventoryPage.tsx`)

#### Stock Overview Cards
**Metrics**:
- Total Stock Value (₱XXX,XXX)
- Low Stock Items (count with warning icon)
- Out of Stock Items (count with danger icon)
- Reorder Needed (count)

#### Inventory Table
**Features**:
- Sortable columns (click headers)
- Inline editing (click to edit stock)
- Bulk actions (checkboxes)

**Columns**:
- Product Image & Name
- SKU
- Category
- Current Stock (color-coded)
- Reserved (from pending orders)
- Available (calculated)
- Reorder Point
- Status Badge
- Actions (Adjust Stock, View History)

#### Stock Adjustment Modal
**Purpose**: Add or remove stock
**Fields**:
- Adjustment Type (radio: Add / Remove)
- Quantity (number input)
- Reason (select: Restock, Sale, Damage, Return, etc.)
- Notes (textarea, optional)
- Date & Time (auto-filled, editable)

**Actions**:
- Confirm Adjustment (primary button)
- Cancel

#### Low Stock Alerts Section
**Design**: Alert cards with amber background
**Info Displayed**:
- Product name
- Current stock (bold, red if critical)
- Reorder point
- Suggested order quantity
- Supplier info (if available)
**Actions**:
- Send Notification (email/SMS)
- Create Purchase Order
- Dismiss Alert

### 5. POS System (`POSPage.tsx`)

#### Layout
**Split View**: 60/40 ratio
- **Left Side**: Product search and selection
- **Right Side**: Cart and checkout

#### Left Panel - Product Search
**Search Methods**:
1. **Search Input**: Text search by name/SKU
2. **Barcode Scanner**: Input field for barcode/QR scanning
   - Icon: Scan (barcode icon)
   - Auto-submit on scan
3. **Category Tabs**: Quick filter by category
4. **Product Grid**: Compact product cards

**Product Card (POS)**:
- Smaller size than main products page
- Image (square, 80x80px)
- Name (truncated)
- Price (prominent, ₱X,XXX.XX)
- Stock indicator (small badge)
- Add to Cart (click anywhere or + button)

#### Right Panel - Cart & Checkout
**Cart Header**:
- "Current Sale" title
- Clear Cart button (text, red)

**Cart Items**:
Each item shows:
- Product name (bold)
- Price per unit (₱X,XXX.XX)
- Quantity controls:
  - Minus button (-)
  - Quantity display (number)
  - Plus button (+)
- Subtotal (calculated)
- Remove button (X icon, red)

**Cart Summary**:
- Subtotal: ₱X,XXX.XX
- Tax (12%): ₱X,XXX.XX
- Discount (optional): -₱X,XXX.XX
- **Total**: ₱X,XXX.XX (large, bold)

**Discount Section**:
- Discount code input
- Apply button
- Manual discount (% or fixed amount)

**Payment Section**:
**Payment Method Selection** (radio buttons):
- Cash
- GCash
- PayPal
- Credit/Debit Card

**Cash Payment** (if selected):
- Amount Tendered input (₱)
- Change display (auto-calculated, large, green)

**Customer Selection** (optional):
- Search customer dropdown
- Selected customer display
- Guest checkout option

**Action Buttons**:
- Complete Sale (primary blue button, large, full-width)
- Park Sale (secondary button, saves for later)
- Cancel Sale (text button, red)

#### Receipt Modal (After Sale)
**Design**: Modal overlay, centered
**Content**:
- Store name and logo
- Receipt number
- Date & time
- Cashier name
- Itemized list
- Payment details
- Total amounts
**Actions**:
- Print Receipt (primary button)
- Email Receipt (secondary button)
- New Sale (text button)

### 6. Orders Page (`OrdersPage.tsx`)

#### Order Stats Cards
**Metrics** (4 cards):
- Pending Orders (yellow icon)
- Processing Orders (blue icon)
- Shipped Orders (purple icon)
- Delivered Orders (green icon)

#### Filters & Tabs
**Status Tabs**:
- All Orders
- Pending
- Processing
- Shipped
- Delivered
- Cancelled

**Filters**:
- Date range picker
- Customer search
- Payment method filter
- Amount range

#### Orders Table
**Columns**:
- Order ID (#ORD-XXXX)
- Customer Name & Email
- Date (formatted)
- Items (count)
- Total Amount (₱X,XXX.XX)
- Payment Method (badge)
- Status (colored badge)
- Actions (View, Edit, Track)

**Row Actions**:
- View Details (eye icon)
- Update Status (edit icon)
- Print Invoice (printer icon)
- Send Email (mail icon)

#### Order Detail Modal
**Sections**:
1. **Order Information**:
   - Order number
   - Date & time
   - Status with timeline
   - Payment status

2. **Customer Information**:
   - Name
   - Email
   - Phone
   - Shipping address
   - Billing address

3. **Order Items Table**:
   - Product name & image
   - SKU
   - Quantity
   - Unit price
   - Subtotal

4. **Order Summary**:
   - Subtotal
   - Tax
   - Shipping
   - Discount
   - **Total**

5. **Order Timeline**:
   - Ordered: date & time
   - Payment Confirmed: date & time
   - Processing: date & time
   - Shipped: date & time (with tracking number)
   - Delivered: date & time

6. **Actions**:
   - Update Status (dropdown + button)
   - Add Tracking Number
   - Send Update Email
   - Print Invoice
   - Refund Order (if applicable)

### 7. Customers Page (`CustomersPage.tsx`)

#### Customer Stats Cards
- Total Customers
- New This Month
- Active Customers (ordered recently)
- VIP Customers (high value)

#### Customer Table
**Columns**:
- Avatar (generated from initials)
- Name
- Email
- Phone
- Total Orders
- Total Spent (₱X,XXX.XX)
- Last Order Date
- Status (Active/Inactive badge)
- Actions (View, Edit, Delete)

#### Customer Detail View
**Tabs**:
1. **Profile**:
   - Personal information
   - Contact details
   - Addresses
   - Edit button

2. **Order History**:
   - List of all orders
   - Quick view modal
   - Reorder button

3. **Analytics**:
   - Total spent
   - Average order value
   - Favorite categories
   - Purchase frequency chart

**Quick Actions**:
- Send Email
- Add Note
- Mark as VIP
- View Full Details

### 8. Reports Page (`ReportsPage.tsx`)

#### Report Cards/Sections
**Available Reports**:

1. **Sales Report**:
   - Period selector (Daily, Weekly, Monthly, Custom)
   - Total sales (₱)
   - Sales chart (line/bar)
   - Top products
   - Sales by category
   - Export to PDF/Excel

2. **Inventory Report**:
   - Stock value
   - Low stock items
   - Out of stock items
   - Stock movement
   - Reorder recommendations
   - Export to PDF/Excel

3. **Customer Report**:
   - New customers
   - Customer lifetime value
   - Customer segmentation
   - Top customers
   - Export to PDF/Excel

4. **Product Performance**:
   - Best sellers
   - Slow moving items
   - Revenue by product
   - Product ratings
   - Export to PDF/Excel

#### Export Options
**Button Design**: Secondary button with icon
**Formats**:
- PDF (primary)
- Excel/CSV
- Print

**PDF Features**:
- Company header/logo
- Report title
- Date range
- Data tables
- Charts (as images)
- Footer with page numbers

### 9. Alerts Page (`AlertsPage.tsx`)

#### Alert Categories (Tabs)
- All Alerts
- Low Stock (amber)
- System Alerts (blue)
- Order Alerts (green)
- Critical (red)

#### Alert Cards
**Design**: Card with left border (color-coded by type)
**Content**:
- Alert icon (left)
- Alert title (bold)
- Alert message
- Timestamp ("2 hours ago")
- Actions:
  - View Details
  - Dismiss
  - Mark as Read

#### Alert Types

1. **Low Stock Alert** (Amber):
   - "Low Stock: [Product Name]"
   - Current stock: X units
   - Reorder point: Y units
   - Action: Reorder button

2. **Out of Stock Alert** (Red):
   - "Out of Stock: [Product Name]"
   - Action: Reorder button

3. **Order Alert** (Green):
   - "New Order: #ORD-XXXX"
   - Customer name
   - Amount
   - Action: View Order

4. **System Alert** (Blue):
   - System updates
   - Maintenance notices
   - Feature announcements

#### Notification Settings
**Options**:
- Email notifications (toggle)
- SMS notifications (toggle)
- Push notifications (toggle)
- Alert thresholds (number inputs)

### 10. Settings Page (`SettingsPage.tsx`)

#### Settings Tabs/Sections

1. **General Settings**:
   - Store Name
   - Store Address
   - Contact Information
   - Business Hours
   - Currency (₱ PHP)
   - Tax Rate (12%)

2. **Appearance**:
   - Theme (Light/Dark)
   - Primary Color
   - Logo Upload
   - Favicon Upload

3. **Inventory Settings**:
   - Low Stock Threshold (default)
   - Auto Reorder (toggle)
   - SKU Format
   - Barcode Format

4. **POS Settings**:
   - Receipt Template
   - Tax Settings
   - Payment Methods (enable/disable)
   - Printer Configuration

5. **Email Settings**:
   - SMTP Configuration
   - Email Templates:
     - Order Confirmation
     - Shipping Notification
     - Low Stock Alert
   - Test Email button

6. **SMS Settings**:
   - SMS Provider
   - API Credentials
   - SMS Templates
   - Test SMS button

7. **User Management**:
   - User list (table)
   - Roles: Admin, Manager, Cashier
   - Permissions
   - Add New User button

8. **Backup & Security**:
   - Database Backup
   - Export Data
   - Security Settings
   - Two-Factor Authentication

**Action Buttons** (bottom):
- Save Changes (primary blue button)
- Reset to Defaults (secondary button)
- Cancel (text button)

---

## Customer Interface

### Design Inspiration: EASYPC Style

The customer interface is inspired by EASYPC's clean, modern e-commerce design with emphasis on:
- Carousel banners
- Category navigation icons
- Product sections with clear labels
- Discount badges and promotions
- Trust indicators and testimonials

### 1. Header (`EasyPCHeader.tsx`)

#### Top Bar (Blue Band)
**Background**: Deep blue (bg-blue-800)
**Content**: Center-aligned tagline
- Text: "Your One-Stop Tech Shop" or similar
- White text color
- Small padding (py-2)

#### Main Header
**Background**: White
**Height**: ~80px
**Layout**: Flexbox, space-between

**Left Section**:
- Logo: "TechTrack" with icon
- Font: Bold, large
- Color: Blue

**Center Section** (Desktop):
- Search bar (prominent)
- Placeholder: "Search for products..."
- Search button (blue)
- Icon: Search (Lucide)

**Right Section**:
- Account icon + "Account" text
- Cart icon + badge (item count)
- Cart shows: mini cart preview on hover

#### Navigation Bar
**Background**: Light gray (bg-gray-50)
**Layout**: Horizontal menu

**Menu Items**:
- Home
- Laptops
- Desktops
- Components
- Peripherals
- Accessories
- Deals
- Build Your PC

**Design**:
- Hover: Blue underline or background
- Active: Blue text, bold

### 2. Home Page (`HomePage.tsx`)

#### Hero Carousel
**Component**: React Slick carousel
**Dimensions**: Full-width, 500-600px height
**Design**:
- Auto-play (5 seconds)
- Dots navigation (bottom)
- Arrow buttons (left/right)
- Smooth transitions

**Slide Content**:
- Large product image or lifestyle image
- Overlay text:
  - Main headline (large, bold)
  - Subheadline
  - CTA button: "Shop Now" (blue button)
- Gradient overlay (optional for text readability)

**Example Slides**:
1. Gaming laptops with discount
2. New arrivals showcase
3. Build your PC promotion
4. Seasonal sale

#### Category Icons Section
**Title**: "Shop by Category" (center-aligned)
**Layout**: Grid (6-8 columns on desktop, 3-4 on mobile)

**Category Card**:
- Circular icon background (light blue)
- Category icon (large, blue)
- Category name (below icon)
- Hover: Scale up slightly
- Click: Navigate to category page

**Categories**:
- Laptops (Laptop icon)
- Desktops (Monitor icon)
- Components (Cpu icon)
- Peripherals (Keyboard icon)
- Accessories (Mouse icon)
- Storage (HardDrive icon)
- Networking (Wifi icon)
- Gaming (Gamepad icon)

#### Featured Products Section
**Title**: "Featured Products" or "Best Sellers"
**Layout**: Grid (4 columns desktop, 2 mobile)

**Product Card** (detailed in CustomerShop section)

#### Deals Section
**Title**: "Hot Deals" with fire icon
**Background**: Light red or orange tint
**Layout**: Horizontal scrollable or grid

**Deal Card**:
- Product image
- Discount badge: "-20%" (red, top-right)
- Original price (strikethrough)
- Sale price (large, red)
- "Limited Time" tag
- Add to Cart button

#### New Arrivals Section
**Title**: "New Arrivals"
**Design**: Similar to featured products
**Badge**: "New" (green badge)

#### Banner Section (Mid-page)
**Type**: Full-width promotional banner
**Content**: 
- Split layout: Image (50%) + Text (50%)
- Promotion details
- CTA button
**Example**: "Build Your Dream PC" with custom PC builder link

#### Testimonials Section
**Title**: "What Our Customers Say"
**Layout**: 3 columns or carousel

**Testimonial Card**:
- Customer avatar (circular)
- Customer name (bold)
- Star rating (5 stars, yellow)
- Review text (italic or regular)
- Date
- Card background: Light gray
- Border or shadow

#### Features/Trust Indicators Section
**Layout**: 4 columns
**Design**: Icon + text

**Indicators**:
1. **Free Shipping** (Truck icon)
   - "Free shipping on orders over ₱2,000"
   
2. **Secure Payment** (Shield icon)
   - "100% secure payment methods"
   
3. **24/7 Support** (Headphones icon)
   - "Customer support available"
   
4. **Easy Returns** (RotateCcw icon)
   - "30-day return policy"

#### Newsletter Section
**Background**: Blue gradient
**Content**: Center-aligned
- Title: "Stay Updated"
- Subtitle: "Subscribe for exclusive deals"
- Email input + Subscribe button
- White text on blue background

#### Footer
**Background**: Dark gray (bg-gray-900)
**Text Color**: White/light gray
**Layout**: 4 columns

**Column 1 - About**:
- Logo
- Brief description
- Social media icons

**Column 2 - Customer Service**:
- Contact Us
- Shipping Info
- Returns
- FAQ

**Column 3 - Quick Links**:
- About Us
- Privacy Policy
- Terms & Conditions
- Sitemap

**Column 4 - Contact Info**:
- Address
- Phone
- Email
- Business hours

**Bottom Bar**:
- Copyright text
- Payment method icons

### 3. Customer Shop (`CustomerShop.tsx`)

#### Page Header
**Title**: Category name or "All Products"
**Breadcrumb**: Home > Category > Subcategory
**Results Count**: "Showing 1-20 of 150 products"

#### Filters Sidebar (Left, 20-25% width)
**Background**: White or light gray
**Sticky**: Scroll with page

**Filter Sections** (collapsible):

1. **Categories**:
   - Checkbox list
   - Sub-categories (nested)

2. **Price Range**:
   - Slider (min-max)
   - Number inputs
   - Apply button

3. **Brands**:
   - Checkbox list
   - Search brands input

4. **Specifications** (for tech products):
   - Processor (checkboxes)
   - RAM (checkboxes)
   - Storage (checkboxes)
   - GPU (checkboxes)

5. **Availability**:
   - In Stock (checkbox)
   - On Sale (checkbox)

**Actions**:
- Apply Filters (blue button)
- Clear All (text button)

#### Products Grid (Right, 75-80% width)

**Toolbar** (above grid):
- Sort By dropdown:
  - Featured
  - Price: Low to High
  - Price: High to Low
  - Newest
  - Best Selling
- View toggle: Grid / List
- Results per page: 20, 40, 60

**Product Card** (Grid View):
**Aspect Ratio**: Vertical card
**Design**:
- White background
- Border: 1px gray-200
- Rounded corners (rounded-xl)
- Hover: Shadow elevation
- Cursor: pointer

**Components**:
- **Image Container**:
  - Aspect ratio: 4:3 or 1:1
  - Product image
  - Badges (top-left):
    - "Sale" (red)
    - "New" (green)
    - "-15%" discount badge (red)
  - Quick view button (on hover, centered overlay)

- **Product Info**:
  - Brand (small, gray text)
  - Product name (2 lines max, truncated)
  - Rating: ★★★★☆ (4.5) with review count
  - Price section:
    - Original price (strikethrough if on sale)
    - Current price (large, bold, blue)
  - Stock status:
    - "In Stock" (green text + dot)
    - "Low Stock" (orange)
    - "Out of Stock" (red)

- **Action Button**:
  - Add to Cart (blue button, full-width)
  - Icon: ShoppingCart
  - Hover: Darker blue
  - Click: Add to cart animation + toast notification

**Product Card** (List View):
**Layout**: Horizontal
- Image (left, 150x150px)
- Info (center, expanded):
  - Name, brand, rating
  - Key specifications (bullet points)
  - Availability
- Price (right, aligned)
- Add to Cart button (right, aligned)

#### Pagination
**Position**: Bottom center
**Design**: Shadcn pagination component
- Previous / Next buttons
- Page numbers (1, 2, 3... 10)
- Current page highlighted (blue)

### 4. Product Detail Page (`ProductDetail.tsx`)

#### Layout
**Two-column layout** (Desktop):
- Left: Images (40%)
- Right: Product info & actions (60%)

#### Left Column - Product Images

**Main Image Display**:
- Large image (600x600px or larger)
- Zoom on hover (magnify glass cursor)
- Lightbox on click (full screen)

**Image Gallery** (below main image):
- Thumbnail strip (horizontal)
- 4-6 thumbnails
- Click to change main image
- Active thumbnail: Blue border
- Scroll arrows if more images

**Badges** (overlay on main image):
- Sale badge
- New badge
- Stock status

#### Right Column - Product Information

**Section 1 - Header**:
- Product name (large, bold)
- Brand (smaller, gray, clickable link)
- SKU: XXX-XXXX (small, gray)
- Rating: ★★★★☆ (4.5/5) with "(123 reviews)" link

**Section 2 - Pricing**:
- Original price (strikethrough, gray) - if on sale
- Current price (very large, bold, blue)
- You save: ₱X,XXX (XX%) - if on sale (green text)
- Tax: "Inclusive of VAT" (small text)

**Section 3 - Stock & Availability**:
- Stock status badge:
  - In Stock (green)
  - Only X left (orange)
  - Out of Stock (red)
- Delivery estimate: "Ships in 2-3 days"

**Section 4 - Quantity & Add to Cart**:
- Quantity selector:
  - Minus button (-)
  - Number input
  - Plus button (+)
  - Max: Current stock
- Add to Cart button (large, blue, full-width)
  - Icon: ShoppingCart
- Buy Now button (secondary, outline)
- Add to Wishlist (icon button, heart)

**Section 5 - Key Features**:
- Bullet points (3-5 main features)
- Icons next to each feature
- Example:
  - ✓ Intel Core i7 Processor
  - ✓ 16GB DDR4 RAM
  - ✓ 512GB NVMe SSD
  - ✓ NVIDIA RTX 3060

**Section 6 - Trust Badges**:
- Warranty: 1 Year (shield icon)
- Free Shipping (truck icon)
- Secure Payment (lock icon)
- Easy Returns (rotate icon)

#### Tabs Section (Full width below columns)

**Tab Navigation**:
- Description
- Specifications
- Reviews
- Shipping & Returns

**Tab 1 - Description**:
- Full product description
- Rich text formatting
- Images (if applicable)
- Video embed (if available)

**Tab 2 - Specifications**:
- Table format
- Category headers (bold)
- Spec name: Spec value
- Example:

| Processor | Intel Core i7-11800H |
| RAM | 16GB DDR4 3200MHz |
| Storage | 512GB NVMe SSD |
| Display | 15.6" FHD 144Hz |
| Graphics | NVIDIA RTX 3060 6GB |
| Battery | 90Wh |
| Weight | 2.3 kg |

**Tab 3 - Reviews**:
- Overall rating (large):
  - Average: 4.5/5
  - Star display
  - Total reviews: 123
  
- Rating breakdown:
  - 5 stars: [====== ] 70 (progress bar)
  - 4 stars: [===    ] 35
  - 3 stars: [==     ] 10
  - 2 stars: [=      ] 5
  - 1 star:  [=      ] 3

- Write a Review button (blue)

- Review cards:
  - User avatar
  - User name
  - Star rating
  - Review date
  - Review title (bold)
  - Review text
  - Helpful buttons: "Was this helpful? Yes (5) No (1)"
  - Images (if uploaded by user)

**Tab 4 - Shipping & Returns**:
- Shipping information
- Return policy
- Warranty details
- Contact support link

#### Related Products Section
**Title**: "You May Also Like"
**Layout**: Horizontal scrollable (4-5 products)
**Cards**: Same as product cards in shop

### 5. Cart & Checkout (`CheckoutPage.tsx`)

#### Cart Page

**Header**: "Shopping Cart (X items)"

**Cart Items Table**:
**Columns**:
- Product (image + name + specs)
- Price (unit price)
- Quantity (stepper: -, input, +)
- Subtotal (calculated)
- Remove (X button, red)

**Features**:
- Update quantity (auto-calculate)
- Remove item (confirm dialog)
- Stock validation (show error if exceeds stock)

**Cart Summary** (Sticky sidebar or bottom):
- Subtotal: ₱X,XXX.XX
- Shipping: ₱XXX.XX (or "Free")
- Tax (12%): ₱X,XXX.XX
- Discount: -₱XXX.XX (if applicable)
- **Total**: ₱X,XXX.XX (large, bold)

**Promo Code Section**:
- Input field
- Apply button
- Success/error message

**Action Buttons**:
- Continue Shopping (secondary, outline)
- Proceed to Checkout (primary blue, large)

#### Checkout Page

**Progress Indicator** (top):
Steps: Cart → Shipping → Payment → Confirmation
- Completed: Green checkmark
- Active: Blue highlight
- Upcoming: Gray

**Step 1 - Shipping Information**:
**Form Fields**:
- Email (for order updates)
- Full Name
- Phone Number
- Address Line 1
- Address Line 2 (optional)
- City
- Province/State
- Postal Code
- Country (dropdown, default: Philippines)

**Shipping Method** (radio buttons):
- Standard Shipping (₱100, 5-7 days)
- Express Shipping (₱200, 2-3 days)
- Same Day Delivery (₱500, if available)

**Buttons**:
- Back to Cart (secondary)
- Continue to Payment (primary blue)

**Step 2 - Payment Information**:

**Payment Method Selection** (radio cards):

1. **Cash on Delivery**:
   - Icon: Banknote
   - Description: "Pay when you receive"
   - Note: "Cash only, exact change appreciated"

2. **GCash**:
   - Icon: GCash logo
   - Description: "Pay via GCash app"
   - Instructions: "QR code will be sent to your email"

3. **PayPal**:
   - Icon: PayPal logo
   - Description: "Pay with PayPal"
   - Note: "Secure payment processing"

4. **Credit/Debit Card**:
   - Icon: CreditCard
   - Form fields:
     - Card Number
     - Cardholder Name
     - Expiry Date (MM/YY)
     - CVV
   - Card brand detection (Visa, Mastercard icons)

**Order Review** (sidebar):
- Order items summary (collapsed)
- Total amount
- Estimated delivery date
- Edit buttons (back to previous steps)

**Buttons**:
- Back to Shipping (secondary)
- Place Order (primary blue, large)

**Step 3 - Order Confirmation**:

**Success Message**:
- Green checkmark icon (large)
- "Order Placed Successfully!"
- Order number: #ORD-XXXX
- "Thank you for your purchase, [Name]!"

**Order Details**:
- Order number
- Order date
- Estimated delivery
- Shipping address
- Payment method

**Order Summary**:
- Items ordered (list)
- Total amount

**Next Steps**:
- "We've sent a confirmation email to [email]"
- "You can track your order using the link below"

**Buttons**:
- Track Order (blue button)
- Continue Shopping (secondary)
- Print Receipt (outline button)

### 6. Order Tracking (`OrderTracking.tsx`)

#### Search Section (if accessed publicly)
**Title**: "Track Your Order"
**Form**:
- Order Number input (#ORD-XXXX)
- Email input
- Track Order button (blue)

#### Order Status Display

**Order Header**:
- Order number (large, bold)
- Order date
- Estimated delivery date (bold, blue)

**Progress Bar/Timeline**:
**Design**: Horizontal stepper
**Steps**:
1. **Order Placed** (checkmark, green)
   - Date & time
   
2. **Payment Confirmed** (checkmark, green or current)
   - Date & time
   
3. **Processing** (current or pending)
   - "We're preparing your order"
   - Date & time (if completed)
   
4. **Shipped** (pending or current)
   - Tracking number (if available)
   - Courier: [Carrier Name]
   - Date & time (if completed)
   
5. **Delivered** (pending)
   - Expected: [Date]
   - Date & time (if completed)

**Visual Design**:
- Completed: Green circle with checkmark, green connecting line
- Current: Blue circle with pulsing animation, blue line
- Pending: Gray circle, gray dashed line

**Order Details Section**:

**Shipping Information Card**:
- Delivery address
- Contact phone
- Shipping method

**Payment Information Card**:
- Payment method
- Amount paid
- Payment status (Paid - green badge)

**Order Items**:
**List of products**:
- Product image (small)
- Product name
- Quantity: X
- Price: ₱X,XXX.XX

**Order Summary** (right sidebar or bottom):
- Subtotal
- Shipping
- Tax
- **Total**

**Actions**:
- Contact Support (if issue)
- Cancel Order (if applicable, early stages only)
- Download Invoice (PDF)

#### Delivery Updates Section
**Timeline cards** (reverse chronological):
- Update icon
- Update title
- Update description
- Date & time
- Location (if applicable)

**Example Updates**:
- "Order delivered" - [Date, Time]
- "Out for delivery" - [Date, Time]
- "Package arrived at local facility" - [City], [Date, Time]
- "In transit" - [Date, Time]
- "Shipped from warehouse" - [Date, Time]
- "Order confirmed" - [Date, Time]

---

## Component Architecture

### Core Structure

```
/App.tsx
├── Admin Routes
│   ├── /admin/dashboard → Dashboard.tsx
│   ├── /admin/products → ProductsPage.tsx (to be created, uses AddProductModal)
│   ├── /admin/inventory → InventoryPage.tsx
│   ├── /admin/pos → POSPage.tsx
│   ├── /admin/orders → OrdersPage.tsx
│   ├── /admin/customers → CustomersPage.tsx
│   ├── /admin/reports → ReportsPage.tsx
│   ├── /admin/alerts → AlertsPage.tsx
│   └── /admin/settings → SettingsPage.tsx
│
└── Customer Routes
    ├── / → HomePage.tsx
    ├── /shop → CustomerShop.tsx
    ├── /product/:id → ProductDetail.tsx
    ├── /checkout → CheckoutPage.tsx
    └── /track/:orderId → OrderTracking.tsx
```

### Shared Components

**Layout Components**:
- `AdminSidebar.tsx`: Sidebar navigation for admin
- `AdminTopBar.tsx`: Top navigation for admin
- `CustomerHeader.tsx`: Header for customer interface (deprecated in favor of EasyPCHeader)
- `EasyPCHeader.tsx`: EASYPC-styled header for customer interface

**Modal Components**:
- `AddProductModal.tsx`: Form for adding/editing products

**Utility Components**:
- Shadcn UI components in `/components/ui/`
- `ImageWithFallback.tsx`: Protected image component

### Data Flow

1. **Mock Data** (`/lib/mockData.ts`):
   - Products array
   - Orders array
   - Customers array
   - Categories array
   - Inventory data

2. **State Management** (React useState):
   - Cart state (in App.tsx, passed to customer components)
   - Filters state (in shop pages)
   - Modal state (open/close)
   - Form state (in forms)

3. **Future**: Supabase integration will replace mock data with real-time database

---

## Data Structure

### Product Schema
```typescript
{
  id: string;
  name: string;
  sku: string;
  category: string;
  brand: string;
  description: string;
  price: number;
  salePrice?: number;
  discount?: number;
  stock: number;
  lowStockThreshold: number;
  reorderPoint: number;
  images: string[];
  specifications: {
    [key: string]: string;
  };
  rating: number;
  reviewCount: number;
  featured: boolean;
  isNew: boolean;
  tags: string[];
  createdAt: Date;
  updatedAt: Date;
}
```

### Order Schema
```typescript
{
  id: string; // #ORD-1001
  customerId: string;
  customerName: string;
  customerEmail: string;
  customerPhone: string;
  items: [
    {
      productId: string;
      productName: string;
      quantity: number;
      price: number;
      subtotal: number;
    }
  ];
  subtotal: number;
  tax: number;
  shipping: number;
  discount: number;
  total: number;
  paymentMethod: 'cash' | 'gcash' | 'paypal' | 'card';
  paymentStatus: 'pending' | 'paid' | 'failed';
  shippingAddress: {
    line1: string;
    line2?: string;
    city: string;
    province: string;
    postalCode: string;
    country: string;
  };
  status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
  trackingNumber?: string;
  courier?: string;
  timeline: [
    {
      status: string;
      timestamp: Date;
      note?: string;
    }
  ];
  createdAt: Date;
  updatedAt: Date;
}
```

### Customer Schema
```typescript
{
  id: string;
  name: string;
  email: string;
  phone: string;
  addresses: [
    {
      type: 'shipping' | 'billing';
      line1: string;
      line2?: string;
      city: string;
      province: string;
      postalCode: string;
      country: string;
      isDefault: boolean;
    }
  ];
  totalOrders: number;
  totalSpent: number;
  lastOrderDate?: Date;
  isVIP: boolean;
  notes?: string;
  createdAt: Date;
}
```

### Inventory Transaction Schema
```typescript
{
  id: string;
  productId: string;
  type: 'add' | 'remove';
  quantity: number;
  reason: 'restock' | 'sale' | 'damage' | 'return' | 'adjustment';
  notes?: string;
  performedBy: string; // user ID
  timestamp: Date;
}
```

### Alert Schema
```typescript
{
  id: string;
  type: 'low_stock' | 'out_of_stock' | 'new_order' | 'system';
  severity: 'info' | 'warning' | 'critical';
  title: string;
  message: string;
  productId?: string;
  orderId?: string;
  isRead: boolean;
  isDismissed: boolean;
  createdAt: Date;
}
```

---

## Future Integration

### Supabase Implementation Plan

When connecting to Supabase, the following will be implemented:

#### 1. Database Tables
- `products`: Product catalog
- `orders`: Order records
- `order_items`: Order line items
- `customers`: Customer information
- `inventory_transactions`: Stock movement history
- `alerts`: System alerts and notifications
- `users`: Admin users and authentication
- `settings`: Application settings

#### 2. Authentication
- **Admin Auth**: Email/password with role-based access
  - Roles: Admin, Manager, Cashier
  - Permissions based on role
- **Customer Auth**: Optional (guest checkout available)
  - Email/password or social login
  - Save addresses, order history

#### 3. Real-time Features
- **Live Inventory Updates**: Multiple users see stock changes instantly
- **Order Status Updates**: Customers see real-time order progress
- **Low Stock Alerts**: Automatic alerts when stock drops below threshold
- **POS Sync**: Multiple POS terminals sync in real-time

#### 4. Storage
- **Product Images**: Supabase Storage for product photos
- **Documents**: Invoices, receipts (PDF)
- **User Uploads**: Customer review images

#### 5. Edge Functions
- **Email Notifications**: Order confirmations, shipping updates, low stock alerts
- **SMS Notifications**: Order updates, delivery notifications
- **Payment Processing**: Integration with payment gateways
- **PDF Generation**: Invoices, receipts, reports

#### 6. Row Level Security (RLS)
- Customers can only access their own orders
- Staff can access based on role permissions
- Public can view products but not modify

### Benefits of Supabase Integration
1. **Real Data Persistence**: Replace mock data with actual database
2. **Multi-user Support**: Multiple admins, staff, customers
3. **Real-time Updates**: Live inventory, orders, alerts
4. **Authentication**: Secure login for admin and customers
5. **File Storage**: Product images, documents
6. **Scalability**: Grow with your business
7. **Notifications**: Email and SMS alerts
8. **Reporting**: Advanced analytics with SQL queries

### Migration Path
1. Connect to Supabase project
2. Create database schema (tables)
3. Migrate mock data to Supabase
4. Update components to use Supabase client
5. Implement authentication
6. Add real-time subscriptions
7. Set up storage for images
8. Configure edge functions for notifications
9. Implement RLS policies
10. Test and deploy

---

## Key Features Summary

### Admin Dashboard ✓
- Comprehensive analytics dashboard with revenue, orders, products, and customer stats
- Low stock alerts and notifications system
- Recent orders overview
- Sales charts and visualizations

### Product Management ✓
- Add/edit/delete products with modal form
- Product grid and table views
- Category and brand management
- SKU generation
- Image uploads (prepared for Supabase Storage)
- Specifications management
- Stock tracking

### Inventory Management ✓
- Real-time stock levels
- Low stock alerts (amber warnings)
- Stock adjustment with reasons
- Inventory value calculations
- Reorder point tracking
- Stock history (prepared for database)

### POS System ✓
- Product search and barcode scanning
- Real-time cart management
- Multiple payment methods (Cash, GCash, PayPal, Card)
- Cash drawer with change calculation
- Customer selection
- Receipt generation
- Park sale functionality

### Order Management ✓
- Order listing with status filters
- Order detail view with timeline
- Status updates (Pending → Processing → Shipped → Delivered)
- Payment tracking
- Shipping information
- Invoice generation (prepared for PDF export)
- Customer notifications (prepared for email/SMS)

### Customer Management ✓
- Customer database with contact info
- Order history per customer
- Total spent calculations
- VIP customer tagging
- Customer analytics

### Reports & Analytics ✓
- Sales reports with date ranges
- Inventory reports
- Customer reports
- Product performance reports
- Export to PDF (prepared for implementation)
- Charts and visualizations

### Alerts System ✓
- Low stock alerts (amber)
- Out of stock alerts (red)
- New order notifications (green)
- System alerts (blue)
- Dismissible alerts
- Email/SMS notification settings (prepared for Supabase Edge Functions)

### Settings ✓
- General store settings
- Appearance customization
- Inventory configuration
- POS settings
- Email/SMS settings
- User management (prepared for Supabase Auth)
- Backup and security settings

### Customer Shop ✓
- EASYPC-inspired modern design
- Hero carousel with promotions
- Category navigation with icons
- Product grid with filters
- Product search
- Discount badges and sale tags
- Stock indicators
- Shopping cart with real-time updates
- Responsive design

### Product Detail ✓
- Image gallery with zoom
- Detailed specifications table
- Customer reviews (prepared for database)
- Related products
- Add to cart functionality
- Stock availability
- Trust badges
- Responsive layout

### Checkout Process ✓
- Multi-step checkout (Shipping → Payment → Confirmation)
- Address form with validation
- Multiple payment options
- Order summary
- Promo code application
- Order confirmation with tracking

### Order Tracking ✓
- Public order tracking by order number
- Visual progress bar/timeline
- Delivery updates
- Shipping information
- Contact support option
- Invoice download

---

## Design System Reference

### Color Variables (Tailwind)
```
Primary: blue-500, blue-600, blue-800
Success: green-500
Warning: amber-500
Danger: red-500
Neutral: gray-50 to gray-900
```

### Spacing Scale
- xs: 0.5rem (2px)
- sm: 0.75rem (3px)
- md: 1rem (4px)
- lg: 1.5rem (6px)
- xl: 2rem (8px)
- 2xl: 3rem (12px)

### Border Radius
- Default: rounded-xl (12px)
- Buttons: rounded-lg (8px)
- Pills: rounded-full

### Shadows
- Cards: shadow-sm
- Hover: shadow-md
- Modals: shadow-xl

### Transitions
- Default: transition-all duration-200
- Hover effects: ease-in-out

---

## Currency & Localization

**Currency**: Philippine Peso (₱)
**Format**: ₱X,XXX.XX
**Tax Rate**: 12% (VAT)
**Date Format**: "MMM dd, yyyy" (e.g., "Dec 15, 2024")
**Time Format**: 12-hour with AM/PM

---

## Responsive Breakpoints

- **Mobile**: < 640px (sm)
- **Tablet**: 640px - 1024px (sm to lg)
- **Desktop**: > 1024px (lg)

### Mobile Considerations
- Hamburger menu for navigation
- Stacked layouts
- Touch-friendly buttons (min 44x44px)
- Simplified tables (cards on mobile)
- Bottom navigation for customer shop

---

## Performance Optimizations (Prepared)

1. **Image Optimization**: Use WebP format, lazy loading
2. **Code Splitting**: Route-based code splitting
3. **Caching**: Cache product data with Supabase
4. **Pagination**: Limit products per page (20-40)
5. **Search Debouncing**: Delay search queries (300ms)
6. **Virtualization**: For large lists (inventory, orders)

---

## Accessibility (WCAG 2.1 AA)

- Semantic HTML elements
- Keyboard navigation support
- ARIA labels and roles
- Color contrast ratios (4.5:1 minimum)
- Focus indicators (blue outline)
- Alt text for images
- Form labels and error messages
- Screen reader friendly

---

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile Safari (iOS 12+)
- Chrome Mobile (Android 8+)

---

## Conclusion

TechTrack is a feature-complete tech shop and inventory management system with a clean, modern design inspired by EASYPC. The system includes comprehensive admin tools for managing products, inventory, orders, and customers, along with a customer-facing e-commerce interface with cart, checkout, and order tracking.

The application is currently built with mock data and is ready for Supabase integration to enable real-time data persistence, authentication, notifications, and multi-user support. The design system is consistent throughout with a blue, gray, and white palette, rounded corners, and soft shadows, creating a professional and trustworthy appearance.

All components are responsive and follow modern React best practices with TypeScript, Tailwind CSS, and Shadcn UI components.

---

**Document Version**: 1.0  
**Last Updated**: December 2024  
**Status**: Complete - Ready for Supabase Integration
