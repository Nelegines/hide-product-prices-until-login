<?php

/**
 * Handles plugin asset loading (CSS, JS).
 */
class HPULR_Assets
{

    /**
     * Hook into WordPress to load assets.
     */
    public static function init()
    {
        // Frontend styles/scripts (if needed)
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_styles']);

        // Admin styles/scripts for settings page
        add_action('admin_enqueue_scripts', [self::class, 'hpulr_enqueue_admin_scripts']);
    }

    /**
     * Enqueue admin script for dynamic restricted roles UI.
     */
    public static function hpulr_enqueue_admin_scripts($hook)
    {
        // Only enqueue on WooCommerce settings page
        if ($hook !== 'woocommerce_page_wc-settings')
            return;

        // Only enqueue on our plugin settings tab
        if (!isset($_GET['tab']) || $_GET['tab'] !== 'products' || !isset($_GET['section']) || $_GET['section'] !== 'hpulr_hide_prices') {
            return;
        }

        wp_enqueue_style(
            'hpulr-admin-style',
            HPULR_PLUGIN_URL . 'assets/css/admin-style.css',
            [],
            HPULR_VERSION
        );

        wp_enqueue_script(
            'hpulr-admin-roles',
            HPULR_PLUGIN_URL . 'assets/js/admin-restricted-roles.js',
            ['jquery'],
            HPULR_VERSION,
            true
        );
    }

    /**
     * Load plugin styles on the frontend.
     */
    public static function enqueue_styles()
    {
        wp_enqueue_style(
            'hpulr-style',
            HPULR_PLUGIN_URL . 'assets/css/style.css',
            [],
            HPULR_VERSION
        );
    }
}
