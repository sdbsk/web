<?php

declare(strict_types=1);

namespace App;

add_filter('allowed_block_types_all', fn(): array => [
    'core/column',
    'core/columns',
    'core/cover',
    'core/heading',
    'core/image',
    'core/list',
    'core/paragraph',
    'core/separator',
    'theme/latest-default-category-posts',
], 10, 2);
