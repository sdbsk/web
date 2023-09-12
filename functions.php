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
            wp_enqueue_script($matches[1] . '-block', get_template_directory_uri() . $matches[0], ['wp-blocks']);
        }
    }
});

add_action('wp_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('style', home_url() . $manifest[$assets . 'style.css']);
});

//add_filter('allowed_block_types_all', function (): array {
//    return [
//        'core/buttons',
//        'core/group',
//        'core/heading',
//        'core/image',
//        'core/list',
//        'core/navigation-link',
//        'core/paragraph',
//        'core/pullquote',
//        'core/site-logo',
//        'core/template-part',
//        'saleziani/latest-default-category-posts',
//        'saleziani/link-to-page',
//    ];
//}, 10, 2);

function placeholder_image_path(int $width, int $height): string
{
    return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}
