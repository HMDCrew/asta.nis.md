<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ASTA_USER' ) ) :

	class ASTA_USER {

		public static ?SEC $sec = null;

		public static function initialize() {
			self::$sec = self::$sec ?? new SEC();
		}


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


		/**
		 * This function checks if the current user has any of the specified roles.
		 *
		 * @param array roles An array of user roles to check against the current user. If the current user has
		 * any of the roles specified in this array, the function will return true.
		 *
		 * @return boolean value (true or false) depending on whether the current user has any of the
		 * specified roles in the input array. If the user is not logged in, the function will not return
		 * anything.
		 */
		public static function asta_current_user_in_roles_list( array $roles ) {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( ! empty( array_intersect( $user->roles, $roles ) ) ) {
					return true;
				}
			}

			return false;
		}


		/**
		 * The function checks if a user is logged in and has the role of "approved".
		 *
		 * @return boolean value. It returns true if the user is logged in and has the role of "approved",
		 * and false otherwise.
		 */
		public static function asta_user_is_aproved() {
			return self::asta_current_user_in_roles_list( array( 'approved', 'administrator' ) );
		}


		// /**
		//  * This function redirects unapproved users to a specified page.
		//  *
		//  * @param string page The page parameter is a string that represents the URL of the page where the
		//  * unapproved user will be redirected to.
		//  * @param WP_User curent_user The  parameter is an optional parameter of type WP_User that
		//  * allows the function to accept a specific user object as input. If this parameter is not provided,
		//  * the function will use the currently logged-in user's object obtained through the
		//  * wp_get_current_user() function.
		//  */
		// public function redirect_unapproved_user( string $page, WP_User $curent_user = null ) {

		//  $user = ! empty( $curent_user ) ? $curent_user : wp_get_current_user();

		//  if (
		//  ! is_user_logged_in() ||
		//  (
		//      ! in_array( 'approved', $user->roles, true ) &&
		//      ! in_array( 'administrator', $user->roles, true )
		//  )
		//  ) {
		//      wp_redirect( $page );
		//      exit;
		//  }
		// }


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
		public static function get_picture_profile( int $user_id ) {

			$profile_picture = get_user_meta( $user_id, 'profile-picture', true );

			return esc_url( $profile_picture ? $profile_picture : 'https://secure.gravatar.com/avatar/c6c727854f5d1de9a7a74abb38cf947d?s=96&d=mm&r=g' );
		}


		/**
		 * The function "get_user_customer_id" retrieves the customer ID associated with a given user ID.
		 *
		 * @param int user_id The user ID is an integer that represents the unique identifier of a user in
		 * the system. It is used to retrieve the customer ID associated with the user.
		 *
		 * @return string value of the 'asta_customer_id' user meta for the given user ID.
		 */
		public static function get_user_customer_id( int $user_id ) {
			return get_user_meta( $user_id, 'asta_customer_id', true );
		}
	}

endif;

ASTA_USER::initialize();
