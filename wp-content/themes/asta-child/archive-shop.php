<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Asta
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="container">

			<?php do_action( 'asta_filter_bar', array() ); ?>

			<div class="list-auction">
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
						get_template_part( 'template-parts/card', 'shop' );

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
