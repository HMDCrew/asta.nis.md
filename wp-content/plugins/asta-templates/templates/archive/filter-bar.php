<?php
/**
 * Template part for displaying Filter bar
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

list(
	'categories' => $categories,
	'slider_min' => $slider_min,
	'slider_max' => $slider_max,
	'visibility' => $visibility,
	'search_label'   => $search_label,
	'category_label' => $category_label,
	'date_label'     => $date_label,
) = $args;
?>

<div class="filter-container">
	<div class="filter-bar d-flex w-100 align-center">

		<?php if ( $visibility['search'] || $visibility['category'] ) : ?>
		<div class="search d-flex w-100">

			<?php if ( $visibility['search'] ) : ?>
			<div class="wrap-input w-auto">
				<input class="input" type="text" name="search-auctions" placeholder="<?php echo $search_label; ?>">
				<span class="focus-input"></span>
				<span class="symbol-input">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
				</span>
			</div>
			<?php endif; ?>

			<?php if ( $visibility['category'] ) : ?>
			<div class="wrap-input select w-auto">
				<select name="category" class="input">
					<option value="false"><?php echo $category_label; ?></option>
					<?php foreach ( $categories as $key => $category ) : ?>
						<option value="<?php echo esc_html( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="focus-input"></span>
				<span class="symbol-input">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 96C0 60.7 28.7 32 64 32H512c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224z"/></svg>
				</span>
			</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<?php if ( $visibility['date'] ) : ?>
		<div class="wrap-input date-range-wrap">
			<input id="date-range" type="text" name="date-range" readonly="true" class="input" placeholder="<?php echo $date_label; ?>">
			<span class="focus-input"></span>
			<span class="symbol-input">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192H400V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192z"/></svg>
			</span>
		</div>
		<?php endif; ?>

		<?php if ( $visibility['price'] ) : ?>
		<div class="price w-100">

			<div class="labels d-flex justify-around">
				<div class="min-price-container"><span><?php echo __( 'min', 'asta-template' ); ?>:</span><span class="min-price"></span></div>
				<div class="max-price-container"><span><?php echo __( 'max', 'asta-template' ); ?>:</span><span class="max-price"></span></div>
			</div>

			<tc-range-slider
				id="price_range"
				step="10"
				min="<?php echo $slider_min; ?>"
				max="<?php echo $slider_max; ?>"
				value1="<?php echo $slider_min; ?>"
				value2="<?php echo $slider_max; ?>"
				value1-label=".min-price"
				value2-label=".max-price"
			></tc-range-slider>

		</div>
		<?php endif; ?>

	</div>

	<button class="btn btn-primary mobile-filter-btn w-100">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"/></svg>
	</button>
</div>
