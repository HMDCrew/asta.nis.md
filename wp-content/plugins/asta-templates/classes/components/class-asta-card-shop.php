<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_CARD_SHOP' ) ) :
	class ASTA_CARD_SHOP {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_CARD_SHOP ) ) {
				self::$instance = new ASTA_CARD_SHOP;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_card_shop', array( $this, 'asta_card_shop' ), 10, 1 );
		}


		/**
		 * The function "get_price" retrieves the price of a product based on its ID.
		 *
		 * @param int product_id The product ID is an integer value that uniquely identifies a product in the
		 * system. It is used to retrieve the price of a specific product.
		 *
		 * @return float product price as a float value.
		 */
		public function get_price( int $product_id ) {
			return floatval( get_post_meta( $product_id, 'price', true ) );
		}


		public function asta_card_shop( array $args = array() ) {

			$product_id = get_the_ID();

			$defaults = array(
				'product_id'    => $product_id,
				'price'         => $this->get_price( $product_id ),
				'post_excerpt'  => get_post_field( 'post_excerpt', $product_id ),
				'post_classes'  => esc_attr( implode( ' ', get_post_class( 'card' ) ) ),
				'product_url'   => esc_url( get_permalink() ),
				'product_name'  => get_the_title(),
				'thumbnail'     => get_asta_thumbanil( $product_id ),
				'author_id'     => (int) get_post_field( 'post_author', $product_id ),
				'details_label' => __( 'Details', 'asta-child' ),
				'edit_label'    => __( 'Edit', 'asta-child' ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'archive/card', 'shop', $args );
		}
	}

endif;

ASTA_CARD_SHOP::instance();
