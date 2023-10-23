<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_SOLD_PROCESS' ) ) :
	class ASTA_THEME_SOLD_PROCESS {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_SOLD_PROCESS ) ) {
				self::$instance = new ASTA_THEME_SOLD_PROCESS;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'chack_auction_status_for_cart', array( $this, 'auction_status_for_cart' ), 10, 1 );
		}


		/**
		 * The function updates the auction status to "sold" and adds the auction ID to the user's cart in
		 * the user meta.
		 *
		 * @param int auction_id The ID of the auction that has been sold.
		 * @param array winner_bid An array containing information about the winning bid, including the user
		 * ID of the bidder and the bid amount.
		 *
		 * @return array the updated user cart array.
		 */
		private function sold_process( int $auction_id, array $winner_bid ) {

			$winner_bid['auction_id'] = $auction_id;

			// move auction status to sell
			wp_update_post(
				array(
					'ID'          => $auction_id,
					'post_status' => 'sold',
				)
			);

			// add auction id to user_meta for cart
			$cart = get_user_meta( $winner_bid['user_id'], 'user_cart', true );

			if ( ! empty( $cart ) ) {
				$cart[ $auction_id ] = $winner_bid;
			} else {
				$cart = array( $auction_id => $winner_bid );
			}

			update_user_meta( $winner_bid['user_id'], 'user_cart', $cart );

			return $cart;
		}


		/**
		 * The function retrieves the highest bid for a given auction and processes it as sold.
		 *
		 * @param int auction The parameter `` is an array that contains information about an auction,
		 * including the auction ID.
		 */
		public function auction_status_for_cart( int $auction_id ) {

			$bids = get_post_meta( $auction_id, 'auction_bids', true );

			if ( ! empty( $bids ) ) {

				$bids_prices = array_column( $bids, 'now_price' );
				$price       = max( $bids_prices );
				$key         = array_search( $price, $bids_prices, true );

				$this->sold_process( $auction_id, $bids[ $key ] );

			} else {
				error_log(
					print_r(
						array(
							'auction_id' => $auction_id,
							'is_empty'   => empty( $bids ) ? 'empty' : $bids,
							'date'       => new DateTimeImmutable(),
						),
						true
					)
				);
			}
		}
	}

endif;

ASTA_THEME_SOLD_PROCESS::instance();
