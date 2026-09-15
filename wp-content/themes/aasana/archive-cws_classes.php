<?php
	get_header ();

	$p_id = get_queried_object_id();
	global $aasana_theme_funcs;

	if ($aasana_theme_funcs){
		$theme_color 			= esc_attr( $aasana_theme_funcs->cws_get_option( "theme-main-one-color" ) );
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
	$taxonomy = get_query_var( 'taxonomy' );
	$term_slug = get_query_var( $taxonomy );

	$posts_grid_atts = array(
			'post_type'						=> 'cws_classes',
			'total_items_count'				=> PHP_INT_MAX
		);
	if ( is_date() ){
		$year = $aasana_theme_funcs->cws_get_date_part( 'y' );
		$month = $aasana_theme_funcs->cws_get_date_part( 'm' );
		$day = $aasana_theme_funcs->cws_get_date_part( 'd' );
		if ( !empty( $year ) ){
			$posts_grid_atts['addl_query_args']['year'] = $year;
		}
		if ( !empty( $month ) ){
			$posts_grid_atts['addl_query_args']['monthnum'] = $month;
		}
		if ( !empty( $day ) ){
			$posts_grid_atts['addl_query_args']['day'] = $day;
		}
	}
	$posts_grid_atts['data_to_show'] = 'title,excerpt,teach,working_days';
	$posts_grid_atts['show_data_override'] = true;
  	$posts_grid_atts['customize_colors'] = '1';
  	$posts_grid_atts['custom_color'] = isset($theme_color) && !empty($theme_color) ? $theme_color : '#7b6cd5';
	$posts_grid_atts['bg_color'] = '#f2f0fb';
	$posts_grid_atts['hover_bg_color'] = 'rgba(123,108,213,0.75)';
?>
<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php
	echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';
	?>
	<main id='page_content'>
		<div class="grid_row">
			<?php
				$posts_grid = function_exists('cws_vc_shortcode_cws_classes_posts_grid') ? cws_vc_shortcode_cws_classes_posts_grid( $posts_grid_atts ) : "";
				echo sprintf('%s', $posts_grid);
			?>
		</div>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : ''; ?>
</div>

<?php

get_footer ();

?>




