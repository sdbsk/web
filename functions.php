<?php

declare(strict_types=1);

$manifest = json_decode(file_get_contents(__DIR__ . '/web/app/themes/saleziani/assets/manifest.json'), true);

add_action('init', function (): void {
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

add_action('enqueue_block_editor_assets', function () use ($manifest): void {
    wp_enqueue_style('editor', home_url() . $manifest['app/themes/saleziani/assets/editor.css']);

    foreach (get_cumstom_block_types() as $blockType) {
        wp_enqueue_script($blockType . '-block', get_template_directory_uri() . '/assets/blocks/' . $blockType . '.js', ['wp-blocks', 'wp-element']);
    }
});

add_action('wp_enqueue_scripts', function () use ($manifest): void {
    wp_enqueue_style('style', home_url() . $manifest['app/themes/saleziani/assets/style.css']);
});

add_filter('allowed_block_types_all', function (): array {
    $blockTypes = [
        'core/button',
        'core/buttons',
        'core/cover',
        'core/heading',
        'core/image',
        'core/list',
        'core/paragraph',
        'core/separator',
        'wp-bootstrap-blocks/column',
        'wp-bootstrap-blocks/container',
        'wp-bootstrap-blocks/row',
    ];

    foreach (get_cumstom_block_types() as $blockType) {
        $blockTypes[] = 'saleziani/' . $blockType;
    }

    return $blockTypes;
}, 10, 2);

function get_cumstom_block_types(): array
{
    $blockTypes = [];

    foreach (scandir(__DIR__ . '/assets/blocks/') as $filename) {
        preg_match('~([a-zA-Z0-9-]+)\.js~', $filename, $matches);

        if (isset($matches[1])) {
            $blockTypes[] = $matches[1];
        }
    }

    return $blockTypes;
}
