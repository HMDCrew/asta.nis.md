<?php

if ( ! function_exists( 'sanitaiz_extension' ) ) {
	/**
	 * The function removes certain file extensions from a given filename string.
	 *
	 * @param string filename The parameter "filename" is a string variable that represents the name of a
	 * file with its extension.
	 *
	 * @return string sanitized filename with all the extensions in the `` array removed.
	 */
	function sanitaiz_extension( string $filename ) {

		$galeta_ext = array(
			'.php',
			'.py',
			'.sh',
			'.exe',
			'.rb',
			'.dll',
			'.js',
			'.css',
			'.jsp',
			'.jsx',
			'.html',
			'.scss',
			'<?',
			'?>',
			'function',
			'(',
			')',
			'script',
			'<',
			'>',
			'[',
			']',
		);

		foreach ( $galeta_ext as $ext ) {
			$filename = str_replace( $ext, '', $filename );
		}

		return $filename;
	}
}


if ( ! function_exists( 'get_user_last_edited_post' ) ) {
	/**
	 * The function `get_user_last_edited_post` retrieves the ID of the most recent draft post of a
	 * specific post type that was last edited by a given user.
	 *
	 * @param int user_id The user ID of the user whose last edited post you want to retrieve.
	 * @param string post_type The post type of the posts you want to retrieve. By default, it is set to
	 * 'auctions'.
	 *
	 * @return int|false ID of the last edited post by the specified user, with the specified post type (default
	 * is 'auctions'). If there is no post found, it will return false.
	 */
	function get_user_last_edited_post( int $user_id, string $post_type = 'auctions' ) {

		$posts = get_posts(
			array(
				'fields'      => 'ids',
				'numberposts' => 1,
				'post_status' => 'draft',
				'author'      => $user_id,
				'post_type'   => $post_type,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		if ( ! empty( $posts ) ) {
			return (int) reset( $posts );
		}

		return false;
	}
}


if ( ! function_exists( 'file_destination_helper' ) ) {
	/**
	 * The function generates a new file name with a unique activation code and returns the file location
	 * and URL for uploading to a specified path in WordPress.
	 *
	 * @param string filename The name of the file that needs to be uploaded.
	 * @param string upload_path The directory path within the WordPress uploads directory where the file
	 * will be stored. The default value is 'profiles'.
	 *
	 * @return array containing the file location, new URL, file extension, and base directory.
	 */
	function file_destination_helper( string $filename, string $upload_path = 'profiles' ) {

		$upload = wp_upload_dir();

		$file_extension = pathinfo( $filename, PATHINFO_EXTENSION );
		$file_extension = strtolower( $file_extension );

		$new_image_name = ASTA_USER::generate_activation_code( 25 ) . '.' . $file_extension;

		$bazedir = str_replace( '//', '/', sprintf( '%s/%s/', $upload['basedir'], $upload_path ) );
		mmkdir( $bazedir );

		$location = $bazedir . $new_image_name;

		$new_url = sprintf(
			'%s/%s/%s',
			$upload['baseurl'],
			$upload_path,
			$new_image_name,
		);

		if ( file_exists( $location ) ) {
			return file_destination_helper( $filename );
		}

		return array(
			'location'       => $location,
			'new_url'        => $new_url,
			'extension'      => $file_extension,
			'basedir'        => $bazedir,
			'new_image_name' => $new_image_name,
		);
	}
}


if ( ! function_exists( 'mmkdir' ) ) {
	/**
	 * The function creates a new directory if it does not already exist.
	 *
	 * @param string structure The parameter "structure" is a string that represents the directory
	 * structure that needs to be created. It is the name and path of the directory that needs to be
	 * created.
	 *
	 * @return bool `true` if the directory was successfully created or `false` if the directory already
	 * exists.
	 */
	function mmkdir( string $structure ) {
		if ( ! file_exists( $structure ) ) {
			return mkdir( $structure, 0755, true );
		}

		return false;
	}
}


if ( ! function_exists( 'upload_widouth_exif' ) ) {
	/**
	 * The function removes Exif, XMP, ICC, and Photoshop metadata from an image file.
	 *
	 * @param string in The input file path (including the file name and extension) that needs to be processed.
	 * @param string out The "out" parameter is the path and filename of the output file where the modified image
	 * without EXIF data will be saved.
	 */
	function upload_widouth_exif( string $in, string $out ) {
		$buffer_len = 4096;
		$fd_in      = fopen( $in, 'rb' );
		$fd_out     = fopen( $out, 'wb' );
		$buffer     = true;
		while ( $buffer ) {
			$buffer = fread( $fd_in, $buffer_len );
			//  \xFF\xE1\xHH\xLLExif\x00\x00 - Exif
			//  \xFF\xE1\xHH\xLLhttp://      - XMP
			//  \xFF\xE2\xHH\xLLICC_PROFILE  - ICC
			//  \xFF\xED\xHH\xLLPhotoshop    - PH
			while ( preg_match( '/\xFF[\xE1\xE2\xED\xEE](.)(.)(exif|photoshop|http:|icc_profile|adobe)/si', $buffer, $match, PREG_OFFSET_CAPTURE ) ) {

				$len         = ord( $match[1][0] ) * 256 + ord( $match[2][0] );
				$fwrite_meta = fwrite( $fd_out, substr( $buffer, 0, $match[0][1] ) );

				if ( false === $fwrite_meta ) {
					return $fwrite_meta;
				}

				$filepos = $match[0][1] + 2 + $len - strlen( $buffer );
				fseek( $fd_in, $filepos, SEEK_CUR );
				$buffer = fread( $fd_in, $buffer_len );
			}
			$fwrite = fwrite( $fd_out, $buffer, strlen( $buffer ) );

			if ( false === $fwrite ) {
				return $fwrite;
			}
		}
		fclose( $fd_out );
		fclose( $fd_in );

		return true;
	}
}


if ( ! function_exists( 'adjust_image_size' ) ) {
	/**
	 * This function adjusts the size of an image using the WordPress image editor.
	 *
	 * @param string path The path to the image file that needs to be adjusted in size.
	 * @param int|null width The desired width of the image after resizing. If set to null, the width will not be
	 * adjusted.
	 * @param int|null height The height parameter is used to specify the desired height of the image after
	 * resizing. If this parameter is not provided or is set to null, the image will be resized
	 * proportionally based on the provided width parameter.
	 *
	 * @return array|bool the saved image file path or false if there is an error.
	 */
	function adjust_image_size( string $path, $width = null, $height = null ) {

		$editor = wp_get_image_editor( $path );

		if ( ! is_wp_error( $editor ) && ! is_wp_error( $editor->resize( $width, $height ) ) ) {
			return $editor->save( $path );
		}

		return false;
	}
}


if ( ! function_exists( 'get_asta_gallery' ) ) {
	/**
	 * The function "get_asta_gallery" retrieves the value of the "asta_gallery" custom field for a given
	 * post ID in PHP.
	 *
	 * @param int post_id The post ID is the unique identifier for a specific post in WordPress. It is
	 * used to retrieve information about a specific post, such as its title, content, and custom fields.
	 * In this case, the `get_asta_gallery` function is using the post ID to retrieve the value of a
	 * custom
	 *
	 * @return array.
	 */
	function get_asta_gallery( int $post_id ) {

		if ( $post_id && 0 !== $post_id ) {
			$gallery = get_post_meta( $post_id, 'asta_gallery', true );
			return ! empty( $gallery ) ? $gallery : array();
		}

		return array();
	}
}

if ( ! function_exists( 'get_asta_thumbanil' ) ) {

	/**
	 * The function "get_asta_thumbnail" returns the first image thumbnail from a gallery associated with
	 * a given post ID, or a placeholder image if no gallery is found.
	 *
	 * @param int post_id The post_id parameter is an integer that represents the ID of the post for which
	 * you want to retrieve the thumbnail.
	 *
	 * @return string first thumbnail image from the gallery associated with the given post ID. If the
	 * gallery is not empty, the function returns the URL of the first thumbnail image. If the gallery is
	 * empty, the function returns a placeholder image URL.
	 */
	function get_asta_thumbanil( int $post_id ) {

		$gallery = get_asta_gallery( $post_id );

		return (
			! empty( $gallery )
			? reset( $gallery )
			: 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg'
		);
	}
}


if ( ! function_exists( 'remove_multimple_slah' ) ) {
	/**
	 * The function removes multiple forward slashes from a given string.
	 *
	 * @param string url The parameter "url" is a string variable that represents a URL.
	 *
	 * @return string modified version of the input string , where any consecutive occurrences of forward
	 * slashes (//) are replaced with a single forward slash (/).
	 */
	function remove_multimple_slah( string $url ) {
		return preg_replace( '/([^:])(\/{2,})/', '$1/', $url );
	}
}


if ( ! function_exists( 'asta_plugin_get_template_part' ) ) {

	/**
	 * The function `asta_get_template_part` is used to include template files in a WordPress plugin, with
	 * the ability to pass arguments to the template.
	 *
	 * @param string plugin_template_path The `plugin_template_path` parameter is a string that represents
	 * the path to the plugin template directory. This is the directory where the template files are
	 * located.
	 * @param string slug The "slug" parameter is a string that represents the name or identifier of the
	 * template file you want to include. It is typically used to specify the specific template file
	 * within a theme or plugin.
	 * @param string name The "name" parameter is an optional parameter that allows you to specify a
	 * specific template file to be used. If provided, it will be appended to the slug with a hyphen ("-")
	 * and used as the template file name.
	 * @param array args The "args" parameter is an optional array that allows you to pass additional data
	 * to the template file. This data can be accessed within the template using the "extract" function.
	 */
	function asta_plugin_get_template_part( string $plugin_template_path, string $slug, string $name = null, array $args = array() ) {

		do_action( "asta_plugin_get_template_part_{$slug}", $slug, $name );

		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		asta_plugin_get_template_path( $plugin_template_path, $templates, $args, true, false );
	}
}


if ( ! function_exists( 'asta_plugin_get_template_path' ) ) {
	/**
	 * The function `asta_plugin_get_template_path` searches for a template file within a specified plugin
	 * directory and loads it if specified.
	 *
	 * @param string plugin_template_path The parameter `plugin_template_path` is a string that represents
	 * the path to the directory where the plugin templates are located.
	 * @param array template_names An array of template names to search for within the plugin template
	 * path.
	 * @param array args The `` parameter is an array that contains any additional arguments that you
	 * want to pass to the template file. These arguments can be accessed within the template file using
	 * the `` variable.
	 * @param bool load A boolean value indicating whether to load the template file or not. If set to
	 * true, the template file will be loaded using the `load_template()` function.
	 * @param bool require_once A boolean value indicating whether the template file should be required
	 * once or not. If set to true, the template file will only be included once, preventing multiple
	 * inclusions.
	 *
	 * @return string path of the located template file.
	 */
	function asta_plugin_get_template_path( string $plugin_template_path, array $template_names, array $args, bool $load = false, bool $is_require_once = true ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}

			/* search file within the PLUGIN_DIR_PATH only */
			if ( file_exists( $plugin_template_path . $template_name ) ) {
				$located = $plugin_template_path . $template_name;
				break;
			}
		}

		if ( $load && '' !== $located ) {
			load_template( $located, $is_require_once, $args );
		}

		return $located;
	}
}


if ( ! function_exists( 'asta_esc_meta' ) ) {
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
	function asta_esc_meta( int $auction_id, string $key ) {

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
	 * The function "get_asta_category" retrieves the first category associated with a given post ID in
	 * WordPress.
	 *
	 * @param int post_id The post ID is the unique identifier for a specific post in WordPress. It is
	 * used to retrieve information about a specific post, such as its title, content, and categories. In
	 * this function, the post ID is used to retrieve the category of a post with the taxonomy
	 * 'asta_category'.
	 *
	 * @return array with the keys 'id' and 'name'. The 'id' key contains the term ID of the first term
	 * in the 'asta_category' taxonomy associated with the given post ID, and the 'name' key contains the
	 * name of that term. If no terms are found or the post ID is invalid, an empty array is returned.
	 */
	function get_asta_category( int $post_id ) {

		if ( $post_id && 0 !== $post_id ) {

			$terms = get_the_terms( $post_id, 'asta_category' );

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

if ( ! function_exists( 'get_asta_categories' ) ) {
	/**
	 * The function "get_asta_categories" retrieves the terms from the "asta_category" taxonomy in
	 * WordPress, optionally hiding empty terms.
	 *
	 * @param bool hide_empty The "hide_empty" parameter is used to determine whether or not to include
	 * categories that have no posts assigned to them. If set to true, empty categories will be excluded
	 * from the results. If set to false, all categories will be included, regardless of whether they have
	 * posts assigned to them or not
	 *
	 * @return array of terms from the 'asta_category' taxonomy. If the  parameter is set to
	 * true, it will only return terms that have posts associated with them. If  is set to
	 * false, it will return all terms, regardless of whether they have posts associated with them or not.
	 */
	function get_asta_categories( bool $hide_empty = false ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'asta_category',
				'hide_empty' => $hide_empty,
			)
		);

		return is_array( $terms ) ? $terms : array();
	}
}


/**
 * The function "string_to_hide" takes a string as input and replaces each character with a specified
 * replacement character, returning the modified string.
 *
 * @param string content The "content" parameter is a string that represents the content that you want
 * to hide or replace with a specific character.
 * @param string replacer The replacer parameter is a string that will be used to replace each
 * character in the content string. By default, it is set to '*'.
 *
 * @return string consisting of the same number of characters as the input string, with each
 * character replaced by the specified replacer character (default is '*').
 */
function string_to_hide( string $content, string $replacer = '*' ) {
	return implode( ' ', preg_replace( '/(\w)/i', $replacer, explode( ' ', $content ) ) );
}


/**
 * The `partial_hider` function takes a string of letters and hides a portion of it based on the
 * specified start and end positions, replacing the hidden portion with a specified character.
 *
 * @param string letters The "letters" parameter is a string that represents the input letters that you
 * want to partially hide.
 * @param int show_start The number of characters from the start of the string that should be shown.
 * Default is 0, which means no characters will be shown from the start.
 * @param int show_end The `show_end` parameter determines how many characters from the end of the
 * string should be shown.
 * @param string replacer The replacer parameter is a string that will be used to replace the hidden
 * characters in the output string. By default, it is set to '*'.
 *
 * @return string modified version of the input string. The modified string has certain parts hidden or
 * replaced with asterisks based on the provided parameters.
 */
function partial_hider( string $letters, int $show_start = 0, int $show_end = 0, string $replacer = '●' ) {

	$letters     = str_split( $letters );
	$part_one    = array();
	$middle_part = array();
	$end_part    = array();

	if ( 0 !== $show_start ) {
		$part_one = implode( '', array_slice( $letters, 0, $show_start ) );
		$end_part = implode( '', array_slice( $letters, $show_start ) );
	}

	if ( 0 !== $show_start && 0 !== $show_end ) {
		$middle_part = implode( '', array_slice( $letters, $show_start, $show_end * -1 ) );
	}

	if ( 0 !== $show_end ) {
		$part_one = empty( $part_one ) ? implode( '', array_slice( $letters, 0, $show_end * -1 ) ) : $part_one;
		$end_part = implode( '', array_slice( $letters, $show_end * -1 ) );
	}

	return (
		0 !== $show_start && 0 !== $show_end
		? $part_one . string_to_hide( $middle_part, $replacer ) . $end_part
		: (
			0 !== $show_start
			? $part_one . string_to_hide( $end_part, $replacer )
			: (
				0 !== $show_end
				? string_to_hide( $part_one, $replacer ) . $end_part
				: string_to_hide(
					implode( '', $letters ),
					$replacer
				)
			)
		)
	);
}
