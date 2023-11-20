<?php

$icon = $_GET['icon'] ?? null;

if (null === $icon) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$file = "../vendor/google/material-symbols/svg/300/outlined/$icon.svg";

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
