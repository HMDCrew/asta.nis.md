<?php
/**
 * Template part for displaying auction card
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

list(
	'post_id'         => $post_id,
	'auction_date'    => $auction_date,
	'price'      => $price,
	'price_increment' => $price_increment,
	'auction_type'    => $auction_type,
	'post_excerpt'    => $post_excerpt,
	'post_classes'    => $post_classes,
	'post_link'       => $post_link,
	'url_thumbnail'   => $url_thumbnail,
	'title'           => $title,
	'category_link'   => $category_link,
	'author_id'       => $author_id,
) = $args;
?>

<article id="post-<?php echo $post_id; ?>" class="<?php echo $post_classes; ?>">

	<a href="<?php echo $post_link; ?>" rel="bookmark">
		<img class="thumbnail auction-thumbnail" src="<?php echo $url_thumbnail; ?>" />
	</a>

	<div class="entry-content">

		<h2 class="entry-title"><a href="<?php echo $post_link; ?>" rel="bookmark"><?php echo $title; ?></a></h2>

		<?php if ( ! empty( $auction_date ) ) : ?>
		<div class="date">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192H400V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192z"/></svg>
			<?php echo $auction_date; ?>
		</div>
		<?php endif; ?>


		<?php if ( ! empty( $auction_type ) ) : ?>
		<div class="auction_type">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 96C0 60.7 28.7 32 64 32H512c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224z"/></svg>
			<a href="<?php echo $category_link; ?>"><?php echo $auction_type['name']; ?></a>
		</div>
		<?php endif; ?>


		<?php if ( ! empty( $post_excerpt ) ) : ?>
		<div class="post_excerpt"><?php echo $post_excerpt; ?></div>
		<?php endif; ?>


		<?php if ( ! empty( $price ) ) : ?>
		<div class="price-info">
			<div class="price-info-row">
				<?php if ( ! empty( $price ) ) : ?>
				<div class="price">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"></path></svg>
					<?php echo $price; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $price_increment ) ) : ?>
				<div class="price_increment">(+<?php echo $price_increment; ?>)</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="actions <?php echo get_current_user_id() === $author_id ? 'd-flex' : ''; ?>">
			<a href="<?php echo $post_link; ?>" class="btn btn-primary details"><?php echo __( 'Details', 'asta-template' ); ?></a>

			<?php if ( get_current_user_id() === $author_id ) : ?>
				<a href="/edit-auction/?auction_id=<?php echo $post_id; ?>" class="btn btn-primary d-inline-flex edit">
					<?php echo __( 'Edit', 'asta-template' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

</article>
