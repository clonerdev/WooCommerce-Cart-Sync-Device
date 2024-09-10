<?php
if (!defined('ABSPATH')) {
    exit;
}

// افزودن صفحه تنظیمات افزونه
function wcsd_add_settings_page() {
    add_options_page(
        __('تنظیمات همگام‌سازی سبد خرید', 'wcsd'),
        __('همگام‌سازی سبد خرید', 'wcsd'),
        'manage_options',
        'wcsd-settings',
        'wcsd_render_settings_page'
    );
}
add_action('admin_menu', 'wcsd_add_settings_page');

// نمایش صفحه تنظیمات افزونه
function wcsd_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('تنظیمات همگام‌سازی سبد خرید', 'wcsd'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wcsd_settings_group');
            do_settings_sections('wcsd-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// ثبت تنظیمات افزونه
function wcsd_register_settings() {
    register_setting('wcsd_settings_group', 'wcsd_webhook_enabled');
    register_setting('wcsd_settings_group', 'wcsd_webhook_url');

    add_settings_section(
        'wcsd_general_settings',
        __('تنظیمات عمومی', 'wcsd'),
        null,
        'wcsd-settings'
    );

    add_settings_field(
        'wcsd_webhook_enabled',
        __('فعال‌سازی وب‌هوک', 'wcsd'),
        'wcsd_render_webhook_enabled_field',
        'wcsd-settings',
        'wcsd_general_settings'
    );

    add_settings_field(
        'wcsd_webhook_url',
        __('آدرس وب‌هوک', 'wcsd'),
        'wcsd_render_webhook_url_field',
        'wcsd-settings',
        'wcsd_general_settings'
    );
}
add_action('admin_init', 'wcsd_register_settings');

// فیلد فعال‌سازی وب‌هوک
function wcsd_render_webhook_enabled_field() {
    $value = get_option('wcsd_webhook_enabled', 0);
    ?>
    <input type="checkbox" name="wcsd_webhook_enabled" value="1" <?php checked($value, 1); ?>>
    <label><?php _e('فعال‌سازی وب‌هوک برای ارسال داده‌های سبد خرید.', 'wcsd'); ?></label>
    <p class="description"><?php _e('اگر این گزینه فعال باشد، داده‌های سبد خرید به آدرس وب‌هوک تعیین‌شده ارسال می‌شود.', 'wcsd'); ?></p>
    <?php
}

// فیلد آدرس وب‌هوک
function wcsd_render_webhook_url_field() {
    $value = get_option('wcsd_webhook_url', '');
    ?>
    <input type="text" name="wcsd_webhook_url" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <p class="description"><?php _e('آدرس وب‌هوک برای دریافت داده‌های سبد خرید.', 'wcsd'); ?></p>
    <?php
}
