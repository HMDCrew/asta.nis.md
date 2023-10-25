<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_CART' ) ) :
	class ASTA_CART {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_CART ) ) {
				self::$instance = new ASTA_CART;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_cart_item', array( $this, 'asta_cart' ), 10, 1 );
		}


		/**
		 * The function calculates the total price based on the quantity and either the current price or the
		 * regular price.
		 *
		 * @param array args The `` parameter is an array that contains the following keys:
		 *
		 * @return float calculated price based on the given arguments. If the 'now_price' argument is not
		 * empty, it will multiply it by the quantity and return the result. If the 'now_price' argument is
		 * empty but the 'price' argument is not empty, it will multiply the 'price' by the quantity and
		 * return the result. If both 'now_price' and 'price'
		 */
		private function get_price( array $args ) {

			$qty = ! empty( $args['qty'] ) ? $args['qty'] : 1;

			return (
				! empty( $args['now_price'] )
				? (float) floatval( $args['now_price'] ) * $qty
				: (
					! empty( $args['price'] )
					? (float) floatval( $args['price'] ) * $qty
					: 0
				)
			);
		}

		/**
		 * The function `ASTA_CART` generates a filter bar for auctions with customizable options.
		 *
		 * @param array args An array of arguments that can be passed to customize the filter bar.
		 * $defaults = array(
		 *      'post_id' => $args['auction_id'] || $args['product_id'],
		 *      'price'   => 0,
		 *      'date'    => date format( 'D, d M Y H:i:s O' ),
		 *  );
		 */
		public function asta_cart( array $args = array() ) {

			$post_id = ( ! empty( $args['auction_id'] ) ? $args['auction_id'] : $args['product_id'] );

			$defaults = array(
				'post_id'      => $post_id,
				'price'        => 0,
				'auction_date' => '',
				'qty'          => '',
				'qty_label'    => __( 'amount', 'asta-template' ),
				'url'          => esc_url( get_permalink( $post_id ) ),
				'img'          => get_asta_thumbanil( $post_id ),
				'title'        => get_the_title( $post_id ),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['price']        = $this->get_price( $args );
			$args['auction_date'] = (
				! empty( $args['date'] )
				? DateTimeImmutable::createFromFormat( 'D, d M Y H:i:s O', $args['date'] )->format( 'd/m/Y' )
				: ''
			);

			asta_plugin_get_template_part(
				ASTA_TEMPLATES_PLUGIN_TEMPLATES,
				'cart/cart',
				'item',
				$args
			);
		}
	}

endif;

ASTA_CART::instance();
