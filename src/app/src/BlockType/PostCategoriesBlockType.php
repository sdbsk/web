<?php

namespace App\BlockType;

use WP_Block;
use WP_Term;

class PostCategoriesBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function render(array $attributes, string $content, WP_Block $block): string
    {
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

        return $this->wrapContent($block, implode('<span class="wp-block-post-categories__separator"></span>', $links));
    }
}
