<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\Webhook;

if ( ! class_exists( 'ASTA_THEME_SOLD_PROCESS' ) ) :
	class ASTA_THEME_SOLD_PROCESS {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_SOLD_PROCESS ) ) {
				self::$instance = new ASTA_THEME_SOLD_PROCESS();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'chack_auction_status_for_cart', array( $this, 'auction_status_for_cart' ), 10, 1 );
			add_action( 'rest_api_init', array( $this, 'asta_rest_api' ), 10 );
		}


		/**
		 * Registering a route for the REST API.
		 *
		 * @param \WP_REST_Server $server
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {
			$server->register_route(
				'rest-api-wordpress',
				'/api-cart-webhook',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_cart_webhook' ),
					'login_user_id' => get_current_user_id(),
				)
			);
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


		/**
		 * The function updates the balance and looked balance of sellers based on the products and auctions
		 * in the cart.
		 *
		 * @param int order_id The order ID is an integer that represents the unique identifier of the order.
		 * It is used to identify a specific order in the system.
		 * @param array cart The `` parameter is an array that contains two sub-arrays: `products_cart`
		 * and `auctions_cart`.
		 */
		private function look_modey_sellers_order( int $order_id, array $cart ) {

			ASTA_THEME_ORDERS::set_order_status( $order_id, 'review' );

			foreach ( $cart['products_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['product_id'] );
				$author_balance        = ASTA_USER::get_user_balance( $author_id );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );
				$price                 = (float) get_post_meta( $cart_item['product_id'], 'price', true );

				ASTA_USER::update_user_balance(
					$author_id,
					(
						$author_balance - (
							$price * (int) $cart_item['qty']
						)
					)
				);

				ASTA_USER::update_user_looked_balance(
					$author_id,
					(
						$looked_author_balance + (
							$price * (int) $cart_item['qty']
						)
					)
				);
			}

			foreach ( $cart['auctions_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['auction_id'] );
				$author_balance        = ASTA_USER::get_user_balance( $author_id );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );

				ASTA_USER::update_user_balance( $author_id, ( $author_balance - (float) $cart_item['now_price'] ) );
				ASTA_USER::update_user_looked_balance( $author_id, ( $looked_author_balance + (float) $cart_item['now_price'] ) );
			}
		}


		/**
		 * The function unlocks the modey sellers order by updating the order status and updating the
		 * balances of the authors for each product and auction in the cart.
		 *
		 * @param int order_id The order_id parameter is an integer that represents the ID of the order that
		 * needs to be unlocked.
		 * @param array cart The `` parameter is an array that contains two sub-arrays: `products_cart`
		 * and `auctions_cart`.
		 */
		private function unlock_modey_sellers_order( int $order_id, array $cart ) {

			ASTA_THEME_ORDERS::set_order_status( $order_id, 'paid' );

			foreach ( $cart['products_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['product_id'] );
				$author_balance        = ASTA_USER::get_user_balance( $author_id );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );
				$price                 = (float) get_post_meta( $cart_item['product_id'], 'price', true );

				ASTA_USER::update_user_balance(
					$author_id,
					(
						$author_balance + (
							$price * (int) $cart_item['qty']
						)
					)
				);
				ASTA_USER::update_user_looked_balance(
					$author_id,
					(
						$looked_author_balance - (
							$price * (int) $cart_item['qty']
						)
					)
				);
			}

			foreach ( $cart['auctions_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['auction_id'] );
				$author_balance        = ASTA_USER::get_user_balance( $author_id );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );

				ASTA_USER::update_user_balance( $author_id, ( $author_balance + (float) $cart_item['now_price'] ) );
				ASTA_USER::update_user_looked_balance( $author_id, ( $looked_author_balance - (float) $cart_item['now_price'] ) );
			}
		}


		private function lost_looked_modey_sellers_order( int $order_id, array $cart ) {

			ASTA_THEME_ORDERS::set_order_status( $order_id, 'unpaid' );

			foreach ( $cart['products_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['product_id'] );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );
				$price                 = (float) get_post_meta( $cart_item['product_id'], 'price', true );

				$qty = (int) get_post_meta( $cart_item['product_id'], 'qty', true );
				update_post_meta( $cart_item['product_id'], 'qty', ( $qty + (int) $cart_item['qty'] ) );

				ASTA_USER::update_user_looked_balance(
					$author_id,
					(
						$looked_author_balance - (
							$price * (int) $cart_item['qty']
						)
					)
				);
			}

			foreach ( $cart['auctions_cart'] as $cart_item ) {

				$author_id             = get_post_field( 'post_author', $cart_item['auction_id'] );
				$looked_author_balance = ASTA_USER::get_user_looked_balance( $author_id );

				wp_update_post(
					array(
						'ID'          => $cart_item['auction_id'],
						'post_status' => 'publish',
					)
				);

				ASTA_USER::update_user_looked_balance( $author_id, ( $looked_author_balance - (float) $cart_item['now_price'] ) );
			}
		}


		private function payment_failed( int $order_id, array $cart ) {

			ASTA_THEME_ORDERS::set_order_status( $order_id, 'unpaid' );

			foreach ( $cart['products_cart'] as $cart_item ) {

				$author_id      = get_post_field( 'post_author', $cart_item['product_id'] );
				$author_balance = ASTA_USER::get_user_balance( $author_id );
				$qty            = (int) get_post_meta( $cart_item['product_id'], 'qty', true );
				$price          = (float) get_post_meta( $cart_item['product_id'], 'price', true );

				update_post_meta( $cart_item['product_id'], 'qty', ( $qty + (int) $cart_item['qty'] ) );

				ASTA_USER::update_user_balance(
					$author_id,
					(
						$author_balance - (
							$price * (int) $cart_item['qty']
						)
					)
				);
			}

			foreach ( $cart['auctions_cart'] as $cart_item ) {

				$author_id      = get_post_field( 'post_author', $cart_item['auction_id'] );
				$author_balance = ASTA_USER::get_user_balance( $author_id );

				wp_update_post(
					array(
						'ID'          => $cart_item['auction_id'],
						'post_status' => 'publish',
					)
				);

				ASTA_USER::update_user_balance( $author_id, ( $author_balance - (float) $cart_item['now_price'] ) );
			}
		}

		/**
		 * This function handles a webhook for Stripe payments and logs whether the payment was successful or
		 * failed.
		 *
		 * @param \WP_REST_Request request  is an object of the \WP_REST_Request class, which is used
		 * to handle REST API requests in WordPress. It contains information about the request, such as the
		 * request method, headers, and body.
		 */
		public function asta_cart_webhook( \WP_REST_Request $request ) {

			$keys = ASTA_STRIPE::get_gateway_keys( 'stripe' );

			try {
				$event = Webhook::constructEvent(
					$request->get_body(),
					$request->get_header( 'stripe_signature' ),
					$keys['signature_key']
				);
			} catch ( Exception $e ) {

				http_response_code( 403 );

				error_log( 'error: ' . print_r( $e->getMessage(), true ) );

				wp_send_json(
					array( 'error' => $e->getMessage() ),
				);
			}

			switch ( $event->type ) {

				case 'payment_intent.succeeded':
					wp_send_json( array( 'status' => 'success' ) );
					break;

				case 'payment_intent.payment_failed':
					$order_id = $event?->data?->object?->metadata?->order_id;

					if ( $order_id ) {
						$cart = ASTA_THEME_ORDERS::get_order_cart( $order_id );
						$this->payment_failed( $order_id, $cart );
					}

					error_log(
						print_r(
							array(
								'payment_status' => 'Payment failed!',
								'event'          => $event,
							),
							true
						)
					);
					break;

				case 'charge.dispute.created':
					$payment_intent      = $event?->data?->object?->payment_intent;
					$data_payment_intent = (
						! empty( $payment_intent )
						? ASTA_STRIPE::client()->paymentIntents->retrieve( $payment_intent )
						: array()
					);

					if ( ! empty( $data_payment_intent ) ) {

						$order_id = $data_payment_intent?->charges?->data[0]->metadata->order_id;

						if ( $order_id ) {
							$cart = ASTA_THEME_ORDERS::get_order_cart( $order_id );
							$this->look_modey_sellers_order( $order_id, $cart );
						}
					}
					break;

				case 'charge.dispute.closed':
					$payment_intent      = $event?->data?->object?->payment_intent;
					$data_payment_intent = (
						! empty( $payment_intent )
						? ASTA_STRIPE::client()->paymentIntents->retrieve( $payment_intent )
						: array()
					);

					if ( ! empty( $data_payment_intent ) ) {

						$order_id = $data_payment_intent?->charges?->data[0]->metadata->order_id;

						if ( 'lost' === $event?->data?->object?->status && $order_id ) {

							$cart = ASTA_THEME_ORDERS::get_order_cart( $order_id );
							$this->lost_looked_modey_sellers_order( $order_id, $cart );

						} elseif ( $order_id ) {

							$cart = ASTA_THEME_ORDERS::get_order_cart( $order_id );
							$this->unlock_modey_sellers_order( $order_id, $cart );
						}
					}
					break;

				default:
					if ( str_contains( $event->type, 'charge.dispute' ) ) {
						error_log( print_r( $event, true ) );
					}
					break;
			}

			wp_send_json(
				array( 'status' => 'success' ),
			);
		}
	}

endif;

ASTA_THEME_SOLD_PROCESS::instance();
