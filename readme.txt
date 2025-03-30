=== Hide Product Prices Until Login – for WooCommerce ===
Contributors: nelegines
Tags: woocommerce, hide prices, geolocation, login required, user roles
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Hide WooCommerce product prices and Add to Cart buttons unless customers are logged in or from allowed countries or roles. Ideal for wholesale stores and private shops.

== Description ==

**WooCommerce Hide Prices Until Login or Region** helps store owners control who can see prices and add products to their cart. Useful for wholesale stores, restricted content, or region-specific pricing policies.

🎯 **Key Features:**
- Hide prices for non-logged-in users
- Disable Add to Cart for guests
- Geolocation-based visibility (by allowed countries)
- User role-based visibility (NEW in 1.2.0)
- Custom message with `{login_url}` support
- Redirects users back to the product after login
- Integrated directly into WooCommerce settings
- Per-product custom message override (Lite)
- Test mode for admin previewing behavior
- Supports all product types (simple, variable, etc.)

== Screenshots ==

1. Settings screen under WooCommerce → Products → Hide Prices
2. Custom message field on product edit screen
3. Frontend example of price hidden message

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/hide-prices-until-login` directory or install through the WordPress plugin screen.
2. Activate the plugin through the "Plugins" menu.
3. Go to **WooCommerce → Settings → Products → Hide Prices** to configure.

== Frequently Asked Questions ==

= Can I add a login link in the message? =
Yes! Use the `{login_url}` placeholder in your message. It will be automatically replaced with a clickable link to the login page.

= Can I show a different message for individual products? =
Yes! Edit a product and use the “Hide Price Message” field in the sidebar to override the global message.

= Will it redirect users back after login? =
Yes. After logging in, users are redirected to the product they were viewing.

= Can I simulate as a guest while logged in? =
Yes. Enable “Test Mode” in the plugin settings to preview hidden prices while logged in as an admin.

== Changelog ==

= 1.2.0 =
- ✅ NEW: Hide prices based on user roles
- ✅ NEW: Dynamic UI to manage restricted roles in admin
- ✅ FIX: Ensures restricted roles are saved as an array
- ✅ Tweak: Minor styling and JS enhancements for settings UI

= 1.1.0 =
- ✅ NEW: Per-product custom message override
- ✅ NEW: Hide Add to Cart button in product loops
- ✅ NEW: Login redirect after login back to product
- ✅ NEW: Admin Test Mode for debugging
- ✅ Tweak: Global `{login_url}` placeholder replacement
- ✅ Updated: Translatable strings and inline documentation

= 1.0.0 =
- Initial release with price hiding and region-based logic

== Upgrade Notice ==

= 1.2.0 =
Added support for user role-based restrictions and improved admin settings UX.

= 1.1.0 =
Adds per-product messages, Add to Cart hiding, and admin test mode. Recommended upgrade for full WooCommerce compatibility and control.

== License ==

This plugin is licensed under the GPLv2 or later.
