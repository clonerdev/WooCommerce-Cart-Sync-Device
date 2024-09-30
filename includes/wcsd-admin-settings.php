<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add submenu page under WooCommerce
 */
function wcsd_add_submenu_page() {
    add_submenu_page(
        'woocommerce', // Parent slug
        __( 'WooCommerce Cart Sync Device', 'cart-sync-device-for-woocommerce' ), // Page title
        __( 'Cart Sync Device', 'cart-sync-device-for-woocommerce' ), // Menu title
        'manage_options', // Capability
        'wcsd_settings', // Menu slug
        'wcsd_settings_page' // Function to display settings page
    );
}
add_action( 'admin_menu', 'wcsd_add_submenu_page' );

/**
 * Settings page
 */
function wcsd_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('WooCommerce Cart Sync Device Settings', 'cart-sync-device-for-woocommerce'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wcsd_settings_group' );
            do_settings_sections( 'wcsd_settings_group' );

            // Render settings tabs
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="#info" class="nav-tab nav-tab-active"><?php esc_html_e('Info', 'cart-sync-device-for-woocommerce'); ?></a>
                <a href="#webhook" class="nav-tab"><?php esc_html_e('Webhooks', 'cart-sync-device-for-woocommerce'); ?></a>
                <a href="#support" class="nav-tab"><?php esc_html_e('Support', 'cart-sync-device-for-woocommerce'); ?></a>
            </h2>

            <!-- Info Settings -->
            <div id="info" class="tab-content active" style="overflow-y: auto; max-height: 400px;">
                <h2><?php esc_html_e('WooCommerce Cart Sync Device', 'cart-sync-device-for-woocommerce'); ?></h2>
                <p><?php esc_html_e('This plugin allows users to sync their shopping carts across devices for logged-in users.', 'cart-sync-device-for-woocommerce'); ?></p>
            </div>

            <!-- Webhook Settings -->
            <div id="webhook" class="tab-content" style="display:none;">
                <h2><?php esc_html_e('Webhook Settings', 'cart-sync-device-for-woocommerce'); ?></h2>
                <p><?php esc_html_e('Configure webhook settings to receive real-time notifications of cart updates.', 'cart-sync-device-for-woocommerce'); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Enable Webhook', 'cart-sync-device-for-woocommerce'); ?></th>
                        <td>
                            <input type="checkbox" name="wcsd_enable_webhook" id="wcsd_enable_webhook" value="1" <?php checked( get_option('wcsd_enable_webhook'), 1 ); ?> />
                            <label for="wcsd_enable_webhook"><?php esc_html_e('Enable Webhook functionality', 'cart-sync-device-for-woocommerce'); ?></label>
                        </td>
                    </tr>
                    <tr class="webhook-settings">
                        <th scope="row"><?php esc_html_e('Webhook URL', 'cart-sync-device-for-woocommerce'); ?></th>
                        <td>
                            <input type="text" name="wcsd_webhook_url" id="wcsd_webhook_url" value="<?php echo esc_attr( get_option('wcsd_webhook_url') ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter the Webhook URL.', 'cart-sync-device-for-woocommerce'); ?></p>
                        </td>
                    </tr>
                    <tr class="webhook-settings">
                        <th scope="row"><?php esc_html_e('Webhook Secret', 'cart-sync-device-for-woocommerce'); ?></th>
                        <td>
                            <input type="text" name="wcsd_webhook_secret" id="wcsd_webhook_secret" value="<?php echo esc_attr( get_option('wcsd_webhook_secret') ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter the Webhook Secret key.', 'cart-sync-device-for-woocommerce'); ?></p>
                        </td>
                    </tr>
                    <tr class="webhook-settings">
                        <th scope="row"><?php esc_html_e('Webhook Authentication', 'cart-sync-device-for-woocommerce'); ?></th>
                        <td>
                            <input type="text" name="wcsd_webhook_authentication" id="wcsd_webhook_authentication" value="<?php echo esc_attr( get_option('wcsd_webhook_authentication') ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter authentication token for Webhook.', 'cart-sync-device-for-woocommerce'); ?></p>
                        </td>
                    </tr>
                    <tr class="webhook-settings">
                        <td colspan="2">
                            <button type="button" id="test-webhook" class="button button-primary"><?php esc_html_e('Test Webhook', 'cart-sync-device-for-woocommerce'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Support -->
            <div id="support" class="tab-content" style="display:none;">
                <h2><?php esc_html_e('Support', 'cart-sync-device-for-woocommerce'); ?></h2>
                <div class="support-button" style="text-align: center;">
                    <a href="https://nedayeweb.ir/" target="_blank" class="button" style="background-color: rgb(66,90,186); color: white;"><?php esc_html_e('NedayeWEB', 'cart-sync-device-for-woocommerce'); ?></a>
                    <a href="https://github.com/clonerdev/WooCommerce-Cart-Sync-Device" target="_blank" class="button" style="background-color: black; color: white;"><?php esc_html_e('GitHub', 'cart-sync-device-for-woocommerce'); ?></a>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>

    <style>
        .nav-tab-wrapper .nav-tab {
            background-color: rgb(66,90,186);
            color: white;
        }
        .nav-tab-active {
            background-color: white;
            color: black;
        }
        .tab-content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').removeClass('active').hide();
                $($(this).attr('href')).addClass('active').show();
            });

            $('#wcsd_enable_webhook').change(function() {
                if ($(this).is(':checked')) {
                    $('.webhook-settings').show();
                } else {
                    $('.webhook-settings').hide();
                }
            }).change(); // Initial trigger to set visibility

            $('#test-webhook').click(function() {
                $.post(ajaxurl, {
                    action: 'test_webhook',
                    // Add any additional data here if needed
                }, function(response) {
                    if (response.success) {
                        alert('Webhook Test Successful: ' + response.data);
                    } else {
                        alert('Webhook Test Failed: ' + response.data);
                    }
                });
            });
        });
    </script>
    <?php
}

// Register settings
function wcsd_register_settings() {
    register_setting('wcsd_settings_group', 'wcsd_enable_webhook');
    register_setting('wcsd_settings_group', 'wcsd_webhook_url');
    register_setting('wcsd_settings_group', 'wcsd_webhook_secret');
    register_setting('wcsd_settings_group', 'wcsd_webhook_authentication');
}
add_action('admin_init', 'wcsd_register_settings');
?>
