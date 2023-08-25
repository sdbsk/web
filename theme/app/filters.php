<?php

declare(strict_types=1);

namespace App;

use const TEMPLATEPATH;

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

    foreach (scandir(TEMPLATEPATH . '/resources/scripts/blocks/') as $filename) {
        preg_match('~([a-zA-Z0-9-]+)\.block\.jsx~', $filename, $matches);

        if (isset($matches[1])) {
            $allowedBlocks[] = 'theme/' . $matches[1];
        }
    }

    return $allowedBlocks;
}, 10, 2);

add_filter('upload_mimes', function (array $mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});
