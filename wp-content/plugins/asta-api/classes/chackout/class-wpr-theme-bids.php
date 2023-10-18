<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPR_THEME_BIDS' ) ) :
	class WPR_THEME_BIDS {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPR_THEME_BIDS ) ) {
				self::$instance = new WPR_THEME_BIDS;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'wpr_rest_api' ), 10 );
		}


		/**
		 * Registering a route for the REST API.
		 * @param [type] $server
		 */
		public function wpr_rest_api( $server ) {

			$server->register_route(
				'rest-api-wordpress',
				'/api-auction-new-bid',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_auction_new_bid' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			$server->register_route(
				'rest-api-wordpress',
				'/api-auction-bids',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_auction_bids' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * This function adds a new bid to an auction and updates the auction bids meta data.
		 *
		 * @param int auction_id The ID of the auction post for which a new bid is being added.
		 * @param int user_id The ID of the user who is placing the bid.
		 *
		 * @return array updated array of auction bids after adding a new bid with the user ID, price
		 * increment, and current price.
		 */
		private function add_new_bid( int $auction_id, int $user_id, DateTime $date ) {

			$price_increment = apply_filters( 'wpr_esc_auction_meta', $auction_id, 'price_increment' );
			$auction_bids    = get_post_meta( $auction_id, 'auction_bids', true );
			$status          = 'success';
			$message         = __( 'bid added with success', 'asta-api' );

			$new_element = array(
				'user_id'         => $user_id,
				'price_increment' => $price_increment,
				'date'            => $date->format( 'r' ),
			);

			if ( ! empty( $auction_bids ) && $auction_bids ) {

				$last_element = end( $auction_bids );

				if ( $last_element['user_id'] !== $user_id ) {

					$last_price               = floatval( $last_element['now_price'] ) + floatval( $price_increment );
					$new_element['now_price'] = $last_price;
					$auction_bids[]           = $new_element;

					update_post_meta( $auction_id, 'auction_price', $last_price );
					update_post_meta( $auction_id, 'auction_bids', $auction_bids );
				} else {
					$status  = 'worring';
					$message = __( 'you don\'t need to add another bid', 'asta-api' );
				}
			} else {

				$auction_bids = array();
				$baze_price   = apply_filters( 'wpr_esc_auction_meta', $auction_id, 'baze_price' );
				$price        = ! empty( $baze_price ) && $baze_price ? $baze_price : 0;

				$last_price               = floatval( $price ) + floatval( $price_increment );
				$new_element['now_price'] = $last_price;
				$auction_bids[]           = $new_element;

				update_post_meta( $auction_id, 'auction_price', $last_price );
				update_post_meta( $auction_id, 'auction_bids', $auction_bids );
			}

			return array(
				'status'  => $status,
				'message' => $message,
				'bids'    => $auction_bids,
			);
		}


		/**
		 * This PHP function handles a REST API request to add a new bid to an auction and returns a JSON
		 * response with the status, message, and updated bids.
		 *
		 * @param \WP_REST_Request request An object of the WP_REST_Request class, which is used to handle
		 * REST API requests in WordPress.
		 */
		public function wpr_auction_new_bid( \WP_REST_Request $request ) {

			$attr       = $request->get_attributes();
			$params     = $request->get_params();
			$auction_id = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );

			if ( ! empty( $auction_id ) && 0 !== $attr['login_user_id'] ) {

				$date       = new DateTime( 'now' );
				$start_date = new DateTimeImmutable( get_post_meta( $auction_id, 'start_date', true ) );
				$end_date   = new DateTimeImmutable( get_post_meta( $auction_id, 'end_date', true ) );

				$bids = array(
					'status'  => 'worring',
					'message' => 'empty',
					'bids'    => array(),
				);

				if ( $start_date < $date && $end_date > $date ) {
					$bids = $this->add_new_bid( $auction_id, $attr['login_user_id'], $date );
				} else {
					$bids['message'] = __( 'Auction isn\'t start', 'asta-api' );
				}

				wp_send_json(
					array(
						'status'  => $bids['status'],
						'message' => $bids['message'],
						'bids'    => apply_filters( 'wpr_get_auction_bids', $bids['bids'] ),
					),
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Please log-in before proceeding', 'asta-api' ),
				),
			);
		}


		/**
		 * This PHP function retrieves the last 10 bids for a given auction ID and returns them in a JSON
		 * response, but only if the user is authenticated and the auction ID is provided.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which represents a REST API request made to the WordPress site.
		 */
		public function wpr_auction_bids( \WP_REST_Request $request ) {

			$attr       = $request->get_attributes();
			$params     = $request->get_params();
			$auction_id = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );

			if ( ! empty( $auction_id ) && 0 !== $attr['login_user_id'] ) {

				wp_send_json(
					array(
						'status'      => 'success',
						'message'     => __( 'list bids', 'asta-api' ),
						'bids'        => apply_filters( 'wpr_get_auction_bids', get_post_meta( $auction_id, 'auction_bids', true ) ),
						'invok_nexts' => true,
					),
				);
			}

			wp_send_json(
				array(
					'status'      => 'worring',
					'message'     => __( 'Please log-in for autorefresh', 'asta-api' ),
					'invok_nexts' => false,
				),
			);
		}
	}
endif;

WPR_THEME_BIDS::instance();
