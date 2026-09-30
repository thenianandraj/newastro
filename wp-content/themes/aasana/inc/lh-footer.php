<?php
/**
 * Full Life Horoscope footer. Links resolve to real published pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_enqueue_footer_assets() {
	if ( is_admin() ) {
		return;
	}

	$css = get_template_directory() . '/css/lh-footer.css';
	wp_enqueue_style(
		'lh-footer',
		get_template_directory_uri() . '/css/lh-footer.css',
		array( 'cws_main', 'lh-cormorant' ),
		file_exists( $css ) ? filemtime( $css ) : '1.2'
	);
}
add_action( 'wp_enqueue_scripts', 'lh_newastro_enqueue_footer_assets', 50 );

function lh_newastro_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

function lh_newastro_term_url( $slug, $taxonomy = 'product_cat' ) {
	if ( taxonomy_exists( $taxonomy ) ) {
		$term = get_term_by( 'slug', $slug, $taxonomy );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}
	}
	return home_url( '/product-category/' . $slug . '/' );
}

function lh_newastro_footer_item( $url, $label ) {
	return '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
}

function lh_newastro_render_site_footer() {
	if ( is_admin() ) {
		return;
	}

	$map = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15544.583722180738!2d80.19896407639476!3d13.089936452451944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5264078822719b%3A0xbda01077b89581e2!2sAnna+Nagar%2C+Chennai%2C+Tamil+Nadu!5e0!3m2!1sen!2sin!4v1562766631008!5m2!1sen!2sin';

	$services = array(
		'Life Horoscope & Jathagam' => lh_newastro_page_url( 'suya-jathagam-report' ),
		'2026 Predictions' => lh_newastro_page_url( 'yearly-horoscope-report-2026' ),
		'Transit Predictions' => lh_newastro_page_url( 'jupiter-transit-predictions-2026' ),
		'Career Services' => lh_newastro_page_url( 'career-astrology' ),
		'Marriage Services' => lh_newastro_page_url( 'marriage-astrology' ),
		'Education, Health and Special' => lh_newastro_page_url( 'education-horoscope' ),
		'Astrological Remedies' => lh_newastro_page_url( 'astrological-remedies' ),
		'Life Horoscope Combo Services' => lh_newastro_page_url( 'combo-of-life-horoscope-2026-yearly-horoscope' ),
		'Consultation & Ask Questions' => lh_newastro_page_url( 'online-astrology-consultation' ),
		'Medical Astrology' => lh_newastro_page_url( 'medical-astrology-team-astrologer' ),
		'Vaastu Consultation' => lh_newastro_page_url( 'vastu-consultation' ),
		'Palmistry' => lh_newastro_page_url( 'palm-reading' ),
	);

	$shop_links = array(
		'Karungali Products' => lh_newastro_term_url( 'karungali' ),
		'Pyramids' => lh_newastro_term_url( 'specialised-pyramid' ),
		'RUDRAKSHA' => 'https://lifehoroscopespiritual.com/collections/rudraksha',
		'Blue Sapphire Gemstones' => 'https://lifehoroscopespiritual.com/products/blue-sapphire-gemstone-1-142-cts',
		'Green Emerald Gemstones' => 'https://lifehoroscopespiritual.com/products/green-emerald-natural-2-92-ct-emstones',
		'Yellow Sapphire Gemstones' => 'https://lifehoroscopespiritual.com/products/light-yellow-sapphire-natural-2-33-cts',
		'Pujas & Homa' => lh_newastro_page_url( 'homam-pooja' ),
		'Online Homam & Poojas' => 'https://lifehoroscopespiritual.com/',
	);

	$quick = array(
		'Home' => home_url( '/' ),
		'All Services' => lh_newastro_page_url( 'online-astrology-consultation' ),
		'Shop' => home_url( '/shop/' ),
		'Blog' => lh_newastro_page_url( 'blog' ),
		'About' => lh_newastro_page_url( 'about-us' ),
		'Contact' => lh_newastro_page_url( 'contacts' ),
		'Order Now' => lh_newastro_page_url( 'online-astrology-consultation' ),
	);

	$important = array(
		'Privacy Policy' => lh_newastro_page_url( 'terms-and-conditions' ) . '#privacy_policy',
		'Terms & Conditions' => lh_newastro_page_url( 'terms-and-conditions' ),
		'Cancellations Conditions' => lh_newastro_page_url( 'terms-and-conditions' ) . '#cancellation_conditions',
		'Life Horoscope Spiritual' => 'https://lifehoroscopespiritual.com/',
	);
	?>
	<footer class="page_footer lh-original-footer">
		<div class="container">
			<div class="footer_container lh-footer-grid">

				<div class="cws-widget lh-footer-col">
					<div class="widget-title">Our Services</div>
					<ul>
						<?php
						foreach ( $services as $label => $url ) {
							echo lh_newastro_footer_item( $url, $label );
						}
						?>
					</ul>
					<div class="widget-title">Follow Us</div>
					<p class="lh-footer-social">
						<a href="https://www.youtube.com/c/LifeHoroscope/videos" target="_blank" rel="noopener">YouTube</a>
						<a href="https://www.facebook.com/astrologershankernarrayan" target="_blank" rel="noopener">Facebook</a>
						<a href="https://www.instagram.com/lifehoroscope.in/" target="_blank" rel="noopener">Instagram</a>
					</p>
				</div>

				<div class="cws-widget lh-footer-col">
					<div class="widget-title">Shop</div>
					<ul>
						<?php
						foreach ( $shop_links as $label => $url ) {
							echo lh_newastro_footer_item( $url, $label );
						}
						?>
					</ul>
				</div>

				<div class="cws-widget lh-footer-col">
					<div class="widget-title">Quick Links</div>
					<ul>
						<?php
						foreach ( $quick as $label => $url ) {
							echo lh_newastro_footer_item( $url, $label );
						}
						?>
					</ul>
					<div class="widget-title">Important Links</div>
					<ul>
						<?php
						foreach ( $important as $label => $url ) {
							echo lh_newastro_footer_item( $url, $label );
						}
						?>
					</ul>
				</div>

				<div class="cws-widget lh-footer-col">
					<div class="widget-title">Contact Us</div>
					<p>
						16/20, 2nd Floor, 4th Cross St E,<br>
						Venkatasamy Nagar, Shenoy Nagar,<br>
						Chennai, Tamil Nadu 600030, India
					</p>
					<p>
						<a href="tel:+919384814538">+91 93848 14538</a><br>
						<a href="tel:+917299679729">+91 72996 79729</a>
					</p>
					<p><a href="mailto:info@lifehoroscope.in">info@lifehoroscope.in</a></p>

					<div class="widget-title">Reach Us</div>
					<div class="ftr_map">
						<iframe
							title="Life Horoscope location"
							src="<?php echo esc_url( $map ); ?>"
							width="100%"
							height="180"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							allowfullscreen
						></iframe>
					</div>
				</div>

			</div>
		</div>
	</footer>
	<div class="copyrights_area lh-original-copyrights">
		<div class="container">
			<div class="copyrights_container">
				<div class="copyrights">Copyright <?php echo esc_html( gmdate( 'Y' ) ); ?> Life Horoscope. All rights reserved.</div>
			</div>
		</div>
	</div>
	<?php
}
