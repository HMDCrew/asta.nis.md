<?php
/**
 * Template Name: Cart
 */

defined( 'ABSPATH' ) || exit;

// ASTA_THEME_SOLD_PROCESS::instance()->auction_status_for_cart( 194 );

$cart = ASTA_THEME_CART::get_cart();

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">

		<h1 class="title"><?php echo __( 'Cart', 'asta-child' ); ?></h1>

		<?php if ( ! empty( $cart['products_cart'] ) || ! empty( $cart['auctions_cart'] ) ) : ?>

			<?php if ( ! empty( $cart['auctions_cart'] ) ) : ?>

				<h4><?php echo __( 'Auctions', 'asta-child' ); ?></h4>

				<?php foreach ( $cart['auctions_cart'] as $cart_item ) : ?>
					<?php do_action( 'asta_cart_item', $cart_item ); ?>
				<?php endforeach; ?>

			<?php endif; ?>

			<?php if ( ! empty( $cart['products_cart'] ) ) : ?>

				<h4><?php echo __( 'Products', 'asta-child' ); ?></h4>

				<?php foreach ( $cart['products_cart'] as $cart_item ) : ?>
					<?php
					do_action(
						'asta_cart_item',
						array(
							'product_id' => $cart_item['product_id'],
							'price'      => floatval( get_post_meta( $cart_item['product_id'], 'price', true ) ),
							'qty'        => $cart_item['qty'],
							'max_qty'    => get_post_meta( $cart_item['product_id'], 'qty', true ),
						)
					);
					?>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( get_current_user_id() ) : ?>

				<div class="my-cards">
					<?php
					do_action(
						'asta_user_credit_cards',
						array(
							'user_id'         => get_current_user_id(),
							'has_placeholder' => false,
						)
					);
					?>
				</div>

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
