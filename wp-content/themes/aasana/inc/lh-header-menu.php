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
			array(),
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

function lh_newastro_header_critical_css() {
	$css  = '.header_wrapper_container,.header_wrapper_container.header_outside_slider{position:relative!important;top:auto!important;left:auto!important;width:100%!important;z-index:10000!important;overflow:visible!important;background:#fff!important;transform:none!important}.header_wrapper_container.header_outside_slider:after{content:none!important;display:none!important}.header_wrapper_container .header_box,.header_wrapper_container .bg_page_header{z-index:0!important;position:relative!important}.header_wrapper_container .site_header,.header_wrapper_container .header_container{z-index:20!important;position:relative!important}.site_header .header_overlay{display:none!important}.site_header .header_container,.header_outside_slider .site_header .header_container,.sticky_header.sticky_active .header_cont .header_container .menu_box{background:#fff!important;overflow:visible!important}#main,.page_content{position:relative;z-index:1}.sticky_header.sticky_active,.site_header.sticky{z-index:100050!important;overflow:visible!important;height:auto!important;max-height:none!important}@media (min-width:980px){.sticky_header:not(.sticky_active){position:fixed!important;top:0!important;left:0!important;width:100%!important;-webkit-transform:translateY(-100%)!important;transform:translateY(-100%)!important;visibility:hidden!important;pointer-events:none!important;overflow:hidden!important}html.wp-toolbar .sticky_header.sticky_active,html.wp-toolbar .site_header.sticky,body.admin-bar .sticky_header.sticky_active,body.admin-bar .site_header.sticky{top:32px!important}}.header_zone .main-nav-container .main-menu>.menu-item>a,.header_zone .main-nav-container .main-menu>.menu-item>span,.site_header .main-nav-container .main-menu>.menu-item>a,.sticky_header.sticky_active .main-nav-container .main-menu>.menu-item>a,.header_site_title,.site_name a{color:#1a2744!important}';
	$css .= '.site_header .header_logo_part .logo,.sticky_header.sticky_active .site_header .header_logo_part .logo{display:inline-block!important;visibility:visible!important;opacity:1!important}.header_wrapper_container .site_header .header_logo_part .logo>img:not(.logo_sticky):not(.logo_mobile){display:inline-block!important;visibility:visible!important;opacity:1!important;max-height:56px!important;height:auto!important;width:auto!important}.header_wrapper_container .site_header .header_logo_part .logo>img.logo_sticky,.header_wrapper_container .site_header .header_logo_part .logo>img.logo_mobile{display:none!important}.sticky_header.sticky_active .site_header .header_logo_part .logo>img.logo_sticky{display:inline-block!important;visibility:visible!important;opacity:1!important;max-height:56px!important;height:auto!important;width:auto!important}.sticky_header.sticky_active .site_header .header_logo_part .logo>img:not(.logo_sticky){display:none!important}';
	$file = get_template_directory() . '/css/lh-header-mega.css';
	if ( is_readable( $file ) ) {
		$css .= file_get_contents( $file );
	}
	return $css;
}

function lh_newastro_print_header_critical_css() {
	if ( is_admin() ) {
		return;
	}
	echo '<style id="lh-header-critical">' . lh_newastro_header_critical_css() . '</style>' . "\n";
}
add_action( 'wp_head', 'lh_newastro_print_header_critical_css', 999 );
add_action( 'wp_footer', 'lh_newastro_print_header_critical_css', 1 );

function lh_newastro_header_body_class( $classes ) {
	$classes[] = 'lh-header-solid';
	return $classes;
}
add_filter( 'body_class', 'lh_newastro_header_body_class' );
