<?php
	get_header();

	$p_id = get_queried_object_id();

	global $aasana_theme_funcs;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
	$page_classes = "";
	$page_classes .= !empty( $sb['layout_class'] ) ? ' '.$sb['layout_class']."_sidebar" : "";
	$page_classes = trim( $page_classes );
	
	$post_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	extract( wp_parse_args( $post_meta, array(
		'show_related' 		=> false,
		'rpo_title'			=> '',
		'rpo_cols'			=> '4',
		'carousel'			=> false,
		'full_width'		=> false,
		'show_featured'	=> '',
		'wide_featured'	=> '',
		'rpo_items_count'	=> get_option( 'posts_per_page' ),
	)));

	$full_width = isset( $post_meta['full_width'] ) ? $post_meta['full_width'] : false;
	$full_width = (bool)$full_width;
	$show_related = isset( $post_meta['show_related'] ) ? $post_meta['show_related'] : false;
	$rpo_title = isset( $post_meta['rpo_title'] ) ? esc_html( $post_meta['rpo_title'] ) : "";
	$rpo_items_count = isset( $post_meta['rpo_items_count'] ) ? esc_textarea( $post_meta['rpo_items_count'] ) : esc_textarea( get_option( "posts_per_page" ) );
	$rpo_cols = isset( $post_meta['rpo_cols'] ) ? esc_textarea( $post_meta['rpo_cols'] ) : 4;
	$title = get_the_title();
	$decr_pos = isset( $post_meta['decr_pos'] ) ? $post_meta['decr_pos'] : '';
	$p_type = isset( $post_meta['p_type'] ) ? $post_meta['p_type'] : '';
	$gall_type = isset( $post_meta['gall_type'] ) ? $post_meta['gall_type'] : '';
	$slider_type = isset( $post_meta['slider_type'] ) ? $post_meta['slider_type'] : '';
	$rev_slider_type = isset( $post_meta['rev_slider_type'] ) ? $post_meta['rev_slider_type'] : '';
	$video_type = isset( $post_meta['video_type'] ) ? $post_meta['video_type'] : '';
	$full_width = isset( $post_meta['full_width'] ) ? $post_meta['full_width'] : false;	
	$decr_pos = isset( $post_meta['decr_pos'] ) ? $post_meta['decr_pos'] : '';
	$cont_width = isset( $post_meta['cont_width'] ) ? $post_meta['cont_width'] : '';
	$categories = isset( $post_meta['rpo_categories'] ) ? $post_meta['rpo_categories'] : '';
	if(is_array($categories)){
		if(empty($categories[0])){
			unset($categories[0]);
		}
	}
	$sb['sb_class'] .= $full_width ? ' full_width' : '';
	$classes = !empty($p_type) ? $p_type : '';
	$classes .= !empty($decr_pos) ? ' ' . $decr_pos : '';
	$classes .= !empty($decr_pos) && $decr_pos !== 'bot' && !$full_width ? ' flex_col' : '';
	$classes .= $decr_pos == 'left' || $decr_pos == 'left_s' ? ' reverse' : '';
	$sticky = $decr_pos == 'left_s' || $decr_pos == 'right_s' ? 'sticky_cont' : '';
	switch ($cont_width) {
		case '25':
			$media_width = '75';
			break;
		case '33':
			$media_width = '66';
			break;
		case '50':
			$media_width = '50';
			break;
		case '66':
			$media_width = '33';
			break;
	}

?>

<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php

	if ($show_featured == '1' && $wide_featured == '1'){
		$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $p_id ), 'full' );
		$thumb_path_hdpi = $featured_img[3]['retina_thumb_exists'] ? " src='". $featured_img[0] ."' data-at2x='" . $featured_img[3]['retina_thumb_url'] ."'" : " src='". $featured_img[0] . "' data-no-retina";
		echo "<div class='wide_featured_img'><img $thumb_path_hdpi alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' /></div>";
	}
	echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';

	$cats = get_the_terms( $p_id, 'cws_portfolio_cat' );
	$cats = $cats ? $cats : array(); 
	$cat_slugs = array();
	foreach( $cats as $cat ){
		$cat_slugs[] = $cat->slug;
	}
	$has_cats = !empty( $cat_slugs );

	$related_posts = array();
	$has_related = false;

	if ( $has_cats ){
		$query_args = array(
			'post_type' => 'cws_portfolio',
		);
		$query_args['tax_query'] = array( array(
			'taxonomy' => 'cws_portfolio_cat',
			'field' => 'slug',
			'terms' => $cat_slugs
		));
		$query_args["post__not_in"] = array( $p_id );
		if ( $rpo_items_count ){
			$query_args['posts_per_page'] = $rpo_items_count;
		}
		$q = new WP_Query( $query_args );
		$related_posts = $q->posts;
		if ( count( $related_posts ) > 0 ){
			$has_related = true;
		}
	}

	$use_related_carousel = $carousel == 1 && $has_related;
	$show_related_items  = $show_related == 1 && $has_related;

	$section_class = "cws_portfolio single";
	$section_class .= $use_related_carousel ? " related" : "";

	?>
	<main>
		<?php
		ob_start();
			cws_vc_shortcode_cws_portfolio_single_post_post_media ();
		$media = ob_get_clean();
		if ( $full_width ) {
			echo sprintf("%s", $media);
		}
		?>
		<div class="grid_row">
			<section class="<?php echo esc_attr($section_class) ?>">
				<div class="cws_wrapper cws_portfolio_items">
					<?php

					$GLOBALS['cws_vc_shortcode_single_post_atts'] = array(
						'sb_layout'						=> $sb['layout_class'],
						);
						while ( have_posts() ) : the_post();
							$pid = get_the_id();
							echo "<article id='cws_portfolio_post_{$pid}' class='cws_portfolio_post post_single item clearfix " . $classes . "'>";
								ob_start();
								cws_vc_shortcode_cws_portfolio_single_post_post_media ();
								$media = ob_get_clean();
								ob_start();
								echo "<div class='cws_portfolio_single_content {$sticky}'>";
									cws_vc_shortcode_cws_portfolio_single_post_title ();
									/*cws_vc_shortcode_cws_portfolio_single_post_terms ();*/
									cws_vc_shortcode_cws_portfolio_single_post_content ();
								echo "</div>";
								$content_terms = ob_get_clean();

								if (!$full_width) {
									if ($decr_pos == 'bot') {
										echo !empty($media) ? $media : '';
										echo !empty($content_terms) ? $content_terms : '';
									} else {
										echo "<div class='single_col single_col_" . (!empty($media_width) ? $media_width : '') . "'>";
											echo !empty($media) ? $media : '';
										echo "</div>";
										echo "<div class='single_col single_col_" . $cont_width . "'>";
											echo !empty($content_terms) ? $content_terms : '';
										echo "</div>";
									}
								} else {
									echo sprintf("%s", $content_terms);
								}

								$aasana_theme_funcs->cws_page_links ();
							echo "</article>";
						endwhile;
						wp_reset_postdata();
						unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
					?>
				</div>
				<?php
					if ( $use_related_carousel ){
						$related_ids = array();
						foreach ( $related_posts as $related_post ) {
							$related_ids[] = $related_post->ID;
						}
						array_unshift( $related_ids, $p_id );
						$ajax_data = array(
							'current' => $p_id,
							'initial' => $p_id,
							'related_ids' => $related_ids
						);
						echo "<input type='hidden' id='cws_portfolio_single_ajax_data' value='" . esc_attr(json_encode( $ajax_data ) ) . "' />";
						?>
						<div class='carousel_nav_panel clearfix'>
							<div class='prev_section'>
								<div class='prev'>
									<div class="wrap">
										<span><?php esc_html_e( 'Prev' , 'aasana' ); ?></span>
									</div>
									<i class="fa fa-long-arrow-left"></i>
								</div>
							</div>
							<div class='next_section'>
								<div class='next'>
									<div class="wrap">
										<span><?php esc_html_e( 'Next' , 'aasana' ); ?></span>
									</div>
									<i class="fa fa-long-arrow-right"></i>
								</div>
							</div>
						</div>
						<?php
					}
				?>
			</section>
		</div>
		<div class="grid_row single_portfolio related_portfolio">
			<?php
				if ( $show_related ){
					$terms = wp_get_post_terms( $p_id, 'cws_portfolio_cat' );
					$term_slugs = array();
					for ( $i=0; $i < count( $terms ); $i++ ){
						$term = $terms[$i];
						$term_slug = $term->slug;
						array_push( $term_slugs, $term_slug );
					}
					$term_slugs = implode( ",", $term_slugs );
					if ( !empty( $term_slugs ) ){
						$rp_args = array(
							'title'							=> $rpo_title,
							'post_type'						=> 'cws_portfolio',
							'total_items_count'				=> $rpo_items_count,
							'display_style'					=> 'carousel',
							'cws_portfolio_layout_override'	=> true,
							'layout'						=> $rpo_cols,
							'tax'							=> 'cws_portfolio_cat',
							'navigation_carousel'			=> true,
							'terms'							=> !empty($categories) ? implode( ",", $categories ) : $term_slugs,
							'addl_query_args'				=> array(
								'post__not_in'					=> array( $p_id ),
							),
						);
						$related_projects = cws_vc_shortcode_cws_portfolio_posts_grid( $rp_args );
						if ( !empty( $related_projects ) ){
							echo sprintf("%s", $related_projects);
						}
					}
				}
			?>
		</div>
	</main>
	<?php echo isset($sb['content']) && !empty($sb['content']) ? '</div>' : ''; ?>
</div>

<?php
	get_footer();
?>





