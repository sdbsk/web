<?php

declare(strict_types=1);

add_action('edit_form_after_title', function ($post): void {
    if (!($post instanceof WP_Post) || 'newsletter' !== $post->post_type) {
        return;
    }

    $value = (string)get_post_meta($post->ID, 'short_description', true);

    wp_nonce_field('newsletter_short_description', 'newsletter_short_description_nonce');

    echo '<div style="margin-top:20px;">';
    echo '<label for="newsletter_short_description" style="font-weight:600;display:block;margin-bottom:6px;">Stručný popis</label>';
    echo '<input type="text" id="newsletter_short_description" name="newsletter_short_description" value="' . esc_attr($value) . '" style="width:100%;padding:8px;font-size:14px;" placeholder="Zobrazí sa pod názvom newslettera vo formulári">';
    echo '</div>';
});

add_action('save_post_newsletter', function (int $postId): void {
    if (!isset($_POST['newsletter_short_description_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['newsletter_short_description_nonce'])), 'newsletter_short_description')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $postId)) {
        return;
    }

    $value = isset($_POST['newsletter_short_description'])
        ? sanitize_text_field(wp_unslash($_POST['newsletter_short_description']))
        : '';

    if ('' === $value) {
        delete_post_meta($postId, 'short_description');
    } else {
        update_post_meta($postId, 'short_description', $value);
    }
});
