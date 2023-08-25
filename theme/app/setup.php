<?php

declare(strict_types=1);

namespace App;

use function Roots\bundle;
use function Roots\view;
use const TEMPLATEPATH;

add_action('wp_enqueue_scripts', function (): void {
    bundle('app')->enqueue();
}, 100);

add_action('enqueue_block_editor_assets', function (): void {
    bundle('editor')->enqueue();
}, 100);

add_action('widgets_init', function (): void {
    register_sidebar([
        'after_widget' => '',
        'before_widget' => '',
        'id' => 'sidebar-footer',
        'name' => 'Pätička',
    ]);
});

add_action('init', function (): void {
    foreach (scandir(TEMPLATEPATH . '/resources/views/blocks/') as $filename) {
        preg_match('~([a-zA-Z0-9-]+)\.blade\.php~', $filename, $matches);

        if (isset($matches[1])) {
            register_block_type('theme/' . $matches[1], [
                'render_callback' => fn(array $attributes): string => view(
                    'blocks/' . $matches[1],
                    ['attributes' => $attributes],
                )->render(),
            ]);
        }
    }

    register_post_type('campaign', [
        'labels' => [
            'add_new' => __('Pridať novú', 'sage'),
            'add_new_item' => __('Pridať novú kampaň', 'sage'),
            'all_items' => __('Všetky kampane', 'sage'),
            'archives' => __('Archív kampaní', 'sage'),
            'attributes' => __('Atribúty kampaní', 'sage'),
            'edit_item' => __('Upraviť kampaň', 'sage'),
            'filter_items_list' => __('Filtrovať zoznam kampaní', 'sage'),
            'insert_into_item' => __('Vložiť ku kampani', 'sage'),
            'item_link' => __('Odkaz na kampaň', 'sage'),
            'item_link_description' => __('Odkaz na kampaň.', 'sage'),
            'item_published' => __('Kampaň zverejnená.', 'sage'),
            'item_published_privately' => __('Kampaň zverejnená ako súkromný.', 'sage'),
            'item_reverted_to_draft' => __('Kampaň vrátená do stavu koncept.', 'sage'),
            'item_scheduled' => __('Zverejnenie kampane bolo naplánované.', 'sage'),
            'item_updated' => __('Kampaň aktualizovaná.', 'sage'),
            'items_list' => __('Zoznam kampaní', 'sage'),
            'items_list_navigation' => __('Navigácia v zozname kampaní', 'sage'),
            'name' => __('Kampane', 'sage'),
            'new_item' => __('Nová kampaň', 'sage'),
            'not_found' => __('Kampaň sa nenašla', 'sage'),
            'not_found_in_trash' => __('Kampaň sa v koši nenašla', 'sage'),
            'search_items' => __('Vyhľadať kampaň', 'sage'),
            'singular_name' => __('Kampaň', 'sage'),
            'uploaded_to_this_item' => __('Nahrať ku kampani', 'sage'),
            'view_item' => __('Zobraziť stránku kampane', 'sage'),
            'view_items' => __('Zobraziť kampane', 'sage'),
        ],
        'public' => true,
        'menu_icon' => 'dashicons-megaphone',
        'menu_position' => 20,
        'rewrite' => ['slug' => 'kampane'],
        'show_in_rest' => true,
    ]);
});
