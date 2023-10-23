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
		 * The function `ASTA_CART` generates a filter bar for auctions with customizable options.
		 *
		 * @param array args An array of arguments that can be passed to customize the filter bar.
		 * $defaults = array(
		 *      'auction_id' => $args['auction_id'] || $args['product_id'],
		 *      'price'  => 0,
		 *      'date'       => date format( 'D, d M Y H:i:s O' ),
		 *  );
		 */
		public function asta_cart( array $args = array() ) {

			$post_id = ( ! empty( $args['auction_id'] ) ? $args['auction_id'] : $args['product_id'] );

			$defaults = array(
				'post_id'      => $post_id,
				'price'        => (
					! empty( $args['now_price'] )
					? $args['now_price']
					: (
						! empty( $args['price'] )
						? $args['price']
						: 0
					)
				),
				'auction_date' => (
					! empty( $args['date'] )
					? DateTimeImmutable::createFromFormat( 'D, d M Y H:i:s O', $args['date'] )->format( 'd/m/Y' )
					: ''
				),
				'url'          => esc_url( get_permalink( $post_id ) ),
				'img'          => get_asta_thumbanil( $post_id ),
				'title'        => get_the_title( $post_id ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'cart/cart', 'item', $args );
		}
	}

endif;

ASTA_CART::instance();
