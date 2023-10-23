<?php

if ( ! function_exists( 'asta_get_template_part' ) ) {
	/**
	 * This function gets a template part in WordPress with optional name and arguments.
	 * Extracted from : http://wordpress.stackexchange.com/questions/94343/get-template-part-from-plugin
	 *
	 * @param string slug The slug is a required parameter that specifies the name of the template to be
	 * loaded. It is used to construct the file name of the template file.
	 * @param string name The name parameter is an optional parameter that can be used to specify a more
	 * specific template file to be loaded. If provided, the function will look for a template file with
	 * the format "slug-name.php". If not provided, the function will only look for a template file with
	 * the format "slug.php
	 * @param array args  is an optional array parameter that can be used to pass additional data to
	 * the template file being loaded. This data can be accessed within the template file using the
	 * variable.
	 */
	function asta_get_template_part( string $slug, string $name = 'null', array $args = array() ) {

		do_action( "asta_get_template_part_{$slug}", $slug, $name );

		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		asta_get_template_path( $templates, $args, true, false );
	}
}


if ( ! function_exists( 'asta_get_template_path' ) ) {
	/**
	 * This function searches for a template file within a specific directory and loads it if specified.
	 *
	 * @param array template_names An array of template names to search for.
	 * @param array args  is an optional parameter that is an array of arguments that can be passed
	 * to the template file being loaded. These arguments can be used to customize the output of the
	 * template. If the  parameter is not provided, an empty array will be used as the default value.
	 * @param bool load A boolean parameter that determines whether to load the template file or just
	 * return the path to the file.
	 * @param bool require_once The  parameter is a boolean value that determines whether the
	 * template file should be included using the PHP require_once() function or the require() function.
	 * If set to true, the file will only be included once, preventing any potential errors that may arise
	 * from multiple inclusions.
	 *
	 * @return string path of the located template file.
	 */
	function asta_get_template_path( array $template_names, array $args, bool $load = false, bool $require_once = true ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}

			/* search file within the PLUGIN_DIR_PATH only */
			if ( file_exists( ASTA_TEMPLATES_PLUGIN_TEMPLATES . $template_name ) ) {
				$located = ASTA_TEMPLATES_PLUGIN_TEMPLATES . $template_name;
				break;
			}
		}

		if ( $load && '' !== $located ) {
			load_template( $located, $require_once, $args );
		}

		return $located;
	}
}

if ( ! function_exists( 'get_asta_gallery' ) ) {
	/**
	 * The function retrieves the gallery of images associated with a given auction ID.
	 *
	 * @param int|false The ID of the auction post for which the gallery images are being retrieved.
	 *
	 * @return array of the gallery images associated with the auction post identified by the
	 *  parameter. If  is not provided or is falsy, an empty array is returned.
	 */
	function get_asta_gallery( $auction_id ) {

		if ( $auction_id ) {
			$gallery = get_post_meta( $auction_id, 'asta_gallery', true );
			return ! empty( $gallery ) ? $gallery : array();
		}

		return array();
	}
}

if ( ! function_exists( 'get_asta_thumbanil' ) ) {
	/**
	 * The function returns the first thumbnail image of an auction's gallery.
	 *
	 * @param int auction_id This is an integer variable that represents the ID of the auction for which we
	 * want to retrieve the thumbnail image.
	 *
	 * @return string first image thumbnail from the gallery of a given auction ID.
	 */
	function get_asta_thumbanil( int $auction_id ) {

		$gallery = get_asta_gallery( $auction_id );

		return (
			! empty( $gallery )
			? reset( $gallery )
			: 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg'
		);
	}
}


if ( ! function_exists( 'single_url_slash' ) ) {
	/**
	 * The function `single_url_slash` takes a string parameter `url` and replaces multiple consecutive
	 * slashes with a single slash.
	 *
	 * @param string url The parameter "url" is a string that represents a URL.
	 *
	 * @return string modified version of the input URL string.
	 */
	function single_url_slash( string $url ) {
		return preg_replace( '/([^:])(\/{2,})/', '$1/', $url );
	}
}


if ( ! function_exists( 'esc_auction_meta' ) ) {
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
}


if ( ! function_exists( 'get_asta_category' ) ) {
	/**
	 * This PHP function retrieves the auction type (category) based on the provided auction ID.
	 *
	 * @param int auction_id This is an integer parameter representing the ID of the auction for which we
	 * want to retrieve the auction type.
	 *
	 * @return array with the ID and name of the first category term associated with the given auction
	 * ID. If the auction ID is not provided or no category term is found, an empty array is returned.
	 */
	function get_asta_category( $auction_id ) {

		if ( $auction_id ) {

			$terms = get_the_terms( $auction_id, 'asta_category' );

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
}
