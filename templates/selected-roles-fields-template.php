<tr>
    <th scope="row">
        <?php
        esc_html_e('Restricted Roles (Hide Prices)', 'hide-product-prices-until-login'); ?>
    </th>
    <td class="forminp forminp-text">
        <p style="margin-bottom: 20px;">
            <select id="hpulr-role-select">
                <?php
                foreach ($all_roles as $key => $label): ?>
                    <?php
                    if (!in_array($key, $selected_roles)): ?>
                        <option value="<?php
                        echo esc_attr($key); ?>">
                            <?php
                            echo esc_html($label); ?>
                        </option>
                    <?php
                    endif; ?>
                <?php
                endforeach; ?>
            </select>
            <button type="button" class="button" id="add-role-btn">
                <?php
                _e('Add Role', 'hide-product-prices-until-login'); ?>
            </button>
        </p>
        <p class="description">
            <?php
            esc_html_e('Select roles that should not see prices or Add to Cart.', 'hide-product-prices-until-login'); ?>
        </p>
    </td>
</tr>