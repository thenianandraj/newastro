<?php
	get_header ();

	$p_id = get_queried_object_id();
	global $aasana_theme_funcs;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
	$taxonomy = get_query_var( 'taxonomy' );
	$term_slug = get_query_var( $taxonomy );

	$posts_grid_atts = array(
			'post_type'						=> 'cws_portfolio',
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
	if(!empty($aasana_theme_funcs)){
		$posts_grid_atts['display_style'] = $aasana_theme_funcs->cws_get_meta_option( 'portfolio_mode' );
		if($posts_grid_atts['display_style'] == 'filter' || $posts_grid_atts['display_style'] =='filter_with_ajax'){
			$i = 0; 
			$name_cats = array();
			foreach (get_categories('taxonomy=cws_portfolio_cat') as $key => $value) { 
				$name_cats[$i]['name'] = $value->name;
				$name_cats[$i]['slug'] = $value->slug;
				$i++;
			}
			$vale = '';
			foreach ($name_cats as $key => $value) {
				$vale .= $value['slug'].',';
			}
			$vale = substr($vale, 0, -1);
			$posts_grid_atts['cws_portfolio_cat_terms'] = $vale;
			$posts_grid_atts['tax'] = 'cws_portfolio_cat';			
		}
		$posts_grid_atts['pagination_grid'] = $aasana_theme_funcs->cws_get_meta_option( 'portfolio_pagination_style' );
	}

	
?>
<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php
		echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';
	?>
	<main id='page_content'>
		<div class="grid_row">
			<?php
			if(function_exists('cws_vc_shortcode_cws_portfolio_posts_grid')){
				$posts_grid = cws_vc_shortcode_cws_portfolio_posts_grid( $posts_grid_atts );
				echo sprintf("%s", $posts_grid );				
			}

			?>
		</div>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : ''; ?>
</div>

<?php

get_footer ();

?>




