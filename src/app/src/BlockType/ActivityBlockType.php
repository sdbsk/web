<?php

declare(strict_types=1);

namespace App\BlockType;

use App\Service\Stack;
use WP_Block;

class ActivityBlockType extends AbstractBlockType implements BlockTypeInterface
{
    public function __construct(private readonly Stack $stack)
    {
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $post = get_post();
        $venue = get_post_meta($this->stack->page()->ID, 'venue', true);
        $buttonLabel = get_post_meta($this->stack->page()->ID, 'buttonLabel', true);

//        $content = get_the_title() . get_the_excerpt() . get_post()->post_title . print_r(get_post_meta($post), true);

        $content = '<div class="venue">' . $venue . '</div><div class="button-label">' . $buttonLabel;

        return $this->wrapContent($block, $content);
    }
}
