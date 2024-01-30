<?php

declare(strict_types=1);

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

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

// todo: remove after import: ext-dom, ext-fileinfo, ext-pdo, symfony/html-sanitizer, LEGACY_ env variables and following hook
add_action('admin_menu', function (): void {
    add_menu_page('Import článkov!', 'Importovať články!', 'import', 'post-import', function (): void {
        set_time_limit(3600);
//        ini_set('memory_limit', '1024M');

        try {
            $legacyDb = new PDO('mysql:host=' . getenv('LEGACY_DATABASE_HOST') . ';dbname=' . getenv('LEGACY_DATABASE_NAME'),
                getenv('LEGACY_DATABASE_USERNAME'),
                getenv('LEGACY_DATABASE_PASSWORD'),
            );
        } catch (Throwable) {
            echo 'Legacy database connection could not be established.';

            return;
        }

        $legacyAssetsDir = getenv('LEGACY_ASSETS_DIR');

        if (false === is_string($legacyAssetsDir) || false === is_dir($legacyAssetsDir)) {
            echo 'Legacy assets directory not found.';
        }

        $author = get_user_by('email', 'gondova@saleziani.sk');

        if (false === $author) {
            echo 'No user found by email gondova@saleziani.sk, create it first.';

            return;
        }

        $categoryAliases = [];

        foreach ($legacyDb->query('SELECT id, alias FROM os9ad_categories WHERE id IN (8, 16)')->fetchAll(PDO::FETCH_ASSOC) as $category) {
            $categoryAliases[$category['id']] = $category['alias'];
        }

        $wpHome = getenv('WP_HOME');

        $dom = new DOMDocument();
        $elementAttributes = [];
        $assets = [];

        $mediaUrl = function (string $filename) use ($wpHome): string {
            return $wpHome . '/app/uploads/' . (new DateTimeImmutable())->setTimestamp(filemtime($filename))->format('Y/m/') . basename($filename);
        };

        $parser = function (DOMNode $node) use (
            &$elementAttributes,
            &$assets,
            &$parser,
            $mediaUrl,
            $legacyAssetsDir,
            $wpHome,
        ): array {
            $autoReplacements = [];

            if (false === in_array($node->nodeName, ['#document', '#text', 'body', 'html'], true)) {
                if (false === isset($elementAttributes[$node->nodeName])) {
                    $elementAttributes[$node->nodeName] = [];
                }

                if ($node->attributes instanceof DOMNamedNodeMap) {
                    foreach ($node->attributes as $attribute) {
                        if (false === in_array($attribute->nodeName, $elementAttributes[$node->nodeName], true)) {
                            $elementAttributes[$node->nodeName][] = $attribute->nodeName;
                        }

                        if ('a' === $node->nodeName) {
                            $href = strtr($node->attributes->getNamedItem('href')->nodeValue, [
                                '%20' => ' ',
                            ]);

                            if (str_starts_with($href, 'images/')) {
                                $assets[] = $href;
                                $filename = $legacyAssetsDir . $href;

                                $autoReplacements[] = [
                                    $href,
                                    is_file($filename) ? $mediaUrl($filename) : 'media-does-not-exists.jpg',
                                ];
                            }
                        }

                        if ('source' === $node->nodeName || 'img' === $node->nodeName) {
                            $src = strtr($node->attributes->getNamedItem('src')->nodeValue, [
                                'Ã¡' => 'á',
                                'Ã³' => 'ó',
                                'Ã½' => 'ý',
                                'Ä' => 'č',
                                'Ä¾' => 'ľ',
                                'Å' => 'ň',
                            ]);

                            if (str_starts_with($src, 'images/')) {
                                $assets[] = $src;
                                $filename = $legacyAssetsDir . $src;

                                $autoReplacements[] = [
                                    $src,
                                    is_file($filename) ? $mediaUrl($filename) : 'media-does-not-exists.jpg',
                                ];
                            }
                        }
                    }
                }
            }

            foreach ($node->childNodes as $child) {
                $autoReplacements = array_merge($autoReplacements, $parser($child));
            }

            return $autoReplacements;
        };

        $lf = "\r\n";

        /** @noinspection HttpUrlsUsage */
        /** @noinspection HtmlDeprecatedAttribute */
        $manualReplacements = [
            '*' => [
                ['http://saleziani.sk', 'https://saleziani.sk'],
                ['http://www.saleziani.sk', 'https://saleziani.sk'],
                ['http://www.youtube.com', 'https://www.youtube.com'],
                ['https://saleziani.sk/images', 'images'],
                ['saleziani.sk/index.php', 'saleziani.sk'],
            ],
            239 => [
                ['<div align="center"><img src="http://www.saleziani.sk/images/sdb/spravyOBR/2013/01/2013_01_04_ans_strenna_540_378.jpg" alt="2013_01_04_ans_strenna_540_378.jpg" align="default" height="378" width="540" /></div>', ''],
            ],
            325 => [
                ['<div><img src="http://www.saleziani.sk/images/120913_misie2.jpg" align="default" height="405" width="540" alt="120913_misie2.jpg" /></div>' . $lf . '<div>Zľava: don Marián Ondriáš, don Jerald David, don Václav Klement – hlavný radca pre misie, Peter Červeň</div>' . $lf . '<div>&nbsp;</div>' . $lf, ''],
            ],
            508 => [
                ['<a href="http://www.sme.sk/c/6757696/voskovy-svatec-don-bosco-pride-v-urne-na-kontrolu-na-slovensko.html"><a href="http://www.sme.sk/c/6757696/voskovy-svatec-don-bosco-pride-v-urne-na-kontrolu-na-slovensko.html">http://www.sme.sk/c/6757696/voskovy-svatec-don-bosco-pride-v-urne-na-kontrolu-na-slovensko.html </a></a>', '<a href="http://www.sme.sk/c/6757696/voskovy-svatec-don-bosco-pride-v-urne-na-kontrolu-na-slovensko.html">http://www.sme.sk/c/6757696/voskovy-svatec-don-bosco-pride-v-urne-na-kontrolu-na-slovensko.html</a>'],
            ],
            643 => [
                ['<a href="http://www.bazilika.sk/">www.bazilika.sk</a><a href="http://www.bazilika.sk/"> </a>', '<a href="http://www.bazilika.sk/">www.bazilika.sk</a>'],
                ['<a href="http://agape.bazilika.sk/"><a href="http://www.agape.bazilika.sk">www.agape.bazilika.sk</a></a>', '<a href="http://www.agape.bazilika.sk">www.agape.bazilika.sk</a>'],
            ],
            1464 => [
                ['<p style="text-align: center;"><img src="images/sdb/spravyOBR/2015/03/2015_03_7návykov3.jpg" alt="" width="710" height="438" /></p>' . $lf, ''],
            ],
            1830 => [
                ['2016/07/2016_06_02_denD', '2016/06/2016_06_02_denD'],
            ],
            1909 => [
                ['images/doc/narodnaput.pdf', 'images/narodnaput.pdf'],
            ],
        ];

        $htmlSanitizer = new HtmlSanitizer((new HtmlSanitizerConfig())
            ->allowElement('iframe', ['allowfullscreen', 'frameborder', 'height', 'src', 'width'])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowSafeElements()
            ->blockElement('div')
            ->blockElement('span')
            ->dropAttribute('align', ['img', 'p'])
            ->dropElement('br'),
        );

        $redirects = [];

        foreach ($legacyDb->query('SELECT id, catid, title, alias, introtext, publish_up, modified FROM os9ad_content WHERE catid IN (8, 16) AND state = 1 AND publish_up > \'2023-10-01 00:00:00\' AND publish_down < \'1970-01-01 00:00:00\' ORDER BY publish_up')->fetchAll(PDO::FETCH_ASSOC) as $content) {
            echo $content['id'] . ', ' . $content['publish_up'] . '<br>';

            $postContent = $content['introtext'];

            if (isset($manualReplacements[$content['id']])) {
                foreach ($manualReplacements[$content['id']] as $replacement) {
                    $postContent = str_replace($replacement[0], $replacement[1], $postContent);
                }
            }

            foreach ($manualReplacements['*'] as $replacement) {
                $postContent = str_replace($replacement[0], $replacement[1], $postContent);
            }

            $postContent = $htmlSanitizer->sanitize($postContent);

            @$dom->loadHTML($postContent);
            $autoReplacements = $parser($dom);

            foreach ($autoReplacements as $replacement) {
                $postContent = str_replace($replacement[0], $replacement[1], $postContent);
            }

            $existingPost = get_posts([
                'date_query' => [
                    'after' => substr($content['publish_up'], 0, 10) . ' 00:00:00',
                    'before' => substr($content['publish_up'], 0, 10) . ' 23:59:59',
                ],
                'title' => $content['title'],
            ])[0] ?? null;

            $post = get_post(
                $existingPost instanceof WP_Post ?
                    wp_update_post([
                        'ID' => $existingPost->ID,
                        'comment_status' => 'closed',
                        'ping_status' => 'closed',
                        'post_author' => $author->ID,
                        'post_content' => $postContent,
                        'post_date' => $content['publish_up'],
                        'post_status' => 'publish',
                        'post_title' => $content['title'],
                    ]) :
                    wp_insert_post([
                        'comment_status' => 'closed',
                        'ping_status' => 'closed',
                        'post_author' => $author->ID,
                        'post_content' => $postContent,
                        'post_date' => $content['publish_up'],
                        'post_status' => 'publish',
                        'post_title' => $content['title'],
                    ]),
            );

            $redirects['/spravy/' . $content['catid'] . '-' . $categoryAliases[$content['catid']] . '/' . $content['id'] . '-' . $content['alias']] = str_replace($wpHome, '', get_permalink($post));

            flush();
            ob_flush();
        }

        foreach (array_unique($assets) as $asset) {
            $source = $legacyAssetsDir . $asset;

            echo $source . ' ';

            if (is_file($source)) {
                $dateTime = (new DateTimeImmutable())->setTimestamp(filemtime($source));
                $baseName = basename($source);
                $filename = wp_upload_dir($dateTime->format('Y/m'))['path'] . '/' . $baseName;

                if (is_file($filename)) {
                    echo ' --> ' . $filename . ' [SKIPPED]<br>';
                } else {
                    copy($source, $filename);

                    $attachmentId = wp_insert_attachment([
                        'post_author' => $author->ID,
                        'post_date' => $dateTime->format('Y-m-d H:i:s'),
                        'post_mime_type' => (new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($source)),
                        'post_title' => $baseName,
                    ], $filename);

                    wp_update_attachment_metadata($attachmentId, @wp_generate_attachment_metadata($attachmentId, $filename));
                    echo ' --> ' . $filename . ' [OK]<br>';
                }
            } else {
                echo '[NOT_FOUND]<br>';
            }

            flush();
            ob_flush();
        }

        echo '<br><strong>Use <a href="https://httpd.apache.org/docs/2.4/rewrite/rewritemap.html">RewriteMap</a> to following redirects</strong><br><textarea cols="190" rows="32" readonly="readonly">';

        foreach ($redirects as $from => $to) {
            echo $from . ' ' . $to . PHP_EOL;
        }

        echo '</textarea>';

        dump($elementAttributes);
    }, 'dashicons-database-import');
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

function wpb_embed_block(): void
{
    wp_enqueue_script(
        'deny-list-blocks',
        get_template_directory_uri() . '/assets/public.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
    );
}

add_action('enqueue_block_editor_assets', 'wpb_embed_block');

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
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the Wordpress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about Wordpress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the Wordpress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the Wordpress documentation link
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
});
