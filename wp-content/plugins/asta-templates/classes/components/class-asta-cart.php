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
		 *      'auction_id' => $args['auction_id'],
		 *      'now_price'  => 0,
		 *      'date'       => date format( 'D, d M Y H:i:s O' ),
		 *  );
		 */
		public function asta_cart( array $args = array() ) {

			$defaults = array(
				'auction_id'   => $args['auction_id'],
				'now_price'    => 0,
				'auction_date' => (
					! empty( $args['date'] )
					? DateTimeImmutable::createFromFormat( 'D, d M Y H:i:s O', $args['date'] )->format( 'd/m/Y' )
					: ''
				),
				'url'          => esc_url( get_permalink( $args['auction_id'] ) ),
				'img'          => get_asta_thumbanil( $args['auction_id'] ),
				'title'        => get_the_title( $args['auction_id'] ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'cart/cart', 'item', $args );
		}
	}

endif;

ASTA_CART::instance();
