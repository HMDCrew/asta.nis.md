<?php
/**
 * Plugin Name: Asta Core
 * Plugin URI: #
 * Description: Asta Core functions
 * Version: 0.0.1
 * Author: Andrei Leca
 * Author URI:
 * Text Domain: asta-template
 * License: MIT
 */

namespace ASTA_CORE;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ASTA_CORE' ) ) :

	class ASTA_CORE {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_CORE ) ) {
				self::$instance = new ASTA_CORE();
				self::$instance->constants();

				// Plugin Setup
				add_action( 'setup_theme', array( self::$instance, 'includes' ), 5, 0 );
			}

			return self::$instance;
		}

		/**
		 * Constants
		 */
		public function constants() {
			// Plugin version
			if ( ! defined( 'ASTA_CORE_PLUGIN_VERSION' ) ) {
				define( 'ASTA_CORE_PLUGIN_VERSION', '0.0.1' );
			}

			// Plugin file
			if ( ! defined( 'ASTA_CORE_PLUGIN_FILE' ) ) {
				define( 'ASTA_CORE_PLUGIN_FILE', __FILE__ );
			}

			// Plugin basename
			if ( ! defined( 'ASTA_CORE_PLUGIN_BASENAME' ) ) {
				define( 'ASTA_CORE_PLUGIN_BASENAME', plugin_basename( ASTA_CORE_PLUGIN_FILE ) );
			}

			// Plugin directory path
			if ( ! defined( 'ASTA_CORE_PLUGIN_DIR_PATH' ) ) {
				define( 'ASTA_CORE_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( ASTA_CORE_PLUGIN_FILE ) ) );
			}

			// Plugin directory URL
			if ( ! defined( 'ASTA_CORE_PLUGIN_DIR_URL' ) ) {
				define( 'ASTA_CORE_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( ASTA_CORE_PLUGIN_FILE ) ) );
			}

			// Plugin directory inc
			if ( ! defined( 'ASTA_CORE_PLUGIN_INC' ) ) {
				define( 'ASTA_CORE_PLUGIN_INC', trailingslashit( ASTA_CORE_PLUGIN_DIR_PATH . 'inc' ) );
			}

			// Plugin directory auctions
			if ( ! defined( 'ASTA_CORE_PLUGIN_AUCTIONS' ) ) {
				define( 'ASTA_CORE_PLUGIN_AUCTIONS', trailingslashit( ASTA_CORE_PLUGIN_DIR_PATH . 'auctions' ) );
			}

			// Plugin directory shop
			if ( ! defined( 'ASTA_CORE_PLUGIN_SHOP' ) ) {
				define( 'ASTA_CORE_PLUGIN_SHOP', trailingslashit( ASTA_CORE_PLUGIN_DIR_PATH . 'shop' ) );
			}

			// Plugin directory user
			if ( ! defined( 'ASTA_CORE_PLUGIN_USER' ) ) {
				define( 'ASTA_CORE_PLUGIN_USER', trailingslashit( ASTA_CORE_PLUGIN_DIR_PATH . 'user' ) );
			}
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once ASTA_CORE_PLUGIN_INC . 'class-sec.php';

			require_once ASTA_CORE_PLUGIN_AUCTIONS . 'class-asta-auction.php';

			require_once ASTA_CORE_PLUGIN_USER . 'class-asta-user.php';

			// Helpers functions
			require_once ASTA_CORE_PLUGIN_DIR_PATH . 'helpers.php';
		}
	}

endif;

ASTA_CORE::instance();
