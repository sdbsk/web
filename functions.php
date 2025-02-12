<?php

declare( strict_types=1 );

use App\ThemeKernel;
use Roots\WPConfig\Config;
use Symfony\Component\ErrorHandler\Debug;

if ( Config::get( 'WP_DEBUG' ) ) {
	Debug::enable();
}

require __DIR__ . '/src/legacy/functions/DisableFeatures.php';
require __DIR__ . '/src/legacy/functions/Meta.php';
require __DIR__ . '/src/legacy/functions/UserSettings.php';
require __DIR__ . '/src/legacy/functions/PagesExcludedFromIndex.php';
require __DIR__ . '/src/legacy/functions/Htaccess.php';
require __DIR__ . '/src/legacy/functions/ExternalJs.php';

$template = wp_get_theme()->get_template();
$assets   = 'app/themes/' . $template . '/assets/';
$manifest = json_decode( file_get_contents( __DIR__ . '/web/' . $assets . 'manifest.json' ), true );

add_action( 'admin_enqueue_scripts', function () use ( $assets, $manifest ): void {
	wp_enqueue_script( 'admin', home_url() . $manifest[ $assets . 'admin.js' ], [], false, [ 'in_footer' => true ] );
} );

add_action( 'enqueue_block_assets', function () use ( $assets, $manifest ): void {
	wp_enqueue_style( 'blocks', home_url() . $manifest[ $assets . 'blocks.css' ] );
	wp_enqueue_script( 'blocks', home_url() . $manifest[ $assets . 'blocks.js' ], [
		'wp-blocks',
		'wp-components',
		'wp-data',
		'wp-edit-post',
		'wp-element',
		'wp-hooks',
		'wp-plugins',
		'wp-server-side-render',
	], false, [ 'in_footer' => true ] );
} );

add_action( 'init', function () use ( $template ): void {
	if ( (
		     is_admin() ||
		     ( defined( 'WP_USE_THEMES' ) && WP_USE_THEMES ) ||
		     ( defined( 'REST_REQUEST' ) && REST_REQUEST )
	     ) && class_exists( ThemeKernel::class ) ) {
		$GLOBALS['kernel'] = new ThemeKernel( WP_ENV, Config::get( 'WP_DEBUG' ) );
		$GLOBALS['kernel']->boot();
		$GLOBALS['kernel']->bootWordpressTheme();

		add_rewrite_rule( '^a/.*', 'index.php?app_request=true', 'top' );
		add_filter( 'query_vars', fn( $vars ) => [ 'app_request', ...$vars ] );

		add_filter( 'template_include', fn( $template ) => preg_match( '/^\/a\/.*/', $_SERVER['REQUEST_URI'] )
			? __DIR__ . '/app_request.php'
			: $template );
	}

	require_once 'src/legacy/icon-controller.php';

	foreach ( require __DIR__ . '/src/legacy/post-types.php' as $type => $args ) {
		register_post_type( $type, $args );
	}

	foreach ( require __DIR__ . '/src/legacy/post-metas.php' as $type => $metas ) {
		foreach ( $metas as $key => $args ) {
			register_post_meta( $type, $key, $args );
		}
	}

	register_block_pattern_category( $template, [
		'label' => 'Saleziáni',
	] );
} );

add_filter( 'block_categories_all', function ( $categories ) {
	$categories[] = [
		'slug'  => 'meta',
		'title' => 'Meta',
	];

	return $categories;
} );

add_action( 'set_object_terms', function ( int $postId, array $termIds, array $termTaxonomyIds, string $taxonomy, bool $append ): void {
	if ( 'category' === $taxonomy && false === $append ) {
		$categoryId = get_default_category_id( get_post( $postId )->post_author );

		if ( false === in_array( $categoryId, $termTaxonomyIds, true ) ) {
			wp_set_post_categories( $postId, $categoryId, true );
		}
	}
}, 10, 5 );

add_action( 'wp_enqueue_scripts', function () use ( $assets, $manifest ): void {
	wp_enqueue_style( 'public', home_url() . $manifest[ $assets . 'public.css' ] );
	wp_enqueue_script( 'public', home_url() . $manifest[ $assets . 'public.js' ], [], false, [ 'in_footer' => true ] );
	wp_enqueue_script( 'consent', home_url() . $manifest[ $assets . 'consent.js' ], [], false, [ 'in_footer' => true ] );

	wp_deregister_script( 'wp-polyfill' );
	wp_deregister_script( 'regenerator-runtime' );
} );

add_filter( 'allowed_block_types_all', function (): array {
	return [
		'core/query',
		'saleziani/posts',

		// large margin blocks
		'saleziani/newsletter-form',

		'saleziani/project-columns',
		'saleziani/organization-columns',
		'saleziani/icon-columns',
		'core/group',
		'core/buttons',
		'core/embed',
		'saleziani/post-columns',

		// small margin blocks (typograficke)
		'core/image',
		'core/gallery',
		'core/heading',
		'core/paragraph',
		'core/list',
		'core/button',
		'core/pullquote',
		'core/column',
		'core/columns',

		// no margin blocks
		'core/site-logo',
		'core/template-part',
		'core/navigation-link',
		'core/site-logo',
		'core/list-item',
		'core/social-link',
		'core/social-links',
		'saleziani/project-column',
		'saleziani/organization-column',
		'saleziani/newsletter-form',
		'saleziani/darujme-form',
		'saleziani/icon',
		'saleziani/icon-column',
	];

}, 10, 2 );

add_action( 'enqueue_block_editor_assets', function () use ( $assets, $manifest ): void {
	wp_enqueue_script( 'deny-list-blocks', home_url() . $manifest[ $assets . 'admin.js' ], [
		'wp-blocks',
		'wp-dom-ready',
		'wp-edit-post'
	], false, [ 'in_footer' => false ] );
} );

add_filter( 'excerpt_more', fn(): string => '…' );

function placeholder_image_path( int $width, int $height ): string {
	return 'https://placehold.co/' . $width . 'x' . $height . '/F8DAD3/272727';
}

add_action( 'wp_body_open', function () {
	ob_start();
}, 0 );

add_action( 'wp_footer', function () {
	$content = ob_get_clean();

	echo str_replace( '<div class="wp-site-blocks">', <<<MODAL
<div class="wp-site-blocks">
<div aria-hidden="true" class="modal fade" id="donationFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" id="donationFormModalContent"></div>
    </div>
</div>
MODAL, $content );
}, 100 );

function cookiesAllowed( string $category ): bool {
	$allowedCategories = json_decode( stripslashes( $_COOKIE['cc_cookie'] ?? 'null' ), true )['categories'] ?? [];

	return in_array( $category, $allowedCategories );
}

add_filter( 'embed_oembed_html', function ( $html ) {
	if ( str_contains( $html, '<iframe' ) && ! cookiesAllowed( 'targeting' ) ) {
		$thisContentLabel = str_contains( $html, 'youtube' ) || str_contains( $html, 'vimeo' ) ? 'toto video' : 'tento obsah';

		return '<div class="cc-iframe" data-iframe="' . esc_attr( $html ) . '">
                    <div>
                        Prosím, povoľte marketingové cookies, aby sme vám mohli zobraziť ' . $thisContentLabel . '. <br>
                        <a href="#" onclick="return showBlockedIframes();">Súhlasím s používaním marketingových cookies</a>
                    </div>
                </div>';
	}

	return $html;
} );

add_action( 'template_redirect', function (): void {
	global $post;

	if ( is_author() || is_date() || ( is_single() && 'future' === $post->post_status ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}
} );

add_filter( 'get_terms', function ( array $terms, array $taxonomies ): array {
	if ( is_user_logged_in() && 'category' === $taxonomies[0] && ( is_admin() || wp_is_json_request() ) ) {
		$currentUser = wp_get_current_user();

		if ( in_array( 'author', $currentUser->roles, true ) ) {
			$defaultCategoryId = get_default_category_id( $currentUser->ID );

			/** @var WP_Term|int $category */
			foreach ( $terms as $index => $category ) {
				$categoryId = $category instanceof WP_Term ? $category->term_id : $category;

				if (
					$defaultCategoryId !== $categoryId &&
					false === in_array(
						$defaultCategoryId,
						get_ancestors( $categoryId, 'category' ),
						true )
				) {
					unset( $terms[ $index ] );
				}
			}

			$terms = array_values( $terms );
		}
	}

	return $terms;
}, 10, 2 );

add_action( 'save_post', function ( int $postId ): void {
	if ( function_exists( 'w3tc_flush_post' ) ) {
		$ancestors = get_post_ancestors( $postId );
		$parentId  = empty( $ancestors ) ? $postId : end( $ancestors );

		w3tc_flush_post( $parentId );

		foreach ( get_pages( [ 'child_of' => $parentId ] ) as $child ) {
			w3tc_flush_post( $child->ID );
		}
	}
} );

add_filter( 'query_loop_block_query_vars', function ( array $query, WP_Block $block ): array {
	if ( isset( $block->context['queryId'], $_GET[ 'query-' . $block->context['queryId'] . '-category' ] ) ) {
		$category = get_category_by_slug( $_GET[ 'query-' . $block->context['queryId'] . '-category' ] );

		if ( $category instanceof WP_Term ) {
			$query['cat'] = $category->term_id;
		}
	}

	return $query;
}, 10, 2 );

add_filter( 'render_block_core/query', function ( string $content, array $block, WP_Block $instance ): string {
	if ( 'saleziani/posts' === ( $instance->attributes['namespace'] ?? '' ) && isset( $instance->attributes['menuCategory'] ) ) {
		$queryCategoryParameter = 'query-' . $instance->attributes['queryId'] . '-category';
		$queryPageParameter     = 'query-' . $instance->attributes['queryId'] . '-page';

		$menuCategoryId    = $instance->attributes['menuCategory'];
		$currentCategoryId = $menuCategoryId;

		if ( isset( $_GET[ $queryCategoryParameter ] ) ) {
			$currentCategory = get_category_by_slug( $_GET[ $queryCategoryParameter ] );

			if ( $currentCategory instanceof WP_Term ) {
				$currentCategoryId = $currentCategory->term_id;
			}
		}

		$categoryUrl = function ( int $categoryId ) use (
			$menuCategoryId,
			$queryCategoryParameter,
			$queryPageParameter,
		): string {
			$url = remove_query_arg( [ $queryCategoryParameter, $queryPageParameter ] );

			if ( $menuCategoryId !== $categoryId ) {
				$category = get_category( $categoryId );

				if ( $category instanceof WP_Term ) {
					$url = add_query_arg( [ $queryCategoryParameter => $category->slug ], $url );
				}
			}

			return $url;
		};

		$categories = '<ul class="wp-block-saleziani-categories"><li class="cat-item cat-item-' . $menuCategoryId . ( $currentCategoryId === $menuCategoryId ? ' current-cat' : '' ) . '"><a href="' . $categoryUrl( $menuCategoryId ) . '">Všetko</a></li>';

		/** @var WP_Term $category */
		foreach ( get_categories( [ 'parent' => $menuCategoryId ] ) as $category ) {
			$categories .= '<li class="cat-item cat-item-' . $category->term_id . ( $currentCategoryId === $category->term_id ? ' current-cat' : '' ) . '"><a href="' . $categoryUrl( $category->term_id ) . '">' . $category->name . '</a></li>';
		}

		$categories .= '</ul>';

		$content = str_replace( '<ul', $categories . '<ul', $content );
	}

	return $content;
}, 10, 3 );

function get_default_category_id( int|string $userId ): int {
	return (int) get_user_meta( (int) $userId, 'default_category', true ) ?: ( (int) get_option( 'default_category' ) );
}

