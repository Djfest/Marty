
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.BuilderCore = (function() {
    // Private variables
    let _initialized = false;
    
    // Dependencies - will be initialized
    let ui, productManager, groupManager, buildPersistence, priceCalculator;
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return;
            
            // Initialize modules
            ui = NexGenRifle.UI.init();
            productManager = NexGenRifle.ProductManager.init();
            groupManager = NexGenRifle.GroupManager.init();
            buildPersistence = NexGenRifle.BuildPersistence.init();
            priceCalculator = NexGenRifle.PriceCalculator.init();
            
            // Load saved build if available
            buildPersistence.loadSavedBuild();
            
            _initialized = true;
            console.log('NexGenRifle Builder Core initialized');
            return this;
        },
        
        // Public methods that coordinate between modules
        showGroupDetails: function(groupType) {
            groupManager.showGroupDetails(groupType);
        },
        
        updateTotalPrice: function() {
            const newTotal = priceCalculator.calculateTotal();
            ui.updateTotalPriceDisplay(newTotal);
            return newTotal;
        }
    };
})();

// Initialize on document ready
$(document).ready(function() {
    NexGenRifle.BuilderCore.init();
});