<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class NavigationBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $output = '';
        $page = $this->stack->page();
        $topLevelPageId = $this->stack->topLevelPageId($page);
        $hasNavigation = (bool)get_post_meta($topLevelPageId, 'has_navigation', true);

        $children = get_children([
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'post_parent' => $topLevelPageId,
            'post_type' => 'page',
        ]);

        if ($topLevelPageId === $page->ID && empty($children) || !$hasNavigation) {
            return '';
        }

        $topLevelPageUrl = $this->stack->url($this->stack->page($topLevelPageId));
        $currentUrl = $this->stack->url($page);

        $output .= $currentUrl === $topLevelPageUrl ? '<span class="navigation-item current">Úvod</span>' : '<a href="' . $topLevelPageUrl . '" class="navigation-item">Úvod</a>';

        foreach ($children as $child) {
            $output .= $page->ID === $child->ID || in_array($child->ID, $this->stack->ancestors($page), true) ?
                '<span class="navigation-item current">' . $child->post_title . '</span>' :
                '<a href="' . $this->stack->url($child) . '" class="navigation-item">' . $child->post_title . '</a>';
        }

        return $this->wrapContent($block, $output);
    }
}
