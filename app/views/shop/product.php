<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= html_escape($product['name'] ?? 'Product') ?> - TechTrack</title>
  <link rel="stylesheet" href="<?= site_url('public/assets/css/tailwind.css') ?>">
  <link rel="stylesheet" href="<?= site_url('public/assets/css/star-rating.css') ?>">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #F9FAFB;
      color: #111827;
    }
    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 24px;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #3B82F6;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 24px;
      transition: color 0.2s;
    }
    .back-link:hover { color: #2563EB; }
    .product-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .product-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
      padding: 40px;
    }
    .image-section {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .main-image-wrapper {
      background: #F9FAFB;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #E5E7EB;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 400px;
    }
    .main-image {
      width: 100%;
      height: auto;
      object-fit: contain;
      max-height: 500px;
    }
    .thumbnail-grid {
      display: flex;
      gap: 12px;
      overflow-x: auto;
      padding: 4px;
    }
    .thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border: 2px solid #E5E7EB;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
      flex-shrink: 0;
    }
    .thumbnail:hover, .thumbnail.active {
      border-color: #3B82F6;
      transform: scale(1.05);
    }
    .info-section h1 {
      font-size: 32px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 8px;
      line-height: 1.2;
    }
    .sku-badge {
      display: inline-block;
      background: #F3F4F6;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 13px;
      color: #6B7280;
      margin-bottom: 16px;
    }
    .price-section {
      margin: 24px 0;
      padding: 20px;
      background: #F9FAFB;
      border-radius: 12px;
      border: 1px solid #E5E7EB;
    }
    .price-current {
      font-size: 36px;
      font-weight: 700;
      color: #3B82F6;
    }
    .price-original {
      font-size: 18px;
      color: #9CA3AF;
      text-decoration: line-through;
      margin-left: 12px;
    }
    .savings-badge {
      display: inline-block;
      background: #FEE2E2;
      color: #DC2626;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      margin-left: 12px;
    }
    .description {
      font-size: 15px;
      line-height: 1.6;
      color: #6B7280;
      margin: 20px 0;
    }
    .stock-info {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      margin: 16px 0;
      padding: 12px;
      background: #F0FDF4;
      border: 1px solid #BBF7D0;
      border-radius: 8px;
      color: #15803D;
      font-weight: 500;
    }
    .stock-info.low { background: #FEF3C7; border-color: #FDE68A; color: #92400E; }
    .stock-info.out { background: #FEE2E2; border-color: #FECACA; color: #991B1B; }
    .cart-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-top: 32px;
      padding: 24px;
      background: #F9FAFB;
      border-radius: 12px;
    }
    .quantity-input {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .quantity-input label {
      font-size: 14px;
      font-weight: 500;
      color: #374151;
    }
    .quantity-input input {
      width: 120px;
      padding: 12px 16px;
      border: 1px solid #E5E7EB;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 500;
    }
    .quantity-input input:focus {
      outline: none;
      border-color: #3B82F6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .add-to-cart-btn {
      width: 100%;
      padding: 16px 24px;
      background: #3B82F6;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .add-to-cart-btn:hover {
      background: #2563EB;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    .specs-section {
      margin-top: 40px;
      padding: 0 40px 40px;
    }
    .specs-section h2 {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 20px;
    }
    .specs-table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #E5E7EB;
    }
    .specs-table tr {
      border-bottom: 1px solid #E5E7EB;
    }
    .specs-table tr:last-child { border-bottom: none; }
    .specs-table td {
      padding: 16px;
      font-size: 14px;
    }
    .specs-table td:first-child {
      font-weight: 600;
      color: #374151;
      background: #F9FAFB;
      width: 30%;
    }
    .specs-table td:last-child {
      color: #6B7280;
    }
    
    /* Form Styling Enhancements */
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      font-weight: 600;
      color: #374151;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #E5E7EB;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.2s;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #3B82F6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-group textarea {
      min-height: 120px;
      resize: vertical;
    }
    .form-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      margin-top: 24px;
    }
    .btn-cancel,
    .btn-submit-review {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-cancel {
      background: #F3F4F6;
      color: #6B7280;
    }
    .btn-cancel:hover {
      background: #E5E7EB;
      color: #374151;
    }
    .btn-submit-review {
      background: #3B82F6;
      color: white;
    }
    .btn-submit-review:hover {
      background: #2563EB;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Select Dropdown Enhancement */
    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
    }
    select:hover {
      border-color: #9CA3AF;
    }
    select:focus {
      outline: none;
      border-color: #3B82F6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    @media (max-width: 768px) {
      .product-grid {
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 20px;
      }
      .info-section h1 { font-size: 24px; }
      .price-current { font-size: 28px; }
      .specs-section { padding: 0 20px 20px; }
    }
  </style>
</head>
<body>
<div class="container">
  <a class="back-link" href="<?= site_url('shop') ?>">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Back to shop
  </a>

  <div class="product-container">
    <div class="product-grid">
      <!-- Image Section -->
      <div class="image-section">
        <?php 
          $images = $images ?? []; 
          $mainUrl = $images[0]['image_url'] ?? ($product['image_url'] ?? null);
          if ($mainUrl && !preg_match('/^https?:\/\//', $mainUrl)) {
            $mainUrl = base_url() . $mainUrl;
          }
        ?>
        <div class="main-image-wrapper">
          <?php if (!empty($mainUrl)): ?>
            <img id="mainImage" src="<?= html_escape($mainUrl) ?>" alt="<?= html_escape($product['name'] ?? '') ?>" class="main-image"/>
          <?php else: ?>
            <div style="color: #9CA3AF; font-size: 14px;">No image available</div>
          <?php endif; ?>
        </div>
        
        <?php if (count($images) > 1): ?>
        <div class="thumbnail-grid">
          <?php foreach ($images as $idx => $img): 
            $thumbUrl = $img['image_url'];
            if (!preg_match('/^https?:\/\//', $thumbUrl)) {
              $thumbUrl = base_url() . $thumbUrl;
            }
          ?>
            <img data-src="<?= html_escape($thumbUrl) ?>" 
                 src="<?= html_escape($thumbUrl) ?>" 
                 alt="thumbnail" 
                 class="thumbnail <?= $idx === 0 ? 'active' : '' ?>"/>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Product Info Section -->
      <div class="info-section">
        <h1><?= html_escape($product['name'] ?? '') ?></h1>
        <div class="sku-badge">SKU: <?= html_escape($product['sku'] ?? 'N/A') ?></div>
        
        <?php 
          $price = (float)($product['price'] ?? 0);
          $sale = isset($product['sale_price']) ? (float)$product['sale_price'] : 0;
          $hasSale = $sale > 0 && $sale < $price;
        ?>
        <div class="price-section">
          <?php if ($hasSale): ?>
            <div>
              <span class="price-current">₱<?= number_format($sale, 2) ?></span>
              <span class="price-original">₱<?= number_format($price, 2) ?></span>
              <?php 
                $discount = round((($price - $sale) / $price) * 100);
              ?>
              <span class="savings-badge">Save <?= $discount ?>%</span>
            </div>
          <?php else: ?>
            <span class="price-current">₱<?= number_format($price, 2) ?></span>
          <?php endif; ?>
        </div>

        <?php if (!empty($product['description'])): ?>
          <div class="description"><?= nl2br(html_escape($product['description'])) ?></div>
        <?php endif; ?>

        <?php 
          $stock = (int)($product['stock'] ?? 0);
          $stockClass = $stock > 10 ? '' : ($stock > 0 ? 'low' : 'out');
          $stockText = $stock > 10 ? "In Stock ({$stock} available)" : ($stock > 0 ? "Low Stock ({$stock} left)" : "Out of Stock");
        ?>
        <div class="stock-info <?= $stockClass ?>">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <?php if ($stock > 0): ?>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <?php else: ?>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <?php endif; ?>
          </svg>
          <?= $stockText ?>
        </div>

        <form id="addToCartForm" class="cart-form" method="post" action="<?= site_url('shop/add-to-cart') ?>">
          <input type="hidden" name="product_id" value="<?= html_escape($product['id'] ?? '') ?>">
          <div class="quantity-input">
            <label for="qty">Quantity</label>
            <input id="qty" type="number" name="qty" min="1" max="<?= $stock ?>" value="1" <?= $stock <= 0 ? 'disabled' : '' ?>>
          </div>
          <button class="add-to-cart-btn" type="submit" <?= $stock <= 0 ? 'disabled' : '' ?>>
            <svg style="display: inline-block; width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Add to Cart
          </button>
        </form>
      </div>
    </div>

    <?php if (!empty($specs)): ?>
    <div class="specs-section">
      <h2>Specifications</h2>
      <table class="specs-table">
        <tbody>
        <?php foreach ($specs as $s): ?>
          <tr>
            <td><?= html_escape($s['spec_name']) ?></td>
            <td><?= html_escape($s['spec_value']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <!-- ============================================
         RATING & REVIEW SYSTEM
         ============================================ -->
    <div class="specs-section" style="border-top: 1px solid #E5E7EB; padding-top: 40px;">
      <h2 style="font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
        <svg width="28" height="28" fill="#F59E0B" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        Ratings & Reviews
      </h2>
      <div class="rating-container">
        <!-- Rating Summary -->
        <div class="rating-header">
          <div class="rating-summary">
            <div class="rating-score" id="rating-score">0.0</div>
            <div class="rating-details">
              <div id="stars-display" class="stars-display"></div>
              <div class="rating-count" id="rating-count">0 ratings</div>
            </div>
          </div>
        </div>

        <!-- Interactive Star Rating -->
        <div id="star-rating-interactive" class="star-rating-interactive">
          <div class="star-rating-label">Rate this product:</div>
          <div id="stars-input" class="stars-input"></div>
        </div>

        <!-- Rating Distribution -->
        <div class="rating-distribution" id="rating-distribution"></div>
      </div>

      <!-- Write Review Form -->
      <div id="write-review-form" class="write-review-form" style="display: none; margin-top: 32px; padding: 28px; background: #FFFFFF; border: 2px solid #E5E7EB; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
        <h3 style="margin-bottom: 20px; font-size: 20px; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 10px;">
          <svg width="24" height="24" fill="#3B82F6" viewBox="0 0 20 20">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
          </svg>
          Write Your Review
        </h3>
        <form id="review-form">
          <div class="form-group">
            <label for="reviewer-name">Your Name *</label>
            <input type="text" id="reviewer-name" name="customer_name" required placeholder="Enter your name">
          </div>
          <div class="form-group">
            <label for="review-title">Review Title (Optional)</label>
            <input type="text" id="review-title" name="review_title" placeholder="Sum up your experience">
          </div>
          <div class="form-group">
            <label for="review-text">Your Review (Optional)</label>
            <textarea id="review-text" name="review_text" placeholder="Tell us what you think about this product..."></textarea>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="document.getElementById('write-review-form').style.display='none'">Cancel</button>
            <button type="submit" class="btn-submit-review">Submit Review</button>
          </div>
        </form>
      </div>

      <!-- Reviews Section -->
      <div class="review-section" style="margin-top: 40px;">
        <div class="review-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #E5E7EB;">
          <h3 style="font-size: 20px; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 10px;">
            <svg width="24" height="24" fill="#10B981" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
            </svg>
            Customer Reviews
          </h3>
          <div class="review-sort" style="display: flex; align-items: center; gap: 8px;">
            <label for="review-sort" style="font-size: 14px; color: #6B7280; font-weight: 500;">Sort by:</label>
            <select id="review-sort" style="padding: 8px 32px 8px 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; background: white; cursor: pointer; transition: all 0.2s;">
              <option value="recent">Most Recent</option>
              <option value="helpful">Most Helpful</option>
              <option value="highest">Highest Rating</option>
              <option value="lowest">Lowest Rating</option>
            </select>
          </div>
        </div>
        <div id="reviews-list"></div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden data for JavaScript -->
<input type="hidden" id="product-id" value="<?= html_escape($product['id'] ?? '') ?>">
<input type="hidden" id="site-url" value="<?= site_url() ?>">

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJ+Y7xvLh5tV7Q9zE2sonMSKcwk5v7fak1wXQ=" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="<?= site_url('public/assets/js/jquery.min.js') ?>"><\\/script>');</script>
<script>
  $(document).ready(function() {
    // Thumbnail click handler
    $('.thumbnail').on('click', function() {
      $('.thumbnail').removeClass('active');
      $(this).addClass('active');
      var src = $(this).data('src');
      if(src) {
        $('#mainImage').attr('src', src);
      }
    });

    // Form submission handler
    $('#addToCartForm').on('submit', function(e) {
      var qty = $('#qty').val();
      if (!qty || qty < 1) {
        e.preventDefault();
        alert('Please enter a valid quantity');
        return false;
      }
    });
  });
</script>
<script src="<?= site_url('public/assets/js/shop.js') ?>" defer></script>
<script src="<?= site_url('public/assets/js/rating-system.js') ?>"></script>
</body>
</html>