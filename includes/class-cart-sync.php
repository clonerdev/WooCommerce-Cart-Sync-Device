<?php

class Cart_Sync_Device {

    public static function save_cart_data($user_id) {
        // Ensure user is logged in
        if (!is_user_logged_in()) {
            return;
        }

        // Get cart data
        $cart = WC()->cart;

        // Validate cart object and save cart data in user meta
        if (is_a($cart, 'WC_Cart')) {
            $cart_contents = $cart->get_cart();
            if (is_array($cart_contents)) {
                update_user_meta($user_id, '_cart_sync_data', wp_json_encode($cart_contents));
                wcsd_save_cart_data_to_custom_table($cart_contents); // Save to custom table for caching compatibility
            }
        }
    }

    public static function load_cart_data($user_id) {
        // Prevent cache interference
        self::prevent_cache();

        // Retrieve saved cart data from custom table
        $saved_cart = wcsd_get_cart_data_from_custom_table($user_id);

        if (!empty($saved_cart)) {
            $cart_data = json_decode($saved_cart, true);
            if (is_array($cart_data)) {
                $cart = WC()->cart;

                if (is_a($cart, 'WC_Cart')) {
                    // Empty current cart and load saved cart
                    $cart->empty_cart();
                    foreach ($cart_data as $cart_item_key => $cart_item) {
                        if (isset($cart_item['product_id'], $cart_item['quantity'])) {
                            $cart->add_to_cart(
                                $cart_item['product_id'], 
                                $cart_item['quantity'], 
                                $cart_item['variation_id'] ?? 0, 
                                $cart_item['variation'] ?? [], 
                                $cart_item['cart_item_data'] ?? []
                            );
                        }
                    }
                }
            }
        } else {
            error_log("No cart data found in custom table for user $user_id."); // Logging error
        }
    }

    public static function sync_cart_via_ajax() {
        if (!is_user_logged_in()) {
            wp_send_json_error('User is not logged in.');
        }

        $user_id = get_current_user_id();
        self::save_cart_data($user_id);

        wp_send_json_success('Cart synced successfully.');
    }

    // Prevent cache interference for AJAX requests
    private static function prevent_cache() {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

// Hook for AJAX action to sync cart
add_action('wp_ajax_sync_cart', ['Cart_Sync_Device', 'sync_cart_via_ajax']);
