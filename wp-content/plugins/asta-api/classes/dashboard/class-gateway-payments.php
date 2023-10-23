<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Gateway_Payments' ) ) :

	class Gateway_Payments {


		private static $instance;


		/**
		 * It creates a singleton instance of the class.
		 *
		 * @return Gateway_Payments instance of the class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Gateway_Payments ) ) {
				self::$instance = new Gateway_Payments;
				self::$instance->hooks();
				self::$instance->includes();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			if ( ! is_admin() ) {
				return false;
			}

			add_action( 'admin_head', array( $this, 'menu_style' ) );
			add_action( 'admin_menu', array( $this, 'plugin_settings_menu_page' ), 20 );
		}


		/**
		 * It adds a style tag to the admin menu page
		 */
		public function menu_style() {

			$menu_url = menu_page_url( 'panasonic', false );

			if ( ! $menu_url ) {
				echo
				'<style>
					.toplevel_page_asta .wp-menu-image.dashicons-before img { max-width: 20px }
					.toplevel_page_asta .wp-first-item { display: none }
				</style>';
			}
		}


		/**
		 * It adds a submenu page to the Tools menu
		 */
		public function plugin_settings_menu_page() {

			$menu_url = menu_page_url( 'asta', false );

			if ( ! $menu_url ) {
				add_menu_page(
					__( 'Asta', 'asta-api' ),
					__( 'Asta', 'asta-api' ),
					'manage_options',
					'asta',
					array( $this, 'asta_init_page' ),
					'/wp-content/plugins/asta-api/assets/img/logo_white-1.svg',
					10
				);
			}

			add_submenu_page(
				'asta',
				__( 'Asta payments gateway', 'asta-api' ),
				__( 'Asta payments gateway', 'asta-api' ),
				'manage_options',
				'asta-gateway-payments',
				array( $this, 'plugin_settings_page_content' ),
				100
			);
		}


		/**
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once ASTA_API_PLUGIN_CLASSES . 'dashboard/options/class-asta-gateway-options.php';

			\ASTA_Gateway_Options::instance();
		}


		/**
		 * It redirects the user to the settings page after they activate the plugin
		 */
		public function asta_init_page() {

			$foo = menu_page_url( 'asta-gateway-payments', false );

			echo sprintf( '<script>window.location.href = "%s";</script>', $foo );
			exit;
		}


		/**
		 * It's a function that creates a new page in the WordPress admin area
		 */
		public function plugin_settings_page_content() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'asta-api' ) );
			}

			wpr_asta_get_template_part( 'asta', 'dashboard' );
		}
	}

endif;

Gateway_Payments::instance();
