 <?php
/**
 * Homepage hero (1-3.png) + layout cleanup. Does not edit page content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_is_front() {
	return is_front_page() && ! is_admin();
}

function lh_newastro_hero_image_url() {
	return content_url( 'uploads/2026/09/1-3.avif' );
}

function lh_newastro_enqueue_home_assets() {
	if ( ! lh_newastro_is_front() ) {
		return;
	}

	// Fonts + CSS bundle: inc/lh-perf-assets.php (one font request, one home CSS file).

	$trust_js = get_template_directory() . '/js/lh-home-trust.js';
	wp_enqueue_script(
		'lh-home-trust',
		get_template_directory_uri() . '/js/lh-home-trust.js',
		array(),
		file_exists( $trust_js ) ? filemtime( $trust_js ) : '1.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_home_assets', 40 );

/**
 * Homepage only: drop old Smart Slider, expand Why Trust Us (no Read More).
 */
function lh_newastro_filter_home_content( $content ) {
	if ( is_admin() || ! is_string( $content ) || '' === $content ) {
		return $content;
	}
	$front_id = (int) get_option( 'page_on_front' );
	if ( ! is_front_page() && ( ! $front_id || (int) get_the_ID() !== $front_id ) ) {
		return $content;
	}

	$content = preg_replace( '/\[smartslider3[^\]]*\]/i', '', $content );

	$content = preg_replace(
		'/<ss3-force-full-width[\s\S]*?<\/ss3-force-full-width>/i',
		'',
		$content
	);
	$content = preg_replace(
		'/<div[^>]*class="[^"]*n2-section-smartslider[^"]*"[\s\S]*?<\/div>\s*<\/div>\s*<\/div>/i',
		'',
		$content
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module'([^>]*)>(?=[\s\S]{0,2500}?<span>Why Trust Us<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-why-trust'\$1>",
		$content,
		1
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module'([^>]*)>(?=[\s\S]{0,800}?<span>Our Services<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-svc-head'\$1>",
		$content,
		1
	);

	$content = preg_replace(
		'/(id="our-services"[^>]*class=")/i',
		'$1lh-svc-grid ',
		$content,
		1
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module'([^>]*)>(?=[\s\S]{0,800}?<span>About Us<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-about'\$1>",
		$content,
		1
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module([^']*)'([^>]*)>(?=[\s\S]{0,800}?<span>Hear From Our Clients<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-reviews$1'\$2>",
		$content,
		1
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module'([^>]*)>(?=[\s\S]{0,800}?<span>Our Latest Youtube Videos<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-videos'\$1>",
		$content,
		1
	);

	$content = preg_replace(
		"/<div class='cws_textmodule cws_vc_shortcode_module'([^>]*)>(?=[\s\S]{0,400}?<span>FAQs<\/span>)/i",
		"<div class='cws_textmodule cws_vc_shortcode_module lh-faqs'\$1>",
		$content,
		1
	);

	$content = preg_replace( '/<button[^>]*class="[^"]*read-link[^"]*"[^>]*>[\s\S]*?<\/button>/i', '', $content );
	$content = preg_replace(
		'/(<span>Why Trust Us<\/span>[\s\S]{0,2500}?<div class="read_div")([^>]*)(>)/i',
		'$1 style="display:block"$3',
		$content,
		1
	);

	return $content;
}
add_filter( 'the_content', 'lh_newastro_filter_home_content', 8 );
add_filter( 'the_content', 'lh_newastro_filter_home_content', 20 );

function lh_newastro_render_home_hero() {
	if ( ! lh_newastro_is_front() ) {
		return;
	}

	$img     = esc_url( lh_newastro_hero_image_url() );
	$consult = esc_url( home_url( '/services/' ) );
	?>
	<section class="lh-home-hero" aria-label="Life Horoscope homepage">
		<div class="lh-home-hero__media">
			<img
				class="lh-home-hero__img"
				src="<?php echo $img; ?>"
				alt="Astrologer Shanker Narrayan"
				width="1920"
				height="1080"
				decoding="async"
				fetchpriority="high"
			/>
		</div>
		<div class="lh-home-hero__shade" aria-hidden="true"></div>
		<div class="lh-home-hero__inner">
			<div class="lh-home-hero__copy">
				<p class="lh-home-hero__brand">Life Horoscope</p>
				<h1 class="lh-home-hero__title">Discover Your Future<br>With Our Experts</h1>
				<p class="lh-home-hero__text">Personalized Vedic guidance for love, career, health, and life decisions.</p>
				<div class="lh-home-hero__actions">
					<a class="lh-home-hero__btn lh-home-hero__btn--primary" href="<?php echo $consult; ?>">Book our services</a>
				</div>
			</div>
		</div>
	</section>
	<?php
}
