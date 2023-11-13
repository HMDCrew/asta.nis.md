<?php
/**
 * Template Name: Shop
 */

defined( 'ABSPATH' ) || exit;
ASTA_USER::redirect_not_logged_user( '/login' );

$cart = get_user_meta( get_current_user_id(), 'user_cart', true );

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">

		<div class="title"><?php echo __( 'Shop', 'asta-child' ); ?></div>

		<?php if ( ! empty( $cart ) ) : ?>

			<?php foreach ( $cart as $cart_item ) : ?>
				<?php get_template_part( 'template-parts/sections/cart', 'item', $cart_item ); ?>
			<?php endforeach; ?>

			<form id="payment-form" class="d-none">
				<div id="payment-element"></div> <!-- Elements will create input elements here -->

				<div id="payment-errors" role="alert"></div> <!-- We'll put the error messages in this element -->

				<button id="submit" class="btn btn-primary"><?php echo __( 'Pay', 'asta-child' ); ?></button>
			</form>

			<button type="button" class="btn btn-primary chackout-btn"><?php echo __( 'Chackout', 'asta-child' ); ?></button>

		<?php else : ?>

			<div>
				<?php echo __( 'You haven\'t products in cart', 'asta-child' ); ?>
			</div>

		<?php endif; ?>

	</div><!-- .container -->
</main><!-- #main -->
<?php
get_footer();
