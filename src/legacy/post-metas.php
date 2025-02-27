<?php

declare(strict_types=1);

return [
    'activity' => [
        'venue' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'button_text' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'button_url' => [
            'show_in_rest' => true,
            'single' => true,
        ],
        'bottom_text' => [
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
