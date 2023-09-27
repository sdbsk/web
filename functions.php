<?php

declare(strict_types=1);

$template = wp_get_theme()->get_template();
$assets = 'app/themes/' . $template . '/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

add_action('init', function () use ($template): void {
    register_block_pattern_category($template, [
        'label' => 'Saleziáni',
    ]);

    foreach (require __DIR__ . '/src/block-types.php' as $type => $args) {
        register_block_type($template . '/' . $type, $args);
    }

    foreach (require __DIR__ . '/src/post-types.php' as $type => $args) {
        register_post_type($type, $args);
    }

    foreach (require __DIR__ . '/src/post-metas.php' as $type => $metas) {
        foreach ($metas as $key => $args) {
            register_post_meta($type, $key, $args);
        }
    }

    unregister_block_pattern('core/query-standard-posts');
    unregister_block_pattern('core/query-medium-posts');
    unregister_block_pattern('core/query-small-posts');
    unregister_block_pattern('core/query-grid-posts');
    unregister_block_pattern('core/query-large-title-posts');
    unregister_block_pattern('core/query-offset-posts');
    unregister_block_pattern('core/social-links-shared-background-color');
    unregister_block_pattern_category('featured');
    unregister_block_pattern_category('text');
});

add_action('enqueue_block_assets', function () use ($assets, $manifest): void {
    wp_enqueue_style('editor', home_url() . $manifest[$assets . 'editor.css']);

    foreach ($manifest as $filename) {
        if (preg_match('~/assets/blocks/([a-z\-]+)\..+~', $filename, $matches)) {
            wp_enqueue_script($matches[1] . '-block', get_template_directory_uri() . $matches[0], ['wp-blocks', 'wp-components', 'wp-element', 'wp-server-side-render'], false, ['in_footer' => true]);
        }

        if (preg_match('~/assets/metaboxes/([a-z\-]+)\..+~', $filename, $matches)) {
            wp_enqueue_script($matches[1] . '-metabox', get_template_directory_uri() . $matches[0], ['wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins'], false, ['in_footer' => true]);
        }
    }
});

add_action('wp_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('style', home_url() . $manifest[$assets . 'style.css']);
});

add_filter('allowed_block_types_all', function (): array {
    return [
        'core/button',
        'core/buttons',
        'core/group',
        'core/heading',
        'core/image',
        'core/list',
        'core/list-item',
        'core/navigation-link',
        'core/paragraph',
        'core/pullquote',
        'core/separator',
        'core/site-logo',
        'core/spacer',
        'core/template-part',
        'saleziani/latest-posts',
        'saleziani/link-to-page',
        'saleziani/navigation',
        'saleziani/newsletter-form',
    ];
}, 10, 2);

function placeholder_image_path(int $width, int $height): string
{
    return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}
