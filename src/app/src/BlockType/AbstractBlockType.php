<?php

namespace App\BlockType;

use WP_Block;

abstract class AbstractBlockType implements BlockTypeInterface
{
    public function attributes(): array
    {
        return [];
    }

    protected function wrapContent(WP_Block $block, string $content, string $element = 'div'): string
    {
        return "<$element class=\"wp-block-" . str_replace('/', '-', $block->name) . '">' . $content . "</$element>";
    }
}
