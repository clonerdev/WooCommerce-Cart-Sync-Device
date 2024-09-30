<?php
if (!defined('ABSPATH')) {
    exit;
}

// Save cart data to custom table
function wcsd_save_cart_data_to_custom_table($cart_data) {
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';
        $user_id = get_current_user_id();

        // Remove previous data
        $wpdb->delete($table_name, array('user_id' => $user_id));

        // Add new data
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'cart_data' => maybe_serialize($cart_data),
            )
        );

        error_log("Cart data saved to custom table for user $user_id: " . maybe_serialize($cart_data)); // Logging success
    }
}

// Restore cart data from custom table
function wcsd_restore_cart_data_for_user() {
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';
        $user_id = get_current_user_id();

        // Prevent cache interference
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        $cart_data = $wpdb->get_var($wpdb->prepare(
            "SELECT cart_data FROM $table_name WHERE user_id = %d",
            $user_id
        ));

        if ($cart_data) {
            WC()->cart->empty_cart();
            $cart_data = maybe_unserialize($cart_data);

            foreach ($cart_data as $item) {
                WC()->cart->add_to_cart($item['product_id'], $item['quantity'], $item['variation_id'], $item['variation'], $item['cart_item_data']);
            }
        } else {
            error_log("No cart data found in custom table for user $user_id."); // Logging error
        }
    }
}
add_action('wp_loaded', 'wcsd_restore_cart_data_for_user');

// Get cart data from custom table
function wcsd_get_cart_data_from_custom_table($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcsd_cart_data';

    return $wpdb->get_var($wpdb->prepare(
        "SELECT cart_data FROM $table_name WHERE user_id = %d",
        $user_id
    ));
}
