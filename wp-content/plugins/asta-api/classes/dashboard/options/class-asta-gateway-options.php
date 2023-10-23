<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ASTA_Gateway_Options' ) ) :

	class ASTA_Gateway_Options extends SEC {

		private static $instance;

		private $options;

		/**
		 * It creates a singleton instance of the class.
		 *
		 * @return ASTA_Gateway_Options instance of the class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_Gateway_Options ) ) {
				self::$instance = new ASTA_Gateway_Options;
				self::$instance->set_up_class_variable();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * It sets up the class variables
		 */
		public function set_up_class_variable() {
			$opt           = get_option( 'asta_payments_element' );
			$this->options = ( is_string( $opt ) ? $this->array_option_dec( $opt ) : $opt );
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			if ( ! is_admin() ) {
				return false;
			}

			add_action( 'admin_init', array( $this, 'plugin_register_settings' ) );
			add_action( 'admin_post_save_asta_dashboard', array( $this, 'save_asta_dashboard' ) );
		}


		/**
		 * This function registers and adds settings fields for payment gateways in a WordPress plugin.
		 */
		public function plugin_register_settings() {

			register_setting( 'asta_payments_group', 'asta_payments_element' );

			add_settings_section(
				'asta_payments_element_id',
				__( 'Payment gateway selected', 'asta-api' ),
				array( $this, 'render_section_gateways' ),
				'asta_payments'
			);

			add_settings_field(
				'payment_stripe',
				__( 'Payment Stripe', 'asta-api' ),
				array( $this, 'render_section_stripe' ),
				'asta_payments',
				'asta_payments_element_id'
			);
			add_settings_field(
				'payment_revolut',
				__( 'Payment Revolut', 'asta-api' ),
				array( $this, 'render_section_revolut' ),
				'asta_payments',
				'asta_payments_element_id'
			);
		}


		/**
		 * This PHP function renders a payment gateway section with stored options.
		 */
		public function render_section_gateways() {
			wpr_asta_get_template_part(
				'payments/gateway',
				'options',
				array(
					'stored_option' => $this->options,
				)
			);
		}


		/**
		 * This PHP function renders a Stripe payment section using stored options.
		 */
		public function render_section_stripe() {
			wpr_asta_get_template_part(
				'payments/payment',
				'stripe',
				array(
					'stored_option' => $this->options,
				)
			);
		}


		/**
		 * This PHP function renders a payment section for Revolut with stored options.
		 */
		public function render_section_revolut() {
			wpr_asta_get_template_part(
				'payments/payment',
				'revolut',
				array(
					'stored_option' => $this->options,
				)
			);
		}


		/**
		 * It saves the order status mapping to the database
		 */
		public function save_asta_dashboard() {

			if ( ! isset( $_POST['asta_payments_element'] ) || ! is_admin() ) {
				return;
			}

			status_header( 200 );

			update_option(
				'asta_payments_element',
				$this->array_option_enc(
					$this->clean_array_key_val( $_POST['asta_payments_element'] )
				)
			);

			wp_redirect( $_SERVER['HTTP_REFERER'] );
			exit();
		}
	}

endif;

ASTA_Gateway_Options::instance();
