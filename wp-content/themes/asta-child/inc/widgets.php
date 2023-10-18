<?php

/**
 * Widget WordPress
 *
 * @author Andrei Leca
 */

/**
 * Footer one Widget
 */
register_sidebar(
	array(
		'name'          => esc_html__( 'Footer 1', 'asta-child' ),
		'id'            => 'footer-one-widget',
		'before_widget' => '<div id="%1$s" class="footer-one footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	)
);

/**
 * Footer two Widget
 */
register_sidebar(
	array(
		'name'          => esc_html__( 'Footer 2', 'asta-child' ),
		'id'            => 'footer-two-widget',
		'before_widget' => '<div id="%1$s" class="footer-two footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	)
);

/**
 * Footer three Widget
 */
register_sidebar(
	array(
		'name'          => esc_html__( 'Footer 3', 'asta-child' ),
		'id'            => 'footer-three-widget',
		'before_widget' => '<div id="%1$s" class="footer-three footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	)
);


/**
 * Disable inactive widgets
 *
 * @param array $sidebars_widgets
 * @return $sidebars_widgets
 */
function wpr_remove_pages_widget( $sidebars_widgets ) {

	unset( $sidebars_widgets['wp_inactive_widgets'] );

	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'wpr_remove_pages_widget' );
