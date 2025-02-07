<?php

use function Env\env;

define('EXCLUDED_SLUGS_FROM_SITEMAP', array_filter(explode(',', env('EXCLUDED_FROM_SITEMAP') ?? ''), fn($item) => !empty($item)));

add_filter('wp_sitemaps_posts_query_args', function ($args) {
    if ('page' === $args['post_type'] && !empty(EXCLUDED_SLUGS_FROM_SITEMAP)) {

        $excludedIds = get_posts(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'post_name__in' => EXCLUDED_SLUGS_FROM_SITEMAP
        ));

        if (!empty($excludedIds)) {
            $args['post__not_in'] = array_merge($args['post__not_in'] ?? [], $excludedIds);
        }
    }

    return $args;
});

add_action('wp_head', function () {
    global $post;

    if (in_array($post?->post_name, EXCLUDED_SLUGS_FROM_SITEMAP, true)) {
        echo '<meta name="robots" content="noindex, nofollow">';
    }
});
