/* Builder Component JavaScript */
+function($) {
    'use strict';

    var BuilderUI = function() {
        this.init();
    }

    BuilderUI.prototype.init = function() {
        this.attachEventListeners();
        this.initializeComponents();
    }

    BuilderUI.prototype.attachEventListeners = function() {
        // Handle product search input delay
        $(document).on('keyup', '.part-search input', this.debounce(function(e) {
            $(e.target).request();
        }, 300));

        // Handle build title editing
        $(document).on('click', '.build-info h2', function() {
            var $title = $(this);
            var currentText = $title.text();
            
            var $input = $('<input type="text" class="build-title-edit" />')
                .val(currentText)
                .on('blur', function() {
                    var newText = $(this).val();
                    if (newText && newText !== currentText) {
                        // Update build title via AJAX
                        $(this).request('onUpdateBuildTitle', {
                            data: { title: newText }
                        });
                    }
                    $title.text(currentText).show();
                    $(this).remove();
                })
                .on('keypress', function(e) {
                    if (e.which === 13) {
                        $(this).blur();
                    }
                });

            $title.hide().after($input);
            $input.focus();
        });

        // Add tooltips to compatible parts
        $(document).on('mouseenter', '.product-item', function() {
            var $item = $(this);
            if (!$item.data('tooltip-initialized') && $item.data('compatibility')) {
                $item.tooltip({
                    title: $item.data('compatibility'),
                    html: true,
                    placement: 'right'
                }).data('tooltip-initialized', true);
            }
        });
    }

    BuilderUI.prototype.initializeComponents = function() {
        // Initialize any Bootstrap components
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }

    // Utility function for debouncing input
    BuilderUI.prototype.debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        }
    }

    // Handle part compatibility checks
    BuilderUI.prototype.checkCompatibility = function(category, productId) {
        var build = this.getCurrentBuildState();
        // You would implement compatibility logic here
        return true;
    }

    BuilderUI.prototype.getCurrentBuildState = function() {
        var state = {
            parts: {},
            total: 0
        };

        $('.selected-part').each(function() {
            var category = $(this).closest('.part-selector').data('category');
            var productId = $(this).data('product-id');
            if (category && productId) {
                state.parts[category] = productId;
            }
        });

        return state;
    }

    // Calculate and update total cost
    BuilderUI.prototype.updateTotalCost = function() {
        var total = 0;
        $('.selected-part .part-price').each(function() {
            var price = parseFloat($(this).text().replace('$', ''));
            if (!isNaN(price)) {
                total += price;
            }
        });
        $('.build-meta .total').text('Total: $' + total.toFixed(2));
    }

    // Error handling and notifications
    BuilderUI.prototype.showNotification = function(message, type) {
        var $notification = $('<div class="builder-message ' + type + '">' + message + '</div>')
            .hide()
            .insertBefore('.build-sections')
            .slideDown();

        setTimeout(function() {
            $notification.slideUp(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Initialize the builder when document is ready
    $(document).ready(function() {
        var builder = new BuilderUI();

        // Make builder instance available globally
        window.builderUI = builder;
    });

}(window.jQuery);
