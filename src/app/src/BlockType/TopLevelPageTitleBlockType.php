<?php

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class TopLevelPageTitleBlockType extends AbstractBlockType implements BlockTypeInterface {
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        return '<h1 class="wp-block-post-title">' . get_the_title($this->stack->topLevelPageId()) . '</h1>';
    }
}
