<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\Exception\ApiErrorException;

if ( ! class_exists( 'ASTA_THEME_CHACKOUT' ) ) :
	class ASTA_THEME_CHACKOUT extends SEC {

		private static $instance;


		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_CHACKOUT ) ) {
				self::$instance = new ASTA_THEME_CHACKOUT();

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
				'/api-save-iban',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_save_iban' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			$server->register_route(
				'rest-api-wordpress',
				'/api-get-iban',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_get_iban' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			$server->register_route(
				'rest-api-wordpress',
				'/api-get-asta-key',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_key' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * The function generates a random string of hexadecimal characters.
		 *
		 * @param int length The length parameter specifies the number of random bytes to generate. By default,
		 * it is set to 16, which means that the function will generate a string of 32 hexadecimal characters
		 * (since each byte is represented by two hexadecimal characters).
		 *
		 * @return string a hexadecimal string of random bytes.
		 */
		public function generate_random_bytes_hex( int $length = 16 ) {
			return bin2hex( openssl_random_pseudo_bytes( $length ) );
		}


		/**
		 * The function generates a secret key and initialization vector, sets a cookie with a base64 encoded
		 * key, and sends a JSON response with the base64 encoded key and IV.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the \WP_REST_Request
		 * class, which represents the REST API request being made. It contains information about the request
		 * such as the HTTP method, headers, and query parameters.
		 */
		public function asta_key( \WP_REST_Request $request ) {

			$secret_key = 'la_mia_chiave_secreta';
			$iv         = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
			$key        = hash( 'sha256', $secret_key, true );

			$key_nat    = $this->generate_random_bytes_hex();
			$cookie_key = base64_encode( $key_nat );

			setcookie(
				'lommer_key',
				$cookie_key,
				array(
					'expires' => time() + 60 * 60 * 0.5,
					'path'    => '/',
					'secure'  => true,
				)
			);

			wp_send_json(
				array(
					'status' => 'success',
					'key'    => base64_encode( $key ),
					'iv'     => base64_encode( $iv ),
				),
			);
		}


		/**
		 * The function `asta_save_iban` saves an IBAN (International Bank Account Number) for a user and
		 * sends a JSON response indicating success.
		 *
		 * @param \WP_REST_Request request \WP_REST_Request object that represents the REST request made to
		 * the server.
		 */
		public function asta_save_iban( \WP_REST_Request $request ) {

			$attr   = $request->get_attributes();
			$params = $request->get_params();

			ASTA_USER::update_user_user_iban(
				$attr['login_user_id'],
				json_decode(
					Encryption::decrypt(
						$params['text'],
						base64_decode(
							$_COOKIE['lommer_key']
						)
					),
					true
				)
			);

			setcookie(
				'lommer_key',
				$this->generate_random_bytes_hex(),
				array(
					'expires' => -1,
					'path'    => '/',
					'secure'  => true,
				)
			);

			wp_send_json(
				array(
					'status'  => 'success',
					'message' => __( 'IBAN saved successfully', 'asta-api' ),
				)
			);
		}


		/**
		 * The function `asta_get_iban` retrieves the IBAN (International Bank Account Number) of a user,
		 * encrypts it using a key stored in a cookie, and sends it as a JSON response.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the \WP_REST_Request
		 * class, which represents the REST API request being made. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function asta_get_iban( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();

			$iban = Encryption::encrypt(
				json_encode(
					ASTA_USER::get_user_iban( $attr['login_user_id'] )
				),
				base64_decode(
					$_COOKIE['lommer_key']
				)
			);

			setcookie(
				'lommer_key',
				$this->generate_random_bytes_hex(),
				array(
					'expires' => -1,
					'path'    => '/',
					'secure'  => true,
				)
			);

			wp_send_json(
				array(
					'status'  => 'success',
					'message' => $iban,
				)
			);
		}


		public function asta_payout( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();
			// $params = $request->get_params();

			$user_stripe_id = ASTA_USER::get_user_stripe_id( $attr['login_user_id'] );

			if ( $user_stripe_id ) {

				// $transfer = ASTA_STRIPE::client()->transfers->create(
				//  array(
				//      'amount'      => 100000,
				//      'currency'    => 'usd',
				//      'destination' => $user_stripe_id,
				//  )
				// );

				try {
					$payout = ASTA_STRIPE::client()->payouts->create(
						array(
							'amount'   => 100000, // L'importo in centesimi
							'currency' => 'usd',
						),
						array(
							'stripe_account' => $user_stripe_id, // L'ID dell'account collegato
						)
					);

					error_log( print_r( $payout, true ) );

					wp_send_json(
						array(
							'status'   => ! empty( $payout ) ? 'success' : 'error',
							'transfer' => ! empty( $payout ) ? $payout : '',
						),
					);

				} catch ( \Exception $e ) {
					error_log( print_r( $e, true ) );
					wp_send_json(
						array(
							'status'   => 'error',
							'transfer' => $e->getMessage(),
						),
					);
				}
			}

			// $payout = ASTA_STRIPE::client()->payouts->create(
			//  array(
			//      'amount'      => 5000,
			//      'currency'    => 'usd',
			//      'destination' => 'ba_1Example', // L'ID del conto bancario o della carta Stripe
			//      'method'      => 'instant', // Il metodo di pagamento, può essere "standard" o "instant"
			//  )
			// );

			wp_send_json(
				array(
					'status'   => ! empty( $payout ) ? 'success' : 'error',
					'transfer' => ! empty( $payout ) ? $payout : '',
				),
			);
		}


		/**
		 * The function "clean_cart" clears the shopping cart by deleting the cart cookie and user meta data.
		 *
		 * @param int order_id The order ID is an integer that represents the unique identifier for the
		 * order. It is used to identify which order's cart needs to be cleaned.
		 */
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
		 * The function "build_intent_args" takes in a total amount, an array of cart titles, a user ID, and
		 * additional arguments, and returns an array of intent arguments for a payment.
		 *
		 * @param float total The total amount of the cart items.
		 * @param array cart_titles An array containing the titles of the items in the user's cart.
		 * @param int user_id The user_id parameter is an integer that represents the ID of the user for whom
		 * the intent is being built.
		 * @param array args An array of additional arguments that can be passed to the function.
		 *
		 * @return array called .
		 */
		private function build_intent_args( float $total, array $cart_titles, int $user_id, array $args ) {

			$intent_args = array(
				'amount'      => $total * 100,
				'currency'    => 'usd',
				'description' => implode( ', ', $cart_titles ),
			);

			if ( ! empty( $args['card'] ) ) {
				$intent_args['customer']       = ASTA_USER::get_user_customer_id( $user_id );
				$intent_args['payment_method'] = $args['card'];
			}

			return $intent_args;
		}


		/**
		 * The function handles the response from a Stripe payment intent and returns an array with relevant
		 * information about the payment status.
		 *
		 * @param \Stripe\PaymentIntent payment_intent The payment_intent parameter is an object of type
		 * \Stripe\PaymentIntent. It represents a payment intent in the Stripe API. It contains information
		 * about the payment, such as the amount, currency, and status.
		 * @param array args The `args` parameter is an array that contains additional arguments or options
		 * that can be passed to the `hundle_payment_intent_response` function. In this case, it is used to
		 * retrieve the value of the `public_key` argument, which is an optional parameter. If the
		 * `public_key`
		 *
		 * @return array with different key-value pairs depending on the status of the payment intent. If
		 * the payment intent status is not "succeeded", it returns an array with the following keys:
		 * "requires_action" (set to true), "client_secret", "public_key" (if provided in the  array),
		 * "payment_intent", "intent_status", and "message". If the
		 */
		private function hundle_payment_intent_response( \Stripe\PaymentIntent $payment_intent, array $args ) {
			return (
				'succeeded' !== $payment_intent->status
				? array(
					'requires_action' => true,
					'client_secret'   => $payment_intent->client_secret,
					'public_key'      => $args['public_key'] ?? '',
					'payment_intent'  => $payment_intent->id,
					'intent_status'   => $payment_intent->status,
					'message'         => 'payment intent ready',
				)
				: array(
					'requires_action' => false,
					'public_key'      => $args['public_key'] ?? '',
					'payment_intent'  => $payment_intent->id,
					'intent_status'   => $payment_intent->status,
					'message'         => 'payment completed',
				)
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

				$payment_intent = ASTA_STRIPE::client()->paymentIntents->create(
					$this->build_intent_args( $total, $cart_titles, $user_id, $args )
				);

				$order_id  = $this->build_order( $user_id, $payment_intent, $args['cart'] );
				$user_info = $user_id ? get_userdata( $user_id ) : '';

				ASTA_STRIPE::client()->paymentIntents->update(
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

				return array_merge(
					array(
						'status'   => 'success',
						'order_id' => $order_id,
					),
					$this->hundle_payment_intent_response( $payment_intent, $args )
				);
			} catch ( ApiErrorException $e ) {
				http_response_code( 400 );

				return array(
					'status'      => 'error',
					'message'     => __( 'Stripe api error exception see php logs', 'asta-api' ),
					'invok_nexts' => false,
				);
			} catch ( Exception $e ) {
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

			$attr   = $request->get_attributes();
			$params = $request->get_params();
			$card   = ( ! empty( $params['card'] ) ? preg_replace( '/[^a-zA-Z0-9\/\=\+]/i', '', $params['card'] ) : '' );

			$keys = ASTA_STRIPE::get_gateway_keys( 'stripe' );
			$cart = ASTA_THEME_CART::get_cart();

			$args = array(
				'cart'       => $cart,
				'public_key' => $keys['public_key'],
			);

			if ( ! empty( $card ) ) {
				$args['card'] = ASTA_STRIPE::sec()->decrypt( base64_decode( $card ) );
			}

			wp_send_json( $this->create_payment_intent( $attr['login_user_id'], $args ) );
		}
	}

endif;

ASTA_THEME_CHACKOUT::instance();
