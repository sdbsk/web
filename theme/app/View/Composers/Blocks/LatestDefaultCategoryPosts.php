<?php

declare(strict_types=1);

namespace App\View\Composers\Blocks;

use App\Entity\Post;
use Roots\Acorn\View\Composer;
use WP_Post;

class LatestDefaultCategoryPosts extends Composer
{
    protected static $views = [
        'blocks.latest-default-category-posts',
    ];

    protected function with(): array
    {
        return [
            'posts' => array_map(
                fn(WP_Post $post) => new Post($post),
                get_posts([
                    'category' => get_option('default_category'),
                    'numberposts' => 3,
                ]),
            ),
        ];
    }
}
