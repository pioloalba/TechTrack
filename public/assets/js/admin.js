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
  });
})(jQuery);
