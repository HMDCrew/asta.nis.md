<?php


/**
 * This function sorts auctions by their start date in ascending order.
 *
 * @param query is a variable that holds the WordPress query object. It is used to modify the
 * parameters of the main query for a specific page or post type.
 */
function sort_auction_by_start_date( $query ) {
	if ( is_post_type_archive( 'auctions' ) && $query->is_main_query() ) {
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );
		$query->set( 'meta_key', 'end_date' );
		$query->set( 'post_status', 'publish' );
	}
}
add_action( 'pre_get_posts', 'sort_auction_by_start_date' );


/**
 * This function redirects users from the default WordPress login page to a custom login page.
 */
function wpr_custom_login() {

	global $pagenow;

	$action = ( isset( $_REQUEST['action'] ) ? preg_replace( '/[^a-zA-Z0-9\%]/i', '', $_REQUEST['action'] ) : '' );

	if ( 'wp-login.php' === $pagenow && 'logout' !== $action ) {
		wp_redirect( '/login/' );
		exit();
	}
}
add_action( 'init', 'wpr_custom_login' );
