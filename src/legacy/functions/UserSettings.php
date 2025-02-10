<?php

function addUserSettings(WP_User $user): void
{
    if (current_user_can('administrator')) {
        echo '<h2>Vlastné nastavenia</h2><table class="form-table"><tr><th><label for="default_category">' . translate('Default Post Category') . '</label></th><td>';

        wp_dropdown_categories(
            [
                'hide_empty' => 0,
                'hierarchical' => true,
                'name' => 'default_category',
                'option_none_value' => '',
                'orderby' => 'name',
                'selected' => get_user_meta($user->ID, 'default_category', true),
                'show_option_none' => '-',
            ],
        );

        echo '<p class="description">Ak je nastavené, má vyššiu prioritu ako ' . translate('Settings') . ' -> ' . translate('Writing') . ' -> ' . translate('Default Post Category') . '.</p></td></tr></table>';
    }
}

add_action('show_user_profile', 'addUserSettings');
add_action('edit_user_profile', 'addUserSettings');

function saveUserSettings($userId): void
{
    if (current_user_can('administrator') && isset($_POST['default_category'])) {
        update_user_meta($userId, 'default_category', $_POST['default_category']);
    }
}

add_action('personal_options_update', 'saveUserSettings');
add_action('edit_user_profile_update', 'saveUserSettings');
