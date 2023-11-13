<?php
/**
 * Plugin Name: Asta API
 * Plugin URI: #
 * Description: Asta api routes
 * Version: 0.0.1
 * Author: Andrei Leca
 * Author URI:
 * Text Domain: asta-api
 * License: MIT
 */

namespace ASTA_API;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ASTA_API' ) ) :

	class ASTA_API {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_API ) ) {
				self::$instance = new ASTA_API();
				self::$instance->constants();

				// Plugin Setup
				// add_action( 'after_setup_theme', array( self::$instance, 'includes' ), 10, 0 );
				add_action( 'setup_theme', array( self::$instance, 'includes' ), 10, 0 );
			}

			return self::$instance;
		}

		/**
		 * Constants
		 */
		public function constants() {
			// Plugin version
			if ( ! defined( 'ASTA_API_PLUGIN_VERSION' ) ) {
				define( 'ASTA_API_PLUGIN_VERSION', '0.0.1' );
			}

			// Plugin file
			if ( ! defined( 'ASTA_API_PLUGIN_FILE' ) ) {
				define( 'ASTA_API_PLUGIN_FILE', __FILE__ );
			}

			// Plugin basename
			if ( ! defined( 'ASTA_API_PLUGIN_BASENAME' ) ) {
				define( 'ASTA_API_PLUGIN_BASENAME', plugin_basename( ASTA_API_PLUGIN_FILE ) );
			}

			// Plugin directory path
			if ( ! defined( 'ASTA_API_PLUGIN_DIR_PATH' ) ) {
				define( 'ASTA_API_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( ASTA_API_PLUGIN_FILE ) ) );
			}

			// Plugin directory URL
			if ( ! defined( 'ASTA_API_PLUGIN_DIR_URL' ) ) {
				define( 'ASTA_API_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( ASTA_API_PLUGIN_FILE ) ) );
			}

			// Plugin URL assets
			if ( ! defined( 'ASTA_API_PLUGIN_ASSETS' ) ) {
				define( 'ASTA_API_PLUGIN_ASSETS', trailingslashit( ASTA_API_PLUGIN_DIR_URL . 'assets' ) );
			}

			// Plugin directory classes
			if ( ! defined( 'ASTA_API_PLUGIN_CLASSES' ) ) {
				define( 'ASTA_API_PLUGIN_CLASSES', trailingslashit( ASTA_API_PLUGIN_DIR_PATH . 'classes' ) );
			}

			// Plugin directory templates
			if ( ! defined( 'ASTA_API_PLUGIN_TEMPLATES' ) ) {
				define( 'ASTA_API_PLUGIN_TEMPLATES', trailingslashit( ASTA_API_PLUGIN_DIR_PATH . 'templates' ) );
			}

			// Plugin directory logs URL
			if ( ! defined( 'ASTA_API_PLUGIN_COMPOSER' ) ) {
				define( 'ASTA_API_PLUGIN_COMPOSER', trailingslashit( ASTA_API_PLUGIN_DIR_PATH . 'vendor' ) );
			}
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once ASTA_API_PLUGIN_DIR_PATH . 'vendor/autoload.php';

			// Helpers functions
			require_once ASTA_API_PLUGIN_DIR_PATH . 'helpers.php';

			// Include class helpers
			require_once ASTA_API_PLUGIN_DIR_PATH . 'inc/class-wpr-editorjs-gutenberg.php';
			require_once ASTA_API_PLUGIN_DIR_PATH . 'inc/class-sec.php';

			// API cart
			require_once ASTA_API_PLUGIN_CLASSES . 'cart/class-asta-theme-cart.php';

			// API ednpoints auction
			require_once ASTA_API_PLUGIN_CLASSES . 'user/class-asta-theme-auth.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'user/class-asta-theme-profile.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'auction/class-asta-theme-edit-auction.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'auction/class-asta-theme-get-auction.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'chackout/class-asta-theme-bids.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'chackout/class-asta-theme-sold-process.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'chackout/class-asta-theme-chackout.php';

			// API ednpoints shop
			require_once ASTA_API_PLUGIN_CLASSES . 'shop/class-asta-theme-edit-product.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'shop/class-asta-theme-get-product.php';

			// Dashboard
			require_once ASTA_API_PLUGIN_CLASSES . 'dashboard/class-gateway-payments.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'orders/class-asta-theme-orders.php';
			require_once ASTA_API_PLUGIN_CLASSES . 'auction/class-asta-gutenberg-metaboxes.php';

			\ASTA_THEME_SOLD_PROCESS::instance();
			\ASTA_THEME_AUTH::instance();
			\ASTA_THEME_PROFILE::instance();
			\ASTA_THEME_EDIT_AUCTION::instance();
			\ASTA_THEME_EDIT_PRODUCT::instance();
			\ASTA_THEME_GET_AUCTION::instance();
			\ASTA_THEME_BIDS::instance();
			\ASTA_THEME_CHACKOUT::instance();
			\Gateway_Payments::instance();
			\ASTA_THEME_ORDERS::instance();
			\ASTA_GUTENBERG_METABOXES::instance();
		}
	}

endif;

ASTA_API::instance();
