/**
 * TechTrack Inventory Management
 * jQuery-based AJAX operations for stock adjustments
 */

(function($) {
    'use strict';

    const baseUrl = window.location.origin + '/admin/inventory';
    
    const $modal = $('#adjustStockModal');
    const $form = $('#adjustStockForm');
    
    $(function() {
        initEventHandlers();
        initSearch();
        initFilters();
    });

    /**
     * Initialize event handlers
     */
    function initEventHandlers() {
        // Adjust stock button
        $(document).on('click', '.adjust-stock-btn', function() {
            const productId = $(this).data('id');
            const productName = $(this).data('name');
            openAdjustModal(productId, productName);
        });

        // Modal close
        $('.modal-close, #cancelAdjustBtn').on('click', function() {
            closeModal();
        });

        // Click outside to close
        $modal.on('click', function(e) {
            if ($(e.target).is('#adjustStockModal')) {
                closeModal();
            }
        });

        // Form submission
        $form.on('submit', function(e) {
            e.preventDefault();
            submitAdjustment();
        });
    }

    /**
     * Initialize search
     */
    function initSearch() {
        let searchTimeout;
        $('#searchInventory').on('keyup', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                filterTable(query, $('#filterStatus').val());
            }, 300);
        });
    }

    /**
     * Initialize filters
     */
    function initFilters() {
        $('#filterStatus').on('change', function() {
            const status = $(this).val();
            const search = $('#searchInventory').val().toLowerCase();
            filterTable(search, status);
        });
    }

    /**
     * Filter inventory table
     */
    function filterTable(searchQuery, status) {
        $('#inventoryTable tbody tr').each(function() {
            const $row = $(this);
            const name = $row.find('.product-name').text().toLowerCase();
            const sku = $row.find('.product-sku').text().toLowerCase();
            const rowStatus = $row.data('status');
            
            const matchesSearch = !searchQuery || name.includes(searchQuery) || sku.includes(searchQuery);
            const matchesStatus = !status || rowStatus === status;
            
            $row.toggle(matchesSearch && matchesStatus);
        });
    }

    /**
     * Open adjustment modal
     */
    function openAdjustModal(productId, productName) {
        $('#adjustProductId').val(productId);
        $('#adjustProductName').val(productName);
        $('#adjustQuantity').val('');
        $('#adjustNotes').val('');
        $('#adjustType').val('add');
        $('#adjustReason').val('restock');
        
        $modal.addClass('active');
        $('#adjustQuantity').focus();
    }

    /**
     * Close modal
     */
    function closeModal() {
        $modal.removeClass('active');
        $form[0].reset();
    }

    /**
     * Submit stock adjustment
     */
    function submitAdjustment() {
        const formData = $form.serialize();
        const productId = $('#adjustProductId').val();
        
        $.ajax({
            url: `${baseUrl}/adjust`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    
                    // Update stock value in table
                    if (response.new_stock !== undefined) {
                        const $row = $(`tr[data-product-id="${productId}"]`);
                        $row.find('.stock-value').text(response.new_stock);
                        
                        // Update status badge
                        const threshold = parseInt($row.find('td:nth-child(5)').text());
                        const newStock = response.new_stock;
                        const $statusCell = $row.find('td:nth-child(7)');
                        
                        if (newStock <= 0) {
                            $statusCell.html('<span class="badge red">Out of Stock</span>');
                            $row.attr('data-status', 'out_of_stock');
                        } else if (newStock <= threshold) {
                            $statusCell.html('<span class="badge amber">Low Stock</span>');
                            $row.attr('data-status', 'low_stock');
                        } else {
                            $statusCell.html('<span class="badge green">In Stock</span>');
                            $row.attr('data-status', 'in_stock');
                        }
                    }
                    
                    closeModal();
                    
                    // Reload after 1 second to show updated transactions
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }

    /**
     * Show notification
     */
    function showNotification(type, message) {
        const bgColor = type === 'success' ? '#ECFDF5' : '#FEE2E2';
        const textColor = type === 'success' ? '#065F46' : '#991B1B';
        const borderColor = type === 'success' ? '#A7F3D0' : '#FECACA';
        
        const $notification = $('<div>')
            .css({
                position: 'fixed',
                top: '24px',
                right: '24px',
                padding: '16px 24px',
                background: bgColor,
                color: textColor,
                border: `1px solid ${borderColor}`,
                borderRadius: '8px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
                zIndex: 9999,
                maxWidth: '400px',
                fontWeight: '500'
            })
            .text(message)
            .appendTo('body');
        
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

})(jQuery);
