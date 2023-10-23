<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ASTA_THEME_AUTH' ) ) :
	class ASTA_THEME_AUTH {

		private static $instance;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ASTA_THEME_AUTH ) ) {
				self::$instance = new ASTA_THEME_AUTH;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'init', array( $this, 'user_activation_routes' ) );
			add_action( 'template_include', array( $this, 'user_activation_template_include' ) );

			add_action( 'rest_api_init', array( $this, 'asta_rest_api' ), 10 );
		}


		/**
		 * This function adds a rewrite rule for the "activate" URL in WordPress.
		 */
		public function user_activation_routes() {
			add_rewrite_rule( '^activate/', 'index.php', 'top' );
		}


		/**
		 * This function includes a custom template for user activation if the current page is "activate" or
		 * if the URL contains "activate".
		 *
		 * @param string template The  parameter is a string that represents the path to the template file
		 * that WordPress will use to display the page.
		 *
		 * @return string the current page is "activate" or the current URL request is "activate", the function
		 * returns the path to the "class-user-activation.php" file located in the child theme's directory.
		 * Otherwise, it returns the original template.
		 */
		public function user_activation_template_include( $template ) {

			global $wp_query;
			global $wp;

			$request = explode( '/', $wp->request );

			if ( is_page( 'activate' ) || 'activate' === current( $request ) ) {

				http_response_code( 200 );

				$wp_query->is_404    = false;
				$wp_query->is_single = true;

				return ASTA_API_PLUGIN_CLASSES . 'user/class-user-activation.php';
			}

			return $template;
		}

		/**
		 * Registering a route for the REST API.
		 * @param [type] $server
		 */
		public function asta_rest_api( \WP_REST_Server $server ) {

			// Login
			$server->register_route(
				'rest-api-wordpress',
				'/api-login',
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'wpr_login' ),
				)
			);

			// Register
			$server->register_route(
				'rest-api-wordpress',
				'/api-register',
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'wpr_register' ),
				)
			);
		}


		// cheack number attempts
		public function wpr_login( \WP_REST_Request $request ) {

			$params = $request->get_params();

			$username = ( ! empty( $params['user'] ) ? preg_replace( '/[^a-zA-Z0-9\?\.\@\_\-]/i', '', $params['user'] ) : '' );
			$password = ( ! empty( $params['pwd'] ) ? preg_replace( '/[^a-zA-Z0-9\?\^\$\€\,\.\@\#\!\_\-\[\]\(\)\*]/i', '', $params['pwd'] ) : '' );
			$remember = ( ! empty( $params['remember'] ) ? preg_replace( '/[^truefalse]/i', '', $params['remember'] ) : false );

			$user = wp_signon(
				array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => ( false !== $remember && 'false' !== $remember ? true : false ),
				)
			);

			$errors = is_a( $user, 'WP_Error' ) ? array_values( $user->errors ) : array();

			wp_send_json(
				array(
					'status'  => is_a( $user, 'WP_Error' ) ? 'error' : 'success',
					'message' => is_a( $user, 'WP_Error' ) ? reset( $errors ) : $user,
				)
			);
		}


		/**
		 * This function sends a user confirmation email with an activation link to the specified email
		 * address.
		 *
		 * @param string user_email The email address of the user who needs to confirm their account.
		 * @param string activation_code The activation code is a unique code generated for each user during
		 * the registration process. It is used to verify the user's email address and activate their
		 * account. The activation link containing this code is sent to the user's email address for them to
		 * click and complete the activation process.
		 */
		private function user_confirmation_email( string $user_email, string $activation_code ) {

			$activation_link = sprintf(
				'%s/activate/?email=%s&activation_code=%s',
				get_site_url(),
				$user_email,
				$activation_code
			);

			$admin_email = get_option( 'new_admin_email' );

			$headers = array(
				'From: Asta <info@asta.nis.md>' . "\r\n",
				sprintf( 'Bcc: %s', $admin_email ) . "\r\n",
				'Content-Type: text/html; charset=UTF-8' . "\r\n",
			);

			$subject = __( 'Asta - User confirmation email', 'asta-api' );

			$message = sprintf(
				'%s, <br>%s:<br> %s',
				__( 'Hi', 'asta-api' ),
				__( 'Please click the following link to activate your account', 'asta-api' ),
				$activation_link
			);

			wp_mail( $user_email, $subject, $message, $headers );
		}


		// Cron for remove superflual users after one month (status: email not confirmed)
		// max 5 registration on day
		// add documents on registration
		// find api for chacke documents validity
		// add password length and special chars usage
		public function wpr_register( \WP_REST_Request $request ) {

			$params = $request->get_params();

			$user_email = ( ! empty( $params['user'] ) ? preg_replace( '/[^a-zA-Z0-9\?\.\@\_\-]/i', '', $params['user'] ) : '' );
			$password   = ( ! empty( $params['pwd'] ) ? preg_replace( '/[^a-zA-Z0-9\?\^\$\€\,\.\@\#\!\_\-\[\]\(\)\*]/i', '', $params['pwd'] ) : '' );

			$user_id = email_exists( $user_email );

			if ( ! $user_id && false === username_exists( $user_email ) ) {

				$user_id = wp_create_user( $user_email, $password, $user_email );

				if ( ! is_a( $user_id, 'WP_Error' ) ) {
					$user = new WP_User( $user_id );
					$user->add_role( 'pending' );

					$activation_code = generate_activation_code();
					update_user_meta( $user_id, 'activation_code', $activation_code );

					$this->user_confirmation_email( $user_email, $activation_code );
				}

				wp_send_json(
					array(
						'status'  => is_a( $user_id, 'WP_Error' ) ? 'error' : 'success',
						'message' => is_a( $user_id, 'WP_Error' ) ? $user_id : __( 'User created! Check your email for confirm account.', 'asta-api' ),
					)
				);
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'User already exists. recover your password', 'asta-api' ),
				)
			);
		}
	}
endif;

ASTA_THEME_AUTH::instance();
