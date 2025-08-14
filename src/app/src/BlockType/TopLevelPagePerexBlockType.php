<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class TopLevelPagePerexBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        return $this->wrapContent($block, get_post_meta($this->stack->page(false === (bool)get_post_meta($this->stack->topLevelPageId(), 'has_navigation', true) ? $this->stack->page()->ID : $this->stack->topLevelPageId())->ID, 'page_perex', true), 'p');
    }
}
