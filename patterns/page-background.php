<?php

declare(strict_types=1);

/**
 * Title: Pozadie stránky
 * Slug: saleziani/page-background
 * Categories: saleziani
 * Inserter: false
 */

$color = get_post_meta(url_to_postid($_SERVER['WP_HOME'] . $_SERVER['REQUEST_URI']), 'background_color', true);

if (false === empty($color)) {
    ?>
    <!-- wp:group {<?php echo '"backgroundColor":"' . $color . '",' ?>"className":"wp-pattern-saleziani-page-background"} -->
    <div class="wp-block-group wp-pattern-saleziani-page-background <?php echo 'has-background has-' . $color . '-background-color' ?>"></div>
    <!-- /wp:group -->
    <?php
}
