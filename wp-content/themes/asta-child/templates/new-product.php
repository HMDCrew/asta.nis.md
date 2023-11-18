<?php
/**
 * Template Name: New product
 */

ASTA_USER::redirect_not_logged_user( '/login' );

$product_id   = get_user_last_edited_post( get_current_user_id(), 'shop' );
$product_type = get_asta_category( $product_id );
get_header();
?>

	<main id="primary" class="site-main">
		<div class="container">

			<input type="text" name="asta-title" placeholder="<?php echo __( 'Auction title', 'asta-child' ); ?>" value="<?php echo get_the_title( $product_id ); ?>" />

			<div class="row">

				<div class="col-8 auction-content-col">
					<div class="swiper gallery">
						<?php
						do_action(
							'asta_gallery_template',
							array(
								'post_id'     => $product_id,
								'last_slide'  => '<img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg" />',
								'slide_after' => '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"/></svg></button>',
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
								'post_id'     => $product_id,
								'last_slide'  => '<button type="button" class="new-content"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><path id="path837" style="fill:#000000" d="m23.78 23.78v-13.999c0-1.9588 0.536-3.55 1.607-4.7741 1.071-1.2546 2.601-1.8819 4.59-1.8819s3.519 0.6273 4.59 1.8819c1.102 1.2241 1.653 2.8153 1.653 4.7737v13.999h13.816c2.019 0 3.611 0.551 4.773 1.653 1.194 1.071 1.791 2.586 1.791 4.544 0 1.989-0.597 3.534-1.791 4.636-1.162 1.102-2.754 1.652-4.773 1.652h-13.816v13.954c0 1.989-0.551 3.596-1.653 4.82-1.101 1.224-2.632 1.836-4.59 1.836s-3.488-0.612-4.59-1.836c-1.071-1.224-1.607-2.831-1.607-4.82v-13.954h-13.816c-1.9582 0-3.5494-0.596-4.7735-1.79-1.1934-1.224-1.7901-2.723-1.7901-4.498 0-1.958 0.5814-3.473 1.7442-4.544 1.1935-1.102 2.8-1.653 4.8196-1.653h13.816z"></path></svg><input type="file" name="slide-image" hidden /></button>',
								'slide_after' => '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"/></svg></button>',
							)
						);
						?>
					</div>

					<div id="editorjs"></div>
				</div><!-- .auction-content-col -->

				<div class="col-4 sidebar">
					<?php get_template_part( 'template-parts/sidebars/product', 'sidebar', array( 'product_id' => $product_id ) ); ?>
				</div><!-- .sidebar -->

			</div><!-- .row -->
		</div><!-- .container -->
	</main><!-- #main -->

<?php
get_footer();
