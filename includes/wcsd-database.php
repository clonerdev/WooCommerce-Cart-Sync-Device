<?php
if (!defined('ABSPATH')) {
    exit;
}

// Create database table for storing cart data
function wcsd_create_plugin_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcsd_cart_data';
    $charset_collate = $wpdb->get_charset_collate();

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            cart_data longtext NOT NULL,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        error_log("Custom table $table_name created successfully."); // Logging table creation
    } else {
        error_log("Custom table $table_name already exists."); // Logging if table exists
    }
}
