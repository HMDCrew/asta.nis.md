<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_GUTENBERG_METABOXES' ) ) :
	class ASTA_GUTENBERG_METABOXES {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_GUTENBERG_METABOXES ) ) {
				self::$instance = new ASTA_GUTENBERG_METABOXES;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'add_meta_boxes', array( $this, 'auction_metaboxes' ) );
			//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_order_metabox_style' ) );
		}


		/**
		 * This function enqueues a CSS style file for the orders post type metabox.
		 *
		 * @param string hook The hook parameter is a string that specifies the name of the current WordPress admin
		 * page. It is used to determine whether or not to enqueue the stylesheet. In this case, the function
		 * checks if the current page is either "post.php" or "post-new.php" and if the post type is "
		 */
		public function enqueue_order_metabox_style( string $hook ) {

			$screen = get_current_screen();

			if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'orders' === $screen->post_type ) {
				wp_register_style( 'order-metabox', ASTA_API_PLUGIN_ASSETS . '/css/style.css' );
				wp_enqueue_style( 'order-metabox' );
			}
		}


		/**
		 * This function adds a metabox for displaying order details in the WordPress admin panel.
		 */
		public function auction_metaboxes() {
			add_meta_box( 'auction_details_metabox', __( 'Auction Details', 'asta-api' ), array( $this, 'render_auction_details_metabox' ), 'auctions', 'normal', 'high' );
		}


		/**
		 * This PHP function renders the order details metabox with information about the payment status,
		 * payment intent, buyer, amount, currency, and cart.
		 *
		 * @param WP_Post order The order object that contains information about the order, such as the order ID,
		 * customer details, and order status.
		 */
		public function render_auction_details_metabox( WP_Post $auction ) {

			$start_date      = get_post_meta( $auction->ID, 'start_date', true );
			$end_date        = get_post_meta( $auction->ID, 'end_date', true );
			$gallery         = get_post_meta( $auction->ID, 'auction_gallery', true );
			$auction_date    = apply_filters( 'wpr_get_auction_date', $auction->ID );
			$price           = apply_filters( 'wpr_esc_auction_meta', $auction->ID, 'price' );
			$price_increment = floatval( apply_filters( 'wpr_esc_auction_meta', $auction->ID, 'price_increment' ) );

			wpr_asta_get_template_part(
				'auctions/auction',
				'metabox',
				array(
					'start_date'      => $start_date,
					'end_date'        => $end_date,
					'gallery'         => $gallery,
					'auction_date'    => $auction_date,
					'price'           => $price,
					'price_increment' => $price_increment,
				)
			);
		}
	}

endif;

ASTA_GUTENBERG_METABOXES::instance();
