<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_GET_PRODUCT' ) ) :
	class ASTA_THEME_GET_PRODUCT {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_GET_PRODUCT ) ) {
				self::$instance = new ASTA_THEME_GET_PRODUCT();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'asta_rest_api' ), 10 );
		}


		/**
		 * Registering a route for the REST API.
		 * @param \WP_REST_Server $server
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {

			// Get Auctions
			$server->register_route(
				'rest-api-wordpress',
				'/api-get-products',
				array(
					'methods'       => 'GET',
					'callback'      => array( $this, 'asta_get_product' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * This is a PHP function that prepares arguments for querying auctions based on various parameters
		 * such as number of posts, search term, date range, and type.
		 *
		 * @param int number_posts The number of auction posts to retrieve per page.
		 * @param int page The current page number of the query results.
		 * @param string search A string containing the search query to filter the auctions by.
		 * @param string date_rage The "date_rage" parameter is a string that contains two dates separated by
		 * a comma. These dates are used to filter the auctions based on their start and end dates. If the
		 * first date is "false", it means that there is no lower limit for the start date. If the second
		 * date
		 * @param int|string type The "type" parameter is used to filter the auctions by their category. It is passed as
		 * a taxonomy term ID to the "tax_query" parameter in the WP_Query arguments.
		 *
		 * @return array of arguments to be used in a WordPress query to retrieve auction posts. The
		 * arguments include the post type, post status, number of posts to retrieve, pagination information,
		 * meta key and order for sorting, and optional search, date range, and taxonomy filters.
		 */
		private function prepare_args( int $number_posts, int $page, string $search, string $price_range, $type, $user_id ) {

			$args = array(
				'post_type'      => 'shop',
				'post_status'    => 'publish',
				'posts_per_page' => $number_posts,
				'paged'          => $page,
				'orderby'        => 'modified',
				'order'          => 'DESC',
			);

			if ( ! empty( $search ) ) {
				$args['s'] = $search;
			}

			if ( ! empty( $type ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'asta_category',
						'field'    => 'id',
						'terms'    => $type,
					),
				);
			}

			if ( ! empty( $price_range ) ) {

				$price_range = explode( ',', $price_range );

				if ( 'false' !== $price_range[0] || 'false' !== $price_range[1] ) {
					$args['meta_query'][] = array(
						'key'     => 'price',
						'type'    => 'DECIMAL',
						'compare' => 'BETWEEN',
						'value'   => $price_range,
					);
				}
			}

			$args['meta_query'][] = array(
				'key'     => 'qty',
				'compare' => '>',
				'value'   => 0,
			);

			if ( ! empty( $user_id ) ) {

				unset( $args['post_status'] );
				unset( $args['meta_key'] );
				unset( $args['orderby'] );

				$args['author'] = $user_id;
			}

			return $args;
		}


		/**
		 * The function adds additional card requirements to an array of posts for display purposes.
		 *
		 * @param array posts An array of posts to be processed and returned with additional information.
		 * @param int curent_user_id The ID of the current user who is viewing the page.
		 *
		 * @return array of posts with additional information added to each post, such as the post
		 * thumbnail URL, author name and URL, auction date, base price, price increment, auction type, post
		 * excerpt, and a boolean value indicating whether the post belongs to the current user.
		 */
		private function card_requirements( array $posts, int $curent_user_id ) {

			foreach ( $posts as $key => $post ) {

				$posts[ $key ]['image']         = get_asta_thumbanil( $post['ID'] );
				$posts[ $key ]['author_name']   = get_the_author_meta( 'display_name', (int) $post['post_author'] );
				$posts[ $key ]['author_url']    = get_author_posts_url( (int) $post['post_author'] );
				$posts[ $key ]['price']         = asta_esc_meta( $post['ID'], 'price' );
				$posts[ $key ]['post_excerpt']  = get_post_field( 'post_excerpt', $post['ID'] );
				$posts[ $key ]['is_my_product'] = $curent_user_id === (int) $post['post_author'] ? true : false;
				$posts[ $key ]['guid']          = get_permalink( $post['ID'] );

				$category = get_asta_category( $post['ID'] );

				if ( ! empty( $category ) ) {
					$category['link']          = get_category_link( $category['id'] );
					$posts[ $key ]['category'] = $category;
				}
			}

			return $posts;
		}


		/**
		 * It takes an array and removes the keys specified in the second argument
		 *
		 * @param array post The post object
		 * @param array remove An array of keys to remove from the post array.
		 *
		 * @return array function compares the keys of two (or more) arrays, and returns the
		 * difference.
		 */
		private function clean_unused_keys( array $post, array $remove = array( 'data' ) ) {
			return array_diff_key( $post, array_flip( $remove ) );
		}


		/**
		 * It removes all the keys from the array that are not in the array
		 *
		 * @param array posts The array of posts to clean.
		 *
		 * @return array of posts.
		 */
		private function clean_get_posts( array $posts ) {

			$remove = array(
				'post_date',
				'post_date_gmt',
				'post_content',
				'post_status',
				'post_parent',
				'post_password',
				'to_ping',
				'pinged',
				'post_modified',
				'post_modified_gmt',
				'post_content_filtered',
				'menu_order',
				'post_mime_type',
				'comment_count',
				'filter',
				'comment_status',
				'ping_status',
				'post_excerpt',
			);

			foreach ( $posts as $key => $post ) {
				$posts[ $key ] = $this->clean_unused_keys( (array) $post, $remove );
			}

			return $posts;
		}


		/**
		 * This function retrieves auctions based on user-defined parameters and returns a JSON response with
		 * the results.
		 *
		 * @param \WP_REST_Request request A parameter of type \WP_REST_Request that represents the REST API
		 * request being made.
		 */
		public function asta_get_product( \WP_REST_Request $request ) {

			$params   = $request->get_params();
			$attr     = $request->get_attributes();
			$per_page = get_option( 'posts_per_page' );

			$page         = ( ! empty( $params['page'] ) ? preg_replace( '/[^0-9]/i', '', $params['page'] ) : 1 );
			$number_posts = ( ! empty( $params['number_posts'] ) ? preg_replace( '/[^0-9]/i', '', $params['number_posts'] ) : $per_page );
			$search       = ( ! empty( $params['search'] ) ? preg_replace( '/[^0-9A-Za-z\s\,\.\-\[\]\(\)\$\€\&\=\!\@\#]/i', '', $params['search'] ) : '' );
			$type         = ( ! empty( $params['type'] ) ? preg_replace( '/[^0-9]/i', '', $params['type'] ) : '' );
			$price_range  = ( ! empty( $params['price_range'] ) ? preg_replace( '/[^false0-9\,]/i', '', $params['price_range'] ) : '' );
			$user_id      = ( ! empty( $params['user_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['user_id'] ) : '' );

			$response = array(
				'status'  => 'error',
				'message' => __( 'there isn\'t auctions', 'asta-api' ),
			);

			$products = new WP_Query(
				$this->prepare_args(
					(int) $number_posts,
					$page,
					$search,
					$price_range,
					$type,
					$user_id
				)
			);

			if ( ! empty( $products->posts ) ) {

				$response['status']  = 'success';
				$response['message'] = $this->card_requirements(
					$this->clean_get_posts( $products->posts ),
					$attr['login_user_id']
				);

				$response['is_lasts'] = count( $products->posts ) < $number_posts;
			}

			wp_send_json( $response );
		}
	}
endif;

ASTA_THEME_GET_PRODUCT::instance();
