<?php

declare(strict_types=1);

add_filter('query_vars', fn(array $vars): array => ['icon', ...$vars]);
add_rewrite_rule('^assets/icon/([a-z0-9_]+\.svg)$', 'index.php?name=icon&icon=$matches[1]', 'top');
add_action('parse_request', function (WP $wp): void {
    if ('icon' === ($wp->query_vars['name'] ?? null)) {
        $icon = $wp->query_vars['icon'] ?? null;

        if (null === $icon) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        if (!preg_match('/[a-z0-9_]+\.svg/', $icon)) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        $file = "../vendor/google/material-symbols/svg/400/outlined/$icon";

        if (!file_exists($file)) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        $content = file_get_contents($file);

        if (false === $content) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        header('Content-Type: image/svg+xml');
        echo $content;
        exit;
    }
});
