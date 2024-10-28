<?php
if (!defined('ABSPATH')) {
    exit;
}

// Create database table for storing cart data
function wcsd_create_plugin_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcsd_cart_data';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create the table
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL UNIQUE, -- Ensure only one entry per user
        cart_data LONGTEXT NOT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Include the upgrade file to use dbDelta
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    // Check if the table was created or not
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        error_log("Custom table $table_name created successfully.");
    } else {
        error_log("Custom table $table_name already exists.");
    }
}

// Use the activation hook for the plugin
register_activation_hook(__FILE__, 'wcsd_create_plugin_custom_table');
