<?php
	get_header ();
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	global $aasana_theme_funcs;
	if(!empty($aasana_theme_funcs)){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

	}

	$taxonomy = get_query_var( 'taxonomy' );
	$term_slug = get_query_var( $taxonomy );

?>
<div class="<?php echo (isset($sb) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php
		echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';

	?>
	<main>
		<div class="grid_row">
			<?php
				switch( $taxonomy ) {
				case "cws_portfolio_cat":
						echo cws_vc_shortcode_cws_portfolio_posts_grid( array(
							'columns' => $aasana_theme_funcs->cws_get_option( "def_layout_portfolio" ),
							'tax'							=> $taxonomy,
							$taxonomy . '_terms'			=> $term_slug,
							'crop_images' => '1',
							'display_style' => $aasana_theme_funcs->cws_get_option( "portfolio_mode" ),
							'pagination_grid' => $aasana_theme_funcs->cws_get_option( "portfolio_pagination_style" ),
							)
						);
						break;
						case "cws_staff_member_department":
						echo cws_vc_shortcode_cws_staff_posts_grid( array(
							'mode' => $aasana_theme_funcs->cws_get_option( "staff_mode" ),
							'tax'							=> $taxonomy,
							$taxonomy . '_terms'			=> $term_slug,
							)
						);
						break;
					case "cws_staff_member_position":
						echo cws_vc_shortcode_cws_staff_posts_grid( array(
							'mode' => $aasana_theme_funcs->cws_get_option( "staff_mode" ),
							'tax'							=> $taxonomy,
							$taxonomy . '_terms'			=> $term_slug,
							)
						);
						break;
					case "cws_testimonial_department":
						echo cws_vc_shortcode_cws_testimonial_posts_grid( array(
							'columns' => $aasana_theme_funcs->cws_get_option( "def_layout_testimonials" ),
							'tax'							=> $taxonomy,
							$taxonomy . '_terms'			=> $term_slug,
							)
						);
						break;
					case "cws_testimonial_position":
						echo cws_vc_shortcode_cws_testimonial_posts_grid( array(
							'columns' => $aasana_theme_funcs->cws_get_option( "def_layout_testimonials" ),
							'tax'							=> $taxonomy,
							$taxonomy . '_terms'			=> $term_slug,
							)
						);
						break;				
				}
			?>
		</div>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content'])) ? "</div>" : ''; ?>
</div>

<?php

get_footer ();
?>