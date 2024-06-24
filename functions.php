<?php

declare(strict_types=1);

use Roots\WPConfig\Config;
use App\ThemeKernel;
use Symfony\Component\ErrorHandler\Debug;

if (Config::get('WP_DEBUG')) {
    Debug::enable();
}

$template = wp_get_theme()->get_template();
$assets = 'app/themes/' . $template . '/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

add_action('admin_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_script('admin', home_url() . $manifest[$assets . 'admin.js'], [], false, ['in_footer' => true]);
});

add_action('enqueue_block_assets', function () use ($assets, $manifest): void {
    wp_enqueue_style('blocks', home_url() . $manifest[$assets . 'blocks.css']);
    wp_enqueue_script('blocks', home_url() . $manifest[$assets . 'blocks.js'], [
        'wp-blocks',
        'wp-components',
        'wp-data',
        'wp-edit-post',
        'wp-element',
        'wp-hooks',
        'wp-plugins',
        'wp-server-side-render',
    ], false, ['in_footer' => true]);
});

add_action('init', function () use ($template): void {
    // Not required. Just here for easier local development.
    // flush_rewrite_rules();

    if (defined('WP_USE_THEMES') && WP_USE_THEMES && class_exists(ThemeKernel::class)) {
        $GLOBALS['kernel'] = new ThemeKernel(WP_ENV, Config::get('WP_DEBUG'));
        $GLOBALS['kernel']->boot();
        $GLOBALS['kernel']->bootWordpressTheme();

        add_filter('template_include', fn($template) => preg_match('/^\/a\/.*/', $_SERVER['REQUEST_URI'])
            ? __DIR__ . '/app_request.php'
            : $template);
    }

    require_once 'src/legacy/icon-controller.php';

    foreach (require __DIR__ . '/src/legacy/post-types.php' as $type => $args) {
        register_post_type($type, $args);
    }

    foreach (require __DIR__ . '/src/legacy/post-metas.php' as $type => $metas) {
        foreach ($metas as $key => $args) {
            register_post_meta($type, $key, $args);
        }
    }

    register_block_pattern_category($template, [
        'label' => 'Saleziáni',
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

add_action('set_object_terms', function (int $postId, array $termIds, array $termTaxonomyIds, string $taxonomy, bool $append): void {
    if ('category' === $taxonomy && false === $append) {
        $categoryId = get_default_category_id(get_post($postId)->post_author);

        if (false === in_array($categoryId, $termTaxonomyIds, true)) {
            wp_set_post_categories($postId, $categoryId, true);
        }
    }
}, 10, 5);

add_action('wp_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('public', home_url() . $manifest[$assets . 'public.css']);
    wp_enqueue_script('public', home_url() . $manifest[$assets . 'public.js'], [], false, ['in_footer' => true]);
    wp_enqueue_script('consent', home_url() . $manifest[$assets . 'consent.js'], [], false, ['in_footer' => true]);

    wp_deregister_script('wp-polyfill');
    wp_deregister_script('regenerator-runtime');
});

add_filter('allowed_block_types_all', function (): array {
    return [
        'core/query',
        'saleziani/posts',

        // large margin blocks
        'saleziani/newsletter-form',

        'saleziani/project-columns',
        'saleziani/organization-columns',
        'saleziani/icon-columns',
        'core/group',
        'core/buttons',
        'core/embed',
        'saleziani/post-columns',

        // small margin blocks (typograficke)
        'core/image',
        'core/gallery',
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
        'saleziani/darujme-form',
        'saleziani/icon',
        'saleziani/icon-column',
    ];
}, 10, 2);

function wpb_embed_block(): void
{
    wp_enqueue_script(
        'deny-list-blocks',
        get_template_directory_uri() . '/assets/admin.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
    );
}

add_action('enqueue_block_editor_assets', 'wpb_embed_block');

add_filter('excerpt_more', fn(): string => '…');

function placeholder_image_path(int $width, int $height): string
{
    return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}

add_filter('xmlrpc_enabled', '__return_false');

// Disable all xml-rpc endpoints
add_filter('xmlrpc_methods', fn(): array => [], PHP_INT_MAX);

// remove some meta tags from WordPress
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

add_action('after_setup_theme', function () {
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

add_filter('rest_authentication_errors', fn($errors) => is_wp_error($errors) ? $errors : (is_user_logged_in() ?
    $errors :
    new WP_Error('not_found', 'Not found', ['status' => 404])
));

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

add_action('wp_before_admin_bar_render', function (): void {
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    $wp_admin_bar->remove_menu('site-editor');      // Remove the comments link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
});

add_action('admin_init', function (): void {
    global $menu;

    if (is_iterable($menu)) {
        remove_menu_page('edit-comments.php');
        remove_menu_page('plugins.php');
        remove_menu_page('tools.php');
        remove_menu_page('w3tc_dashboard');

        if (false === current_user_can('edit_others_posts')) {
            remove_menu_page('index.php');
        }
    }
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

    if (defined('WP_ENV') && WP_ENV === 'production') {
        echo <<<TRACKING
        <!-- Matomo -->
        <script>
          var _paq = window._paq = window._paq || [];
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          (function() {
            var u='//matomo.saleziani.sk/';
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Matomo Code -->


        <!-- Meta Pixel Code -->
        <script type="text/plain" data-category="targeting">
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '920280066494770');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=920280066494770&ev=PageView&noscript=1"/></noscript>
        <!-- End Meta Pixel Code -->
TRACKING;
    }
});

function cookiesAllowed(string $category): bool
{
    $allowedCategories = json_decode(stripslashes($_COOKIE['cc_cookie'] ?? 'null'), true)['categories'] ?? [];

    return in_array($category, $allowedCategories);
}

add_filter('embed_oembed_html', function ($html) {
    if (str_contains($html, '<iframe') && !cookiesAllowed('targeting')) {
        $thisContentLabel = str_contains($html, 'youtube') || str_contains($html, 'vimeo') ? 'toto video' : 'tento obsah';

        return '<div class="cc-iframe" data-iframe="' . esc_attr($html) . '">
                    <div>
                        Prosím, povoľte marketingové cookies, aby sme vám mohli zobraziť ' . $thisContentLabel . '. <br>
                        <a href="#" onclick="return showBlockedIframes();">Súhlasím s používaním marketingových cookies</a>
                    </div>
                </div>';
    }

    return $html;
});

add_filter('do_redirect_guess_404_permalink', fn() => false);
add_filter('wp_sitemaps_add_provider', fn($provider, $name) => 'users' === $name ? false : $provider, 10, 2);
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
    unset($taxonomies['post_tag']);

    return $taxonomies;
});

require __DIR__ . '/.htaccess.php';

function disable_all_feeds(): void
{
    global $wp_query;
    $wp_query->is_feed = false;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();

    echo 'Not Found';
    exit;
}

add_action('do_feed', 'disable_all_feeds', 1);
add_action('do_feed_rdf', 'disable_all_feeds', 1);
add_action('do_feed_rss', 'disable_all_feeds', 1);
add_action('do_feed_rss2', 'disable_all_feeds', 1);
add_action('do_feed_atom', 'disable_all_feeds', 1);
add_action('do_feed_rss2_comments', 'disable_all_feeds', 1);
add_action('do_feed_atom_comments', 'disable_all_feeds', 1);

add_action('template_redirect', function (): void {
    global $post;

    if (is_author() || is_date() || (is_single() && 'future' === $post->post_status)) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
});

function add_user_custom_settings(WP_User $user): void
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

add_action('show_user_profile', 'add_user_custom_settings');
add_action('edit_user_profile', 'add_user_custom_settings');

function save_user_custom_settings($userId): void
{
    if (current_user_can('administrator') && isset($_POST['default_category'])) {
        update_user_meta($userId, 'default_category', $_POST['default_category']);
    }
}

add_action('personal_options_update', 'save_user_custom_settings');
add_action('edit_user_profile_update', 'save_user_custom_settings');

add_filter('get_terms', function (array $terms, array $taxonomies): array {
    if (is_user_logged_in() && 'category' === $taxonomies[0] && (is_admin() || wp_is_json_request())) {
        $currentUser = wp_get_current_user();

        if (in_array('author', $currentUser->roles, true)) {
            $defaultCategoryId = get_default_category_id($currentUser->ID);

            /** @var WP_Term|int $category */
            foreach ($terms as $index => $category) {
                $categoryId = $category instanceof WP_Term ? $category->term_id : $category;

                if (
                    $defaultCategoryId !== $categoryId &&
                    false === in_array(
                        $defaultCategoryId,
                        get_ancestors($categoryId, 'category'),
                        true)
                ) {
                    unset($terms[$index]);
                }
            }

            $terms = array_values($terms);
        }
    }

    return $terms;
}, 10, 2);

add_action('save_post', function (int $postId): void {
    if (function_exists('w3tc_flush_post')) {
        $ancestors = get_post_ancestors($postId);
        $parentId = empty($ancestors) ? $postId : end($ancestors);

        w3tc_flush_post($parentId);

        foreach (get_pages(['child_of' => $parentId]) as $child) {
            w3tc_flush_post($child->ID);
        }
    }
});

function get_default_category_id(int|string $userId): int
{
    return (int)get_user_meta((int)$userId, 'default_category', true) ?: ((int)get_option('default_category'));
}
