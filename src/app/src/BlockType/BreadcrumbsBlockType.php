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

        if ($page->post_type === 'post') {
            $categories = get_the_category($page->ID);
            $allBreadcrumbIds = [];

            foreach ($categories as $category) {
                if ($category->name === 'Aktuality') {
                    continue;
                }
                $ancestorsChain = array_reverse(get_ancestors($category->term_id, 'category'));
                $ancestorsChain[] = $category->term_id;
                $allBreadcrumbIds[] = $ancestorsChain;
            }

            $filteredBreadcrumbIds = [];
            foreach ($allBreadcrumbIds as $i => $ancestorsChain) {
                $isSubset = false;
                foreach ($allBreadcrumbIds as $j => $otherChain) {
                    if ($i !== $j && array_slice($otherChain, 0, count($ancestorsChain)) === $ancestorsChain) {
                        $isSubset = true;
                        break;
                    }
                }
                if (!$isSubset) {
                    $filteredBreadcrumbIds[] = $ancestorsChain;
                }
            }

            foreach ($filteredBreadcrumbIds as $breadcrumbId) {
                $output .= '<div class="category-breadcrumbs">';
                $lastIndex = count($breadcrumbId) - 1;
                foreach ($breadcrumbId as $i => $categoryId) {
                    $cat = get_category($categoryId);
                    $url = get_category_link($categoryId);
                    $class = $i === $lastIndex ? 'breadcrumb-item current' : 'breadcrumb-item';
                    $output .= '<a href="' . esc_url($url) . '" class="' . $class . '"><strong>' . esc_html($cat->name) . '</strong></a>';
                    if ($i !== $lastIndex) {
                        $output .= '<span class="breadcrumb-divider">/</span>';
                    }
                }
                $output .= '</div>';
            }
        } else {
            if (
                $topLevelPageId === $page->ID &&
                (empty($children) || !$hasNavigation)
            ) {
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
                    : '<a href="' . esc_url($url) . '" class="breadcrumb-item"><strong>' . esc_html($breadcrumbPage->post_title) . '</strong></a><span class="breadcrumb-divider">/</span>';
            }
        }

        return $this->wrapContent($block, $output);
    }
}
