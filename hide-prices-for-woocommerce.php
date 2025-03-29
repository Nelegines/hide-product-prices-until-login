<?php
/**
 * Plugin Name: Hide Prices for WooCommerce
 * Plugin URI: https://nelegines.com/plugins/hide-prices
 * Description: A plugin by Nelegines to hide WooCommerce product prices and disables purchasing unless the user is
 * logged in or from an allowed region.
 * Version: 1.1.0
 * Author: Nelegines
 * Author URI: https://nelegines.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hide-prices-for-woocommerce
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

// Define constants
define('HPULR_PLUGIN_PATH', plugin_dir_path(__FILE__)); // Filesystem path
define('HPULR_PLUGIN_URL', plugin_dir_url(__FILE__));   // URL path
define('HPULR_VERSION', '1.1.0');

// Autoloader for class files in includes/
spl_autoload_register(function ($class) {
    $prefix = 'HPULR_'; // Plugin-specific class prefix
    $base_dir = HPULR_PLUGIN_PATH . 'includes/';

    // Only load our plugin's classes
    if (strpos($class, $prefix) !== 0) return;

    // Convert class name to lowercase dashed file name
    $relative_class = strtolower(str_replace('_', '-', substr($class, strlen($prefix))));
    $file = $base_dir . 'class-' . $relative_class . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Kick off the plugin
add_action('plugins_loaded', function () {
    if (class_exists('HPULR_Init')) {
        HPULR_Init::get_instance(); // Bootstrap the plugin
    }
});
