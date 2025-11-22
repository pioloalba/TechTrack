/**
 * TechTrack Products Management
 * jQuery-based AJAX CRUD operations
 */

(function ($) {
    'use strict';

    // Base URL - will be set from form data attribute
    let baseUrl = null;

    // Modal elements - will be initialized on document ready
    let $modal, $modalTitle, $productForm, $productId, $viewModal;

    // Initialize on document ready
    $(function () {
        console.log('=== Products.js: Document ready ===');
        
        // Initialize modal elements
        $modal = $('#productModal');
        $modalTitle = $('#modalTitle');
        $productForm = $('#productForm');
        $productId = $('#productId');
        $viewModal = $('#viewProductModal');
        
        // Get base URL from form data attribute
        baseUrl = $productForm.data('base-url');
        console.log('=== BASE URL DEBUG ===');
        console.log('Form element:', $productForm[0]);
        console.log('Data attribute value:', $productForm.attr('data-base-url'));
        console.log('jQuery data() value:', baseUrl);
        
        if (!baseUrl) {
            console.error('ERROR: Base URL not found in form data attribute!');
            // Fallback to manual construction
            const pathParts = window.location.pathname.split('/');
            const basePath = pathParts.length >= 2 && pathParts[1] ? '/' + pathParts[1] : '';
            baseUrl = window.location.origin + basePath + '/admin/products';
            console.log('Using fallback baseUrl:', baseUrl);
        }
        
        console.log('=== FINAL BASE URL:', baseUrl, '===');
        console.log('Current path:', window.location.pathname);
        console.log('Origin:', window.location.origin);
        console.log('$productForm found:', $productForm.length);
        console.log('$modal found:', $modal.length);
        
        // Configure jQuery AJAX defaults
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true
            },
            crossDomain: false,
            beforeSend: function(xhr, settings) {
                console.log('[AJAX] Before send:', settings.url, settings.type);
            }
        });
        
        initEventHandlers();
        initSearch();
        initFilters();
    });

    /**
     * Initialize all event handlers
     */
    function initEventHandlers() {
        // Add Product button
        $('#addProductBtn').on('click', function () {
            openModal('add');
        });

        // View Product button
        $(document).on('click', '.view-btn', function () {
            const explicitId = $(this).data('id');
            const fallbackId = $(this).closest('[data-product-id]').data('product-id');
            const productId = explicitId || fallbackId;
            if (productId) {
                openViewModal(productId);
            }
        });

        // Edit Product buttons
        $(document).on('click', '.edit-product', function () {
            const productId = $(this).data('id');
            openModal('edit', productId);
        });

        // Delete Product buttons
        $(document).on('click', '.delete-product', function () {
            const productId = $(this).data('id');
            const productName = $(this).closest('tr').find('.product-name').text();

            if (confirm(`Are you sure you want to delete "${productName}"?\n\nThis action cannot be undone.`)) {
                deleteProduct(productId);
            }
        });

        // Modal close buttons
        $('.modal-close, #cancelBtn').on('click', function () {
            closeModal();
        });

        // Click outside modal to close
        $modal.on('click', function (e) {
            if ($(e.target).is('#productModal')) {
                closeModal();
            }
        });

        // Close view modal
        $(document).on('click', '#viewModalCloseBtn', function () {
            closeViewModal();
        });

        // Click outside view modal to close
        $viewModal.on('click', function (e) {
            if ($(e.target).is('#viewProductModal')) {
                closeViewModal();
            }
        });

        // Form submission
        $productForm.on('submit', function (e) {
            console.log('Form submit event triggered!');
            e.preventDefault();
            e.stopPropagation();
            submitForm();
            return false;
        });

        // Image upload preview
        $('#productImages').on('change', function (e) {
            handleImagePreview(e.target.files);
        });
    }

    /**
     * Initialize search functionality
     */
    function initSearch() {
        let searchTimeout;
        $('#searchProducts').on('keyup', function () {
            clearTimeout(searchTimeout);
            const query = $(this).val().toLowerCase();

            searchTimeout = setTimeout(function () {
                // Check if grid view exists, otherwise use table
                if ($('#productsGrid').length) {
                    filterGrid(query, $('#filterCategory').val(), $('#filterStock').val());
                } else {
                    filterTable(query, $('#filterCategory').val());
                }
            }, 300);
        });
    }

    /**
     * Initialize category filter
     */
    function initFilters() {
        $('#filterCategory').on('change', function () {
            const category = $(this).val();
            const search = $('#searchProducts').val().toLowerCase();
            const stockStatus = $('#filterStock').val();
            
            // Check if grid view exists, otherwise use table
            if ($('#productsGrid').length) {
                filterGrid(search, category, stockStatus);
            } else {
                filterTable(search, category);
            }
        });
        
        $('#filterStock').on('change', function () {
            const stockStatus = $(this).val();
            const search = $('#searchProducts').val().toLowerCase();
            const category = $('#filterCategory').val();
            filterGrid(search, category, stockStatus);
        });
        
        // Grid/List View Toggle
        $('#toggleView').on('click', function () {
            $('#productsGrid').removeClass('list-view').addClass('products-grid');
            $(this).addClass('active');
            $('#toggleViewList').removeClass('active');
            localStorage.setItem('productView', 'grid');
        });
        
        $('#toggleViewList').on('click', function () {
            $('#productsGrid').removeClass('products-grid').addClass('list-view');
            $(this).addClass('active');
            $('#toggleView').removeClass('active');
            localStorage.setItem('productView', 'list');
        });
        
        // Restore saved view preference
        const savedView = localStorage.getItem('productView');
        if (savedView === 'list') {
            $('#toggleViewList').click();
        }
    }

    /**
     * Filter table based on search and category
     */
    function filterTable(searchQuery, category) {
        $('#productsTable tbody tr').each(function () {
            const $row = $(this);
            const name = $row.find('.product-name').text().toLowerCase();
            const sku = $row.find('.product-sku').text().toLowerCase();
            const rowCategory = $row.find('td:nth-child(4)').text();

            const matchesSearch = !searchQuery || name.includes(searchQuery) || sku.includes(searchQuery);
            const matchesCategory = !category || rowCategory === category;

            $row.toggle(matchesSearch && matchesCategory);
        });
    }
    
    /**
     * Filter grid based on search, category, and stock status
     */
    function filterGrid(searchQuery, category, stockStatus) {
        $('.product-card').each(function () {
            const $card = $(this);
            const name = $card.find('.product-card-title').text().toLowerCase();
            const sku = $card.find('.product-sku').text().toLowerCase();
            const cardCategory = $card.attr('data-category').toLowerCase();
            const cardStatus = $card.attr('data-status');

            const matchesSearch = !searchQuery || name.includes(searchQuery) || sku.includes(searchQuery);
            const matchesCategory = !category || cardCategory === category.toLowerCase();
            const matchesStock = !stockStatus || cardStatus === stockStatus;

            $card.toggle(matchesSearch && matchesCategory && matchesStock);
        });
    }

    /**
     * Open modal for add or edit
     */
    function openModal(mode, productId = null) {
        if (mode === 'add') {
            $modalTitle.text('Add New Product');
            $productForm[0].reset();
            $productId.val('');
            $('#submitBtn').text('Create Product');
            $('#imagePreviewContainer').empty();
        } else if (mode === 'edit' && productId) {
            $modalTitle.text('Edit Product');
            $('#submitBtn').text('Update Product');
            loadProductData(productId);
        }

        $modal.addClass('active');
    }

    /**
     * Open the View Product modal and populate with data
     */
    function openViewModal(productId) {
        // Reset content
        $('#viewName').text('');
        $('#viewSKU').text('');
        $('#viewCategory').text('');
        $('#viewBrand').text('');
        $('#viewPrice').text('');
        $('#viewStock').text('');
        $('#viewDescription').text('');
        $('#viewImages').empty();

        // Show modal immediately with a light loading indicator
        $viewModal.addClass('active');
        $('#viewLoading').show();
        $('#viewContent').hide();

        $.ajax({
            url: `${baseUrl}/get/${productId}`,
            method: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res || !res.success || !res.product) {
                    showNotification('error', res && res.message ? res.message : 'Failed to load product.');
                    closeViewModal();
                    return;
                }

                const p = res.product;
                $('#viewName').text(p.name || '');
                $('#viewSKU').text(p.sku || '');
                $('#viewCategory').text(p.category || 'Uncategorized');
                $('#viewBrand').text(p.brand || '—');
                $('#viewPrice').text(formatCurrency(p.price));
                $('#viewStock').text(`${p.stock ?? 0}`);
                $('#viewDescription').text(p.description || 'No description.');

                // Images
                const images = Array.isArray(p.images) ? p.images : [];
                if (images.length) {
                    images.forEach(img => {
                        const $img = $('<img>').attr('src', img.image_url)
                            .css({ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '8px', border: '1px solid #E5E7EB' });
                        const $wrap = $('<div>').css({ aspectRatio: '1/1', overflow: 'hidden' }).append($img);
                        $('#viewImages').append($wrap);
                    });
                } else {
                    $('#viewImages').append(
                        $('<div>').text('No images uploaded yet.').css({ color: '#6B7280', fontSize: '14px' })
                    );
                }

                $('#viewLoading').hide();
                $('#viewContent').show();
            },
            error: function () {
                showNotification('error', 'Failed to load product.');
                closeViewModal();
            }
        });
    }

    function closeViewModal() {
        $viewModal.removeClass('active');
    }

    /**
     * Close modal
     */
    function closeModal() {
        $modal.removeClass('active');
        $productForm[0].reset();
        $productId.val('');
    }

    /**
     * Load product data for editing via AJAX
     */
    function loadProductData(productId) {
        console.log('Loading product data for ID:', productId);
        
        // Show loading state
        $('#submitBtn').prop('disabled', true).text('Loading...');
        
        $.ajax({
            url: `${baseUrl}/get/${productId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                console.log('Product data loaded:', response);
                
                if (response.success && response.product) {
                    const product = response.product;
                    
                    // Fill form fields
                    $productId.val(product.id || '');
                    $('#productName').val(product.name || '');
                    $('#productSKU').val(product.sku || '');
                    $('#productCategory').val(product.category || '');
                    $('#productBrand').val(product.brand || '');
                    $('#productPrice').val(product.price || '');
                    $('#productSalePrice').val(product.sale_price || '');
                    $('#productStock').val(product.stock || '');
                    $('#productLowStockThreshold').val(product.low_stock_threshold || 5);
                    $('#productDescription').val(product.description || '');
                    
                    // Set featured checkbox
                    $('#productFeatured').prop('checked', product.featured == 1);
                    
                    // Display existing images
                    displayExistingImages(product.images || []);
                    
                    // Re-enable submit button
                    $('#submitBtn').prop('disabled', false).text('Update Product');
                } else {
                    showNotification('error', response.message || 'Failed to load product data.');
                    $('#submitBtn').prop('disabled', false).text('Update Product');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading product:', error);
                showNotification('error', 'Failed to load product data.');
                $('#submitBtn').prop('disabled', false).text('Update Product');
            }
        });
    }

    /**
     * Submit form via AJAX with image upload
     */
    function submitForm() {
        console.log('===== submitForm() called =====');
        
        // Check if form exists
        if (!$productForm || !$productForm.length) {
            console.error('ERROR: Product form not found!');
            showNotification('error', 'Form not found. Please refresh the page.');
            return;
        }
        
        // Use FormData to handle file uploads
        const formData = new FormData($productForm[0]);
        const productId = $productId.val();
        const isEdit = productId !== '';

        const url = isEdit ? `${baseUrl}/update/${productId}` : `${baseUrl}/store`;
        const method = 'POST';

        console.log('[submitForm] URL:', url);
        console.log('[submitForm] Method:', method);
        console.log('[submitForm] Is Edit:', isEdit);
        console.log('[submitForm] Has images:', $('#productImages')[0] ? $('#productImages')[0].files.length : 0);
        
        // Debug: Log form data
        console.log('[submitForm] Form data entries:');
        for (let pair of formData.entries()) {
            console.log('  ' + pair[0] + ': ' + pair[1]);
        }

        // Disable submit button
        const $submitBtn = $('#submitBtn');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,  // Required for FormData
            contentType: false,  // Required for FormData
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                console.log('AJAX Success Response:', response);
                if (response.success) {
                    showNotification('success', response.message || 'Product saved successfully!');
                    closeModal();

                    // Reload page to reflect changes
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification('error', response.message || 'Failed to save product.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('XHR Status:', status);
                console.error('XHR Response Code:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                
                // Try to parse error response
                let errorMsg = 'An error occurred. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMsg = errorResponse.message;
                    }
                } catch (e) {
                    // If response is not JSON, use response text if available
                    if (xhr.responseText && xhr.responseText.length < 200) {
                        errorMsg = xhr.responseText;
                    }
                }
                
                showNotification('error', errorMsg);
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    }

    /**
     * Delete product via AJAX
     */
    function deleteProduct(productId) {
        $.ajax({
            url: `${baseUrl}/delete/${productId}`,
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.success) {
                    showNotification('success', response.message || 'Product deleted successfully!');

                    // Remove row from table with animation
                    const $row = $(`tr[data-product-id="${productId}"]`);
                    $row.fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    showNotification('error', response.message || 'Failed to delete product.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }

    /**
     * Show notification message
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

        setTimeout(function () {
            $notification.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    // Helpers
    function formatCurrency(val) {
        const n = parseFloat(val || 0);
        return `₱${n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    /**
     * Handle image preview
     */
    function handleImagePreview(files) {
        const $container = $('#imagePreviewContainer');
        $container.empty();

        if (files.length === 0) return;

        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const $preview = $('<div>')
                    .css({
                        position: 'relative',
                        borderRadius: '8px',
                        overflow: 'hidden',
                        border: '2px solid #E5E7EB',
                        aspectRatio: '1/1'
                    });

                const $img = $('<img>')
                    .attr('src', e.target.result)
                    .css({
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover'
                    });

                const $removeBtn = $('<button>')
                    .attr('type', 'button')
                    .html('×')
                    .css({
                        position: 'absolute',
                        top: '4px',
                        right: '4px',
                        background: '#EF4444',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '50%',
                        width: '24px',
                        height: '24px',
                        cursor: 'pointer',
                        fontSize: '18px',
                        lineHeight: '1',
                        fontWeight: 'bold'
                    })
                    .on('click', function () {
                        removeImage(index);
                    });

                $preview.append($img, $removeBtn);
                $container.append($preview);
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Remove image from preview
     */
    function removeImage(index) {
        const $input = $('#productImages')[0];
        const dt = new DataTransfer();
        const files = $input.files;

        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                dt.items.add(files[i]);
            }
        }

        $input.files = dt.files;
        handleImagePreview($input.files);
    }

    /**
     * Display existing product images when editing
     */
    function displayExistingImages(images) {
        const $container = $('#imagePreviewContainer');
        $container.empty();

        if (!images || images.length === 0) return;

        images.forEach((image, index) => {
            const $preview = $('<div>')
                .css({
                    position: 'relative',
                    borderRadius: '8px',
                    overflow: 'hidden',
                    border: '2px solid #E5E7EB',
                    aspectRatio: '1/1'
                })
                .attr('data-image-id', image.id);

            const $img = $('<img>')
                .attr('src', image.image_url)
                .css({
                    width: '100%',
                    height: '100%',
                    objectFit: 'cover'
                });

            // Main image badge
            if (image.is_main == 1) {
                const $mainBadge = $('<span>')
                    .text('Main')
                    .css({
                        position: 'absolute',
                        top: '4px',
                        left: '4px',
                        background: '#3B82F6',
                        color: '#fff',
                        padding: '2px 8px',
                        borderRadius: '4px',
                        fontSize: '11px',
                        fontWeight: 'bold'
                    });
                $preview.append($mainBadge);
            }

            const $removeBtn = $('<button>')
                .attr('type', 'button')
                .html('×')
                .css({
                    position: 'absolute',
                    top: '4px',
                    right: '4px',
                    background: '#EF4444',
                    color: '#fff',
                    border: 'none',
                    borderRadius: '50%',
                    width: '24px',
                    height: '24px',
                    cursor: 'pointer',
                    fontSize: '18px',
                    lineHeight: '1',
                    fontWeight: 'bold'
                })
                .on('click', function () {
                    deleteExistingImage(image.id, $preview);
                });

            $preview.append($img, $removeBtn);
            $container.append($preview);
        });
    }

    /**
     * Delete existing product image
     */
    function deleteExistingImage(imageId, $previewElement) {
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        $.ajax({
            url: `${baseUrl}/delete_image/${imageId}`,
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.success) {
                    $previewElement.fadeOut(300, function () {
                        $(this).remove();
                    });
                    showNotification('success', 'Image deleted successfully.');
                } else {
                    showNotification('error', response.message || 'Failed to delete image.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error deleting image:', error);
                showNotification('error', 'Failed to delete image.');
            }
        });
    }

})(jQuery);
