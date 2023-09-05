<?php

declare(strict_types=1);

return [
    'heading-with-description-and-link' => [
        'categories' => ['saleziani'],
        'content' => '<!-- wp:wp-bootstrap-blocks/container --><!-- wp:wp-bootstrap-blocks/row --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:heading --><h2 class="wp-block-heading">Nadpis</h2><!-- /wp:heading --><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":6} --><!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque orci sem, interdum ac eleifend sed.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="#">Zobraziť všetko</a></p><!-- /wp:paragraph --><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row --><!-- /wp:wp-bootstrap-blocks/container -->',
        'title' => 'Nadpis s popisom a odkazom',
    ],
    'three-links-to-pages' => [
        'categories' => ['saleziani'],
        'content' => '<!-- wp:wp-bootstrap-blocks/container --><!-- wp:wp-bootstrap-blocks/row {"template":"custom","noGutters":true} --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- wp:wp-bootstrap-blocks/column {"sizeMd":4,"sizeSm":6} --><!-- wp:saleziani/link-to-page /--><!-- /wp:wp-bootstrap-blocks/column --><!-- /wp:wp-bootstrap-blocks/row --><!-- /wp:wp-bootstrap-blocks/container -->',
        'title' => '3 x odkaz na stránku',
    ],
];
