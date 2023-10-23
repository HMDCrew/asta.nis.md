<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_FILTER_BAR' ) ) :
	class ASTA_FILTER_BAR {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_FILTER_BAR ) ) {
				self::$instance = new ASTA_FILTER_BAR;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * This function adds meta boxes and enqueues styles for the order page in the WordPress admin panel.
		 */
		public function hooks() {
			add_action( 'asta_filter_bar', array( $this, 'asta_filter_bar' ), 10, 1 );
		}


		/**
		 * This function retrieves all categories for auctions in WordPress, with an option to hide empty
		 * categories.
		 *
		 * @param bool hide_empty This parameter is used to determine whether to include empty categories in the
		 * result or not. If set to true, only categories with at least one auction item will be returned. If
		 * set to false, all categories, including empty ones, will be returned.
		 *
		 * @return array of terms from the 'asta_category' taxonomy. The terms may be empty or not
		 * depending on the value of the  parameter. If the  array is not empty, it is
		 * returned. Otherwise, an empty array is returned.
		 */
		private function get_categories( $hide_empty = false ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'asta_category',
					'hide_empty' => $hide_empty,
				)
			);

			return is_array( $terms ) ? $terms : array();
		}


		/**
		 * The function retrieves the minimum or maximum price of an auction post type and caches the result
		 * for future use.
		 *
		 * @param string max_or_min This parameter is a string that determines whether the function should
		 * return the maximum or minimum price range. It can have two possible values: "max" or "min". If
		 * "max" is passed, the function will return the highest price range, and if "min" is passed, it will
		 * return
		 *
		 * @return float cached posts if they exist, or the minimum or maximum price of an auction post
		 * depending on the argument passed to the function (``). The minimum or maximum price is
		 * determined by querying the `auctions` post type and ordering the results by the `price` meta
		 * value in ascending or descending order. The function then caches the result using the `
		 */
		private function helper_price_range( string $max_or_min, string $post_type = 'auctions' ) {

			$key = 'post_' . $max_or_min;

			$cached_posts = wp_cache_get( $key );

			if ( ! empty( $cached_posts ) ) {
				return $cached_posts;
			}

			$posts = get_posts(
				array(
					'fields'      => 'ids',
					'numberposts' => 1,
					'post_type'   => $post_type,
					'meta_key'    => 'price',
					'orderby'     => 'meta_value_num',
					'order'       => ( 'min' === $max_or_min ? 'ASC' : 'DESC' ),
				)
			);

			$post = reset( $posts );

			$value = floatval(
				get_post_meta( $post, 'price', true )
			);

			wp_cache_set( $key, $value );

			return $value;
		}


		/**
		 * The function `asta_filter_bar` generates a filter bar for auctions with customizable options.
		 *
		 * @param array args An array of arguments that can be passed to customize the filter bar.
		 *  $args_default = array(
		 *      'categories'     => get_terms() : array<WP_Term>,
		 *      'slider_min'     => float,
		 *      'slider_max'     => float,
		 *      'visibility'     => array(
		 *          'search'   => true,
		 *          'category' => true,
		 *          'date'     => true,
		 *          'price'    => true,
		 *      ),
		 *      'search_label'   => __( 'Search', 'asta-template' ),
		 *      'category_label' => __( 'Auction type', 'asta-template' ),
		 *      'date_label'     => __( 'Period date', 'asta-template' ),
		 *  );
		 */
		public function asta_filter_bar( array $args = array() ) {

			$archive = get_queried_object();

			$defaults = array(
				'categories'     => $this->get_categories(),
				'slider_min'     => $this->helper_price_range( 'min', get_post_type() ),
				'slider_max'     => $this->helper_price_range( 'max', get_post_type() ),
				'visibility'     => array(
					'search'   => true,
					'category' => true,
					'date'     => true,
					'price'    => true,
				),
				'search_label'   => __( 'Search', 'asta-template' ),
				'category_label' => __( 'Type of thing', 'asta-template' ),
				'date_label'     => __( 'Period date', 'asta-template' ),
			);

			$args = wp_parse_args( $args, $defaults );

			asta_get_template_part( 'archive/filter', 'bar', $args );
		}
	}

endif;

ASTA_FILTER_BAR::instance();
