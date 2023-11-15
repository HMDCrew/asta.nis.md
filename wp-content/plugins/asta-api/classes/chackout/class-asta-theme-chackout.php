<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;

if ( ! class_exists( 'ASTA_THEME_CHACKOUT' ) ) :
	class ASTA_THEME_CHACKOUT extends SEC {

		private static $instance;
		public $stripe_client;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_CHACKOUT ) ) {
				self::$instance = new ASTA_THEME_CHACKOUT();

				self::$instance->stripe_client = ASTA_STRIPE::client();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
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
				'/api-cart-chackout',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_cart_chackout' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			$server->register_route(
				'rest-api-wordpress',
				'/api-payout',
				array(
					'methods'       => 'get',
					'callback'      => array( $this, 'asta_payout' ),
					'login_user_id' => get_current_user_id(),
				)
			);

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

		public function asta_payout( \WP_REST_Request $request ) {

			// $attr   = $request->get_attributes();
			// $params = $request->get_params();

			// $payout = $this->stripe_client->payouts->create(
			// 	array(
			// 		'amount'   => 5000,
			// 		'currency' => 'usd',

			// 		'amount' => 5000, // L'importo in centesimi
			// 		'currency' => 'usd', // La valuta
			// 		'destination' => 'ba_1Example', // L'ID del conto bancario o della carta Stripe
			// 		'method' => 'instant', // Il metodo di pagamento, può essere "standard" o "instant"
			// 	)
			// );

			// wp_send_json(
			// 	array(
			// 		'status' => 'success',
			// 		'payout' => $payout,
			// 	),
			// );
		}


		public static function clean_cart( int $order_id ) {

			$user_id = get_current_user_id();

			setcookie( 'asta_cart', '', -1, '/' );

			if ( $user_id ) {
				delete_user_meta( $user_id, 'last_intent_pay' );
				update_user_meta( $user_id, 'user_cart', array() );
			}
		}


		/**
		 * The function calculates the total price of items in a shopping cart.
		 *
		 * @param array cart The parameter `` is an array that contains information about the items in a
		 * shopping cart. Each item in the cart is represented as an array with keys such as `now_price` that
		 * hold the price of the item. The function calculates the total price of all items in the cart and
		 * returns it
		 *
		 * @return float total price of all items in the cart, calculated by adding up the "now_price" value of
		 * each item in the cart array. If the cart is empty, the function returns 0.
		 */
		private function get_cart_total( array $cart ) {

			$products_prices = (
				! empty( $cart['products_cart'] )
				? array_map(
					fn( $cart_item ) => floatval( get_post_meta( $cart_item['product_id'], 'price', true ) ) * $cart_item['qty'],
					$cart['products_cart']
				)
				: array()
			);

			return (
				! empty( $cart['auctions_cart'] ) || ! empty( $cart['products_cart'] )
				? array_sum(
					array_merge(
						array_column( $cart['auctions_cart'], 'now_price' ),
						$products_prices
					)
				)
				: 0
			);
		}


		/**
		 * The function "build_order" creates a new order post in WordPress with the provided user ID,
		 * payment intent, and cart details.
		 *
		 * @param int user_id The user ID of the buyer who is placing the order.
		 * @param Stripe\PaymentIntent payment_intent The payment_intent parameter is an instance of the
		 * Stripe\PaymentIntent class. It represents a payment intent object created in the Stripe payment
		 * gateway. It contains information about the payment, such as the amount, currency, and payment
		 * status.
		 * @param array cart The "cart" parameter is an array that contains the details of the items in the
		 * user's shopping cart. It typically includes information such as the product ID, quantity, price,
		 * and any other relevant details for each item.
		 *
		 * @return int|WP_Error post ID of the newly inserted order post.
		 */
		private function build_order( int $user_id, Stripe\PaymentIntent $payment_intent, array $cart ) {

			$args = array(
				'post_type'  => 'orders',
				'meta_input' => array(
					'payment_status' => 'pending',
					'payment_intent' => $payment_intent->id,
					'details'        => array(
						'amount'   => $payment_intent->amount,
						'currency' => $payment_intent->currency,
						'cart'     => $cart,
					),
				),
			);

			if ( $user_id ) {
				$args['post_author']                    = $user_id;
				$args['meta_input']['details']['buyer'] = $user_id;
			}

			return wp_insert_post( $args );
		}

		/**
		 * This PHP function returns an array of auction titles based on the auction IDs in a given cart
		 * array.
		 *
		 * @param array cart The parameter `` is an array that contains information about the items in the
		 * user's shopping cart. Each item in the cart is represented as an array with keys such as
		 * `auction_id`, `quantity`, etc.
		 *
		 * @return array `get_cart_titles()` function is returning an array of auction titles corresponding to
		 * the auction IDs in the `` array. If the `` array is empty, it returns an empty array.
		 */
		private function get_cart_titles( array $cart ) {

			$auctions_ids = ! empty( $cart['auctions_cart'] ) ? array_column( $cart['auctions_cart'], 'auction_id' ) : array();
			$products_ids = ! empty( $cart['products_cart'] ) ? array_column( $cart['products_cart'], 'product_id' ) : array();

			return (
				! empty( $auctions_ids ) || ! empty( $products_ids )
				? array_map(
					function ( $post_id ) {
						return get_the_title( $post_id );
					},
					array_merge( $auctions_ids, $products_ids )
				)
				: array()
			);
		}


		/**
		 * The function `create_payment_intent` creates a payment intent using the Stripe API and returns the
		 * client secret, public key, and order ID.
		 *
		 * @param int user_id The user ID of the user for whom the payment intent is being created.
		 * @param array args - total: The total amount of the payment in USD.
		 *
		 * @return array with the following keys and values:
		 */
		private function create_payment_intent( int $user_id, array $args ) {

			$cart_titles = $this->get_cart_titles( $args['cart'] );
			$total       = $this->get_cart_total( $args['cart'] );

			try {

				$payment_intent = $this->stripe_client->paymentIntents->create( // phpcs:ignore
					array(
						'automatic_payment_methods' => array( 'enabled' => true ),
						'amount'                    => $total * 100,
						'currency'                  => 'usd',
						'description'               => implode( ', ', $cart_titles ),
					)
				);

				$order_id  = $this->build_order( $user_id, $payment_intent, $args['cart'] );
				$user_info = $user_id ? get_userdata( $user_id ) : '';

				$this->stripe_client->paymentIntents->update( // phpcs:ignore
					$payment_intent->id,
					array(
						'metadata' => array( 'order_id' => $order_id ),
					)
				);

				// update post title
				wp_update_post(
					array(
						'ID'          => $order_id,
						'post_status' => 'publish',
						'post_title'  => (
							! empty( $user_info )
							? sprintf( 'Order #%d from %s, total: %2.F', $order_id, $user_info->user_email, $total )
							: sprintf( 'Order #%d from non auth user, total: %2.F', $order_id, $total )
						),
						'meta_input'  => array(
							'client_secret' => base64_encode( $this->encrypt( $payment_intent->client_secret ) ),
						),
					)
				);

				if ( $user_id ) {
					update_user_meta( $user_id, 'last_intent_pay', $order_id );
				}

				return array(
					'status'        => 'success',
					'message'       => 'payment intent ready',
					'client_secret' => $payment_intent->client_secret,
					'public_key'    => $args['public_key'],
					'order_id'      => $order_id,
				);
			} catch ( ApiErrorException $e ) {
				http_response_code( 400 );
				error_log( print_r( $e->getError()->message, true ) );

				return array(
					'status'      => 'error',
					'message'     => __( 'Stripe api error exception see php logs', 'asta-api' ),
					'invok_nexts' => false,
				);
			} catch ( Exception $e ) {
				error_log( print_r( $e, true ) );
				http_response_code( 500 );

				return array(
					'status'      => 'error',
					'message'     => __( 'General endpoint error see php logs', 'asta-api' ),
					'invok_nexts' => false,
				);
			}
		}


		/**
		 * This function creates a payment intent using Stripe API for a logged in user's cart total.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function asta_cart_chackout( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();

			$keys = ASTA_STRIPE::get_gateway_keys( 'stripe' );
			$cart = ASTA_THEME_CART::get_cart();

			$args = array(
				'cart'       => $cart,
				'public_key' => $keys['public_key'],
			);

			wp_send_json( $this->create_payment_intent( $attr['login_user_id'], $args ) );
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

			if ( 'payment_intent.succeeded' === $event->type ) {

				wp_send_json( array( 'status' => 'success' ) );

			} elseif ( 'payment_intent.payment_failed' === $event->type ) {

				$order_id = $event?->data?->object?->metadata?->order_id;

				if ( $order_id ) {
					ASTA_THEME_ORDERS::set_order_status( $order_id, 'pending' );
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
			}

			wp_send_json(
				array( 'status' => 'success' ),
			);
		}
	}

endif;

ASTA_THEME_CHACKOUT::instance();
