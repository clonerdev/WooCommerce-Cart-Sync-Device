<?php
/**
 * WooCommerce Cart Sync Device Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

// Webhook Test Functionality
add_action('wp_ajax_test_webhook', 'wcsd_test_webhook');
function wcsd_test_webhook() {
    check_ajax_referer('wcsd_nonce', 'security');
    
    $webhook_url = get_option('wcsd_webhook_url');
    $webhook_secret = get_option('wcsd_webhook_secret');

    if (!empty($webhook_url)) {
        $response = wp_remote_post($webhook_url, [
            'headers' => ['Authorization' => 'Bearer ' . $webhook_secret],
            'body' => wp_json_encode(['test' => true]),
            'timeout' => 15,
            'sslverify' => false,
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        } else {
            wp_send_json_success(wp_remote_retrieve_body($response));
        }
    } else {
        wp_send_json_error('Webhook URL not set');
    }
}

// Save Cart Data to Custom Table
function wcsd_save_cart_data_to_custom_table() {
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';
        $user_id = get_current_user_id();
        $cart_data = WC()->cart->get_cart(); // Get the cart data

        $result = $wpdb->replace($table_name, [
            'user_id' => $user_id,
            'cart_data' => maybe_serialize($cart_data),
        ]);

        if ($result === false) {
            error_log("Failed to save cart data for user $user_id: " . $wpdb->last_error);
        } else {
            error_log("Cart data saved to custom table for user $user_id.");
        }
    }
}

// Restore Cart Data from Custom Table
function wcsd_restore_cart_data_for_user() {
    if (is_user_logged_in()) {
        global $wpdb;
        $user_id = get_current_user_id();
        $cart_data = wcsd_get_cart_data_from_custom_table($user_id);

        if ($cart_data) {
            WC()->cart->empty_cart();
            $cart_items = maybe_unserialize($cart_data);

            if (is_array($cart_items)) {
                foreach ($cart_items as $item) {
                    if (isset($item['product_id'], $item['quantity'])) {
                        WC()->cart->add_to_cart(
                            $item['product_id'], 
                            $item['quantity'], 
                            $item['variation_id'] ?? 0, 
                            $item['variation'] ?? [], 
                            $item['cart_item_data'] ?? []
                        );
                    }
                }
            } else {
                error_log("Cart data for user $user_id is not an array.");
            }
        } else {
            error_log("No cart data found for user $user_id.");
        }
    }
}
add_action('wp_loaded', 'wcsd_restore_cart_data_for_user');

// Get Cart Data from Custom Table
function wcsd_get_cart_data_from_custom_table($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcsd_cart_data';

    return $wpdb->get_var($wpdb->prepare(
        "SELECT cart_data FROM $table_name WHERE user_id = %d",
        $user_id
    ));
}

// Cart Sync Device Class
class Cart_Sync_Device {

    public static function save_cart_data() {
        if (!is_user_logged_in()) {
            return;
        }

        wcsd_save_cart_data_to_custom_table();
    }

    public static function load_cart_data() {
        self::prevent_cache();

        $user_id = get_current_user_id();
        $saved_cart = wcsd_get_cart_data_from_custom_table($user_id);
        if (!empty($saved_cart)) {
            $cart_data = maybe_unserialize($saved_cart);
            if (is_array($cart_data)) {
                WC()->cart->empty_cart();
                foreach ($cart_data as $item) {
                    if (isset($item['product_id'], $item['quantity'])) {
                        WC()->cart->add_to_cart(
                            $item['product_id'], 
                            $item['quantity'], 
                            $item['variation_id'] ?? 0, 
                            $item['variation'] ?? [], 
                            $item['cart_item_data'] ?? []
                        );
                    }
                }
            } else {
                error_log("Cart data for user $user_id is not an array.");
            }
        } else {
            error_log("No saved cart data found for user $user_id.");
        }
    }

    public static function sync_cart_via_ajax() {
        check_ajax_referer('wcsd_nonce', 'security');

        if (!is_user_logged_in()) {
            wp_send_json_error('User is not logged in.');
        }

        self::save_cart_data();
        wp_send_json_success('Cart synced successfully.');
    }

    private static function prevent_cache() {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }

    // Remove item from cart
    public static function remove_item_from_cart() {
        check_ajax_referer('wcsd_nonce', 'security');

        if (!is_user_logged_in()) {
            wp_send_json_error('User is not logged in.');
        }

        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        WC()->cart->remove_cart_item($cart_item_key);

        // Update cart data in custom table
        self::save_cart_data();

        wp_send_json_success('Item removed from cart.');
    }

    // Update item quantity in cart
    public static function update_item_quantity() {
        check_ajax_referer('wcsd_nonce', 'security');

        if (!is_user_logged_in()) {
            wp_send_json_error('User is not logged in.');
        }

        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $quantity = intval($_POST['quantity']);
        WC()->cart->set_quantity($cart_item_key, $quantity);

        // Update cart data in custom table
        self::save_cart_data();

        wp_send_json_success('Item quantity updated.');
    }
}

add_action('wp_ajax_sync_cart', ['Cart_Sync_Device', 'sync_cart_via_ajax']);
add_action('wp_ajax_remove_item_from_cart', ['Cart_Sync_Device', 'remove_item_from_cart']);
add_action('wp_ajax_update_item_quantity', ['Cart_Sync_Device', 'update_item_quantity']);
