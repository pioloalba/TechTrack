<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechTrack - Your Tech Store</title>
    <?php
        $fav = null;
        foreach (['favicon.ico','favicon.png'] as $f) {
            if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
        }
        if (!$fav) {
            foreach (['logo.png','logo.jpg','logo.jpeg','logo.svg'] as $f) {
                if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $fav = base_url() . 'public/assets/img/' . $f; break; }
            }
        }
    ?>
    <?php if ($fav): ?>
        <link rel="icon" href="<?= $fav ?>">
    <?php endif; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F9FAFB;
            color: #111827;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            color: #fff;
            padding: 16px 0;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .logo { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; display:flex; align-items:center; gap:10px; }
        .logo img { height: 32px; width: auto; display: block; }

        .search-bar {
            flex: 1;
            max-width: 600px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 48px 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
        }

        .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            color: #3B82F6;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 24px;
            color: #fff;
            font-size: 14px;
            position: relative;
            z-index: 10;
        }

        .header-actions a,
        .header-actions button {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
            z-index: 10;
        }

        /* Navigation */
        .nav {
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            padding: 0;
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            gap: 32px;
        }

        .nav-link {
            padding: 16px 0;
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: #3B82F6;
            border-bottom-color: #3B82F6;
        }

        /* Hero Carousel */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 80px 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            min-height: 500px;
        }

        .hero-content h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 12px;
        }

        .hero-price {
            font-size: 36px;
            font-weight: 700;
            margin: 24px 0;
        }

        .hero-btn {
            display: inline-block;
            padding: 14px 32px;
            background: #fff;
            color: #667eea;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
        }

        .hero-image {
            text-align: right;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
        }

        /* Categories */
        .categories {
            max-width: 1280px;
            margin: 60px auto;
            padding: 0 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 600;
        }

        .view-all {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 20px;
        }

        .category-card {
            text-align: center;
            padding: 24px 16px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            cursor: pointer;
            transition: all 0.2s;
        }

        .category-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .category-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: #EFF6FF;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3B82F6;
        }

        .category-name {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .category-count {
            font-size: 13px;
            color: #6B7280;
        }

        /* Category Sections */
        .category-section {
            max-width: 1280px;
            margin: 60px auto;
            padding: 0 24px;
        }

        .category-section.special-deals {
            max-width: 100%;
            margin: 60px 0;
        }

        .category-section.special-deals .section-header {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .category-section.special-deals .products-grid {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Featured Products */
        .featured {
            max-width: 1280px;
            margin: 60px auto;
            padding: 0 24px;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 60px;
        }

        .featured-banner {
            padding: 48px;
            border-radius: 16px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .featured-banner.desktop {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        }

        .featured-banner.laptop {
            background: linear-gradient(135deg, #EC4899 0%, #F59E0B 100%);
        }

        .featured-banner h3 {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .featured-banner p {
            opacity: 0.9;
            margin-bottom: 24px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E5E7EB;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
            border-color: #3B82F6;
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }

        .product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #111827;
            line-height: 1.4;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .stars {
            color: #FBBF24;
            font-size: 14px;
        }

        .product-price {
            font-size: 22px;
            font-weight: 700;
            color: #3B82F6;
            margin-bottom: 4px;
        }

        .product-stock {
            font-size: 13px;
            color: #059669;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn-primary {
            background: #3B82F6;
            color: #fff;
            width: 100%;
        }

        .btn-primary:hover {
            background: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #374151;
            width: 100%;
            border: 1px solid #E5E7EB;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        /* Top Sellers */
        .top-sellers {
            max-width: 1280px;
            margin: 60px auto;
            padding: 0 24px;
        }

        .top-sellers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .seller-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E5E7EB;
            position: relative;
        }

        .hot-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #EF4444;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .seller-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .seller-content {
            padding: 20px;
        }

        .seller-name {
            font-weight: 600;
            margin-bottom: 12px;
        }

        .save-badge {
            background: #EF4444;
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        /* Testimonials */
        .testimonials {
            max-width: 1280px;
            margin: 80px auto;
            padding: 0 24px;
            text-align: center;
        }

        .testimonials h2 {
            font-size: 36px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .testimonials-subtitle {
            color: #6B7280;
            margin-bottom: 48px;
            font-size: 18px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .testimonial-card {
            background: #fff;
            padding: 28px;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            text-align: left;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .testimonial-stars {
            color: #FBBF24;
            margin-bottom: 16px;
            font-size: 18px;
        }

        .testimonial-text {
            color: #4B5563;
            line-height: 1.7;
            margin-bottom: 20px;
            font-style: italic;
        }

        .testimonial-author {
            font-weight: 600;
            color: #111827;
        }

        .testimonial-date {
            font-size: 13px;
            color: #9CA3AF;
        }

        /* Footer */
        .footer {
            background: #1F2937;
            color: #D1D5DB;
            padding: 60px 0 24px;
            margin-top: 80px;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-section h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .footer-section p {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #D1D5DB;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid #374151;
            color: #9CA3AF;
        }

        /* User Profile Dropdown */
        .user-profile {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            color: #111827;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-profile-btn:hover {
            background: #f9fafb;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #3B82F6;
        }

        .user-profile-btn span {
            color: #111827;
            font-weight: 500;
        }

        .user-profile-btn svg {
            color: #6B7280;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #FFFFFF;
            letter-spacing: 0.5px;
        }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            display: none;
            z-index: 100;
            overflow: hidden;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown-header {
            padding: 16px;
            border-bottom: 1px solid #E5E7EB;
        }

        .user-dropdown-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .user-dropdown-email {
            font-size: 13px;
            color: #6B7280;
        }

        .user-dropdown-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #DBEAFE;
            color: #1E40AF;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            margin-top: 6px;
        }

        .user-dropdown-menu {
            padding: 8px;
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #1F2937;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.5;
        }

        .user-dropdown-item span {
            color: #1F2937;
            display: inline-block;
        }

        .user-dropdown-item:hover {
            background: #EFF6FF;
            color: #1E40AF;
        }

        .user-dropdown-item:hover span {
            color: #1E40AF;
        }

        .user-dropdown-item:hover .user-dropdown-icon {
            color: #3B82F6;
        }

        .user-dropdown-item.logout {
            color: #DC2626;
            font-weight: 600;
        }

        .user-dropdown-item.logout:hover {
            background: #FEE2E2;
            color: #991B1B;
        }

        .user-dropdown-icon {
            width: 20px;
            height: 20px;
            color: #6B7280;
            flex-shrink: 0;
        }

        .user-dropdown-item.logout .user-dropdown-icon {
            color: #DC2626;
        }

        .user-dropdown-item.logout:hover .user-dropdown-icon {
            color: #991B1B;
        }

        /* Cart Badge */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #EF4444;
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-badge.hidden {
            display: none;
        }

        /* Loading spinner for buttons */
        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
            position: relative;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <?php 
                    $logoUrl = null;
                    foreach (['logo.svg','logo.png','logo.jpg','logo.jpeg'] as $f) {
                        if (is_file(ROOT_DIR . PUBLIC_DIR . '/assets/img/' . $f)) { $logoUrl = base_url() . 'public/assets/img/' . $f; break; }
                    }
                ?>
                <?php if ($logoUrl): ?>
                    <img src="<?= $logoUrl ?>" alt="TechTrack" />
                <?php endif; ?>
                <span>TechTrack</span>
            </div>
            <div class="search-bar">
                <form method="get" action="<?= site_url('shop') ?>" style="display:flex;width:100%;">
                    <input type="text" name="search" class="search-input" placeholder="Search for products..." value="<?= html_escape($search_query ?? '') ?>">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <div class="header-actions">
                <button type="button" id="adminBtn" style="background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;border:none;color:#fff;font-size:14px;display:flex;align-items:center;gap:6px;" title="Login to access Admin">
                    👨‍💼 Admin
                </button>
                <button type="button" id="cashierBtn" style="background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;border:none;color:#fff;font-size:14px;display:flex;align-items:center;gap:6px;" title="Login to access Cashier">
                    💳 Cashier
                </button>
                <a href="#" id="openLocation">📍 Location</a>
                <a href="#">🚚 Delivery</a>
                <a href="#">❤️</a>
                <a href="<?= site_url('checkout') ?>" style="position:relative;">
                    🛒
                    <span id="cartBadge" style="display:none;position:absolute;top:-8px;right:-8px;background:#EF4444;color:#fff;border-radius:50%;width:20px;height:20px;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;">0</span>
                </a>
                <?php 
                    $cust = isset($_SESSION['customer']) ? $_SESSION['customer'] : null; 
                ?>
                <?php if ($cust): ?>
                    <div class="user-profile">
                        <button class="user-profile-btn" id="userProfileBtn">
                            <div class="user-avatar">
                                <?= strtoupper(substr($cust['name'] ?? 'C', 0, 2)) ?>
                            </div>
                            <span><?= htmlspecialchars($cust['name'] ?? 'Customer User') ?></span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-name"><?= htmlspecialchars($cust['name'] ?? 'Customer User') ?></div>
                                <div class="user-dropdown-email"><?= htmlspecialchars($cust['email'] ?? 'customer@email.com') ?></div>
                                <span class="user-dropdown-badge">customer</span>
                            </div>
                            <div class="user-dropdown-menu">
                                <a href="#" class="user-dropdown-item">
                                    <svg class="user-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span>My Orders</span>
                                </a>
                                <a href="#" class="user-dropdown-item">
                                    <svg class="user-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Settings</span>
                                </a>
                                <a href="<?= site_url('shop/logout') ?>" class="user-dropdown-item logout">
                                    <svg class="user-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= site_url('shop/login') ?>">👤 Login / Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Location Modal -->
    <div id="locationModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; width:90%; max-width:800px; border-radius:12px; overflow:hidden; box-shadow:0 20px 40px rgba(2,6,23,.2);">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-bottom:1px solid #e5e7eb;">
                <strong>Select your location</strong>
                <button id="closeLocation" style="border:none; background:transparent; font-size:20px; cursor:pointer;">×</button>
            </div>
            <div style="display:grid; grid-template-columns:1fr 340px; gap:0; max-height:70vh;">
                <div style="min-height:360px;">
                    <?php 
                        $defLat = null; $defLng = null; $defAddr = '';
                        if (!empty($saved_location)) {
                            $defLat = isset($saved_location['lat']) ? (float)$saved_location['lat'] : null;
                            $defLng = isset($saved_location['lng']) ? (float)$saved_location['lng'] : null;
                            $defAddr = $saved_location['address'] ?? '';
                        } elseif (!empty($store_settings) && isset($store_settings['store_lat']) && isset($store_settings['store_lng'])) {
                            $defLat = (float)$store_settings['store_lat'];
                            $defLng = (float)$store_settings['store_lng'];
                            $defAddr = $store_settings['store_address'] ?? '';
                        }
                        if ($defLat !== null && $defLng !== null) {
                            $d = 0.02;
                            $bbox = ($defLng-$d) . '%2C' . ($defLat-$d) . '%2C' . ($defLng+$d) . '%2C' . ($defLat+$d);
                            $mapSrc = 'https://www.openstreetmap.org/export/embed.html?bbox=' . $bbox . '&layer=mapnik&marker=' . $defLat . '%2C' . $defLng;
                        } else {
                            $mapSrc = 'https://www.openstreetmap.org/export/embed.html?bbox=120.90%2C14.35%2C121.10%2C14.65&layer=mapnik';
                        }
                    ?>
                    <iframe id="mapFrame" title="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="width:100%; height:100%; border:0;"
                        src="<?= $mapSrc ?>">
                    </iframe>
                </div>
                <div style="padding:14px; border-left:1px solid #e5e7eb; display:flex; flex-direction:column; gap:10px;">
                    <label style="font-weight:600;">Address</label>
                    <input id="addressInput" type="text" value="<?= htmlspecialchars($defAddr) ?>" placeholder="Enter address or drag on map" style="border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px;">
                    <div style="display:flex; gap:10px;">
                        <input id="latInput" type="text" value="<?= $defLat !== null ? htmlspecialchars(number_format($defLat, 6, '.', '')) : '' ?>" placeholder="Lat" style="flex:1; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px;">
                        <input id="lngInput" type="text" value="<?= $defLng !== null ? htmlspecialchars(number_format($defLng, 6, '.', '')) : '' ?>" placeholder="Lng" style="flex:1; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px;">
                    </div>
                    <button id="useMyLocation" style="border:1px solid #e5e7eb; background:#fff; padding:10px 12px; border-radius:8px; cursor:pointer;">Use my current location</button>
                    <button id="saveLocation" style="background:#3b82f6; color:#fff; border:none; padding:10px 12px; border-radius:8px; cursor:pointer; font-weight:700;">Save Location</button>
                    <?php if (!empty($saved_location)): ?>
                        <div style="font-size:13px; color:#475569;">Current: <?= htmlspecialchars($saved_location['address'] ?? ( ($saved_location['lat'] ?? '') . ',' . ($saved_location['lng'] ?? '') )) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-container">
            <a href="<?= site_url('shop') ?>" class="nav-link" data-nav="home">Home</a>
            <a href="<?= site_url('shop') ?>" class="nav-link" data-nav="products">Products</a>
            <a href="<?= site_url('shop/desktops') ?>" class="nav-link" data-nav="desktop">Desktop</a>
            <a href="<?= site_url('shop/laptops') ?>" class="nav-link" data-nav="laptop">Laptop</a>
            <a href="<?= site_url('shop/build-pc') ?>" class="nav-link" data-nav="pc">Build a PC</a>
            <a href="#accessories-section" class="nav-link" data-nav="accessories" data-scroll="accessories">Accessories</a>
            <a href="<?= site_url('shop?search=Brands') ?>" class="nav-link" data-nav="brands">Brands</a>
            <a href="<?= site_url('shop/rewards') ?>" class="nav-link" data-nav="rewards">Rewards</a>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Gaming Desktop PCs</h1>
                <p>High-Performance Gaming Systems</p>
                <div class="hero-price">Starting at ₱45,999</div>
                <a href="#" class="hero-btn">Pre-Order Now</a>
            </div>
            <div class="hero-image">
                <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=Gaming+Setup" alt="Gaming PC">
            </div>
        </div>
    </section>

    <!-- Shop by Category -->
    <section class="categories">
        <div class="section-header">
            <h2 class="section-title">Shop by Category</h2>
            <a href="#" class="view-all">View All ›</a>
        </div>
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-icon">🖥️</div>
                <div class="category-name">Processor</div>
                <div class="category-count">45 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">⚡</div>
                <div class="category-name">Motherboard</div>
                <div class="category-count">32 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">🖥️</div>
                <div class="category-name">Graphics Card</div>
                <div class="category-count">28 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">💾</div>
                <div class="category-name">Memory</div>
                <div class="category-count">56 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">💿</div>
                <div class="category-name">SSD</div>
                <div class="category-count">64 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">🔌</div>
                <div class="category-name">Power Supply</div>
                <div class="category-count">38 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">📦</div>
                <div class="category-name">PC Case</div>
                <div class="category-count">42 items</div>
            </div>
            <div class="category-card">
                <div class="category-icon">💻</div>
                <div class="category-name">Laptop</div>
                <div class="category-count">72 items</div>
            </div>
        </div>
    </section>

    <?php
    // Filter products by category
    $accessoriesProducts = array_filter($products ?? [], function($p) {
        return stripos($p['category'] ?? '', 'Accessor') !== false;
    });
    $laptopProducts = array_filter($products ?? [], function($p) {
        return stripos($p['category'] ?? '', 'Laptop') !== false;
    });
    $desktopProducts = array_filter($products ?? [], function($p) {
        return stripos($p['category'] ?? '', 'Desktop') !== false;
    });
    ?>

    <!-- Accessories Section -->
    <section class="category-section" id="accessories-section">
        <div class="section-header">
            <h2 class="section-title">Accessories</h2>
            <a href="<?= site_url('shop?search=Accessories') ?>" class="view-all">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            $count = 0;
            foreach ($accessoriesProducts as $product): 
                if ($count >= 8) break; // Limit to 8 products
                $img = $product_images[$product['id']] ?? null;
                $count++;
            ?>
                <div class="product-card">
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($accessoriesProducts)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#6b7280;">
                    No accessories products available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Laptop Section -->
    <section class="category-section" id="laptop-section">
        <div class="section-header">
            <h2 class="section-title">Laptops</h2>
            <a href="<?= site_url('shop?search=Laptop') ?>" class="view-all">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            $count = 0;
            foreach ($laptopProducts as $product): 
                if ($count >= 8) break; // Limit to 8 products
                $img = $product_images[$product['id']] ?? null;
                $count++;
            ?>
                <div class="product-card">
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($laptopProducts)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#6b7280;">
                    No laptop products available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Desktop Section -->
    <section class="category-section" id="desktop-section">
        <div class="section-header">
            <h2 class="section-title">Desktop Computers</h2>
            <a href="<?= site_url('shop?search=Desktop') ?>" class="view-all">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            $count = 0;
            foreach ($desktopProducts as $product): 
                if ($count >= 8) break; // Limit to 8 products
                $img = $product_images[$product['id']] ?? null;
                $count++;
            ?>
                <div class="product-card">
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($desktopProducts)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#6b7280;">
                    No desktop products available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Special Deals Section -->
    <section class="category-section special-deals" style="background:#FEF3C7;padding:48px 24px;">
        <div class="section-header">
            <h2 class="section-title" style="color:#92400E;">🔥 Special Deals</h2>
            <a href="<?= site_url('shop') ?>" class="view-all" style="color:#92400E;">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            // Get products with stock and sort by price for deals
            $specialDeals = array_filter($products ?? [], function($p) {
                return ($p['stock'] ?? 0) > 0;
            });
            $specialDeals = array_slice($specialDeals, 0, 4);
            
            foreach ($specialDeals as $product): 
                $img = $product_images[$product['id']] ?? null;
                $originalPrice = ($product['price'] ?? 0) * 1.15; // Show 15% discount
            ?>
                <div class="product-card">
                    <span class="hot-badge" style="position:absolute;top:12px;right:12px;background:#EF4444;color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;z-index:1;">SALE</span>
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;margin:8px 0;">
                                <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                                <span style="text-decoration:line-through;color:#9CA3AF;font-size:14px;">₱<?= number_format($originalPrice, 0) ?></span>
                                <span style="background:#10B981;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">SAVE ₱<?= number_format($originalPrice - ($product['price'] ?? 0), 0) ?></span>
                            </div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Best Sellers Section -->
    <section class="category-section" id="best-sellers-section">
        <div class="section-header">
            <h2 class="section-title">🏆 Best Sellers</h2>
            <a href="<?= site_url('shop') ?>" class="view-all">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            // Get top selling products (products with lower stock = more sold)
            $bestSellers = $products ?? [];
            usort($bestSellers, function($a, $b) {
                return ($a['stock'] ?? 999) - ($b['stock'] ?? 999);
            });
            $bestSellers = array_slice($bestSellers, 0, 8);
            
            foreach ($bestSellers as $product): 
                $img = $product_images[$product['id']] ?? null;
            ?>
                <div class="product-card">
                    <span style="position:absolute;top:12px;left:12px;background:#3B82F6;color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;z-index:1;">TOP</span>
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                                <span style="font-size:13px;color:#6B7280;margin-left:4px;">(<?= rand(50, 200) ?>)</span>
                            </div>
                            <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="category-section" id="new-arrivals-section">
        <div class="section-header">
            <h2 class="section-title">✨ New Arrivals</h2>
            <a href="<?= site_url('shop') ?>" class="view-all">View All ›</a>
        </div>
        <div class="products-grid">
            <?php 
            // Get latest products (reverse order assuming newest have higher IDs)
            $newArrivals = $products ?? [];
            usort($newArrivals, function($a, $b) {
                return ($b['id'] ?? 0) - ($a['id'] ?? 0);
            });
            $newArrivals = array_slice($newArrivals, 0, 8);
            
            foreach ($newArrivals as $product): 
                $img = $product_images[$product['id']] ?? null;
            ?>
                <div class="product-card">
                    <span style="position:absolute;top:12px;right:12px;background:#10B981;color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;z-index:1;">NEW</span>
                    <?php if ($img): ?>
                        <img src="<?= html_escape($img) ?>" alt="<?= html_escape($product['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?= html_escape($product['name'] ?? '') ?></div>
                            <div class="product-rating">
                                <span class="stars">⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="product-price">₱<?= number_format((float)($product['price'] ?? 0), 0) ?></div>
                            <div class="product-stock"><?= (int)($product['stock'] ?? 0) ?> in stock</div>
                        </div>
                        <div class="product-actions">
                            <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-secondary">View Product</a>
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="<?= $product['id'] ?>" data-product-name="<?= html_escape($product['name']) ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>💬 What Customers Say About Us</h2>
        <p class="testimonials-subtitle">Trusted by thousands of satisfied customers nationwide</p>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Excellent service and fast delivery! Got my gaming PC in perfect condition. The staff helped me choose the right components for my budget."</p>
                <div class="testimonial-author">John Martinez</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Manila • 2 days ago</div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Best prices in town! Staff is very helpful and knowledgeable. They explained everything clearly and made sure I got exactly what I needed."</p>
                <div class="testimonial-author">Maria Santos</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Quezon City • 1 week ago</div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Built my dream PC here. Quality parts and great customer support! The warranty service is also top-notch. Highly recommended!"</p>
                <div class="testimonial-author">David Chen</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Makati • 2 weeks ago</div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Amazing experience! Will definitely recommend to friends and family. The online ordering system is smooth and delivery was super quick."</p>
                <div class="testimonial-author">Sarah Johnson</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Pasig • 3 weeks ago</div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Great selection of products at competitive prices. The checkout process was easy and my order arrived on time. Very satisfied customer!"</p>
                <div class="testimonial-author">Ramon Valdez</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Cebu • 1 month ago</div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-text">"Professional service from start to finish. They answered all my questions patiently. My laptop is running perfectly. 10/10 would buy again!"</p>
                <div class="testimonial-author">Anna Rodriguez</div>
                <div class="testimonial-date" style="color:#9CA3AF;font-size:13px;">Davao • 1 month ago</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>About TechTrack</h3>
                <p>Your trusted source for computer hardware, laptops, and gaming accessories.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Customer Service</h3>
                <ul class="footer-links">
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="#">Warranty</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <p>Email: info@techtrack.com</p>
                <p>Phone: +1 (555) 123-4567</p>
                <p>Address: 123 Tech Street<br>San Francisco, CA 94102</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
                © 2025 TechTrack. All rights reserved.
            </div>
        </div>
    </footer>

        <script>
        (function(){
            // Location Modal
            const modal = document.getElementById('locationModal');
            const openBtn = document.getElementById('openLocation');
            const closeBtn = document.getElementById('closeLocation');
            const useMy = document.getElementById('useMyLocation');
            const saveBtn = document.getElementById('saveLocation');
            const latEl = document.getElementById('latInput');
            const lngEl = document.getElementById('lngInput');
            const addrEl = document.getElementById('addressInput');

            function open(){ modal.style.display = 'flex'; }
            function close(){ modal.style.display = 'none'; }
            if(openBtn) openBtn.addEventListener('click', function(e){ e.preventDefault(); open(); });
            if(closeBtn) closeBtn.addEventListener('click', function(){ close(); });

            if(useMy){
                useMy.addEventListener('click', function(){
                    if(!navigator.geolocation){ alert('Geolocation not supported'); return; }
                    navigator.geolocation.getCurrentPosition(function(pos){
                        const { latitude, longitude } = pos.coords;
                        latEl.value = latitude.toFixed(6);
                        lngEl.value = longitude.toFixed(6);
                        // Update map frame center roughly by building bbox around coords
                        const d = 0.02; // small bounding box
                        const url = `https://www.openstreetmap.org/export/embed.html?bbox=${(longitude-d).toFixed(6)}%2C${(latitude-d).toFixed(6)}%2C${(longitude+d).toFixed(6)}%2C${(latitude+d).toFixed(6)}&layer=mapnik&marker=${latitude.toFixed(6)}%2C${longitude.toFixed(6)}`;
                        document.getElementById('mapFrame').src = url;
                    }, function(err){ alert('Unable to get location: ' + err.message); });
                });
            }

            if(saveBtn){
                saveBtn.addEventListener('click', function(){
                    const lat = latEl.value.trim();
                    const lng = lngEl.value.trim();
                    const address = addrEl.value.trim();
                    if(!lat || !lng){ alert('Please provide latitude and longitude (or click "Use my current location").'); return; }
                    fetch('<?= site_url('shop/save-location') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ lat, lng, address })
                    }).then(r => r.json()).then(data => {
                        if(data && data.success){
                            alert('Location saved.');
                            close();
                            location.reload();
                        } else {
                            alert((data && data.message) || 'Failed to save location');
                        }
                    }).catch(() => alert('Network error'));
                });
            }

            // User Profile Dropdown
            const userProfileBtn = document.getElementById('userProfileBtn');
            const userDropdown = document.getElementById('userDropdown');

            if (userProfileBtn && userDropdown) {
                userProfileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && e.target !== userProfileBtn) {
                        userDropdown.classList.remove('show');
                    }
                });

                // Prevent dropdown from closing when clicking inside
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Cart functionality
            const cartBadge = document.getElementById('cartBadge');

            // Update cart badge on page load
            function updateCartBadge() {
                fetch('<?= site_url('shop/get-cart') ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.cart_count > 0) {
                            cartBadge.textContent = data.cart_count;
                            cartBadge.style.display = 'flex';
                        } else {
                            cartBadge.style.display = 'none';
                        }
                    })
                    .catch(err => console.error('Error loading cart:', err));
            }

            // Load cart count on page load
            updateCartBadge();

            // Add to cart functionality
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    const originalText = this.innerHTML;
                    
                    // Disable button and show loading
                    this.disabled = true;
                    this.classList.add('btn-loading');
                    this.innerHTML = 'Adding...';
                    
                    fetch('<?= site_url('shop/add-to-cart') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart badge
                            if (data.cart_count > 0) {
                                cartBadge.textContent = data.cart_count;
                                cartBadge.style.display = 'flex';
                            }
                            
                            // Show success message
                            this.innerHTML = '✓ Added';
                            this.style.background = '#10B981';
                            
                            // Reset button after 2 seconds
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.style.background = '';
                                this.disabled = false;
                                this.classList.remove('btn-loading');
                            }, 2000);
                            
                            // Show notification
                            showNotification('Added to cart: ' + productName, 'success');
                        } else {
                            // Show error
                            alert(data.message || 'Failed to add to cart');
                            this.innerHTML = originalText;
                            this.disabled = false;
                            this.classList.remove('btn-loading');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        this.innerHTML = originalText;
                        this.disabled = false;
                        this.classList.remove('btn-loading');
                    });
                });
            });

            // Simple notification function
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 16px 24px;
                    background: ${type === 'success' ? '#10B981' : '#3B82F6'};
                    color: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10000;
                    font-size: 14px;
                    font-weight: 500;
                    animation: slideIn 0.3s ease-out;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        })();

        // Admin and Cashier button handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation active state handler
            const currentUrl = window.location.href;
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(function(link) {
                link.classList.remove('active');
            });
            
            // Set active based on URL
            if (searchParam) {
                const searchLower = searchParam.toLowerCase();
                const matchingLink = document.querySelector(`.nav-link[data-nav="${searchLower}"]`);
                if (matchingLink) {
                    matchingLink.classList.add('active');
                }
            } else {
                // No search param, activate "Home"
                const homeLink = document.querySelector('.nav-link[data-nav="home"]');
                if (homeLink) {
                    homeLink.classList.add('active');
                }
            }

            // Navigation scroll handlers for Accessories, Laptop, Desktop
            document.querySelectorAll('.nav-link[data-scroll]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionName = this.getAttribute('data-scroll');
                    const targetSection = document.getElementById(sectionName + '-section');
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Update active state
                        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });

            const adminBtn = document.getElementById('adminBtn');
            const cashierBtn = document.getElementById('cashierBtn');
            
            if (adminBtn) {
                adminBtn.addEventListener('click', function(e) {
                    console.log('Admin button clicked');
                    window.location.href = '<?= base_url() ?>admin-login';
                });
            }
            
            if (cashierBtn) {
                cashierBtn.addEventListener('click', function(e) {
                    console.log('Cashier button clicked');
                    window.location.href = '<?= base_url() ?>cashier-login';
                });
            }

            // Pre-Order Now button handler
            const heroBtn = document.querySelector('.hero-btn');
            if (heroBtn) {
                heroBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Pre-Order Now button clicked');
                    window.location.href = '<?= base_url() ?>shop?search=' + encodeURIComponent('Gaming');
                });
            }

            // View All buttons handler
            document.querySelectorAll('.view-all').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('View All button clicked');
                    
                    // Scroll to products section
                    const productsSection = document.querySelector('.products-grid');
                    if (productsSection) {
                        productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        window.location.href = '<?= base_url() ?>shop';
                    }
                });
            });

            // Category cards handler
            document.querySelectorAll('.category-card').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Category card clicked');
                    
                    const categoryName = this.querySelector('.category-name');
                    if (categoryName) {
                        const category = categoryName.textContent.trim();
                        window.location.href = '<?= base_url() ?>shop?search=' + encodeURIComponent(category);
                    }
                });
                
                // Add pointer cursor
                card.style.cursor = 'pointer';
            });

            // Featured banner handler (Desktop - redirects, Laptop - scrolls)
            document.querySelectorAll('.featured-banner[data-category]').forEach(function(banner) {
                banner.addEventListener('click', function(e) {
                    e.preventDefault();
                    const category = this.getAttribute('data-category');
                    console.log('Featured banner clicked:', category);
                    window.location.href = '<?= base_url() ?>shop?search=' + encodeURIComponent(category);
                });
            });

        });
        </script>
</body>
</html>