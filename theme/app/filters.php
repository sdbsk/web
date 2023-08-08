<?php

declare(strict_types=1);

namespace App;

use WP_Block;

add_filter('render_block', function (string $content, array $props, WP_Block $block): string {
    /** @noinspection PhpSwitchStatementWitSingleBranchInspection */
    switch ($block->name) {
        case 'core/latest-posts':
            if ($block->attributes['displayCategories']) {
                return str_replace('</li>', 'Toto je tricky. Ak by sme chceli zobrazit kategorie clanku, musime najskor najst clanok podla slugu, vygenerovaneho v $content "a href".</li>', $content);
            }
            break;
    }

    return $content;
}, 10, 3);
