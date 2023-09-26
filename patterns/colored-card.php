<?php

declare(strict_types=1);

/**
 * Title: Farebné kartičky
 * Slug: saleziani/colored-cards
 * Categories: saleziani
 */
?>

<!-- wp:group {"className":"wp-pattern-saleziani-colored-cards"} -->
<div class="wp-block-group wp-pattern-saleziani-colored-cards">
    <!-- wp:group {"className":"row row-cols-1 row-cols-sm-2 g-4"} -->
    <div class="wp-block-group row row-cols-1 row-cols-sm-2 g-4">
        <!-- wp:group {"className":"col", "layout":{"type":"constrained"}} -->
        <div class="wp-block-group col">
            <!-- wp:group {"backgroundColor":"potato","className":"colored-card"} -->
            <div class="wp-block-group colored-card has-potato-background-color has-background">
                <!-- wp:image -->
                <figure class="wp-block-image"><img src="<?php echo placeholder_image_path(570, 356) ?>"/></figure>
                <!-- /wp:image -->
                <!-- wp:group {"className":"content"} -->
                <div class="wp-block-group content">
                    <!-- wp:group {"className":"content-top"} -->
                    <div class="wp-block-group content-top">
                        <!-- wp:heading {"level":3,"textColor":"white"} -->
                        <h3 class="wp-block-heading has-white-color has-text-color">Nadpis</h3>
                        <!-- /wp:heading -->
                        <!-- wp:paragraph {"textColor":"white"} -->
                        <p class="has-white-color has-text-color">Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit,
                            sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                    <!-- wp:group {"className":"content-bottom"} -->
                    <div class="wp-block-group content-bottom">
                        <!-- wp:paragraph {"textColor":"white"} -->
                        <p class="has-white-color has-text-color"><a href="#">Čítať viac</a></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
        <!-- wp:group {"className":"col", "layout":{"type":"constrained"}} -->
        <div class="wp-block-group col">
            <!-- wp:group {"backgroundColor":"black","textColor":"white", "className":"colored-card"} -->
            <div class="wp-block-group colored-card has-white-color has-black-background-color has-text-color has-background">
                <!-- wp:image -->
                <figure class="wp-block-image"><img src="<?php echo placeholder_image_path(570, 356) ?>"/></figure>
                <!-- /wp:image -->
                <!-- wp:group {"className":"content"} -->
                <div class="wp-block-group content">
                    <!-- wp:group {"className":"content-top"} -->
                    <div class="wp-block-group content-top">
                        <!-- wp:heading {"level":3,"textColor":"white"} -->
                        <h3 class="wp-block-heading has-white-color has-text-color">Nadpis</h3>
                        <!-- /wp:heading -->
                        <!-- wp:paragraph {"textColor":"white"} -->
                        <p class="has-white-color has-text-color">Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit,
                            sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                    <!-- wp:group {"className":"content-bottom"} -->
                    <div class="wp-block-group content-bottom">
                        <!-- wp:paragraph {"textColor":"white"} -->
                        <p class="has-white-color has-text-color">
                            <a href="#">Čítať viac</a>
                        </p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
