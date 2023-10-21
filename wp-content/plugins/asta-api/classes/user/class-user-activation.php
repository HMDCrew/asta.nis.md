<?php

if ( ! class_exists( 'User_Activation' ) ) :

	class User_Activation {

		private $email;
		private $activation_code;

		public function __construct() {
			$this->email           = ( isset( $_GET['email'] ) ? preg_replace( '/[^a-zA-Z0-9\?\.\@\_\-]/i', '', $_GET['email'] ) : '' );
			$this->activation_code = ( isset( $_GET['activation_code'] ) ? preg_replace( '/[^a-zA-Z0-9]/i', '', $_GET['activation_code'] ) : '' );

			$this->activate_user();
		}

		private function activate_user() {

			if ( ! empty( $this->email ) && ! empty( $this->activation_code ) ) {

				$user = get_user_by( 'email', $this->email );

				$user_activation = $user->__get( 'activation_code' );

				if (
					$this->activation_code === $user_activation &&
					in_array( 'pending', $user->roles, true ) &&
					! in_array( 'administrator', $user->roles, true )
				) {
					$user->remove_role( 'pending' );
					$user->add_role( 'approved' );

					wp_set_auth_cookie( $user->ID, true );

					wp_redirect( get_site_url(), 302 );
					exit;
				}

				if ( in_array( 'administrator', $user->roles, true ) ) {
					wp_redirect( get_site_url(), 302 );
					exit;
				}

				wp_redirect( site_url( '/login' ), 302 );
				exit;
			}
		}
	}

endif;

new User_Activation();