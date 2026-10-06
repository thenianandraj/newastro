<?php
/**
 * [lh_product_form id="123"] — prints the live WooCommerce add-to-cart form
 * (with Custom Product Addons fields) for a product, so service pages always
 * show the fields currently saved in WCPA admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lh_newastro_product_form_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'lh_product_form' );

	if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
		return '';
	}

	$form_product = wc_get_product( absint( $atts['id'] ) );
	if ( ! $form_product || ! $form_product->is_purchasable() ) {
		return '';
	}

	$prev_product       = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
	$GLOBALS['product'] = $form_product;

	ob_start();
	woocommerce_template_single_add_to_cart();
	$html = ob_get_clean();

	$GLOBALS['product'] = $prev_product;

	return $html;
}
add_shortcode( 'lh_product_form', 'lh_newastro_product_form_shortcode' );
