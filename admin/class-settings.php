<?php

/**
 * Admin settings for WooCommerce Hide Prices plugin (Free Version).
 * This class manages WooCommerce settings, product-level meta fields,
 * and centralized saving for both free and premium settings.
 */
class HPULR_Settings
{

    /**
     * Initialize WooCommerce settings and product meta features.
     * Hooks into WooCommerce to register the plugin's settings, fields,
     * sanitization, and meta box saving.
     */
    public static function init()
    {
        // Register section and fields under WooCommerce → Products
        add_filter('woocommerce_get_sections_products', [self::class, 'add_settings_section']);
        add_filter('woocommerce_get_settings_products', [self::class, 'add_settings_fields'], 10, 2);

        // Register custom field renderers and sanitization
        add_action('woocommerce_admin_field_hpulr_roles_table', [self::class, 'hpulr_render_roles_table_setting']);
        add_action('woocommerce_admin_field_hpulr_restricted_roles', [self::class, 'render_hpulr_restricted_roles_field']);
        add_filter('woocommerce_admin_settings_sanitize_option', [self::class, 'sanitize_all_options'], 10, 3);

        // ✅ Centralized saving for both free and premium settings
        add_action('woocommerce_update_options_products', [self::class, 'save_combined_settings']);

        // Register meta box for per-product custom message
        add_action('add_meta_boxes', function () {
            add_meta_box(
                'hpulr_product_message',
                __('Hide Price Message (Optional)', 'hide-product-prices-until-login'),
                [self::class, 'render_product_message_box'],
                'product',
                'side'
            );
        });

        // Save the custom message for individual products
        add_action('save_post', function ($post_id) {
            if (!isset($_POST['hpulr_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hpulr_nonce'])), 'hpulr_save_product_message')) {
                return;
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return;
            if (!current_user_can('edit_post', $post_id))
                return;

            if (isset($_POST['hpulr_custom_message'])) {
                update_post_meta($post_id, '_hpulr_custom_message', sanitize_text_field(wp_unslash($_POST['hpulr_custom_message'])));
            }
        });
    }

    /**
     * Add a custom settings section tab under WooCommerce → Products.
     *
     * @param array $sections Existing WooCommerce sections.
     * @return array Modified sections including Hide Prices.
     */
    public static function add_settings_section($sections)
    {
        $sections['hpulr_hide_prices'] = __('Hide Prices', 'hide-product-prices-until-login');
        return $sections;
    }

    /**
     * Define the settings fields shown in our custom section.
     *
     * @param array $settings Existing settings.
     * @param string $current_section Active section ID.
     * @return array Fields to show under the "Hide Prices" section.
     */
    public static function add_settings_fields($settings, $current_section)
    {
        if ($current_section === 'hpulr_hide_prices') {
            $settings_fields = [
                [
                    'title' => __('Hide Prices Settings', 'hide-product-prices-until-login'),
                    'type'  => 'title',
                    'desc'  => __('Configure rules for hiding prices and disabling Add to Cart.', 'hide-product-prices-until-login'),
                    'id'    => 'hpulr_hide_prices_title',
                ],
                [
                    'title'   => __('Enable Test Mode', 'hide-product-prices-until-login'),
                    'desc'    => __('Force-hide prices even for logged-in admins (for testing).', 'hide-product-prices-until-login'),
                    'id'      => 'hpulr_test_mode',
                    'default' => 'no',
                    'type'    => 'checkbox',
                ],
                [
                    'title'    => __('Allowed Countries', 'hide-product-prices-until-login'),
                    'desc'     => __('Comma-separated ISO country codes (e.g., US,CA,AU)', 'hide-product-prices-until-login'),
                    'id'       => 'hpulr_allowed_countries',
                    'default'  => '',
                    'type'     => 'text',
                    'desc_tip' => true,
                ],
                [
                    'title'    => __('Hidden Price Message', 'hide-product-prices-until-login'),
                    'desc'     => __('Shown when prices are hidden. Supports {login_url}', 'hide-product-prices-until-login'),
                    'id'       => 'hpulr_hidden_price_message',
                    'default'  => 'Login to view price',
                    'type'     => 'text',
                    'desc_tip' => true,
                ],
                [
                    'name' => __('Restricted Roles (Hide Prices)', 'hide-product-prices-until-login'),
                    'desc' => __('Select roles to restrict price visibility. Selected roles will appear below and be removed from the list.', 'hide-product-prices-until-login'),
                    'type' => 'hpulr_restricted_roles',
                    'id'   => 'hpulr_restricted_roles',
                ],
                [
                    'name' => __('Restricted Roles Table', 'hide-product-prices-until-login'),
                    'type' => 'hpulr_roles_table',
                    'id'   => 'hpulr_roles_table_placeholder',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'hpulr_hide_prices_end',
                ],
            ];

            // 🔁 Allow premium extensions to inject more fields into this section
            return apply_filters('hpulr_settings_fields', $settings_fields, $current_section);
        }

        return $settings;
    }

    /**
     * Combine all settings from free and premium plugins.
     *
     * @return array Full settings array to be saved.
     */
    public static function get_combined_settings()
    {
        $base_settings = self::add_settings_fields([], 'hpulr_hide_prices');

        // 🔁 Let premium inject additional settings
        $premium_settings = apply_filters('hpulr_additional_settings', []);
        if (!empty($premium_settings)) {
            $filtered = array_filter($premium_settings, function ($field) {
                return empty($field['type']) || ($field['type'] !== 'title' && $field['type'] !== 'sectionend');
            });

            $base_settings = array_merge($base_settings, $filtered);
        }

        return $base_settings;
    }

    /**
     * Save all plugin settings (free + premium) in one centralized handler.
     */
    public static function save_combined_settings()
    {
        woocommerce_update_options(self::get_combined_settings());
    }

    /**
     * Get all registered roles in WordPress.
     *
     * @return array Role slugs => Role names.
     */
    public static function get_all_roles()
    {
        global $wp_roles;
        $roles = [];

        foreach ($wp_roles->roles as $key => $role) {
            $roles[$key] = $role['name'];
        }

        return $roles;
    }

    /**
     * Render the per-product custom message input box.
     *
     * @param WP_Post $post The current post object.
     */
    public static function render_product_message_box($post)
    {
        $value = get_post_meta($post->ID, '_hpulr_custom_message', true);
        wp_nonce_field('hpulr_save_product_message', 'hpulr_nonce');
        require_once HPULR_PLUGIN_PATH . 'templates/product-edit-message-box-template.php';
    }

    /**
     * Render the restricted roles table.
     * This is reused inside the WooCommerce settings page.
     */
    public static function output_restricted_roles_table()
    {
        $all_roles      = self::get_all_roles();
        $stored         = get_option('hpulr_restricted_roles', []);
        $selected_roles = is_array($stored) ? $stored : [];

        require_once HPULR_PLUGIN_PATH . 'templates/restricted-roles-table-template.php';
    }

    /**
     * Render the custom dropdown + Add Role button in the settings page.
     *
     * @param array $option The setting field definition.
     */
    public static function render_hpulr_restricted_roles_field($option)
    {
        $all_roles      = self::get_all_roles();
        $stored         = get_option('hpulr_restricted_roles', []);
        $selected_roles = is_array($stored) ? $stored : [];

        require_once HPULR_PLUGIN_PATH . 'templates/selected-roles-fields-template.php';
    }

    /**
     * Render the roles table inside WooCommerce settings.
     *
     * @param array $field The field definition array.
     */
    public static function hpulr_render_roles_table_setting($field)
    {
        echo '<tr>';
        echo '<th scope="row">' . esc_html($field['name']) . '</th>';
        echo '<td>';
        self::output_restricted_roles_table();
        echo '</td>';
        echo '</tr>';
    }

    /**
     * Centralized sanitizer for both free and premium fields.
     *
     * @param mixed $value The value being saved.
     * @param array $option The option definition.
     * @param mixed $raw_value The unsanitized input value.
     * @return mixed The sanitized value.
     */
    public static function sanitize_all_options($value, $option, $raw_value)
    {
        $id = $option['id'];

        switch ($id) {
            case 'hpulr_geo_category_countries':
            case 'hpulr_restricted_roles':
                $list = is_array($raw_value)
                    ? $raw_value
                    : explode(',', wp_unslash($raw_value));
                return array_map('sanitize_text_field', array_filter($list));

            case 'hpulr_geo_category_categories':
                $list = is_array($raw_value)
                    ? $raw_value
                    : explode(',', wp_unslash($raw_value));
                return array_map('absint', array_filter($list));

            default:
                return $value;
        }
    }


}
