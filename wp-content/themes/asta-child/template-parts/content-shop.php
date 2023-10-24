<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

$price        = get_post_meta( get_the_ID(), 'price', true );
$post_excerpt = get_post_field( 'post_excerpt', get_the_ID() );

?>

<div class="title"><?php echo get_the_title( get_the_ID() ); ?></div>

<div class="row">

	<div class="col-8 product-content-col">
		<div class="swiper gallery">
			<?php
			do_action(
				'asta_gallery_template',
				array(
					'post_id' => get_the_ID(),
				)
			);
			?>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>

		<div class="swiper thumbnails">
			<?php
			do_action(
				'asta_gallery_thumbs_template',
				array(
					'post_id' => get_the_ID(),
				)
			);
			?>
		</div>

		<div class="col-12 js-editor-content">
			<?php the_content(); ?>
		</div>
	</div><!-- .product-content-col -->

	<div class="col-4 sidebar">

		<?php if ( ! empty( $price ) ) : ?>
		<div class="wrap-input price">
			<input type="text" readonly="true" class="input" value="<?php echo $price; ?>">
			<span class="focus-input"></span>
			<span class="symbol-input">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"/></svg>
			</span>
		</div><!-- baze start (default = 0) -->
		<?php endif; ?>

		<?php if ( ! empty( $post_excerpt ) ) : ?>
		<div class="wrap-input post_excerpt">
			<textarea readonly="true" class="input aditional-info"><?php echo $post_excerpt; ?></textarea>
			<span class="focus-input"></span>
			<span class="symbol-input">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 256h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zm256-32H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
			</span>
		</div><!-- Aditional info -->
		<?php endif; ?>

		<button type="button" class="btn btn-primary buy-now d-inline-flex"><?php echo __( 'Buy now', 'asta-child' ); ?></button>

		<?php if ( (int) get_post_field( 'post_author', get_the_ID() ) === get_current_user_id() ) : ?>
			<a href="/edit-shop/?product_id=<?php echo get_the_ID(); ?>" class="btn btn-primary d-inline-flex edit">
				<?php echo __( 'Edit', 'asta-child' ); ?>
			</a>
		<?php endif; ?>

	</div><!-- .sidebar -->
</div>
