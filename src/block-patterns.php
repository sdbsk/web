<?php

declare(strict_types=1);

function wrap_pattern_content(string $name, string $content): string
{
    return '<!-- wp:group {"className":"wp-pattern-saleziani-' . $name . '"} --><div class="wp-block-group wp-pattern-saleziani-' . $name . '">' . $content . '</div><!-- /wp:group -->';
}

return [
    'call-to-action' => [
        'content' => '<!-- wp:group {"align":"full","backgroundColor":"apricot","className":"wp-pattern-saleziani-call-to-action","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull wp-pattern-saleziani-call-to-action has-apricot-background-color has-background"><!-- wp:group {"backgroundColor":"white","layout":{"type":"constrained"}} --><div class="wp-block-group has-white-background-color has-background"><!-- wp:heading --><h2 class="wp-block-heading">Vaša podpora je dôležitá</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Saleziánske dielo je sieť saleziánskych komunitných centier, v ktorých tisíce ľudí denne trávia zmysluplný čas. Každý je vítaný. Tvoj pravidelný mesačný príspevok je potrebný pre udržanie a rozvoj športových, kultúrnych, sociálnych a duchovných aktivít pre deti, mladých, rodičov aj seniorov.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Podporiť teraz</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --></div><!-- /wp:group -->',
        'title' => 'Výzva k akcii',
    ],
    'description-link' => [
        'content' => wrap_pattern_content('description-link', '<!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":8} --><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec rhoncus elit quis nisl vehicula, ac mattis nulla suscipit.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="#">Dozvedieť sa viac</a></p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->'),
        'title' => 'Popis a odkaz',
    ],
    'heading-description-link' => [
        'content' => wrap_pattern_content('heading-description-link', '<!-- wp:wp-bootstrap-blocks/row --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:heading --><h2 class="wp-block-heading">Nadpis</h2><!-- /wp:heading --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque orci sem, interdum ac eleifend sed.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="#">Zobraziť všetko</a></p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->'),
        'title' => 'Nadpis, popis a odkaz',
    ],
    'narrow-content' => [
        'content' => '<!-- wp:group {"className":"wp-pattern-saleziani-narrow-content"} --><div class="wp-block-group wp-pattern-saleziani-narrow-content"><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In dui nisi, tempor ac orci in, condimentum vehicula ipsum. Proin iaculis erat vel sapien faucibus tempor. Nunc mauris urna, rutrum in mauris at, placerat euismod magna. Nunc scelerisque dignissim ligula id dapibus. Aliquam sodales feugiat felis, id ultricies urna scelerisque quis. Donec lobortis sem diam, id maximus nunc ornare a. Etiam urna enim, tincidunt vitae sapien ac, mattis mattis magna. Pellentesque faucibus consequat orci at cursus.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
        'title' => 'Zúžený obsah',
    ],
    'numbers' => [
        'content' => wrap_pattern_content('numbers', '<!-- wp:heading --><h2 class="wp-block-heading">Saleziáni v číslach</h2><!-- /wp:heading --><!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"potato"} --><h3 class="wp-block-heading has-potato-color has-text-color">200</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"potato"} --><p class="has-potato-color has-text-color">saleziánov</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"pineapple"} --><h3 class="wp-block-heading has-pineapple-color has-text-color">24 165</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"pineapple"} --><p class="has-pineapple-color has-text-color">detí na táboroch</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"lime"} --><h3 class="wp-block-heading has-lime-color has-text-color">1 050</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"lime"} --><p class="has-lime-color has-text-color">podujatí</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":3,"sizeXs":6} --><!-- wp:heading {"level":3,"textColor":"blueberry"} --><h3 class="wp-block-heading has-blueberry-color has-text-color">17</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"blueberry"} --><p class="has-blueberry-color has-text-color">projektov</p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->'),
        'title' => 'Čísla',
    ],
    'three-links-to-pages' => [
        'content' => wrap_pattern_content('three-links-to-pages', '<!-- wp:wp-bootstrap-blocks/row {"template":"custom"} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row -->'),
        'title' => '3 x odkaz na stránku',
    ],
];
