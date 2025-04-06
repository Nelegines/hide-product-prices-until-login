/**
 * Combined script for handling both:
 * - Free plugin: restricted roles
 * - Premium plugin: geo-restricted categories
 */
document.addEventListener('DOMContentLoaded', () => {
    // Localized fallback texts
    const noCategoryDataText = window.hpulr_i18n?.geo_label || 'No categories selected yet.';

    // === FREE: Handle Restricted User Roles ===
    const roleSelect = document.getElementById('hpulr-role-select');
    const roleTable = document.querySelector('#restricted-roles-table tbody');
    const addRoleBtn = document.getElementById('add-role-btn');
    const hiddenRoleInput = document.getElementById('hpulr_restricted_roles');

    if (roleSelect && roleTable && addRoleBtn && hiddenRoleInput) {
        const noDataText = window.hpulr_i18n?.no_data || 'No roles selected yet.';

        addRoleBtn.addEventListener('click', () => {
            const value = roleSelect.value;
            const label = roleSelect.options[roleSelect.selectedIndex]?.text;
            if (!value) return;

            jQuery('#restricted-roles-table tr.no-data-available').remove();

            const row = document.createElement('tr');
            row.setAttribute('data-role', value);
            row.innerHTML = `<td>${label}</td><td class="action"><button type="button" class="button remove-role-btn">Remove</button></td>`;
            roleTable.appendChild(row);
            roleSelect.querySelector(`option[value="${value}"]`)?.remove();

            updateHiddenInput(roleTable, 'data-role', hiddenRoleInput);
            updateEmptyStateRow(roleTable, noDataText);
        });

        roleTable.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-role-btn')) return;
            const row = e.target.closest('tr');
            const value = row.getAttribute('data-role');
            const label = row.querySelector('td')?.textContent;

            if (value && label) {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                roleSelect.appendChild(opt);
            }

            row.remove();
            updateHiddenInput(roleTable, 'data-role', hiddenRoleInput);
            updateEmptyStateRow(roleTable, noDataText);
        });
    }

    // === PREMIUM: Handle Geo-Restricted Categories ===
    const categorySelect = document.getElementById('hpulr-geo-category-select');
    const categoryTable = document.querySelector('#restricted-geo-categories-table tbody');
    const addCategoryBtn = document.getElementById('add-geo-category-btn');
    const hiddenCategoryInput = document.getElementById('hpulr_geo_category_categories');

    if (categorySelect && categoryTable && addCategoryBtn && hiddenCategoryInput) {

        addCategoryBtn.addEventListener('click', () => {
            const value = categorySelect.value;
            const label = categorySelect.options[categorySelect.selectedIndex]?.text;

            console.log('Im clicked..');
            console.log('Selected category:', value);

            if (!value) return;

            jQuery('#restricted-geo-categories-table tr.no-available-data').remove();

            const row = document.createElement('tr');

            row.setAttribute('data-geo-categories', value);
            row.innerHTML = `<td>${label}</td><td class="action"><button type="button" class="button remove-geo-categories-button">Remove</button></td>`;
            categoryTable.appendChild(row);
            categorySelect.querySelector(`option[value="${value}"]`)?.remove();

            updateHiddenInput(categoryTable, 'data-geo-categories', hiddenCategoryInput);
            updateEmptyStateRow(categoryTable, noCategoryDataText);
        });

        categoryTable.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-geo-categories-button')) return;
            const row = e.target.closest('tr');
            const value = row.getAttribute('data-geo-categories');
            const label = row.querySelector('td')?.textContent;

            if (value && label) {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                categorySelect.appendChild(opt);
            }

            row.remove();
            updateHiddenInput(categoryTable, 'data-geo-categories', hiddenCategoryInput);
            updateEmptyStateRow(categoryTable, noCategoryDataText);
        });
    }

    // === Shared Utilities ===

    function updateHiddenInput(table, attr, hiddenInputEl) {
        const values = jQuery(table).find('tr').map(function () {
            return jQuery(this).attr(attr);
        }).get().filter(val => val && val.trim() !== '');

        console.log(table);

        jQuery(hiddenInputEl).val(values.join(',')).trigger('change');
        jQuery('#hpulr_save_trigger').val(Date.now()).trigger('change');

        const $form = jQuery('.wc_settings_form');
        if ($form.length) $form.addClass('submitting').trigger('change');

        jQuery('.woocommerce-save-button').prop('disabled', false);

        if (jQuery('.woocommerce-message').length === 0) {
            jQuery('<div class="updated woocommerce-message inline"><p>Settings have changed, you should save them.</p></div>')
                .insertAfter(jQuery('.wrap h1, .wrap h2').first());
        }
    }

    function updateEmptyStateRow(tableBody, noDataText) {
        const $tbody = jQuery(tableBody);
        $tbody.find('tr.no-available-data, tr.no-data-available').remove();

        const hasRows = $tbody.find('tr').filter(function () {
            return !jQuery(this).hasClass('no-data-available') && !jQuery(this).hasClass('no-available-data');
        }).length > 0;

        if (!hasRows) {
            $tbody.append(`
                <tr class="no-available-data">
                    <td colspan="2" style="text-align: center;">${noDataText}</td>
                </tr>
            `);
        }
    }
});
