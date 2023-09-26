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
            $output = '<ul>';
            $post = get_post();
            $ancestors = get_post_ancestors($post);
            $children = get_children([
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'post_parent' => empty($ancestors) ? $post->ID : end($ancestors),
            ]);

            foreach ($children as $child) {
                $output .= '<li><a href="' . get_permalink($child) . '">' . $child->post_title . '</a></li>';
            }

            $output .= '</ul>';

            return wrap_block_content($block, $output);
        },
    ],
    'latest-default-category-posts' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $output = '';
            $posts = get_posts(['category' => (int)get_option('default_category'), 'numberposts' => 3]);

            if (count($posts) > 0) {
                $output .= '<div class="row">';

                foreach ($posts as $post) {
                    $output .= '<div class="col-4"><div class="box"><div>';
                    $permalink = get_permalink($post);
                    $thumbnail = get_the_post_thumbnail($post, 'medium_large');

                    if (false === empty($thumbnail)) {
                        $output .= '<a href="' . $permalink . '" class="image">' . $thumbnail . '</a>';
                    }

                    foreach (wp_get_post_categories($post->ID) as $categoryId) {
                        $category = get_category($categoryId);
                        if ($category->parent > 0) {
                            $output .= '<a href="' . get_category_link($category) . '" class="category">' . $category->name . '</a>';
                        }
                    }

                    $output .= '<a href="' . $permalink . '" class="title"><h3>' . $post->post_title . '</h3></a>';
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
        'render_callback' => fn(array $attributes, string $content, WP_Block $block): string => wrap_block_content($block, '<script>(function(w,d,s,o,f,js,fjs){w["ecm-widget"]=o;w[o]=w[o]||function(){(w[o].q=w[o].q||[]).push(arguments);};js=d.createElement(s),fjs=d.getElementsByTagName(s)[0];js.id="1-43c2cd496486bcc27217c3e790fb4088";js.dataset.a="sdbsk";js.src=f;js.async=1;fjs.parentNode.insertBefore(js,fjs);}(window,document,"script","ecmwidget","https://d70shl7vidtft.cloudfront.net/widget.js"));</script><div id="f-1-43c2cd496486bcc27217c3e790fb4088"></div>'),
    ],
];
