<?php
/**
 * Plugin Name: Asta Templates
 * Plugin URI: #
 * Description: Asta Template hooks
 * Version: 0.0.1
 * Author: Andrei Leca
 * Author URI:
 * Text Domain: asta-template
 * License: MIT
 */

namespace ASTA_TEMPLATES;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ASTA_TEMPLATES' ) ) :

	class ASTA_TEMPLATES {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_TEMPLATES ) ) {
				self::$instance = new ASTA_TEMPLATES;
				self::$instance->constants();

				// Plugin Setup
				add_action( 'setup_theme', array( self::$instance, 'includes' ), 15, 0 );
			}

			return self::$instance;
		}

		/**
		 * Constants
		 */
		public function constants() {
			// Plugin version
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_VERSION' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_VERSION', '0.0.1' );
			}

			// Plugin file
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_FILE' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_FILE', __FILE__ );
			}

			// Plugin basename
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_BASENAME' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_BASENAME', plugin_basename( ASTA_TEMPLATES_PLUGIN_FILE ) );
			}

			// Plugin directory path
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_DIR_PATH' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( ASTA_TEMPLATES_PLUGIN_FILE ) ) );
			}

			// Plugin directory URL
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_DIR_URL' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( ASTA_TEMPLATES_PLUGIN_FILE ) ) );
			}

			// Plugin URL assets
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_ASSETS' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_ASSETS', trailingslashit( ASTA_TEMPLATES_PLUGIN_DIR_URL . 'assets' ) );
			}

			// Plugin directory classes
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_CLASSES' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_CLASSES', trailingslashit( ASTA_TEMPLATES_PLUGIN_DIR_PATH . 'classes' ) );
			}

			// Plugin directory components
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_COMPONENTS' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_COMPONENTS', trailingslashit( ASTA_TEMPLATES_PLUGIN_CLASSES . 'components' ) );
			}

			// Plugin directory templates
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_TEMPLATES' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_TEMPLATES', trailingslashit( ASTA_TEMPLATES_PLUGIN_DIR_PATH . 'templates' ) );
			}

			// Plugin directory vendor
			if ( ! defined( 'ASTA_TEMPLATES_PLUGIN_COMPOSER' ) ) {
				define( 'ASTA_TEMPLATES_PLUGIN_COMPOSER', trailingslashit( ASTA_TEMPLATES_PLUGIN_DIR_PATH . 'vendor' ) );
			}
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once ASTA_TEMPLATES_PLUGIN_COMPOSER . 'autoload.php';

			// Helpers functions
			require_once ASTA_TEMPLATES_PLUGIN_DIR_PATH . 'helpers.php';

			// Components hooks
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-filter-bar.php';
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-card-auction.php';
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-card-shop.php';
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-cart.php';
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-order.php';
			require_once ASTA_TEMPLATES_PLUGIN_COMPONENTS . 'class-asta-gallery.php';

			\ASTA_FILTER_BAR::instance();
			\ASTA_CARD_AUCTION::instance();
			\ASTA_CARD_SHOP::instance();
			\ASTA_CART::instance();
			\ASTA_ORDER::instance();
			\ASTA_GALLERY::instance();
		}
	}

endif;

ASTA_TEMPLATES::instance();
