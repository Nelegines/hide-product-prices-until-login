<?php
/**
 * Handles WooCommerce price and purchase visibility based on login or region.
 */
class HPULR_Price_Handler {

    /**
     * Filters the product price HTML.
     *
     * If price should be hidden, it replaces the price with a product-specific or global message.
     *
     * @param string     $price   The original price HTML.
     * @param WC_Product $product WooCommerce product object.
     * @return string
     */
    public static function filter_price($price, $product) {
        if (self::should_hide_price()) {
            $redirect_url = get_permalink($product->get_id());

            // Get login URL with redirect
            $login_url = function_exists('wc_get_page_permalink')
                ? add_query_arg('redirect_to', urlencode($redirect_url), wc_get_page_permalink('myaccount'))
                : wp_login_url($redirect_url);

            // ✅ Check for per-product message first
            $custom_message = get_post_meta($product->get_id(), '_hpulr_custom_message', true);
            $raw_message = trim($custom_message ?: get_option('hpulr_hidden_price_message', ''));

            if (empty($raw_message)) {
                $raw_message = 'Please <a href="{login_url}">login</a> to view this price.';
            }

            // Replace {login_url} with full anchor
            if (strpos($raw_message, '{login_url}') !== false) {
                $login_link = '<a href="' . esc_url($login_url) . '">' . __('Login here', 'hide-product-prices-until-login') . '</a>';
                $message = str_replace('{login_url}', $login_link, $raw_message);
            } else {
                $message = $raw_message . ' <a href="' . esc_url($login_url) . '">' . __('Login', 'hide-product-prices-until-login') . '</a>';
            }

            return '<span class="price-hidden-msg">' . wp_kses_post($message) . '</span>';
        }

        return $price;
    }


    /**
     * Disables purchasing for restricted users.
     *
     * @param bool       $purchasable Whether the product can be purchased.
     * @param WC_Product $product     WooCommerce product.
     * @return bool
     */
    public static function maybe_disable_purchase($purchasable, $product) {
        return self::should_hide_price() ? false : $purchasable;
    }

    /**
     * Hides Add to Cart button in loop/archive templates if needed.
     *
     * @param string     $button   The Add to Cart button HTML.
     * @param WC_Product $product  WooCommerce product.
     * @return string
     */
    public static function maybe_hide_add_to_cart($button, $product) {
        return self::should_hide_price() ? '' : $button;
    }

    /**
     * Determines whether the price should be hidden.
     *
     * - Allows bypass for logged-in users (unless test mode is enabled).
     * - Checks allowed countries via WooCommerce geolocation.
     *
     * @return bool
     */
    private static function should_hide_price() {
        // If admin and test mode enabled, simulate hiding
        if (current_user_can('manage_woocommerce') && get_option('hpulr_test_mode') === 'yes') {
            return true;
        }

        // Skip if user is logged in
        if (is_user_logged_in()) return false;

        // Get allowed countries from settings
        $allowed = array_map('trim', explode(',', get_option('hpulr_allowed_countries', '')));
        $geo     = WC_Geolocation::geolocate_ip();
        $country = $geo['country'] ?? '';

        return !in_array(strtoupper($country), array_map('strtoupper', $allowed));
    }
}
