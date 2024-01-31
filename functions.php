<?php

declare(strict_types=1);

$template = wp_get_theme()->get_template();
$assets = 'app/themes/' . $template . '/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

add_action('admin_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_script('admin', home_url() . $manifest[$assets . 'admin.js'], [], false, ['in_footer' => true]);
});

add_action('enqueue_block_assets', function () use ($assets, $manifest): void {
    wp_enqueue_style('blocks', home_url() . $manifest[$assets . 'blocks.css']);
    wp_enqueue_script('blocks', home_url() . $manifest[$assets . 'blocks.js'], ['wp-blocks', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-hooks', 'wp-plugins', 'wp-server-side-render'], false, ['in_footer' => true]);
});

add_action('init', function () use ($template): void {

    require_once 'src/icon-controller.php';

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

    register_block_pattern_category($template, [
        'label' => 'Saleziáni',
    ]);

    register_post_meta('page', 'page_perex', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ]);
});

add_action('after_setup_theme', function () {
    remove_theme_support('core-block-patterns');
    add_filter('should_load_remote_block_patterns', '__return_false');
});

add_filter('block_categories_all', function ($categories) {
    $categories[] = [
        'slug' => 'meta',
        'title' => 'Meta',
    ];

    return $categories;
});

add_action('save_post_post', function (int $postId): void {
    wp_set_post_categories($postId, (int)get_option('default_category'), true);
});

add_action('wp_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('public', home_url() . $manifest[$assets . 'public.css']);
    wp_enqueue_script('public', home_url() . $manifest[$assets . 'public.js'], [], false, ['in_footer' => true]);
//    wp_deregister_script('wp-interactivity');
});

add_filter('allowed_block_types_all', function (): array {
    return [
        'core/query',
        'saleziani/posts',

        // large margin blocks
        'saleziani/newsletter-form',
//        'saleziani/navigation',

        'saleziani/project-columns',
        'saleziani/organization-columns',
        'saleziani/icon-columns',
        'core/group',
        'core/buttons',
        'core/embed',
        'saleziani/page-perex-meta',
        'saleziani/post-columns',

        // small margin blocks (typograficke)
        'core/image',
        'core/heading',
        'core/paragraph',
        'core/list',
        'core/button',
        'core/pullquote',
        'core/column',
        'core/columns',

        // no margin blocks
        'core/site-logo',
        'core/template-part',
        'core/navigation-link',
        'core/site-logo',
        'core/list-item',
        'core/social-link',
        'core/social-links',
        'saleziani/project-column',
        'saleziani/organization-column',
        'saleziani/newsletter-form',
        'saleziani/icon',
        'saleziani/icon-column',
    ];
}, 10, 2);

add_filter('excerpt_more', fn(): string => '…');

add_filter('term_links-category', fn(array $links): array => array_values(array_filter($links, fn(string $link): bool => false === str_contains($link, '>Aktuality<'))));

add_filter('wp_list_categories', fn(string $output): string => str_replace('>Aktuality<', '>Všetko<', $output));

function placeholder_image_path(int $width, int $height): string
{
    return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}

add_filter('xmlrpc_enabled', '__return_false');

// Disable all xml-rpc endpoints
add_filter('xmlrpc_methods', function () {
    return [];
}, PHP_INT_MAX);

// remove some meta tags from WordPress
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

add_action('after_setup_theme', function () {

//    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
//    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

    // Remove the REST API lines from the HTML Header
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery.
    add_filter('embed_oembed_discover', '__return_false');

    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result');

    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Filters for WP-API version 1.x
    add_filter('json_enabled', '__return_false');
    add_filter('json_jsonp_enabled', '__return_false');

    // Filters for WP-API version 2.x
    add_filter('rest_jsonp_enabled', '__return_false');

    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
});

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action('wp_head', 'rel_canonical');

add_action('wp_dashboard_setup', function () {
    remove_action('welcome_panel', 'wp_welcome_panel');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('health_check_status', 'dashboard', 'normal');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
});

add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
//    $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
    $wp_admin_bar->remove_menu('site-editor');         // Remove the comments link
    $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link

});

add_action('admin_init', function () {
    global $menu;

    if (is_iterable($menu)) {
        remove_menu_page('edit-comments.php');
        remove_menu_page('plugins.php');
        remove_menu_page('w3tc_dashboard');
    }

    require __DIR__ . '/.htaccess.php';
});

add_action('wp_head', function (): void {
    $fallbackImage = get_template_directory_uri() . '/assets/images/fb-share.jpg';

    if (is_category()) {
        $category = get_queried_object();

        $tags = [
            'title' => $category->name,
            'description' => $category->description,
            'image' => $fallbackImage,
            'url' => get_category_link($category),
        ];
    } else {
        global $post;

        if ($post instanceof WP_Post) {
            $thumbnailImage = get_the_post_thumbnail_url($post->ID, 'large');

            $tags = [
                'title' => get_the_title(),
                'description' => get_the_excerpt(),
                'image' => empty($thumbnailImage) ? $fallbackImage : $thumbnailImage,
                'url' => get_permalink(),
            ];

            if (is_single()) {
                $tags['type'] = 'article';
            }
        } else {
            $tags = [];
        }
    }

    foreach ($tags as $name => $value) {
        if (!empty($value)) {
            echo '<meta property="og:' . $name . '" content="' . esc_attr($value) . '" />';
        }
    }
});
