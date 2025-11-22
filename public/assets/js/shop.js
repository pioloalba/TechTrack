/* TechTrack Shop JS (jQuery) */
(function($){
  $(function(){
    // Add to cart demo: intercept product form if present
    $(document).on('submit','#addToCartForm', function(e){
      e.preventDefault();
      var $f = $(this);
      // For demo, redirect to checkout with query
      window.location.href = $f.attr('action') + '?product_id=' + encodeURIComponent($f.find('[name=product_id]').val()) + '&quantity=' + encodeURIComponent($f.find('[name=qty]').val());
    });

    // Checkout via AJAX example (if a form has data-ajax)
    $(document).on('submit','#checkoutForm[data-ajax="true"]', function(e){
      e.preventDefault();
      var $f = $(this);
      $.ajax({
        url: $f.attr('action'),
        method: $f.attr('method')||'POST',
        data: $f.serialize(),
        success: function(){ alert('Order placed'); },
        error: function(xhr){ alert('Error: '+(xhr.responseText||xhr.status)); }
      });
    });

    // Track order polling example (if an element with data-track-id exists)
    var $track = $('[data-track-id]');
    if ($track.length) {
      var id = $track.data('track-id');
      // Optional: poll every 10s
      // setInterval(function(){
      //   $.getJSON('/api/orders/'+id, function(data){
      //     if (data && data.status) { $('.js-order-status').text(data.status); }
      //   });
      // }, 10000);
    }
    
    // ==== NEW: View All & Category Navigation ====
    
    // "View All" buttons - Navigate to products section
    $('.view-all').on('click', function(e){
      e.preventDefault();
      
      // Scroll to products section
      var $productsSection = $('.products-grid').first();
      if ($productsSection.length) {
        $('html, body').animate({
          scrollTop: $productsSection.offset().top - 100
        }, 600);
      } else {
        // If no products section on current page, go to shop page
        window.location.href = window.location.origin + '/index.php/shop';
      }
    });
    
    // Category card navigation - Filter by category
    $('.category-card').on('click', function(e){
      e.preventDefault();
      
      // Get category name from the card
      var categoryName = $(this).find('.category-name').text().trim();
      
      // Navigate to shop with category filter
      if (categoryName) {
        // Construct URL with category as search parameter
        var url = window.location.origin + '/index.php/shop?search=' + encodeURIComponent(categoryName);
        window.location.href = url;
      }
    });
    
    // Category card hover effect enhancement
    $('.category-card').hover(
      function(){
        $(this).css({
          'transform': 'translateY(-4px)',
          'box-shadow': '0 8px 20px rgba(0,0,0,0.12)'
        });
      },
      function(){
        $(this).css({
          'transform': 'translateY(0)',
          'box-shadow': '0 2px 8px rgba(0,0,0,0.06)'
        });
      }
    );
    
    // Add cursor pointer to category cards
    $('.category-card').css('cursor', 'pointer');
    
    // Hero "Pre-Order Now" button - Navigate to products
    $('.hero-btn').on('click', function(e){
      e.preventDefault();
      
      // Search for Gaming PCs
      var url = window.location.origin + '/index.php/shop?search=' + encodeURIComponent('Gaming');
      window.location.href = url;
    });
    
    // Admin Button - Navigate to admin login
    $('#adminBtn').on('click', function(e){
      e.preventDefault();
      window.location.href = window.location.origin + '/index.php/auth/admin_login';
    });
    
    // Cashier Button - Navigate to cashier login (if exists)
    $('#cashierBtn').on('click', function(e){
      e.preventDefault();
      window.location.href = window.location.origin + '/index.php/auth/cashier_login';
    });
    
    // Show notification helper
    function showNotification(type, message) {
      var bgColor = type === 'success' ? '#10B981' : '#EF4444';
      var $notification = $('<div>')
        .css({
          position: 'fixed',
          top: '24px',
          right: '24px',
          padding: '16px 24px',
          background: bgColor,
          color: '#fff',
          borderRadius: '12px',
          boxShadow: '0 10px 25px rgba(0,0,0,0.15)',
          zIndex: 9999,
          maxWidth: '400px',
          fontWeight: '600',
          fontSize: '14px'
        })
        .text(message)
        .appendTo('body');
      
      setTimeout(function(){
        $notification.fadeOut(300, function(){ $(this).remove(); });
      }, 3000);
    }
    
  });
})(jQuery);
