<?php
	get_header ();

	global $aasana_theme_funcs;
	global $aasana_theme_standard;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
?>
<div class="<?php echo (isset($sb) ? $sb['sb_class'] : ' page_content'); ?>">
	<?php
	$pid = get_the_id();
	if ($aasana_theme_funcs){	
		$meta = $aasana_theme_funcs->cws_get_post_meta( $pid );
		if(isset($meta[0])){
			$meta = $meta[0];
		} 
		extract( shortcode_atts( array(
				'enable_lightbox' => '0',
				'show_related' => '0',
				// 'related_projects_options' => array(),
				'author_info' => '1',
				'show_featured' => '',
				'full_width' => '',
				// 'link_options_fancybox' => '0',
			), $meta) );

	if(!empty($show_featured) && !empty($full_width)){
		echo cws_vc_shortcode_post_post_single_post_media();
	}

	echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';

		$related_projects_title = isset( $meta['rpo']['title'] ) ? $meta['rpo']['title'] : '';
		$related_projects_category = isset( $meta['rpo']['category'] ) ? $meta['rpo']['category'] : '';
		$related_projects_text_length = isset( $meta['rpo']['text_length'] ) ? $meta['rpo']['text_length'] : '90';
		$related_projects_cols = isset( $meta['rpo']['cols'] ) ? (int) $meta['rpo']['cols'] : '4';
		$related_projects_count = isset( $meta['rpo']['items_show'] ) ? (int) $meta['rpo']['items_show'] : '4';
		$posts_hide = isset( $meta['rpo']['posts_hide'] ) ? $meta['rpo']['posts_hide'] : '';

		$related_projects_category = !empty($related_projects_category) ? implode(',', $related_projects_category) : '';
		$posts_hide = !empty($posts_hide) ? implode(',', $posts_hide) : '';
	}

	$query_args = array(
		'post_type' => 'post',
		'ignore_sticky_posts' => true,
		'post_status' => 'publish',
	);
	$query_args["post__not_in"] = array( $pid );
	if ($aasana_theme_funcs){
		if ( $related_projects_count ){
			$query_args['posts_per_page'] = $related_projects_count;
		}
	}
	$q = new WP_Query( $query_args );
	$related_posts = $q->posts;
	if ( count( $related_posts ) > 0 ) {
		$has_related = true;
	}

	if ($aasana_theme_funcs){
		$show_related_items  = $show_related == 1 && $has_related;
	}

	$section_class = "news single";

	$custom_layout_arr = array(
		'columns' => '1',
		'meta_position' => 'top',
		'meta_align' => 'left',
		'content_align' => 'left',
		'column_count' => '1',
		'post_text_length' => '0',
		'content_divider' => '1',
		'custom_layout' => '1',
		'hide' => array(),
		'column_style' => false,
		'this_shortcode' => false,
		'text_over_image' => '0',
		'date_style' => '1',
		'is_related' => '0',
		'disable_lightbox' => '1',
		'full_width' => '0',
		'full_width_spacing' => '',
		'full_width_border' => '',
		'aspect_ratio' => '0',

		'blogtype' => '1',	
		'row_style' => 'def',
		'pagination' => '0',	
		'post_size' => '',
	);

	?>
	<main>
		<div class="grid_row clearfix">
			<section class="<?php echo esc_attr($section_class) ?>">
				<div class="cws_wrapper">
					<div class="grid">
						<?php
						echo "<article class='item clearfix meta-".$custom_layout_arr['meta_align']." content-".$custom_layout_arr['content_align']."'>";
							while ( have_posts() ):
								the_post();

								if ($aasana_theme_funcs){
									$aasana_theme_funcs->cws_single_post_output ($custom_layout_arr);
								} else {
									$aasana_theme_standard->cws_single_post_output();									
								}

							endwhile;
							wp_reset_postdata();
						?>
						</article>
					</div>
				</div>
			</section>
		</div>

		<?php
			if ( $aasana_theme_funcs && $show_related_items ){
				$mode = $q->post_count > $related_projects_cols ? '1' : '0';
				
				$terms = wp_get_post_terms( get_queried_object_id(), 'category' );
				$term_slugs = array();
				for ( $i=0; $i < count( $terms ); $i++ ){
					$term = $terms[$i];
					$term_slug = $term->slug;
					array_push( $term_slugs, $term_slug );
				}
				$term_slugs = implode( ",", $term_slugs );
				$GLOBALS['cws_vc_shortcode_single_post_atts'] = array(
					'sb_layout'						=> $sb['layout_class'],
				);
				$p_id = get_queried_object_id();
				$sc_atts = array(
					'title' => $related_projects_title,
					'tax' => 'category',
					'related_items' => true,
  					'terms' => !empty($related_projects_category) ? $related_projects_category : $term_slugs,
  					'total_items_count'				=> $related_projects_count,
  					'display_style'					=> 'carousel',
  					'layout'						=> $related_projects_cols,
  					'chars_count' => $related_projects_text_length,
  					'post_hide_meta_override'	=> true,
  					'navigation_carousel'		=> true,
  					'post_hide_meta'				=> $posts_hide,
  					'addl_query_args'				=> array(
						'post__not_in'					=> array( $p_id )
					),
				);

				$related_projects_section = function_exists('cws_vc_shortcode_cws_blog_posts_grid') ? cws_vc_shortcode_cws_blog_posts_grid( $sc_atts ) : "";
				echo !empty( $related_projects_section ) ? "<div class='grid_row single_related'>$related_projects_section</div>" : "";
				unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
			}

		?>

		<?php comments_template(); ?>
	</main>
	<?php echo (isset($sb) && !empty($sb['content']) ) ? '</div>' : ''; ?>
</div>

<?php

get_footer ();
?>