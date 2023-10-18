<?php
/**
 * Template part for displaying Gateway Options on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list('payment_status' => $payment_status, 'payment_intent' => $payment_intent, 'buyer' => $buyer, 'amount' => $amount, 'currency' => $currency, 'cart' => $cart) = $args;

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
	<?php foreach ( $cart as $auction_id => $cart_item ) : ?>
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
