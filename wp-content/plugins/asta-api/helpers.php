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


if ( ! function_exists( 'get_user_last_edited_auction' ) ) {
	/**
	 * The function returns the ID of the most recently edited draft auction post by a given user.
	 *
	 * @param int user_id The ID of the user whose last edited auction is being retrieved.
	 *
	 * @return int|false ID of the most recently edited draft auction post by the specified user, or false if no
	 * such post exists.
	 */
	function get_user_last_edited_auction( int $user_id ) {

		$auctions = get_posts(
			array(
				'fields'      => 'ids',
				'numberposts' => 1,
				'post_status' => 'draft',
				'author'      => $user_id,
				'post_type'   => 'auctions',
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		if ( ! empty( $auctions ) ) {
			return (int) reset( $auctions );
		}

		return false;
	}
}


if ( ! function_exists( 'generate_activation_code' ) ) {
	/**
	 * The function generates a random activation code of a specified length using PHP's random_bytes and
	 * bin2hex functions.
	 *
	 * @param int length The "length" parameter is an optional integer value that specifies the length of
	 * the activation code to be generated. If no value is provided, the default length of 16 characters
	 * will be used.
	 *
	 * @return string randomly generated activation code in hexadecimal format with a default length of 16
	 * characters.
	 */
	function generate_activation_code( int $length = 16 ) {
		return preg_replace( '/[^a-zA-Z0-9\.\_\-]/i', '', bin2hex( random_bytes( $length ) ) );
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

		$new_image_name = generate_activation_code( 25 ) . '.' . $file_extension;

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
		return preg_replace( '/(\/+)/', '/', $url );
	}
}


if ( ! function_exists( 'wpr_asta_get_template_part' ) ) {
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
	function wpr_asta_get_template_part( string $slug, string $name = 'null', array $args = array() ) {

		do_action( "wpr_asta_get_template_part_{$slug}", $slug, $name );

		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		wpr_asta_get_template_path( $templates, $args, true, false );
	}
}


if ( ! function_exists( 'wpr_asta_get_template_path' ) ) {
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
	function wpr_asta_get_template_path( array $template_names, array $args, bool $load = false, bool $require_once = true ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}

			/* search file within the PLUGIN_DIR_PATH only */
			if ( file_exists( ASTA_API_PLUGIN_TEMPLATES . $template_name ) ) {
				$located = ASTA_API_PLUGIN_TEMPLATES . $template_name;
				break;
			}
		}

		if ( $load && '' !== $located ) {
			load_template( $located, $require_once, $args );
		}

		return $located;
	}
}
