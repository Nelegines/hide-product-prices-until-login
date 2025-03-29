<?php
/**
 * Handles price hiding and Add to Cart disabling based on login and region.
 */
class HPULR_Price_Handler {

    /**
     * Filter WooCommerce product price output.
     *
     * @param string     $price   The original price HTML.
     * @param WC_Product $product The product object.
     * @return string
     */
    public static function filter_price($price, $product) {
        if (self::should_hide_price()) {
            $message = get_option('hpulr_hidden_price_message', __('Login to view price', 'nelegines-hide-prices'));
            return '<span class="price-hidden-msg">' . esc_html($message) . '</span>';
        }

        return $price;
    }

    /**
     * Prevent product from being purchasable.
     *
     * @param bool       $purchasable
     * @param WC_Product $product
     * @return bool
     */
    public static function maybe_disable_purchase($purchasable, $product) {
        return self::should_hide_price() ? false : $purchasable;
    }

    /**
     * Determine if price should be hidden.
     *
     * @return bool
     */
    private static function should_hide_price() {
        // Always show prices to logged-in users
        if (is_user_logged_in()) return false;

        // Get allowed countries from settings (CSV)
        $allowed = array_map('trim', explode(',', get_option('hpulr_allowed_countries', '')));

        // Get user's country using WooCommerce geolocation
        $geo = WC_Geolocation::geolocate_ip();
        $country = $geo['country'] ?? '';

        // If user’s country is not in allowed list, hide price
        return !in_array(strtoupper($country), array_map('strtoupper', $allowed));
    }
}
