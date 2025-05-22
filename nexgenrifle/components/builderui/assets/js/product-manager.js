
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.ProductManager = (function() {
    // Private variables
    let _initialized = false;
    
    // Cache DOM elements
    let elements = {};
    
    // Bind product events
    function bindProductEvents() {
        // Product URL input
        elements.productUrlInput.on('keypress', function(e) {
            if (e.key === 'Enter') {
                processProductUrl($(this).val());
            }
        });
        
        // Catalog browse button
        elements.catalogBrowseBtn.on('click', function() {
            openCatalogBrowser();
        });
        
        // Product cards
        $(document).on('click', '.product-card', function() {
            selectProduct($(this));
        });
    }
    
    // Process product URL
    function processProductUrl(url) {
        if (url && url.trim() !== '') {
            console.log('Processing product URL:', url);
            
            // AJAX request to process the URL
            $.request('onProcessProductUrl', {
                data: { url: url },
                success: function(data) {
                    if (data.product) {
                        // Add product to the current group or display it
                        addProductToCurrentGroup(data.product);
                    } else {
                        alert('Could not process this product URL. Please try another URL or browse the catalog.');
                    }
                }
            });
        }
    }
    
    // Open catalog browser
    function openCatalogBrowser() {
        console.log('Opening catalog browser');
        
        // AJAX request to load catalog
        $.request('onLoadCatalog', {
            update: { 'builder/catalog': '#catalog-container' },
            success: function() {
                // Show catalog modal or panel
                $('#catalog-browser-modal').modal('show');
            }
        });
    }
    
    // Select a product
    function selectProduct(productCard) {
        // Remove selection from all other products in the same group
        const groupContent = productCard.closest('.group-content');
        if (groupContent.length) {
            groupContent.find('.product-card').removeClass('selected');
        }
        
        // Select this product
        productCard.addClass('selected');
        
        // Update price if available
        const priceEl = productCard.find('[data-price]');
        if (priceEl.length) {
            const price = parseFloat(priceEl.attr('data-price')) || 0;
            const groupCard = productCard.closest('.group-card');
            
            if (groupCard.length) {
                const groupId = groupCard.attr('id');
                NexGenRifle.PriceCalculator.updatePrice(groupId, price);
                
                // Save the updated build
                NexGenRifle.BuildPersistence.saveBuild();
            }
        }
    }
    
    // Add product to current group
    function addProductToCurrentGroup(product) {
        // Find the active group
        const activeGroup = $('.group-card.active');
        if (!activeGroup.length) {
            alert('Please select a group first.');
            return;
        }
        
        // Create product card HTML
        const productCardHtml = `
            <div class="product-card-row mb-3">
                <div class="product-card-container d-flex">
                    <div class="product-image">
                        <div class="card bg-light text-center product-thumbnail">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div>
                                    ${product.image ? `<img src="${product.image}" alt="${product.name}" class="img-fluid">` : 
                                    `<div class="text-muted">${product.name}</div>`}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-details flex-grow-1 ms-3">
                        <h5 class="product-title text-white">${product.name}</h5>
                        <p class="product-description text-light">${product.description || 'No description available.'}</p>
                    </div>
                    <div class="product-actions d-flex flex-column justify-content-between">
                        <div class="product-status">
                            <span class="badge ${product.available ? 'bg-primary' : 'bg-secondary'}">
                                ${product.available ? 'Available' : 'Currently Unavailable'}
                            </span>
                            <span class="badge bg-success">Not Purchased</span>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-outline-primary btn-sm w-100 mb-2">View Product</button>
                            <button class="btn btn-outline-success btn-sm w-100">Select</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add to the active group's content
        const groupContent = activeGroup.find('.group-content');
        if (groupContent.length) {
            groupContent.append(productCardHtml);
            
            // Update price
            if (product.price) {
                const groupId = activeGroup.attr('id');
                NexGenRifle.PriceCalculator.updatePrice(groupId, product.price);
                
                // Save the updated build
                NexGenRifle.BuildPersistence.saveBuild();
            }
        }
    }
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return this;
            
            // Cache DOM elements
            elements = {
                productUrlInput: $('#product-url-input'),
                catalogBrowseBtn: $('#catalog-browse-btn')
            };
            
            // Bind events
            bindProductEvents();
            
            _initialized = true;
            console.log('NexGenRifle Product Manager initialized');
            return this;
        },
        
        // Public methods
        processProductUrl: processProductUrl,
        openCatalogBrowser: openCatalogBrowser,
        selectProduct: selectProduct
    };
})();