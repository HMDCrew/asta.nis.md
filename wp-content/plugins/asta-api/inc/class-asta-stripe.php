<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\StripeClient;

if ( ! class_exists( 'ASTA_STRIPE' ) ) :


	class ASTA_STRIPE {

		public static ?SEC $sec = null;

		public static function initialize() {
			self::$sec = self::$sec ?? new SEC();
		}

		/**
		 * The function retrieves the public, private, and signature keys for a specified payment gateway
		 * from the WordPress options table.
		 *
		 * @param string gateway The name of the payment gateway for which the keys are being retrieved.
		 *
		 * @return array containing the public key, private key, and signature key for a specified payment
		 * gateway.
		 */
		public static function get_gateway_keys( string $gateway ) {

			$opt_encrypt = get_option( 'asta_payments_element' );
			$opt         = self::$sec->array_option_dec( $opt_encrypt );

			$use_case = $opt[ $gateway ]['use_case'];

			return array(
				'public_key'    => $opt[ $gateway ][ $use_case . '_pub' ],
				'private_key'   => $opt[ $gateway ][ $use_case . '_priv' ],
				'signature_key' => ! empty( $opt[ $gateway ][ $use_case . '_signature' ] ) ? $opt[ $gateway ][ $use_case . '_signature' ] : '',
			);
		}


		/**
		 * This function retrieves a specific key from an array of gateway keys based on the provided gateway
		 * name.
		 *
		 * @param string gateway The name of the payment gateway for which the key is being retrieved.
		 * @param string key The  parameter is a string that represents the specific key that needs to be
		 * retrieved from the array of gateway keys.
		 *
		 * @return string|array|null value of the specified key from an array of gateway keys for a given gateway.
		 */
		public static function get_gateway_key( string $gateway, string $key ) {
			$gateway_keys = self::get_gateway_keys( $gateway );
			return $gateway_keys[ $key ];
		}


		/**
		 * The function returns a new instance of the StripeClient class with the specified API key and
		 * Stripe version.
		 *
		 * @return StripeClient instance of the StripeClient class.
		 */
		public static function client() {

			return new StripeClient(
				array(
					'api_key'        => self::get_gateway_key( 'stripe', 'private_key' ),
					'stripe_version' => '2020-08-27',
				)
			);
		}
	}

endif;

ASTA_STRIPE::initialize();
