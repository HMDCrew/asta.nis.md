<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_ORDER' ) ) :
	class ASTA_ORDER {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_ORDER ) ) {
				self::$instance = new ASTA_ORDER;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_order_item', array( $this, 'asta_order' ), 10, 1 );
		}


		/**
		 * The function `asta_order` generates a filter bar for auctions with customizable options.
		 *
		 * @param array args An array of arguments that can be passed to customize the filter bar.
		 * $defaults = array(
		 *      'order_id' => $args['ID'],
		 *      'title'    => $args['post_title'],
		 * );
		 */
		public function asta_order( array $args = array() ) {

			$defaults = array(
				'order_id'       => $args['ID'],
				'title'          => explode( ', ', $args['post_title'] )[0],
				'details'        => get_post_meta( $args['ID'], 'details', true ),
				'payment_status' => get_post_meta( $args['ID'], 'payment_status', true ),
				'oreder_link'    => ASTA_THEME_ORDERS::get_meta( $args['ID'], 'oreder_link' ),
				'date'           => get_the_date( 'd/m/Y', $args['ID'] ),
				'pay_label'      => __( 'Pay', 'asta-template' ),
				'pay_now_label'  => __( 'Pay now', 'asta-template' ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'order/order', 'item', $args );
		}
	}

endif;

ASTA_ORDER::instance();
