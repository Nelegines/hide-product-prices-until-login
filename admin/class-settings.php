<?php
/**
 * Admin settings for WooCommerce Hide Prices plugin.
 */
class HPULR_Settings {

    /**
     * Initialize WooCommerce settings and product meta features.
     */
    public static function init() {
        add_filter('woocommerce_get_sections_products', [self::class, 'add_settings_section']);
        add_filter('woocommerce_get_settings_products', [self::class, 'add_settings_fields'], 10, 2);

        // Add product-level meta box
        add_action('add_meta_boxes', function () {
            add_meta_box(
                'hpulr_product_message',
                __('Hide Price Message (Optional)', 'nelegines-hide-prices'),
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
            if (!isset($_POST['hpulr_nonce']) || !wp_verify_nonce($_POST['hpulr_nonce'], 'hpulr_save_product_message')) {
                return;
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if (!current_user_can('edit_post', $post_id)) return;

            if (isset($_POST['hpulr_custom_message'])) {
                update_post_meta($post_id, '_hpulr_custom_message', sanitize_text_field($_POST['hpulr_custom_message']));
            }
        });
    }

    /**
     * Add a new section tab under WooCommerce → Products.
     *
     * @param array $sections
     * @return array
     */
    public static function add_settings_section($sections) {
        $sections['hpulr_hide_prices'] = __('Hide Prices', 'nelegines-hide-prices');
        return $sections;
    }

    /**
     * Add settings fields to the custom section.
     *
     * @param array  $settings
     * @param string $current_section
     * @return array
     */
    public static function add_settings_fields($settings, $current_section) {
        if ($current_section === 'hpulr_hide_prices') {
            return [
                [
                    'title' => __('Hide Prices Settings', 'nelegines-hide-prices'),
                    'type'  => 'title',
                    'desc'  => __('Configure rules for hiding prices and disabling Add to Cart.', 'nelegines-hide-prices'),
                    'id'    => 'hpulr_hide_prices_title',
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
                    'desc'     => __('Shown when prices are hidden. Supports {login_url}', 'nelegines-hide-prices'),
                    'id'       => 'hpulr_hidden_price_message',
                    'default'  => 'Login to view price',
                    'type'     => 'text',
                    'desc_tip' => true,
                ],
                [
                    'title'    => __('Enable Test Mode', 'nelegines-hide-prices'),
                    'desc'     => __('Force-hide prices even for logged-in admins (for testing).', 'nelegines-hide-prices'),
                    'id'       => 'hpulr_test_mode',
                    'default'  => 'no',
                    'type'     => 'checkbox',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'hpulr_hide_prices_end',
                ],
            ];
        }

        return $settings;
    }

    /**
     * Render the custom message input on the product edit screen.
     *
     * @param WP_Post $post
     */
    public static function render_product_message_box($post) {
        $value = get_post_meta($post->ID, '_hpulr_custom_message', true);
        wp_nonce_field('hpulr_save_product_message', 'hpulr_nonce');
        ?>
        <textarea name="hpulr_custom_message" rows="4" style="width:100%;"><?php echo esc_textarea($value); ?></textarea>
        <p class="description"><?php esc_html_e('Supports', 'nelegines-hide-prices'); ?> <code>{login_url}</code> <?php esc_html_e('placeholder.', 'nelegines-hide-prices'); ?></p>
        <?php
    }
}
