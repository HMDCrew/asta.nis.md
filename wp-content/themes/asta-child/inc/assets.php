<?php


/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param    array  $plugins
 * @return   array             Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}


/**
 * The function disables emojis in WordPress by removing related actions and filters and adding a
 * filter to TinyMCE.
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );


/**
 * Manage JS file's Front-end
 */
function manage_js_front_end( $tag ) {

	$src = $tag;

	if (
		str_contains( $src, '/themes/asta/js/navigation.js' ) ||
		(
			( ! is_user_logged_in() || ! in_array( 'administrator', wp_get_current_user()->roles, true ) ) &&
			(
				str_contains( $src, '/wp-includes/js/jquery/jquery.min.js' ) ||
				str_contains( $src, '/wp-includes/js/jquery/jquery-migrate.min.js' ) ||
				str_contains( $src, '/wp-includes/js/jquery/jquery.js' ) ||
				str_contains( $src, '/wp-includes/js/jquery/jquery-migrate.js' )
			)
		) ||
		(
			str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/index.bundle.js' ) &&
			(
				is_page_template( 'templates/login.php' ) ||
				is_page_template( 'templates/register.php' )
			)
		)
	) {
		$src = '';
	}

	if (
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/index.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/home.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/login.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/register.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/profile.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/new_auction.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/new_product.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/archive_auctions.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/archive_shop.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/single_auction.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/single_shop.bundle.js' ) ||
		str_contains( $src, get_stylesheet_directory_uri() . '/assets/dist/js/auth_user.bundle.js' )
	) {
		$src = wpr_async_js( $tag );
	}

	return $src;
}
add_filter( 'script_loader_tag', 'manage_js_front_end' );


/**
 * Manage CSS file's Front-end
 */
function manage_css_front_end( $tag ) {

	$src = $tag;

	if ( str_contains( $src, 'themes/asta-child/style.css' ) ) {
		$src = '';
	}

	return $src;
}
add_filter( 'style_loader_tag', 'manage_css_front_end' );



/**
 * Add JS scripts Front-end
 */
function assets_js_front_end() {

	wp_enqueue_script( 'asta', get_stylesheet_directory_uri() . '/assets/dist/js/index.bundle.js', array(), false, true );
	wp_localize_script(
		'asta',
		'asta_data',
		array(
			'json_url'      => get_rest_url(),
			'current_state' => wpr_front_information_queried_object(),
		)
	);

	if ( is_front_page() ) {
		wp_enqueue_script( 'home-asta', get_stylesheet_directory_uri() . '/assets/dist/js/home.bundle.js', array(), false, true );
	}

	if ( is_page_template( 'templates/login.php' ) ) {
		wp_enqueue_script( 'asta-login', get_stylesheet_directory_uri() . '/assets/dist/js/login.bundle.js', array(), false, true );
		wp_localize_script(
			'asta',
			'login_data',
			array(
				'json_url' => get_rest_url(),
			)
		);
	}

	if ( is_page_template( 'templates/register.php' ) ) {
		wp_enqueue_script( 'asta-register', get_stylesheet_directory_uri() . '/assets/dist/js/register.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-register',
			'register_data',
			array(
				'json_url' => get_rest_url(),
			)
		);
	}

	if ( is_page_template( 'templates/profile.php' ) ) {

		$keys = ASTA_STRIPE::get_gateway_keys( 'stripe' );

		wp_enqueue_script( 'stripe', 'https://js.stripe.com/v3/', array(), false, true );

		wp_enqueue_script( 'asta-profile', get_stylesheet_directory_uri() . '/assets/dist/js/profile.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-profile',
			'profile_data',
			array(
				'json_url'  => get_rest_url(),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'stripe_pk' => $keys['public_key'],
			)
		);
	}

	if ( is_page_template( 'templates/new-auction.php' ) || is_page_template( 'templates/edit-auction.php' ) ) {
		wp_enqueue_script( 'asta-new-auction', get_stylesheet_directory_uri() . '/assets/dist/js/new_auction.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-new-auction',
			'auction_data',
			array(
				'json_url' => get_rest_url(),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	if ( is_page_template( 'templates/new-product.php' ) || is_page_template( 'templates/edit-product.php' ) ) {
		wp_enqueue_script( 'asta-new-product', get_stylesheet_directory_uri() . '/assets/dist/js/new_product.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-new-product',
			'product_data',
			array(
				'json_url' => get_rest_url(),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	if ( is_page_template( 'templates/my-auctions.php' ) || ( is_archive() && 'auctions' === get_queried_object()->name ) ) {
		wp_enqueue_script( 'asta-archive-auctions', get_stylesheet_directory_uri() . '/assets/dist/js/archive_auctions.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-archive-auctions',
			'auctions_data',
			array(
				'json_url'      => get_rest_url(),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'current_state' => wpr_front_information_queried_object(),
				'user_id'       => get_current_user_id(),
				'labels'        => array(
					'edit'    => 'Edit',
					'details' => 'Details',
				),
			)
		);
	}

	if ( is_archive() && 'shop' === get_queried_object()->name ) {
		wp_enqueue_script( 'asta-archive-shop', get_stylesheet_directory_uri() . '/assets/dist/js/archive_shop.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-archive-shop',
			'shop_data',
			array(
				'json_url'      => get_rest_url(),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'current_state' => wpr_front_information_queried_object(),
				'user_id'       => get_current_user_id(),
				'labels'        => array(
					'edit'    => 'Edit',
					'details' => 'Details',
				),
			)
		);
	}

	if ( is_page_template( 'templates/cart.php' ) ) {

		wp_enqueue_script( 'stripe', 'https://js.stripe.com/v3/', array(), false, true );

		wp_enqueue_script( 'asta-cart', get_stylesheet_directory_uri() . '/assets/dist/js/cart.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-cart',
			'auctions_data',
			array(
				'json_url'      => get_rest_url(),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'current_state' => wpr_front_information_queried_object(),
				'user_id'       => get_current_user_id(),
			)
		);
	}

	if ( is_page_template( 'templates/my-orders.php' ) ) {

		wp_enqueue_script( 'stripe', 'https://js.stripe.com/v3/', array(), false, true );

		wp_enqueue_script( 'asta-my-orders', get_stylesheet_directory_uri() . '/assets/dist/js/my_orders.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-my-orders',
			'auctions_data',
			array(
				'json_url'      => get_rest_url(),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'current_state' => wpr_front_information_queried_object(),
				'user_id'       => get_current_user_id(),
			)
		);
	}

	if ( is_single() && 'auctions' === get_post_type( get_the_ID() ) ) {
		wp_enqueue_script( 'asta-auction', get_stylesheet_directory_uri() . '/assets/dist/js/single_auction.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-auction',
			'auction_data',
			array(
				'auction_id' => get_the_ID(),
				'json_url'   => get_rest_url(),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	if ( is_single() && 'shop' === get_post_type( get_the_ID() ) ) {
		wp_enqueue_script( 'asta-shop', get_stylesheet_directory_uri() . '/assets/dist/js/single_shop.bundle.js', array(), false, true );
		wp_localize_script(
			'asta-shop',
			'shop_data',
			array(
				'product_id' => get_the_ID(),
				'json_url'   => get_rest_url(),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	if ( is_user_logged_in() ) {
		wp_enqueue_script( 'asta-auth', get_stylesheet_directory_uri() . '/assets/dist/js/auth_user.bundle.js', array(), false, true );
	}
}
add_action( 'wp_enqueue_scripts', 'assets_js_front_end', 0 );


/**
 * Add Front-end CSS
 */
function assets_css_front_end() {

	wp_dequeue_style( 'global-styles' );
	wp_enqueue_style( 'asta', get_stylesheet_directory_uri() . '/assets/dist/css/index.bundle.css' );

	if ( is_page_template( 'templates/login.php' ) ) {
		wp_enqueue_style( 'login-asta', get_stylesheet_directory_uri() . '/assets/dist/css/login.bundle.css' );
	}

	if ( is_page_template( 'templates/register.php' ) ) {
		wp_enqueue_style( 'register-asta', get_stylesheet_directory_uri() . '/assets/dist/css/register.bundle.css' );
	}

	if ( is_page_template( 'templates/profile.php' ) ) {
		wp_enqueue_style( 'profile-asta', get_stylesheet_directory_uri() . '/assets/dist/css/profile.bundle.css' );
	}

	if ( is_page_template( 'templates/new-auction.php' ) || is_page_template( 'templates/edit-auction.php' ) ) {
		wp_enqueue_style( 'new-auction', get_stylesheet_directory_uri() . '/assets/dist/css/new_auction.bundle.css' );
	}

	if ( is_page_template( 'templates/new-product.php' ) || is_page_template( 'templates/edit-product.php' ) ) {
		wp_enqueue_style( 'new-product', get_stylesheet_directory_uri() . '/assets/dist/css/new_product.bundle.css' );
	}

	if ( is_page_template( 'templates/my-auctions.php' ) || is_archive() ) {

		if ( 'auctions' === get_queried_object()->name ) {
			wp_enqueue_style( 'asta-archive-auctions', get_stylesheet_directory_uri() . '/assets/dist/css/archive_auctions.bundle.css' );
		}

		if ( 'shop' === get_queried_object()->name ) {
			wp_enqueue_style( 'asta-archive-shop', get_stylesheet_directory_uri() . '/assets/dist/css/archive_shop.bundle.css' );
		}
	}

	if ( is_single() && 'auctions' === get_post_type( get_the_ID() ) ) {
		wp_enqueue_style( 'asta-auction', get_stylesheet_directory_uri() . '/assets/dist/css/single_auction.bundle.css' );
	}

	if ( is_single() && 'shop' === get_post_type( get_the_ID() ) ) {
		wp_enqueue_style( 'asta-shop', get_stylesheet_directory_uri() . '/assets/dist/css/single_shop.bundle.css' );
	}

	if ( is_user_logged_in() ) {
		wp_enqueue_style( 'asta-auth', get_stylesheet_directory_uri() . '/assets/dist/css/auth_user.bundle.css' );
	}

	if ( is_page_template( 'templates/cart.php' ) || is_page_template( 'templates/thankyou.php' ) ) {
		wp_enqueue_style( 'asta-cart', get_stylesheet_directory_uri() . '/assets/dist/css/cart.bundle.css' );
	}

	if ( is_page_template( 'templates/my-orders.php' ) ) {
		wp_enqueue_style( 'asta-my-orders', get_stylesheet_directory_uri() . '/assets/dist/css/my_orders.bundle.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'assets_css_front_end' );
