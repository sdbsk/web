<?php

declare(strict_types=1);

// todo: na tomto clanku vyladit odstavce a obrazky - obrazky nesmu byt tahane z povodneho webu, musia byt naimportovane
//  https://dobrovolnici.saleziani.sk/2015/09/28/pre-nase-manzelstvo-je-tento-rok-velkym-darom-ktory-s-vdakou-prijimame/

// todo: remove after import: ext-dom, ext-fileinfo, ext-pdo, symfony/html-sanitizer, DOBROVOLNICI_ env variables, this php file and its usage in functions.php
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

add_action('admin_menu', function (): void {
    add_menu_page('Import článkov z dobrovolnici.saleziani.sk', 'Import', 'import', 'dobrovolnici-import', function (): void {
        $first = $_GET['first'] ?? null;
        $last = $_GET['last'] ?? null;

        if (null === $first || null === $last) {
            echo 'Invalid parameters.';

            return;
        }

        set_time_limit(3600);

        try {
            $sourceDb = new PDO('mysql:host=' . getenv('DOBROVOLNICI_DATABASE_HOST') . ';dbname=' . getenv('DOBROVOLNICI_DATABASE_NAME'),
                getenv('DOBROVOLNICI_DATABASE_USERNAME'),
                getenv('DOBROVOLNICI_DATABASE_PASSWORD'),
            );
        } catch (Throwable) {
            echo 'Legacy database connection could not be established.';

            return;
        }

        $sourceAssetsDir = getenv('DOBROVOLNICI_ASSETS_DIR');

        if (false === is_string($sourceAssetsDir) || false === is_dir($sourceAssetsDir)) {
            echo 'Source assets directory not found.';
        }

        $author = get_user_by('email', 'hnatova.misie@saleziani.sk');

        if (false === $author) {
            echo 'No user found by email hnatova.misie@saleziani.sk, create it first.';

            return;
        }

        $categoryMap = [
            3 => get_category_by_slug('angola'),
            4 => get_category_by_slug('azerbajdzan'),
            5 => get_category_by_slug('kena'),
            6 => get_category_by_slug('rusko-sibir'),
            21 => get_category_by_slug('ukrajina'),
            24 => get_category_by_slug('slovensko-lunik-ix'),
            27 => get_category_by_slug('mexiko'),
            36 => get_category_by_slug('ekvador'),
            38 => get_category_by_slug('tanzania'),
            44 => get_category_by_slug('slovensko-savio-o-z'),
            45 => get_category_by_slug('slovensko-orechov-dvor'),
            48 => get_category_by_slug('juzny-sudan'),
        ];

        if (in_array(false, $categoryMap, true)) {
            echo 'Could not map categories.';

            return;
        } else {
            $categoryMap = array_map(fn(WP_Term $category): int => $category->term_id, $categoryMap);
        }

        global $wpdb;

        foreach ($wpdb->get_results($wpdb->prepare('SELECT id FROM wp_posts WHERE post_author = %s AND post_type = %s', [$author->ID, 'attachment']), ARRAY_A) as $attachment) {
            $metadata = get_post_meta($attachment['id'], '_wp_attachment_metadata', true);
            $filename = wp_upload_dir()['path'] . '/../../' . $metadata['file'];

            unlink($filename);

            if (isset($metadata['original_image'])) {
                unlink(dirname($filename) . '/' . $metadata['original_image']);
            }

            foreach ($metadata['sizes'] as $size) {
                unlink(dirname($filename) . '/' . $size['file']);
            }
        }

        $wpdb->query($wpdb->prepare('DELETE wp_postmeta FROM wp_posts INNER JOIN wp_postmeta ON wp_postmeta.post_id = wp_posts.ID WHERE wp_posts.post_author = %s', [$author->ID]));
        $wpdb->query($wpdb->prepare('DELETE FROM wp_posts WHERE post_author = %s', [$author->ID]));

        $importImage = function (string $source) use ($wpdb, $author, $sourceAssetsDir): ?int {
            $source = $sourceAssetsDir . $source;

            if (is_file($source)) {
                $dateTime = (new DateTimeImmutable())->setTimestamp(filemtime($source));
                $baseName = basename($source);
                $filename = wp_upload_dir($dateTime->format('Y/m'))['path'] . '/' . $baseName;

                if (is_file($filename)) {
                    $existing = $wpdb->get_results($wpdb->prepare('SELECT post_id FROM wp_postmeta WHERE meta_key = \'%s\' AND meta_value = \'%s\'', [
                        '_wp_attached_file',
                        ltrim(str_replace(wp_upload_dir('', false), '', $filename), '/'),
                    ]), ARRAY_A);

                    if (isset($existing[0]['post_id'])) {
                        return (int)$existing[0]['post_id'];
                    }
                }

                copy($source, $filename);

                $attachmentId = wp_insert_attachment([
                    'post_author' => $author->ID,
                    'post_date' => $dateTime->format('Y-m-d H:i:s'),
                    'post_mime_type' => (new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($source)),
                    'post_title' => $baseName,
                ], $filename);

                wp_update_attachment_metadata($attachmentId, @wp_generate_attachment_metadata($attachmentId, $filename));

                return $attachmentId;
            }

            return null;
        };

        $manualReplacements = [
            '*' => [
                ['&nbsp;', ' '],
                [' ', ' '],
            ],
            124 => [
                ['◆◆◆', ''],
            ],
        ];

        $htmlSanitizer = new HtmlSanitizer((new HtmlSanitizerConfig())
//            ->allowElement('iframe', ['allowfullscreen', 'frameborder', 'height', 'src', 'width'])
//            ->allowRelativeLinks()
//            ->allowRelativeMedias()
            ->allowSafeElements()
//            ->blockElement('div')
//            ->blockElement('span')
            ->dropAttribute('align', ['img', 'p'])
            ->dropAttribute('alt', ['img'])
            ->dropAttribute('class', ['img'])
            ->dropAttribute('height', ['img'])
            ->dropAttribute('width', ['img'])
            ->dropElement('br'),
        );

        $attachments = [];

        foreach ($sourceDb->query('SELECT ID, meta_value FROM wp_posts INNER JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id AND wp_postmeta.meta_key = \'_wp_attached_file\' WHERE wp_posts.post_type = \'attachment\'')->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $attachments[$row['ID']] = $row['meta_value'];
        }

        $sourcePosts = $sourceDb->query('
            SELECT
                wp_posts.ID AS id,
                wp_posts.post_date AS date,
                wp_posts.post_content AS content,
                wp_posts.post_title AS title,
                wp_posts.guid AS guid,
                wp_users.display_name AS author,
                GROUP_CONCAT(wp_term_relationships.term_taxonomy_id) AS categories
            FROM
                wp_posts
            INNER JOIN
                wp_users ON wp_users.ID = wp_posts.post_author
            INNER JOIN
                wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID AND wp_term_relationships.term_taxonomy_id IN (3, 4, 5, 6, 21, 24, 27, 36, 38, 44, 45, 48)
            WHERE
                post_type=\'post\' AND
                post_status=\'publish\'
            GROUP BY
                wp_posts.ID,
                wp_posts.post_date
            ORDER BY
                post_date
        ')->fetchAll(PDO::FETCH_ASSOC);

        foreach (array_slice($sourcePosts, $first - 1, $last - $first + 1) as $sourcePost) {
            echo $sourcePost['date'] . ' ' . $sourcePost['author'] . ' ' . $sourcePost['title'] . ' <a href="' . $sourcePost['guid'] . '" target="_blank">' . $sourcePost['guid'] . '</a><br>';

            $content = $sourcePost['content'];
            echo '<textarea cols="160" rows="8">' . $content . '</textarea><br>';

            foreach ($manualReplacements['*'] as $replacement) {
                $content = str_replace($replacement[0], $replacement[1], $content);
            }

            foreach ($manualReplacements[$sourcePost['id']] ?? [] as $replacement) {
                $content = str_replace($replacement[0], $replacement[1], $content);
            }

            // Sanitize.
            $content = $htmlSanitizer->sanitize($content);

            // Decode entities.
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

            // Collapse multiple spaces.
            $content = preg_replace('/ +/', ' ', $content);

            // Remove <a> wrapper around <img>
            $content = preg_replace_callback('~<a[0-9a-zA-Z\s=":/.\-_]+>(<img[0-9a-zA-Z\s=":/.\-_]+/>)</a>~', fn($matches): string => $matches[1], $content);

            // Images from <img src>
            $content = preg_replace_callback('~<img[\s0-9a-zA-Z="\-:/._]+src="([0-9a-zA-Z:/.\-_]+)"[\s0-9a-zA-Z="\-:/._]*>~', function ($matches) use ($importImage): string {
                $imageId = $importImage(strtr($matches[1], ['http://dobrovolnici.saleziani.sk/wp-content/uploads/' => '', 'https://dobrovolnici.saleziani.sk/wp-content/uploads/' => '']));

                if (null === $imageId) {
                    return '';
                }

                return '<!-- wp:image {"id":' . $imageId . ',"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="' . wp_get_attachment_url($imageId) . '" alt="" class="wp-image-' . $imageId . '"/></figure><!-- /wp:image -->';
            }, $content);

            // Images from [gallery]
            $content = preg_replace_callback('~\[gallery.+ids="([0-9,]+)".*]~', function ($matches) use ($importImage, $attachments): string {
                $output = '<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-default is-cropped">';

                foreach (explode(',', $matches[1]) as $sourceImageId) {
                    if (isset($attachments[$sourceImageId])) {
                        $imageId = $importImage($attachments[$sourceImageId]);

                        if (null !== $imageId) {
                            $output .= '<!-- wp:image {"id":' . $imageId . ',"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . wp_get_attachment_url($imageId) . '" alt="" class="wp-image-' . $imageId . '"/></figure><!-- /wp:image -->';
                        }
                    }
                }

                $output .= '</figure><!-- /wp:gallery -->';

                return $output;
            }, $content);

            // Paragraphs from line feeds.
            $content = preg_replace_callback("~(.*)[\r\n|\r|\n](.*)~", function ($matches): string {
                $inner = trim(empty($matches[2]) ? $matches[1] : $matches[1] . ' ' . $matches[2]);

                if (empty($inner)) {
                    return '';
                }

                if (str_starts_with($inner, '<!--') || str_starts_with($inner, '<figure class="wp-block-image')) {
                    return $inner;
                }

                if (str_starts_with($inner, '<p>')) {
                    $endOfParagraph = mb_strrpos($inner, '</p>');

                    return '<!-- wp:paragraph -->' . trim(mb_substr($inner, 0, $endOfParagraph + 4)) . '<!-- /wp:paragraph -->' . trim(mb_substr($inner, $endOfParagraph + 4));
                }

                return '<!-- wp:paragraph --><p>' . $inner . '</p><!-- /wp:paragraph -->';
            }, $content);

            // Add line feeds and trim.
            $content = trim(strtr($content, [
                '<!-- /wp:image -->' => PHP_EOL . '<!-- /wp:image -->' . PHP_EOL . PHP_EOL,
                '<!-- /wp:paragraph -->' => PHP_EOL . '<!-- /wp:paragraph -->' . PHP_EOL . PHP_EOL,
                '<!-- wp:paragraph -->' => '<!-- wp:paragraph -->' . PHP_EOL,
                '<figure class="wp-block-image size-full">' => PHP_EOL . '<figure class="wp-block-image size-full">',
                '<figure class="wp-block-gallery has-nested-images columns-default is-cropped">' => PHP_EOL . '<figure class="wp-block-gallery has-nested-images columns-default is-cropped">' . PHP_EOL . PHP_EOL,
                '<figure class="wp-block-image size-large">' => PHP_EOL . '<figure class="wp-block-image size-large">',
                '</figure><!-- /wp:gallery -->' => '</figure>' . PHP_EOL . '<!-- /wp:gallery -->',
            ]));

            echo '<textarea cols="160" rows="8" readonly>' . $content . '</textarea><br>';

            $post = get_post(wp_insert_post([
                'post_author' => $author->ID,
                'post_category' => isset($categoryMap[$sourcePost['categories']]) ? [$categoryMap[$sourcePost['categories']]] : [get_default_category_id($author->ID)],
                'post_content' => $content,
                'post_date' => $sourcePost['date'],
                'post_status' => 'publish',
                'post_title' => $sourcePost['title'],
            ]));

            update_post_meta($post->ID, 'domicil', $sourcePost['author']);

            flush();
            ob_flush();
        }
    }, 'dashicons-database-import');
});
