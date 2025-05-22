
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.BuildPersistence = (function() {
    // Private variables
    let _initialized = false;
    const STORAGE_KEY = 'nexgenrifle_current_build';
    
    // Collect build data from the DOM
    function collectBuildData() {
        const buildData = {
            name: $('#build-name-display').text(),
            totalPrice: NexGenRifle.PriceCalculator.getCurrentTotal(),
            groups: []
        };
        
        // Collect data from each group
        $('.group-card').each(function() {
            const groupElement = $(this);
            buildData.groups.push({
                id: groupElement.attr('id'),
                type: groupElement.data('group-type'),
                name: groupElement.find('.card-body h5').text(),
                price: parseFloat(groupElement.find('.group-price').text().replace('$', '')),
                completed: groupElement.hasClass('completed')
            });
        });
        
        return buildData;
    }
    
    // Apply build data to the DOM
    function applyBuildData(buildData) {
        if (!buildData) return;
        
        // Set build name
        $('#build-name-display').text(buildData.name || 'My Custom AR-15 Build');
        
        // Set total price
        NexGenRifle.UI.updateTotalPriceDisplay(buildData.totalPrice || 0);
        
        // Apply group data if available
        if (buildData.groups && buildData.groups.length) {
            // Clear existing groups
            $('#group-listing').empty();
            
            // Add each group
            buildData.groups.forEach(function(group) {
                // Create group element
                const groupHtml = `
                    <div class="card mb-3 group-card ${group.completed ? 'completed' : ''}" 
                         id="${group.id || 'group-' + Date.now()}" 
                         data-group-type="${group.type}">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">${group.name}</h5>
                            <div class="d-flex align-items-center">
                                <div class="group-indicators me-3">
                                    <div class="d-flex">
                                        <div class="status-square ${group.completed ? 'bg-primary' : 'bg-danger'} me-1" 
                                             title="${group.completed ? 'Completed' : 'Incomplete'}"></div>
                                    </div>
                                </div>
                                <span class="badge bg-success group-price">$${group.price.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#group-listing').append(groupHtml);
            });
            
            // Rebind events to new groups
            $('.group-card').on('click', function() {
                const groupType = $(this).data('group-type');
                NexGenRifle.GroupManager.showGroupDetails(groupType);
            });
        }
    }
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return this;
            
            _initialized = true;
            console.log('NexGenRifle Build Persistence initialized');
            return this;
        },
        
        // Public methods
        saveBuild: function() {
            const buildData = collectBuildData();
            
            // Store in local storage
            localStorage.setItem(STORAGE_KEY, JSON.stringify(buildData));
            
            // Also send to server
            $.request('onSaveBuild', {
                data: { buildData: buildData }
            });
            
            return buildData;
        },
        
        loadSavedBuild: function() {
            // Try to load from local storage first
            const savedBuild = localStorage.getItem(STORAGE_KEY);
            
            if (savedBuild) {
                const buildData = JSON.parse(savedBuild);
                applyBuildData(buildData);
                return true;
            }
            
            // If no local storage data, try to load from server
            $.request('onLoadBuild', {
                success: function(data) {
                    if (data.buildData) {
                        applyBuildData(data.buildData);
                    }
                }
            });
            
            return false;
        }
    };
})();