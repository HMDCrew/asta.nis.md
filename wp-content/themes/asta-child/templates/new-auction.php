<?php
/**
 * Template Name: New auction
 */

redirect_not_logged_user( '/login' );

$auction_id   = get_user_last_edited_auction( get_current_user_id() );
$auction_type = apply_filters( 'wpr_get_auction_type', $auction_id );
get_header();
?>

	<main id="primary" class="site-main">
		<div class="container">

			<input type="text" name="auction-title" placeholder="<?php echo __( 'Auction title', 'asta-child' ); ?>" value="<?php echo get_the_title( $auction_id ); ?>" />

			<div class="row">

				<div class="col-8 auction-content-col">
					<div class="swiper auction-gallery">
						<?php
						do_action(
							'gallery_auction_template',
							array(
								'auction_id'  => $auction_id,
								'last_slide'  => '<img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg" />',
								'slide_after' => '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"/></svg></button>',
							)
						);
						?>
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
					</div>

					<div class="swiper auction-thumbnail">
						<?php
						do_action(
							'gallery_thumbs_auction_template',
							array(
								'auction_id'  => $auction_id,
								'last_slide'  => '<button type="button" class="new-content"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Placeholder_view_vector.svg" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><path id="path837" style="fill:#000000" d="m23.78 23.78v-13.999c0-1.9588 0.536-3.55 1.607-4.7741 1.071-1.2546 2.601-1.8819 4.59-1.8819s3.519 0.6273 4.59 1.8819c1.102 1.2241 1.653 2.8153 1.653 4.7737v13.999h13.816c2.019 0 3.611 0.551 4.773 1.653 1.194 1.071 1.791 2.586 1.791 4.544 0 1.989-0.597 3.534-1.791 4.636-1.162 1.102-2.754 1.652-4.773 1.652h-13.816v13.954c0 1.989-0.551 3.596-1.653 4.82-1.101 1.224-2.632 1.836-4.59 1.836s-3.488-0.612-4.59-1.836c-1.071-1.224-1.607-2.831-1.607-4.82v-13.954h-13.816c-1.9582 0-3.5494-0.596-4.7735-1.79-1.1934-1.224-1.7901-2.723-1.7901-4.498 0-1.958 0.5814-3.473 1.7442-4.544 1.1935-1.102 2.8-1.653 4.8196-1.653h13.816z"></path></svg><input type="file" name="slide-image" hidden /></button>',
								'slide_after' => '<button type="button" class="remove-slide"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"/></svg></button>',
							)
						);
						?>
					</div>

					<div id="editorjs"></div>
				</div><!-- .auction-content-col -->

				<div class="col-4 sidebar">

					<div class="wrap-input">
						<input id="litepicker" type="text" name="auction-date" readonly="true" class="input" placeholder="<?php echo __( 'Auction date', 'asta-child' ); ?>" value="<?php echo apply_filters( 'wpr_get_auction_date', $auction_id ); ?>">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192H400V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192z"/></svg>
						</span>
					</div><!-- start date - end date -->

					<div class="wrap-input">
						<input type="text" name="baze-price" class="input" placeholder="<?php echo __( 'Auction baze price', 'asta-child' ); ?>" value="<?php echo apply_filters( 'wpr_esc_auction_meta', $auction_id, 'baze_price' ); ?>">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"/></svg>
						</span>
					</div><!-- baze start (default = 0) -->

					<div class="wrap-input">
						<input type="text" name="price-increment" class="input" placeholder="<?php echo __( 'Auction bid incrementation', 'asta-child' ); ?>" value="<?php echo apply_filters( 'wpr_esc_auction_meta', $auction_id, 'price_increment' ); ?>">
						<span class="focus-input"></span>
						<span class="symbol-input">
							+ <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"/></svg>
						</span>
					</div><!-- Price bid incrementation ( default +1 ) -->

					<div class="wrap-input select">
						<select name="category" class="input">
							<option value="false"><?php echo __( 'Auction type', 'asta-child' ); ?></option>
							<?php foreach ( get_auctions_categories() as $key => $category ) : ?>
								<option value="<?php echo esc_html( $category->term_id ); ?>"  <?php echo ( $auction_type && $auction_type['id'] === $category->term_id ? 'selected' : '' ); ?>><?php echo esc_html( $category->name ); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="focus-input"></span>
						<span class="symbol-input">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 96C0 60.7 28.7 32 64 32H512c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224z"/></svg>
						</span>
					</div><!-- Auction type -->

					<div class="wrap-input">
						<textarea class="input aditional-info" name="aditional-info" placeholder="<?php echo __( 'Small description', 'asta-child' ); ?>"><?php echo get_post_field( 'post_excerpt', $auction_id ); ?></textarea>
						<span class="focus-input"></span>
						<span class="symbol-input">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 256h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zm256-32H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
						</span>
					</div><!-- Aditional info -->

					<button type="button" class="btn btn-primary save-auction"><?php echo __( 'Save', 'asta-child' ); ?></button>
				</div><!-- .sidebar -->

			</div><!-- .row -->
		</div><!-- .container -->
	</main><!-- #main -->

<?php
get_footer();
