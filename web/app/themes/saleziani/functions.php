<?php

declare(strict_types=1);

$manifest = json_decode(file_get_contents(__DIR__ . '/assets/public/manifest.json'), true);

add_action('admin_enqueue_scripts', function () use ($manifest): void {
    wp_enqueue_style('editor', home_url() . $manifest['app/themes/saleziani/assets/public/editor.css']);
});

add_action('wp_enqueue_scripts', function () use ($manifest): void {
    wp_enqueue_style('style', home_url() . $manifest['app/themes/saleziani/assets/public/style.css']);
});
