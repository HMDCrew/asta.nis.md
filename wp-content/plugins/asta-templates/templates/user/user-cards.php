<?php
/**
 * Template part for displaying auction Order item
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list(
	'credit_cards'    => $credit_cards,
	'has_placeholder' => $has_placeholder
) = $args;
?>

<?php if ( $has_placeholder ) : ?>
	<div class="new-carta d-none">
		<form id="cards-form">
			<div id="card-element"></div>
			<div id="card-errors" role="alert"></div>
			<button id="submit" type="button" class="btn btn-primary"><?php echo __( 'add card', 'asta-child' ); ?></button>
		</form>
	</div>
<?php endif; ?>

<div class="lista-catre">

	<div class="contaier-carte">
		<?php foreach ( $credit_cards as $card ) : ?>
			<div class="card" cart-id="<?php echo base64_encode( ASTA_STRIPE::sec()->encrypt( $card->id ) ); ?>">
				<div class="card-type"><?php echo $card->brand; ?></div>
				<div class="card-numbers">**** **** **** <?php echo $card->last4; ?></div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $has_placeholder ) : ?>
		<div class="contaier-placeholder">
			<div class="card placeholder create-cart">
				<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
					<path d="M4 12H20M12 4V20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
		</div>
	<?php endif; ?>
</div>