<?php
/**
 * Template Name: Thank you
 */

! empty( $_GET['payment_intent'] ) || wp_redirect( get_site_url() ) && exit;

$payment_intent = WPR_THEME_CHACKOUT::instance()->stripe_client->paymentIntents->retrieve( // phpcs:ignore
	preg_replace( '/[^a-zA-Z0-9\_\-]/i', '', $_GET['payment_intent'] ),
);

$order = WPR_THEME_ORDERS::get_order( $payment_intent->id );
! empty( $order ) || wp_redirect( get_site_url() ) && exit;

$order_details = WPR_THEME_ORDERS::get_meta( $order->ID, 'details' );
$paid_status   = reset( $payment_intent->charges->data )->paid;

get_header();

?>

<main id="primary" class="site-main">
	<div class="container">

		<div class="payment-status">
			<?php if ( $paid_status ) : ?>

				<?php
				WPR_THEME_ORDERS::set_order_status( $order->ID, 'paid' );
				WPR_THEME_ORDERS::set_meta( $order->ID, 'oreder_link', preg_replace( '/[^a-zA-Z0-9\@\:\/\%\&\?\#\.\-\_\=]/i', '', $_SERVER['REQUEST_URI'] ) );
				WPR_THEME_ORDERS::crons_after_payment_order_status( $order->ID );
				WPR_THEME_CHACKOUT::remove_order_products_from_user_cart( get_current_user_id(), $order->ID );
				?>

				<h2><?php echo __( 'Thank you for participating in the auctions on the platform!', 'asta-child' ); ?></h2>
				<p><?php echo __( 'We are pleased to inform you that we have received your payment for the auctions you participated in. We would like to express our gratitude for your support and for making our auctions a success.', 'asta-child' ); ?></p>
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
				<h2><?php echo __( 'Thank you for participating in the auctions on the platform!', 'asta-child' ); ?></h2>
				<p><?php echo __( 'We would like to express our gratitude for your support and for making our auctions a success. However, we regret to inform you that we have not yet received your payment for the auctions you participated in.', 'asta-child' ); ?></p>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>				
			<?php endif; ?>
		</div>

		<div class="cart">
			<?php foreach ( $order_details['cart'] as $auction_id => $cart_item ) : ?>
				<?php
				get_template_part(
					'template-parts/sections/cart',
					'item',
					array(
						'auction_id' => $auction_id,
						'now_price'  => $cart_item['now_price'],
					)
				);
				?>
			<?php endforeach; ?>
		</div>

	</div><!-- .container -->
</main><!-- #main -->
<?php
get_footer();
