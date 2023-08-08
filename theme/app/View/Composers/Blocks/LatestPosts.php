<?php

declare(strict_types=1);

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class LatestPosts extends Composer
{
    protected static $views = [
        'blocks.latest-posts',
    ];

    protected function with(): array
    {
        return [
            'posts' => get_posts([
                'category' => get_option('default_category'),
                'numberposts' => 3,
            ]),
        ];
    }
}
