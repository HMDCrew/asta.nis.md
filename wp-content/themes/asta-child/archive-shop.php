<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

get_header();

global $wp_query;

$wp_query = new WP_Query(
	array(
		'post_type'      => 'shop',
		'posts_per_page' => preg_replace( '/[^0-9]/i', '', $wp_query->query_vars['posts_per_page'] ),
		'paged'          => ! empty( $wp_query->query['page'] ) ? preg_replace( '/[^0-9]/i', '', $wp_query->query['page'] ) : 1,
		'order'          => 'DESC',
		'meta_query'     => array(
			array(
				'key'     => 'qty',
				'compare' => '>',
				'value'   => 0,
			),
		),
	)
);
?>

	<main id="primary" class="site-main">
		<div class="container">

			<?php
			do_action(
				'asta_filter_bar',
				array(
					'visibility' => array(
						'search'   => true,
						'category' => true,
						'date'     => false,
						'price'    => true,
					),
				),
			);
			?>

			<div class="list-content list-products">
				<?php if ( have_posts() ) : ?>

					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						/**
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						do_action( 'asta_card_shop', array() );

					endwhile;

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>
			</div>

			<?php get_template_part( 'template-parts/navigation' ); ?>

		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
