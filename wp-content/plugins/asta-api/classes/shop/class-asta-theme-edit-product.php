<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_EDIT_PRODUCT' ) ) :
	class ASTA_THEME_EDIT_PRODUCT {


		private static $instance;
		private $image_ext = array( 'jpg', 'png', 'jpeg' );


		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_EDIT_PRODUCT ) ) {
				self::$instance = new ASTA_THEME_EDIT_PRODUCT();
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

			// Shop create draft
			$server->register_route(
				'rest-api-wordpress',
				'/api-new-product',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_new_product' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Edit Product
			$server->register_route(
				'rest-api-wordpress',
				'/api-edit-product',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_edit_product' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Product upload Photo
			$server->register_route(
				'rest-api-wordpress',
				'/api-product-upload-image',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_upload_product_image' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Product remove Photo
			$server->register_route(
				'rest-api-wordpress',
				'/api-product-remove-image',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_remove_product_image' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Product save info
			$server->register_route(
				'rest-api-wordpress',
				'/api-save-product',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_save_product_info' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * This function creates a new draft product post or retrieves the latest draft product post for a
		 * specific user.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and query parameters.
		 */
		public function wpr_new_product( \WP_REST_Request $request ) {

			$attr = $request->get_attributes();

			$unsaved_product = get_posts(
				array(
					'fields'      => 'ids',
					'numberposts' => 1,
					'post_status' => 'draft',
					'author'      => $attr['login_user_id'],
					'post_type'   => 'shop',
					'orderby'     => 'date',
					'order'       => 'DESC',
				)
			);

			if ( ! empty( $unsaved_product ) ) {

				$product_id = reset( $unsaved_product );

			} else {

				$product_args = array(
					'post_title'  => 'draft product',
					'post_author' => $attr['login_user_id'],
					'post_type'   => 'shop',
				);

				$product_id = wp_insert_post( $product_args );
			}

			$product_editor = get_post_meta( $product_id, 'product_editor', true );

			wp_send_json(
				array(
					'status'       => ! is_wp_error( $product_id ) ? 'success' : 'error',
					'message'      => ! is_wp_error( $product_id ) ? __( 'Product created', 'asta-api' ) : __( 'Problem to create product', 'asta-api' ),
					'product_id'   => ! is_wp_error( $product_id ) ? $product_id : false,
					'product_json' => ( $product_editor ? $product_editor : array() ),
				)
			);
		}


		/**
		 * This is a PHP function that initializes an product editor and returns its status and data in JSON
		 * format.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which represents a REST API request. It contains information about the request, such as the
		 * HTTP method, headers, and parameters.
		 */
		public function wpr_edit_product( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$product_id     = ( ! empty( $params['product_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );
			$product        = get_post( $product_id );
			$product_editor = get_post_meta( $product_id, 'product_editor', true );

			if ( $product_id && (int) get_post_field( 'post_author', $product_id ) === $attr['login_user_id'] ) {
				wp_send_json(
					array(
						'status'       => ! is_wp_error( $product ) ? 'success' : 'error',
						'message'      => ! is_wp_error( $product ) ? __( 'Product editable', 'asta-api' ) : __( 'Problem to initialize product editor', 'asta-api' ),
						'product_id'   => ! is_wp_error( $product ) ? $product_id : false,
						'product_json' => ( $product_editor ? $product_editor : array() ),
					)
				);
			}

			wp_send_json(
				array(
					'status'       => 'error',
					'message'      => __( 'Problem to initialize product editor 2', 'asta-api' ),
					'product_id'   => false,
					'product_json' => array(),
				)
			);
		}


		/**
		 * This is a PHP function that updates the gallery of an product post with the given ID.
		 *
		 * @param int product_id An integer value representing the ID of the product post for which the gallery
		 * needs to be updated.
		 * @param array gallery An array of image URLs representing the gallery of images for the product. This
		 * function updates the 'asta_gallery' post meta for the given product ID with the new gallery
		 * array.
		 *
		 * @return int|bool result of the `update_post_meta()` function, which is a boolean value indicating whether
		 * the update was successful or not.
		 */
		private function update_product_gallery( int $product_id, array $gallery ) {
			return update_post_meta( $product_id, 'asta_gallery', $gallery );
		}


		/**
		 * This function adds an image URL to an product gallery and returns the updated gallery metadata.
		 *
		 * @param int product_id An integer representing the ID of the product to which the image is being
		 * added.
		 * @param string url The URL of the image that needs to be added to the product gallery.
		 *
		 * @return array updated gallery metadata for the product, which includes the new image URL that was
		 * added to the gallery.
		 */
		private function add_image_product_gallery( int $product_id, string $url ) {

			$gallery_meta   = get_asta_gallery( $product_id );
			$gallery_meta[] = $url;

			$this->update_product_gallery( $product_id, $gallery_meta );

			return $gallery_meta;
		}


		/**
		 * This function uploads an image for an product and adds it to the product gallery.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which represents a REST API request. It contains information about the request, such as the
		 * HTTP method, headers, and parameters.
		 */
		public function wpr_upload_product_image( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();
			$files  = $request->get_file_params();

			$param_product_id = ( ! empty( $params['product_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );

			$product_id = (
				! empty( $params['product_id'] )
					? $param_product_id
					: get_user_last_edited_post( $attr['login_user_id'], 'shop' )
			);

			if ( $product_id && (int) get_post_field( 'post_author', $product_id ) === $attr['login_user_id'] ) {

				$filename = sanitaiz_extension(
					preg_replace( '/[^a-zA-Z0-9\.\_\-]/i', '', $files['file']['name'] )
				);

				$file_helper = file_destination_helper(
					$filename,
					sprintf( 'shop/%d', $product_id )
				);

				if (
					in_array( $file_helper['extension'], $this->image_ext, true ) &&
					upload_widouth_exif( $files['file']['tmp_name'], $file_helper['location'] )
				) {

					adjust_image_size( $file_helper['location'], 1440 );
					$this->add_image_product_gallery( $product_id, $file_helper['new_url'] );

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
					'message' => __( 'product not found for upload image', 'asta-api' ),
				),
			);
		}


		/**
		 * This PHP function removes an image from an product gallery and checks if the user has permission
		 * to do so.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress. It contains information about the
		 * request, such as the HTTP method, headers, and parameters.
		 */
		public function wpr_remove_product_image( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$product_id = ( ! empty( $params['product_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );
			$image_url  = ( ! empty( $params['image_url'] ) ? preg_replace( '/[^a-zA-Z0-9\@\:\/\%\&\?\#\.\-\_]/i', '', $params['image_url'] ) : '' );

			if ( (int) get_post_field( 'post_author', $product_id ) === $attr['login_user_id'] ) {

				$img_path     = remove_multimple_slah( ABSPATH . parse_url( $image_url, PHP_URL_PATH ) );
				$gallery_meta = get_asta_gallery( $product_id );
				$key          = array_search( $image_url, $gallery_meta, true );

				unset( $gallery_meta[ $key ] );
				wp_delete_file( $img_path );
				$this->update_product_gallery( $product_id, $gallery_meta );

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
					'message' => __( 'Only product author have permission to edit product', 'asta-api' ),
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
		 * This function saves product information in WordPress and performs various checks before updating
		 * the post.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which contains information about the REST API request being made.
		 */
		public function wpr_save_product_info( \WP_REST_Request $request ) {

			$params = $request->get_params();
			$attr   = $request->get_attributes();

			$product_id      = ( ! empty( $params['product_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['product_id'] ) : '' );
			$product_title   = ( ! empty( $params['product_title'] ) ? preg_replace( '/[^a-zA-Z0-9\s\@\!\?\,\.\-\_]/i', '', $params['product_title'] ) : '' );
			$price           = ( ! empty( $params['price'] ) ? preg_replace( '/[^0-9]/i', '', $params['price'] ) : '' );
			$category_id     = ( ! empty( $params['category_id'] ) ? preg_replace( '/[^0-9]/i', '', $params['category_id'] ) : '' );
			$aditional_info  = ( ! empty( $params['aditional_info'] ) ? preg_replace( '/[^a-zA-Z0-9\s\@\!\?\,\.\-\_\n\t\#\:\/\&]/i', '', $params['aditional_info'] ) : '' );
			$product_content = (
				! empty( $params['product_content'] )
				? $this->regex_applied_array(
					'/[^0-9a-zA-Z\s\Ă\ă\Â\â\Î\î\Ș\ș\Ț\А\Б\В\Г\Д\Е\Ё\Ж\З\И\Й\К\М\Л\Н\О\П\Р\С\Т\У\Ф\Х\Ч\Ц\Ш\Щ\Ъ\Ы\Ь\Э\Ю\Я\а\б\в\г\д\е\ё\ж\з\и\й\к\м\л\н\о\п\р\с\т\у\ф\х\ч\ц\ш\щ\ъ\ы\ь\э\ю\я\?\!\'\"\$\€\%\&\/\(\)\=\[\]\\\@\#\*\-\_\;\:\.\,\>\<]/i',
					$params['product_content']
				)
				: array()
			);

			if ( (int) get_post_field( 'post_author', $product_id ) === $attr['login_user_id'] ) {

				if (
					! empty( $product_title ) &&
					! empty( $price )
				) {

					$product_meta = array(
						'price'          => $price,
						'product_editor' => $product_content,
					);

					$translations = new WPR_EditorJS_Gutenberg( $product_content );
					wp_update_post(
						array(
							'ID'           => $product_id,
							'post_title'   => $product_title,
							'post_content' => $translations->render_gutenberg(),
							'post_status'  => 'publish',
							'post_excerpt' => $aditional_info,
							'meta_input'   => $product_meta,
						)
					);

					wp_set_post_terms( $product_id, array( $category_id ), 'asta_category', false );

					wp_send_json(
						array(
							'status'  => 'success',
							'message' => __( 'product saved', 'asta-api' ),
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
					'message' => __( 'Only product author have permission to edit product', 'asta-api' ),
				),
			);
		}
	}
endif;

ASTA_THEME_EDIT_PRODUCT::instance();
