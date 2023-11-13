<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_USER' ) ) :

	class ASTA_USER {


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
		public static function generate_activation_code( int $length = 16 ) {
			return preg_replace( '/[^a-zA-Z0-9\.\_\-]/i', '', bin2hex( random_bytes( $length ) ) );
		}


		/**
		 * This function redirects a logged-in user to the home page of the website.
		 */
		public static function redirect_auth_user( string $page ) {
			if ( is_user_logged_in() ) {
				wp_redirect( $page );
				exit;
			}
		}


		/**
		 * This function redirects non-logged-in users to the home page.
		 */
		public static function redirect_not_logged_user( string $page ) {
			if ( ! is_user_logged_in() ) {
				wp_redirect( $page );
				exit;
			}
		}


		/**
		 * The function redirects the user to the homepage or the post page if they are not the author of the
		 * specified post.
		 *
		 * @param int post_id The post ID is an integer value that represents a specific post in WordPress. It
		 * is used to identify and retrieve information about a particular post, such as its title, content,
		 * author, and other metadata. In this function, the post ID is used to check if the current user is
		 * the author of
		 */
		public static function redirect_not_author( int $post_id ) {
			if ( (int) get_post_field( 'post_author', $post_id ) !== get_current_user_id() ) {
				wp_redirect(
					0 !== $post_id && get_post_status( $post_id )
						? get_permalink( $post_id )
						: '/'
				);
				exit;
			}
		}
	}

endif;
