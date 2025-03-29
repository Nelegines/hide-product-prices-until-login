<?php
/**
 * Handles plugin asset loading (CSS, JS).
 */
class HPULR_Assets {

    /**
     * Hook into WordPress to load assets.
     */
    public static function init() {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_styles']);
    }

    /**
     * Load plugin styles on the frontend.
     */
    public static function enqueue_styles() {
        wp_enqueue_style(
            'hpulr-style',
            HPULR_PLUGIN_URL . 'assets/css/style.css',
            [],
            HPULR_VERSION
        );
    }
}
