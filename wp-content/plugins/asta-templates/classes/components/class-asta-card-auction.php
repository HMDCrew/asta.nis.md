<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_CARD_AUCTION' ) ) :
	class ASTA_CARD_AUCTION {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_CARD_AUCTION ) ) {
				self::$instance = new ASTA_CARD_AUCTION();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_card_auction', array( $this, 'asta_card_auction' ), 10, 1 );
		}


		/**
		 * The function `asta_card_auction` is used to display a card for an auction with various details and
		 * options.
		 *
		 * @param array args An array of additional arguments that can be passed to the function. These
		 * arguments will override the default values.
		 */
		public function asta_card_auction( array $args = array() ) {

			$auction_type = get_asta_category( get_the_ID() );

			$defaults = array(
				'post_id'         => get_the_ID(),
				'auction_date'    => ASTA_AUCTION::get_auction_date( get_the_ID() ),
				'price'           => ASTA_AUCTION::get_auction_last_price( get_the_ID() ),
				'price_increment' => asta_esc_meta( get_the_ID(), 'price_increment' ),
				'auction_type'    => $auction_type,
				'category_link'   => ! empty( $auction_type['id'] ) ? get_category_link( $auction_type['id'] ) : '#',
				'post_excerpt'    => get_post_field( 'post_excerpt', get_the_ID() ),
				'post_classes'    => esc_attr( implode( ' ', get_post_class( 'card' ) ) ),
				'post_link'       => esc_url( get_permalink() ),
				'url_thumbnail'   => get_asta_thumbanil( get_the_ID() ),
				'title'           => get_the_title(),
				'author_id'       => (int) get_post_field( 'post_author', get_the_ID() ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_plugin_get_template_part(
				ASTA_TEMPLATES_PLUGIN_TEMPLATES,
				'archive/card',
				'auction',
				$args
			);
		}
	}

endif;

ASTA_CARD_AUCTION::instance();
