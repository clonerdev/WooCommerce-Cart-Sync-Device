<?php

/*
 * Plugin Name: WooCommerce Cart Sync Device
 * Plugin URI: https://github.com/clonerdev/WooCommerce-Cart-Sync-Device
 * Description:  WooCommerce Cart Sync Device enables seamless synchronization of WooCommerce shopping carts across multiple devices for logged-in users. The plugin ensures that users can access and continue their shopping experience on different devices without losing their cart contents. 
 * Version: 1.0.4
 * Author: Ali Karimi | Nedaye Web
 * Author URI: https://nedayeweb.ir
 * WC requires at least: 6.4
 * Requires PHP: 7.4
 * Text Domain: wcsd
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// بارگذاری فایل‌های مورد نیاز
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-cart-sync-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/wcsd-admin-settings.php';

// فعال‌سازی افزونه
register_activation_hook(__FILE__, 'wcsd_activate');
function wcsd_activate() {
    wcsd_create_plugin_custom_table(); // ایجاد جدول دیتابیس
    flush_rewrite_rules();
}

// غیرفعال‌سازی افزونه
register_deactivation_hook(__FILE__, 'wcsd_deactivate');
function wcsd_deactivate() {
    flush_rewrite_rules();
}

// بارگذاری متن‌های افزونه
add_action('plugins_loaded', 'wcsd_load_textdomain');
function wcsd_load_textdomain() {
    load_plugin_textdomain('wcsd', false, basename(dirname(__FILE__)) . '/languages');
}
