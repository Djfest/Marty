
window.NexGenRifle = window.NexGenRifle || {};

NexGenRifle.PriceCalculator = (function() {
    // Private variables
    let _initialized = false;
    let _totalPrice = 0;
    
    // Initialize the total price from the DOM
    function initializeTotalPrice() {
        const headerTotalPrice = $('#header-total-price');
        if (headerTotalPrice.length) {
            _totalPrice = parseFloat(headerTotalPrice.attr('data-total-price')) || 0;
        }
    }
    
    // Calculate the total price from all groups
    function calculateTotalFromGroups() {
        let total = 0;
        $('.group-price').each(function() {
            const price = parseFloat($(this).text().replace('$', '')) || 0;
            total += price;
        });
        return total;
    }
    
    // Public methods
    return {
        init: function() {
            if (_initialized) return this;
            
            // Initialize total price
            initializeTotalPrice();
            
            _initialized = true;
            console.log('NexGenRifle Price Calculator initialized');
            return this;
        },
        
        // Public methods
        calculateTotal: function() {
            _totalPrice = calculateTotalFromGroups();
            return _totalPrice;
        },
        
        getCurrentTotal: function() {
            return _totalPrice;
        },
        
        updatePrice: function(groupId, newPrice) {
            const group = $(`#${groupId}`);
            if (group.length) {
                group.find('.group-price').text('$' + newPrice.toFixed(2));
                return NexGenRifle.BuilderCore.updateTotalPrice();
            }
            return _totalPrice;
        }
    };
})();