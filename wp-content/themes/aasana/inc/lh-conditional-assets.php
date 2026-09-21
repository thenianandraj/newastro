<?php
/**
 * Conditional theme/plugin assets: dequeue unused JS/CSS per page.
 * Does not change page content or booking/checkout markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skip when frontend editor / admin would break without full assets.
 */
function lh_newastro_assets_should_skip() {
	if ( is_admin() ) {
		return true;
	}
	if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
		return true;
	}
	if ( isset( $_GET['vc_editable'] ) || isset( $_GET['vc_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return true;
	}
	return false;
}

/**
 * Text blob used to detect shortcodes / classes that need libraries.
 */
function lh_newastro_assets_scan_text() {
	static $text = null;
	if ( null !== $text ) {
		return $text;
	}

	$parts = array();

	$post = get_queried_object();
	if ( $post instanceof WP_Post && ! empty( $post->post_content ) ) {
		$parts[] = (string) $post->post_content;
	}

	// Front page can be a static page; also scan blog index when relevant.
	if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
		$fp = (int) get_option( 'page_on_front' );
		if ( $fp && ( ! $post instanceof WP_Post || (int) $post->ID !== $fp ) ) {
			$fp_post = get_post( $fp );
			if ( $fp_post && ! empty( $fp_post->post_content ) ) {
				$parts[] = (string) $fp_post->post_content;
			}
		}
	}

	if ( function_exists( 'lh_newastro_is_service_landing' ) && lh_newastro_is_service_landing() && $post instanceof WP_Post ) {
		$parts[] = (string) $post->post_content;
	}

	$text = implode( "\n", $parts );
	return $text;
}

/**
 * @param string $haystack Scan text.
 * @param string[] $needles Substrings (case-sensitive).
 */
function lh_newastro_assets_text_has( $haystack, $needles ) {
	foreach ( (array) $needles as $n ) {
		if ( '' !== $n && false !== strpos( $haystack, $n ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Whether current request is a WooCommerce surface (or needs mini-cart styles lightly).
 */
function lh_newastro_assets_is_woo_surface() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}
	return (
		is_woocommerce()
		|| is_cart()
		|| is_checkout()
		|| is_account_page()
		|| ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() )
	);
}

/**
 * Map of which libraries this request needs.
 *
 * @return array<string,bool>
 */
function lh_newastro_assets_needs() {
	static $needs = null;
	if ( null !== $needs ) {
		return $needs;
	}

	$c    = lh_newastro_assets_scan_text();
	$woo  = lh_newastro_assets_is_woo_surface();
	$svc  = function_exists( 'lh_newastro_is_service_landing' ) && lh_newastro_is_service_landing();
	$home = is_front_page();

	$needs = array(
		'fancybox'  => (
			$woo
			|| lh_newastro_assets_text_has( $c, array( 'fancy', 'fancybox', '[gallery', 'cws_img_frame', 'post_media' ) )
		),
		'owl'       => lh_newastro_assets_text_has( $c, array(
			'owl',
			'carousel',
			'cws_sc_carousel',
			'widget_carousel',
			'cws_vc_shortcode_carousel',
			'posts_grid',
		) ),
		'isotope'   => (
			is_home()
			|| is_category()
			|| is_tag()
			|| is_post_type_archive( array( 'cws_portfolio', 'cws_staff', 'cws_classes', 'cws_testimonial' ) )
			|| lh_newastro_assets_text_has( $c, array( 'isotope', 'news-pinterest', 'cws_portfolio', 'posts_grid', 'portfolio_item' ) )
		),
		'odometer'  => lh_newastro_assets_text_has( $c, array( 'milestone', 'odometer', 'cws_vc_shortcode_milestone' ) ),
		'wow'       => lh_newastro_assets_text_has( $c, array( ' wow', 'class="wow', "class='wow", 'data-wow-' ) ),
		'tweenmax'  => lh_newastro_assets_text_has( $c, array( 'add_animation_icon', 'TweenMax', 'goo' ) ),
		'yt'        => lh_newastro_assets_text_has( $c, array( 'cws_Yt_video_bg', 'youtube.com/player_api', 'yt_player', 'data-video-source' ) ),
		'vimeo'     => lh_newastro_assets_text_has( $c, array( 'cws_Vimeo_video_bg', 'vimeo' ) ),
		'parallax'  => lh_newastro_assets_text_has( $c, array( 'cws_prlx', 'parallax', 'cws_parallax' ) ),
		'skrollr'   => lh_newastro_assets_text_has( $c, array( 'animate_title', 'skrollr' ) ),
		'select2'   => (
			$woo
			|| $svc
			|| lh_newastro_assets_text_has( $c, array(
				'select2',
				'wpcf7',
				'contact-form-7',
				'wcpa',
				'form class="cart"',
				"form class='cart'",
				'<select',
			) )
		),
		'animate'   => false, // set below
		'woo_css'   => $woo,
		'woo_js'    => $woo,
		'sticky_sidebar' => false, // set below
	);

	// animate.css: keep with wow or explicit animate classes in content.
	$needs['animate'] = $needs['wow'] || lh_newastro_assets_text_has( $c, array( 'animated ', 'animate.css', 'wow ' ) );

	$needs['sticky_sidebar'] = lh_newastro_needs_sticky_sidebar();

	// Homepage: drop heavy libs unless content actually needs them (already detected above).
	if ( $home && ! $woo ) {
		// Prefer dropping YouTube API / tweenmax when unused (common home case).
		// Needs flags already false when not in content.
	}

	/**
	 * Allow overrides: add_filter( 'lh_newastro_assets_needs', fn( $needs ) => $needs );
	 */
	$needs = apply_filters( 'lh_newastro_assets_needs', $needs, $c );

	return $needs;
}

/**
 * Guard scripts.js when optional libs were dequeued.
 */
function lh_newastro_assets_script_guards() {
	if ( ! wp_script_is( 'cws_scripts', 'enqueued' ) && ! wp_script_is( 'cws_scripts', 'registered' ) ) {
		return;
	}

	$n    = lh_newastro_assets_needs();
	$parts = array( '(function ($) { if (!$) { return; }' );

	if ( empty( $n['fancybox'] ) ) {
		$parts[] = '$.fn.fancybox = function () { return this; };';
		$parts[] = 'window.fancybox_init = function () {};';
	}
	if ( empty( $n['wow'] ) ) {
		$parts[] = 'window.wow_init = function () {};';
	}
	if ( empty( $n['isotope'] ) ) {
		$parts[] = 'window.isotope_init = function () {};';
	}
	if ( empty( $n['odometer'] ) ) {
		$parts[] = 'window.Odometer = function () { this.update = function () {}; };';
	}
	if ( empty( $n['tweenmax'] ) ) {
		$parts[] = 'window.TweenMax = { set: function () {}, to: function () { return { kill: function () {} }; }, killTweensOf: function () {} };';
	}
	if ( empty( $n['skrollr'] ) ) {
		$parts[] = 'window.skrollr = { init: function () { return { destroy: function () {} }; } };';
	}
	if ( empty( $n['owl'] ) ) {
		$parts[] = '$.fn.owlCarousel = function () { return this; };';
	}
	if ( empty( $n['vimeo'] ) ) {
		$parts[] = '$.fn.vimeo = function () { return this; };';
	}
	if ( empty( $n['yt'] ) ) {
		$parts[] = 'window.YT = { loaded: 0, Player: function () {} };';
	}

	$parts[] = '}(window.jQuery));';

	wp_add_inline_script( 'cws_scripts', implode( "\n", $parts ), 'after' );
}

/**
 * Dequeue theme / plugin assets that are not needed on this request.
 */
function lh_newastro_conditional_dequeue_assets() {
	if ( lh_newastro_assets_should_skip() ) {
		return;
	}

	$n = lh_newastro_assets_needs();

	// Dequeue only — keep registrations so shortcodes can late-enqueue during render.
	$dequeue_script = static function ( $handles ) {
		foreach ( (array) $handles as $h ) {
			wp_dequeue_script( $h );
		}
	};
	$dequeue_style = static function ( $handles ) {
		foreach ( (array) $handles as $h ) {
			wp_dequeue_style( $h );
		}
	};

	if ( empty( $n['fancybox'] ) ) {
		$dequeue_script( array( 'fancybox' ) );
		$dequeue_style( array( 'fancybox' ) );
	}

	if ( empty( $n['select2'] ) ) {
		$dequeue_script( array( 'select2_init', 'select2_main' ) );
		$dequeue_style( array( 'select2_init', 'select2_main' ) );
	}

	if ( empty( $n['owl'] ) ) {
		$dequeue_script( array( 'owl_carousel' ) );
	}

	if ( empty( $n['isotope'] ) ) {
		$dequeue_script( array( 'isotope' ) );
	}

	if ( empty( $n['odometer'] ) ) {
		$dequeue_script( array( 'odometer' ) );
	}

	if ( empty( $n['wow'] ) ) {
		$dequeue_script( array( 'wow' ) );
	}

	if ( empty( $n['tweenmax'] ) ) {
		$dequeue_script( array( 'tweenmax' ) );
	}

	if ( empty( $n['yt'] ) ) {
		$dequeue_script( array( 'yt_player_api' ) );
	}

	if ( empty( $n['vimeo'] ) ) {
		$dequeue_script( array( 'vimeo' ) );
	}

	if ( empty( $n['parallax'] ) ) {
		$dequeue_script( array( 'parallax' ) );
	}

	if ( empty( $n['skrollr'] ) ) {
		$dequeue_script( array( 'skrollr' ) );
	}

	if ( empty( $n['animate'] ) ) {
		$dequeue_style( array( 'animate' ) );
	}

	// WooCommerce plugin CSS on non-shop pages.
	// Keep theme handle `woocommerce` (aasana/woocommerce/css/woocommerce.css) sitewide —
	// it hides the header mini-cart dropdown; without it empty-cart text pushes the hamburger off-screen.
	if ( empty( $n['woo_css'] ) ) {
		$dequeue_style( array(
			'woocommerce-general',
			'woocommerce-layout',
			'woocommerce-smallscreen',
			'woocommerce-inline',
			'woocommerce-blocktheme',
			'woocommerce_gridlist',
			'woocommerce-rtl',
			'woocommerce_gridlist-rtl',
		) );
	}

	if ( empty( $n['woo_js'] ) ) {
		$dequeue_script( array( 'aasana_woo' ) );
	}

	// Sticky sidebar lib only when theme option is on (scripts.js no-ops otherwise).
	if ( empty( $n['sticky_sidebar'] ) ) {
		$dequeue_script( array( 'fixed_sidebars' ) );
	}

	lh_newastro_assets_script_guards();
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_conditional_dequeue_assets', 100 );

/**
 * Whether sticky sidebar JS is needed on this request.
 */
function lh_newastro_needs_sticky_sidebar() {
	// Homepage / service landings typically have no theme sidebars.
	if ( is_front_page() ) {
		return false;
	}
	if ( function_exists( 'lh_newastro_is_service_landing' ) && lh_newastro_is_service_landing() ) {
		return false;
	}

	global $aasana_theme_funcs;
	if ( isset( $aasana_theme_funcs ) && is_object( $aasana_theme_funcs ) && method_exists( $aasana_theme_funcs, 'cws_get_meta_option' ) ) {
		return '1' === (string) $aasana_theme_funcs->cws_get_meta_option( 'sticky_sidebars' );
	}

	return false;
}

/**
 * Move heavy theme JS out of <head> into the footer.
 * Same files / same behavior — only print location changes (less render-blocking).
 */
function lh_newastro_defer_core_js_to_footer() {
	if ( lh_newastro_assets_should_skip() ) {
		return;
	}

	$handles = array(
		'cws_scripts',
		'fixed_sidebars',
		'fancybox',
		'select2_init',
		'select2_main',
		'tweenmax',
	);

	$wp_scripts = wp_scripts();
	foreach ( $handles as $handle ) {
		if ( ! $wp_scripts->query( $handle, 'registered' ) ) {
			continue;
		}
		// group = 1 → printed in footer (WordPress script API).
		$wp_scripts->add_data( $handle, 'group', 1 );
	}
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_defer_core_js_to_footer', 101 );

/**
 * Stop WooCommerce core styles registering on non-shop pages (runs before enqueue).
 */
function lh_newastro_filter_woo_styles( $styles ) {
	if ( lh_newastro_assets_should_skip() ) {
		return $styles;
	}
	if ( lh_newastro_assets_is_woo_surface() ) {
		return $styles;
	}
	return array();
}
add_filter( 'woocommerce_enqueue_styles', 'lh_newastro_filter_woo_styles', 100 );
