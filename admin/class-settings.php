<?php

/**
 * Admin settings for WooCommerce Hide Prices plugin.
 */
class HPULR_Settings
{

    /**
     * Initialize WooCommerce settings and product meta features.
     */
    public static function init()
    {
        add_filter('woocommerce_get_sections_products', [self::class, 'add_settings_section']);
        add_filter('woocommerce_get_settings_products', [self::class, 'add_settings_fields'], 10, 2);

        // Register WooCommerce settings filters or renderers
        add_action('woocommerce_admin_field_hpulr_roles_table', [self::class, 'hpulr_render_roles_table_setting']);
        add_action('woocommerce_admin_field_hpulr_restricted_roles', [self::class, 'render_hpulr_restricted_roles_field']);
        add_filter('woocommerce_admin_settings_sanitize_option', [self::class, 'sanitize_restricted_roles'], 10, 3);


        // Add product-level meta box
        add_action('add_meta_boxes', function () {
            add_meta_box(
                'hpulr_product_message',
                __('Hide Price Message (Optional)', 'hide-product-prices-until-login'),
                [self::class, 'render_product_message_box'],
                'product',
                'side'
            );
        });

        // Save per-product meta field
        add_action('save_post', function ($post_id) {
            /**
             * Verify the nonce and save the field securely.
             */
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
     * Add a new section tab under WooCommerce → Products.
     *
     * @param array $sections
     * @return array
     */
    public static function add_settings_section($sections)
    {
        $sections['hpulr_hide_prices'] = __('Hide Prices', 'hide-product-prices-until-login');
        return $sections;
    }

    /**
     * Add settings fields to the custom section.
     *
     * @param array $settings
     * @param string $current_section
     * @return array
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
                    'type' => 'hpulr_restricted_roles', // custom field type
                    'id'   => 'hpulr_restricted_roles',
                ],
                [
                    'name' => __('Restricted Roles Table', 'hide-product-prices-until-login'),
                    'type' => 'hpulr_roles_table', // Custom type hook
                    'id'   => 'hpulr_roles_table_placeholder',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'hpulr_hide_prices_end',
                ],
            ];

            // Apply the filter to allow premium add-ons to modify or add settings.
            return apply_filters('hpulr_settings_fields', $settings_fields, $current_section);

        }

        return $settings;
    }

    /**
     * Retrieve all registered user roles in WordPress.
     *
     * This is used in the plugin settings to allow the admin
     * to select which roles should have prices hidden from them.
     *
     * @return array Associative array of role slugs => role names.
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
     * Render the custom message input on the product edit screen.
     *
     * @param WP_Post $post
     */
    public static function render_product_message_box($post)
    {
        $value = get_post_meta($post->ID, '_hpulr_custom_message', true);
        wp_nonce_field('hpulr_save_product_message', 'hpulr_nonce');

        require_once HPULR_PLUGIN_PATH . 'templates/product-edit-message-box-template.php';
    }

    /**
     * Render the restricted roles table and hidden input.
     * This is intended to be reused inside WooCommerce settings.
     */
    public static function output_restricted_roles_table()
    {
        $all_roles      = HPULR_Settings::get_all_roles();
        $stored         = get_option('hpulr_restricted_roles', []);
        $selected_roles = is_array($stored) ? $stored : [];

        require_once HPULR_PLUGIN_PATH . 'templates/restricted-roles-table-template.php';
    }

    /**
     * Render the role selector field for WooCommerce settings.
     * This field outputs a dropdown of available user roles and an "Add Role" button.
     * The selected roles will be shown in a separate table rendered by another field.
     *
     * @param array $option The WooCommerce setting field config.
     */
    public static function render_hpulr_restricted_roles_field($option)
    {
        $all_roles      = HPULR_Settings::get_all_roles();
        $stored         = get_option('hpulr_restricted_roles', []);
        $selected_roles = is_array($stored) ? $stored : [];

        require_once HPULR_PLUGIN_PATH . 'templates/selected-roles-fields-template.php';
    }


    /**
     * Render the roles table inside a WooCommerce form-table row.
     *
     * @param array $field The field definition array.
     */
    public static function hpulr_render_roles_table_setting($field)
    {
        echo '<tr class>';
        echo '<th scope="row">' . esc_html($field['name']) . '</th>';
        echo '<td>';
        HPULR_Settings::output_restricted_roles_table();
        echo '</td>';
        echo '</tr>';
    }

    /**
     * Sanitize and save the restricted roles setting.
     *
     * @param mixed $value The raw value submitted.
     * @param array $option Option array from settings definition.
     * @param string $raw_value The unsanitized value from $_POST.
     *
     * @return mixed The sanitized value.
     */
    public static function sanitize_restricted_roles($value, $option, $raw_value)
    {
        if ($option['id'] === 'hpulr_restricted_roles') {
            $decoded = array_filter(explode(',', wp_unslash($raw_value)));

            if (is_array($decoded)) {
                return array_map('sanitize_text_field', $decoded);
            }

            return []; // fallback if decoding fails
        }

        return $value;
    }

}
