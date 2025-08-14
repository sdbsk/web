<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class TopLevelPageTitleBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        return '<h1 class="wp-block-post-title">' . get_the_title(false === (bool)get_post_meta($this->stack->topLevelPageId(), 'has_navigation', true) ? $this->stack->page()->ID : $this->stack->topLevelPageId()) . '</h1>';
    }
}
