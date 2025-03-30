<div class="hpulr-roles-table-wrapper">
    <table class="widefat striped" id="restricted-roles-table">
        <thead>
        <tr>
            <th>
                <?php
                _e('Role', 'hide-product-prices-until-login'); ?>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty(array_filter($selected_roles))):
            foreach ($selected_roles as $role): ?>
                <tr data-role="<?php
                echo esc_attr($role); ?>">
                    <td><?php
                        echo esc_html($all_roles[$role]); ?></td>
                    <td class="action">
                        <button type="button"
                                class="button remove-role-btn"><?php
                            _e('Remove', 'hide-product-prices-until-login'); ?></button>
                    </td>
                </tr>
            <?php
            endforeach;
        else: ?>
            <tr class="no-data-available">
                <td colspan="2" style="text-align: center;"><?php
                    _e('No roles selected', 'hide-product-prices-until-login') ?></td>
            </tr>
        <?php
        endif;
        ?>
        </tbody>
    </table>
</div>

<input type="text" id="hpulr_save_trigger" style="display: none;"/>

<input type="hidden" name="hpulr_restricted_roles" id="hpulr_restricted_roles"
       value="<?php
       echo esc_attr(implode(',', $selected_roles)); ?>"/>