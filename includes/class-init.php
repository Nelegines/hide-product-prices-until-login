<?php

/**
 * Initializes the plugin by setting up hooks and loading settings.
 */
class HPULR_Init
{
    private static $instance;

    // Singleton pattern to ensure only one instance
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Private constructor sets up everything
    private function __construct()
    {
        // Load settings page in WP Admin
        if (is_admin()) {
            require_once HPULR_PLUGIN_PATH . 'admin/class-settings.php';
            HPULR_Settings::init();
        }

        // Load public assets
        require_once HPULR_PLUGIN_PATH . 'includes/class-assets.php';
        HPULR_Assets::init();

        // Hook into WooCommerce to override price display and purchasability
        add_filter('woocommerce_get_price_html', ['HPULR_Price_Handler', 'filter_price'], 10, 2);
        add_filter('woocommerce_is_purchasable', ['HPULR_Price_Handler', 'maybe_disable_purchase'], 10, 2);
        add_filter('woocommerce_loop_add_to_cart_link', ['HPULR_Price_Handler', 'maybe_hide_add_to_cart'], 10, 2);

    }
}
