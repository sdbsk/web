<?php

declare(strict_types=1);

namespace App;

use function Roots\bundle;
use function Roots\view;
use const TEMPLATEPATH;

add_action('wp_enqueue_scripts', function (): void {
    bundle('app')->enqueue();
}, 100);

add_action('enqueue_block_editor_assets', function (): void {
    bundle('editor')->enqueue();
}, 100);

add_action('after_setup_theme', function (): void {
    add_theme_support('soil', ['clean-up', 'nav-walker', 'nice-search', 'relative-urls']);
    remove_theme_support('block-templates');
    register_nav_menus(['header' => __('Hlavička', 'sage')]);
    remove_theme_support('core-block-patterns');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form', 'script', 'style']);
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

add_action('widgets_init', function (): void {
    $config = ['after_widget' => '', 'before_widget' => ''];

    register_sidebar(['name' => 'Propagačná lišta', 'id' => 'sidebar-promotion-bar'] + $config);
    register_sidebar(['name' => 'Pätička', 'id' => 'sidebar-footer'] + $config);
});

add_action('init', function (): void {
    foreach (scandir(TEMPLATEPATH . '/resources/views/blocks/') as $filename) {
        preg_match('~([a-zA-Z0-9-]+)\.blade\.php~', $filename, $matches);

        if (isset($matches[1])) {
            register_block_type('theme/' . $matches[1], [
                'render_callback' => fn(array $attributes): string => view(
                    'blocks/' . $matches[1],
                    ['attributes' => $attributes],
                )->render(),
            ]);
        }
    }
});
