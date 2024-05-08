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
    'categories' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $currentCategory = get_queried_object();

            if ($currentCategory instanceof WP_Term) {
                $parentCategoryId = $currentCategory->parent ?: $currentCategory->term_id;

                $content .= '<li class="cat-item cat-item-' . $parentCategoryId . ($currentCategory->term_id === $parentCategoryId ? ' current-cat' : '') . '"><a href="' . get_term_link($parentCategoryId) . '">Všetko</a></li>';

                /** @var WP_Term $category */
                foreach (get_categories(['parent' => $parentCategoryId]) as $category) {
                    $content .= '<li class="cat-item cat-item-' . $category->term_id . ($currentCategory->term_id === $category->term_id ? ' current-cat' : '') . '"><a href="' . get_term_link($category) . '">' . $category->name . '</a></li>';
                }

                return wrap_block_content($block, $content, 'ul');
            }

            return $content;
        },
    ],
    'domicil' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block) use ($stack): string {
            $domicil = get_post_meta($stack->page()->ID, 'domicil', true);

            return empty($domicil) ? '' : wrap_block_content($block, $domicil);
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
            'source' => [
                'default' => 'saleziani-sk',
                'type' => 'string',
            ],
        ],
        'render_callback' => fn(array $attributes, string $content, WP_Block $block): string => wrap_block_content($block, '
                <h3>' . $attributes['title'] . '</h3>
                <form method="post" action="https://sdbsk.ecomailapp.cz/public/subscribe/1/43c2cd496486bcc27217c3e790fb4088?source=' . preg_replace('~[^a-zA-Z0-9\-]~', '', $attributes['source']) . '">
                    <input type="email" name="email" placeholder="Vaša emailová adresa" required="required">
                    <label class="input-checkbox">
                        <input type="checkbox" name="gdpr" required="required">
                        <span class="label">Súhlasím so spracúvaním osobných údajov</span>
                    </label>
                    <button type="submit" name="submit">Registrovať</button>
                </form>
        '),
    ],
    'post-categories' => [
        'render_callback' => function (array $attributes, string $content, WP_Block $block): string {
            $categoryIds = wp_get_post_categories(get_the_ID(), ['exclude' => array_map(fn(WP_Term $t): int => $t->term_id, get_categories(['parent' => 0]))]);

            if (empty($categoryIds)) {
                return '';
            }

            $links = [];

            foreach ($categoryIds as $categoryId) {
                $category = get_category($categoryId);

                if ($category instanceof WP_Term) {
                    $links[] = '<a href="' . get_category_link($category) . '" rel="tag">' . $category->name . '</a>';
                }
            }

            return wrap_block_content($block, implode('<span class="wp-block-post-categories__separator"></span>', $links));
        },
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
