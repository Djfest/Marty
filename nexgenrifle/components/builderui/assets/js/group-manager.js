
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.GroupManager = (function() {
    // Private variables
    let _initialized = false;
    
    // Cache DOM elements
    let elements = {};
    
    // Bind group events
    function bindGroupEvents() {
        // Group card click handlers
        elements.groupCards.on('click', function() {
            const groupType = $(this).data('group-type');
            showGroupDetails(groupType);
        });
        
        // Add group button
        elements.addGroupBtn.on('click', function() {
            handleAddGroup();
        });
    }
    
    // Handle adding a new group
    function handleAddGroup() {
        const groupName = prompt('Enter a name for the new group:');
        if (groupName && groupName.trim() !== '') {
            addNewGroup(groupName);
        }
    }
    
    // Add a new group to the UI
    function addNewGroup(groupName) {
        console.log('Adding new group:', groupName);
        
        // Create a unique ID for the group
        const groupId = 'group-' + Date.now();
        
        // Create the HTML for the new group
        const newGroupHtml = `
            <div class="card mb-3 group-card" id="${groupId}" data-group-type="custom-group">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">${groupName}</h5>
                    <div class="d-flex align-items-center">
                        <div class="group-indicators me-3">
                            <div class="d-flex">
                                <div class="status-square bg-danger me-1" title="No components selected"></div>
                            </div>
                        </div>
                        <span class="badge bg-success group-price">$0.00</span>
                    </div>
                </div>
            </div>
        `;
        
        // Append the new group to the group listing
        elements.groupListing.append(newGroupHtml);
        
        // Add click handler to the new group
        $(`#${groupId}`).on('click', function() {
            const groupType = $(this).data('group-type');
            showGroupDetails(groupType);
        });
        
        // Update the cached elements
        elements.groupCards = $('.group-card');
        
        // Save the build
        NexGenRifle.BuildPersistence.saveBuild();
    }
    
    // Show group details
    function showGroupDetails(groupType) {
        // Load group partial based on group type
        // The onShowGroupDetails PHP handler returns an array like {'#groupDetailsTarget': 'html_content'}.
        // The October AJAX framework automatically updates the element specified by the key in the response.
        // Therefore, the 'update' option here is not needed and was causing the "partial not found" error.
        $.request('onShowGroupDetails', {
            data: { groupType: groupType },
            // update: { 'builder/details': '#detailed-content' }, // This line is removed
            success: function(response) { // The 'response' argument contains the data from the server
                // The framework handles the update automatically based on the PHP response.
                // If you need to do something specific with the response data (e.g. check for errors manually),
                // you can inspect the 'response' object here. It will be an object like:
                // { '#groupDetailsTarget': '<p>Rendered HTML...</p>' } or error messages.

                // Show detailed view (which should contain #groupDetailsTarget)
                NexGenRifle.UI.showDetailedView();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Optional: Handle AJAX errors more explicitly if needed
                console.error("AJAX Error:", textStatus, errorThrown);
                // You could display a generic error message to the user here.
                // For example, by updating a specific error display div:
                // $('#ajax-error-message').html('<p class="text-danger">An error occurred while loading details. Please try again.</p>').show();
            }
        });
    }
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return this;
            
            // Cache DOM elements
            elements = {
                groupCards: $('.group-card'),
                groupListing: $('#group-listing'),
                addGroupBtn: $('#add-group-btn')
            };
            
            // Bind events
            bindGroupEvents();
            
            _initialized = true;
            console.log('NexGenRifle Group Manager initialized');
            return this;
        },
        
        // Public methods
        showGroupDetails: showGroupDetails,
        addNewGroup: addNewGroup
    };
})();
