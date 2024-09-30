(function($) {
    // Initialize the admin scripts
    function init() {
        setupTabSwitching();
        toggleWebhookSettings();
        setupWebhookToggle();
        setupTestWebhook();
    }

    // Handle tab switching functionality
    function setupTabSwitching() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            var target = $(this).attr('href');
            $('.nav-tab').removeClass('nav-tab-active'); // Remove active class from all tabs
            $(this).addClass('nav-tab-active'); // Add active class to the clicked tab
            $('.tab-content').hide(); // Hide all tab content
            $(target).show(); // Show the target tab content
        });
    }

    // Show/hide webhook settings based on the "Enable Webhook" checkbox
    function toggleWebhookSettings() {
        if ($('#wcsd_enable_webhook').is(':checked')) {
            $('.webhook-settings').show(); // Show settings if checked
        } else {
            $('.webhook-settings').hide(); // Hide settings if unchecked
        }
    }

    // Setup event handler for the webhook toggle checkbox
    function setupWebhookToggle() {
        $('#wcsd_enable_webhook').on('change', function() {
            toggleWebhookSettings(); // Toggle settings visibility
        });
    }

    // Setup the test webhook button functionality
    function setupTestWebhook() {
        $('#test-webhook').on('click', function() {
            var button = $(this);
            button.prop('disabled', true); // Disable button during request

            // Send AJAX request to test the webhook
            $.post(ajaxurl, { action: 'test_webhook' }, function(response) {
                if (response.success) {
                    alert('Webhook test successful: ' + response.data);
                } else {
                    alert('Webhook test failed: ' + response.data);
                }
            }).fail(function() {
                alert('AJAX request failed.');
            }).always(function() {
                button.prop('disabled', false); // Re-enable button after request completes
            });
        });
    }

    // Run the init function when the document is ready
    $(document).ready(init);
})(jQuery);
