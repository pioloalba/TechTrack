<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= html_escape($product['name'] ?? 'Product') ?> - TechTrack</title>
  <link rel="stylesheet" href="<?= site_url('public/assets/css/tailwind.css') ?>">
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

        <form id="addToCartForm" class="cart-form" method="post" action="<?= site_url('checkout') ?>">
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
  </div>
</div>

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
</body>
</html>