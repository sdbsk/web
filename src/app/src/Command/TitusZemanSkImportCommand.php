<?php

declare(strict_types=1);

namespace App\Command;

use DateTimeImmutable;
use finfo;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Throwable;
use WP_Term;
use function wp_generate_attachment_metadata;
use const ARRAY_A;
use const ENT_QUOTES;
use const FILEINFO_MIME_TYPE;
use const PHP_EOL;

require_once ABSPATH . 'wp-admin/includes/image.php';

// todo: remove from composer.json
//  "ext-fileinfo": "*",
//  "ext-pdo": "*",
//  "symfony/html-sanitizer": "^7.2"

class TitusZemanSkImportCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:titus-zeman-sk:import');
    }

    protected function configure(): void
    {
        $this->addOption('cleanup-only');
        $this->addOption('first', null, InputOption::VALUE_REQUIRED, '', 0);
        $this->addOption('last', null, InputOption::VALUE_REQUIRED, '', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cleanupOnly = $input->getOption('cleanup-only');

        try {
            $sourceDb = new PDO('mysql:host=' . getenv('TITUSZEMAN_DATABASE_HOST') . ';dbname=' . getenv('TITUSZEMAN_DATABASE_NAME'),
                getenv('TITUSZEMAN_DATABASE_USERNAME'),
                getenv('TITUSZEMAN_DATABASE_PASSWORD'),
            );
        } catch (Throwable) {
            $output->writeln('Legacy database connection could not be established.');

            return parent::FAILURE;
        }

        $sourceAssetsDir = getenv('TITUSZEMAN_ASSETS_DIR');

        if (false === is_string($sourceAssetsDir) || false === is_dir($sourceAssetsDir)) {
            $output->writeln('Source assets directory not found.');

            return parent::FAILURE;
        }

        $author = get_user_by('email', 'rastohamracek@sdb.sk');

        if (false === $author) {
            $output->writeln('No user found by email rastohamracek@sdb.sk, create it first.');

            return parent::FAILURE;
        }

        $category = get_category_by_slug('osobnosti');

        if (false === $category instanceof WP_Term) {
            $output->writeln('No category found by slug osobnosti, create it first.');

            return parent::FAILURE;
        }

        $tag = get_term_by('slug', 'titus-zeman', 'post_tag');

        if (false === $tag instanceof WP_Term) {
            $output->writeln('No tag found by slug titus-zeman, create it first.');

            return parent::FAILURE;
        }

        global $wpdb;

        $first = (int)$input->getOption('first');
        $last = (int)$input->getOption('last');

        if ($cleanupOnly || (0 === $first && 0 === $last)) {
            $output->writeln('Cleanup ...');

            foreach ($wpdb->get_results($wpdb->prepare('SELECT id FROM wp_posts WHERE post_author = %s AND post_type = %s', [$author->ID, 'attachment']), ARRAY_A) as $attachment) {
                $metadata = get_post_meta($attachment['id'], '_wp_attachment_metadata', true);

                if (isset($metadata['file'])) {
                    $filename = wp_upload_dir()['path'] . '/../../' . $metadata['file'];

                    if (is_file($filename)) {
                        unlink($filename);
                    }

                    if (isset($metadata['original_image'])) {
                        $originalImageFilename = dirname($filename) . '/' . $metadata['original_image'];

                        if (is_file($originalImageFilename)) {
                            unlink($originalImageFilename);
                        }
                    }

                    foreach ($metadata['sizes'] as $size) {
                        $sizeFilename = dirname($filename) . '/' . $size['file'];

                        if (is_file($sizeFilename)) {
                            unlink($sizeFilename);
                        }
                    }
                }
            }

            $wpdb->query($wpdb->prepare('DELETE wp_postmeta FROM wp_posts INNER JOIN wp_postmeta ON wp_postmeta.post_id = wp_posts.ID WHERE wp_posts.post_author = %s', [$author->ID]));
            $wpdb->query($wpdb->prepare('DELETE FROM wp_posts WHERE post_author = %s', [$author->ID]));
        }

        if ($cleanupOnly) {
            return parent::SUCCESS;
        }

        $importAttachment = function (string $source) use ($wpdb, $author, $sourceAssetsDir): array {
            $source = $sourceAssetsDir . $source;

            if (is_file($source)) {
                $dateTime = (new DateTimeImmutable())->setTimestamp(filemtime($source));
                $baseName = basename($source);
                $filename = wp_upload_dir($dateTime->format('Y/m'))['path'] . '/' . $baseName;
                $path = ltrim(str_replace(wp_upload_dir('', false), '', $filename), '/');
                $url = home_url() . '/app/uploads/' . $path;

                if (is_file($filename)) {
                    $existing = $wpdb->get_results($wpdb->prepare('SELECT post_id FROM wp_postmeta WHERE meta_key = \'%s\' AND meta_value = \'%s\'', [
                        '_wp_attached_file',
                        $path,
                    ]), ARRAY_A);

                    if (isset($existing[0]['post_id'])) {
                        return [(int)$existing[0]['post_id'], $url];
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

                return [$attachmentId, $url];
            }

            return [null, null];
        };

        $htmlSanitizer = new HtmlSanitizer((new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->dropAttribute('align', ['img', 'p'])
            ->dropAttribute('alt', ['img'])
            ->dropAttribute('class', ['img'])
            ->dropAttribute('height', ['img'])
            ->dropAttribute('width', ['img'])
            ->dropElement('br'),
        );

        $attachments = [];

        foreach ($sourceDb->query('SELECT ID, meta_value FROM blwp_posts INNER JOIN blwp_postmeta ON blwp_posts.ID = blwp_postmeta.post_id AND blwp_postmeta.meta_key = \'_wp_attached_file\' WHERE blwp_posts.post_type = \'attachment\'')->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $attachments[$row['ID']] = $row['meta_value'];
        }

        $sourcePosts = $sourceDb->query('
            SELECT
                blwp_posts.ID AS id,
                blwp_posts.post_date AS date,
                blwp_posts.post_content AS content,
                blwp_posts.post_title AS title,
                blwp_posts.guid AS guid,
                blwp_users.display_name AS author,
                GROUP_CONCAT(blwp_term_relationships.term_taxonomy_id) AS categories,
                blwp_postmeta.meta_value AS thumbnail
            FROM
                blwp_posts
            INNER JOIN
                blwp_users ON blwp_users.ID = blwp_posts.post_author
            INNER JOIN
                blwp_term_relationships ON blwp_term_relationships.object_id = blwp_posts.ID AND blwp_term_relationships.term_taxonomy_id IN (89, 134)
            LEFT JOIN
                blwp_postmeta ON blwp_postmeta.post_id = blwp_posts.ID AND blwp_postmeta.meta_key = \'_thumbnail_id\'
            WHERE
                post_type=\'post\' AND
                post_status=\'publish\'
            GROUP BY
                blwp_posts.ID,
                blwp_posts.post_date
            ORDER BY
                post_date
        ')->fetchAll(PDO::FETCH_ASSOC);

        if ($first > 0 && $last > 0) {
            $sourcePosts = array_slice($sourcePosts, $first - 1, $last - $first + 1);
        }

        $messages = [];
        $progress = new ProgressBar($output, count($sourcePosts));
        $progress->start();

        foreach ($sourcePosts as $sourcePost) {
            $content = $sourcePost['content'];

            // Sanitize.
            $content = $htmlSanitizer->sanitize($content);

            // Decode entities.
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

            // Collapse multiple spaces.
            $content = preg_replace('/ +/', ' ', $content);

            // Remove <a> wrapper around <img>
            $content = preg_replace_callback('~<a[0-9a-zA-Zá-žÁ-Ž\s=":/.\-_]+>(<img[0-9a-zA-Zá-žÁ-Ž\s=":/.\-_]+/>)</a>~', fn($matches): string => $matches[1], $content);

            // Images from <img src>
            $content = preg_replace_callback('~<img[\s0-9a-zA-Zá-žÁ-Ž="\-:/._]+src="([0-9a-zA-Zá-žÁ-Ž:/.\-–_]+)"[\s0-9a-zA-Zá-žÁ-Ž="\-:/._]*>~', function ($matches) use ($importAttachment): string {
                [$imageId, $imageUrl] = $importAttachment(strtr($matches[1], ['http://tituszeman.sk/page/wp-content/uploads/' => '', 'https://tituszeman.sk/page/wp-content/uploads/' => '']));

                if (null === $imageId) {
                    return '';
                }

                return '<!-- wp:image {"id":' . $imageId . ',"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="' . $imageUrl . '" alt="" class="wp-image-' . $imageId . '"/></figure><!-- /wp:image -->';
            }, $content);

            // Images from [gallery]
            $content = preg_replace_callback('~\[gallery.+ids="([0-9,]+)".*]~', function ($matches) use ($importAttachment, $attachments): string {
                $output = '<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-default is-cropped">';

                foreach (explode(',', $matches[1]) as $sourceImageId) {
                    if (isset($attachments[$sourceImageId])) {
                        [$imageId, $imageUrl] = $importAttachment($attachments[$sourceImageId]);

                        if (null !== $imageId) {
                            $output .= '<!-- wp:image {"id":' . $imageId . ',"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . $imageUrl . '" alt="" class="wp-image-' . $imageId . '"/></figure><!-- /wp:image -->';
                        }
                    }
                }

                $output .= '</figure><!-- /wp:gallery -->';

                return $output;
            }, $content);

            // Files from <a href>
            $content = preg_replace_callback('~<a[\s0-9a-zA-Zá-žÁ-Ž="\-:/._]+href="([0-9a-zA-Zá-žÁ-Ž:/.\-–_]+)"[\s0-9a-zA-Zá-žÁ-Ž="\-:/._]*>~', function ($matches) use ($importAttachment): string {
                if (str_contains($matches[1], 'tituszeman.sk')) {
                    [$fileId, $fileUrl] = $importAttachment(strtr($matches[1], ['http://tituszeman.sk/page/wp-content/uploads/' => '', 'https://tituszeman.sk/page/wp-content/uploads/' => '']));

                    if (null === $fileId) {
                        return '';
                    }

                    return '<a href="' . $fileUrl . '"/>';
                } else {
                    return '<a href="' . $matches[1] . '"/>';
                }
            }, $content);

            // Paragraphs from line feeds.
            $content = preg_replace_callback("~(.*)[\r|\n](.*)~", function ($matches): string {
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

            $postId = wp_insert_post([
                'post_author' => $author->ID,
                'post_category' => [$category->term_id],
                'post_content' => $content,
                'post_date' => $sourcePost['date'],
                'post_status' => 'publish',
                'post_title' => $sourcePost['title'],
                'tags_input' => [$tag->term_id],
                '_thumbnail_id' => isset($sourcePost['thumbnail']) ? $importAttachment($attachments[$sourcePost['thumbnail']]) : null,
            ]);

            $messages[] = $sourcePost['guid'] . PHP_EOL . get_post_permalink($postId) . PHP_EOL;
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');

        $output->writeln($messages);

        return parent::SUCCESS;
    }
}
