<?php

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class DomicilBlockType extends AbstractBlockType implements BlockTypeInterface {
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $domicil = get_post_meta($this->stack->page()->ID, 'domicil', true);

        return empty($domicil) ? '' : $this->wrapContent($block, $domicil);
    }
}
