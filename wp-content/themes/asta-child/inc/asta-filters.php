<?php

/**
 * It adds the SVG MIME type to the list of allowed MIME types
 * @link https://css-tricks.com/snippets/wordpress/allow-svg-through-wordpress-media-uploader/
 *
 * @param array mime_types An array of mime types keyed by the file extension regex
 * corresponding to those types.
 *
 * @return array of allowed mime types.
 */
function theme_restrict_mime_types( $mime_types ) {

	$allowed_mime = array(
		'svg' => 'image/svg+xml',
	);

	return array_merge( $mime_types, $allowed_mime );
}
add_filter( 'upload_mimes', 'theme_restrict_mime_types' );



/**
 * The function retrieves either the URL or HTML tag for an image attachment in WordPress based on its
 * ID, size, and optional classes.
 *
 * @param int id The ID of the image attachment you want to retrieve.
 * @param string size The size parameter specifies the size of the image to retrieve. It can be set to
 * 'full' for the original size, or to a registered image size name (e.g. 'thumbnail', 'medium',
 * 'large', etc.) or to an array of width and height values in pixels.
 * @param bool tag A boolean parameter that determines whether to return the image tag or just the
 * image URL. If set to true, the function will return the complete image tag with all the necessary
 * attributes. If set to false, the function will return just the URL of the image.
 * @param string classes The classes parameter is a string that allows you to add CSS classes to the
 * HTML tag that wraps the image. This can be useful for styling the image with custom CSS.
 *
 * @return string the HTML tag for an image with the
 * specified ID and size, or the URL of the image with the specified ID and size, depending on the
 * value of the `` parameter. If `` is `true`, the function returns the HTML tag, otherwise it
 * returns the URL. The function also accepts optional parameters for adding CSS classes
 */
function wpr_get_image_by_id( int $id, string $size = 'full', bool $tag = false, string $classes = '' ) {
	return $tag
		? wp_get_attachment_image( $id, $size, false, $classes )
		: wp_get_attachment_image_url( $id, $size );
}
add_filter( 'wpr_get_image_by_id', 'wpr_get_image_by_id', 3 );


/**
 * This function retrieves the profile picture of a user by their ID, and if it doesn't exist, it
 * returns a default gravatar image.
 *
 * @param int user_id The user ID is an integer value that represents the unique identifier of a user
 * in the WordPress database. It is used to retrieve the user's profile picture from the user meta
 * data.
 *
 * @return string URL of the user's profile picture if it exists in the user meta data, otherwise it
 * returns the URL of a default Gravatar image.
 */
function get_picture_profile( int $user_id ) {

	$profile_picture = get_user_meta( $user_id, 'profile-picture', true );

	return esc_url( $profile_picture ? $profile_picture : 'https://secure.gravatar.com/avatar/c6c727854f5d1de9a7a74abb38cf947d?s=96&d=mm&r=g' );
}
add_filter( 'wpr_get_picture_profile', 'get_picture_profile', 10 );


/**
 * The function retrieves the start and end dates of an auction and returns them in a formatted string.
 *
 * @param int auction_id The ID of the auction post for which the start and end dates are being retrieved.
 *
 * @return string formatted string that includes the start and end dates of an auction, based on the
 * provided auction ID. If no auction ID is provided, an empty string is returned.
 */
function get_auction_date( int $auction_id ) {

	if ( $auction_id ) {

		$start_date = get_post_meta( $auction_id, 'start_date', true );
		$end_date   = get_post_meta( $auction_id, 'end_date', true );

		if ( '' !== $start_date && '' !== $end_date ) {
			$start_date = new DateTimeImmutable( $start_date );
			$end_date   = new DateTimeImmutable( $end_date );

			return esc_html( sprintf( '%s to %s', $start_date->format( 'd/m/Y' ), $end_date->format( 'd/m/Y' ) ) );
		}
	}

	return '';
}
add_filter( 'wpr_get_auction_date', 'get_auction_date', 10 );


/**
 * The function returns the value of a specified meta key for a given auction ID, with HTML characters
 * escaped.
 *
 * @param int auction_id An integer representing the ID of the auction post for which the meta value is
 * being retrieved.
 * @param string key The  parameter is a string that represents the name of the meta field to
 * retrieve the value from. It is used in conjunction with the  parameter to retrieve the
 * meta value associated with a specific auction post. The function then returns the escaped HTML value
 * of the meta field.
 *
 * @return string This function returns the value of a specific meta key for a given auction post ID, after
 * sanitizing it with `esc_html()`. If the auction ID is not provided or does not exist, an empty
 * string is returned.
 */
function esc_auction_meta( int $auction_id, string $key ) {

	if ( $auction_id ) {
		return esc_html(
			get_post_meta( $auction_id, $key, true )
		);
	}

	return '';
}
add_filter( 'wpr_esc_auction_meta', 'esc_auction_meta', 10, 2 );


/**
 * This PHP function retrieves the auction type (category) based on the provided auction ID.
 *
 * @param int auction_id This is an integer parameter representing the ID of the auction for which we
 * want to retrieve the auction type.
 *
 * @return array with the ID and name of the first category term associated with the given auction
 * ID. If the auction ID is not provided or no category term is found, an empty array is returned.
 */
function get_auction_type( $auction_id ) {

	if ( $auction_id ) {

		$terms = get_the_terms( $auction_id, 'auction_category' );

		if ( ! empty( $terms ) ) {

			$term = reset( $terms );

			return array(
				'id'   => $term->term_id,
				'name' => $term->name,
			);
		}
	}

	return array();
}
add_filter( 'wpr_get_auction_type', 'get_auction_type', 10 );


/**
 * This function returns the users who have placed bids on an auction, with a limit of 10 bids if there
 * are more than 10.
 *
 * @param array|false auction_bids The parameter `` is likely an array containing information about
 * bids made on an auction. The function `get_auction_bids()` takes this array as input and returns an
 * array of user IDs who made the bids. If there are more than 10 bids, it only returns the last
 *
 * @return array of users who have placed bids on an auction. If there are no bids or the input
 * parameter is empty, it returns an empty array. The function uses the `bids_users_ids_to_users`
 * function to convert the user IDs to user objects and returns the last 10 bids if there are more than
 * 10 bids, otherwise it returns all the bids.
 */
function get_auction_bids( $auction_bids ) {

	if ( ! empty( $auction_bids ) && $auction_bids ) {

		return bids_users_ids_to_users(
			count( $auction_bids ) > 10
				? array_slice( $auction_bids, -10, 10, true )
				: $auction_bids
		);
	}

	return array();
}
add_filter( 'wpr_get_auction_bids', 'get_auction_bids', 10 );


/**
 * The function retrieves the last price of an auction as a float value.
 *
 * @param int auction_id The ID of the auction post for which we want to retrieve the last price.
 *
 * @return float last price of an auction as a float value. It retrieves the value from the
 * 'auction_price' meta field of the post with the given .
 */
function get_auction_last_price( int $auction_id ) {
	return floatval( get_post_meta( $auction_id, 'auction_price', true ) );
}
add_filter( 'wpr_get_auction_last_price', 'get_auction_last_price', 10 );


/**
 * This PHP function adds a class attribute to a navigation link.
 *
 * @param string val  is a parameter that represents the default value of the attributes for the
 * navigation posts link. This parameter is used in a WordPress filter function to modify the default
 * attributes of the navigation posts link. In this specific example, the function is modifying the
 * class attribute to add the classes "btn" and "btn
 *
 * @return string containing HTML attributes for a link element. Specifically, it is returning a
 * class attribute with the value "btn btn-primary load-more-auctions".
 */
function wpr_nav_posts_link_attributes( $val ) {
	return 'class="btn btn-primary load-more-auctions"';
}
add_filter( 'next_posts_link_attributes', 'wpr_nav_posts_link_attributes', 10 );
add_filter( 'previous_posts_link_attributes', 'wpr_nav_posts_link_attributes', 10 );
