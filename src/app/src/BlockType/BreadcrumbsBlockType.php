<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class BreadcrumbsBlockType extends AbstractBlockType implements BlockTypeInterface
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

        if ($topLevelPageId === $page->ID && empty($children) || !$hasNavigation) {
            return '';
        }

        $ancestors = array_reverse($this->stack->ancestors($page));
        $breadcrumbIds = array_merge($ancestors, [$page->ID]);

        foreach ($breadcrumbIds as $id) {
            $breadcrumbPage = $this->stack->page($id);
            $url = $this->stack->url($breadcrumbPage);
            $isCurrent = $id === $page->ID;

            $output .= $isCurrent
                ? '<span class="breadcrumb-item current">' . esc_html($breadcrumbPage->post_title) . '</span>'
                : '<a href="' . esc_url($url) . '" class="breadcrumb-item">' . esc_html($breadcrumbPage->post_title) . '</a>';
        }

        return $this->wrapContent($block, $output);
    }
}
