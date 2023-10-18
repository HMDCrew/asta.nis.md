<?php

function wpr_prevent_access() {

	$roles = array( 'pending', 'approved' );

	if ( wpr_current_user_in_roles_list( $roles ) ) {

		add_filter( 'show_admin_bar', '__return_false' );
		redirect_home_on_wp_admin();
	}
}
add_action( 'init', 'wpr_prevent_access' );
