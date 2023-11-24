<?php

/** @noinspection CommaExpressionJS */

declare(strict_types=1);

$stack = new class() {
    private array $ancestors = [];
    private array $pages = [];
    private array $topLevelPages = [];
    private array $urls = [];

    function ancestors(WP_Post $page): array
    {
        if (!isset($this->ancestors[$page->ID])) {
            $this->ancestors[$page->ID] = get_post_ancestors($page);
        }

        return $this->ancestors[$page->ID];
    }

    function page(?int $pageId = null): WP_Post
    {
        if (null === $pageId || !isset($this->pages[$pageId])) {
            global $post;

            $page = null === $pageId ? ($post ?? get_post()) : get_post($pageId);
            $pageId = $page->ID;
            $this->pages[$pageId] = $page;
        }

        return $this->pages[$pageId];
    }

    function topLevelPageId(?WP_Post $page = null): int
    {
        $page = $page ?? $this->page();

        if (!isset($this->topLevelPages[$page->ID])) {
            $ancestors = $this->ancestors($page);
            $this->topLevelPages[$page->ID] = empty($ancestors) ? $page->ID : end($ancestors);
        }

        return $this->topLevelPages[$page->ID];
    }

    function url(WP_Post $page): string
    {
        if (!isset($this->urls[$page->ID])) {
            $this->urls[$page->ID] = get_permalink($page);
        }

        return $this->urls[$page->ID];
    }
};

function wrap_block_content(WP_Block $block, string $content, string $element = 'div'): string
{
    return "<$element class=\"wp-block-" . str_replace('/', '-', $block->name) . '">' . $content . "</$element>";
}

return [
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
        'render_callback' => function (array $attributes, string $content, WP_Block $block) use ($stack): string {
            $output = '';
            $posts = get_posts(['numberposts' => $attributes['count'], 'tag_id' => $attributes['tag']]);

            if (count($posts) > 0) {
                $output .= '<div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3">';

                foreach ($posts as $post) {
                    $output .= '<div class="col"><div class="box"><div>';
                    $permalink = $stack->url($post);
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
        'render_callback' => function (array $attributes, string $content, WP_Block $block) use ($stack): string {
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
                        $stack->url($page),
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
    'navigation' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block) use ($stack): string {
            $output = '';
            $page = $stack->page();
            $topLevelPageId = $stack->topLevelPageId($page);

            $children = get_children([
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'post_parent' => $topLevelPageId,
                'post_type' => 'page',
            ]);

            if ($topLevelPageId === $page->ID && empty($children)) {
                return '';
            }

            $topLevelPageUrl = $stack->url($stack->page($topLevelPageId));
            $currentUrl = $stack->url($page);

            $output .= $currentUrl === $topLevelPageUrl ? '<span class="navigation-item current">Úvod</span>' : '<a href="' . $topLevelPageUrl . '" class="navigation-item">Úvod</a>';

            foreach ($children as $child) {
                $output .= $page->ID === $child->ID || in_array($child->ID, $stack->ancestors($page), true) ?
                    '<span class="navigation-item current">' . $child->post_title . '</span>' :
                    '<a href="' . $stack->url($child) . '" class="navigation-item">' . $child->post_title . '</a>';
            }

            return wrap_block_content($block, $output);
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
    'top-level-page-title' => [
        'render_callback' => fn() => '<h1 class="wp-block-post-title">' . get_the_title($stack->topLevelPageId()) . '</h1>',
    ],
    'top-level-page-perex' => [
        'render_callback' => fn(array $attributes, string $content, WP_Block $block) => wrap_block_content(
            $block,
            get_post_meta($stack->page($stack->topLevelPageId())->ID, 'page_perex', true),
            'p',
        ),
    ],
];
