<?php
/**
 * Template part for displaying auction gallery
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list('last_slide' => $last_slide, 'urls' => $urls, 'slide_before' => $slide_before, 'slide_after' => $slide_after) = $args;

?>

<div class="swiper-wrapper">

	<?php if ( ! empty( $urls ) ) : ?>
		<?php foreach ( $urls as $url ) : ?>

			<a class="swiper-slide" href="<?php echo esc_url( $url ); ?>" data-fancybox="gallery">
				<?php echo $slide_before; ?>
				<img src="<?php echo esc_url( single_url_slash( $url ) ); ?>" />
				<?php echo $slide_after; ?>
			</a>

		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( ! empty( $last_slide ) ) : ?>
		<div class="swiper-slide empty-slide"><?php echo $last_slide; ?></div>
	<?php endif; ?>

</div>
