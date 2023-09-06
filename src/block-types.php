<?php

declare(strict_types=1);

function wrap_block_content(WP_Block $block, string $content): string
{
    return '<div class="wp-block-' . str_replace('/', '-', $block->name) . '">' . $content . '</div>';
}

return [
    'latest-default-category-posts' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $output = '';
            $posts = get_posts(['category' => (int)get_option('default_category'), 'numberposts' => 3]);

            if (count($posts) > 0) {
                $output .= '<div class="row">';

                foreach ($posts as $post) {
                    $output .= '<div class="col-4"><div class="latest-post-box">';
                    $permalink = get_permalink($post);
                    $thumbnail = get_the_post_thumbnail($post, 'medium_large');

                    if (false === empty($thumbnail)) {
                        $output .= '<a href="' . $permalink . '" style="display:block;" class="box-image">' . $thumbnail . '</a>';
                    }

                    foreach (wp_get_post_categories($post->ID) as $categoryId) {
                        $category = get_category($categoryId);
                        if ($category->parent > 0) {
                            $output .= '<a href="' . get_category_link($category) . '">' . $category->name . '</a>';
                        }
                    }

                    $output .= '<a href="' . $permalink . '"><h3>' . $post->post_title . '</h3></a>';
                    $output .= '<div>' . get_the_excerpt($post) . '</div>';
                    $output .= '<a href="' . $permalink . '">Dozvedieť sa viac</a>';
                    $output .= '</div></div>';
                }

                $output .= '</div>';
            }

            return wrap_block_content($block, $output);
        },
    ],
    'link-to-page' => [
        'attributes' => [
            'page' => [
                'default' => 0,
                'type' => 'integer',
            ],
        ],
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $output = '';

            if (isset($attributes['page']) && $attributes['page'] > 0) {
                $page = get_post($attributes['page']);

                if ($page instanceof WP_Post) {
                    $permalink = get_permalink($page);
                    $thumbnail = get_the_post_thumbnail($page, 'medium_large');

                    if (false === empty($thumbnail)) {
                        $output .= '<a class="image" href="' . $permalink . '" style="display:block;">' . $thumbnail . '</a>';
                    }

                    $output .= '<div class="content"><div class="text"><a href="' . $permalink . '"><h3>' . $page->post_title . '</h3></a>';
                    $output .= '<p>' . get_the_excerpt($page) . '</p></div>';
                    $output .= '<a class="link" href="' . $permalink . '">Dozvedieť sa viac</a></div>';
                }

                return wrap_block_content($block, $output);
            }

            return wrap_block_content($block, 'Cieľová stránka nie je nastavená.');
        },
    ],
];
