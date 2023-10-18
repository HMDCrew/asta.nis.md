<?php
/**
 * Template part for displaying Gateway Dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="admin-settings-asta-dashboard">
	<div id="Notifications"></div>

	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">

		<input type="hidden" name="action" value="save_asta_dashboard">

		<?php
			do_settings_sections( 'asta_payments' );
			submit_button( __( 'Save', 'asta-api' ) );
		?>
	</form>
</div>
