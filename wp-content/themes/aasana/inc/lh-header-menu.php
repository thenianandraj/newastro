<?php
/**
 * Header mega-menu styles. Does not change pages or URLs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_enqueue_header_menu_assets() {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_style(
		'lh-cormorant',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600;1,700&family=Source+Sans+3:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	$css = get_template_directory() . '/css/lh-header-mega.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'lh-header-mega',
			get_template_directory_uri() . '/css/lh-header-mega.css',
			array( 'cws_main', 'lh-cormorant' ),
			filemtime( $css )
		);
	}

	$js = get_template_directory() . '/js/lh-header-mega.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'lh-header-mega',
			get_template_directory_uri() . '/js/lh-header-mega.js',
			array(),
			filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_header_menu_assets', 40 );
