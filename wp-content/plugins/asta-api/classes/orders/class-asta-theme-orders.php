<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_ORDERS' ) ) :
	class ASTA_THEME_ORDERS extends SEC {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_ORDERS ) ) {
				self::$instance = new ASTA_THEME_ORDERS();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'add_meta_boxes', array( $this, 'order_metaboxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_order_metabox_style' ) );
			add_action( 'rest_api_init', array( $this, 'asta_rest_api' ), 10 );
			add_action( 'chack_order_payment_status', array( $this, 'chack_order_payment_status_after_payment' ), 10, 1 );
		}


		/**
		 * The function checks the payment status of an order after payment and updates the payment status to
		 * 'pending' if no charges are found.
		 *
		 * @param int order_id The order_id parameter is an integer that represents the ID of the order for
		 * which you want to check the payment status.
		 */
		public function chack_order_payment_status_after_payment( int $order_id ) {

			$payment_intent_id = get_post_meta( $order_id, 'payment_intent', true );

			$payment_intent = ASTA_THEME_CHACKOUT::instance()->stripe_client->paymentIntents->retrieve( // phpcs:ignore
				$payment_intent_id,
			);

			if ( empty( $payment_intent->charges->data ) ) {
				update_post_meta( $order_id, 'payment_status', 'pending' );
			}
		}


		/**
		 * The function schedules multiple events to check the payment status of an order at different
		 * intervals.
		 *
		 * @param int order_id The order ID is an integer value that represents a unique identifier for an
		 * order. It is used to identify a specific order in the system.
		 */
		public static function crons_after_payment_order_status( int $order_id ) {

			$date          = new DateTimeImmutable();
			$date_1_week   = $date->add( new DateInterval( 'P7D' ) );
			$date_1_month  = $date->add( new DateInterval( 'P1M' ) );
			$date_2_months = $date->add( new DateInterval( 'P2M' ) );
			$date_3_months = $date->add( new DateInterval( 'P3M' ) );
			$date_1_year   = $date->add( new DateInterval( 'P1Y' ) );
			$date_5_years  = $date->add( new DateInterval( 'P5Y' ) );
			$date_10_years = $date->add( new DateInterval( 'P10Y' ) );

			wp_schedule_single_event( $date_1_week->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_1_month->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_2_months->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_3_months->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_1_year->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_5_years->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
			wp_schedule_single_event( $date_10_years->getTimestamp(), 'chack_order_payment_status', array( 'order_id' => $order_id ) );
		}


		/**
		 * The function "asta_rest_api" registers a route for the WordPress REST API that allows users to pay
		 * for a forgotten order.
		 *
		 * @param \WP_REST_Server The  parameter is an instance of the WP_REST_Server class. It is used to
		 * register routes and handle REST API requests.
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {

			$server->register_route(
				'rest-api-wordpress',
				'/api-pay-forgotten-order',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'pay_forgotten' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * The function `pay_forgotten` checks if the user is authenticated and returns the payment intent
		 * details if they are, otherwise it returns an authentication error message.
		 *
		 * @param \WP_REST_Request request The `` parameter is an instance of the `\WP_REST_Request`
		 * class, which represents a REST API request. It contains information about the request, such as the
		 * HTTP method, headers, and parameters.
		 */
		public function pay_forgotten( \WP_REST_Request $request ) {

			$attr     = $request->get_attributes();
			$params   = $request->get_params();
			$order_id = ( ! empty( $params['order_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['order_id'] ) : '' );

			$author_id     = (int) get_post_field( 'post_author', $order_id );
			$client_secret = get_post_meta( $order_id, 'client_secret', true );

			if ( ! empty( $attr['login_user_id'] ) && $author_id === $attr['login_user_id'] ) {
				wp_send_json(
					array(
						'status'        => 'success',
						'message'       => 'payment intent ready',
						'client_secret' => ! empty( $client_secret ) ? $this->decrypt( base64_decode( $client_secret ) ) : '',
						'public_key'    => ASTA_STRIPE::get_gateway_key( 'stripe', 'public_key' ),
					),
				);
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
		 * This function enqueues a CSS style file for the orders post type metabox.
		 *
		 * @param string hook The hook parameter is a string that specifies the name of the current WordPress admin
		 * page. It is used to determine whether or not to enqueue the stylesheet. In this case, the function
		 * checks if the current page is either "post.php" or "post-new.php" and if the post type is "
		 */
		public function enqueue_order_metabox_style( string $hook ) {

			$screen = get_current_screen();

			if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'orders' === $screen->post_type ) {
				wp_register_style( 'order-metabox', ASTA_API_PLUGIN_ASSETS . '/css/style.css' );
				wp_enqueue_style( 'order-metabox' );
			}
		}


		/**
		 * This function adds a metabox for displaying order details in the WordPress admin panel.
		 */
		public function order_metaboxes() {
			add_meta_box( 'order_details_metabox', __( 'Order Details', 'asta-api' ), array( $this, 'render_order_details_metabox' ), 'orders', 'normal', 'high' );
		}


		/**
		 * This PHP function renders the order details metabox with information about the payment status,
		 * payment intent, buyer, amount, currency, and cart.
		 *
		 * @param WP_Post order The order object that contains information about the order, such as the order ID,
		 * customer details, and order status.
		 */
		public function render_order_details_metabox( WP_Post $order ) {

			$details = get_post_meta( $order->ID, 'details', true );

			asta_plugin_get_template_part(
				ASTA_API_PLUGIN_TEMPLATES,
				'orders/order',
				'metabox',
				array(
					'payment_status' => get_post_meta( $order->ID, 'payment_status', true ),
					'payment_intent' => get_post_meta( $order->ID, 'payment_intent', true ),
					'buyer'          => get_userdata( $details['buyer'] ),
					'amount'         => $details['amount'] / 100,
					'currency'       => $details['currency'],
					'cart'           => $details['cart'],
				)
			);
		}


		/**
		 * This function retrieves an order post based on a given payment intent ID.
		 *
		 * @param string payment_intent_id The payment_intent_id is a unique identifier for a payment intent
		 * in Stripe, which is a payment processing platform. This function retrieves the order associated
		 * with the given payment intent ID from the WordPress database.
		 *
		 * @return WP_Post|false post object of the first order that matches the given payment intent ID, or false if no
		 * order is found.
		 */
		public static function get_order( string $payment_intent_id ) {

			$orders = get_posts(
				array(
					'post_type'  => 'orders',
					'meta_query' => array(
						array(
							'key'     => 'payment_intent',
							'value'   => $payment_intent_id,
							'compare' => '=',
						),
					),
				)
			);

			return ! empty( $orders ) ? reset( $orders ) : false;
		}


		/**
		 * This function returns the order ID associated with a given payment intent ID.
		 *
		 * @param string payment_intent_id The payment_intent_id parameter is a string that represents the
		 * unique identifier of a payment intent. A payment intent is an object that represents a payment
		 * attempt, and it contains information about the payment, such as the amount, currency, and payment
		 * method. This parameter is used to retrieve the order ID associated with
		 *
		 * @return int ID of an order associated with a given payment intent ID.
		 */
		public static function get_order_id( string $payment_intent_id ) {
			return self::$instance->get_order( $payment_intent_id )->ID;
		}


		/**
		 * This function retrieves a specific meta value for a given order ID.
		 *
		 * @param int order_id The ID of the order for which you want to retrieve the meta data.
		 * @param string key The key parameter is a string that represents the name of the meta data that we
		 * want to retrieve from the post meta table. In this case, it is used to retrieve a specific meta
		 * data associated with an order ID.
		 *
		 * @return string|array|int|float value of a specific meta key for a given order ID. It is using the WordPress function
		 * `get_post_meta()` to retrieve the meta value.
		 */
		public static function get_meta( int $order_id, string $key ) {
			return get_post_meta( $order_id, $key, true );
		}


		/**
		 * This function sets a meta value for a given order ID in WordPress using the update_post_meta
		 * function.
		 *
		 * @param int order_id The ID of the order for which the meta data is being set.
		 * @param string key The key parameter is a string that represents the name of the meta data that is
		 * being set for the order. It is used to identify the specific piece of data that is being stored.
		 * @param string value The value to be stored in the post meta field. It can be a string, integer,
		 * boolean, array or object.
		 *
		 * @return bool result of the `update_post_meta()` function, which is a boolean value indicating
		 * whether the update was successful or not.
		 */
		public static function set_meta( int $order_id, string $key, string $value ) {
			return update_post_meta( $order_id, $key, $value );
		}


		/**
		 * The function sets the payment status of an order in WordPress using the update_post_meta function.
		 *
		 * @param int order_id An integer representing the ID of the order for which the status needs to be
		 * updated.
		 * @param string status The status parameter is a string that represents the new payment status that
		 * will be set for the order. It could be "pending", "processing", "completed", "cancelled", or any
		 * other custom status that has been defined.
		 *
		 * @return bool result of the `update_post_meta()` function, which is a boolean value indicating
		 * whether the update was successful or not.
		 */
		public static function set_order_status( int $order_id, string $status ) {
			return update_post_meta( $order_id, 'payment_status', $status );
		}


		/**
		 * The function retrieves all orders made by a specific user in ascending order by date.
		 *
		 * @param int user_id The ID of the user whose orders are being retrieved.
		 *
		 * @return WP_Post[] function is returning an array of posts of the post type "orders" authored by the
		 * user with the specified user ID, sorted by post date in ascending order.
		 */
		public static function get_user_orders( int $user_id ) {
			$orders = new WP_Query(
				array(
					'post_type'      => 'orders',
					'author'         => $user_id,
					'orderby'        => 'post_date',
					'order'          => 'DESC',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => 'payment_status',
							'compare' => '!=',
							'value'   => 'pending',
						),
					),
				)
			);

			return $orders->posts;
		}
	}

endif;

ASTA_THEME_ORDERS::instance();
