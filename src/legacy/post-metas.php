<?php

declare(strict_types=1);

return [
    'activity' => [
        'venue' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'buttonText' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'buttonUrl' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'bottomText' => [
            'show_in_rest' => true,
            'single' => true,
        ],
    ],
    'page' => [
        'background_color' => [
            'auth_callback' => fn(): bool => current_user_can('edit_pages'),
            'show_in_rest' => true,
            'single' => true,
        ],
        'page_perex' => [
            'show_in_rest' => true,
            'single' => true,
        ],
    ],
    'post' => [
        'domicil' => [
            'show_in_rest' => true,
            'single' => true,
        ],
    ],
];
