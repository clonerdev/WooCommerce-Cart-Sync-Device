<?php
/**
 * General functions for WooCommerce Cart Sync Device
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Test Webhook functionality
 */
add_action( 'wp_ajax_test_webhook', 'wcsd_test_webhook' );

if ( ! function_exists( 'wcsd_test_webhook' ) ) {
    function wcsd_test_webhook() {
        $webhook_url = get_option( 'wcsd_webhook_url' );
        $webhook_secret = get_option( 'wcsd_webhook_secret' );

        if ( ! empty( $webhook_url ) ) {
            $response = wp_remote_post( $webhook_url, array(
                'headers' => array( 'Authorization' => 'Bearer ' . $webhook_secret ),
                'body'    => wp_json_encode( array( 'test' => true ) ),
                'timeout' => 15, // Add a timeout to prevent hanging
                'sslverify' => false, // Disable SSL verification (useful for localhost)
            ) );

            if ( is_wp_error( $response ) ) {
                wp_send_json_error( $response->get_error_message() );
            } else {
                wp_send_json_success( wp_remote_retrieve_body( $response ) );
            }
        } else {
            wp_send_json_error( 'Webhook URL not set' );
        }
    }
}

/**
 * Clear cart cache if LiteSpeed cache is enabled
 */
add_action('wp_loaded', 'wcsd_clear_cart_cache');

function wcsd_clear_cart_cache() {
    // Check if WooCommerce is loaded and cart is available
    if (class_exists('WooCommerce') && WC()->cart) {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $current_cart = WC()->cart->get_cart();
            $saved_cart = wcsd_get_cart_data_from_custom_table($user_id); // Get saved cart data

            // Check if saved cart data exists before comparing
            if ($saved_cart && $saved_cart !== wp_json_encode($current_cart)) {
                if (function_exists('litespeed_purge_private')) {
                    litespeed_purge_private(); // Clear LiteSpeed cache for logged-in user
                    error_log("LiteSpeed cache cleared for user $user_id's cart."); // Logging cache clear
                }
            }
        }
    } else {
        error_log("WooCommerce cart not available or WooCommerce not loaded.");
    }
}

// Save cart data to custom table
if ( ! function_exists( 'wcsd_save_cart_data_to_custom_table' ) ) {
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
}

// Restore cart data from custom table
if ( ! function_exists( 'wcsd_restore_cart_data_for_user' ) ) {
    function wcsd_restore_cart_data_for_user() {
        if (is_user_logged_in()) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'wcsd_cart_data';
            $user_id = get_current_user_id();

            // Prevent cache interference
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');

            $cart_data = wcsd_get_cart_data_from_custom_table($user_id);

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
}

// Ensure the restore function is called on wp_loaded
add_action('wp_loaded', 'wcsd_restore_cart_data_for_user');

// Get cart data from custom table
if ( ! function_exists( 'wcsd_get_cart_data_from_custom_table' ) ) {
    function wcsd_get_cart_data_from_custom_table($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';

        return $wpdb->get_var($wpdb->prepare(
            "SELECT cart_data FROM $table_name WHERE user_id = %d",
            $user_id
        ));
    }
}
