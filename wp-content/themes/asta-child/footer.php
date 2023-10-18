<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Asta
 */

$user = wp_get_current_user();

?>

	<?php if ( is_user_logged_in() && ( in_array( 'approved', $user->roles, true ) || in_array( 'administrator', $user->roles, true ) ) ) : ?>
		<div class="wpr-admin-menu">

			<div class="options">
				<a href="/new-auction" class="add-auction option"><?php echo __( 'New auction', 'asta-child' ); ?></a>
				<a href="/my-auctions" class="auctions option"><?php echo __( 'My auctions', 'asta-child' ); ?></a>
				<a href="/my-orders" class="auctions option"><?php echo __( 'My orders', 'asta-child' ); ?></a>
				<?php do_action( 'wpr_admin_menu' ); ?>
			</div>

			<button type="button" class="open-admin-menu">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" class="w-100">
					<path id="path837" style="fill:#000000" d="m23.78 23.78v-13.999c0-1.9588 0.536-3.55 1.607-4.7741 1.071-1.2546 2.601-1.8819 4.59-1.8819s3.519 0.6273 4.59 1.8819c1.102 1.2241 1.653 2.8153 1.653 4.7737v13.999h13.816c2.019 0 3.611 0.551 4.773 1.653 1.194 1.071 1.791 2.586 1.791 4.544 0 1.989-0.597 3.534-1.791 4.636-1.162 1.102-2.754 1.652-4.773 1.652h-13.816v13.954c0 1.989-0.551 3.596-1.653 4.82-1.101 1.224-2.632 1.836-4.59 1.836s-3.488-0.612-4.59-1.836c-1.071-1.224-1.607-2.831-1.607-4.82v-13.954h-13.816c-1.9582 0-3.5494-0.596-4.7735-1.79-1.1934-1.224-1.7901-2.723-1.7901-4.498 0-1.958 0.5814-3.473 1.7442-4.544 1.1935-1.102 2.8-1.653 4.8196-1.653h13.816z"/>
				</svg>
			</button>

		</div>
	<?php endif; ?>

	<footer id="colophon" class="site-footer">
		<div class="container site-info">
			<div class="row">

				<div class="col-4">
					<?php dynamic_sidebar( 'footer-one-widget' ); ?>
				</div>

				<div class="col-4">
					<?php dynamic_sidebar( 'footer-two-widget' ); ?>
				</div>

				<div class="col-4">
					<?php dynamic_sidebar( 'footer-three-widget' ); ?>
				</div>

			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
