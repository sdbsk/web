<?php

namespace App\BlockType;

use WP_Block;

/**
 * https://developer.wordpress.org/reference/functions/register_block_type/
 */
interface BlockTypeInterface
{
    public function attributes(): array;
    public function render(array $attributes, string $content, WP_Block $block): string;
}
