<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

$price        = asta_esc_meta( get_the_ID(), 'price' );
$qty          = asta_esc_meta( get_the_ID(), 'qty' );
$auction_type = get_asta_category( get_the_ID() );
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

		<?php if ( ! empty( $qty ) ) : ?>
		<div class="wrap-input">
			<input type="number" readonly="true" name="qty" class="input" placeholder="<?php echo __( 'Quantity', 'asta-child' ); ?>" value="<?php echo $qty; ?>">
			<span class="focus-input"></span>
			<span class="symbol-input">
				<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="20 20 60 60"><path d="m47.44 61.66a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3h-20.49a3.06 3.06 0 0 1 -3-2.88v-14.38a1 1 0 0 1 .91-1h24.5zm29.51 0a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3h-20.49a3.06 3.06 0 0 1 -3-2.88v-14.38a1 1 0 0 1 .91-1h24.5zm-37.36 4.23-.09.11-5.82 6.32-2.63-2.55a.77.77 0 0 0 -1-.08l-.09.08-1.09 1a.62.62 0 0 0 -.07.9l.07.08 3.73 3.54a1.56 1.56 0 0 0 1.08.45 1.43 1.43 0 0 0 1.09-.45l3.14-3.32.63-.67 3.14-3.31a.78.78 0 0 0 .06-.9l-.06-.08-1.09-1a.76.76 0 0 0 -1-.12zm29.51 0-.1.11-5.82 6.32-2.64-2.55a.75.75 0 0 0 -1-.08l-.09.08-1.09 1a.62.62 0 0 0 -.07.9l.07.08 3.73 3.54a1.54 1.54 0 0 0 1.08.45 1.43 1.43 0 0 0 1.09-.45l3.14-3.32.63-.67 3.14-3.31a.78.78 0 0 0 .06-.9l-.06-.08-1.07-1.01a.76.76 0 0 0 -1-.11zm-23.43-14.41a3 3 0 0 1 2.85 2.87v3.24a1 1 0 0 1 -.84 1h-26.68a1 1 0 0 1 -.94-.9v-3.16a3 3 0 0 1 2.69-3.05h23zm31.48 0a3 3 0 0 1 2.85 2.87v3.24a1 1 0 0 1 -.84 1h-26.73a1 1 0 0 1 -1-.9v-3.16a3 3 0 0 1 2.68-3.05h23zm-15-21.29a1 1 0 0 1 1 .91v14.37a3.06 3.06 0 0 1 -2.87 3.05h-20.44a3.06 3.06 0 0 1 -3.05-2.87v-14.44a1 1 0 0 1 .9-1h24.51zm-7.85 4.22-.09.08-5.82 6.32-2.59-2.56a.76.76 0 0 0 -1-.07l-.09.07-1.08 1a.61.61 0 0 0 -.07.9l.07.08 3.72 3.53a1.56 1.56 0 0 0 1.09.45 1.43 1.43 0 0 0 1.08-.45l3.14-3.31.64-.67 3.13-3.32a.78.78 0 0 0 .06-.9l-.06-.07-1.08-1a.77.77 0 0 0 -1-.08zm7.9-14.41a3.06 3.06 0 0 1 3 2.88v3.23a1 1 0 0 1 -.91 1h-28.52a1 1 0 0 1 -1-.91v-3.14a3.06 3.06 0 0 1 2.87-3h24.56z"/></svg>
			</span>
		</div><!-- Quantity -->
		<?php endif; ?>

		<?php if ( ! empty( $auction_type ) ) : ?>
		<div class="wrap-input">
			<input type="text" readonly="true" class="input" value="<?php echo $auction_type['name']; ?>">
			<span class="focus-input"></span>
			<span class="symbol-input">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 96C0 60.7 28.7 32 64 32H512c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224z"/></svg>
			</span>
		</div><!-- Auction type -->
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
