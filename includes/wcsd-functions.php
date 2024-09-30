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
    if (function_exists('litespeed_cache_clear')) {
        litespeed_cache_clear(); // Clear LiteSpeed cache
        error_log("LiteSpeed cache cleared for cart."); // Logging cache clear
    }
}
