<?php

/** @noinspection CommaExpressionJS */

declare(strict_types=1);

function wrap_block_content(WP_Block $block, string $content): string
{
    return '<div class="wp-block-' . str_replace('/', '-', $block->name) . '">' . $content . '</div>';
}

return [
    'navigation' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $output = '';
            $post = get_post();
            $ancestors = get_post_ancestors($post);
            $children = get_children([
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'post_parent' => $post->ID,
                'post_type' => 'page',
            ]);

            if (empty($ancestors) && empty($children)) {
                return '';
            }

            $parentPostID = empty($ancestors) ? $post->ID : end($ancestors);

            if ($parentPostID !== $post->ID) {
                $children = get_children([
                    'order' => 'ASC',
                    'orderby' => 'menu_order',
                    'post_parent' => $parentPostID,
                    'post_type' => 'page',
                ]);
            }

            $parentUrl = get_permalink(get_post($parentPostID));
            $currentUrl = get_permalink($post);
            $homeIcon = '<svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20"><path d="M267.231-219.231h106.616v-236.576h212.306v236.576h106.616v-343.961L480-723.539 267.231-563.256v344.025Zm-47.96 47.96v-415.998L480-783.691l260.729 196.653v415.767H538.193v-236.576H421.807v236.576H219.271ZM480-471.385Z"/></svg>';
            $output .= $currentUrl === $parentUrl ? '<span class="navigation-item current">' . $homeIcon . '</span>' : '<a href="' . $parentUrl . '" class="navigation-item">' . $homeIcon . '</a>';

            foreach ($children as $child) {
                $childUrl = get_permalink($child);
                $output .= $currentUrl === $childUrl ? '<span class="navigation-item current">' . $child->post_title . '</span>' : '<a href="' . $childUrl . '" class="navigation-item">' . $child->post_title . '</a>';
            }

            return wrap_block_content($block, $output);
        },
    ],
    'latest-posts' => [
        'attributes' => [
            'count' => [
                'default' => 3,
                'type' => 'integer',
            ],
            'tag' => [
                'default' => 0,
                'type' => 'integer',
            ],
        ],
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $output = '';
            $posts = get_posts(['numberposts' => $attributes['count'], 'tag_id' => $attributes['tag']]);

            if (count($posts) > 0) {
                $output .= '<div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3">';

                foreach ($posts as $post) {
                    $output .= '<div class="col"><div class="box"><div>';
                    $permalink = get_permalink($post);
                    $thumbnail = get_the_post_thumbnail($post, 'medium_large');

                    if (false === empty($thumbnail)) {
                        $output .= '<a href="' . $permalink . '" class="image">' . $thumbnail . '</a>';
                    }
                    $output .= '<div class="category-list">';
                    foreach (wp_get_post_categories($post->ID) as $categoryId) {
                        $category = get_category($categoryId);
                        if ($category->parent > 0) {
                            $output .= '<a href="' . get_category_link($category) . '" class="category">' . $category->name . '</a>';
                        }
                    }
                    $output .= '</div>';

                    $output .= '<h3 class="title"><a href="' . $permalink . '">' . $post->post_title . '</a></h3>';
                    $output .= '<div class="description">' . get_the_excerpt($post) . '</div></div>';
                    $output .= '<a href="' . $permalink . '" class="link">Čítať viac</a>';
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
            'backgroundColor' => [
                'default' => 'light-brown',
                'type' => 'string',
            ],
        ],
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $template = function (string $thumbnail, string $title, string $permalink, string $excerpt, string $backgroundColor): string {
                $output = '<div class="basic-card has-background has-' . $backgroundColor . '-background-color">';

                if (false === empty($thumbnail)) {
                    $output .= '<a class="image" href="' . $permalink . '">' . $thumbnail . '</a>';
                }

                $output .= '<div class="content"><div class="text"><a href="' . $permalink . '"><h3>' . $title . '</h3></a>';
                $output .= '<p>' . $excerpt . '</p></div>';
                $output .= '<a class="link" href="' . $permalink . '">Dozvedieť sa viac</a></div>';
                $output .= '</div>';

                return $output;
            };

            if (isset($attributes['page']) && $attributes['page'] > 0) {
                $page = get_post($attributes['page']);

                if ($page instanceof WP_Post) {
                    return wrap_block_content($block, $template(
                        get_the_post_thumbnail($page, 'medium_large'),
                        $page->post_title,
                        get_permalink($page),
                        get_the_excerpt($page),
                        $attributes['backgroundColor'],
                    ));
                }
            }

            return wrap_block_content($block, $template(
                '<img src="' . placeholder_image_path(320, 160) . '">',
                'Cieľová stránka nie je nastavená.',
                '#',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco',
                $attributes['backgroundColor'],
            ));
        },
    ],
    'newsletter-form' => [
        'attributes' => [
            'title' => [
                'default' => 'Chcete sledovať, čo máme nové? Pridajte sa do nášho newslettra.',
                'type' => 'string',
            ],
        ],
        'render_callback' => fn(array $attributes, string $content, WP_Block $block): string => wrap_block_content($block, '
                <h3>' . $attributes['title'] . '</h3>
                <form method="post" action="https://sdbsk.ecomailapp.cz/public/subscribe/1/43c2cd496486bcc27217c3e790fb4088">
                    <input type="email" name="email" placeholder="Vaša emailová adresa" required="required">

                    <label class="input-checkbox">
                        <input type="checkbox" name="gdpr" required="required">
                        <span class="label">Súhlasím so spracúvaním osobných údajov</span>
                    </label>

                    <button type="submit" name="submit">Registrovať</button>
                </form>
'),
    ],
];
