<?php
/**
 * Template part for displaying Gateway Options on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list('start_date' => $start_date, 'end_date' => $end_date, 'gallery' => $gallery, 'auction_date' => $auction_date, 'price' => $price, 'price_increment' => $price_increment) = $args;
?>


<table class="custom-table">
	<!-- no save info form only show details -->
	<tbody>
		<tr>
			<th><?php echo __( 'Start date', 'asta-api' ); ?></th>
			<td><input type="text" name="start_date" value="<?php echo $start_date; ?>" /></td>
		</tr>
		<tr>
			<th><?php echo __( 'End date', 'asta-api' ); ?></th>
			<td><input type="text" name="end_date" value="<?php echo $end_date; ?>" /></td>
		</tr>
		<tr>
			<th><?php echo __( 'Auction date', 'asta-api' ); ?></th>
			<td><input type="text" name="auction_date" value="<?php echo $auction_date; ?>" /></td>
		</tr>
		<tr>
			<th><?php echo __( 'Baze price', 'asta-api' ); ?></th>
			<td><input type="text" name="price" value="<?php echo $price; ?>" /></td>
		</tr>
		<tr>
			<th><?php echo __( 'Price increment', 'asta-api' ); ?></th>
			<td><input type="text" name="price_increment" value="<?php echo $price_increment; ?>" /></td>
		</tr>
	</tbody>
</table>
