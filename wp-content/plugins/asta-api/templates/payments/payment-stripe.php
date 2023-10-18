<?php
/**
 * Template part for displaying Gateway Stripe on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

$stored_option = $args['stored_option'];
$usecase       = ! empty( $stored_option['stripe']['use_case'] ) ? $stored_option['stripe']['use_case'] : '';
?>

<div>
	<strong><label for="stripe-dev-public-key">DEV Public Key:</label></strong>
	<div>
		<input type="text" id="stripe-dev-public-key" name="asta_payments_element[stripe][dev_pub]" value="<?php echo ( ! empty( $stored_option['stripe']['dev_pub'] ) ? $stored_option['stripe']['dev_pub'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="stripe-dev-private-key">DEV Private Key:</label></strong>
	<div>
		<input type="text" id="stripe-dev-private-key" name="asta_payments_element[stripe][dev_priv]" value="<?php echo ( ! empty( $stored_option['stripe']['dev_priv'] ) ? $stored_option['stripe']['dev_priv'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="stripe-dev-signature-key">DEV Signature Key WebHook:</label></strong>
	<div>
		<input type="text" id="stripe-dev-signature-key" name="asta_payments_element[stripe][dev_signature]" value="<?php echo ( ! empty( $stored_option['stripe']['dev_signature'] ) ? $stored_option['stripe']['dev_signature'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<hr />
<br />

<div>
	<strong><label for="stripe-prod-public-key">PROD Public Key:</label></strong>
	<div>
		<input type="text" id="stripe-prod-public-key" name="asta_payments_element[stripe][prod_pub]" value="<?php echo ( ! empty( $stored_option['stripe']['prod_pub'] ) ? $stored_option['stripe']['prod_pub'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="stripe-prod-private-key">PROD Private Key:</label></strong>
	<div>
		<input type="text" id="stripe-prod-private-key" name="asta_payments_element[stripe][prod_priv]" value="<?php echo ( ! empty( $stored_option['stripe']['prod_priv'] ) ? $stored_option['stripe']['prod_priv'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="stripe-prod-signature-key">PROD Signature Key WebHook:</label></strong>
	<div>
		<input type="text" id="stripe-prod-signature-key" name="asta_payments_element[stripe][prod_signature]" value="<?php echo ( ! empty( $stored_option['stripe']['prod_signature'] ) ? $stored_option['stripe']['prod_signature'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />


<div>
	<strong><label for="stripe-prod-private-key">Use dev or prod:</label></strong>
	<div>
		<input type="radio" id="stripe_use_case_dev" name="asta_payments_element[stripe][use_case]" value="dev" <?php echo ( 'dev' === $usecase ? 'checked' : '' ); ?> />
		<label for="stripe_use_case_dev">Dev keys</label><br>

		<input type="radio" id="stripe_use_case_prod" name="asta_payments_element[stripe][use_case]" value="prod" <?php echo ( 'prod' === $usecase ? 'checked' : '' ); ?> />
		<label for="stripe_use_case_prod">Prod keys</label><br>
	</div>
</div>
<br />
