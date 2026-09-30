<?php
/**
 * Perf helpers: one Google Fonts request + bundled homepage CSS.
 * Does not change page content, booking, or checkout markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single LH font CSS URL (Cormorant + Source Sans 3).
 */
function lh_newastro_fonts_css_url() {
	return 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600;1,700&family=Source+Sans+3:wght@400;500;600;700&display=swap';
}

/**
 * Sitewide fonts — one handle for header, footer, home, service pages.
 */
function lh_newastro_enqueue_site_fonts() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_style(
		'lh-cormorant',
		lh_newastro_fonts_css_url(),
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_site_fonts', 5 );

/**
 * Warm up Google Fonts connections early (non-blocking hints).
 */
function lh_newastro_font_preconnect() {
	if ( is_admin() ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'lh_newastro_font_preconnect', 1 );

/**
 * Homepage section CSS sources (edit these files; bundle rebuilds from mtime).
 *
 * @return string[] Basenames under /css/
 */
function lh_newastro_home_css_parts() {
	return array(
		'lh-home-refresh.css',
		'lh-home-mid.css',
		'lh-home-trust.css',
		'lh-home-services.css',
		'lh-home-about.css',
		'lh-home-reviews.css',
		'lh-home-more.css',
	);
}

/**
 * Build or refresh css/lh-home-bundle.css from part files.
 *
 * @return array{path:string,uri:string,ver:int|string}|null
 */
function lh_newastro_home_bundle_info() {
	$dir   = trailingslashit( get_template_directory() ) . 'css/';
	$uri   = trailingslashit( get_template_directory_uri() ) . 'css/';
	$parts = lh_newastro_home_css_parts();
	$bundle_name = 'lh-home-bundle.css';
	$bundle_path = $dir . $bundle_name;

	$max_mtime = 0;
	$chunks    = array();
	foreach ( $parts as $file ) {
		$path = $dir . $file;
		if ( ! is_readable( $path ) ) {
			continue;
		}
		$max_mtime = max( $max_mtime, (int) filemtime( $path ) );
		$chunks[]  = "/* === {$file} === */\n" . file_get_contents( $path );
	}

	if ( empty( $chunks ) ) {
		return null;
	}

	$needs_write = ! is_readable( $bundle_path ) || (int) filemtime( $bundle_path ) < $max_mtime;
	if ( $needs_write ) {
		$css = implode( "\n\n", $chunks );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- local theme cache rebuild
		@file_put_contents( $bundle_path, $css );
	}

	if ( ! is_readable( $bundle_path ) ) {
		return null;
	}

	return array(
		'path' => $bundle_path,
		'uri'  => $uri . $bundle_name,
		'ver'  => (int) filemtime( $bundle_path ),
	);
}

/**
 * Enqueue one homepage CSS file instead of 7 chained stylesheets.
 */
function lh_newastro_enqueue_home_css_bundle() {
	if ( ! function_exists( 'lh_newastro_is_front' ) || ! lh_newastro_is_front() ) {
		return;
	}

	$info = lh_newastro_home_bundle_info();
	if ( $info ) {
		wp_enqueue_style(
			'lh-home-bundle',
			$info['uri'],
			array( 'cws_main', 'lh-cormorant' ),
			$info['ver']
		);
		return;
	}

	// Fallback: enqueue parts individually if bundle cannot be written.
	$dir  = trailingslashit( get_template_directory() ) . 'css/';
	$uri  = trailingslashit( get_template_directory_uri() ) . 'css/';
	$prev = 'cws_main';
	foreach ( lh_newastro_home_css_parts() as $file ) {
		$path = $dir . $file;
		if ( ! is_readable( $path ) ) {
			continue;
		}
		$handle = 'lh-' . basename( $file, '.css' );
		wp_enqueue_style( $handle, $uri . $file, array( $prev, 'lh-cormorant' ), filemtime( $path ) );
		$prev = $handle;
	}
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_home_css_bundle', 40 );
