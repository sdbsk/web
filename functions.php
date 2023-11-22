<?php

declare(strict_types=1);

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

$template = wp_get_theme()->get_template();
$assets = 'app/themes/' . $template . '/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

add_action('admin_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('admin', home_url() . $manifest[$assets . 'admin.css']);
    wp_enqueue_script('admin', home_url() . $manifest[$assets . 'admin.js'], ['wp-blocks', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-hooks', 'wp-plugins', 'wp-server-side-render'], false, ['in_footer' => true]);
});

// todo: remove after import: ext-dom, ext-fileinfo, ext-pdo, symfony/html-sanitizer, LEGACY_ env variables and following hook
add_action('admin_menu', function (): void {
    add_menu_page('Import článkov', 'Importovať články', 'import', 'post-import', function (): void {
        set_time_limit(3600);

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

        global $wpdb;

        foreach ($wpdb->get_results($wpdb->prepare('SELECT id FROM wp_posts WHERE post_author = %s AND post_type = %s', [$author->ID, 'attachment']), ARRAY_A) as $attachment) {
            $metadata = get_post_meta($attachment['id'], '_wp_attachment_metadata', true);
            if (isset($metadata['file'])) {
                $filename = wp_upload_dir()['path'] . '/../../' . $metadata['file'];

                unlink($filename);

                foreach ($metadata['sizes'] as $size) {
                    unlink(dirname($filename) . '/' . $size['file']);
                }

                delete_post_meta($attachment['id'], '_wp_attached_file');
                delete_post_meta($attachment['id'], '_wp_attachment_metadata');
            }
        }

        $wpdb->query($wpdb->prepare('DELETE FROM wp_posts WHERE post_author = %s', [$author->ID]));

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
            2693 => [
                ['2019_03_05_40_4_U__kópia.jpg', '2019_03_05_40_4_U.jpg'],
            ],
            2708 => [
                ['2019_03_21_ans_Mozambik2ľľľ.jpg', '2019_03_21_ans_Mozambik22.jpg'],
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

        foreach ($legacyDb->query('SELECT id, catid, title, alias, introtext, publish_up, modified FROM os9ad_content WHERE catid IN (8, 16) AND state = 1 AND publish_down < \'1970-01-01 00:00:00\' ORDER BY publish_up')->fetchAll(PDO::FETCH_ASSOC) as $content) {
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

            $post = get_post(wp_insert_post([
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_author' => $author->ID,
                'post_content' => $postContent,
                'post_date' => $content['publish_up'],
                'post_status' => 'publish',
                'post_title' => $content['title'],
            ]));

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

                copy($source, $filename);

                $attachmentId = wp_insert_attachment([
                    'post_author' => $author->ID,
                    'post_date' => $dateTime->format('Y-m-d H:i:s'),
                    'post_mime_type' => (new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($source)),
                    'post_title' => $baseName,
                ], $filename);

                wp_update_attachment_metadata($attachmentId, @wp_generate_attachment_metadata($attachmentId, $filename));
                echo ' --> ' . $filename . ' [OK]<br>';
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

add_action('save_post_post', function (int $postId): void {
    wp_set_post_categories($postId, (int)get_option('default_category'), true);
});

add_action('wp_enqueue_scripts', function () use ($assets, $manifest): void {
    wp_enqueue_style('public', home_url() . $manifest[$assets . 'public.css']);
    wp_enqueue_script('public', home_url() . $manifest[$assets . 'public.js'], [], false, ['in_footer' => true]);

    wp_deregister_script('wp-interactivity');
});

add_filter('allowed_block_types_all', function (): array {
    return [
        // large margin blocks
        'saleziani/newsletter-form',
        'saleziani/latest-posts',
        'saleziani/navigation',

        'saleziani/project-columns',
        'saleziani/organization-columns',
        'saleziani/icon-columns',
        'saleziani/page-columns',
        'core/group',
        'core/buttons',

        // asi na vyhodenie z whitelistu
        'core/separator',
        'core/spacer',

        // small margin blocks (typograficke)
        'core/image',
        'core/heading',
        'core/paragraph',
        'core/list',
        'core/button',
        'core/pullquote',

        // no margin blocks
        'core/template-part',
        'core/navigation-link',
        'saleziani/link-to-page',
        'core/site-logo',
        'core/list-item',
        'saleziani/project-column',
        'saleziani/organization-column',
        'saleziani/icon',
        'saleziani/icon-column',
        'saleziani/page-column',
    ];
}, 10, 2);

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
    add_filter('rest_enabled', '__return_false');
    add_filter('rest_jsonp_enabled', '__return_false');

    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
});

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action('wp_head', 'rel_canonical');
