<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Stripe\StripeClient;

if ( ! class_exists( 'ASTA_THEME_PROFILE' ) ) :
	class ASTA_THEME_PROFILE extends SEC {

		private static $instance;

		private $image_ext = array( 'jpg', 'png', 'jpeg' );

		private ?StripeClient $stripe_client = null;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_PROFILE ) ) {
				self::$instance = new ASTA_THEME_PROFILE();

				self::$instance->stripe_client = ASTA_STRIPE::client();
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
		 * @param [type] $server
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {

			// Profile image
			$server->register_route(
				'rest-api-wordpress',
				'/api-profile-upload-image',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_upload_profile_image' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Profile user info
			$server->register_route(
				'rest-api-wordpress',
				'/api-profile-update-info',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'wpr_profile_update_info' ),
					'login_user_id' => get_current_user_id(),
				)
			);

			// Profile add card to user
			$server->register_route(
				'rest-api-wordpress',
				'/api-card-to-user',
				array(
					'methods'       => 'POST',
					'callback'      => array( $this, 'asta_api_card_to_user' ),
					'login_user_id' => get_current_user_id(),
				)
			);
		}


		/**
		 * This function removes the old profile image of a user by deleting the file from the server.
		 *
		 * @param int user_id An integer representing the ID of the user whose profile image is being
		 * removed.
		 * @param string basedir The base directory where the user's profile picture is stored.
		 */
		private function remove_old_profile_image( int $user_id, string $basedir ) {

			$old_picture = get_user_meta( $user_id, 'profile-picture', true );

			if ( $old_picture ) {
				$image      = explode( 'uploads/profiles/', $old_picture );
				$last_image = end( $image );
				wp_delete_file( $basedir . $last_image );
			}
		}


		/**
		 * This function uploads a profile image for a user and updates their user meta data with the new
		 * image URL.
		 *
		 * @param \WP_REST_Request request An object of the WP_REST_Request class that contains the request
		 * data.
		 */
		public function wpr_upload_profile_image( \WP_REST_Request $request ) {

			$attr  = $request->get_attributes();
			$files = $request->get_file_params();

			$filename = sanitaiz_extension(
				preg_replace( '/[^a-zA-Z0-9\.\_\-]/i', '', $files['file']['name'] )
			);

			$file_helper = file_destination_helper( $filename );

			if (
				in_array( $file_helper['extension'], $this->image_ext, true ) &&
				upload_widouth_exif( $files['file']['tmp_name'], $file_helper['location'] )
			) {

				adjust_image_size( $file_helper['location'], null, 150 );
				$this->remove_old_profile_image( $attr['login_user_id'], $file_helper['basedir'] );

				update_user_meta( $attr['login_user_id'], 'profile-picture', $file_helper['new_url'] );

				wp_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'image has been uploaded', 'asta-api' ),
						'image'   => $file_helper['new_url'],
					)
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'image hasn\'t uploaded', 'asta-api' ),
				)
			);
		}


		/**
		 * This function updates user information in WordPress and returns a success or error message.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the WP_REST_Request
		 * class, which is used to handle REST API requests in WordPress.
		 */
		public function wpr_profile_update_info( \WP_REST_Request $request ) {

			$attr   = $request->get_attributes();
			$params = $request->get_params();

			$first_name      = ( ! empty( $params['first_name'] ) ? preg_replace( '/[^a-zA-Z0-9\s]/i', '', $params['first_name'] ) : '' );
			$last_name       = ( ! empty( $params['last_name'] ) ? preg_replace( '/[^a-zA-Z0-9\s]/i', '', $params['last_name'] ) : '' );
			$website         = ( ! empty( $params['website'] ) ? preg_replace( '/[^a-zA-Z0-9\@\:\/\%\&\?\#\.\-\_]/i', '', $params['website'] ) : false );
			$email           = ( ! empty( $params['email'] ) ? preg_replace( '/[^a-zA-Z0-9\@\.]/i', '', $params['email'] ) : false );
			$description     = ( ! empty( $params['description'] ) ? preg_replace( '/[^a-zA-Z0-9\s\n\t]/i', '', $params['description'] ) : '' );
			$password        = ( ! empty( $params['password'] ) ? preg_replace( '/[^a-zA-Z0-9\?\^\$\€\,\.\@\#\!\_\-\[\]\(\)\*]/i', '', $params['password'] ) : '' );
			$repeat_password = ( ! empty( $params['repeat_password'] ) ? preg_replace( '/[^a-zA-Z0-9\?\^\$\€\,\.\@\#\!\_\-\[\]\(\)\*]/i', '', $params['repeat_password'] ) : '' );

			$args = array(
				'ID'          => $attr['login_user_id'],
				'first_name'  => $first_name,
				'last_name'   => $last_name,
				'user_email'  => $email,
				'description' => $description,
				'user_url'    => $website,
			);

			if ( $password === $repeat_password && 'password' !== $password ) {
				$args['user_pass'] = $password;
			}

			if ( is_wp_error( wp_update_user( $args ) ) ) {

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'User info hasn\'t been updated', 'asta-api' ),
					)
				);
			} else {

				wp_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'User info has been updated', 'asta-api' ),
					)
				);
			}
		}


		/**
		 * The function `asta_api_card_to_user` saves a card token to a user's Stripe customer account.
		 *
		 * @param \WP_REST_Request request The  parameter is an instance of the \WP_REST_Request
		 * class, which represents the REST API request being made.
		 */
		public function asta_api_card_to_user( \WP_REST_Request $request ) {

			$attr   = $request->get_attributes();
			$params = $request->get_params();

			$token = ( ! empty( $params['token'] ) ? preg_replace( '/[^a-zA-Z0-9\-\_]/i', '', $params['token'] ) : false );

			if ( 0 !== $attr['login_user_id'] ) {

				$customer_id = ASTA_USER::get_user_customer_id( $attr['login_user_id'] );
				if ( ! empty( $customer_id ) ) {

					try {

						$source = $this->stripe_client->customers->createSource(
							$customer_id,
							array(
								'source' => $token,
							)
						);

						// Future
						// $paymentMethod = $this->stripe_client->paymentMethods->attach($token, [ 'customer' => $customer_id ]);
						// $this->stripe_client->customers->update($customer_id, [ 'invoice_settings' => ['default_payment_method' => $paymentMethod->id] ]);

						wp_send_json(
							array(
								'status'  => ! empty( $source ) ? 'success' : 'error',
								'message' => ! empty( $source ) ? $source : __( 'Problem to save card', 'asta-api' ),
							),
						);

					} catch ( Exception $e ) {
						wp_send_json(
							array(
								'status'  => 'error',
								'message' => $e->getMessage(),
							),
						);
					}
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => __( 'User system error not added to stripe', 'asta-api' ),
					),
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Utente non identificato', 'asta-api' ),
				),
			);
		}
	}
endif;

ASTA_THEME_PROFILE::instance();
