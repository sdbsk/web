<?php

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class ActivityBlockType extends AbstractBlockType implements BlockTypeInterface {
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $post = get_post();
        return get_the_title() . get_the_excerpt() . get_post()->post_title . print_r(get_posst_meta($post), true);
    }
}
