<?php
/**
 * Adds the 'Hide Prices' tab inside WooCommerce > Settings > Products.
 */
class HPULR_Settings {
    public static function init() {
        add_filter('woocommerce_get_sections_products', [self::class, 'add_settings_section']);
        add_filter('woocommerce_get_settings_products', [self::class, 'add_settings_fields'], 10, 2);
    }

    // Add new tab section under "Products"
    public static function add_settings_section($sections) {
        $sections['hpulr_hide_prices'] = __('Hide Prices', 'nelegines-hide-prices');
        return $sections;
    }

    // Define settings fields inside the tab
    public static function add_settings_fields($settings, $current_section) {
        if ($current_section === 'hpulr_hide_prices') {
            return [
                [
                    'title' => __('Hide Prices Settings', 'nelegines-hide-prices'),
                    'type' => 'title',
                    'desc' => 'Configure rules for hiding prices and disabling Add to Cart.',
                    'id' => 'hpulr_hide_prices_title',
                ],
                [
                    'title'    => __('Allowed Countries', 'nelegines-hide-prices'),
                    'desc'     => __('Comma-separated ISO country codes (e.g., US,CA,AU)', 'nelegines-hide-prices'),
                    'id'       => 'hpulr_allowed_countries',
                    'default'  => '',
                    'type'     => 'text',
                    'desc_tip' => true,
                ],
                [
                    'title'    => __('Hidden Price Message', 'nelegines-hide-prices'),
                    'desc'     => __('Shown when prices are hidden.', 'nelegines-hide-prices'),
                    'id'       => 'hpulr_hidden_price_message',
                    'default'  => 'Login to view price',
                    'type'     => 'text',
                    'desc_tip' => true,
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'hpulr_hide_prices_end',
                ],
            ];
        }

        return $settings;
    }
}
