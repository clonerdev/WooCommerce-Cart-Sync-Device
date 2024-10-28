=== Cart Sync Device for WooCommerce ===
Contributors: persianweb
Tags: woocommerce, woocommerce cart, order sync, woocommerce order
Requires at least: 4.7
Tested up to: 6.6.2
Stable tag: 3.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce Cart Sync Device enables seamless synchronization of WooCommerce shopping carts across multiple devices for logged-in users. The plugin ensures that users can access and continue their shopping experience on different devices without losing their cart contents.

== Description ==

WooCommerce Cart Sync Device synchronizes the WooCommerce shopping cart data for users across different devices. Whether your customers start shopping on a mobile device and finish on a desktop, their cart items remain intact. This plugin is especially useful for users who frequently switch between devices, ensuring a consistent shopping experience.

Features include:
* Device-to-device cart synchronization: Automatically sync cart data between multiple devices for logged-in users.
* Real-time updates: Cart updates are instantly synchronized across devices using webhooks.
* Custom database table: Stores cart data for quick retrieval and synchronization.
* Admin settings: Configure synchronization options, including webhook URL for real-time syncing.
* User-friendly: Seamlessly integrates with WooCommerce without disrupting the user experience.

== Frequently Asked Questions ==

= How does the plugin handle cart synchronization? =

The plugin saves the cart data in a custom database table whenever a user updates their cart. This data is then retrieved and synchronized across devices when the user logs in on a different device.

= Does the plugin work with guest users? =

No, the plugin is designed to sync carts only for logged-in users.

= How are webhooks used in the plugin? =

Webhooks are triggered when the cart is updated, allowing real-time synchronization with other devices.

== Changelog ==

= 3.1.0 =
* Improved file structure and organization.
* Moved inline styles and scripts to external CSS and JS files.
* Resolved issues with creating the custom table `wp_wcsd_cart_data`.
* Fixed errors related to fetching cart data from the custom table.
* Enhanced AJAX handlers for syncing cart data with better error handling.
* Improved cart synchronization for item additions, removals, and quantity updates.
* Updated webhook test functionality for secure communication.
* Added extensive error logging for debugging purposes.
* Updated translation strings for improved localization.


= 3.0.0 =
* Synchronization with Litespeed cache
* Development
* Fix Bug

= 2.5.0 =
* Synchronization with Litespeed cache
* Fix Bug

= 2.0.0 =
* Add plugin setting
* Adding Persian language to the plugin
* Fix Bug

= 1.0.4 =
* Fix Bug

= 1.0.0 =
* Initial release with full functionality for synchronizing WooCommerce carts across devices for logged-in users.
* Custom database table for storing and retrieving cart data.
* Admin settings for configuring the plugin.
* Real-time cart synchronization using webhooks.

== Upgrade Notice ==

= 2.5.0 =
* Synchronization with Litespeed cache
* Fix Bug

= 2.0.0 =
* Add plugin setting
* Adding Persian language to the plugin
* Fix Bug

= 1.0.4 =
* Fix plugin problems

= 1.0.0 =
* Initial release with full functionality for synchronizing WooCommerce carts across devices for logged-in users.
* Custom database table for storing and retrieving cart data.
* Admin settings for configuring the plugin.
* Real-time cart synchronization using webhooks.

== Additional Information ==

### About the Author

Ali Karimi is a web developer with extensive experience in WordPress and WooCommerce. He specializes in developing custom plugins and themes to enhance the functionality of WordPress sites. Visit [Nedaye Web](https://nedayeweb.ir) for more information.

### Support

For support, please visit the [support forum](https://wordpress.org/support/plugin/WooCommerce-Cart-Sync-Device) or contact us via [Nedaye Web](https://nedayeweb.ir).

### Documentation

Comprehensive documentation for the plugin can be found [here](https://github.com/clonerdev/WooCommerce-Cart-Sync-Device).

### Contributions

We welcome contributions to the plugin. Please feel free to submit issues or pull requests on [GitHub](https://github.com/clonerdev/WooCommerce-Cart-Sync-Device).

### License

This plugin is licensed under the GPLv2 or later. For more information, please visit the [license page](https://www.gnu.org/licenses/gpl-2.0.html).
