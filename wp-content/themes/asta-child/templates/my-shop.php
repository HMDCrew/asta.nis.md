<?php
/**
 * Template Name: My Shop
 */

ASTA_USER::redirect_not_logged_user( '/shop' );
get_header();

global $wp_query;

$wp_query = new WP_Query(
	array(
		'post_type'      => 'shop',
		'author'         => get_current_user_id(),
		'posts_per_page' => preg_replace( '/[^0-9]/i', '', $wp_query->query_vars['posts_per_page'] ),
		'paged'          => preg_replace( '/[^0-9]/i', '', $wp_query->query['page'] ),
		'order'          => 'DESC',
	)
);
?>

	<main id="primary" class="site-main">
		<div class="container">

			<?php
			do_action(
				'asta_filter_bar',
				array(
					'post_type'  => 'shop',
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
wp_reset_postdata();
get_sidebar();
get_footer();
