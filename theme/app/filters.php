<?php

declare(strict_types=1);

namespace App;

add_filter('allowed_block_types_all', function (): array {
    $allowedBlocks = [
        'core/button',
        'core/buttons',
        'core/column',
        'core/columns',
        'core/cover',
        'core/heading',
        'core/image',
        'core/list',
        'core/paragraph',
        'core/separator',
    ];

    foreach (get_theme_block_types() as $blockType) {
        $allowedBlocks[] = 'theme/' . $blockType;
    }

    return $allowedBlocks;
}, 10, 2);

add_filter('upload_mimes', function (array $mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});
