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
	function wpr_sanitaiz_queried_array( array $array_sanitaz, array $allowed_params ) {

		$array_sanitaz = wpr_has_user_id( $array_sanitaz, $array_sanitaz );

		foreach ( $array_sanitaz as $key => $value ) {

			$is_recursive = ( is_object( $value ) || is_array( $value ) );

			if ( ! in_array( $key, $allowed_params, true ) && ! $is_recursive ) {
				unset( $array_sanitaz[ $key ] );
			} elseif ( $is_recursive ) {
				$array_sanitaz[ $key ] = wpr_sanitaiz_queried_array( (array) $value, $allowed_params );
			}

			if ( empty( $array_sanitaz[ $key ] ) ) {
				unset( $array_sanitaz[ $key ] );
			}
		}

		return $array_sanitaz;
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
