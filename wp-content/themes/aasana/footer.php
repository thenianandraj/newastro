<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Aasana
 * @since Aasana 1.0
 */
?>

	</div><!-- #main -->

	<?php
    global $aasana_theme_funcs;
		if ($aasana_theme_funcs){
			echo sprintf("%s", $aasana_theme_funcs->cws_page_footer());
		} else {
		?>

		<div class="copyrights_area">
			<div class="container">
				<div class="copyrights_container a-center">
					<div class="copyrights"><?php echo esc_html__('Copyright ', 'aasana'); ?> <?php echo date("Y");?></div>
				</div>
			</div>
		</div>

		<?php
		}
		if ( function_exists( 'lh_newastro_render_site_footer' ) ) {
			lh_newastro_render_site_footer();
		}
		wp_footer();
	?>
	</div>
<!-- end body cont -->
</body>
</html>