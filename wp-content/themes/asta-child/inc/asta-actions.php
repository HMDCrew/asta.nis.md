<?php

/**
 * The function `gallery_auction_template` displays a gallery of images for an auction, with options
 * for the URLs of the images and the order of the slides.
 *
 * @param array args  is an array of optional parameters that can be passed to the
 * `gallery_auction_template()` function. It contains the following keys:
 */
function gallery_auction_template( array $args = array() ) {

	$args = array(
		'urls'         => (
			isset( $args['urls'] )
				? $args['urls']
				: (
					isset( $args['auction_id'] ) && $args['auction_id']
						? apply_filters( 'wpr_get_auction_gallery', $args['auction_id'] )
						: array()
				)
		),
		'last_slide'   => isset( $args['last_slide'] ) ? $args['last_slide'] : '',
		'slide_after'  => isset( $args['slide_after'] ) ? $args['slide_after'] : '',
		'slide_before' => isset( $args['slide_before'] ) ? $args['slide_before'] : '',
	);

	get_template_part( 'template-parts/sections/auction', 'gallery', $args );
}
add_action( 'gallery_auction_template', 'gallery_auction_template' );


/**
 * This is a PHP function that generates a gallery of thumbnails for an auction template.
 *
 * @param array args  is an array of optional parameters that can be passed to the function
 * gallery_thumbs_auction_template(). It contains the following keys:
 */
function gallery_thumbs_auction_template( array $args = array() ) {

	$args = array(
		'urls'         => (
			isset( $args['urls'] )
				? $args['urls']
				: (
					isset( $args['auction_id'] ) && $args['auction_id']
						? apply_filters( 'wpr_get_auction_gallery', $args['auction_id'] )
						: array()
				)
		),
		'last_slide'   => isset( $args['last_slide'] ) ? $args['last_slide'] : '',
		'slide_after'  => isset( $args['slide_after'] ) ? $args['slide_after'] : '',
		'slide_before' => isset( $args['slide_before'] ) ? $args['slide_before'] : '',
	);

	get_template_part( 'template-parts/sections/auction', 'gallery-thumbs', $args );
}
add_action( 'gallery_thumbs_auction_template', 'gallery_thumbs_auction_template' );


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
