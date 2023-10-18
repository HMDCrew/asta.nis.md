<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;

if ( ! class_exists( 'WPR_THEME_CHACKOUT' ) ) :
	class WPR_THEME_CHACKOUT extends SEC {

		private static $instance;
		public $stripe_client;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPR_THEME_CHACKOUT ) ) {
				self::$instance = new WPR_THEME_CHACKOUT();
				self::$instance->set_up_class_variable();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * It sets up the class variables
		 */
		public function set_up_class_variable() {
			$this->stripe_client = new StripeClient(
				array(
					'api_key'        => self::get_gateway_key( 'stripe', 'private_key' ),
					'stripe_version' => '2020-08-27',
				)
			);
		}

		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'wpr_rest_api' ), 10 );
		}


		/**
		 * Registering a route for the REST API.
		 *
		 * @param [type] $server
		 */
		public function wpr_rest_api( $server ) {

			$server->register_route(
				'rest-api-wordpress',
				'/api-cart-chackout',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_cart_chackout' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			$server->register_route(
				'rest-api-wordpress',
				'/api-cart-webhook',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_cart_webhook' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * The function retrieves the public, private, and signature keys for a specified payment gateway
		 * from the WordPress options table.
		 *
		 * @param string gateway The name of the payment gateway for which the keys are being retrieved.
		 *
		 * @return array containing the public key, private key, and signature key for a specified payment
		 * gateway.
		 */
		private function get_gateway_keys( string $gateway ) {
			$opt_encrypt = get_option( 'asta_payments_element' );
			$opt         = $this->array_option_dec( $opt_encrypt );

			$use_case = $opt[ $gateway ]['use_case'];

			return array(
				'public_key'    => $opt[ $gateway ][ $use_case . '_pub' ],
				'private_key'   => $opt[ $gateway ][ $use_case . '_priv' ],
				'signature_key' => ! empty( $opt[ $gateway ][ $use_case . '_signature' ] ) ? $opt[ $gateway ][ $use_case . '_signature' ] : '',
			);
		}

		/**
		 * This function retrieves a specific key from an array of gateway keys based on the provided gateway
		 * name.
		 *
		 * @param string gateway The name of the payment gateway for which the key is being retrieved.
		 * @param string key The  parameter is a string that represents the specific key that needs to be
		 * retrieved from the array of gateway keys.
		 *
		 * @return string|array|null value of the specified key from an array of gateway keys for a given gateway.
		 */
		public static function get_gateway_key( string $gateway, string $key ) {
			$gateway_keys = self::$instance->get_gateway_keys( $gateway );
			return $gateway_keys[ $key ];
		}

		/**
		 * The function removes order products from a user's cart and updates the user's cart meta data.
		 *
		 * @param int user_id The user ID of the user whose cart needs to be updated.
		 * @param int order_id The order_id parameter is an integer that represents the ID of the order from
		 * which you want to remove products.
		 */
		public static function remove_order_products_from_user_cart( int $user_id, int $order_id ) {

			delete_user_meta( $user_id, 'last_intent_pay' );
			$user_cart     = get_user_meta( $user_id, 'user_cart', true );
			$order_details = WPR_THEME_ORDERS::get_meta( $order_id, 'details' );

			update_user_meta( $user_id, 'user_cart', array_diff( $user_cart, $order_details['cart'] ) );
		}

		/**
		 * This is a PHP function that retrieves a user's cart from their user meta data.
		 *
		 * @param int user_id The user ID is an integer value that uniquely identifies a user in the system.
		 * It is used to retrieve the user's cart information from the user meta data.
		 *
		 * @return array function `get_user_cart` is returning the value of the user meta field with the key
		 * 'user_cart' for the given user ID.
		 */
		private function get_user_cart( int $user_id ) {
			return get_user_meta( $user_id, 'user_cart', true );
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
			return (
				! empty( $cart )
					? array_sum( array_column( $cart, 'now_price' ) )
					: 0
			);
		}

		/**
		 * The function "get_last_order" retrieves the last intent pay value from the user meta data for a
		 * given user ID.
		 *
		 * @param int user_id The user ID is an integer that represents the unique identifier of a user in
		 * the system. It is used to retrieve the last order associated with a specific user.
		 *
		 * @return mixed value of the 'last_intent_pay' user meta for the given user ID.
		 */
		private function get_last_order( int $user_id ) {
			return get_user_meta( $user_id, 'last_intent_pay', true );
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
			return wp_insert_post(
				array(
					'post_type'   => 'orders',
					'post_author' => $user_id,
					'meta_input'  => array(
						'payment_status' => 'pending',
						'payment_intent' => $payment_intent->id,
						'details'        => array(
							'buyer'    => $user_id,
							'amount'   => $payment_intent->amount,
							'currency' => $payment_intent->currency,
							'cart'     => $cart,
						),
					),
				)
			);
		}

		/**
		 * This PHP function returns an array of auction titles based on the auction IDs in a given cart
		 * array.
		 *
		 * @param array cart The parameter `` is an array that contains information about the items in the
		 * user's shopping cart. Each item in the cart is represented as an array with keys such as
		 * `auction_id`, `quantity`, etc.
		 *
		 * @return string `get_cart_titles()` function is returning an array of auction titles corresponding to
		 * the auction IDs in the `` array. If the `` array is empty, it returns an empty string.
		 */
		private function get_cart_titles( array $cart ) {
			return (
				! empty( $cart )
					? array_map(
						function ( $auction_id ) {
							return get_the_title( $auction_id );
						},
						array_column( $cart, 'auction_id' )
					)
					: ''
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
				$user_info = get_userdata( $user_id );

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
						'post_title'  => sprintf( 'Order #%d from %s, total: %2.F', $order_id, $user_info->user_email, $total ),
						'meta_input'  => array(
							'client_secret' => base64_encode( $this->encrypt( $payment_intent->client_secret ) ),
						),
					)
				);

				update_user_meta( $user_id, 'last_intent_pay', $order_id );

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
		 * The function checks if two arrays are identical by comparing their elements.
		 *
		 * @param array array1 The first array to compare.
		 * @param array array2 The above code defines a private function named "are_arrays_identical" that
		 * takes two array parameters:  and .
		 *
		 * @return boolean value. It returns true if the two arrays are identical (i.e., they have the same
		 * elements in the same order), and false otherwise.
		 */
		private function are_arrays_identical( array $array1, array $array2 ) {

			if ( is_array( $array1 ) && is_array( $array2 ) ) {

				$diff1 = array_diff( $array1, $array2 );
				$diff2 = array_diff( $array2, $array1 );

				return empty( $diff1 ) && empty( $diff2 );
			}

			return false;
		}

		/**
		 * The function "get_existing_payment_intent" retrieves the existing payment intent for a given order
		 * ID and returns it along with other relevant information.
		 *
		 * @param int order_id The order_id parameter is an integer that represents the ID of the order for
		 * which we want to retrieve the existing payment intent.
		 * @param array args  is an array that contains the following parameters:
		 *
		 * @return array with the following keys and values:
		 */
		private function get_existing_payment_intent_updated( int $order_id, array $args ) {

			$client_secret  = get_post_meta( $order_id, 'client_secret', true );
			$order_details  = get_post_meta( $order_id, 'details', true );
			$payment_intent = get_post_meta( $order_id, 'payment_intent', true );

			if (
				! empty( $order_details['cart'] ) && is_array( $order_details['cart'] ) &&
				! empty( $args['cart'] ) && is_array( $args['cart'] ) &&
				$this->are_arrays_identical( $args['cart'], $order_details['cart'] )
			) {

				// Update last payment intent from user cart
				$this->stripe_client->paymentIntents->update( // phpcs:ignore
					$payment_intent,
					array(
						'amount'      => $this->get_cart_total( $args['cart'] ) * 100,
						'currency'    => 'usd',
						'description' => implode( ', ', $this->get_cart_titles( $args['cart'] ) ),
					)
				);

				// update cart in order
				$order_details['cart'] = $args['cart'];
				update_post_meta( $order_id, 'details', $order_details );
			}

			return array(
				'status'        => 'success',
				'message'       => 'last payment intent ready',
				'client_secret' => ! empty( $client_secret ) ? $this->decrypt( base64_decode( $client_secret ) ) : '',
				'public_key'    => $args['public_key'],
				'order_id'      => $order_id,
			);
		}


		/**
		 * The function `intent_payment` checks if there is an existing order for a user and creates or
		 * retrieves a payment intent accordingly.
		 *
		 * @param int user_id The user ID is an integer that represents the unique identifier of the user for
		 * whom the payment intent is being created or retrieved.
		 *
		 * @return array payment intent.
		 */
		private function intent_payment( int $user_id ) {

			$order_id = $this->get_last_order( $user_id );
			$keys     = $this->get_gateway_keys( 'stripe' );
			$cart     = (array) $this->get_user_cart( $user_id );

			$args = array(
				'cart'       => $cart,
				'public_key' => $keys['public_key'],
			);

			return (
				! $order_id
				? $this->create_payment_intent( $user_id, $args )
				: $this->get_existing_payment_intent_updated( $order_id, $args )
			);
		}

		/**
		 * This function creates a payment intent using Stripe API for a logged in user's cart total.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function wpr_cart_chackout( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();

			if ( ! empty( $attr['login_user_id'] ) ) {

				wp_send_json( $this->intent_payment( $attr['login_user_id'] ) );

			} else {
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => 'Authentication required',
					),
				);
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
		public function wpr_cart_webhook( \WP_REST_Request $request ) {

			$keys = $this->get_gateway_keys( 'stripe' );

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
					WPR_THEME_ORDERS::set_order_status( $order_id, 'pending' );
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

WPR_THEME_CHACKOUT::instance();
