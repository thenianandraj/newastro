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
		wp_localize_script(
			'lh-service-page',
			'lhServicePage',
			array(
				'sampleTamilUrl'   => content_url( '/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033.pdf' ),
				'sampleEnglishUrl' => content_url( '/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033-English.pdf' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_service_page_assets', 45 );

/**
 * Restore Tamil/English sample PDF links when page HTML was stripped to plain text.
 * (Does not rewrite DB — front-end output only.)
 */
function lh_newastro_restore_sample_report_links( $content ) {
	if ( ! is_string( $content ) || false === stripos( $content, 'btn-hr' ) ) {
		return $content;
	}
	if ( false !== strpos( $content, 'class="hr_btn"' ) || false !== strpos( $content, "class='hr_btn'" ) ) {
		return $content;
	}
	if ( ! preg_match( '/Tamil\s*English/i', $content ) ) {
		return $content;
	}

	$tamil   = esc_url( content_url( '/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033.pdf' ) );
	$english = esc_url( content_url( '/uploads/2024/05/Tamil-Selvi-Life-Horoscope-2024-2033-English.pdf' ) );
	$links   = '<a class="hr_btn" href="' . $tamil . '" target="_blank" rel="noopener noreferrer">Tamil</a>'
		. '<a class="hr_btn1" href="' . $english . '" target="_blank" rel="noopener noreferrer">English</a>';

	$fixed = preg_replace(
		'/(<div class="btn-hr">\s*<p>)\s*Tamil\s*English\s*(<\/p>\s*<\/div>)/i',
		'$1' . $links . '$2',
		$content,
		-1,
		$count
	);

	return ( null !== $fixed && $count > 0 ) ? $fixed : $content;
}
add_filter( 'the_content', 'lh_newastro_restore_sample_report_links', 20 );

/**
 * Late mobile heading CSS — prints after VC/page custom <style> so 50% title
 * width and 170px side padding cannot clip headings on phones.
 */
function lh_newastro_service_page_late_heading_css() {
	if ( ! lh_newastro_is_service_landing() ) {
		return;
	}
	?>
<style id="lh-service-heading-fix">
@media screen and (max-width: 991px) {
	body.lh-service-page .page_title .container.header_center > *,
	body.lh-service-page .page_title .title,
	body.lh-service-page .page_title.custom_spacing,
	body.lh-service-page .header_box .page_title .container.header_center > * {
		max-width: 100% !important;
		width: 100% !important;
	}
	body.lh-service-page .page_title .title h1,
	body.lh-service-page .page_title h1 {
		display: block !important;
		visibility: visible !important;
		opacity: 1 !important;
		max-width: 100% !important;
		font-size: clamp(1.1rem, 5vw, 1.5rem) !important;
		line-height: 1.25 !important;
		white-space: normal !important;
		overflow-wrap: anywhere !important;
		word-break: break-word !important;
	}
	body.lh-service-page .widgettitle,
	body.lh-service-page .widgettitle span {
		max-width: 100% !important;
		white-space: normal !important;
		overflow-wrap: anywhere !important;
		word-break: break-word !important;
	}
	body.lh-service-page .wpb-content-wrapper:has(form.cart) .cws-content:not(:has(form.cart)) .widgettitle span {
		font-size: clamp(1.05rem, 4.8vw, 1.35rem) !important;
	}
	body.lh-service-page [class*="vc_custom_"] {
		padding-left: 0 !important;
		padding-right: 0 !important;
	}
}
/* After Additional CSS / page <style> — lock Tamil/English sample pills to gold */
html body.lh-service-page .btn-hr,
html body.lh-service-page .btn-hr p {
	display: flex !important;
	flex-wrap: wrap !important;
	align-items: center !important;
	gap: 10px !important;
}
html body.lh-service-page h4.abt_hd1,
html body.lh-service-page .abt_hd1 {
	color: #ffffff !important;
	text-transform: uppercase !important;
	font-family: "Cormorant Garamond", Georgia, serif !important;
	background: none !important;
}
html body.lh-service-page a.hr_btn,
html body.lh-service-page a.hr_btn1 {
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	margin: 0 10px 0 0 !important;
	padding: 8px 18px !important;
	border-radius: 999px !important;
	border: 1px solid #d4af37 !important;
	background: rgba(212, 175, 55, 0.12) !important;
	background-color: rgba(212, 175, 55, 0.12) !important;
	background-image: none !important;
	color: #f0d35a !important;
	font-size: 0.85rem !important;
	font-weight: 600 !important;
	text-decoration: none !important;
	box-shadow: none !important;
}
html body.lh-service-page a.hr_btn:hover,
html body.lh-service-page a.hr_btn1:hover {
	background: #d4af37 !important;
	background-color: #d4af37 !important;
	color: #000b1e !important;
}
/* html body.lh-service-page form.cart .wcpa_field_error,
html body.lh-service-page form.cart .wcpa_field_bottom:not(:has(.wcpa_field_price)) {
	display: none !important;
	height: 0 !important;
	margin: 0 !important;
	padding: 0 !important;
	overflow: hidden !important;
}
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) select,
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) textarea,
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]):focus,
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) select:focus,
html body.lh-service-page form.cart .wcpa_field_wrap:has(.wcpa_field_error p) textarea:focus {
	border-color: #e23b3b !important;
	box-shadow: 0 0 0 3px rgba(226, 59, 59, 0.2) !important;
}
</style>
	<?php
}
add_action( 'wp_footer', 'lh_newastro_service_page_late_heading_css', 99 );
