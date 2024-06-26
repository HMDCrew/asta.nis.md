<?php
/**
 * Template Name: Thank you
 */

! empty( $_GET['payment_intent'] ) || wp_redirect( get_site_url() ) && exit;

$payment_intent = ASTA_STRIPE::client()->paymentIntents->retrieve( // phpcs:ignore
	preg_replace( '/[^a-zA-Z0-9\_\-]/i', '', $_GET['payment_intent'] ),
);

$order = ASTA_THEME_ORDERS::get_order( $payment_intent->id );
! empty( $order ) || wp_redirect( get_site_url() ) && exit;

$thank_you_visited = ASTA_THEME_ORDERS::get_meta( $order->ID, 'thank_you_visited' );
$order_details     = ASTA_THEME_ORDERS::get_meta( $order->ID, 'details' );
$payment_status    = ASTA_THEME_ORDERS::get_meta( $order->ID, 'payment_status' );
$paid_status       = reset( $payment_intent->charges->data )->paid;

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">

		<div class="payment-status">

			<h2><?php echo __( 'Thank you for participating in purchases on the platform!', 'asta-child' ); ?></h2>

			<?php if ( ( $paid_status && 'visited' !== $thank_you_visited ) || 'unpaid' !== $payment_status ) : ?>

				<?php
				if ( 'visited' !== $thank_you_visited ) {
					// update products qty
					ASTA_THEME_ORDERS::set_meta( $order->ID, 'thank_you_visited', 'visited' );
					ASTA_THEME_ORDERS::set_order_status( $order->ID, 'paid' );
					ASTA_THEME_ORDERS::set_meta( $order->ID, 'oreder_link', preg_replace( '/[^a-zA-Z0-9\@\:\/\%\&\?\#\.\-\_\=]/i', '', $_SERVER['REQUEST_URI'] ) );
					ASTA_THEME_ORDERS::update_users_payouts( $order_details );
					ASTA_THEME_ORDERS::crons_after_payment_order_status( $order->ID );
					ASTA_THEME_CHACKOUT::clean_cart( $order->ID );
				}
				?>

				<p><?php echo __( 'We are pleased to inform you that we have received your payment. We would like to express our gratitude for your support and for making our site a success.', 'asta-child' ); ?></p>
				<div class="totals">
					<strong>
						<span class="label"><?php echo __( 'Total', 'asta-child' ); ?>: </span>
						<span class="price">
							<span class="value"><?php echo $order_details['amount'] / 100; ?></span>
							<span class="currency"><?php echo strtoupper( $order_details['currency'] ); ?></span>
						</span>
					</strong>
				</div>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
			<?php else : ?>
				<p><?php echo __( 'We would like to express our gratitude for your support and for making our site a success. However, we regret to inform you that we have not yet received payment for your purchased purchases.', 'asta-child' ); ?></p>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>				
			<?php endif; ?>
		</div>

		<div class="cart">

			<?php if ( ! empty( $order_details['cart']['auctions_cart'] ) ) : ?>

				<h2><?php echo __( 'Auctions', 'asta-child' ); ?></h2>

				<?php foreach ( $order_details['cart']['auctions_cart'] as $auction_id => $cart_item ) : ?>
					<?php do_action( 'asta_cart_item', $cart_item ); ?>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( ! empty( $order_details['cart']['products_cart'] ) ) : ?>

				<h2><?php echo __( 'Products', 'asta-child' ); ?></h2>

				<?php foreach ( $order_details['cart']['products_cart'] as $cart_item ) : ?>
					<?php

					if ( 'visited' !== $thank_you_visited ) {
						$qty = (int) get_post_meta( $cart_item['product_id'], 'qty', true );
						update_post_meta( $cart_item['product_id'], 'qty', ( $qty - $cart_item['qty'] ) );
					}

					do_action(
						'asta_cart_item',
						array(
							'product_id' => $cart_item['product_id'],
							'price'      => floatval( get_post_meta( $cart_item['product_id'], 'price', true ) ),
							'qty'        => $cart_item['qty'],
						)
					);
					?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

	</div><!-- .container -->
</main><!-- #main -->
<?php
get_footer();
