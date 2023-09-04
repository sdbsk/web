<?php

declare(strict_types=1);

return [
    'latest-default-category-posts' => [
        'render_callback' => function (): string {
            $output = '';
            $posts = get_posts(['category' => (int)get_option('default_category'), 'numberposts' => 3]);

            if (count($posts) > 0) {
                $output .= '<div class="row">';

                foreach ($posts as $post) {
                    $output .= '<div class="col-4">';
                    $permalink = get_permalink($post);
                    $thumbnail = get_the_post_thumbnail($post, 'medium_large');

                    if (false === empty($thumbnail)) {
                        $output .= '<a href="' . $permalink . '" style="display:block;">' . $thumbnail . '</a>';
                    }

                    foreach (wp_get_post_categories($post->ID) as $categoryId) {
                        $category = get_category($categoryId);

                        if ($category->parent > 0) {
                            $output .= '<a href="' . get_category_link($category) . '">' . $category->name . '</a>';
                            $output .= '<a href="' . $permalink . '"><h2>' . $post->post_title . '</h2></a>';
                            $output .= '<div>' . get_the_excerpt($post) . '</div>';
                            $output .= '<a href="' . $permalink . '">Čítať viac</a>';
                        }
                    }

                    $output .= '</div>';
                }

                $output .= '</div>';
            }

            return $output;
        },
    ],
];
