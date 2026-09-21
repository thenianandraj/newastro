<?php
/**
 * Service landing pages: left content / right booking form.
 * Layout CSS only — does not edit page or product content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_is_service_landing() {
	if ( is_admin() || is_front_page() || ! is_page() ) {
		return false;
	}
// Frontend Editor — don't apply layout CSS/JS
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	return false;
}
if ( isset( $_GET['vc_editable'] ) || isset( $_GET['vc_action'] ) ) {
	return false;
}
	$post = get_queried_object();
	if ( ! $post || empty( $post->post_content ) ) {
		return false;
	}

	$content = (string) $post->post_content;

	return (
		false !== strpos( $content, 'service-row' )
		|| false !== strpos( $content, 'wcpa_form_outer' )
		|| false !== strpos( $content, 'form class="cart"' )
		|| false !== strpos( $content, "form class='cart'" )
	);
}

function lh_newastro_service_page_body_class( $classes ) {
	if ( lh_newastro_is_service_landing() ) {
		$classes[] = 'lh-service-page';
	}
	return $classes;
}
add_filter( 'body_class', 'lh_newastro_service_page_body_class' );

function lh_newastro_enqueue_service_page_assets() {
	if ( ! lh_newastro_is_service_landing() ) {
		return;
	}

	$css = get_template_directory() . '/css/lh-service-page.css';
	if ( ! file_exists( $css ) ) {
		return;
	}

	wp_enqueue_style(
		'lh-service-page',
		get_template_directory_uri() . '/css/lh-service-page.css',
		array( 'cws_main', 'lh-cormorant' ),
		filemtime( $css )
	);

	$js = get_template_directory() . '/js/lh-service-page.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'lh-service-page',
			get_template_directory_uri() . '/js/lh-service-page.js',
			array(),
			filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_service_page_assets', 45 );
