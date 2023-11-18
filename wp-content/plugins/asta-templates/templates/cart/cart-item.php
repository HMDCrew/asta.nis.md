<?php
/**
 * Template part for displaying auction cart item
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

defined( 'ABSPATH' ) || exit;

list(
	'post_id'      => $post_id,
	'price'        => $price,
	'auction_date' => $date,
	'url'          => $url,
	'img'          => $img,
	'title'        => $title,
	'qty'          => $qty,
	'max_qty'      => $max_qty,
	'qty_label'    => $qty_label
) = $args;
?>

<div class="cart-item" post_id="<?php echo $post_id; ?>" max_qty="<?php echo $max_qty; ?>">

	<div class="thumbnail">
		<a href="<?php echo $url; ?>" rel="bookmark">
			<img src="<?php echo $img; ?>" />
		</a>
	</div>

	<div class="title">

		<h2 class="entry-title">
			<a href="<?php echo $url; ?>" rel="bookmark"><?php echo $title; ?></a>
		</h2>

		<?php if ( ! empty( $date ) ) : ?>
			<sub><?php echo $date; ?></sub>
		<?php endif; ?>

		<?php if ( ! empty( $qty ) ) : ?>
			<sub class="qty"><?php echo $qty_label; ?>: <span><?php echo $qty; ?></span></sub>
		<?php endif; ?>

	</div>

	<div class="actions">
		<div class="price-info">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"></path></svg>
			<span><?php echo $price; ?></span>
		</div>

		<?php if ( ! empty( $qty ) && is_page_template( 'templates/cart.php' ) ) : ?>
		<div class="qty-actions">
			<button type="button" class="btn btn-primary plus_qty">
				<svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
					width="800px" height="800px" viewBox="0 0 45.402 45.402"
					xml:space="preserve">
					<path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141
						c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27
						c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435
						c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z"/>
				</svg>
			</button>
			<button type="button" class="btn btn-primary minus_qty">
				<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 -4 12 12" fill="none">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 3.5H10.5C11.3284 3.5 12 2.8284 12 2C12 1.1716 11.3284 0.5 10.5 0.5H1.5C0.67157 0.5 0 1.1716 0 2C0 2.8284 0.67157 3.5 1.5 3.5z" />
				</svg>
			</button>
		</div>
		<?php endif; ?>
	</div>

	<div class="remove">
		<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
			<path d="M8.00386 9.41816C7.61333 9.02763 7.61334 8.39447 8.00386 8.00395C8.39438 7.61342 9.02755 7.61342 9.41807 8.00395L12.0057 10.5916L14.5907 8.00657C14.9813 7.61605 15.6144 7.61605 16.0049 8.00657C16.3955 8.3971 16.3955 9.03026 16.0049 9.42079L13.4199 12.0058L16.0039 14.5897C16.3944 14.9803 16.3944 15.6134 16.0039 16.0039C15.6133 16.3945 14.9802 16.3945 14.5896 16.0039L12.0057 13.42L9.42097 16.0048C9.03045 16.3953 8.39728 16.3953 8.00676 16.0048C7.61624 15.6142 7.61624 14.9811 8.00676 14.5905L10.5915 12.0058L8.00386 9.41816Z" fill="#0F0F0F"/>
			<path fill-rule="evenodd" clip-rule="evenodd" d="M23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12ZM3.00683 12C3.00683 16.9668 7.03321 20.9932 12 20.9932C16.9668 20.9932 20.9932 16.9668 20.9932 12C20.9932 7.03321 16.9668 3.00683 12 3.00683C7.03321 3.00683 3.00683 7.03321 3.00683 12Z" fill="#0F0F0F"/>
		</svg>
	</div>
</div>
