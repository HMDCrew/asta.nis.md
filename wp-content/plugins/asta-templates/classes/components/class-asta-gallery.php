<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_GALLERY' ) ) :
	class ASTA_GALLERY {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_GALLERY ) ) {
				self::$instance = new ASTA_GALLERY();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_gallery_thumbs_template', array( $this, 'asta_gallery_thumbs_template' ) );
			add_action( 'asta_gallery_template', array( $this, 'asta_gallery_template' ) );
		}


		/**
		 * The function `asta_gallery_template` displays a gallery of images for an auction/product, with options
		 * for the URLs of the images and the order of the slides.
		 *
		 * @param array args  is an array of optional parameters that can be passed to the
		 * `asta_gallery_template()` function. It contains the following keys:
		 *  $args_default = array(
		 *      'urls'         => array<string>,
		 *      'last_slide'   => string,
		 *      'slide_after'  => string,
		 *      'slide_before' => string,
		 *  );
		 */
		public function asta_gallery_template( array $args = array() ) {

			$defaults = array(
				'urls'         => (
					isset( $args['urls'] )
						? $args['urls']
						: (
							isset( $args['post_id'] ) && $args['post_id']
								? get_asta_gallery( $args['post_id'] )
								: ''
						)
				),
				'last_slide'   => '',
				'slide_after'  => '',
				'slide_before' => '',
			);

			$args = wp_parse_args( $args, $defaults );

			asta_plugin_get_template_part(
				ASTA_TEMPLATES_PLUGIN_TEMPLATES,
				'auction/gallery',
				'',
				$args
			);
		}


		/**
		 * This is a PHP function that generates a gallery of thumbnails for an auction/product template.
		 *
		 * @param array args  is an array of optional parameters that can be passed to the function
		 * asta_gallery_thumbs_template(). It contains the following keys:
		 *  $args_default = array(
		 *      'urls'         => array<string>,
		 *      'last_slide'   => string,
		 *      'slide_after'  => string,
		 *      'slide_before' => string,
		 *  );
		 */
		public function asta_gallery_thumbs_template( array $args = array() ) {

			$defaults = array(
				'urls'         => (
					isset( $args['urls'] )
						? $args['urls']
						: (
							isset( $args['post_id'] ) && $args['post_id']
								? get_asta_gallery( $args['post_id'] )
								: array()
						)
				),
				'last_slide'   => '',
				'slide_after'  => '',
				'slide_before' => '',
			);

			$args = wp_parse_args( $args, $defaults );

			asta_plugin_get_template_part(
				ASTA_TEMPLATES_PLUGIN_TEMPLATES,
				'auction/gallery',
				'thumbs',
				$args
			);
		}
	}

endif;

ASTA_GALLERY::instance();
