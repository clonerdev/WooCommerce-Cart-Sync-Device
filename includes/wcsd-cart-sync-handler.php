<?php
if (!defined('ABSPATH')) {
    exit;
}

// ذخیره‌سازی داده‌های سبد خرید در جدول اختصاصی
function wcsd_save_cart_data_to_custom_table($cart_data) {
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';
        $user_id = get_current_user_id();

        // حذف داده‌های قبلی
        $wpdb->delete($table_name, array('user_id' => $user_id));

        // افزودن داده‌های جدید
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'cart_data' => maybe_serialize($cart_data),
            )
        );
    }
}
add_action('woocommerce_cart_updated', 'wcsd_save_cart_data_to_custom_table');

// بازیابی داده‌های سبد خرید از جدول اختصاصی
function wcsd_restore_cart_data_for_user() {
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcsd_cart_data';
        $user_id = get_current_user_id();

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
        }
    }
}
add_action('wp_loaded', 'wcsd_restore_cart_data_for_user');
