<?php
/**
 * Loads the typed date / time / AM-PM enhancement wherever [lh_product_form] renders.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_datetime_typed_assets( $output, $tag ) {
	if ( 'lh_product_form' !== $tag || '' === $output || is_admin() ) {
		return $output;
	}
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'lh-datetime-typed', $uri . '/css/lh-datetime-typed.css', array(), (string) filemtime( $dir . '/css/lh-datetime-typed.css' ) );
	wp_enqueue_script( 'lh-datetime-typed', $uri . '/js/lh-datetime-typed.js', array(), (string) filemtime( $dir . '/js/lh-datetime-typed.js' ), true );
	return $output;
}
add_filter( 'do_shortcode_tag', 'lh_newastro_datetime_typed_assets', 10, 2 );
