<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_EDIT_AUCTION' ) ) :
	class ASTA_THEME_EDIT_AUCTION {


		private static $instance;
		private $image_ext = array( 'jpg', 'png', 'jpeg' );


		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_EDIT_AUCTION ) ) {
				self::$instance = new ASTA_THEME_EDIT_AUCTION();
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

			// Auction create draft
			$server->register_route(
				'rest-api-wordpress',
				'/api-new-auction',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_new_auction' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Edit Auction
			$server->register_route(
				'rest-api-wordpress',
				'/api-edit-auction',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_edit_auction' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Auction upload Photo
			$server->register_route(
				'rest-api-wordpress',
				'/api-auction-upload-image',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_upload_auction_image' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Auction remove Photo
			$server->register_route(
				'rest-api-wordpress',
				'/api-auction-remove-image',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_remove_auction_image' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Auction save info
			$server->register_route(
				'rest-api-wordpress',
				'/api-save-auction',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_save_auction_info' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * This function creates a new draft auction post or retrieves the latest draft auction post for a
		 * specific user.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function asta_new_auction( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();

			$auctions = get_posts(
				array(
					'fields'      => 'ids',
					'numberposts' => 1,
					'post_status' => 'draft',
					'author'      => $attr['login_user_id'],
					'post_type'   => 'auctions',
					'orderby'     => 'date',
					'order'       => 'DESC',
				)
			);

			if ( ! empty( $auctions ) ) {

				$auction_id = reset( $auctions );

			} else {

				$auction_args = array(
					'post_title'  => 'draft auction',
					'post_author' => $attr['login_user_id'],
					'post_type'   => 'auctions',
				);

				$auction_id = wp_insert_post( $auction_args );
			}

			$auction_editor = get_post_meta( $auction_id, 'auction_editor', true );

			wp_send_json(
				array(
					'status'       => ! is_wp_error( $auction_id ) ? 'success' : 'error',
					'message'      => ! is_wp_error( $auction_id ) ? __( 'Auction created', 'asta-api' ) : __( 'Problem to create auction', 'asta-api' ),
					'auction_id'   => ! is_wp_error( $auction_id ) ? $auction_id : false,
					'auction_json' => ( $auction_editor ? $auction_editor : array() ),
				)
			);
		}


		/**
		 * This is a PHP function that initializes an auction editor and returns its status and data in JSON
		 * format.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which represents a REST API request. It contains information about the request, such as the
		 * HTTP method, headers, and parameters.
		 */
		public function asta_edit_auction( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$auction_id     = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );
			$auction        = get_post( $auction_id );
			$auction_editor = get_post_meta( $auction_id, 'auction_editor', true );

			if ( $auction_id && (int) get_post_field( 'post_author', $auction_id ) === $attr['login_user_id'] ) {
				wp_send_json(
					array(
						'status'       => ! is_wp_error( $auction ) ? 'success' : 'error',
						'message'      => ! is_wp_error( $auction ) ? __( 'Auction editable', 'asta-api' ) : __( 'Problem to initialize auction editor', 'asta-api' ),
						'auction_id'   => ! is_wp_error( $auction ) ? $auction_id : false,
						'auction_json' => ( $auction_editor ? $auction_editor : array() ),
					)
				);
			}

			wp_send_json(
				array(
					'status'       => 'error',
					'message'      => __( 'Problem to initialize auction editor 2', 'asta-api' ),
					'auction_id'   => false,
					'auction_json' => array(),
				)
			);
		}


		/**
		 * This is a PHP function that updates the gallery of an auction post with the given ID.
		 *
		 * @param int auction_id An integer value representing the ID of the auction post for which the gallery
		 * needs to be updated.
		 * @param array gallery An array of image URLs representing the gallery of images for the auction. This
		 * function updates the 'auction_gallery' post meta for the given auction ID with the new gallery
		 * array.
		 *
		 * @return int|bool result of the `update_post_meta()` function, which is a boolean value indicating whether
		 * the update was successful or not.
		 */
		private function update_auction_gallery( int $auction_id, array $gallery ) {
			return update_post_meta( $auction_id, 'auction_gallery', $gallery );
		}


		/**
		 * This function adds an image URL to an auction gallery and returns the updated gallery metadata.
		 *
		 * @param int auction_id An integer representing the ID of the auction to which the image is being
		 * added.
		 * @param string url The URL of the image that needs to be added to the auction gallery.
		 *
		 * @return array updated gallery metadata for the auction, which includes the new image URL that was
		 * added to the gallery.
		 */
		private function add_image_auction_gallery( int $auction_id, string $url ) {

			$gallery_meta   = get_asta_gallery( $auction_id );
			$gallery_meta[] = $url;

			$this->update_auction_gallery( $auction_id, $gallery_meta );

			return $gallery_meta;
		}


		/**
		 * This function uploads an image for an auction and adds it to the auction gallery.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which represents a REST API request. It contains information about the request, such as the
		 * HTTP method, headers, and parameters.
		 */
		public function asta_upload_auction_image( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();
			$files  = $request->get_file_params();

			$param_auction_id = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );

			$auction_id = (
				! empty( $params['auction_id'] )
					? $param_auction_id
					: get_user_last_edited_post( $attr['login_user_id'] )
			);

			if ( $auction_id && (int) get_post_field( 'post_author', $auction_id ) === $attr['login_user_id'] ) {

				$filename = sanitaiz_extension(
					preg_replace( '/[^a-zA-Z0-9\.\_\-]/i', '', $files['file']['name'] )
				);

				$file_helper = file_destination_helper(
					$filename,
					sprintf( 'auctions/%d', $auction_id )
				);

				if (
					in_array( $file_helper['extension'], $this->image_ext, true ) &&
					upload_widouth_exif( $files['file']['tmp_name'], $file_helper['location'] )
				) {

					adjust_image_size( $file_helper['location'], 1440 );
					$this->add_image_auction_gallery( $auction_id, $file_helper['new_url'] );

					wp_send_json(
						array(
							'status'  => 'success',
							'message' => __( 'image has been uploaded', 'asta-api' ),
							'url'     => $file_helper['new_url'],
						),
					);
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'image hasn\'t uploaded', 'asta-api' ),
					),
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'auction not found for upload image', 'asta-api' ),
				),
			);
		}


		/**
		 * This PHP function removes an image from an auction gallery and checks if the user has permission
		 * to do so.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and parameters.
		 */
		public function asta_remove_auction_image( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$auction_id = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );
			$image_url  = ( ! empty( $params['image_url'] ) ? preg_replace( '/[^a-zA-Z0-9\@\:\/\%\&\?\#\.\-\_]/i', '', $params['image_url'] ) : '' );

			if ( (int) get_post_field( 'post_author', $auction_id ) === $attr['login_user_id'] ) {

				$img_path     = remove_multimple_slah( ABSPATH . parse_url( $image_url, PHP_URL_PATH ) );
				$gallery_meta = get_asta_gallery( $auction_id );
				$key          = array_search( $image_url, $gallery_meta, true );

				unset( $gallery_meta[ $key ] );
				wp_delete_file( $img_path );
				$this->update_auction_gallery( $auction_id, $gallery_meta );

				wp_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'image has been removed', 'asta-api' ),
					),
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Only auction author have permission to edit auction', 'asta-api' ),
				),
			);
		}


		/**
		 * It takes a regex string and an array, and returns the array with all the values cleaned by the
		 * regex
		 *
		 * @param string regex_val The regex to use to clean the array.
		 * @param array array The array to be cleaned.
		 *
		 * @return array of cleaned data.
		 */
		private function regex_applied_array( string $regex_val, array $callback ) {

			$cleaned = array();
			foreach ( $callback as $key => $value ) {

				$clean_key = preg_replace( '/[^0-9a-zA-Z\-\_]/i', '', $key );
				if ( ! is_array( $value ) ) {

					$clean_val = preg_replace( $regex_val, '', $value );

					$cleaned[ $clean_key ] = $clean_val;

				} else {
					$cleaned[ $clean_key ] = $this->regex_applied_array( $regex_val, $value );
				}
			}

			return $cleaned;
		}


		/**
		 * This function saves auction information in WordPress and performs various checks before updating
		 * the post.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which contains information about the REST API request being made.
		 */
		public function asta_save_auction_info( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$auction_id      = ( ! empty( $params['auction_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_id'] ) : '' );
			$auction_title   = ( ! empty( $params['auction_title'] ) ? preg_replace( '/[^a-zA-Z0-9\s\@\!\?\,\.\-\_]/i', '', $params['auction_title'] ) : '' );
			$auction_date    = ( ! empty( $params['auction_date'] ) ? preg_replace( '/[^to0-9\s\/]/i', '', $params['auction_date'] ) : '' );
			$price           = ( ! empty( $params['price'] ) ? preg_replace( '/[^0-9]/i', '', $params['price'] ) : '' );
			$price_increment = ( ! empty( $params['price_increment'] ) ? preg_replace( '/[^0-9]/i', '', $params['price_increment'] ) : '' );
			$auction_type_id = ( ! empty( $params['auction_type_select_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['auction_type_select_id'] ) : '' );
			$aditional_info  = ( ! empty( $params['aditional_info'] ) ? preg_replace( '/[^a-zA-Z0-9\s\@\!\?\,\.\-\_\n\t\#\:\/\&]/i', '', $params['aditional_info'] ) : '' );
			$auction_content = (
				! empty( $params['auction_content'] )
				? $this->regex_applied_array(
					'/[^0-9a-zA-Z\s\Ă\ă\Â\â\Î\î\Ș\ș\Ț\А\Б\В\Г\Д\Е\Ё\Ж\З\И\Й\К\М\Л\Н\О\П\Р\С\Т\У\Ф\Х\Ч\Ц\Ш\Щ\Ъ\Ы\Ь\Э\Ю\Я\а\б\в\г\д\е\ё\ж\з\и\й\к\м\л\н\о\п\р\с\т\у\ф\х\ч\ц\ш\щ\ъ\ы\ь\э\ю\я\?\!\'\"\$\€\%\&\/\(\)\=\[\]\\\@\#\*\-\_\;\:\.\,\>\<]/i',
					$params['auction_content']
				)
				: array()
			);

			if ( (int) get_post_field( 'post_author', $auction_id ) === $attr['login_user_id'] ) {

				if (
					! empty( $auction_title ) &&
					! empty( $auction_date ) &&
					! empty( $price ) &&
					! empty( $price_increment ) &&
					! empty( $auction_type_id ) ) {

					$auction_meta = array(
						'auction_price'   => $price,
						'price'           => $price,
						'price_increment' => $price_increment,
						'auction_editor'  => $auction_content,
					);

					$auction_date = explode( 'to', $auction_date );
					if ( ! empty( $auction_date ) && count( $auction_date ) > 1 ) {
						$auction_meta['start_date'] = DateTimeImmutable::createFromFormat( 'd/m/Y', trim( $auction_date[0] ) )->format( 'c' );
						$auction_meta['end_date']   = DateTimeImmutable::createFromFormat( 'd/m/Y', trim( $auction_date[1] ) )->format( 'c' );
					} else {
						$auction_meta['end_date'] = DateTimeImmutable::createFromFormat( 'd/m/Y', trim( $auction_date[0] ) )->format( 'c' );
					}

					$translations = new WPR_EditorJS_Gutenberg( $auction_content );
					wp_update_post(
						array(
							'ID'           => $auction_id,
							'post_title'   => $auction_title,
							'post_content' => $translations->render_gutenberg(),
							'post_status'  => 'publish',
							'post_excerpt' => $aditional_info,
							'meta_input'   => $auction_meta,
						)
					);

					wp_set_post_terms( $auction_id, array( $auction_type_id ), 'asta_category', false );

					// remove olds crons
					if ( wp_next_scheduled( 'chack_auction_status_for_cart', array( 'auction_id' => $auction_id ) ) ) {
						wp_clear_scheduled_hook( 'chack_auction_status_for_cart', array( 'auction_id' => $auction_id ) );
					}

					// schedule cron to end auction
					$end_epoch = DateTimeImmutable::createFromFormat( 'd/m/Y', trim( end( $auction_date ) ) )->format( 'U' );
					wp_schedule_single_event( $end_epoch, 'chack_auction_status_for_cart', array( 'auction_id' => $auction_id ) );

					wp_send_json(
						array(
							'status'  => 'success',
							'message' => __( 'auction saved', 'asta-api' ),
						),
					);
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'Please check required fields', 'asta-api' ),
					),
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Only auction author have permission to edit auction', 'asta-api' ),
				),
			);
		}
	}
endif;

ASTA_THEME_EDIT_AUCTION::instance();
