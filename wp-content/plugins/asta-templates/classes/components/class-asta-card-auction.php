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
				self::$instance = new ASTA_CARD_AUCTION;
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
		 * The function retrieves the start and end dates of an auction and returns them in a formatted string.
		 *
		 * @param int auction_id The ID of the auction post for which the start and end dates are being retrieved.
		 *
		 * @return string formatted string that includes the start and end dates of an auction, based on the
		 * provided auction ID. If no auction ID is provided, an empty string is returned.
		 */
		private function get_auction_date( int $auction_id ) {

			if ( $auction_id ) {

				$start_date = get_post_meta( $auction_id, 'start_date', true );
				$end_date   = get_post_meta( $auction_id, 'end_date', true );

				if ( '' !== $start_date && '' !== $end_date ) {
					$start_date = new DateTimeImmutable( $start_date );
					$end_date   = new DateTimeImmutable( $end_date );

					return esc_html( sprintf( '%s to %s', $start_date->format( 'd/m/Y' ), $end_date->format( 'd/m/Y' ) ) );
				}
			}

			return '';
		}


		/**
		 * The function retrieves the last price of an auction as a float value.
		 *
		 * @param int auction_id The ID of the auction post for which we want to retrieve the last price.
		 *
		 * @return float last price of an auction as a float value. It retrieves the value from the
		 * 'auction_price' meta field of the post with the given .
		 */
		private function get_auction_last_price( int $auction_id ) {
			return floatval( get_post_meta( $auction_id, 'auction_price', true ) );
		}


		/**
		 * The function returns the value of a specified meta key for a given auction ID, with HTML characters
		 * escaped.
		 *
		 * @param int auction_id An integer representing the ID of the auction post for which the meta value is
		 * being retrieved.
		 * @param string key The  parameter is a string that represents the name of the meta field to
		 * retrieve the value from. It is used in conjunction with the  parameter to retrieve the
		 * meta value associated with a specific auction post. The function then returns the escaped HTML value
		 * of the meta field.
		 *
		 * @return string This function returns the value of a specific meta key for a given auction post ID, after
		 * sanitizing it with `esc_html()`. If the auction ID is not provided or does not exist, an empty
		 * string is returned.
		 */
		private function esc_auction_meta( int $auction_id, string $key ) {

			if ( $auction_id ) {
				return esc_html(
					get_post_meta( $auction_id, $key, true )
				);
			}

			return '';
		}


		/**
		 * This PHP function retrieves the auction type (category) based on the provided auction ID.
		 *
		 * @param int auction_id This is an integer parameter representing the ID of the auction for which we
		 * want to retrieve the auction type.
		 *
		 * @return array with the ID and name of the first category term associated with the given auction
		 * ID. If the auction ID is not provided or no category term is found, an empty array is returned.
		 */
		private function get_auction_type( int $auction_id ) {

			if ( $auction_id && 0 !== $auction_id ) {

				$terms = get_the_terms( $auction_id, 'auction_category' );

				if ( ! empty( $terms ) ) {

					$term = reset( $terms );

					return array(
						'id'   => $term->term_id,
						'name' => $term->name,
					);
				}
			}

			return array();
		}


		/**
		 * The function `asta_card_auction` is used to display a card for an auction with various details and
		 * options.
		 *
		 * @param array args An array of additional arguments that can be passed to the function. These
		 * arguments will override the default values.
		 */
		public function asta_card_auction( array $args = array() ) {

			$auction_type = $this->get_auction_type( get_the_ID() );

			$defaults = array(
				'post_id'         => get_the_ID(),
				'auction_date'    => $this->get_auction_date( get_the_ID() ),
				'baze_price'      => $this->get_auction_last_price( get_the_ID() ),
				'price_increment' => $this->esc_auction_meta( get_the_ID(), 'price_increment' ),
				'auction_type'    => $auction_type,
				'post_excerpt'    => get_post_field( 'post_excerpt', get_the_ID() ),
				'post_classes'    => esc_attr( implode( ' ', get_post_class( 'card' ) ) ),
				'post_link'       => esc_url( get_permalink() ),
				'url_thumbnail'   => get_asta_thumbanil( get_the_ID() ),
				'title'           => get_the_title(),
				'category_link'   => get_category_link( $auction_type['id'] ),
				'author_id'       => (int) get_post_field( 'post_author', get_the_ID() ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'archive/card', 'auction', $args );
		}
	}

endif;

ASTA_CARD_AUCTION::instance();
