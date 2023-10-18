<?php

if ( ! function_exists( 'wpr_async_js' ) ) {
	/**
	 * If the script tag contains the word "async" then return the tag as is, otherwise add the word
	 * "async" to the tag
	 *
	 * @param string tag The script tag.
	 *
	 * @return string str_replace() function is being used to replace the first occurrence of the string
	 * '<script' with the string '<script async'.
	 */
	function wpr_async_js( string $tag ) {
		return str_replace( '<script', '<script async', $tag );
	}
}


if ( ! function_exists( 'wpr_has_user_id' ) ) {
	/**
	 * If the array has an ID key and a user_login or user_nicename key, then set the user_id key to the
	 * value of the ID key
	 *
	 * @param array user The user array.
	 * @param array callback The callback function to be called when the action is triggered.
	 *
	 * @return array callback array with the user_id key added.
	 */
	function wpr_has_user_id( array $user, array $callback = array() ) {

		$arr_keys = array_keys( $user );

		if (
		in_array( 'ID', $arr_keys, true ) &&
		( in_array( 'user_login', $arr_keys, true ) || in_array( 'user_nicename', $arr_keys, true ) ) &&
		isset( $user['ID'] )
		) {
			$callback['user_id'] = $user['ID'];
		}

		return $callback;
	}
}


if ( ! function_exists( 'wpr_sanitaiz_queried_array' ) ) {
	/**
	 * It takes an array, and removes any keys that aren't in the second array
	 *
	 * @param array The array to sanitize.
	 * @param array allowed_params An array of allowed parameters.
	 *
	 * @return array that is passed to it.
	 */
	function wpr_sanitaiz_queried_array( array $array, array $allowed_params ) {

		$array = wpr_has_user_id( $array, $array );

		foreach ( $array as $key => $value ) {

			$is_recursive = ( is_object( $value ) || is_array( $value ) );

			if ( ! in_array( $key, $allowed_params, true ) && ! $is_recursive ) {
				unset( $array[ $key ] );
			} elseif ( $is_recursive ) {
				$array[ $key ] = wpr_sanitaiz_queried_array( (array) $value, $allowed_params );
			}

			if ( empty( $array[ $key ] ) ) {
				unset( $array[ $key ] );
			}
		}

		return $array;
	}
}


if ( ! function_exists( 'wpr_front_information_queried_object' ) ) {
	/**
	 * It returns an array of information about the current page, including the page type, the page ID, and
	 * the page title
	 */
	function wpr_front_information_queried_object() {

		$queried = (array) get_queried_object();

		$allowed_params = array(
			'taxonomy',
			'slug',
			'term_id',
			'name',
			'data',
			'user_id',
		);

		return wpr_sanitaiz_queried_array( $queried, $allowed_params );
	}
}


if ( ! function_exists( 'wpr_current_user_in_roles_list' ) ) {
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
	function wpr_current_user_in_roles_list( array $roles ) {

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			if ( ! empty( array_intersect( $user->roles, $roles ) ) ) {
				return true;
			}
		}

		return false;
	}
}


if ( ! function_exists( 'redirect_home_on_wp_admin' ) ) {
	/**
	 * This function redirects users to the home page if they try to access the WordPress admin area.
	 */
	function redirect_home_on_wp_admin() {
		if ( str_contains( $_SERVER['REQUEST_URI'], 'wp-admin' ) ) {
			wp_redirect( get_site_url() );
			exit;
		}
	}
}


if ( ! function_exists( 'redirect_auth_user' ) ) {
	/**
	 * This function redirects a logged-in user to the home page of the website.
	 */
	function redirect_auth_user( string $page ) {
		if ( is_user_logged_in() ) {
			wp_redirect( $page );
			exit;
		}
	}
}


if ( ! function_exists( 'redirect_not_logged_user' ) ) {
	/**
	 * This function redirects non-logged-in users to the home page.
	 */
	function redirect_not_logged_user( string $page ) {
		if ( ! is_user_logged_in() ) {
			wp_redirect( $page );
			exit;
		}
	}
}


if ( ! function_exists( 'redirect_not_author' ) ) {
	/**
	 * The function redirects the user to the homepage or the post page if they are not the author of the
	 * specified post.
	 *
	 * @param int post_id The post ID is an integer value that represents a specific post in WordPress. It
	 * is used to identify and retrieve information about a particular post, such as its title, content,
	 * author, and other metadata. In this function, the post ID is used to check if the current user is
	 * the author of
	 */
	function redirect_not_author( int $post_id ) {
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


if ( ! function_exists( 'redirect_unapproved_user' ) ) {
	/**
	 * This function redirects unapproved users to a specified page.
	 *
	 * @param string page The page parameter is a string that represents the URL of the page where the
	 * unapproved user will be redirected to.
	 * @param WP_User curent_user The  parameter is an optional parameter of type WP_User that
	 * allows the function to accept a specific user object as input. If this parameter is not provided,
	 * the function will use the currently logged-in user's object obtained through the
	 * wp_get_current_user() function.
	 */
	function redirect_unapproved_user( string $page, WP_User $curent_user = null ) {

		$user = ! empty( $curent_user ) ? $curent_user : wp_get_current_user();

		if (
			! is_user_logged_in() ||
			(
				! in_array( 'approved', $user->roles, true ) &&
				! in_array( 'administrator', $user->roles, true )
			)
		) {
			wp_redirect( $page );
			exit;
		}
	}
}


if ( ! function_exists( 'get_auctions_categories' ) ) {
	/**
	 * This function retrieves all categories for auctions in WordPress, with an option to hide empty
	 * categories.
	 *
	 * @param bool hide_empty This parameter is used to determine whether to include empty categories in the
	 * result or not. If set to true, only categories with at least one auction item will be returned. If
	 * set to false, all categories, including empty ones, will be returned.
	 *
	 * @return array of terms from the 'auction_category' taxonomy. The terms may be empty or not
	 * depending on the value of the  parameter. If the  array is not empty, it is
	 * returned. Otherwise, an empty array is returned.
	 */
	function get_auctions_categories( $hide_empty = false ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'auction_category',
				'hide_empty' => $hide_empty,
			)
		);

		return is_array( $terms ) ? $terms : array();
	}
}


if ( ! function_exists( 'user_filter_info' ) ) {
	/**
	 * The function filters user information based on a given array of user IDs and returns an array of
	 * filtered user data.
	 *
	 * @param array users_ids An array of user IDs to filter and retrieve information for.
	 *
	 * @return array of user data filtered by the user IDs provided as an argument. The returned array
	 * contains user data for each user, with certain fields removed (ID, user_activation_key,
	 * user_login, user_pass, user_registered, user_status).
	 */
	function user_filter_info( array $users_ids ) {

		$users = array_column(
			get_users(
				array( 'include' => $users_ids )
			),
			'data'
		);

		$id_array = array_column( $users, 'ID' );

		return array_map(
			function( $utente ) {
				unset( $utente->ID, $utente->user_activation_key, $utente->user_login, $utente->user_pass, $utente->user_registered, $utente->user_status );
				return (array) $utente;
			},
			array_combine( $id_array, $users )
		);
	}
}


if ( ! function_exists( 'bids_users_ids_to_users' ) ) {
	/**
	 * The function maps user IDs in an array of bids to their corresponding user information.
	 *
	 * @param array bids An array of bids, where each bid is an associative array containing information
	 * about a bid, including the user ID of the bidder.
	 *
	 * @return array of bids with the corresponding user information for each bid. The user
	 * information is obtained by filtering the unique user IDs from the bids array and then mapping each
	 * user ID to its corresponding user information. Finally, the user information is added to each bid
	 * as a 'user' key and the 'user_id' key is removed.
	 */
	function bids_users_ids_to_users( array $bids ) {

		$users = user_filter_info(
			array_unique(
				array_column( $bids, 'user_id' )
			)
		);

		foreach ( $bids as $key => $bid ) {
			$bids[ $key ]['user'] = $users[ $bid['user_id'] ];
			unset( $bids[ $key ]['user_id'] );
		}

		return $bids;
	}
}
