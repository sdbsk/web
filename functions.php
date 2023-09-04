<?php

declare(strict_types=1);

$assets = 'app/themes/saleziani/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

add_action('init', function (): void {
    foreach (scandir(__DIR__ . '/src/blocks') as $filename) {
        if (preg_match('~(.+)\.php~', $filename, $matches)) {
            register_block_type('saleziani/' . $matches[1], [
                'render_callback' => fn(): string => require __DIR__ . '/src/blocks/' . $matches[0],
            ]);
        }
    }

    foreach (require __DIR__ . '/src/post-types.php' as $type => $args) {
        register_post_type($type, $args);
    }
});

add_action('enqueue_block_editor_assets', function () use ($assets, $manifest): void {
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
