<?php
/**
 * Template Name: Cart
 */

defined( 'ABSPATH' ) || exit;

// $details = get_post_meta( 255, 'details', true );
// update_user_meta( get_current_user_id(), 'user_cart', $details['cart'] );

$cart = ASTA_THEME_CART::get_cart();

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">

		<div class="title"><?php echo __( 'Cart', 'asta-child' ); ?></div>

		<?php if ( ! empty( $cart['products_cart'] ) || ! empty( $cart['auctions_cart'] ) ) : ?>

			<?php if ( ! empty( $cart['auctions_cart'] ) ) : ?>

				<h2><?php echo __( 'Auctions', 'asta-child' ); ?></h2>

				<?php foreach ( $cart['auctions_cart'] as $cart_item ) : ?>
					<?php do_action( 'asta_cart_item', $cart_item ); ?>
				<?php endforeach; ?>

			<?php endif; ?>

			<?php if ( ! empty( $cart['products_cart'] ) ) : ?>

				<h2><?php echo __( 'Products', 'asta-child' ); ?></h2>

				<?php foreach ( $cart['products_cart'] as $product_id ) : ?>
					<?php
					do_action(
						'asta_cart_item',
						array(
							'product_id' => $product_id,
							'price'      => floatval( get_post_meta( $product_id, 'price', true ) ),
						)
					);
					?>
				<?php endforeach; ?>
			<?php endif; ?>

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
