=== Hide Product Prices Until Login – for WooCommerce ===
Contributors: nelegines
Tags: woocommerce, hide prices, geolocation, login required, user roles, category based
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Hide WooCommerce product prices and Add to Cart buttons unless customers are logged in or from allowed countries or roles. Ideal for wholesale stores and private shops.

== Description ==

**WooCommerce Hide Prices Until Login or Region** helps store owners control who can see prices and add products to their cart. Useful for wholesale stores, restricted content, or region-specific pricing policies.

== Developer Features ==

This plugin supports integrations with premium extensions via a global override flag and centralized architecture:

- Set `$GLOBALS['hpulr_force_hide'] = true` before calling `HPULR_Price_Handler::filter_price()` to trigger the free plugin’s hidden price message
- Inject premium settings using `hpulr_additional_settings` filter
- Automatically save premium and custom fields via centralized update hook
- Sanitize both free and premium fields with centralized filter `sanitize_all_options`

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
- Centralized saving and sanitization (NEW in 1.2.1)
- Compatible with premium add-ons (e.g., category-based hiding)

== Screenshots ==

1. Settings screen under WooCommerce → Products → Hide Prices
2. Custom message field on product edit screen
3. Frontend example of price hidden message

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/hide-product-prices-until-login` directory or install through the WordPress plugin screen.
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

= 1.2.1 =
- ✅ REFACTOR: Centralized settings saving and sanitization for both free and premium fields
- ✅ NEW: Added filter `hpulr_additional_settings` to allow premium plugins to register their settings
- ✅ NEW: Unified `sanitize_all_options` method for secure value handling
- ✅ FIX: Prevented fatal error when sanitizing array-based inputs
- ✅ REFACTOR: JavaScript update logic for role and category tables generalized
- ✅ Tweak: Updated hidden field support for WooCommerce compatibility

= 1.2.0 =
- ✅ NEW: Hide prices based on user roles
- ✅ NEW: Dynamic UI to manage restricted roles in admin
- ✅ FIX: Ensures restricted roles are saved as an array
- ✅ FIX: Proper handling of empty state in roles table (no-available-data)
- ✅ FIX: JavaScript updates to restore placeholder row when roles removed
- ✅ REFACTOR: Moved HTML render logic to template files for better maintainability
- ✅ Tweak: Minor styling and JS enhancements for settings UI
- ✅ NEW: Added support for external price hiding override via `$GLOBALS['hpulr_force_hide']`
- ✅ NEW: This enables premium add-ons to trigger hidden price logic (e.g., for category-based restrictions)

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

= 1.2.2 =
Added ABSPATH protection to all PHP files to prevent direct access.

= 1.2.1 =
Introduces centralized saving and sanitization for settings fields, allowing clean integration with premium extensions and avoiding potential saving bugs.

= 1.2.0 =
Added support for user role-based restrictions, improved admin UI, and moved HTML rendering to template files for better extensibility.

== License ==

This plugin is licensed under the GPLv2 or later.
