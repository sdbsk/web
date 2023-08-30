<?php

declare(strict_types=1);

add_action('wp_enqueue_scripts', function (): void {
    wp_enqueue_style('saleziani', get_template_directory_uri() . '/assets/public/app.css');
});
