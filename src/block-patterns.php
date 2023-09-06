<?php

declare(strict_types=1);

$definitions = [
    'call-to-action' => [
        'attributes' => ['align' => 'full', 'backgroundColor' => 'apricot'],
        'classes' => ['alignfull', 'has-apricot-background-color', 'has-background'],
        'content' => '<!-- wp:group {"backgroundColor":"white"} --><div class="wp-block-group has-white-background-color has-background"><!-- wp:heading --><h2 class="wp-block-heading">Vaša podpora je dôležitá</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Saleziánske dielo je sieť saleziánskych komunitných centier, v ktorých tisíce ľudí denne trávia zmysluplný čas. Každý je vítaný. Tvoj pravidelný mesačný príspevok je potrebný pre udržanie a rozvoj športových, kultúrnych, sociálnych a duchovných aktivít pre deti, mladých, rodičov aj seniorov.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Podporiť teraz</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->',
        'title' => 'Výzva k akcii',
    ],
    'description-link' => [
        'content' => '<!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":8} --><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec rhoncus elit quis nisl vehicula, ac mattis nulla suscipit.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="#">Dozvedieť sa viac</a></p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->',
        'title' => 'Popis a odkaz',
    ],
    'heading-description-link' => [
        'content' => '<!-- wp:wp-bootstrap-blocks/row --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:heading --><h2 class="wp-block-heading">Nadpis</h2><!-- /wp:heading --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque orci sem, interdum ac eleifend sed.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="#">Zobraziť všetko</a></p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->',
        'title' => 'Nadpis, popis a odkaz',
    ],
    'narrow-content' => [
        'content' => '<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In dui nisi, tempor ac orci in, condimentum vehicula ipsum. Proin iaculis erat vel sapien faucibus tempor. Nunc mauris urna, rutrum in mauris at, placerat euismod magna. Nunc scelerisque dignissim ligula id dapibus. Aliquam sodales feugiat felis, id ultricies urna scelerisque quis. Donec lobortis sem diam, id maximus nunc ornare a. Etiam urna enim, tincidunt vitae sapien ac, mattis mattis magna. Pellentesque faucibus consequat orci at cursus.</p><!-- /wp:paragraph -->',
        'title' => 'Zúžený obsah',
    ],
    'numbers' => [
        'content' => '<!-- wp:heading --><h2 class="wp-block-heading">Saleziáni v číslach</h2><!-- /wp:heading --><!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"potato"} --><h3 class="wp-block-heading has-potato-color has-text-color">200</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"potato"} --><p class="has-potato-color has-text-color">saleziánov</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"pineapple"} --><h3 class="wp-block-heading has-pineapple-color has-text-color">24 165</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"pineapple"} --><p class="has-pineapple-color has-text-color">detí na táboroch</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"lime"} --><h3 class="wp-block-heading has-lime-color has-text-color">1 050</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"lime"} --><p class="has-lime-color has-text-color">podujatí</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"blueberry"} --><h3 class="wp-block-heading has-blueberry-color has-text-color">17</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"blueberry"} --><p class="has-blueberry-color has-text-color">projektov</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->',
        'title' => 'Čísla',
    ],
    'three-links-to-pages' => [
        'content' => '<!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->',
        'title' => '3 x odkaz na stránku',
    ],
];

$patterns = [];

foreach ($definitions as $name => $definition) {
    $class = 'wp-pattern-saleziani-' . $name;
    $definition['attributes']['className'] = $class;
    $definition['classes'][] = 'wp-block-group';
    $definition['classes'][] = $class;

    sort($definition['attributes']);
    sort($definition['classes']);

    $patterns[$name] = [
        'categories' => ['saleziani'],
        'content' => '<!-- wp:group ' . json_encode($definition['attributes']) . ' --><div class="' . implode(' ', $definition['classes']) . '">' . $definition['content'] . '</div><!-- /wp:group -->',
        'title' => $definition['title'],
    ];
}

return $patterns;
