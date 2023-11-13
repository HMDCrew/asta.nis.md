<?php

class ASTA_AUCTION {


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
	public static function user_filter_info( array $users_ids ) {

		$users = array_column(
			get_users(
				array( 'include' => $users_ids )
			),
			'data'
		);

		$id_array = array_column( $users, 'ID' );

		return array_map(
			function ( $utente ) {
				unset( $utente->ID, $utente->user_activation_key, $utente->user_login, $utente->user_pass, $utente->user_registered, $utente->user_status );
				return (array) $utente;
			},
			array_combine( $id_array, $users )
		);
	}


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
	public static function bids_users_ids_to_users( array $bids ) {

		$users = self::user_filter_info(
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
	public static function get_auction_bids( $auction_bids ) {

		if ( ! empty( $auction_bids ) && $auction_bids ) {

			return self::bids_users_ids_to_users(
				count( $auction_bids ) > 10
				? array_slice( $auction_bids, -10, 10, true )
				: $auction_bids
			);
		}

		return array();
	}


	/**
	 * The function retrieves the last price of an auction as a float value.
	 *
	 * @param int auction_id The ID of the auction post for which we want to retrieve the last price.
	 *
	 * @return float last price of an auction as a float value. It retrieves the value from the
	 * 'auction_price' meta field of the post with the given .
	 */
	public static function get_auction_last_price( int $auction_id ) {
		return floatval( get_post_meta( $auction_id, 'auction_price', true ) );
	}


	/**
	 * The function retrieves the start and end dates of an auction and returns them in a formatted string.
	 *
	 * @param int auction_id The ID of the auction post for which the start and end dates are being retrieved.
	 *
	 * @return string formatted string that includes the start and end dates of an auction, based on the
	 * provided auction ID. If no auction ID is provided, an empty string is returned.
	 */
	public static function get_auction_date( int $auction_id ) {

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
}
