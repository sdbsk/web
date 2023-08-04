<?php

declare(strict_types=1);

namespace App;

use function Roots\bundle;

add_action('wp_enqueue_scripts', function (): void {
    bundle('app')->enqueue();
}, 100);

add_action('enqueue_block_editor_assets', function (): void {
    bundle('editor')->enqueue();
}, 100);

add_action('after_setup_theme', function (): void {
    add_theme_support('soil', ['clean-up', 'nav-walker', 'nice-search', 'relative-urls']);
    remove_theme_support('block-templates');
    register_nav_menus(['primary_navigation' => __('Primary Navigation', 'sage')]);
    remove_theme_support('core-block-patterns');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form', 'script', 'style']);
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

