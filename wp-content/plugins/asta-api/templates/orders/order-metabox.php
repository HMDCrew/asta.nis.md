<?php
/**
 * Template part for displaying Gateway Options on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list(
	'payment_status' => $payment_status,
	'payment_intent' => $payment_intent,
	'buyer' => $buyer,
	'amount' => $amount,
	'currency' => $currency,
	'cart' => $cart
) = $args;

?>

<table class="custom-table">
	<tbody>
		<tr>
			<th><?php echo __( 'Status', 'asta-api' ); ?></th>
			<td><?php echo $payment_status; ?></td>
		</tr>
		<tr>
			<th><?php echo __( 'Payment intent', 'asta-api' ); ?></th>
			<td><?php echo $payment_intent; ?></td>
		</tr>
		<tr>
			<th><?php echo __( 'Buyer', 'asta-api' ); ?></th>
			<td><a href="<?php echo get_edit_profile_url( $buyer->ID ); ?>"><?php echo $buyer->user_email; ?></a></td>
		</tr>
		<tr>
			<th><?php echo __( 'Total', 'asta-api' ); ?></th>
			<td><?php echo $amount; ?><?php echo $currency; ?></td>
		</tr>
	</tbody>
</table>

<div class="cart">

	<?php if ( ! empty( $cart['auctions_cart'] ) ) : ?>

		<h2><?php echo __( 'Auctions', 'asta-child' ); ?></h2>

		<?php foreach ( $cart['auctions_cart'] as $cart_item ) : ?>
			<?php do_action( 'asta_cart_item', $cart_item ); ?>
		<?php endforeach; ?>

	<?php endif; ?>

	<?php if ( ! empty( $cart['products_cart'] ) ) : ?>

		<h2><?php echo __( 'Products', 'asta-child' ); ?></h2>

		<?php foreach ( $cart['products_cart'] as $cart_item ) : ?>
			<?php
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
