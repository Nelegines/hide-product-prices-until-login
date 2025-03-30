/**
 * Handle dynamic UI for managing restricted user roles in WooCommerce settings.
 * Moves selected roles to a table and syncs the list to a hidden input field.
 */
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('hpulr-role-select');
    const table = document.getElementById('restricted-roles-table')?.querySelector('tbody');
    const hiddenInput = document.getElementById('hpulr_restricted_roles');
    const addButton = document.getElementById('add-role-btn');

    if (!select || !table || !hiddenInput || !addButton) return;

    // Add new role to table
    addButton.addEventListener('click', () => {
        const role = select.value;
        const text = select.options[select.selectedIndex]?.text;

        if (!role) return;

        const row = document.createElement('tr');
        row.setAttribute('data-role', role);
        row.innerHTML = `
            <td>${text}</td>
            <td><button type="button" class="button remove-role-btn">Remove</button></td>
        `;

        table.appendChild(row);
        select.querySelector(`option[value="${role}"]`)?.remove();

        updateHiddenInput();
    });

    // Remove role from table
    table.addEventListener('click', (e) => {
        if (!e.target.classList.contains('remove-role-btn')) return;

        const row = e.target.closest('tr');
        const role = row.getAttribute('data-role');
        const label = row.querySelector('td')?.textContent;

        if (role && label) {
            const opt = document.createElement('option');
            opt.value = role;
            opt.textContent = label;
            select.appendChild(opt);
        }

        row.remove();
        updateHiddenInput();
    });

    /**
     * Update the hidden input and force WooCommerce to recognize the settings as changed.
     */
    function updateHiddenInput() {
        const roles = jQuery('#restricted-roles-table tr').map(function () {
            return jQuery(this).data('role');
        }).get();

        const $hiddenInput = jQuery('#hpulr_restricted_roles');
        $hiddenInput.val(roles.join(',')).trigger('change');

        // ✅ Trigger change on WooCommerce-visible field
        jQuery('#hpulr_save_trigger').val(Date.now()).trigger('change');

        // Trigger WooCommerce form change tracking
        const $form = jQuery('.wc_settings_form');
        if ($form.length) {
            $form.addClass('submitting').trigger('change');
        }

        // Re-enable the Save Changes button
        const $saveButton = jQuery('.woocommerce-save-button');
        if ($saveButton.length) {
            $saveButton.prop('disabled', false);
        }

        // Show WooCommerce "Settings have changed" notice if not already shown
        if (jQuery('.woocommerce-message').length === 0) {
            const $notice = jQuery('<div class="updated woocommerce-message inline"><p>Settings have changed, you should save them.</p></div>');
            const $insertAfter = jQuery('.wrap h1, .wrap h2').first();
            if ($insertAfter.length) {
                $insertAfter.after($notice);
            }
        }
    }
});
