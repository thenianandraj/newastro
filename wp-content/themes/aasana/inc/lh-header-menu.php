<?php
/**
 * Header mega-menu styles. Does not change pages or URLs.
 *
 * Loads once: enqueued lh-header-mega.css + small critical inline in wp_head.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_enqueue_header_menu_assets() {
	if ( is_admin() ) {
		return;
	}

	// Fonts: lh_newastro_enqueue_site_fonts() in lh-perf-assets.php

	$css = get_template_directory() . '/css/lh-header-mega.css';
	if ( file_exists( $css ) ) {
		// After theme main CSS so mega-menu rules win without duplicating the file.
		$deps = array( 'lh-cormorant' );
		if ( wp_style_is( 'cws_main', 'registered' ) || wp_style_is( 'cws_main', 'enqueued' ) ) {
			$deps[] = 'cws_main';
		}
		if ( wp_style_is( 'style', 'registered' ) || wp_style_is( 'style', 'enqueued' ) ) {
			$deps[] = 'style';
		}

		wp_enqueue_style(
			'lh-header-mega',
			get_template_directory_uri() . '/css/lh-header-mega.css',
			$deps,
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
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_header_menu_assets', 1000 );

/**
 * Tiny FOUC-critical rules only (not the full mega CSS file).
 */
function lh_newastro_header_critical_css() {
	return '.header_wrapper_container,.header_wrapper_container.header_outside_slider{position:relative!important;top:auto!important;left:auto!important;width:100%!important;z-index:10000!important;overflow:visible!important;background:#fff!important;transform:none!important}.header_wrapper_container.header_outside_slider:after{content:none!important;display:none!important}.site_header .header_overlay{display:none!important}.site_header .header_container,.header_outside_slider .site_header .header_container{background:#fff!important;overflow:visible!important}.site_header .header_logo_part .logo{display:inline-block!important;visibility:visible!important;opacity:1!important}.header_wrapper_container .site_header .header_logo_part .logo>img:not(.logo_sticky):not(.logo_mobile){display:inline-block!important;visibility:visible!important;opacity:1!important;max-height:56px!important;height:auto!important;width:auto!important}.header_wrapper_container .site_header .header_logo_part .logo>img.logo_sticky,.header_wrapper_container .site_header .header_logo_part .logo>img.logo_mobile{display:none!important}.mini-cart .woo_mini_cart{position:absolute!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}@media (max-width:979px){.mobile_menu_hamburger{display:inline-block!important;visibility:visible!important;opacity:1!important;width:44px!important;height:44px!important;position:relative!important;z-index:100080!important}.mobile_menu_hamburger span{display:block!important;position:absolute!important;left:10px!important;right:10px!important;top:21px!important;height:2px!important;background:#1a2744!important}.mobile_menu_hamburger span::before,.mobile_menu_hamburger span::after{content:""!important;display:block!important;position:absolute!important;left:0!important;width:100%!important;height:2px!important;background:#1a2744!important}.mobile_menu_hamburger span::before{top:-8px!important}.mobile_menu_hamburger span::after{bottom:-8px!important}}';
}

function lh_newastro_print_header_critical_css() {
	if ( is_admin() ) {
		return;
	}
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;
	echo '<style id="lh-header-critical">' . lh_newastro_header_critical_css() . '</style>' . "\n";
}
add_action( 'wp_head', 'lh_newastro_print_header_critical_css', 999 );

function lh_newastro_header_body_class( $classes ) {
	$classes[] = 'lh-header-solid';
	return $classes;
}
add_filter( 'body_class', 'lh_newastro_header_body_class' );
