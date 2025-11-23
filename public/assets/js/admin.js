/* TechTrack Admin JS (jQuery) */
(function($){
  $(function(){
    // AJAXify forms tagged with data-ajax="true"
    $(document).on('submit','form[data-ajax="true"]', function(e){
      e.preventDefault();
      var $f = $(this);
      $.ajax({
        url: $f.attr('action'),
        method: $f.attr('method')||'POST',
        data: $f.serialize(),
        success: function(resp){
          alert('Saved successfully');
          if ($f.data('redirect')) { window.location.href = $f.data('redirect'); }
          else { location.reload(); }
        },
        error: function(xhr){
          alert('Error: '+(xhr.responseText||xhr.status));
        }
      });
    });

    // Delete links with data-delete
    $(document).on('click','a[data-delete="true"]', function(e){
      e.preventDefault();
      if(!confirm('Delete this item?')) return;
      $.get($(this).attr('href'), function(){ location.reload(); });
    });

    // Example: update order status via AJAX
    $(document).on('submit','#order-status-form', function(e){
      e.preventDefault();
      var $f = $(this);
      $.post($f.attr('action'), $f.serialize(), function(){
        alert('Status updated');
        location.reload();
      });
    });

    // ===== Notification Bell Functionality =====
    var notificationDropdownOpen = false;

    // Load notifications on page load
    function loadNotifications() {
      $.ajax({
        url: window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/').replace(/\/admin.*/, '') + '/admin/alerts/get-notifications',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            updateNotificationUI(response.count, response.notifications);
          }
        },
        error: function() {
          $('#notificationList').html('<div class="notification-empty">Failed to load notifications</div>');
        }
      });
    }

    // Update notification UI
    function updateNotificationUI(count, notifications) {
      var $badge = $('#notificationCount');
      var $list = $('#notificationList');

      // Update badge
      if (count > 0) {
        $badge.text(count).addClass('active');
      } else {
        $badge.removeClass('active');
      }

      // Update list
      if (notifications.length === 0) {
        $list.html('<div class="notification-empty">No new notifications</div>');
      } else {
        var html = '';
        notifications.forEach(function(notif) {
          html += '<div class="notification-item ' + notif.type + '" data-product-id="' + notif.product_id + '">';
          html += '<div class="notification-item-title">' + notif.title + '</div>';
          html += '<div class="notification-item-message">' + notif.message + '</div>';
          html += '</div>';
        });
        $list.html(html);
      }
    }

    // Toggle notification dropdown
    $('#notificationBell').on('click', function(e) {
      e.stopPropagation();
      notificationDropdownOpen = !notificationDropdownOpen;
      
      if (notificationDropdownOpen) {
        $('#notificationDropdown').fadeIn(200);
        loadNotifications();
      } else {
        $('#notificationDropdown').fadeOut(200);
      }
    });

    // Click on notification item
    $(document).on('click', '.notification-item', function() {
      var productId = $(this).data('product-id');
      if (productId) {
        window.location.href = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/').replace(/\/admin.*/, '') + '/admin/products';
      }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
      if (notificationDropdownOpen && !$(e.target).closest('.notification-container').length) {
        notificationDropdownOpen = false;
        $('#notificationDropdown').fadeOut(200);
      }
    });

    // Load notifications on page load
    loadNotifications();

    // Refresh notifications every 60 seconds
    setInterval(function() {
      if (!notificationDropdownOpen) {
        $.ajax({
          url: window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/').replace(/\/admin.*/, '') + '/admin/alerts/get-notifications',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.count > 0) {
              $('#notificationCount').text(response.count).addClass('active');
            } else {
              $('#notificationCount').removeClass('active');
            }
          }
        });
      }
    }, 60000);

  });
})(jQuery);
