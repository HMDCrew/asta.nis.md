<?php
/**
 * Template part for displaying Gateway Options on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

$stored_option    = $args['stored_option'];
$selected_gateway = ( ! empty( $args['stored_option']['selected_gateway'] ) ? $args['stored_option']['selected_gateway'] : '' );
unset( $stored_option['selected_gateway'] );

?>

<div>
	<strong><label for="payments-gateways">Select payment gateway: </label></strong>
	<select id="payments-gateways" name="asta_payments_element[selected_gateway]">
		<?php foreach ( $stored_option as $key => $option ) : ?>
			<option <?php echo ( $selected_gateway === $key ? 'selected' : '' ); ?> value="<?php echo $key; ?>" ><?php echo ucfirst( $key ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
<br />
