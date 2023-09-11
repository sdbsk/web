<?php

declare(strict_types=1);

/**
 * Title: Nadpis, popis a obrázok
 * Slug: saleziani/heading-description-image
 * Categories: saleziani
 */
?>

<!-- wp:group {"className":"wp-pattern-saleziani-heading-description-image"} -->
<div class="wp-block-group wp-pattern-saleziani-heading-description-image">
    <!-- wp:group {"className":"row"} -->
    <div class="wp-block-group row">
        <!-- wp:group {"className":"col-12 col-sm-6"} -->
        <div class="wp-block-group col-12 col-sm-6">
            <!-- wp:heading {"level":3} -->
            <h3 class="wp-block-heading">Lorem ipsum dolor sit amet</h3>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
        <!-- wp:group {"className":"col-12 col-sm-6"} -->
        <div class="wp-block-group col-12 col-sm-6">
            <!-- wp:image -->
            <figure class="wp-block-image">
                <img src="<?php echo placeholder_image_path(375, 185) ?>"/>
            </figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
