<?php
	get_header ();

	$p_id = get_queried_object_id();

	global $aasana_theme_funcs;
	if(!empty($aasana_theme_funcs)){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

	}
?>
<div class="<?php echo (isset($sb) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php
	echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';

	$meta = get_post_meta( $p_id, 'cws_mb_post' );
	$meta = isset( $meta[0] ) ? $meta[0] : array();


	extract( shortcode_atts( array(
			'carousel' => '0',
			// 'show_related' => '0',
			// 'related_projects_options' => array(),
		), $meta) );


	// $related_projects_title = isset( $meta['rpo_title'] ) ? $meta['rpo_title'] : '';
	// $related_projects_cols = isset( $meta['rpo_cols'] ) ? (int) $meta['rpo_cols'] : '1';
	// $related_projects_count = isset( $meta['rpo_items_count'] ) ? (int) $meta['rpo_items_count'] : '4';


	$link_options = isset( $link_options['@'] ) ? $link_options['@'] : array();


	$cats = get_the_terms( $p_id, 'cws_testimonial_department' );
	$cats = $cats ? $cats : array(); /* if get_the_terms returns false */
	$cat_slugs = array();
	if(!empty($cats)){
		foreach( $cats as $cat ){
			$cat_slugs[] = $cat->slug;
		}		
	}

	$has_cats = !empty( $cat_slugs );

/*	$related_posts = array();
	$has_related = false;*/

	if ( $has_cats ){
		$query_args = array(
			'post_type' => 'cws_testimonial',
		);
		$query_args['tax_query'] = array( array(
			'taxonomy' => 'cws_testimonial_department',
			'field' => 'slug',
			'terms' => $cat_slugs
		));
		$query_args["post__not_in"] = array( $p_id );
	/*	if ( $related_projects_count ){
			$query_args['posts_per_page'] = $related_projects_count;
		}*/
		$q = new WP_Query( $query_args );
	/*	$related_posts = $q->posts;
		if ( count( $related_posts ) > 0 ){
			$has_related = true;
		}*/
	}

	// $use_related_carousel = $carousel == 1 && $has_related;
	// $show_related_items  = $show_related == 1 && $has_related;

	$section_class = "cws_testimonials single";
	// $section_class .= $use_related_carousel ? " related" : ""

	?>
	<main>
		<div class="grid_row">
			<section class="<?php echo esc_attr($section_class) ?>">
				<div class="cws_wrapper">
					<div class="cws_testimonials_items grid">
						<?php
						$GLOBALS['cws_vc_shortcode_single_post_atts'] = array(
							'sb_layout'						=> isset($sb['sb_class']) ? $sb['sb_class'] : "",
							);
						while ( have_posts() ) : the_post();
						echo "<article id='cws_testimonial_post_{$p_id}' class='cws_testimonial_post post_single clearfix'>";
						ob_start();
						echo "<div class='wrapper-author'>";
						cws_vc_shortcode_cws_testimonial_single_post_media ();
						echo "</div>";
						$media = ob_get_clean();
						$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_testimonial_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_testimonial_single_post_floated_media'] : false;
						unset( $GLOBALS['cws_vc_shortcode_cws_testimonial_single_post_floated_media'] );
						if($floated_media){
							echo "<div class='clearfix'>";
						}						
						if ( $floated_media ){
							echo "<div class='floated_media cws_testimonial_floated_media single_post_floated_media'>";
							echo "<div class='floated_media_wrapper cws_testimonial_floated_media_wrapper single_post_floated_media_wrapper'>";
							echo sprintf("%s", $media);									
							echo "</div>";
							echo "</div>";						
						}
						else{
							echo sprintf("%s", $media);
						}
							// ob_start();
						echo "<div class='quote'>";
						cws_vc_shortcode_cws_testimonial_posts_grid_post_title ();			

						$mark = isset( $post_meta['mark'] ) ? $post_meta['mark']: array();
						if ( !empty( $mark ) && is_numeric( $mark ) ){
							$mark_percents = floatval($mark)*20;
							echo "<div class='pricing_plan_mark'>";
							echo "<div class='cws_vc_shortcode_stars_wrapper'>";
							echo "<div class='cws_vc_shortcode_inactive_stars cws_vc_shortcode_stars'>";
							echo "</div>";
							echo "<div class='cws_vc_shortcode_active_stars cws_vc_shortcode_stars' style='width:{$mark_percents}%;'>";
							echo "</div>";
							echo "</div>";
							echo "</div>";
						}


						cws_vc_shortcode_cws_testimonial_single_post_content ();
						$poss = cws_vc_shortcode_get_post_term_links_str( 'cws_testimonial_position' );
						$terms = "";
						$terms .= !empty( $deps ) ? "<i class='fa fa-building-o'></i>&#x20;$deps" : "";
						$terms .= !empty( $poss ) ? "<i class='cwsicon-aasana-adult'></i>&#x20;$poss" : "";
						ob_start();
						cws_vc_shortcode_cws_testimonial_single_social_links ();
						$social_links = ob_get_clean();							
						if ( !empty( $terms ) || !empty( $social_links ) ){
							echo "<div class='post_atts cws_testimonial_post_atts post_single_post_atts'>";
							if ( !empty( $terms ) ){
								echo "<div class='post_terms cws_testimonial_post_terms post_single_post_terms'>";
								echo sprintf("%s", $terms);
								echo "</div>";
							}
							echo sprintf("%s", $social_links);
							echo "</div>";
						}
						echo "</div>";
						if($floated_media){
							echo "</div>";
						}
						$aasana_theme_funcs->cws_page_links ();
						echo "</article>";
						endwhile;
						wp_reset_postdata();
						unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
						?>
					</div>
				</div>
				<?php ?>
			</section>
		</div>
		<?php
		?>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content'])) ? "</div>" : ''; ?>
</div>

<?php

get_footer ();
?>







