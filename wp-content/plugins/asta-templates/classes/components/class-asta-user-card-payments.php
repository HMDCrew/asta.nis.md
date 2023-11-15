<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_USER_CARD_PAYMENTS' ) ) :
	class ASTA_USER_CARD_PAYMENTS {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_USER_CARD_PAYMENTS ) ) {
				self::$instance = new ASTA_USER_CARD_PAYMENTS();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_user_credit_cards', array( $this, 'asta_user_credit_cards' ), 10, 1 );
		}

		public function asta_user_credit_cards( array $args = array() ) {

			$defaults = array(
				'user_id'     => '',
				'customer_id' => (
					empty( $args['customer_id'] ) && ! empty( $args['user_id'] )
					? get_user_meta( $args['user_id'], 'asta_customer_id', true )
					: ''
				),
			);

			$args = wp_parse_args( $args, $defaults );

			if ( ! empty( $args['customer_id'] ) ) {

				$sources = ASTA_STRIPE::client()->customers->allSources(
					$args['customer_id'],
					array(
						'object' => 'card',
						'limit'  => 10,
					)
				);

				$args['credit_cards'] = $sources->autoPagingIterator();

				asta_plugin_get_template_part(
					ASTA_TEMPLATES_PLUGIN_TEMPLATES,
					'user/user',
					'cards',
					$args
				);
			}
		}
	}
endif;

ASTA_USER_CARD_PAYMENTS::instance();
