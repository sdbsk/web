<?php

declare(strict_types=1);

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

$template = wp_get_theme()->get_template();
$assets = 'app/themes/' . $template . '/assets/';
$manifest = json_decode(file_get_contents(__DIR__ . '/web/' . $assets . 'manifest.json'), true);

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
            $filename = wp_upload_dir()['path'] . '/../../' . $metadata['file'];

            unlink($filename);

            foreach ($metadata['sizes'] as $size) {
                unlink(dirname($filename) . '/' . $size['file']);
            }

            delete_post_meta($attachment['id'], '_wp_attached_file');
            delete_post_meta($attachment['id'], '_wp_attachment_metadata');
        }

        $wpdb->query($wpdb->prepare('DELETE FROM wp_posts WHERE post_author = %s', [$author->ID]));

        $categoryAliases = [];

        foreach ($legacyDb->query('SELECT id, alias FROM os9ad_categories WHERE id IN (8, 16)')->fetchAll(PDO::FETCH_ASSOC) as $category) {
            $categoryAliases[$category['id']] = $category['alias'];
        }

        $wpHome = getenv('WP_HOME');

        $dom = new DOMDocument();
        $elementAttributes = [];
        $imgSrcValues = [];

        $imageUrl = function (string $filename) use ($wpHome): string {
            return $wpHome . '/app/uploads/' . (new DateTimeImmutable())->setTimestamp(filemtime($filename))->format('Y/m/') . basename($filename);
        };

        $parser = function (DOMNode $node) use (
            &$elementAttributes,
            &$imgSrcValues,
            &$parser,
            $imageUrl,
            $legacyAssetsDir,
            $wpHome,
        ): array {
            $imgReplacements = [];

            if (false === in_array($node->nodeName, ['#document', '#text', 'body', 'html'], true)) {
                if (false === isset($elementAttributes[$node->nodeName])) {
                    $elementAttributes[$node->nodeName] = [];
                }

                if ($node->attributes instanceof DOMNamedNodeMap) {
                    foreach ($node->attributes as $attribute) {
                        if (false === in_array($attribute->nodeName, $elementAttributes[$node->nodeName], true)) {
                            $elementAttributes[$node->nodeName][] = $attribute->nodeName;
                        }

                        if ('img' === $node->nodeName) {
                            $imgSrc = $node->attributes->getNamedItem('src')->nodeValue;

                            if (str_starts_with($imgSrc, 'images/')) {
                                $imgSrcValues[] = $imgSrc;
                                $filename = $legacyAssetsDir . $imgSrc;

                                $imgReplacements[] = [
                                    $imgSrc,
                                    is_file($filename) ? $imageUrl($filename) : 'image-does-not-exists.jpg',
                                ];
                            }
                        }
                    }
                }
            }

            foreach ($node->childNodes as $child) {
                $imgReplacements = array_merge($imgReplacements, $parser($child));
            }

            return $imgReplacements;
        };

        $lf = "\r\n";

        /** @noinspection HttpUrlsUsage */
        /** @noinspection HtmlDeprecatedAttribute */
        $replacements = [
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
            761 => [
                ['2013_07_31_trnavka6ň.jpg', '2013_07_31_trnavka6.jpg'],
            ],
            799 => [
                ['images/mp3/Festa_hymna2007.mp3', 'app/uploads/2013/09/festa-hymna-2007.mp3'],
                ['images/mp3/Festa_hymna2007.ogg', 'app/uploads/2013/09/festa-hymna-2007.ogg'],
            ],
            1464 => [
                ['<p style="text-align: center;"><img src="images/sdb/spravyOBR/2015/03/2015_03_7návykov3.jpg" alt="" width="710" height="438" /></p>' . $lf, ''],
            ],
            1830 => [
                ['2016/07/2016_06_02_denD', '2016/06/2016_06_02_denD'],
            ],
            2227 => [
                ['2017_09_21_výstupTZ.jpg', '2017_09_21_vystupTZ.jpg'],
            ],
            2693 => [
                ['2019_03_05_40_4_U__kópia.jpg', '2019_03_05_40_4_U.jpg'],
            ],
            2708 => [
                ['2019_03_21_ans_Mozambik2ľľľ.jpg', '2019_03_21_ans_Mozambik22.jpg'],
            ],
            2902 => [
                ['2019_10_22_Mariansky_vystup_na_Choč', '2019_10_22_Mariansky_vystup_na_Choc'],
            ],
            3459 => [
                ['2021_07_23_tábor', '2021_07_23_tabor'],
            ],
            3534 => [
                ['2021_09_17_trojročie', '2021_09_17_trojrocie'],
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

            if (isset($replacements[$content['id']])) {
                foreach ($replacements[$content['id']] as $replacement) {
                    $postContent = str_replace($replacement[0], $replacement[1], $postContent);
                }
            }

            foreach ($replacements['*'] as $replacement) {
                $postContent = str_replace($replacement[0], $replacement[1], $postContent);
            }

            $postContent = $htmlSanitizer->sanitize($postContent);

            @$dom->loadHTML($postContent);
            $imgReplacements = $parser($dom);

            foreach ($imgReplacements as $replacement) {
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

        foreach (array_unique($imgSrcValues) as $imgSrcValue) {
            $source = $legacyAssetsDir . $imgSrcValue;

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

        copy($legacyAssetsDir . 'images/mp3/Festa_hymna2007.mp3', wp_upload_dir('2013/09')['path'] . '/festa-hymna-2007.mp3');
        copy($legacyAssetsDir . 'images/mp3/Festa_hymna2007.ogg', wp_upload_dir('2013/09')['path'] . '/festa-hymna-2007.ogg');

        wp_insert_attachment([
            'post_author' => $author->ID,
            'post_date' => '2013-09-11 12:18:09',
            'post_mime_type' => 'audio/mpeg',
            'post_title' => 'Festa hymna 2007 mp3',
        ], wp_upload_dir('2013/09')['path'] . '/festa-hymna-2007.mp3');

        wp_insert_attachment([
            'post_author' => $author->ID,
            'post_date' => '2013-09-11 12:18:09',
            'post_mime_type' => 'audio/ogg',
            'post_title' => 'Festa hymna 2007 ogg',
        ], wp_upload_dir('2013/09')['path'] . '/festa-hymna-2007.ogg');

        echo '<br><strong>Use <a href="https://httpd.apache.org/docs/2.4/rewrite/rewritemap.html">RewriteMap</a> to following redirects</strong><br><textarea cols="190" rows="32" readonly="readonly">';

        foreach ($redirects as $from => $to) {
            echo $from . ' ' . $to . PHP_EOL;
        }

        echo '</textarea>';

        dump($elementAttributes);
    }, 'dashicons-database-import');
});

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

        if (preg_match('~/assets/filters/([a-z\-]+)\..+~', $filename, $matches)) {
            wp_enqueue_script($matches[1] . '-plugin', get_template_directory_uri() . $matches[0], ['wp-hooks'], false, ['in_footer' => true]);
        }

        if (preg_match('~/assets/plugins/([a-z\-]+)\..+~', $filename, $matches)) {
            wp_enqueue_script($matches[1] . '-plugin', get_template_directory_uri() . $matches[0], ['wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins'], false, ['in_footer' => true]);
        }
    }
});

add_action('save_post_post', function (int $postId): void {
    wp_set_post_categories($postId, (int)get_option('default_category'), true);
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

add_filter('term_links-category', fn(array $links): array => array_values(array_filter($links, fn(string $link): bool => false === str_contains($link, '>Aktuality<'))));

add_filter('wp_list_categories', fn(string $output): string => str_replace('>Aktuality<', '>Všetko<', $output));

function placeholder_image_path(int $width, int $height): string
{
    return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}
