<?php
/**
 * Template part for displaying Gateway Revolut on plugin dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

$stored_option = $args['stored_option'];
$usecase       = ! empty( $stored_option['revolut']['use_case'] ) ? $stored_option['revolut']['use_case'] : 'dev';
?>

<div>
	<strong><label for="revolut-dev-public-key">DEV Public Key:</label></strong>
	<div>
		<input type="text" id="revolut-dev-public-key" name="asta_payments_element[revolut][dev_pub]" value="<?php echo ( ! empty( $stored_option['revolut']['dev_pub'] ) ? $stored_option['revolut']['dev_pub'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="revolut-dev-private-key">DEV Private Key:</label></strong>
	<div>
		<input type="text" id="revolut-dev-private-key" name="asta_payments_element[revolut][dev_priv]" value="<?php echo ( ! empty( $stored_option['revolut']['dev_priv'] ) ? $stored_option['revolut']['dev_priv'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<hr />
<br />

<div>
	<strong><label for="revolut-prod-public-key">PROD Public Key:</label></strong>
	<div>
		<input type="text" id="revolut-prod-public-key" name="asta_payments_element[revolut][prod_pub]" value="<?php echo ( ! empty( $stored_option['revolut']['prod_pub'] ) ? $stored_option['revolut']['prod_pub'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="revolut-prod-private-key">PROD Private Key:</label></strong>
	<div>
		<input type="text" id="revolut-prod-private-key" name="asta_payments_element[revolut][prod_priv]" value="<?php echo ( ! empty( $stored_option['revolut']['prod_priv'] ) ? $stored_option['revolut']['prod_priv'] : '' ); ?>" style="width: 600px" />
	</div>
</div>
<br />

<div>
	<strong><label for="revolut-prod-private-key">Use dev or prod:</label></strong>
	<div>
		<input type="radio" id="revolut_use_case_dev" name="asta_payments_element[revolut][use_case]" value="dev" <?php echo ( 'dev' === $usecase ? 'checked' : '' ); ?> />
		<label for="revolut_use_case_dev">Dev keys</label><br>

		<input type="radio" id="revolut_use_case_prod" name="asta_payments_element[revolut][use_case]" value="prod" <?php echo ( 'prod' === $usecase ? 'checked' : '' ); ?> />
		<label for="revolut_use_case_prod">Prod keys</label><br>
	</div>
</div>
<br />
