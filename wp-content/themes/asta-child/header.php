<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Asta
 */

$cart_length = ASTA_THEME_CART::get_cart_counter();

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<header id="masthead" class="site-header">
		<div class="container d-flex justify-between align-center h-100">
			<div class="site-branding">

				<div class="logo d-flex align-end">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link d-block" rel="home" aria-current="page">
						<img src="<?php echo apply_filters( 'wpr_get_image_by_id', get_theme_mod( 'custom_logo' ) ); ?>" class="custom-logo w-100 d-block" alt="Asta">
					</a>
					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="d-block">
							<?php echo get_bloginfo( 'name' ); ?>
						</a>
					</h1>
				</div>

				<?php $asta_description = get_bloginfo( 'description', 'display' ); ?>
				<?php if ( $asta_description || is_customize_preview() ) : ?>
					<p class="site-description"><?php echo $asta_description; ?></p>
				<?php endif; ?>

			</div><!-- .site-branding -->

			<nav id="site-navigation" class="main-navigation">

				<div class="nav-btns">

					<a href="/cart" class="cart-menu">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
							<path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" fill="white"/>
						</svg>
						<?php if ( ! is_page_template( 'templates/thankyou.php' ) ) : ?>
							<span class="n_products <?php echo ( $cart_length <= 0 ? 'hide' : '' ); ?>"><?php echo $cart_length; ?></span>
						<?php endif; ?>
					</a>

					<a href="<?php echo ( is_user_logged_in() ? '/profile' : '/login' ); ?>" class="user-menu">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="user-icon">
							<path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" fill="white"/>
						</svg>
					</a>

					<button class="mobile-menu-toggle">
						<span></span>
					</button>
				</div>

				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'menu-1',
						'menu_id'         => 'primary-menu',
						'container'       => 'nav',
						'container_class' => 'main-navigation',
						'menu_class'      => 'menu',
						'fallback_cb'     => 'wp_page_menu',
						'items_wrap'      => '<ul id="%1$s" class="%2$s align-center d-flex">%3$s</ul>',
						'item_spacing'    => 'preserve',
					)
				);
				?>
				</div>
			</nav><!-- #site-navigation -->
		</div>
	</header><!-- #masthead -->
