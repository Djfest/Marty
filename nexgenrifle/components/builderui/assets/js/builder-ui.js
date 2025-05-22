
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.UI = (function() {
    // Private variables
    let _initialized = false;
    let _activeView = 'list'; // 'list' or 'grid'
    
    // Cache DOM elements
    let elements = {};
    
    // Bind UI events
    function bindUIEvents() {
        // View toggle buttons
        elements.viewListBtn.on('click', function() {
            setActiveView('list');
        });
        
        elements.viewGridBtn.on('click', function() {
            setActiveView('grid');
        });
        
        // Edit build name button
        elements.editBuildNameBtn.on('click', function() {
            handleEditBuildName();
        });
        
        // Help button
        elements.helpBtn.on('click', function() {
            showHelp();
        });
        
        // Settings button
        elements.settingsBtn.on('click', function() {
            showSettings();
        });
        
        // Docs button
        elements.viewDocsBtn.on('click', function() {
            showDocs();
        });
        
        // Back to listing button
        elements.backToListingBtn.on('click', function() {
            showGroupListing();
        });
    }
    
    // Set active view (list or grid)
    function setActiveView(viewType) {
        _activeView = viewType;
        
        if (viewType === 'list') {
            elements.viewListBtn.addClass('active');
            elements.viewGridBtn.removeClass('active');
            elements.groupListing.removeClass('grid-view').addClass('list-view');
        } else {
            elements.viewGridBtn.addClass('active');
            elements.viewListBtn.removeClass('active');
            elements.groupListing.removeClass('list-view').addClass('grid-view');
        }
    }
    
    // Handle editing build name
    function handleEditBuildName() {
        const currentName = elements.buildNameDisplay.text();
        const newName = prompt('Enter a name for your build:', currentName);
        
        if (newName && newName.trim() !== '') {
            elements.buildNameDisplay.text(newName.trim());
            NexGenRifle.BuildPersistence.saveBuild();
        }
    }
    
    // Show help dialog
    function showHelp() {
        alert('Opening help resources');
        // This would typically open help documentation or support resources
    }
    
    // Show settings panel
    function showSettings() {
        alert('Opening settings panel');
        // This would typically open a settings modal or panel
    }
    
    // Show documentation panel
    function showDocs() {
        alert('Opening documentation viewer');
        // This would typically open a modal or navigate to documentation pages
    }
    
    // Show group listing (hide detailed view)
    function showGroupListing() {
        elements.detailedView.hide();
        elements.groupListing.show();
    }
    
    // Show detailed view (hide group listing)
    function showDetailedView() {
        elements.groupListing.hide();
        elements.detailedView.show();
    }
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return this;
            
            // Cache DOM elements
            elements = {
                viewListBtn: $('#view-list-btn'),
                viewGridBtn: $('#view-grid-btn'),
                editBuildNameBtn: $('#edit-build-name-btn'),
                buildNameDisplay: $('#build-name-display'),
                helpBtn: $('#help-btn'),
                settingsBtn: $('#settings-btn'),
                viewDocsBtn: $('#view-docs-btn'),
                backToListingBtn: $('#back-to-listing-btn'),
                detailedView: $('#detailed-view'),
                groupListing: $('#group-listing'),
                headerTotalPrice: $('#header-total-price'),
                totalBuildPrice: $('#total-build-price')
            };
            
            // Bind events
            bindUIEvents();
            
            _initialized = true;
            console.log('NexGenRifle UI initialized');
            return this;
        },
        
        // Public methods
        updateTotalPriceDisplay: function(price) {
            const formattedPrice = '$' + price.toFixed(2);
            elements.headerTotalPrice.text(formattedPrice).attr('data-total-price', price.toFixed(2));
            elements.totalBuildPrice.text(formattedPrice).attr('data-total-price', price.toFixed(2));
        },
        
        // Get active view
        getActiveView: function() {
            return _activeView;
        },
        
        // Show detailed view
        showDetailedView: showDetailedView,
        
        // Show group listing
        showGroupListing: showGroupListing
    };
})();