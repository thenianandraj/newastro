<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$class_container = 'page_content';
$class_container .= ' col-3';
$woo_sb_layout = '';
global $aasana_theme_funcs;
if($aasana_theme_funcs){
	$class_container .= ' col-'.$aasana_theme_funcs->cws_get_option( 'woo_columns' );
	$woo_sb_layout = sanitize_html_class( $aasana_theme_funcs->cws_get_option( 'woo_sb_layout' ) );
}

$woo_sidebar = '';
if ( $woo_sb_layout != 'none' ) {
	ob_start();
	do_action( 'woocommerce_sidebar' );
	$woo_sidebar = ob_get_clean();
	$class_container .= ! empty( $woo_sidebar ) ? ' single_sidebar' : '';
}
?>

	<div class="<?php echo esc_attr($class_container) ?>" <?php echo isset( $footer_img_height ) ? 'style="padding-bottom:'.$footer_img_height.'px"' : '' ;?>>

		<?php
		?>

		<div class="container">
			<?php
				echo ( ! empty( $woo_sidebar ) && $woo_sb_layout != 'none' ) ? "<aside class='sb_" . $woo_sb_layout . "'>" . $woo_sidebar . '</aside>' : '';
			?>
			<main>
					<?php 
						do_action( 'woocommerce_before_main_content' );

						/**
						 * Hook: woocommerce_shop_loop_header.
						 *
						 * @since 8.6.0
						 *
						 * @hooked woocommerce_product_taxonomy_archive_header - 10
						 */
						do_action( 'woocommerce_shop_loop_header' );

						if ( woocommerce_product_loop() ) {

						/**
						 * Hook: woocommerce_before_shop_loop.
						 *
						 * @hooked wc_print_notices - 10
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						echo ('<div class="woo_panel">');
							do_action( 'woocommerce_before_shop_loop' );
						echo ('</div>');


						woocommerce_product_loop_start();

						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();

								/**
								 * Hook: woocommerce_shop_loop.
								 *
								 * @hooked WC_Structured_Data::generate_product_data() - 10
								 */
								do_action( 'woocommerce_shop_loop' );

								wc_get_template_part( 'content', 'product' );
							}
						}

						woocommerce_product_loop_end();

						/**
						 * Hook: woocommerce_after_shop_loop.
						 *
						 * @hooked woocommerce_pagination - 10
						 */
						do_action( 'woocommerce_after_shop_loop' );

					} else {
						/**
						 * Hook: woocommerce_no_products_found.
						 *
						 * @hooked wc_no_products_found - 10
						 */
						do_action( 'woocommerce_no_products_found' );
					}

					/**
					 * Hook: woocommerce_after_main_content.
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'woocommerce_after_main_content' );
					?>				

			</main>
		</div>
	</div>

<?php get_footer( 'shop' ); ?>
