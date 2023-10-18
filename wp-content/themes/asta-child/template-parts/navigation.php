<?php
/**
 * Template part for displaying navigation
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

$next_link = get_next_posts_link( __( 'More', 'asta-child' ) );
$prev_link = get_previous_posts_link( __( 'Prev', 'asta-child' ) );

?>

<div class="navigation d-flex">
	<?php if ( $prev_link ) : ?>
		<?php echo $prev_link; ?>
	<?php endif; ?>

	<?php if ( $next_link ) : ?>
		<?php echo $next_link; ?>
	<?php endif; ?>
</div>
