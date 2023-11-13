<?php

function wpr_prevent_access() {

	$roles = array( 'pending', 'approved' );

	if ( ASTA_USER::asta_current_user_in_roles_list( $roles ) ) {

		add_filter( 'show_admin_bar', '__return_false' );

		if ( str_contains( $_SERVER['REQUEST_URI'], 'wp-admin' ) ) {
			wp_redirect( get_site_url() );
			exit;
		}
	}
}
add_action( 'init', 'wpr_prevent_access' );
