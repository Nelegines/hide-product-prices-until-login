<textarea name="hpulr_custom_message" rows="4"
          style="width:100%;"><?php
    echo esc_textarea($value); ?></textarea>
<p class="description"><?php
    esc_html_e('Supports', 'hide-product-prices-until-login'); ?>
    <code>{login_url}</code> <?php
    esc_html_e('placeholder.', 'hide-product-prices-until-login'); ?></p>