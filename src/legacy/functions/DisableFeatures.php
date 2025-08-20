<?php

use JetBrains\PhpStorm\NoReturn;

add_action( 'after_setup_theme', function () {
	remove_theme_support( 'core-block-patterns' );
	add_filter( 'should_load_remote_block_patterns', '__return_false' );
} );

add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable all xml-rpc endpoints
add_filter( 'xmlrpc_methods', fn(): array => [], PHP_INT_MAX );

// remove some meta tags from WordPress
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

add_action( 'after_setup_theme', function () {
	// Remove the REST API lines from the HTML Header
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Remove the REST API endpoint.
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );

	// Don't filter oEmbed results.
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result' );

	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	// Filters for WP-API version 1.x
	add_filter( 'json_enabled', '__return_false' );
	add_filter( 'json_jsonp_enabled', '__return_false' );

	// Filters for WP-API version 2.x
	add_filter( 'rest_jsonp_enabled', '__return_false' );

	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
} );

add_filter( 'rest_authentication_errors', fn( $errors ) => is_wp_error( $errors ) ? $errors : ( is_user_logged_in() ?
	$errors :
	new WP_Error( 'not_found', 'Not found', [ 'status' => 404 ] )
) );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Tidy up the admin
add_action( 'admin_init', function (): void {
	global $menu;

	if ( is_iterable( $menu ) ) {
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'w3tc_dashboard' );
//		remove_menu_page( 'firebox' );

		if ( false === current_user_can( 'edit_others_posts' ) ) {
			remove_menu_page( 'index.php' );
		}
	}
} );

// Remove 404 redirection guesser
add_filter( 'do_redirect_guess_404_permalink', fn() => false );

// Remove users from sitemap
add_filter( 'wp_sitemaps_add_provider', fn( $provider, $name ) => 'users' === $name ? false : $provider, 10, 2 );

// Remove tags from sitemap
add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
	unset( $taxonomies['post_tag'] );

	return $taxonomies;
} );

#[NoReturn] function disableFeed(): void {
	global $wp_query;
	$wp_query->is_feed = false;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

	echo 'Not Found';
	exit;
}

add_action( 'do_feed', 'disableFeed', 1 );
add_action( 'do_feed_rdf', 'disableFeed', 1 );
add_action( 'do_feed_rss', 'disableFeed', 1 );
add_action( 'do_feed_rss2', 'disableFeed', 1 );
add_action( 'do_feed_atom', 'disableFeed', 1 );
add_action( 'do_feed_rss2_comments', 'disableFeed', 1 );
add_action( 'do_feed_atom_comments', 'disableFeed', 1 );

add_action( 'wp_dashboard_setup', function () {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'health_check_status', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
} );

add_action( 'wp_before_admin_bar_render', function (): void {
	global $wp_admin_bar;

	$wp_admin_bar->remove_menu( 'about' );            // Remove the about WordPress link
	$wp_admin_bar->remove_menu( 'comments' );         // Remove the comments link
	$wp_admin_bar->remove_menu( 'documentation' );    // Remove the WordPress documentation link
	$wp_admin_bar->remove_menu( 'feedback' );         // Remove the feedback link
	$wp_admin_bar->remove_menu( 'site-editor' );      // Remove the comments link
	$wp_admin_bar->remove_menu( 'support-forums' );   // Remove the support forums link
	$wp_admin_bar->remove_menu( 'updates' );          // Remove the updates link
	$wp_admin_bar->remove_menu( 'w3tc' );             // If you use w3 total cache remove the performance link
	$wp_admin_bar->remove_menu( 'wp-logo' );          // Remove the WordPress logo
	$wp_admin_bar->remove_menu( 'wporg' );            // Remove the WordPress.org link
} );

