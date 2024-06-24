<?php

namespace App\BlockType;

use WP_Block;
use WP_Term;

class CategoriesBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function render(array $attributes, string $content, WP_Block $block): string
    {
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
    }
}
