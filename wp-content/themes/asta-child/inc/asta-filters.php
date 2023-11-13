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
	return (
		$tag
		? wp_get_attachment_image( $id, $size, false, $classes )
		: wp_get_attachment_image_url( $id, $size )
	);
}
add_filter( 'wpr_get_image_by_id', 'wpr_get_image_by_id', 3 );



/**
 * This PHP function adds a class attribute to a navigation link.
 *
 * @param string val  is a parameter that represents the default value of the attributes for the
 * navigation posts link. This parameter is used in a WordPress filter function to modify the default
 * attributes of the navigation posts link. In this specific example, the function is modifying the
 * class attribute to add the classes "btn" and "btn
 *
 * @return string containing HTML attributes for a link element. Specifically, it is returning a
 * class attribute with the value "btn btn-primary load-more".
 */
function wpr_nav_posts_link_attributes( $val ) {
	return 'class="btn btn-primary load-more"';
}
add_filter( 'next_posts_link_attributes', 'wpr_nav_posts_link_attributes', 10 );
add_filter( 'previous_posts_link_attributes', 'wpr_nav_posts_link_attributes', 10 );
