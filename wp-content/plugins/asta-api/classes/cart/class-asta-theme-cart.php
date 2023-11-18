<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_CART' ) ) :
	class ASTA_THEME_CART {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_CART ) ) {
				self::$instance = new ASTA_THEME_CART();
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
		 * @param \WP_REST_Server $server
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {

			// Product add to cart
			$server->register_route(
				'rest-api-wordpress',
				'/api-add-to-cart',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_add_to_cart' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Product update qty
			$server->register_route(
				'rest-api-wordpress',
				'/api-qty-update',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_update_qty' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Product remove product
			$server->register_route(
				'rest-api-wordpress',
				'/api-remove-cart-product',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_remove_product' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * The function `asta_add_to_cart` adds a product to the cart and returns a JSON response with the
		 * updated cart.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the \WP_REST_Request
		 * class, which represents the REST API request being made. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function asta_add_to_cart( \WP_REST_Request $request ) {

			$params = $request->get_params();

			$product_id = ( ! empty( $params['product_id'] ) ? (int) preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );

			if ( ! empty( $product_id ) ) {

				$qty_updated   = false;
				$cart          = self::get_cart();
				$products_cart = ! empty( $_COOKIE['asta_cart'] ) ? preg_replace( '/[^a-zA-Z0-9\%\,\[\]\{\}\:\"\'\_\-]/i', '', $_COOKIE['asta_cart'] ) : '';
				$products_cart = (
					! empty( $products_cart )
					? json_decode( $products_cart, true )
					: array()
				);

				! empty( $products_cart ) ? sort( $products_cart ) : array();

				$key = array_search( $product_id, array_column( $products_cart, 'product_id' ), true );

				if ( false !== $key ) {
					$qty_updated                   = true;
					$products_cart[ $key ]['qty'] += 1;
				} else {
					$products_cart[] = array(
						'product_id' => $product_id,
						'qty'        => 1,
					);
				}

				$cart['products_cart'] = $products_cart;

				setcookie( 'asta_cart', json_encode( $cart['products_cart'] ), time() + 3600 * 24 * 30 * 12, '/' );

				wp_send_json(
					array(
						'status'     => 'success',
						'message'    => (
							$qty_updated
							? __( 'Product amount updated', 'asta-api' )
							: __( 'Product added to cart', 'asta-api' )
						),
						'n_products' => (int) count( $cart['products_cart'] ) + count( $cart['auctions_cart'] ),
					)
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'missing product id', 'asta-api' ),
				)
			);
		}


		public static function clean_zero_qty( array $porduct_cart ) {
			return array_filter( $porduct_cart, fn( $item ) => $item['qty'] > 0 );
		}


		/**
		 * The function `get_cart()` retrieves the user's cart information, including products and auctions,
		 * either from user meta data or from a cookie.
		 *
		 * @return array with two keys: 'products_cart' and 'auctions_cart'. The value of 'products_cart'
		 * is an array that is either the decoded value of the 'asta_cart' cookie or an empty array if the
		 * cookie is empty. The value of 'auctions_cart' is either the value of the 'user_cart' user meta for
		 * the current user or an empty
		 */
		public static function get_cart() {

			$auctions_cart = array();

			if ( is_user_logged_in() ) {
				$auctions_cart = get_user_meta( get_current_user_id(), 'user_cart', true );
			}

			$products_cart = ! empty( $_COOKIE['asta_cart'] ) ? preg_replace( '/[^a-zA-Z0-9\%\,\[\]\{\}\:\"\'\_\-]/i', '', $_COOKIE['asta_cart'] ) : '';

			return array(
				'products_cart' => (
					! empty( $products_cart )
					? self::clean_zero_qty( json_decode( $products_cart, true ) )
					: array()
				),
				'auctions_cart' => (
					! empty( $auctions_cart )
					? $auctions_cart
					: array()
				),
			);
		}


		/**
		 * The function "get_cart_counter" returns the total number of products and auctions in the cart.
		 *
		 * @return int sum of the number of products in the cart and the number of auctions in the cart.
		 */
		public static function get_cart_counter() {

			$cart = self::get_cart();

			return (int) count( $cart['products_cart'] ) + count( $cart['auctions_cart'] );
		}


		public function asta_update_qty( \WP_REST_Request $request ) {

			$params = $request->get_params();

			$product_id = ( ! empty( $params['product_id'] ) ? (int) preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );
			$qty        = ( ! empty( $params['qty'] ) ? (int) preg_replace( '/[^0-9]/i', '', $params['qty'] ) : 0 );

			if ( ! empty( $product_id ) ) {

				$product_qty = (int) get_post_meta( $product_id, 'qty', true );
				$qty         = $product_qty >= $qty ? $qty : $product_qty;

				$products_cart = ! empty( $_COOKIE['asta_cart'] ) ? preg_replace( '/[^a-zA-Z0-9\%\,\[\]\{\}\:\"\'\_\-]/i', '', $_COOKIE['asta_cart'] ) : '';
				$products_cart = (
					! empty( $products_cart )
					? json_decode( $products_cart, true )
					: array()
				);

				$key = array_search( $product_id, array_column( $products_cart, 'product_id' ), true );

				if ( false !== $key ) {

					$products_cart[ $key ]['qty'] = $qty;
					setcookie( 'asta_cart', json_encode( $products_cart ), time() + 3600 * 24 * 30 * 12, '/' );

					$price = floatval( get_post_meta( $product_id, 'price', true ) );

					wp_send_json(
						array(
							'status'       => 'success',
							'message'      => __( 'product qty is updates', 'asta-api' ),
							'cart_product' => array(
								'product' => $products_cart[ $key ],
								'price'   => $price * $qty,
							),
						)
					);
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'product is not in cart', 'asta-api' ),
					)
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'missing product id', 'asta-api' ),
				)
			);
		}

		public function asta_remove_product( \WP_REST_Request $request ) {

			$params     = $request->get_params();
			$product_id = ( ! empty( $params['product_id'] ) ? (int) preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );

			if ( ! empty( $product_id ) ) {

				$products_cart = ! empty( $_COOKIE['asta_cart'] ) ? preg_replace( '/[^a-zA-Z0-9\%\,\[\]\{\}\:\"\'\_\-]/i', '', $_COOKIE['asta_cart'] ) : '';
				$products_cart = (
					! empty( $products_cart )
					? json_decode( $products_cart, true )
					: array()
				);

				! empty( $products_cart ) ? sort( $products_cart ) : array();

				$key = array_search( $product_id, array_column( $products_cart, 'product_id' ), true );

				if ( false !== $key ) {

					unset( $products_cart[ $key ] );
					setcookie( 'asta_cart', json_encode( $products_cart ), time() + 3600 * 24 * 30 * 12, '/' );

					wp_send_json(
						array(
							'status'  => 'success',
							'message' => __( 'product has been removed', 'asta-api' ),
						)
					);
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'product is not in cart', 'asta-api' ),
					)
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'missing product id', 'asta-api' ),
				)
			);
		}
	}
endif;

ASTA_THEME_CART::instance();
