<?php
/**
 * Template Name: My Orders
 */

defined( 'ABSPATH' ) || exit;
ASTA_USER::redirect_not_logged_user( '/login' );

get_header();

$orders = ASTA_THEME_ORDERS::get_user_orders( get_current_user_id() );
?>

<main id="primary" class="site-main">
	<div class="container">

		<h1 class="title"><?php echo __( 'My Orders', 'asta-child' ); ?></h1>

		<?php if ( ! empty( $orders ) ) : ?>

			<?php foreach ( $orders as $order ) : ?>
				<?php do_action( 'asta_order_item', (array) $order ); ?>
			<?php endforeach; ?>

		<?php else : ?>

			<div><?php echo __( 'You haven\'t any order', 'asta-child' ); ?></div>

		<?php endif; ?>

	</div><!-- .container -->
</main><!-- #main -->
<?php
get_footer();
