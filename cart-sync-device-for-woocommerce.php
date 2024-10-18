<?php
/**
 * Plugin Name: Cart Sync Device for WooCommerce
 * Plugin URI: https://github.com/clonerdev/WooCommerce-Cart-Sync-Device
 * Description: Synchronize the WooCommerce cart across devices.
 * Version: 3.0.0
 * Author: Ali Karimi | Nedaye Web
 * Author URI: https://nedayeweb.ir
 * WC requires at least: 6.4
 * Requires PHP: 7.4
 * Tested up to: 6.6.2
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load text domain for translations
function wcsd_load_textdomain() {
    load_plugin_textdomain('cart-sync-device-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'wcsd_load_textdomain');

// Enqueue scripts and styles for front-end
function wcsd_enqueue_scripts() {
    wp_register_script('wcsd-custom-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.0.0', true); // اصلاح مسیر
    wp_enqueue_script('wcsd-custom-js');

    wp_register_style('wcsd-custom-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0'); // اصلاح مسیر
    wp_enqueue_style('wcsd-custom-css');
}
add_action('wp_enqueue_scripts', 'wcsd_enqueue_scripts');

// Enqueue admin scripts and styles
function wcsd_enqueue_admin_scripts($hook_suffix) {
    if (strpos($hook_suffix, 'wcsd') !== false) {
        wp_register_script('wcsd-admin-js', plugin_dir_url(__FILE__) . 'assets/js/wcsd-admin.js', array('jquery'), '1.0.0', true); // اصلاح مسیر
        wp_enqueue_script('wcsd-admin-js');

        wp_register_style('wcsd-admin-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0'); // اصلاح مسیر
        wp_enqueue_style('wcsd-admin-css');
    }
}
add_action('admin_enqueue_scripts', 'wcsd_enqueue_admin_scripts');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cart-sync.php';
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-cart-sync-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-admin-settings.php'; // Ensure settings file is included

// Activate the plugin
register_activation_hook(__FILE__, 'wcsd_create_plugin_custom_table');

// Hook into WooCommerce actions
add_action('woocommerce_add_to_cart', 'wcsd_sync_cart_on_add');
add_action('wp_ajax_wcsd_sync_cart', 'wcsd_sync_cart');
add_action('wp_ajax_nopriv_wcsd_sync_cart', 'wcsd_sync_cart');

// Sync cart when an item is added
function wcsd_sync_cart_on_add($cart_item_key) {
    if (is_user_logged_in()) {
        global $wpdb;
        $user_id = get_current_user_id();
        $cart_data = WC()->cart->get_cart();
        $cart_json = json_encode($cart_data);

        // Save cart to custom table
        $table_name = $wpdb->prefix . 'wcsd_cart';
        $wpdb->replace($table_name, [
            'user_id' => $user_id,
            'cart_data' => $cart_json,
        ]);
    }
}

// AJAX handler to synchronize cart
function wcsd_sync_cart() {
    if (is_user_logged_in()) {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'wcsd_cart';
        $cart_data = $wpdb->get_var($wpdb->prepare("SELECT cart_data FROM $table_name WHERE user_id = %d", $user_id));

        if ($cart_data) {
            $cart_items = json_decode($cart_data, true);
            WC()->cart->empty_cart(); // Clear current cart
            foreach ($cart_items as $item) {
                WC()->cart->add_to_cart($item['product_id'], $item['quantity']);
            }
            wp_send_json_success();
        } else {
            wp_send_json_error(__('No cart data found', 'cart-sync-device-for-woocommerce'));
        }
    } else {
        wp_send_json_error(__('User not logged in', 'cart-sync-device-for-woocommerce'));
    }
}

// Cache control for AJAX requests
add_action('init', 'wcsd_cache_control');
function wcsd_cache_control() {
    if (is_user_logged_in()) {
        // Prevent caching for AJAX responses
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

// Error logging for debugging
function wcsd_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log($message);
    }
}
